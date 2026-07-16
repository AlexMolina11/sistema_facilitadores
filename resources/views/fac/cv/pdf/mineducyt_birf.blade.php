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
@endphp

@php
    $tipoFormacion = $cvCatalogos['tipo_formacion'] ?? [];
    $idsTipos = fn (...$keys) => collect($keys)->flatMap(fn ($key) => $tipoFormacion[$key] ?? [])->map(fn ($id) => (int) $id)->all();

    $educacionFormal = collect($cvData['atestados'])->filter(fn ($item) =>
        in_array((int) ($item['id_tipo_formacion'] ?? 0), $idsTipos('educacion_formal'), true)
        && $visible('atestados', $item, ['titulo', 'institucion', 'fecha_fin'])
    );

    $formacionComplementaria = collect($cvData['atestados'])->filter(fn ($item) =>
        in_array((int) ($item['id_tipo_formacion'] ?? 0), $idsTipos('acreditacion', 'educacion_continua', 'capacitacion_recibida'), true)
        && $visible('atestados', $item, ['titulo', 'institucion', 'fecha_fin', 'fecha_emision'])
    );

    $experiencias = collect($cvData['experiencias'])->filter(fn ($item) =>
        $visible('experiencias', $item, ['empresa', 'cargo', 'descripcion', 'desde', 'hasta', 'jefe_nombre', 'jefe_email', 'jefe_telefono'])
    );

    $areas = collect($cvData['areas'])->filter(fn ($area) => $selected('areas', $area['id'], 'nombre') && ! blank($area['nombre']));
    $habilidades = $areas->flatMap(fn ($area) => collect($area['habilidades'] ?? [])->filter(fn ($hab) =>
        $selected('habilidades_area_' . $area['id'], $hab['id'], 'nombre') && ! blank($hab['nombre'])
    )->pluck('nombre'))->unique()->values();

    $idiomas = collect($cvData['idiomas'])->filter(fn ($item) =>
        $visible('idiomas', $item, ['idioma', 'nivel', 'certificado_url'])
    );

    $cargoNumero = trim((string) data_get($config, 'campos_manual.cargo_numero', ''));
    $nombreConsultoria = trim((string) data_get($config, 'campos_manual.nombre_consultoria', ''));
    $tareas = trim((string) data_get($config, 'campos_manual.tareas_asignadas', ''));
@endphp

<div class="cv-institutional-document">
    <div class="cv-institutional-header">
        <div class="cv-institutional-brand">FEPADE</div>
        <h1>CURRÍCULUM DEL PERSONAL PROPUESTO</h1>
        <p>Formato CV MINEDUCYT / BIRF</p>
    </div>

    <div class="cv-institutional-process">
        <strong>Consultoría o proceso:</strong>
        {{ $nombreConsultoria !== '' ? $nombreConsultoria : 'Pendiente de completar por FEPADE' }}
    </div>

    <h2 class="cv-numbered-title"><span>1</span> Información general</h2>
    <table class="cv-table cv-table-clean cv-info-table">
        <tr><th>Nombre del cargo y número:</th><td>{{ $cargoNumero !== '' ? $cargoNumero : 'Pendiente de completar por FEPADE' }}</td></tr>
        <tr><th>Nombre del Experto:</th><td>{{ $personal('nombre') }}</td></tr>
        <tr><th>Fecha de nacimiento:</th><td>{{ $personal('fecha_nacimiento') }}</td></tr>
        <tr><th>País de ciudadanía/residencia:</th><td>{{ $personal('nacionalidad') ?: $personal('residencia') }}</td></tr>
    </table>

    <h2 class="cv-numbered-title"><span>2</span> Educación</h2>
    <table class="cv-table cv-institutional-table">
        <thead><tr><th>Título obtenido</th><th>Institución</th><th>Fecha de estudios</th></tr></thead>
        <tbody>
            @forelse($educacionFormal as $item)
                <tr><td>{{ $cell('atestados', $item, 'titulo') }}</td><td>{{ $cell('atestados', $item, 'institucion') }}</td><td>{{ $cell('atestados', $item, 'fecha_fin') }}</td></tr>
            @empty
                <tr><td colspan="3"><em>No registrado</em></td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="cv-numbered-title"><span>3</span> Formación complementaria</h2>
    <table class="cv-table cv-institutional-table">
        <thead><tr><th>Curso, seminario o certificación</th><th>Institución</th><th>Fecha</th></tr></thead>
        <tbody>
            @forelse($formacionComplementaria as $item)
                <tr>
                    <td>{{ $cell('atestados', $item, 'titulo') }}</td>
                    <td>{{ $cell('atestados', $item, 'institucion') }}</td>
                    <td>{{ $cell('atestados', $item, 'fecha_fin') ?: $cell('atestados', $item, 'fecha_emision') }}</td>
                </tr>
            @empty
                <tr><td colspan="3"><em>No registrado</em></td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="cv-numbered-title"><span>4</span> Experiencia laboral pertinente para el trabajo</h2>
    <table class="cv-table cv-institutional-table cv-experience-table">
        <thead><tr><th>Período</th><th>Entidad empleadora y referencias</th><th>País</th><th>Resumen</th></tr></thead>
        <tbody>
            @forelse($experiencias as $item)
                <tr>
                    <td>{{ $cell('experiencias', $item, 'desde') }}{{ $cell('experiencias', $item, 'hasta') ? ' – ' . $cell('experiencias', $item, 'hasta') : '' }}</td>
                    <td>
                        @if($cell('experiencias', $item, 'empresa'))<strong>{{ $cell('experiencias', $item, 'empresa') }}</strong><br>@endif
                        {{ $cell('experiencias', $item, 'cargo') }}
                        @if($cell('experiencias', $item, 'jefe_nombre') || $cell('experiencias', $item, 'jefe_telefono') || $cell('experiencias', $item, 'jefe_email'))
                            <div class="cv-cell-reference">
                                @if($cell('experiencias', $item, 'jefe_nombre'))<strong>Referencia:</strong> {{ $cell('experiencias', $item, 'jefe_nombre') }}<br>@endif
                                @if($cell('experiencias', $item, 'jefe_telefono'))<strong>Teléfono:</strong> {{ $cell('experiencias', $item, 'jefe_telefono') }}<br>@endif
                                @if($cell('experiencias', $item, 'jefe_email'))<strong>Correo:</strong> {{ $cell('experiencias', $item, 'jefe_email') }}@endif
                            </div>
                        @endif
                    </td>
                    <td>{{ $item['pais'] ?: 'No registrado' }}</td>
                    <td>{{ $cell('experiencias', $item, 'descripcion') }}</td>
                </tr>
            @empty
                <tr><td colspan="4"><em>No registrado</em></td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="cv-numbered-title"><span>5</span> Asociaciones profesionales y publicaciones</h2>
    <table class="cv-table cv-institutional-table">
        <tr><th style="width:32%">Asociaciones profesionales</th><td><em>No registrado</em></td></tr>
        <tr><th>Publicaciones</th><td><em>No registrado</em></td></tr>
    </table>

    <h2 class="cv-numbered-title"><span>6</span> Idiomas</h2>
    <table class="cv-table cv-institutional-table">
        <thead><tr><th>Idioma</th><th>Nivel</th><th>Certificado</th></tr></thead>
        <tbody>
            @forelse($idiomas as $item)
                <tr>
                    <td>{{ $cell('idiomas', $item, 'idioma') }}</td>
                    <td>{{ $cell('idiomas', $item, 'nivel') }}</td>
                    <td>
                        @if($selected('idiomas', $item['id'], 'certificado_url') && $item['certificado_url'])
                            <a href="{{ $item['certificado_url'] }}">Ver certificado</a>
                        @else
                            <em>No registrado</em>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="3"><em>No registrado</em></td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="cv-numbered-title"><span>7</span> Idoneidad para el trabajo</h2>
    <table class="cv-table cv-institutional-table cv-suitability-table">
        <tr><th>Tareas detalladas asignadas</th><td class="cv-manual-text">{!! $tareas !== '' ? nl2br(e($tareas)) : '<em>Pendiente de completar por FEPADE</em>' !!}</td></tr>
        <tr><th>Áreas de especialización relevantes</th><td>@forelse($areas as $area)<span class="cv-inline-tag">{{ $area['nombre'] }}</span>@empty<em>No registrado</em>@endforelse</td></tr>
        <tr><th>Habilidades técnicas relacionadas</th><td>@forelse($habilidades as $habilidad)<span class="cv-inline-tag cv-inline-tag-muted">{{ $habilidad }}</span>@empty<em>No registrado</em>@endforelse</td></tr>
    </table>

    <h2 class="cv-numbered-title"><span>8</span> Información de contacto</h2>
    @php
        $emails = collect($cvData['emails'])->filter(fn ($item) => $visible('emails', $item, ['email']));
        $telefonos = collect($cvData['telefonos'])->filter(fn ($item) => $visible('telefonos', $item, ['numero']));
    @endphp
    @forelse($emails as $item)<p><strong>Correo:</strong> {{ $cell('emails', $item, 'email') }}</p>@empty @endforelse
    @forelse($telefonos as $item)<p><strong>Teléfono:</strong> {{ $cell('telefonos', $item, 'numero') }}</p>@empty @endforelse
    @if($emails->isEmpty() && $telefonos->isEmpty())<p><em>No registrado</em></p>@endif

    <h2 class="cv-numbered-title"><span>9</span> Certificación</h2>
    <p>Yo, la/el abajo firmante, certifico que este currículum describe correctamente mi persona, mis calificaciones y mi experiencia.</p>

    <table class="cv-signature-block"><tr><td>Nombre del Consultor/a</td><td>Firma</td><td>Fecha</td></tr></table>
    <table class="cv-signature-block cv-signature-authority"><tr><td><strong>Ana María Porras de Bardi</strong><br>Directora Ejecutiva y Representante Legal<br>FEPADE</td><td>Firma</td><td>Fecha</td></tr></table>
</div>
