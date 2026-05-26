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

<style>
    .perfil-tabs{display:flex;flex-wrap:wrap;gap:8px;border-bottom:1px solid #dee2e6;margin:20px 0 24px 0}.perfil-tab{display:inline-flex;align-items:center;padding:12px 18px;background:#f5f6f8;border:1px solid transparent;border-radius:8px 8px 0 0;color:#1f2d3d;text-decoration:none;font-weight:700;font-size:14px}.perfil-tab:hover{background:#eef0f3;color:#a6192e;text-decoration:none}.perfil-tab.active{background:#fff;color:#a6192e;border-color:#a6192e #a6192e #fff #a6192e}
</style>

<div class="perfil-tabs">
    @foreach($steps as $number => $item)
        @if($item['route'])
            <a href="{{ $item['route'] }}" class="perfil-tab {{ (int) $step === (int) $number ? 'active' : '' }}">{{ $item['label'] }}</a>
        @else
            <span class="perfil-tab {{ (int) $step === (int) $number ? 'active' : '' }}">{{ $item['label'] }}</span>
        @endif
    @endforeach
</div>
