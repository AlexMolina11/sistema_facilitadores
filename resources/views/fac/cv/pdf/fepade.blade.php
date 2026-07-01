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

<h1 class="cv-title">Hoja de Vida</h1>

<table class="cv-table cv-table-clean">
    <tr><th>Cargo:</th><td></td></tr>
    <tr><th>Nombre del Profesional:</th><td>{{ $personal('nombre') }}</td></tr>
    <tr><th>Fecha de nacimiento:</th><td>{{ $personal('fecha_nacimiento') }}</td></tr>
    <tr><th>País de ciudadanía/residencia:</th><td>{{ $personal('nacionalidad') ?: $personal('residencia') }}</td></tr>
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
        @forelse(collect($cvData['atestados'])->filter(fn($item) => $visible('atestados', $item, ['titulo', 'institucion', 'fecha_fin'])) as $item)
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
        <tr><th>Institución / área</th></tr>
    </thead>
    <tbody>
        @forelse(collect($cvData['areas'])->filter(fn($item) => $selected('areas', $item['id'], 'nombre')) as $item)
            <tr><td>{{ $item['nombre'] }}</td></tr>
        @empty
            <tr><td>&nbsp;</td></tr>
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
        @forelse(collect($cvData['atestados'])->filter(fn($item) => $visible('atestados', $item, ['titulo', 'institucion', 'fecha_inicio'])) as $item)
            <tr>
                <td>{{ $cell('atestados', $item, 'titulo') }}</td>
                <td>{{ $cell('atestados', $item, 'institucion') }}</td>
                <td>{{ $cell('atestados', $item, 'fecha_inicio') }}</td>
            </tr>
        @empty
            <tr><td colspan="3">&nbsp;</td></tr>
        @endforelse
    </tbody>
</table>

<h2>4. Países donde tiene experiencia de trabajo los últimos 10 años:</h2>
<p>{{ $personal('residencia') }}</p>

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
        @forelse(collect($cvData['experiencias'])->filter(fn($item) => $visible('experiencias', $item, ['desde', 'hasta', 'empresa', 'cargo'])) as $item)
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
            <th>Consultorías</th>
            <th>Empresa / organización</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @forelse(collect($cvData['atestados'])->filter(fn($item) => $visible('atestados', $item, ['titulo', 'institucion', 'fecha_fin'])) as $item)
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