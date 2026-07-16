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

    $especialidades = collect($cvData['atestados'])->filter(fn ($item) =>
        in_array((int) ($item['id_tipo_formacion'] ?? 0), $idsTipos('acreditacion', 'educacion_continua', 'capacitacion_recibida'), true)
        && $visible('atestados', $item, ['titulo', 'institucion'])
    );

    $experiencias = collect($cvData['experiencias'])->filter(fn ($item) =>
        $visible('experiencias', $item, ['empresa', 'cargo', 'desde', 'hasta'])
    );

    $areas = collect($cvData['areas'])->filter(fn ($area) => $selected('areas', $area['id'], 'nombre') && ! blank($area['nombre']));

    $nombreConsultoria = trim((string) data_get($config, 'campos_manual.nombre_consultoria', ''));
    $cargoPropuesto = trim((string) data_get($config, 'campos_manual.cargo_propuesto', ''));
    $actividades = trim((string) data_get($config, 'campos_manual.actividades_consultoria', ''));
@endphp

<div class="cv-summary-document">
    <div class="cv-summary-header">
        <div class="cv-summary-brand">FEPADE</div>
        <h1>RESUMEN DEL CV DEL PERSONAL PROPUESTO</h1>
        <p>Documento de síntesis para oferta o consultoría</p>
    </div>

    <table class="cv-summary-context">
        <tr>
            <td><strong>Consultoría o proceso</strong>{{ $nombreConsultoria !== '' ? $nombreConsultoria : 'Pendiente de completar por FEPADE' }}</td>
            <td><strong>Cargo propuesto</strong>{{ $cargoPropuesto !== '' ? $cargoPropuesto : 'Pendiente de completar por FEPADE' }}</td>
        </tr>
    </table>

    <table class="cv-table cv-summary-table">
        <tr class="cv-summary-section-row"><th colspan="2">Identificación</th></tr>
        <tr><th>Nombre del Oferente</th><td>Fundación Empresarial para el Desarrollo Educativo -FEPADE-</td></tr>
        <tr><th>Nombre del profesional propuesto</th><td>{{ $personal('nombre') }}</td></tr>
        <tr><th>Nacionalidad</th><td>{{ $personal('nacionalidad') ?: 'No registrado' }}</td></tr>

        <tr class="cv-summary-section-row"><th colspan="2">Formación y experiencia</th></tr>
        <tr>
            <th>Educación</th>
            <td>
                @forelse($educacionFormal as $item)
                    <div><strong>{{ $cell('atestados', $item, 'titulo') }}</strong>@if($cell('atestados', $item, 'institucion')) — {{ $cell('atestados', $item, 'institucion') }}@endif @if($cell('atestados', $item, 'fecha_fin')) ({{ $cell('atestados', $item, 'fecha_fin') }})@endif</div>
                @empty
                    <em>No registrado</em>
                @endforelse
            </td>
        </tr>
        <tr><th>Asociaciones profesionales a las que pertenece</th><td><em>No registrado</em></td></tr>
        <tr>
            <th>Otras especialidades</th>
            <td>
                @foreach($especialidades as $item)
                    <div><strong>{{ $cell('atestados', $item, 'titulo') }}</strong>@if($cell('atestados', $item, 'institucion')) — {{ $cell('atestados', $item, 'institucion') }}@endif</div>
                @endforeach
                @foreach($areas as $area)
                    @php
                        $habilidadesArea = collect($area['habilidades'] ?? [])->filter(fn ($hab) => $selected('habilidades_area_' . $area['id'], $hab['id'], 'nombre') && ! blank($hab['nombre']));
                    @endphp
                    <div><strong>{{ $area['nombre'] }}</strong>@if($habilidadesArea->isNotEmpty()): {{ $habilidadesArea->pluck('nombre')->implode(', ') }}@endif</div>
                @endforeach
                @if($especialidades->isEmpty() && $areas->isEmpty())<em>No registrado</em>@endif
            </td>
        </tr>
        <tr><th>Países donde tiene experiencia de trabajo</th><td><em>No registrado</em></td></tr>
        <tr>
            <th>Trabajos que ha realizado</th>
            <td>
                @forelse($experiencias as $item)
                    <div>
                        @if($cell('experiencias', $item, 'cargo'))<strong>{{ $cell('experiencias', $item, 'cargo') }}</strong>@endif
                        @if($cell('experiencias', $item, 'empresa')) — {{ $cell('experiencias', $item, 'empresa') }}@endif
                        @if($cell('experiencias', $item, 'desde') || $cell('experiencias', $item, 'hasta'))
                            <span class="cv-muted">({{ $cell('experiencias', $item, 'desde') }}{{ $cell('experiencias', $item, 'hasta') ? ' – ' . $cell('experiencias', $item, 'hasta') : '' }})</span>
                        @endif
                    </div>
                @empty
                    <em>No registrado</em>
                @endforelse
            </td>
        </tr>

        <tr class="cv-summary-section-row"><th colspan="2">Asignación de la consultoría</th></tr>
        <tr><th>Detalle de las actividades asignadas en esta consultoría</th><td class="cv-manual-text">{!! $actividades !== '' ? nl2br(e($actividades)) : '<em>Pendiente de completar por FEPADE</em>' !!}</td></tr>
    </table>
</div>
