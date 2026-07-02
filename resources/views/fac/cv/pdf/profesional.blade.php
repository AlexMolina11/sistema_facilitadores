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

    $emails = collect($cvData['emails'] ?? [])->filter(fn($item) => $visible('emails', $item, ['email']));

    $telefonos = collect($cvData['telefonos'] ?? [])->filter(fn($item) => $visible('telefonos', $item, ['tipo', 'extension', 'numero']));

    $areas = collect($cvData['areas'] ?? [])->filter(fn($item) => $selected('areas', $item['id'], 'nombre'));

    $idiomas = collect($cvData['idiomas'] ?? [])->filter(fn($item) => $visible('idiomas', $item, ['idioma', 'nivel', 'certificado_url']));

    $disponibilidades = collect($cvData['disponibilidades'] ?? [])->filter(fn($item) => $visible('disponibilidades', $item, ['nombre']));

    $experiencias = collect($cvData['experiencias'] ?? [])->filter(fn($item) => $visible('experiencias', $item, [
        'empresa',
        'cargo',
        'descripcion',
        'desde',
        'hasta',
        'trabajo_actual',
        'jefe_nombre',
        'jefe_email',
        'jefe_telefono',
    ]));

    $atestados = collect($cvData['atestados'] ?? [])->filter(fn($item) => $visible('atestados', $item, [
        'tipo_formacion',
        'tipo_atestado',
        'nivel',
        'titulo',
        'institucion',
        'descripcion',
        'pais',
        'fecha_inicio',
        'fecha_fin',
        'fecha_emision',
        'fecha_vencimiento',
        'horas',
        'archivo_url',
    ]));

    $capacitaciones = collect($cvData['capacitaciones_fepade'] ?? [])->filter(fn($item) => $visible('capacitaciones_fepade', $item, [
        'nombre_evento',
        'tema',
        'institucion',
        'modalidad',
        'fecha_inicio',
        'fecha_fin',
        'horas',
        'fuente',
    ]));

    $referencias = collect($cvData['referencias'] ?? [])->filter(fn($item) => $visible('referencias', $item, [
        'tipo',
        'nombre',
        'telefono',
        'correo',
        'empresa',
        'cargo',
    ]));

    $referenciasPorTipo = $referencias->groupBy(fn($item) => $item['tipo'] ?: 'Sin tipo');

    $fotoPdf = $selected('personal', 'personal', 'foto') ? data_get($cvData, 'personal.foto_pdf') : null;

    $hayContacto = $emails->count() || $telefonos->count() || $personal('direccion');
    $haySidebar = $hayContacto || $areas->count() || $idiomas->count() || $disponibilidades->count();
@endphp

<style>
    @page {
        margin: 0;
    }

    body {
        margin: 0;
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 10.5px;
        color: #1f2937;
        background: #ffffff;
        line-height: 1.35;
    }

    .cv-pro-wrapper {
        width: 100%;
        border-collapse: collapse;
    }

    .cv-pro-sidebar {
        width: 31%;
        background: #0D1B2A;
        color: #ffffff;
        vertical-align: top;
        padding: 26px 20px;
    }

    .cv-pro-main {
        width: 69%;
        vertical-align: top;
        padding: 30px 34px;
    }

    .cv-pro-photo {
        width: 118px;
        height: 118px;
        border-radius: 59px;
        overflow: hidden;
        border: 4px solid #ffffff;
        margin: 0 auto 18px;
        background: #eef2f8;
        text-align: center;
    }

    .cv-pro-photo img {
        width: 118px;
        height: 118px;
        object-fit: cover;
    }

    .cv-pro-photo-empty {
        height: 118px;
        line-height: 118px;
        color: #0D1B2A;
        font-size: 30px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .cv-pro-sidebar-title {
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: .07em;
        border-bottom: 1px solid rgba(255,255,255,.35);
        padding-bottom: 6px;
        margin: 20px 0 9px;
        font-weight: bold;
        color: #A0C525;
    }

    .cv-pro-sidebar p {
        margin: 0 0 7px;
        font-size: 9.8px;
        word-break: break-word;
    }

    .cv-pro-sidebar ul {
        margin: 0;
        padding-left: 15px;
    }

    .cv-pro-sidebar li {
        margin-bottom: 6px;
        font-size: 9.8px;
    }

    .cv-pro-sidebar a {
        color: #A0C525;
        text-decoration: none;
    }

    .cv-pro-name {
        font-size: 25px;
        line-height: 1.05;
        color: #0D1B2A;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin: 0;
    }

    .cv-pro-role {
        font-size: 12px;
        color: #385506;
        font-weight: bold;
        text-transform: uppercase;
        margin: 6px 0 13px;
    }

    .cv-pro-meta {
        border-top: 3px solid #0D1B2A;
        border-bottom: 1px solid #d8dee4;
        padding: 9px 0;
        margin-bottom: 18px;
        color: #475569;
        font-size: 10px;
    }

    .cv-pro-meta div {
        margin-bottom: 3px;
    }

    .cv-pro-section {
        margin-bottom: 18px;
    }

    .cv-pro-section-title {
        color: #0D1B2A;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 5px;
        margin: 0 0 10px;
        font-weight: bold;
        letter-spacing: .03em;
    }

    .cv-pro-item {
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .cv-pro-item-title {
        color: #0D1B2A;
        font-weight: bold;
        font-size: 11.5px;
        margin-right: 90px;
    }

    .cv-pro-date {
        float: right;
        color: #64748b;
        font-size: 9.5px;
        text-align: right;
        max-width: 115px;
    }

    .cv-pro-place {
        color: #385506;
        font-weight: bold;
        margin: 3px 0 4px;
    }

    .cv-pro-small {
        color: #64748b;
        font-size: 9.5px;
        margin-top: 2px;
    }

    .cv-pro-tags {
        margin-top: 4px;
    }

    .cv-pro-tag {
        display: inline-block;
        background: #eef5d7;
        color: #385506;
        border: 1px solid #d9e7a8;
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 8.8px;
        margin: 2px 2px 2px 0;
    }

    .cv-pro-link {
        color: #0065cc;
        text-decoration: none;
        font-weight: bold;
    }

    .cv-pro-grid {
        width: 100%;
        border-collapse: collapse;
    }

    .cv-pro-grid td {
        width: 50%;
        vertical-align: top;
        padding: 0 8px 8px 0;
    }

    .cv-pro-ref {
        border: 1px solid #d8dee4;
        border-radius: 8px;
        padding: 8px;
        background: #fbfcfd;
    }

    .cv-pro-ref strong,
    .cv-pro-ref span {
        display: block;
    }

    .cv-pro-ref-type {
        color: #385506;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .cv-pro-empty {
        color: #64748b;
        font-style: italic;
    }

    .clear {
        clear: both;
    }
</style>

<table class="cv-pro-wrapper">
    <tr>
        <td class="cv-pro-sidebar">
            @if($fotoPdf && file_exists($fotoPdf))
                <div class="cv-pro-photo">
                    <img src="{{ $fotoPdf }}" alt="Foto">
                </div>
            @else
                <div class="cv-pro-photo">
                    <div class="cv-pro-photo-empty">
                        {{ mb_substr($personal('nombre') ?: 'CV', 0, 2) }}
                    </div>
                </div>
            @endif

            @if($hayContacto)
                <div class="cv-pro-sidebar-title">Contacto</div>

                @foreach($emails as $item)
                    @if($cell('emails', $item, 'email'))
                        <p>{{ $cell('emails', $item, 'email') }}</p>
                    @endif
                @endforeach

                @foreach($telefonos as $item)
                    <p>
                        @if($cell('telefonos', $item, 'tipo'))
                            {{ $cell('telefonos', $item, 'tipo') }}:
                        @endif

                        @if($cell('telefonos', $item, 'extension'))
                            ({{ $cell('telefonos', $item, 'extension') }})
                        @endif

                        {{ $cell('telefonos', $item, 'numero') }}
                    </p>
                @endforeach

                @if($personal('direccion'))
                    <p>{{ $personal('direccion') }}</p>
                @endif
            @endif

            @if($areas->count())
                <div class="cv-pro-sidebar-title">Áreas de especialización</div>
                <ul>
                    @foreach($areas as $area)
                        <li>{{ $area['nombre'] }}</li>
                    @endforeach
                </ul>
            @endif

            @php
                $habilidadesSeleccionadas = collect();

                foreach (($cvData['areas'] ?? []) as $area) {
                    foreach (($area['habilidades'] ?? []) as $hab) {
                        if (data_get($config, "selections.habilidades_area_{$area['id']}.{$hab['id']}.nombre") === true && ! blank($hab['nombre'])) {
                            $habilidadesSeleccionadas->push($hab['nombre']);
                        }
                    }
                }
            @endphp

            @if($habilidadesSeleccionadas->count())
                <div class="cv-pro-sidebar-title">Habilidades técnicas</div>
                <ul>
                    @foreach($habilidadesSeleccionadas->unique() as $habilidad)
                        <li>{{ $habilidad }}</li>
                    @endforeach
                </ul>
            @endif

            @if($idiomas->count())
                <div class="cv-pro-sidebar-title">Idiomas</div>
                <ul>
                    @foreach($idiomas as $item)
                        <li>
                            @if($cell('idiomas', $item, 'idioma'))
                                {{ $cell('idiomas', $item, 'idioma') }}
                            @endif

                            @if($cell('idiomas', $item, 'nivel'))
                                — {{ $cell('idiomas', $item, 'nivel') }}
                            @endif

                            @if($cell('idiomas', $item, 'certificado_url'))
                                <br>
                                <a href="{{ $cell('idiomas', $item, 'certificado_url') }}">Ver certificado</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($disponibilidades->count())
                <div class="cv-pro-sidebar-title">Disponibilidad</div>
                <ul>
                    @foreach($disponibilidades as $item)
                        <li>{{ $cell('disponibilidades', $item, 'nombre') }}</li>
                    @endforeach
                </ul>
            @endif
        </td>

        <td class="cv-pro-main">
            <h1 class="cv-pro-name">{{ $personal('nombre') ?: 'Consultor FEPADE' }}</h1>
            <div class="cv-pro-role">Consultor/a profesional FEPADE</div>

            <div class="cv-pro-meta">
                @if($personal('nacionalidad'))
                    <div><strong>Nacionalidad:</strong> {{ $personal('nacionalidad') }}</div>
                @endif

                @if($personal('residencia_completa') || $personal('residencia'))
                    <div><strong>Residencia:</strong> {{ $personal('residencia_completa') ?: $personal('residencia') }}</div>
                @endif

                @if($personal('fecha_nacimiento'))
                    <div><strong>Fecha de nacimiento:</strong> {{ $personal('fecha_nacimiento') }}</div>
                @endif
            </div>

            @if($experiencias->count())
                <div class="cv-pro-section">
                    <h2 class="cv-pro-section-title">Experiencia profesional</h2>

                    @foreach($experiencias as $item)
                        <div class="cv-pro-item">
                            @if($cell('experiencias', $item, 'desde') || $cell('experiencias', $item, 'hasta'))
                                <div class="cv-pro-date">
                                    {{ $cell('experiencias', $item, 'desde') }}
                                    @if($cell('experiencias', $item, 'hasta'))
                                        - {{ $cell('experiencias', $item, 'hasta') }}
                                    @endif
                                </div>
                            @endif

                            @if($cell('experiencias', $item, 'cargo'))
                                <div class="cv-pro-item-title">{{ $cell('experiencias', $item, 'cargo') }}</div>
                            @endif

                            @if($cell('experiencias', $item, 'empresa'))
                                <div class="cv-pro-place">{{ $cell('experiencias', $item, 'empresa') }}</div>
                            @endif

                            @if($cell('experiencias', $item, 'descripcion'))
                                <div>{{ $cell('experiencias', $item, 'descripcion') }}</div>
                            @endif

                            @if($cell('experiencias', $item, 'jefe_nombre') || $cell('experiencias', $item, 'jefe_email') || $cell('experiencias', $item, 'jefe_telefono'))
                                <div class="cv-pro-small">
                                    @if($cell('experiencias', $item, 'jefe_nombre'))
                                        Referencia: {{ $cell('experiencias', $item, 'jefe_nombre') }}
                                    @endif

                                    @if($cell('experiencias', $item, 'jefe_telefono'))
                                        | Tel. {{ $cell('experiencias', $item, 'jefe_telefono') }}
                                    @endif

                                    @if($cell('experiencias', $item, 'jefe_email'))
                                        | {{ $cell('experiencias', $item, 'jefe_email') }}
                                    @endif
                                </div>
                            @endif

                            <div class="clear"></div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($atestados->count())
                <div class="cv-pro-section">
                    <h2 class="cv-pro-section-title">Formación, atestados y trayectoria profesional</h2>

                    @foreach($atestados as $item)
                        <div class="cv-pro-item">
                            @if($cell('atestados', $item, 'fecha_inicio') || $cell('atestados', $item, 'fecha_fin'))
                                <div class="cv-pro-date">
                                    {{ $cell('atestados', $item, 'fecha_inicio') }}
                                    @if($cell('atestados', $item, 'fecha_fin'))
                                        - {{ $cell('atestados', $item, 'fecha_fin') }}
                                    @endif
                                </div>
                            @endif

                            @if($cell('atestados', $item, 'titulo'))
                                <div class="cv-pro-item-title">{{ $cell('atestados', $item, 'titulo') }}</div>
                            @endif

                            @if($cell('atestados', $item, 'institucion'))
                                <div class="cv-pro-place">{{ $cell('atestados', $item, 'institucion') }}</div>
                            @endif

                            <div class="cv-pro-tags">
                                @if($cell('atestados', $item, 'tipo_formacion'))
                                    <span class="cv-pro-tag">{{ $cell('atestados', $item, 'tipo_formacion') }}</span>
                                @endif

                                @if($cell('atestados', $item, 'tipo_atestado'))
                                    <span class="cv-pro-tag">{{ $cell('atestados', $item, 'tipo_atestado') }}</span>
                                @endif

                                @if($cell('atestados', $item, 'nivel'))
                                    <span class="cv-pro-tag">{{ $cell('atestados', $item, 'nivel') }}</span>
                                @endif

                                @if($cell('atestados', $item, 'pais'))
                                    <span class="cv-pro-tag">{{ $cell('atestados', $item, 'pais') }}</span>
                                @endif

                                @if($cell('atestados', $item, 'horas'))
                                    <span class="cv-pro-tag">{{ $cell('atestados', $item, 'horas') }} horas</span>
                                @endif
                            </div>

                            @if($cell('atestados', $item, 'fecha_emision') || $cell('atestados', $item, 'fecha_vencimiento'))
                                <div class="cv-pro-small">
                                    @if($cell('atestados', $item, 'fecha_emision'))
                                        Emisión: {{ $cell('atestados', $item, 'fecha_emision') }}
                                    @endif

                                    @if($cell('atestados', $item, 'fecha_vencimiento'))
                                        | Vence: {{ $cell('atestados', $item, 'fecha_vencimiento') }}
                                    @endif
                                </div>
                            @endif

                            @if($cell('atestados', $item, 'descripcion'))
                                <div>{{ $cell('atestados', $item, 'descripcion') }}</div>
                            @endif

                            @if($cell('atestados', $item, 'archivo_url'))
                                <div class="cv-pro-small">
                                    <a class="cv-pro-link" href="{{ $cell('atestados', $item, 'archivo_url') }}">Ver atestado</a>
                                </div>
                            @endif

                            <div class="clear"></div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($capacitaciones->count())
                <div class="cv-pro-section">
                    <h2 class="cv-pro-section-title">Capacitaciones FEPADE</h2>

                    @foreach($capacitaciones as $item)
                        <div class="cv-pro-item">
                            @if($cell('capacitaciones_fepade', $item, 'fecha_inicio') || $cell('capacitaciones_fepade', $item, 'fecha_fin'))
                                <div class="cv-pro-date">
                                    {{ $cell('capacitaciones_fepade', $item, 'fecha_inicio') }}
                                    @if($cell('capacitaciones_fepade', $item, 'fecha_fin'))
                                        - {{ $cell('capacitaciones_fepade', $item, 'fecha_fin') }}
                                    @endif
                                </div>
                            @endif

                            @if($cell('capacitaciones_fepade', $item, 'nombre_evento'))
                                <div class="cv-pro-item-title">{{ $cell('capacitaciones_fepade', $item, 'nombre_evento') }}</div>
                            @endif

                            @if($cell('capacitaciones_fepade', $item, 'institucion'))
                                <div class="cv-pro-place">{{ $cell('capacitaciones_fepade', $item, 'institucion') }}</div>
                            @endif

                            <div class="cv-pro-tags">
                                @if($cell('capacitaciones_fepade', $item, 'tema'))
                                    <span class="cv-pro-tag">{{ $cell('capacitaciones_fepade', $item, 'tema') }}</span>
                                @endif

                                @if($cell('capacitaciones_fepade', $item, 'modalidad'))
                                    <span class="cv-pro-tag">{{ $cell('capacitaciones_fepade', $item, 'modalidad') }}</span>
                                @endif

                                @if($cell('capacitaciones_fepade', $item, 'horas'))
                                    <span class="cv-pro-tag">{{ $cell('capacitaciones_fepade', $item, 'horas') }} horas</span>
                                @endif

                                @if($cell('capacitaciones_fepade', $item, 'fuente'))
                                    <span class="cv-pro-tag">{{ $cell('capacitaciones_fepade', $item, 'fuente') }}</span>
                                @endif
                            </div>

                            <div class="clear"></div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($referenciasPorTipo->count())
                <div class="cv-pro-section">
                    <h2 class="cv-pro-section-title">Referencias</h2>

                    @foreach($referenciasPorTipo as $tipo => $items)
                        <div class="cv-pro-ref-type">{{ $tipo }}</div>

                        <table class="cv-pro-grid">
                            @foreach($items->chunk(2) as $fila)
                                <tr>
                                    @foreach($fila as $item)
                                        <td>
                                            <div class="cv-pro-ref">
                                                @if($cell('referencias', $item, 'nombre'))
                                                    <strong>{{ $cell('referencias', $item, 'nombre') }}</strong>
                                                @endif

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
                    @endforeach
                </div>
            @endif
        </td>
    </tr>
</table>