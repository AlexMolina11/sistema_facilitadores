@props([
    'showUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteMessage' => '¿Seguro que deseas eliminar este registro?',
])

<div {{ $attributes->merge(['class' => 'd-flex justify-content-end gap-1']) }}>
    @if($showUrl)
        <a href="{{ $showUrl }}" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-eye"></i>
        </a>
    @endif

    @if($editUrl)
        <a href="{{ $editUrl }}" class="btn btn-sm btn-outline-warning">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
    @endif

    @if($deleteUrl)
        <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm('{{ $deleteMessage }}');">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    @endif
</div>