@props([
    'title' => 'Título',
    'subtitle' => ''
])

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="page-title">{{ $title }}</h1>

        @if($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if(trim($slot) !== '')
        <div>
            {{ $slot }}
        </div>
    @endif
</div>