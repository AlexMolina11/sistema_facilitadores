@props([
    'title' => null,
    'subtitle' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'fepade-card ' . $class]) }}>
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

    {{ $slot }}
</div>