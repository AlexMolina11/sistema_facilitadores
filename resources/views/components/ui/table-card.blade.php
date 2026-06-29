@props([
    'title' => null,
    'subtitle' => null,
    'items' => null,
    'emptyTitle' => 'No hay registros.',
    'emptyMessage' => 'No se encontró información para mostrar.',
])

<div {{ $attributes->merge(['class' => 'fepade-card']) }}>
    @if($title || $subtitle || isset($actions))
        <div class="fepade-card-header">
            <div>
                @if($title)
                    <h5 class="mb-1">{{ $title }}</h5>
                @endif

                @if($subtitle)
                    <p class="text-muted small mb-0">{{ $subtitle }}</p>
                @endif
            </div>

            @isset($actions)
                <div>
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    @isset($filters)
        <div class="mb-4">
            {{ $filters }}
        </div>
    @endisset

    @if($items && method_exists($items, 'count') && $items->count() === 0)
        <x-ui.empty-state
            :title="$emptyTitle"
            :message="$emptyMessage"
        />
    @else
        <div class="table-responsive fepade-table-wrapper">
            {{ $slot }}
        </div>

        @if($items && method_exists($items, 'links') && $items->hasPages())
            <div class="pagination-wrapper">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>