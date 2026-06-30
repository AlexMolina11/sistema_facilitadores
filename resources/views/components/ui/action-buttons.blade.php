@props([
    'showUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteMessage' => '¿Seguro que deseas eliminar este registro?',
])

<div {{ $attributes->merge(['class' => 'fepade-actions']) }}>
    @if($showUrl)
        <a href="{{ $showUrl }}" class="btn btn-sm btn-outline-primary" title="Ver">
            <i class="fa-solid fa-eye"></i>
        </a>
    @endif

    @if($editUrl)
        <a href="{{ $editUrl }}" class="btn btn-sm btn-outline-warning" title="Editar">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
    @endif

    @if($deleteUrl)
        <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm('{{ $deleteMessage }}');">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    @endif
</div>