@props([
    'backUrl' => null,
    'submitText' => 'Guardar cambios',
    'backText' => 'Volver',
])

<div {{ $attributes->merge(['class' => 'd-flex justify-content-end gap-2 mt-4']) }}>
    @if($backUrl)
        <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
            {{ $backText }}
        </a>
    @endif

    <button type="submit" class="btn btn-fepade">
        {{ $submitText }}
    </button>
</div>