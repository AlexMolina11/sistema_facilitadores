@php
    $seleccionados = collect($filtros[$nombre] ?? [])->map(fn ($id) => (int) $id)->toArray();
@endphp

<div class="busqueda-checkbox-group mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label mb-0">{{ $titulo }}</label>
        <span class="badge badge-primary-soft">{{ $items->count() }}</span>
    </div>

    <div class="busqueda-checkbox-scroll">
        @forelse($items as $item)
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="{{ $nombre }}[]"
                    value="{{ $item->id_habilidad }}"
                    id="{{ $nombre }}_{{ $item->id_habilidad }}"
                    @checked(in_array((int) $item->id_habilidad, $seleccionados, true))
                >
                <label class="form-check-label" for="{{ $nombre }}_{{ $item->id_habilidad }}">
                    {{ $item->nombre }}
                </label>
            </div>
        @empty
            <div class="text-muted small">No hay opciones activas.</div>
        @endforelse
    </div>
</div>