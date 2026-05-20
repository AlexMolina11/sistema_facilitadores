@php
    $steps = [
        1 => 'Datos personales',
        2 => 'Contacto',
        3 => 'Formación',
        4 => 'Experiencia',
        5 => 'Documentos',
    ];
@endphp

<div class="fepade-card mb-4">
    <div class="row g-3">
        @foreach($steps as $number => $label)
            <div class="col">
                <div class="d-flex align-items-center gap-2">
                    <div 
                        class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                        style="width: 34px; height: 34px; {{ $step === $number ? 'background:#00C896;color:white;' : 'background:#EEF2F8;color:#6B7A90;' }}"
                    >
                        {{ $number }}
                    </div>

                    <div class="{{ $step === $number ? 'fw-semibold' : 'text-muted' }}">
                        {{ $label }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>