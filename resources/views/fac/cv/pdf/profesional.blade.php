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

    $emails = collect($cvData['emails'])->filter(fn($item) => $visible('emails', $item, ['email']));
    $telefonos = collect($cvData['telefonos'])->filter(fn($item) => $visible('telefonos', $item, ['numero']));
    $areas = collect($cvData['areas'])->filter(fn($item) => $selected('areas', $item['id'], 'nombre'));
    $idiomas = collect($cvData['idiomas'])->filter(fn($item) => $visible('idiomas', $item, ['idioma', 'nivel', 'certificado']));
    $disponibilidades = collect($cvData['disponibilidades'])->filter(fn($item) => $visible('disponibilidades', $item, ['nombre']));
    $experiencias = collect($cvData['experiencias'])->filter(fn($item) => $visible('experiencias', $item, ['empresa', 'cargo', 'descripcion', 'desde', 'hasta']));
    $atestados = collect($cvData['atestados'])->filter(fn($item) => $visible('atestados', $item, ['titulo', 'institucion', 'tipo_formacion', 'fecha_fin', 'descripcion']));
    $capacitaciones = collect($cvData['capacitaciones_fepade'])->filter(fn($item) => $visible('capacitaciones_fepade', $item, ['nombre_evento', 'tema', 'institucion', 'fecha_inicio', 'fecha_fin', 'horas']));
    $referencias = collect($cvData['referencias'])->filter(fn($item) => $visible('referencias', $item, ['nombre', 'telefono', 'correo', 'empresa', 'cargo']));
@endphp

<style>
    body {
        margin: 0;
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 10.5px;
        color: #1f2937;
    }

    .pro-layout {
        width: 100%;
        border-collapse: collapse;
    }

    .pro-sidebar {
        width: 30%;
        background: #0D1B2A;
        color: #ffffff;
        vertical-align: top;
        padding: 24px 18px;
    }

    .pro-main {
        width: 70%;
        vertical-align: top;
        padding: 28px 30px;
    }

    .pro-photo {
        width: 120px;
        height: 120px;
        border-radius: 60px;
        overflow: hidden;
        border: 4px solid #ffffff;
        margin: 0 auto 18px;
        text-align: center;
        background: #eef2f8;
    }

    .pro-photo img {
        width: 120px;
        height: 120px;
        object-fit: cover;
    }

    .pro-photo-empty {
        color: #0D1B2A;
        font-size: 30px;
        font-weight: bold;
        line-height: 120px;
    }

    .pro-sidebar-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        border-bottom: 1px solid rgba(255,255,255,.35);
        padding-bottom: 6px;
        margin: 20px 0 9px;
        font-weight: bold;
    }

    .pro-sidebar p,
    .pro-sidebar li {
        font-size: 10px;
        line-height: 1.35;
    }

    .pro-sidebar ul {
        margin: 0;
        padding-left: 15px;
    }

    .pro-name {
        font-size: 25px;
        color: #0D1B2A;
        line-height: 1.05;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .pro-role {
        font-size: 12px;
        color: #0065cc;
        font-weight: bold;
        text-transform: uppercase;
        margin: 6px 0 14px;
    }

    .pro-meta {
        border-top: 2px solid #0D1B2A;
        border-bottom: 1px solid #d8dee4;
        padding: 8px 0;
        margin-bottom: 18px;
        color: #475569;
    }

    .pro-section {
        margin-bottom: 18px;
    }

    .pro-section-title {
        color: #0D1B2A;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 5px;
        margin: 0 0 10px;
        font-weight: bold;
    }

    .pro-item {
        margin-bottom: 11px;
        padding-bottom: 9px;
        border-bottom: 1px solid #e5e7eb;
    }

    .pro-item-title {
        color: #0D1B2A;
        font-weight: bold;
        font-size: 11.5px;
    }

    .pro-item-date {
        float: right;
        color: #64748b;
        font-size: 10px;
    }

    .pro-place {
        color: #0065cc;
        font-weight: bold;
        margin: 2px 0 4px;
    }

    .pro-small {
        color: #64748b;
        font-size: 9.5px;
    }

    .pro-grid {
        width: 100%;
        border-collapse: collapse;
    }

    .pro-grid td {
        width: 50%;
        vertical-align: top;
        padding: 0 8px 8px 0;
    }

    .pro-ref {
        border: 1px solid #d8dee4;
        border-radius: 8px;
        padding: 8px;
    }

    .pro-ref strong,
    .pro-ref span {
        display: block;
    }

    .clear {
        clear: both;
    }
</style>

<table class="pro-layout">
    <tr>
        <td class="pro-sidebar">
            <div class="pro-photo">
                @if(data_get($cvData, 'personal.foto'))
                    <img src="{{ data_get($cvData, 'personal.foto') }}" alt="Foto">
                @else
                    <div class="pro-photo-empty">
                        {{ mb_substr($personal('nombre') ?: 'CV', 0, 2) }}
                    </div>
                @endif
            </div>

            @if($emails->count() || $telefonos->count() || $personal('direccion'))
                <div class="pro-sidebar-title">Contacto</div>

                @foreach($emails as $item)
                    <p>{{ $cell('emails', $item, 'email') }}</p>
                @endforeach

                @foreach($telefonos as $item)
                    <p>{{ $cell('telefonos', $item, 'numero') }}</p>
                @endforeach

                @if($personal('direccion'))
                    <p>{{ $personal('direccion') }}</p>
                @endif
            @endif

            @if($areas->count())
                <div class="pro-sidebar-title">Áreas de especialización</div>
                <ul>
                    @foreach($areas as $item)
                        <li>{{ $item['nombre'] }}</li>
                    @endforeach
                </ul>
            @endif

            @if(collect($cvData['areas'])->contains(fn($area) => collect($area['habilidades'])->isNotEmpty()))
                <div class="pro-sidebar-title">Habilidades técnicas</div>
                <ul>
                    @foreach($cvData['areas'] as $area)
                        @foreach($area['habilidades'] as $hab)
                            @if(data_get($config, "selections.habilidades_area_{$area['id']}.{$hab['id']}.nombre") === true)
                                <li>{{ $hab['nombre'] }}</li>
                            @endif
                        @endforeach
                    @endforeach
                </ul>
            @endif

            @if($idiomas->count())
                <div class="pro-sidebar-title">Idiomas</div>
                <ul>
                    @foreach($idiomas as $item)
                        <li>
                            {{ $cell('idiomas', $item, 'idioma') }}
                            @if($cell('idiomas', $item, 'nivel'))
                                — {{ $cell('idiomas', $item, 'nivel') }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($disponibilidades->count())
                <div class="pro-sidebar-title">Disponibilidad</div>
                <ul>
                    @foreach($disponibilidades as $item)
                        <li>{{ $cell('disponibilidades', $item, 'nombre') }}</li>
                    @endforeach
                </ul>
            @endif
        </td>

        <td class="pro-main">
            <h1 class="pro-name">{{ $personal('nombre') }}</h1>
            <div class="pro-role">Consultor/a profesional FEPADE</div>

            <div class="pro-meta">
                @if($personal('nacionalidad'))
                    <strong>Nacionalidad:</strong> {{ $personal('nacionalidad') }} &nbsp;&nbsp;
                @endif

                @if($personal('residencia'))
                    <strong>Residencia:</strong> {{ $personal('residencia') }} &nbsp;&nbsp;
                @endif

                @if($personal('fecha_nacimiento'))
                    <strong>Fecha de nacimiento:</strong> {{ $personal('fecha_nacimiento') }}
                @endif
            </div>

            @if($experiencias->count())
                <div class="pro-section">
                    <h2 class="pro-section-title">Experiencia profesional</h2>

                    @foreach($experiencias as $item)
                        <div class="pro-item">
                            <span class="pro-item-date">
                                {{ $cell('experiencias', $item, 'desde') }}
                                @if($cell('experiencias', $item, 'hasta'))
                                    - {{ $cell('experiencias', $item, 'hasta') }}
                                @endif
                            </span>

                            <div class="pro-item-title">{{ $cell('experiencias', $item, 'cargo') }}</div>
                            <div class="pro-place">{{ $cell('experiencias', $item, 'empresa') }}</div>

                            @if($cell('experiencias', $item, 'descripcion'))
                                <div>{{ $cell('experiencias', $item, 'descripcion') }}</div>
                            @endif

                            <div class="clear"></div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($atestados->count())
                <div class="pro-section">
                    <h2 class="pro-section-title">Formación, atestados y trayectoria profesional</h2>

                    @foreach($atestados as $item)
                        <div class="pro-item">
                            <span class="pro-item-date">{{ $cell('atestados', $item, 'fecha_fin') }}</span>

                            <div class="pro-item-title">{{ $cell('atestados', $item, 'titulo') }}</div>
                            <div class="pro-place">{{ $cell('atestados', $item, 'institucion') }}</div>

                            @if($cell('atestados', $item, 'tipo_formacion'))
                                <div class="pro-small">{{ $cell('atestados', $item, 'tipo_formacion') }}</div>
                            @endif

                            @if($cell('atestados', $item, 'descripcion'))
                                <div>{{ $cell('atestados', $item, 'descripcion') }}</div>
                            @endif

                            <div class="clear"></div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($capacitaciones->count())
                <div class="pro-section">
                    <h2 class="pro-section-title">Capacitaciones FEPADE</h2>

                    @foreach($capacitaciones as $item)
                        <div class="pro-item">
                            <span class="pro-item-date">
                                {{ $cell('capacitaciones_fepade', $item, 'fecha_inicio') }}
                                @if($cell('capacitaciones_fepade', $item, 'fecha_fin'))
                                    - {{ $cell('capacitaciones_fepade', $item, 'fecha_fin') }}
                                @endif
                            </span>

                            <div class="pro-item-title">{{ $cell('capacitaciones_fepade', $item, 'nombre_evento') }}</div>
                            <div class="pro-place">{{ $cell('capacitaciones_fepade', $item, 'institucion') }}</div>

                            @if($cell('capacitaciones_fepade', $item, 'tema'))
                                <div>{{ $cell('capacitaciones_fepade', $item, 'tema') }}</div>
                            @endif

                            @if($cell('capacitaciones_fepade', $item, 'horas'))
                                <div class="pro-small">{{ $cell('capacitaciones_fepade', $item, 'horas') }} horas</div>
                            @endif

                            <div class="clear"></div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($referencias->count())
                <div class="pro-section">
                    <h2 class="pro-section-title">Referencias</h2>

                    <table class="pro-grid">
                        @foreach($referencias->chunk(2) as $fila)
                            <tr>
                                @foreach($fila as $item)
                                    <td>
                                        <div class="pro-ref">
                                            <strong>{{ $cell('referencias', $item, 'nombre') }}</strong>

                                            @if($cell('referencias', $item, 'cargo'))
                                                <span>{{ $cell('referencias', $item, 'cargo') }}</span>
                                            @endif

                                            @if($cell('referencias', $item, 'empresa'))
                                                <span>{{ $cell('referencias', $item, 'empresa') }}</span>
                                            @endif

                                            @if($cell('referencias', $item, 'telefono'))
                                                <span>{{ $cell('referencias', $item, 'telefono') }}</span>
                                            @endif

                                            @if($cell('referencias', $item, 'correo'))
                                                <span>{{ $cell('referencias', $item, 'correo') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach

                                @if($fila->count() === 1)
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
        </td>
    </tr>
</table>