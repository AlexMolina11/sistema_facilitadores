@php
    use Illuminate\Support\Facades\Route;

    $usuario = auth()->user();

    $can = fn (string $permission): bool => (bool) $usuario?->tienePermiso($permission);
    $routeAvailable = fn (string $route): bool => Route::has($route);

    $dashboardVisible = $can('fac.dashboard.ver') && $routeAvailable('fac.dashboard');

    $consultorItems = collect([
        [
            'label' => 'Listado de consultores',
            'route' => 'fac.consultores.index',
            'permission' => 'fac.consultores.ver',
            'active' => 'fac.consultores.index',
        ],
        [
            'label' => 'Crear consultor',
            'route' => 'fac.consultores.create',
            'permission' => 'fac.consultores.gestionar',
            'active' => 'fac.consultores.create',
        ],
        [
            'label' => 'Búsqueda avanzada',
            'route' => 'fac.busqueda.index',
            'permission' => 'fac.consultores.ver',
            'active' => 'fac.busqueda.*',
        ],
        [
            'label' => 'Revisión de perfiles',
            'route' => 'fac.revision.index',
            'permission' => 'fac.consultores.ver',
            'active' => 'fac.revision.*',
        ],
        [
            'label' => 'Exportación CV',
            'route' => 'fac.cv.preview',
            'permission' => 'fac.consultores.ver',
            'active' => 'fac.cv.*',
        ],
    ])->filter(fn ($item) => $can($item['permission']) && $routeAvailable($item['route']))->values();

    $catalogoGroups = collect([
        [
            'title' => 'Perfil académico',
            'items' => [
                ['label' => 'Niveles académicos', 'route' => 'fac.catalogos.nivel-academico.index', 'active' => 'fac.catalogos.nivel-academico.*'],
                ['label' => 'Tipos de formación', 'route' => 'fac.catalogos.tipo-formacion.index', 'active' => 'fac.catalogos.tipo-formacion.*'],
                ['label' => 'Tipos de atestado', 'route' => 'fac.catalogos.tipo-atestado.index', 'active' => 'fac.catalogos.tipo-atestado.*'],
            ],
        ],
        [
            'title' => 'Idiomas',
            'items' => [
                ['label' => 'Idiomas', 'route' => 'fac.catalogos.idiomas.index', 'active' => 'fac.catalogos.idiomas.*'],
                ['label' => 'Niveles de idioma', 'route' => 'fac.catalogos.idioma-nivel.index', 'active' => 'fac.catalogos.idioma-nivel.*'],
            ],
        ],
        [
            'title' => 'Ubicación',
            'items' => [
                ['label' => 'Países', 'route' => 'fac.catalogos.paises.index', 'active' => 'fac.catalogos.paises.*'],
                ['label' => 'Departamentos', 'route' => 'fac.catalogos.departamentos.index', 'active' => 'fac.catalogos.departamentos.*'],
                ['label' => 'Municipios', 'route' => 'fac.catalogos.municipios_mh.index', 'active' => 'fac.catalogos.municipios_mh.*'],
                ['label' => 'Distritos', 'route' => 'fac.catalogos.municipios.index', 'active' => 'fac.catalogos.municipios.*'],
            ],
        ],
        [
            'title' => 'Datos del consultor',
            'items' => [
                ['label' => 'Sexo', 'route' => 'fac.catalogos.sexo.index', 'active' => 'fac.catalogos.sexo.*'],
                ['label' => 'Tipos de teléfono', 'route' => 'fac.catalogos.tipo_telefono.index', 'active' => 'fac.catalogos.tipo_telefono.*'],
                ['label' => 'Tipos de referencia', 'route' => 'fac.catalogos.tipo-referencia.index', 'active' => 'fac.catalogos.tipo-referencia.*'],
                ['label' => 'Tipos de red social', 'route' => 'fac.catalogos.tipo-red-social.index', 'active' => 'fac.catalogos.tipo-red-social.*'],
                ['label' => 'Tipos de documento', 'route' => 'fac.catalogos.tipo-documento.index', 'active' => 'fac.catalogos.tipo-documento.*'],
            ],
        ],
        [
            'title' => 'Consultoría',
            'items' => [
                ['label' => 'Tipos de consultoría', 'route' => 'fac.catalogos.tipo-consultoria.index', 'active' => 'fac.catalogos.tipo-consultoria.*'],
                ['label' => 'Tipos de habilidad', 'route' => 'fac.catalogos.tipo-habilidad.index', 'active' => 'fac.catalogos.tipo-habilidad.*'],
                ['label' => 'Habilidades', 'route' => 'fac.catalogos.habilidad.index', 'active' => 'fac.catalogos.habilidad.*'],
                ['label' => 'Tipos de disponibilidad', 'route' => 'fac.catalogos.tipo-disponibilidad.index', 'active' => 'fac.catalogos.tipo-disponibilidad.*'],
            ],
        ],
    ])->map(function ($group) use ($routeAvailable) {
        $group['items'] = collect($group['items'])
            ->filter(fn ($item) => $routeAvailable($item['route']))
            ->values();

        return $group;
    })->filter(fn ($group) => $group['items']->isNotEmpty())->values();

    $catalogosVisible = $can('fac.catalogos.gestionar') && $catalogoGroups->isNotEmpty();
    $consultoresVisible = $consultorItems->isNotEmpty();
@endphp

<aside class="app-sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('img/isotipo-fepade-rojo.png') }}" alt="FEPADE">

        <div class="sidebar-brand-text">
            <div class="sidebar-brand-title">CONSULTORES</div>
            <div class="sidebar-brand-subtitle">FEPADE 2026</div>
        </div>
    </div>

    @if($dashboardVisible)
        <div class="sidebar-section">Principal</div>

        <a href="{{ route('fac.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('fac.dashboard') ? 'active' : '' }}">
            <span class="sidebar-icon">⌂</span>
            <span class="sidebar-label">Dashboard</span>
        </a>
    @endif

    @if($consultoresVisible)
        <div class="sidebar-section">Módulos</div>

        <div class="sidebar-dropdown {{ request()->routeIs('fac.consultores.*', 'fac.busqueda.*', 'fac.revision.*', 'fac.cv.*') ? 'open' : '' }}">
            <button class="sidebar-dropdown-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#menuConsultores"
                    aria-expanded="{{ request()->routeIs('fac.consultores.*', 'fac.busqueda.*', 'fac.revision.*', 'fac.cv.*') ? 'true' : 'false' }}">
                <span class="sidebar-icon">◉</span>
                <span class="sidebar-label">Consultores</span>
                <span class="sidebar-caret">▾</span>
            </button>

            <div id="menuConsultores"
                 class="collapse {{ request()->routeIs('fac.consultores.*', 'fac.busqueda.*', 'fac.revision.*', 'fac.cv.*') ? 'show' : '' }}">
                <div class="sidebar-submenu">
                    @foreach($consultorItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="sidebar-sublink {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('fac.consultores.busqueda-avanzada') }}"
                class="sidebar-link {{ request()->routeIs('fac.consultores.busqueda-avanzada') ? 'active' : '' }}">
                Búsqueda Avanzada
            </a>
        </div>
    @endif

    @if($catalogosVisible)
        <div class="sidebar-section">Catálogos</div>

        <div class="sidebar-dropdown {{ request()->routeIs('fac.catalogos.*') ? 'open' : '' }}">
            <button class="sidebar-dropdown-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#menuCatalogos"
                    aria-expanded="{{ request()->routeIs('fac.catalogos.*') ? 'true' : 'false' }}">
                <span class="sidebar-icon">▦</span>
                <span class="sidebar-label">Catálogos</span>
                <span class="sidebar-caret">▾</span>
            </button>

            <div id="menuCatalogos"
                 class="collapse {{ request()->routeIs('fac.catalogos.*') ? 'show' : '' }}">
                <div class="sidebar-submenu">
                    @foreach($catalogoGroups as $group)
                        <div class="sidebar-subtitle">{{ $group['title'] }}</div>

                        @foreach($group['items'] as $item)
                            <a href="{{ route($item['route']) }}"
                               class="sidebar-sublink {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</aside>
