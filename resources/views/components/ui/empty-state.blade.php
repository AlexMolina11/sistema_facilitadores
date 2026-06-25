@props([
    'icon' => 'fa-regular fa-folder-open',
    'title' => 'No hay registros.',
    'message' => 'No se encontró información para mostrar.',
])

<div {{ $attributes->merge(['class' => 'text-center py-5']) }}>
    <div class="mb-3 text-muted">
        <i class="{{ $icon }} fa-3x"></i>
    </div>

    <h5 class="mb-2">{{ $title }}</h5>

    <p class="text-muted mb-3">{{ $message }}</p>

    @if(trim($slot) !== '')
        <div>
            {{ $slot }}
        </div>
    @endif
</div>