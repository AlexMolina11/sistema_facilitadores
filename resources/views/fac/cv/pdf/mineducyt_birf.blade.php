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

<h1 class="cv-title">CURRÍCULUM</h1>

<table class="cv-table cv-table-clean">
    <tr><th>Nombre del cargo y número:</th><td></td></tr>
    <tr><th>Nombre del Experto:</th><td>{{ $personal('nombre') }}</td></tr>
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

<h2>2. Otras capacitaciones recibidas:</h2>
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

<h2>3. Experiencia laboral pertinente para el trabajo:</h2>
<table class="cv-table">
    <thead>
        <tr>
            <th>Período</th>
            <th>Entidad empleadora y referencias</th>
            <th>País</th>
            <th>Resumen</th>
        </tr>
    </thead>
    <tbody>
        @forelse(collect($cvData['experiencias'])->filter(fn($item) => $visible('experiencias', $item, ['empresa', 'cargo', 'descripcion', 'desde', 'hasta'])) as $item)
            <tr>
                <td>{{ $cell('experiencias', $item, 'desde') }} - {{ $cell('experiencias', $item, 'hasta') }}</td>
                <td>
                    <strong>Organización:</strong> {{ $cell('experiencias', $item, 'empresa') }}<br>
                    <strong>Cargo:</strong> {{ $cell('experiencias', $item, 'cargo') }}<br>
                    <strong>Referencia:</strong> {{ $cell('experiencias', $item, 'jefe_nombre') }}<br>
                    <strong>Teléfono:</strong> {{ $cell('experiencias', $item, 'jefe_telefono') }}<br>
                    <strong>Correo:</strong> {{ $cell('experiencias', $item, 'jefe_email') }}
                </td>
                <td>{{ $personal('residencia') }}</td>
                <td>{{ $cell('experiencias', $item, 'descripcion') }}</td>
            </tr>
        @empty
            <tr><td colspan="4">&nbsp;</td></tr>
        @endforelse
    </tbody>
</table>

<h2>4. Pertenencia a asociaciones profesionales y publicaciones:</h2>
<p><strong>Asociaciones profesionales:</strong></p>
<ul>
    @forelse(collect($cvData['areas'])->filter(fn($item) => $selected('areas', $item['id'], 'nombre')) as $item)
        <li>{{ $item['nombre'] }}</li>
    @empty
        <li>&nbsp;</li>
    @endforelse
</ul>

<p><strong>Publicaciones:</strong></p>
<ul>
    <li>&nbsp;</li>
</ul>

<h2>5. Idiomas:</h2>
<table class="cv-table">
    <thead>
        <tr>
            <th>Idioma</th>
            <th>Nivel</th>
            <th>Certificado</th>
        </tr>
    </thead>
    <tbody>
        @forelse(collect($cvData['idiomas'])->filter(fn($item) => $visible('idiomas', $item, ['idioma', 'nivel', 'certificado'])) as $item)
            <tr>
                <td>{{ $cell('idiomas', $item, 'idioma') }}</td>
                <td>{{ $cell('idiomas', $item, 'nivel') }}</td>
                <td>{{ $cell('idiomas', $item, 'certificado') }}</td>
            </tr>
        @empty
            <tr><td colspan="3">&nbsp;</td></tr>
        @endforelse
    </tbody>
</table>

<h2>6. Idoneidad para el trabajo:</h2>
<table class="cv-table">
    <tr>
        <th>Tareas detalladas asignadas al grupo de Expertos del Consultor:</th>
        <td>LLENADO POR FEPADE</td>
    </tr>
</table>

<h2>Información de contacto del Experto:</h2>
@foreach(collect($cvData['emails'])->filter(fn($item) => $visible('emails', $item, ['email'])) as $item)
    <p><strong>Correo:</strong> {{ $cell('emails', $item, 'email') }}</p>
@endforeach

@foreach(collect($cvData['telefonos'])->filter(fn($item) => $visible('telefonos', $item, ['numero'])) as $item)
    <p><strong>Teléfono:</strong> {{ $cell('telefonos', $item, 'numero') }}</p>
@endforeach

<h2>Certificación:</h2>
<p>
    Yo, la/el abajo firmante, certifico que este currículum describe correctamente mi persona,
    mis calificaciones y mi experiencia.
</p>

<div class="cv-signature">
    ______________________________________________________________________<br>
    Nombre del Consultor/a &nbsp;&nbsp;&nbsp;&nbsp; Firma &nbsp;&nbsp;&nbsp;&nbsp; Fecha
</div>

<div class="cv-signature">
    ______________________________________________________________________<br>
    Ana María Porras de Bardi &nbsp;&nbsp;&nbsp;&nbsp; Firma &nbsp;&nbsp;&nbsp;&nbsp; Fecha<br>
    Directora Ejecutiva y Representante Legal<br>
    FEPADE
</div>