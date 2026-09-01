@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $usuario = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Perfil personal
    |--------------------------------------------------------------------------
    */

    $tienePerfilConsultor =
        filled($usuario?->id_consultor)
        && Route::has('fac.mi-perfil')
        && Route::has('fac.mis-capacitaciones');

    /*
    |--------------------------------------------------------------------------
    | Roles visibles
    |--------------------------------------------------------------------------
    |
    | No mostramos solamente el primer rol porque un usuario puede
    | poseer más de uno.
    |
    */

    $rolesUsuario = $usuario
        ?->roles
        ?->pluck('nombre')
        ?->filter()
        ?->unique()
        ?->values()
        ?? collect();

    $textoRoles = $rolesUsuario->isNotEmpty()
        ? $rolesUsuario->implode(' · ')
        : 'Usuario';

    /*
    |--------------------------------------------------------------------------
    | Seguridad
    |--------------------------------------------------------------------------
    */

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
            'label' => 'Bitácora de acceso',
            'route' => 'seg.bitacora.index',
            'permission' => 'seg.bitacora.ver',
        ],
        [
            'label' => 'Bitácora SAF',
            'route' => 'seg.bitacora-saf.index',
            'permission' => 'seg.bitacora-saf.ver',
        ],
        [
            'label' => 'Aceptación de términos',
            'route' => 'seg.bitacora-terminos.index',
            'permission' => 'seg.bitacora_terminos.ver',
        ],
    ])
        ->filter(
            fn ($item) =>
                $usuario?->tienePermiso(
                    $item['permission']
                )
                && Route::has($item['route'])
        )
        ->values();
@endphp


<header class="app-topbar">

    <div class="topbar-left">

        <button
            type="button"
            class="topbar-sidebar-btn d-none d-lg-inline-flex"
            id="sidebarToggle"
        >
            ☰
        </button>

        <button
            type="button"
            class="topbar-sidebar-btn d-inline-flex d-lg-none"
            id="sidebarMobileToggle"
        >
            ☰
        </button>


        <div class="topbar-page-info d-none d-lg-block">

            <strong>
                @yield(
                    'page-title',
                    'Panel principal'
                )
            </strong>

            <div class="small">
                @yield(
                    'page-subtitle',
                    'Sistema de gestión de facilitadores'
                )
            </div>

        </div>


        <div class="topbar-mobile-logo d-lg-none">

            <img
                src="{{ asset('img/isotipo-fepade-rojo.png') }}"
                alt="FEPADE"
            >

        </div>

    </div>


    <div class="d-flex align-items-center gap-3">

        @auth

            {{-- SEGURIDAD --}}
            @if($seguridadItems->isNotEmpty())

                <div class="dropdown">

                    <button
                        class="topbar-menu-btn dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                    >
                        Seguridad
                    </button>


                    <ul class="dropdown-menu dropdown-menu-end">

                        @foreach($seguridadItems as $item)

                            <li>

                                <a
                                    class="dropdown-item {{
                                        request()->routeIs(
                                            Str::beforeLast(
                                                $item['route'],
                                                '.index'
                                            ) . '.*'
                                        )
                                            ? 'active'
                                            : ''
                                    }}"
                                    href="{{ route($item['route']) }}"
                                >
                                    {{ $item['label'] }}
                                </a>

                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- USUARIO --}}
            <div class="dropdown">

                <button
                    class="border-0 bg-transparent d-flex align-items-center gap-2"
                    type="button"
                    data-bs-toggle="dropdown"
                >

                    <div class="topbar-user-info d-none d-sm-block">

                        <div class="topbar-user-name">
                            {{
                                trim(
                                    ($usuario->nombres ?? '')
                                    . ' '
                                    . ($usuario->apellidos ?? '')
                                )
                                    ?: $usuario->email
                            }}
                        </div>

                        <div class="topbar-user-role">
                            {{ $textoRoles }}
                        </div>

                    </div>


                    <div
                        class="rounded-circle d-flex align-items-center justify-content-center"
                        style="
                            width:42px;
                            height:42px;
                            background:#00C896;
                            color:white;
                            font-weight:bold;
                        "
                    >
                        {{
                            strtoupper(
                                substr(
                                    $usuario->nombres ?? 'U',
                                    0,
                                    1
                                )
                            )
                        }}
                    </div>

                </button>


                <ul class="dropdown-menu dropdown-menu-end">

                    {{-- ÁREA PERSONAL --}}
                    @if($tienePerfilConsultor)

                        <li>

                            <a
                                class="dropdown-item"
                                href="{{ route('fac.mi-perfil') }}"
                            >
                                <i class="fa-solid fa-user me-2"></i>
                                Mi perfil
                            </a>

                        </li>


                        <li>

                            <a
                                class="dropdown-item"
                                href="{{ route('fac.mis-capacitaciones') }}"
                            >
                                <i class="fa-solid fa-chalkboard-user me-2"></i>
                                Mis capacitaciones
                            </a>

                        </li>


                        <li>
                            <hr class="dropdown-divider">
                        </li>

                    @endif


                    {{-- CUENTA --}}
                    <li>

                        <span class="dropdown-item-text">

                            <small class="text-muted d-block">
                                Cuenta
                            </small>

                            {{ $usuario->email }}

                        </span>

                    </li>


                    <li>

                        <span class="dropdown-item-text">

                            <small class="text-muted d-block">
                                Rol{{ $rolesUsuario->count() === 1 ? '' : 'es' }}
                            </small>

                            {{ $textoRoles }}

                        </span>

                    </li>


                    <li>
                        <hr class="dropdown-divider">
                    </li>


                    <li>

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="dropdown-item text-danger"
                            >
                                <i class="fa-solid fa-right-from-bracket me-2"></i>
                                Cerrar sesión
                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        @endauth

    </div>

</header>