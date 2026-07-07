<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
            {{ $titulo }}
        </button>
    </h2>

    <div id="{{ $collapseId }}" class="accordion-collapse collapse" data-bs-parent="#cvAccordion">
        <div class="accordion-body">
            @forelse($items as $item)
                <div class="cv-config-item">
                    <strong class="d-block mb-2">
                        {{ $item[$mainField] ?? 'Registro sin nombre' }}
                    </strong>

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

                            <label 
                                class="form-check-label" 
                                for="{{ $section }}_{{ $item['id'] }}_{{ $field }}"
                            >
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @empty
                <p class="text-muted mb-0">No hay registros disponibles.</p>
            @endforelse
        </div>
    </div>
</div>