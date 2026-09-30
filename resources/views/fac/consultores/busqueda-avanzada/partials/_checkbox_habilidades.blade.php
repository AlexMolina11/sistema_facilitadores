@php
    $seleccionados = collect($filtros[$nombre] ?? [])
        ->map(fn ($id) => (int) $id)
        ->toArray();

    $idCampo = match ($nombre) {
        'area_especializacion' => 'id_area_especializacion',
        'areas_especializacion' => 'id_area_especializacion',
        'habilidades_tecnicas' => 'id_habilidad_tecnica',
        default => 'id_habilidad',
    };

    $idBuscador = 'buscador_' . $nombre;
    $idContenedor = 'contenedor_' . $nombre;
    $idContador = 'contador_' . $nombre;
    $idSinResultados = 'sin_resultados_' . $nombre;

    $placeholder = $nombre === 'habilidades_tecnicas'
        ? 'Buscar habilidad técnica...'
        : 'Buscar área de especialización...';
@endphp

<div
    class="busqueda-checkbox-group mb-3 js-filtro-buscable"
    data-filter-name="{{ $nombre }}"
>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label mb-0">{{ $titulo }}</label>

        <span
            class="badge badge-primary-soft"
            id="{{ $idContador }}"
            data-total="{{ $items->count() }}"
        >
            {{ $items->count() }}
        </span>
    </div>

    <div class="busqueda-filter-search mb-2">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white">
                <i class="fas fa-search text-muted"></i>
            </span>

            <input
                type="search"
                class="form-control js-buscador-checkbox"
                id="{{ $idBuscador }}"
                placeholder="{{ $placeholder }}"
                autocomplete="off"
                aria-label="Buscar en {{ strtolower($titulo) }}"
            >

            <button
                type="button"
                class="btn btn-outline-secondary js-limpiar-buscador d-none"
                title="Limpiar búsqueda"
                aria-label="Limpiar búsqueda"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div
        class="busqueda-checkbox-scroll js-contenedor-checkbox"
        id="{{ $idContenedor }}"
    >
        @forelse($items as $item)
            @php
                $valor = (int) $item->{$idCampo};
            @endphp

            <div
                class="form-check js-opcion-checkbox"
                data-search="{{ $item->nombre }}"
            >
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="{{ $nombre }}[]"
                    value="{{ $valor }}"
                    id="{{ $nombre }}_{{ $valor }}"
                    @checked(in_array($valor, $seleccionados, true))
                >

                <label
                    class="form-check-label"
                    for="{{ $nombre }}_{{ $valor }}"
                >
                    {{ $item->nombre }}
                </label>
            </div>
        @empty
            <div class="text-muted small">
                No hay opciones activas.
            </div>
        @endforelse

        <div
            class="text-muted small text-center py-3 d-none js-sin-resultados"
            id="{{ $idSinResultados }}"
        >
            <i class="fas fa-search me-1"></i>
            No se encontraron coincidencias.
        </div>
    </div>

    @if($items->isNotEmpty())
        <div class="small text-muted mt-2 js-resumen-opciones">
            <span class="js-cantidad-visible">{{ $items->count() }}</span>
            de {{ $items->count() }} opciones
        </div>
    @endif
</div>