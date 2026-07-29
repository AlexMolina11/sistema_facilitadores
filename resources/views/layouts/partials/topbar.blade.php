@php
    use Illuminate\Support\Facades\Route;

    $usuario = auth()->user();

    $seguridadItems = collect([
        [
            'label' => 'Usuarios',
            'route' => 'seg.usuarios.index',
            'permission' => 'seg.usuarios.gestionar',
        ],
        [
            'label' => 'Roles',
            'route' => 'seg.roles.index',
            'permission' => 'seg.roles.gestionar',
        ],
        [
            'label' => 'Permisos',
            'route' => 'seg.permisos.index',
            'permission' => 'seg.permisos.gestionar',
        ],
        [
            'label' => 'Invitaciones',
            'route' => 'seg.invitaciones.index',
            'permission' => 'seg.invitaciones.gestionar',
        ],
        [
            'label' => 'Bitácora de acceso',
            'route' => 'seg.bitacora.index',
            'permission' => 'seg.bitacora.ver',
        ],
        [
            'label' => 'Bitácora SAF',
            'route' => 'seg.bitacora-saf.index',
            'permission' => 'seg.bitacora-saf.ver',
        ],
    ])->filter(fn ($item) => $usuario?->tienePermiso($item['permission']) && Route::has($item['route']))->values();
@endphp

<header class="app-topbar">
    <div class="topbar-left">
        <button type="button" class="topbar-sidebar-btn d-none d-lg-inline-flex" id="sidebarToggle">
            ☰
        </button>

        <button type="button" class="topbar-sidebar-btn d-inline-flex d-lg-none" id="sidebarMobileToggle">
            ☰
        </button>

        <div class="topbar-page-info d-none d-lg-block">
            <strong>
                @yield('page-title', 'Panel principal')
            </strong>

            <div class="small">
                @yield('page-subtitle', 'Sistema de gestión de facilitadores')
            </div>
        </div>

        <div class="topbar-mobile-logo d-lg-none">
            <img src="{{ asset('img/isotipo-fepade-rojo.png') }}" alt="FEPADE">
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        @auth
            @if($seguridadItems->isNotEmpty())
                <div class="dropdown">
                    <button class="topbar-menu-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Seguridad
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach($seguridadItems as $item)
                            <li>
                                <a class="dropdown-item {{ request()->routeIs(Str::beforeLast($item['route'], '.index') . '.*') ? 'active' : '' }}"
                                   href="{{ route($item['route']) }}">
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="dropdown">
                <button class="border-0 bg-transparent d-flex align-items-center gap-2"
                        type="button"
                        data-bs-toggle="dropdown">

                    <div class="topbar-user-info d-none d-sm-block">
                        <div class="topbar-user-name">
                            {{ trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellidos ?? '')) ?: $usuario->email }}
                        </div>

                        <div class="topbar-user-role">
                            {{ $usuario->roles->pluck('nombre')->first() ?? 'Usuario' }}
                        </div>
                    </div>

                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:42px;height:42px;background:#00C896;color:white;font-weight:bold;">
                        {{ strtoupper(substr($usuario->nombres ?? 'U', 0, 1)) }}
                    </div>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <span class="dropdown-item-text">
                            {{ $usuario->email }}
                        </span>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="dropdown-item text-danger">
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</header>
