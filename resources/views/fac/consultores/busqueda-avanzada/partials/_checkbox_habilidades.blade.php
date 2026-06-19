@php
    $items = $items ?? collect();
    $seleccionados = collect((array) request()->input($nombre, $filtros[$nombre] ?? []))->map(fn ($id) => (string) $id)->all();
@endphp

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label fw-bold mb-0">{{ $titulo }}</label>
        <span class="badge bg-light text-dark border">{{ $items->count() }}</span>
    </div>

    <div class="border rounded p-2 bg-white" style="max-height: 230px; overflow-y: auto;">
        @forelse($items as $item)
            @php
                $id = $item->id_area_especializacion ?? $item->id_habilidad_tecnica ?? $item->id ?? null;
                $nombreItem = $item->nombre ?? 'Sin nombre';
                $areaNombre = $item->areaEspecializacion?->nombre ?? null;
            @endphp

            <div class="form-check mb-2">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="{{ $nombre }}[]"
                    id="{{ $nombre }}_{{ $id }}"
                    value="{{ $id }}"
                    @checked(in_array((string) $id, $seleccionados, true))
                >
                <label class="form-check-label small" for="{{ $nombre }}_{{ $id }}">
                    {{ $nombreItem }}
                    @if($areaNombre)
                        <span class="d-block text-muted">{{ $areaNombre }}</span>
                    @endif
                </label>
            </div>
        @empty
            <p class="text-muted small mb-0">No hay registros disponibles.</p>
        @endforelse
    </div>
</div>
