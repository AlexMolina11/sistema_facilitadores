@php
    $steps = [
        1 => [
            'label' => 'Datos personales',
            'route' => isset($consultor) ? route('fac.consultores.edit', $consultor) : null,
        ],
        2 => [
            'label' => 'Contacto',
            'route' => isset($consultor) ? route('fac.consultores.contacto.edit', $consultor) : null,
        ],
        3 => [
            'label' => 'Formación',
            'route' => isset($consultor) ? route('fac.consultores.formacion.edit', $consultor) : null,
        ],
        4 => [
            'label' => 'Experiencia',
            'route' => isset($consultor) ? route('fac.consultores.experiencia.edit', $consultor) : null,
        ],
        5 => [
            'label' => 'Documentos',
            'route' => isset($consultor) ? route('fac.consultores.documentos.edit', $consultor) : null,
        ],
    ];
@endphp

<div class="fepade-card mb-4">
    <div class="row g-3">
        @foreach($steps as $number => $item)
            <div class="col">
                @if($item['route'])
                    <a href="{{ $item['route'] }}" class="text-decoration-none">
                @endif

                <div class="d-flex align-items-center gap-2">
                    <div 
                        class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                        style="width: 34px; height: 34px; {{ $step === $number ? 'background:#00C896;color:white;' : 'background:#EEF2F8;color:#6B7A90;' }}"
                    >
                        {{ $number }}
                    </div>

                    <div class="{{ $step === $number ? 'fw-semibold text-dark' : 'text-muted' }}">
                        {{ $item['label'] }}
                    </div>
                </div>

                @if($item['route'])
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</div>