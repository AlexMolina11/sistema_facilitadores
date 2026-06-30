@props([
    'title' => 'Proceso',
    'description' => null,
    'steps' => [],
    'current' => null,
    'completion' => null,
    'missing' => [],
    'criteriaFixed' => [],
    'criteriaDynamic' => [],
    'presentedCriteria' => [],
    'pendingMessages' => [],
    'obtained' => null,
    'total' => null,
])

@php
    $completionValue = is_numeric($completion) ? max(0, min(100, (int) $completion)) : null;

    $criteriaFixed = collect($criteriaFixed);
    $criteriaDynamic = collect($criteriaDynamic);

    $pendingFixed = $criteriaFixed->filter(fn ($completed) => !$completed)->keys();
    $pendingDynamic = $criteriaDynamic->filter(fn ($completed) => !$completed)->keys();

    $presentedCriteria = collect($presentedCriteria);
    $pendingMessages = collect($pendingMessages);

    $pendingAll = $pendingMessages->isNotEmpty()
        ? $pendingMessages
        : $pendingFixed->merge($pendingDynamic)->values();

@endphp



<div {{ $attributes->merge(['class' => 'fepade-wizard']) }}>
    <div class="fepade-wizard-hero">
        <div>
            <h4 class="fepade-wizard-title">{{ $title }}</h4>

            @if($description)
                <p class="fepade-wizard-description">{{ $description }}</p>
            @endif

            @if(!is_null($obtained) && !is_null($total))
                <p class="text-muted small mb-0 mt-2">
                    {{ $obtained }} de {{ $total }} criterios completados.
                </p>
            @endif
        </div>

        @if(!is_null($completionValue))
            <div class="fepade-wizard-completion">
                <strong>{{ $completionValue }}%</strong>
                <span>completado</span>
            </div>
        @endif
    </div>

    @if(!is_null($completionValue))
        <div class="fepade-wizard-progress-wrap">
            <div class="fepade-wizard-progress-bar" style="width: {{ $completionValue }}%;"></div>
        </div>
    @endif

    @if($pendingAll->isNotEmpty())
        <div class="fepade-wizard-missing">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div>
                    <strong>Para completar el perfil falta:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach($pendingAll->take(5) as $criterio)
                            <li>{{ $criterio }}</li>
                        @endforeach
                    </ul>

                    @if($pendingAll->count() > 5)
                        <div class="small mt-2">
                            Y {{ $pendingAll->count() - 5 }} criterio(s) adicional(es).
                        </div>
                    @endif
                </div>

                <button
                    class="btn btn-sm btn-outline-secondary"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#wizardCriteriaDetail"
                >
                    Ver detalle
                </button>
            </div>
        </div>

        @if($presentedCriteria->isNotEmpty())
            <div class="collapse mb-3" id="wizardCriteriaDetail">
                <div class="fepade-wizard-criteria">
                    <div class="row g-2">
                        @foreach($presentedCriteria->flatten(1) as $item)
                            <div class="col-md-6 col-lg-4">
                                <div class="fepade-wizard-criteria-item {{ $item['completed'] ? 'completed' : 'pending' }}">
                                    @if($item['completed'])
                                        <i class="fa-solid fa-circle-check"></i>
                                    @else
                                        <i class="fa-regular fa-circle"></i>
                                    @endif

                                    <span>{{ $item['message'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="fepade-wizard-complete-message">
            <i class="fa-solid fa-circle-check me-1"></i>
            Perfil completo. Todos los criterios requeridos han sido registrados.
        </div>
    @endif

    <div class="fepade-wizard-steps">
        @foreach($steps as $key => $step)
            @php
                $isActive = (string) $current === (string) $key;
                $isCompleted = $step['completed'] ?? false;
                $url = $step['url'] ?? '#';
                $icon = $step['icon'] ?? 'fa-circle';
                $label = $step['label'] ?? $key;
            @endphp

            <a href="{{ $url }}"
               class="fepade-wizard-step {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                <span class="fepade-wizard-step-icon">
                    @if($isCompleted)
                        <i class="fa-solid fa-check"></i>
                    @else
                        <i class="fa-solid {{ $icon }}"></i>
                    @endif
                </span>

                <span class="fepade-wizard-step-text">{{ $label }}</span>
            </a>
        @endforeach
    </div>
</div>