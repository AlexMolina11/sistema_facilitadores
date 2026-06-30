@props([
    'backUrl' => null,
    'submitText' => 'Guardar cambios',
    'backText' => 'Volver',
])

<div {{ $attributes->merge(['class' => 'fepade-form-actions']) }}>
    @if($backUrl)
        <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
            {{ $backText }}
        </a>
    @endif

    <button type="submit" class="btn btn-fepade">
        {{ $submitText }}
    </button>
</div>