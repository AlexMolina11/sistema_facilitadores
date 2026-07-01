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
    $primerAtestado = collect($cvData['atestados'])->first();
    $primeraExperiencia = collect($cvData['experiencias'])->first();
@endphp

<h1 class="cv-title">Resumen del CV del personal propuesto</h1>

<table class="cv-table">
    <tr>
        <th style="width: 35%;">Campo</th>
        <th>Información a completar</th>
    </tr>

    <tr>
        <td>Nombre del Oferente</td>
        <td>Fundación Empresarial para el Desarrollo Educativo -FEPADE-</td>
    </tr>

    <tr>
        <td>Nombre del profesional propuesto</td>
        <td>{{ $personal('nombre') }}</td>
    </tr>

    <tr>
        <td>Nacionalidad</td>
        <td>{{ $personal('nacionalidad') }}</td>
    </tr>

    <tr>
        <td>Educación</td>
        <td>
            @if($primerAtestado)
                Título: {{ $cell('atestados', $primerAtestado, 'titulo') }}<br>
                Institución: {{ $cell('atestados', $primerAtestado, 'institucion') }}
            @endif
        </td>
    </tr>

    <tr>
        <td>Asociaciones profesionales a las que pertenece</td>
        <td>
            @forelse(collect($cvData['areas'])->filter(fn($item) => $selected('areas', $item['id'], 'nombre')) as $item)
                {{ $item['nombre'] }}<br>
            @empty
                &nbsp;
            @endforelse
        </td>
    </tr>

    <tr>
        <td>Otras especialidades</td>
        <td>
            @if($primerAtestado)
                Certificado: {{ $cell('atestados', $primerAtestado, 'titulo') }}<br>
                Institución: {{ $cell('atestados', $primerAtestado, 'institucion') }}
            @endif
        </td>
    </tr>

    <tr>
        <td>Países donde tiene experiencia de trabajo</td>
        <td>{{ $personal('residencia') }}</td>
    </tr>

    <tr>
        <td>Trabajos que ha realizado</td>
        <td>
            @if($primeraExperiencia)
                Cargo: {{ $cell('experiencias', $primeraExperiencia, 'cargo') }}<br>
                Institución: {{ $cell('experiencias', $primeraExperiencia, 'empresa') }}
            @endif
        </td>
    </tr>

    <tr>
        <td>Detalle de las actividades asignadas en esta consultoría</td>
        <td>COMPLETADO POR FEPADE</td>
    </tr>
</table>