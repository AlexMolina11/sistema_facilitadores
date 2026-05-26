@php
    $steps = [
        1 => ['label' => 'Perfil Personal', 'route' => isset($consultor) ? route('fac.consultores.edit', $consultor) : null],
        2 => ['label' => 'Contacto', 'route' => isset($consultor) ? route('fac.consultores.contacto.edit', $consultor) : null],
        3 => ['label' => 'Experiencia', 'route' => isset($consultor) ? route('fac.consultores.experiencia.edit', $consultor) : null],
        4 => ['label' => 'Títulos Académicos', 'route' => isset($consultor) ? route('fac.consultores.formacion.edit', $consultor) : null],
        5 => ['label' => 'Habilidades', 'route' => isset($consultor) ? route('fac.consultores.habilidades.edit', $consultor) : null],
        6 => ['label' => 'Idiomas', 'route' => isset($consultor) ? route('fac.consultores.idiomas.edit', $consultor) : null],
        7 => ['label' => 'Referencias', 'route' => isset($consultor) ? route('fac.consultores.referencias.edit', $consultor) : null],
        8 => ['label' => 'Disponibilidad', 'route' => isset($consultor) ? route('fac.consultores.disponibilidad.edit', $consultor) : null],
    ];
@endphp

<div class="perfil-tabs">
    @foreach($steps as $number => $item)
        @if($item['route'])
            <a href="{{ $item['route'] }}" class="perfil-tab {{ (int) $step === (int) $number ? 'active' : '' }}">{{ $item['label'] }}</a>
        @else
            <span class="perfil-tab {{ (int) $step === (int) $number ? 'active' : '' }}">{{ $item['label'] }}</span>
        @endif
    @endforeach
</div>
