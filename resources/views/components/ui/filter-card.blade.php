@props([
    'title' => null,
    'subtitle' => null,
])

<x-ui.page-card {{ $attributes }}>
    @if($title || $subtitle)
        <div class="fepade-card-header">
            <div>
                @if($title)
                    <h5 class="mb-1">{{ $title }}</h5>
                @endif

                @if($subtitle)
                    <div class="text-muted small">{{ $subtitle }}</div>
                @endif
            </div>
        </div>
    @endif

    {{ $slot }}
</x-ui.page-card>