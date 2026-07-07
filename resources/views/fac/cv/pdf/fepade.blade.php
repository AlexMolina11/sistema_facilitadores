@include('fac.cv.pdf._styles')

@php
    $selected = function ($section, $item, $field) use ($config) {
        return data_get($config, "selections.$section.$item.$field") === true;
    };

    $personal = function ($field) use ($cvData, $selected) {
        return $selected('personal', 'personal', $field)
            ? data_get($cvData, "personal.$field")
            : '';
    };

    $visible = function ($section, $item, $fields) use ($selected) {
        foreach ($fields as $field) {
            if ($selected($section, $item['id'], $field) && ! blank($item[$field] ?? null)) {
                return true;
            }
        }

        return false;
    };

    $cell = function ($section, $item, $field) use ($selected) {
        return $selected($section, $item['id'], $field)
            ? ($item[$field] ?? '')
            : '';
    };

    $TIPO_FORMACION = $cvCatalogos['tipo_formacion'] ?? [];

    $atestadosPorTipo = function ($ids) use ($cvData, $visible) {
        return collect($cvData['atestados'] ?? [])
            ->filter(fn ($item) => in_array((int) ($item['id_tipo_formacion'] ?? 0), $ids, true))
            ->filter(fn ($item) => $visible('atestados', $item, [
                'titulo',
                'institucion',
                'fecha_inicio',
                'fecha_fin',
                'archivo_url',
            ]));
    };

    $parseDate = function ($value) {
        if (blank($value)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    };

    $experiencias = collect($cvData['experiencias'] ?? []);

    $cargoActual = $experiencias->first(fn ($item) => ($item['trabajo_actual_bool'] ?? false) === true);

    $cargoReciente = $cargoActual ?: $experiencias
        ->filter(fn ($item) => ! blank($item['hasta_iso'] ?? null))
        ->sortByDesc('hasta_iso')
        ->first();

    $cargo = $cargoReciente && $selected('experiencias', $cargoReciente['id'], 'cargo')
        ? ($cargoReciente['cargo'] ?? '')
        : '';

    $educacionFormal = $atestadosPorTipo($TIPO_FORMACION['educacion_formal'] ?? []);

    $otrosEstudios = $atestadosPorTipo(array_merge(
        $TIPO_FORMACION['acreditacion'] ?? [],
        $TIPO_FORMACION['educacion_continua'] ?? []
    ));

    $consultorias = $atestadosPorTipo(array_merge(
        $TIPO_FORMACION['capacitacion_impartida'] ?? [],
        $TIPO_FORMACION['capacitacion_recibida'] ?? [],
        $TIPO_FORMACION['consultoria_realizada'] ?? []
    ));

    $asociaciones = collect($cvData['areas'] ?? [])
        ->filter(fn ($area) => $selected('areas', $area['id'], 'nombre'))
        ->map(function ($area) use ($cvData, $selected) {
            $atestado = collect($cvData['atestados'] ?? [])
                ->first(fn ($item) => (int) $item['id'] === (int) ($area['id_atestado'] ?? 0));

            $habilidades = collect($area['habilidades'] ?? [])
                ->filter(fn ($hab) => $selected('habilidades_area_' . $area['id'], $hab['id'], 'nombre'))
                ->pluck('nombre')
                ->filter()
                ->unique()
                ->values();

            return [
                'institucion' => $atestado['institucion'] ?? '',
                'area' => $area['nombre'] ?? '',
                'habilidades' => $habilidades,
            ];
        })
        ->filter(fn ($item) => $item['institucion'] || $item['area'] || $item['habilidades']->count());

    $mostrarPaisesExperiencia = $selected('fepade_opciones', 'paises_experiencia_10', 'mostrar');

    $limite10 = now()->subYears(10);

    $paisesExperiencia = $experiencias
        ->filter(function ($item) use ($parseDate, $limite10) {
            $hasta = ($item['trabajo_actual_bool'] ?? false)
                ? now()
                : $parseDate($item['hasta_iso'] ?? null);

            return $hasta && $hasta->greaterThanOrEqualTo($limite10) && ! blank($item['pais'] ?? null);
        })
        ->pluck('pais')
        ->filter()
        ->unique()
        ->values();
@endphp

<div class="cv-fepade-document">

    <div class="cv-fepade-hero">
        <div class="cv-fepade-brand">CONSULTORES FEPADE 2026</div>
        <h1>Hoja de Vida</h1>
        <p>Formato CV FEPADE</p>
    </div>

    <table class="cv-table cv-table-clean">
        <tr><th>Cargo:</th><td>{{ $cargo }}</td></tr>
        <tr><th>Nombre del Profesional:</th><td>{{ $personal('nombre') }}</td></tr>
        <tr><th>Fecha de nacimiento:</th><td>{{ $personal('fecha_nacimiento') }}</td></tr>
        <tr><th>País de ciudadanía/residencia:</th><td>{{ $personal('residencia') }}</td></tr>
    </table>

    <h2>1. Educación:</h2>
    <table class="cv-table">
        <thead>
            <tr>
                <th>Título obtenido</th>
                <th>Institución</th>
                <th>Fecha de estudios</th>
            </tr>
        </thead>
        <tbody>
            @forelse($educacionFormal as $item)
                <tr>
                    <td>{{ $cell('atestados', $item, 'titulo') }}</td>
                    <td>{{ $cell('atestados', $item, 'institucion') }}</td>
                    <td>{{ $cell('atestados', $item, 'fecha_fin') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">&nbsp;</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>2. Asociaciones profesionales a las que pertenece:</h2>
    <table class="cv-table">
        <thead>
            <tr>
                <th>Institución</th>
                <th>Área de especialización</th>
                <th>Habilidad técnica</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asociaciones as $item)
                <tr>
                    <td>{{ $item['institucion'] ?: '' }}</td>
                    <td>{{ $item['area'] ?: '' }}</td>
                    <td>
                        @foreach($item['habilidades'] as $habilidad)
                            {{ $habilidad }}<br>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">&nbsp;</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>3. Otros estudios:</h2>
    <table class="cv-table">
        <thead>
            <tr>
                <th>Nombre del curso/seminario</th>
                <th>Institución que lo impartió</th>
                <th>Fecha de estudios</th>
            </tr>
        </thead>
        <tbody>
            @forelse($otrosEstudios as $item)
                <tr>
                    <td>{{ $cell('atestados', $item, 'titulo') }}</td>
                    <td>{{ $cell('atestados', $item, 'institucion') }}</td>
                    <td>{{ $cell('atestados', $item, 'fecha_fin') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">&nbsp;</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($mostrarPaisesExperiencia)
        <h2>4. Países donde tiene experiencia de trabajo los últimos 10 años:</h2>
        <p>{{ $paisesExperiencia->implode(', ') }}</p>
    @endif

    <h2>5. Historia laboral:</h2>
    <table class="cv-table">
        <thead>
            <tr>
                <th>Desde</th>
                <th>Hasta</th>
                <th>Empresa</th>
                <th>Cargos desempeñados</th>
            </tr>
        </thead>
        <tbody>
            @forelse($experiencias->filter(fn($item) => $visible('experiencias', $item, ['desde', 'hasta', 'empresa', 'cargo'])) as $item)
                <tr>
                    <td>{{ $cell('experiencias', $item, 'desde') }}</td>
                    <td>{{ $cell('experiencias', $item, 'hasta') }}</td>
                    <td>{{ $cell('experiencias', $item, 'empresa') }}</td>
                    <td>{{ $cell('experiencias', $item, 'cargo') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">&nbsp;</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>6. Experiencia en consultorías y gestión de proyectos:</h2>
    <table class="cv-table">
        <thead>
            <tr>
                <th>Consultorías / capacitaciones</th>
                <th>Empresa / organización</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consultorias as $item)
                <tr>
                    <td>{{ $cell('atestados', $item, 'titulo') }}</td>
                    <td>{{ $cell('atestados', $item, 'institucion') }}</td>
                    <td>{{ $cell('atestados', $item, 'fecha_fin') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">&nbsp;</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>7. Experiencia como facilitador/a:</h2>
    <table class="cv-table">
        <thead>
            <tr>
                <th>Nombre de la capacitación</th>
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <th>Empresa a quien se impartió</th>
            </tr>
        </thead>
        <tbody>
            @forelse(collect($cvData['capacitaciones_fepade'])->filter(fn($item) => $visible('capacitaciones_fepade', $item, ['nombre_evento', 'fecha_inicio', 'fecha_fin', 'institucion'])) as $item)
                <tr>
                    <td>{{ $cell('capacitaciones_fepade', $item, 'nombre_evento') }}</td>
                    <td>{{ $cell('capacitaciones_fepade', $item, 'fecha_inicio') }}</td>
                    <td>{{ $cell('capacitaciones_fepade', $item, 'fecha_fin') }}</td>
                    <td>{{ $cell('capacitaciones_fepade', $item, 'institucion') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">&nbsp;</td></tr>
            @endforelse
        </tbody>
    </table>

    @php
        $emails = collect($cvData['emails'] ?? [])
            ->filter(fn ($item) => $visible('emails', $item, ['email']));

        $telefonos = collect($cvData['telefonos'] ?? [])
            ->filter(fn ($item) => $visible('telefonos', $item, ['tipo', 'extension', 'numero']));
    @endphp

    @if($emails->count() || $telefonos->count())
        <h2>Información de contacto:</h2>

        @if($emails->count())
            <p>
                <strong>Correo:</strong><br>
                @foreach($emails as $item)
                    {{ $cell('emails', $item, 'email') }}<br>
                @endforeach
            </p>
        @endif

        @if($telefonos->count())
            <p>
                <strong>Teléfono:</strong><br>
                @foreach($telefonos as $item)
                    @php
                        $tipo = $cell('telefonos', $item, 'tipo');
                        $extension = $cell('telefonos', $item, 'extension');
                        $numero = $cell('telefonos', $item, 'numero');
                    @endphp

                    {{ $tipo ? $tipo . ': ' : '' }}
                    {{ $extension ? '(' . $extension . ') ' : '' }}
                    {{ $numero }}
                    <br>
                @endforeach
            </p>
        @endif
    @endif
</div>