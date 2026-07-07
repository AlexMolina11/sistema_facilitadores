<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
            {{ $titulo }}
        </button>
    </h2>

    <div id="{{ $collapseId }}" class="accordion-collapse collapse">
        <div class="accordion-body">

            <div class="cv-section-actions">
                <button type="button" class="btn btn-sm btn-outline-primary cv-section-select" data-section="{{ $section }}">
                    Seleccionar sección
                </button>

                <button type="button" class="btn btn-sm btn-outline-secondary cv-section-clear" data-section="{{ $section }}">
                    Quitar sección
                </button>
            </div>

            @forelse($items as $item)
                <div class="cv-config-item" data-config-section="{{ $section }}" data-config-item="{{ $item['id'] }}">
                    <div class="cv-config-item-header">
                        <strong>{{ $item[$mainField] ?? 'Registro sin nombre' }}</strong>

                        @if($section !== 'emails')
                            <div class="cv-config-item-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary cv-item-select" data-section="{{ $section }}" data-item="{{ $item['id'] }}">
                                    Todo
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-secondary cv-item-clear" data-section="{{ $section }}" data-item="{{ $item['id'] }}">
                                    Nada
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="cv-config-fields">
                        @foreach($fields as $field => $label)
                            <div class="form-check cv-check">
                                <input
                                    class="form-check-input cv-toggle"
                                    type="checkbox"
                                    checked
                                    data-section="{{ $section }}"
                                    data-item="{{ $item['id'] }}"
                                    data-field="{{ $field }}"
                                    id="{{ $section }}_{{ $item['id'] }}_{{ $field }}"
                                >

                                <label class="form-check-label" for="{{ $section }}_{{ $item['id'] }}_{{ $field }}">
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">No hay registros disponibles.</p>
            @endforelse
        </div>
    </div>
</div>