@props([
    'title',
    'subtitle' => null,
    'height' => '320px',
])

<x-ui.page-card {{ $attributes }}>
    <div class="dashboard-card-header">
        <div>
            <h5>{{ $title }}</h5>

            @if($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div>
                {{ $actions }}
            </div>
        @endisset
    </div>

    <div class="dashboard-chart-wrap" style="height: {{ $height }};">
        {{ $slot }}
    </div>
</x-ui.page-card>