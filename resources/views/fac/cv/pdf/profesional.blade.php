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

    $iniciales = collect(explode(' ', $personal('nombre') ?: 'CV'))
        ->filter()
        ->take(2)
        ->map(fn ($parte) => mb_substr($parte, 0, 1))
        ->implode('');

    $hayContacto = $emails->count() || $telefonos->count() || $personal('direccion');
    $haySidebar = $hayContacto || $areas->count() || $idiomas->count() || $disponibilidades->count();
@endphp

<style>
    @page {
        margin: 24px;
    }

    body {
        margin: 0;
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 10.3px;
        color: #1f2937;
        line-height: 1.35;
        background: #ffffff;
    }

    .cv-pro-document {
        width: 100%;
    }

    .cv-pro-header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        background: #f8fafc;
        border: 1px solid #dfe5ea;
        border-left: 8px solid #0D1B2A;
    }

    .cv-pro-header-photo-cell {
        width: 145px;
        vertical-align: top;
        padding: 18px 18px 18px 20px;
    }

    .cv-pro-header-info-cell {
        vertical-align: middle;
        padding: 18px 22px 18px 0;
    }

    .cv-pro-photo {
        width: 112px;
        height: 112px;
        border-radius: 56px;
        overflow: hidden;
        border: 4px solid #ffffff;
        background: #eef2f8;
        text-align: center;
        box-shadow: 0 0 0 2px #0D1B2A;
    }

    .cv-pro-photo img {
        width: 112px;
        height: 112px;
        object-fit: cover;
    }

    .cv-pro-photo-empty {
        height: 112px;
        line-height: 112px;
        font-size: 30px;
        font-weight: bold;
        color: #0D1B2A;
        text-transform: uppercase;
    }

    .cv-pro-name {
        margin: 0;
        font-size: 27px;
        font-weight: bold;
        color: #0D1B2A;
        text-transform: uppercase;
        letter-spacing: .035em;
        line-height: 1.08;
    }

    .cv-pro-role {
        margin-top: 6px;
        margin-bottom: 12px;
        color: #385506;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .cv-pro-meta {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #d8dee4;
    }

    .cv-pro-meta-pill {
        display: inline-block;
        padding: 3px 8px;
        margin: 0 5px 5px 0;
        background: #eef5d7;
        border: 1px solid #d9e7a8;
        border-radius: 12px;
        color: #385506;
        font-size: 9px;
        font-weight: bold;
    }

    .cv-pro-summary-section {
        margin-bottom: 8px;
        padding: 9px 10px;
        border: 1px solid #e1e7ed;
        border-radius: 8px;
        background: #fbfcfd;
    }

    .cv-pro-summary-title {
        font-size: 9.5px;
        font-weight: bold;
        text-transform: uppercase;
        color: #0D1B2A;
        margin-bottom: 6px;
        letter-spacing: .06em;
    }

    .cv-pro-summary-pill {
        display: inline-block;
        background: #ffffff;
        border: 1px solid #d7dde2;
        border-radius: 12px;
        padding: 3px 8px;
        margin: 0 4px 4px 0;
        color: #374151;
        font-size: 9px;
    }

    .cv-pro-section {
        margin-top: 16px;
        margin-bottom: 16px;
        page-break-inside: auto;
    }

    .cv-pro-section-title {
        margin: 0 0 10px;
        padding: 7px 10px;
        background: #0D1B2A;
        color: #ffffff;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .055em;
        border-radius: 6px;
    }

    .cv-pro-item {
        margin-bottom: 10px;
        padding: 10px 10px 8px;
        border: 1px solid #e5eaf0;
        border-radius: 8px;
        background: #ffffff;
        page-break-inside: avoid;
    }

    .cv-pro-item-title {
        font-size: 11.5px;
        font-weight: bold;
        color: #0D1B2A;
        margin-right: 115px;
    }

    .cv-pro-place {
        color: #385506;
        font-weight: bold;
        margin: 3px 0;
    }

    .cv-pro-date {
        float: right;
        max-width: 120px;
        text-align: right;
        font-size: 9.2px;
        color: #64748b;
        background: #f3f6f8;
        border-radius: 8px;
        padding: 2px 6px;
    }

    .cv-pro-small {
        margin-top: 4px;
        color: #64748b;
        font-size: 9px;
    }

    .cv-pro-tags {
        margin-top: 5px;
        margin-bottom: 3px;
    }

    .cv-pro-tag {
        display: inline-block;
        margin: 2px 3px 2px 0;
        padding: 2px 7px;
        border-radius: 10px;
        background: #eef5d7;
        border: 1px solid #d9e7a8;
        color: #385506;
        font-size: 8.7px;
        font-weight: bold;
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
        page-break-inside: avoid;
    }

    .cv-pro-ref {
        border: 1px solid #dce2e8;
        border-radius: 8px;
        background: #fbfcfd;
        padding: 8px;
    }

    .cv-pro-ref strong {
        display: block;
        color: #0D1B2A;
    }

    .cv-pro-ref span {
        display: block;
        color: #4b5563;
    }

    .cv-pro-ref-type {
        margin: 8px 0 5px;
        color: #385506;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .cv-pro-empty {
        color: #64748b;
        font-style: italic;
    }

    .clear {
        clear: both;
    }
</style>

<div class="cv-pro-document">

    <table class="cv-pro-header-table">
        <tr>
            <td class="cv-pro-header-photo-cell">
                @if($fotoPdf && file_exists($fotoPdf))
                    <div class="cv-pro-photo">
                        <img src="{{ $fotoPdf }}" alt="Foto">
                    </div>
                @else
                    <div class="cv-pro-photo">
                        <div class="cv-pro-photo-empty">
                            {{ $iniciales ?: 'CV' }}
                        </div>
                    </div>
                @endif
            </td>

            <td class="cv-pro-header-info-cell">
                <h1 class="cv-pro-name">{{ $personal('nombre') ?: 'Consultor FEPADE' }}</h1>
                <div class="cv-pro-role">Consultor/a profesional FEPADE</div>

                <div class="cv-pro-meta">
                    @if($personal('nacionalidad'))
                        <span class="cv-pro-meta-pill">Nacionalidad: {{ $personal('nacionalidad') }}</span>
                    @endif

                    @if($personal('residencia_completa') || $personal('residencia'))
                        <span class="cv-pro-meta-pill">
                            Residencia: {{ $personal('residencia_completa') ?: $personal('residencia') }}
                        </span>
                    @endif

                    @if($personal('fecha_nacimiento'))
                        <span class="cv-pro-meta-pill">Nacimiento: {{ $personal('fecha_nacimiento') }}</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    @if($hayContacto)
        <div class="cv-pro-summary-section">
            <div class="cv-pro-summary-title">Contacto</div>

            @foreach($emails as $item)
                @if($cell('emails', $item, 'email'))
                    <span class="cv-pro-summary-pill">{{ $cell('emails', $item, 'email') }}</span>
                @endif
            @endforeach

            @foreach($telefonos as $item)
                <span class="cv-pro-summary-pill">
                    @if($cell('telefonos', $item, 'tipo'))
                        {{ $cell('telefonos', $item, 'tipo') }}:
                    @endif

                    @if($cell('telefonos', $item, 'extension'))
                        ({{ $cell('telefonos', $item, 'extension') }})
                    @endif

                    {{ $cell('telefonos', $item, 'numero') }}
                </span>
            @endforeach

            @if($personal('direccion'))
                <span class="cv-pro-summary-pill">{{ $personal('direccion') }}</span>
            @endif
        </div>
    @endif

    @if($areas->count())
        <div class="cv-pro-summary-section">
            <div class="cv-pro-summary-title">Áreas de especialización</div>
            @foreach($areas as $area)
                <span class="cv-pro-summary-pill">{{ $area['nombre'] }}</span>
            @endforeach
        </div>
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
        <div class="cv-pro-summary-section">
            <div class="cv-pro-summary-title">Habilidades técnicas</div>
            @foreach($habilidadesSeleccionadas->unique() as $habilidad)
                <span class="cv-pro-summary-pill">{{ $habilidad }}</span>
            @endforeach
        </div>
    @endif

    @if($idiomas->count())
        <div class="cv-pro-summary-section">
            <div class="cv-pro-summary-title">Idiomas</div>
            @foreach($idiomas as $item)
                <span class="cv-pro-summary-pill">
                    {{ $cell('idiomas', $item, 'idioma') }}
                    @if($cell('idiomas', $item, 'nivel'))
                        — {{ $cell('idiomas', $item, 'nivel') }}
                    @endif
                </span>
            @endforeach
        </div>
    @endif

    @if($disponibilidades->count())
        <div class="cv-pro-summary-section">
            <div class="cv-pro-summary-title">Disponibilidad</div>
            @foreach($disponibilidades as $item)
                <span class="cv-pro-summary-pill">{{ $cell('disponibilidades', $item, 'nombre') }}</span>
            @endforeach
        </div>
    @endif

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
</div>