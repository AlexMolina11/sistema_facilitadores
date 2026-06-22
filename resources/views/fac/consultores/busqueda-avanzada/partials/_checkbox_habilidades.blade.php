@php
    $seleccionados = collect($filtros[$nombre] ?? [])->map(fn ($id) => (int) $id)->toArray();

    $idCampo = match ($nombre) {
        'area_especializacion' => 'id_area_especializacion',
        'areas_especializacion' => 'id_area_especializacion',
        'habilidades_tecnicas' => 'id_habilidad_tecnica',
        default => 'id_habilidad',
    };
@endphp

<div class="busqueda-checkbox-group mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label mb-0">{{ $titulo }}</label>
        <span class="badge badge-primary-soft">{{ $items->count() }}</span>
    </div>

    <div class="busqueda-checkbox-scroll">
        @forelse($items as $item)
            @php
                $valor = (int) $item->{$idCampo};
            @endphp

            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="{{ $nombre }}[]"
                    value="{{ $valor }}"
                    id="{{ $nombre }}_{{ $valor }}"
                    @checked(in_array($valor, $seleccionados, true))
                >
                <label class="form-check-label" for="{{ $nombre }}_{{ $valor }}">
                    {{ $item->nombre }}
                </label>
            </div>
        @empty
            <div class="text-muted small">No hay opciones activas.</div>
        @endforelse
    </div>
</div>