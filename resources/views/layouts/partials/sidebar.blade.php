@php
    use App\Modules\Fac\Models\Consultor;
    use Illuminate\Support\Facades\Route;

    $usuario = auth()->user();

    $can = fn (string $permission): bool =>
        (bool) $usuario?->tienePermiso($permission);

    $routeAvailable = fn (string $route): bool =>
        Route::has($route);

    /*
    |--------------------------------------------------------------------------
    | Área personal del consultor
    |--------------------------------------------------------------------------
    |
    | Tener id_consultor agrega "Mi espacio".
    | NO elimina los módulos que correspondan por permisos.
    |
    */

    $tienePerfilConsultor =
        filled($usuario?->id_consultor)
        && $routeAvailable('fac.mi-perfil')
        && $routeAvailable('fac.mis-capacitaciones');

    /*
    |--------------------------------------------------------------------------
    | Detectar si estamos dentro del propio expediente
    |--------------------------------------------------------------------------
    */

    $consultorRuta = request()->route('consultor');

    $idConsultorRuta = match (true) {
        $consultorRuta instanceof Consultor =>
            (int) $consultorRuta->id_consultor,

        is_numeric($consultorRuta) =>
            (int) $consultorRuta,

        default =>
            null,
    };

    $rutaPropioExpediente =
        $idConsultorRuta !== null
        && (int) $usuario?->id_consultor === $idConsultorRuta;

    $miPerfilActivo =
        request()->routeIs(
            'fac.mi-perfil',
            'fac.mi-perfil.editar'
        )
        || (
            $rutaPropioExpediente
            && request()->routeIs(
                'fac.consultores.show',
                'fac.consultores.edit',
                'fac.consultores.contacto.*',
                'fac.consultores.formacion.*',
                'fac.consultores.experiencia.*',
                'fac.consultores.habilidades.*',
                'fac.consultores.idiomas.*',
                'fac.consultores.referencias.*',
                'fac.consultores.disponibilidad.*',
                'fac.consultores.documentos.*'
            )
        );

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    $dashboardVisible =
        $can('fac.dashboard.ver')
        && $routeAvailable('fac.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Consultores administrativos
    |--------------------------------------------------------------------------
    */

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
            'permission' => 'fac.consultores.crear',
            'active' => 'fac.consultores.create',
        ],
        [
            'label' => 'Búsqueda avanzada',
            'route' => 'fac.busqueda.index',
            'permission' => 'fac.busqueda.ver',
            'active' => 'fac.busqueda.*',
        ],
        [
            'label' => 'Invitaciones',
            'route' => 'seg.invitaciones.index',
            'permission' => 'seg.invitaciones.gestionar',
            'active' => 'seg.invitaciones.*',
        ],
        [
            'label' => 'Revisión de perfiles',
            'route' => 'fac.revision.index',
            'permission' => 'fac.consultores.ver',
            'active' => 'fac.revision.*',
        ],
    ])
        ->filter(
            fn ($item) =>
                $can($item['permission'])
                && $routeAvailable($item['route'])
        )
        ->values();


    /*
    |--------------------------------------------------------------------------
    | Catálogos
    |--------------------------------------------------------------------------
    */

    $catalogoGroups = collect([
        [
            'title' => 'Perfil académico',
            'items' => [
                [
                    'label' => 'Niveles académicos',
                    'route' => 'fac.catalogos.nivel-academico.index',
                    'active' => 'fac.catalogos.nivel-academico.*',
                ],
                [
                    'label' => 'Tipos de formación',
                    'route' => 'fac.catalogos.tipo-formacion.index',
                    'active' => 'fac.catalogos.tipo-formacion.*',
                ],
                [
                    'label' => 'Tipos de atestado',
                    'route' => 'fac.catalogos.tipo-atestado.index',
                    'active' => 'fac.catalogos.tipo-atestado.*',
                ],
            ],
        ],
        [
            'title' => 'Idiomas',
            'items' => [
                [
                    'label' => 'Idiomas',
                    'route' => 'fac.catalogos.idiomas.index',
                    'active' => 'fac.catalogos.idiomas.*',
                ],
                [
                    'label' => 'Niveles de idioma',
                    'route' => 'fac.catalogos.idioma-nivel.index',
                    'active' => 'fac.catalogos.idioma-nivel.*',
                ],
            ],
        ],
        [
            'title' => 'Ubicación',
            'items' => [
                [
                    'label' => 'Países',
                    'route' => 'fac.catalogos.paises.index',
                    'active' => 'fac.catalogos.paises.*',
                ],
                [
                    'label' => 'Departamentos',
                    'route' => 'fac.catalogos.departamentos.index',
                    'active' => 'fac.catalogos.departamentos.*',
                ],
                [
                    'label' => 'Municipios',
                    'route' => 'fac.catalogos.municipios_mh.index',
                    'active' => 'fac.catalogos.municipios_mh.*',
                ],
                [
                    'label' => 'Distritos',
                    'route' => 'fac.catalogos.municipios.index',
                    'active' => 'fac.catalogos.municipios.*',
                ],
            ],
        ],
        [
            'title' => 'Datos del consultor',
            'items' => [
                [
                    'label' => 'Sexo',
                    'route' => 'fac.catalogos.sexo.index',
                    'active' => 'fac.catalogos.sexo.*',
                ],
                [
                    'label' => 'Tipos de teléfono',
                    'route' => 'fac.catalogos.tipo_telefono.index',
                    'active' => 'fac.catalogos.tipo_telefono.*',
                ],
                [
                    'label' => 'Tipos de referencia',
                    'route' => 'fac.catalogos.tipo-referencia.index',
                    'active' => 'fac.catalogos.tipo-referencia.*',
                ],
                [
                    'label' => 'Tipos de red social',
                    'route' => 'fac.catalogos.tipo-red-social.index',
                    'active' => 'fac.catalogos.tipo-red-social.*',
                ],
                [
                    'label' => 'Tipos de documento',
                    'route' => 'fac.catalogos.tipo-documento.index',
                    'active' => 'fac.catalogos.tipo-documento.*',
                ],
            ],
        ],
        [
            'title' => 'Consultoría',
            'items' => [
                [
                    'label' => 'Áreas de especialización',
                    'route' => 'fac.catalogos.area-especializacion.index',
                    'active' => 'fac.catalogos.area-especializacion.*',
                ],
                [
                    'label' => 'Habilidades técnicas',
                    'route' => 'fac.catalogos.habilidad-tecnica.index',
                    'active' => 'fac.catalogos.habilidad-tecnica.*',
                ],
                [
                    'label' => 'Tipos de disponibilidad',
                    'route' => 'fac.catalogos.tipo-disponibilidad.index',
                    'active' => 'fac.catalogos.tipo-disponibilidad.*',
                ],
            ],
        ],
        [
            'title' => 'Exportación',
            'items' => [
                [
                    'label' => 'Plantillas CV',
                    'route' => 'fac.catalogos.cv-plantillas.index',
                    'active' => 'fac.catalogos.cv-plantillas.*',
                ],
            ],
        ],
    ])
        ->map(function ($group) use ($routeAvailable) {
            $group['items'] = collect($group['items'])
                ->filter(
                    fn ($item) =>
                        $routeAvailable($item['route'])
                )
                ->values();

            return $group;
        })
        ->filter(
            fn ($group) =>
                $group['items']->isNotEmpty()
        )
        ->values();

    $catalogosVisible =
        $can('fac.catalogos.gestionar')
        && $catalogoGroups->isNotEmpty();

    $consultoresVisible =
        $consultorItems->isNotEmpty();
@endphp


<aside class="app-sidebar">

    {{-- MARCA --}}
    <div class="sidebar-brand">

        <img
            src="{{ asset('img/isotipo-fepade-rojo.png') }}"
            alt="FEPADE"
        >

        <div class="sidebar-brand-text">

            <div class="sidebar-brand-title">
                CONSULTORES
            </div>

            <div class="sidebar-brand-subtitle">
                FEPADE 2026
            </div>

        </div>

    </div>


    {{-- =====================================================
         MI ESPACIO
         Se SUMA al resto del menú cuando existe id_consultor.
    ====================================================== --}}

    @if($tienePerfilConsultor)

        <div class="sidebar-section">
            Mi espacio
        </div>


        <a
            href="{{ route('fac.mi-perfil') }}"
            class="sidebar-link {{ $miPerfilActivo ? 'active' : '' }}"
        >
            <span class="sidebar-icon">
                <i class="fa-solid fa-user"></i>
            </span>

            <span class="sidebar-label">
                Mi perfil
            </span>
        </a>


        <a
            href="{{ route('fac.mis-capacitaciones') }}"
            class="sidebar-link {{ request()->routeIs('fac.mis-capacitaciones') ? 'active' : '' }}"
        >
            <span class="sidebar-icon">
                <i class="fa-solid fa-chalkboard-user"></i>
            </span>

            <span class="sidebar-label">
                Mis capacitaciones
            </span>
        </a>

    @endif


    {{-- =====================================================
         MENÚ SEGÚN PERMISOS
         Continúa mostrándose aunque exista id_consultor.
    ====================================================== --}}

    @if($dashboardVisible)

        <div class="sidebar-section">
            Principal
        </div>

        <a
            href="{{ route('fac.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('fac.dashboard') ? 'active' : '' }}"
        >
            <span class="sidebar-icon">
                <i class="fa-solid fa-house"></i>
            </span>

            <span class="sidebar-label">
                Dashboard
            </span>
        </a>

    @endif


    @if($consultoresVisible)

        <div class="sidebar-section">
            Módulos
        </div>

        <div
            class="sidebar-dropdown {{
                request()->routeIs(
                    'fac.consultores.*',
                    'fac.busqueda.*',
                    'fac.revision.*',
                    'fac.cv.*',
                    'seg.invitaciones.*'
                )
                    ? 'open'
                    : ''
            }}"
        >

            <button
                class="sidebar-dropdown-toggle"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuConsultores"
                aria-expanded="{{
                    request()->routeIs(
                        'fac.consultores.*',
                        'fac.busqueda.*',
                        'fac.revision.*',
                        'fac.cv.*',
                        'seg.invitaciones.*'
                    )
                        ? 'true'
                        : 'false'
                }}"
            >
                <span class="sidebar-icon">
                    <i class="fa-solid fa-users"></i>
                </span>

                <span class="sidebar-label">
                    Consultores
                </span>

                <span class="sidebar-caret">
                    <i class="fa-solid fa-chevron-down"></i>
                </span>
            </button>


            <div
                id="menuConsultores"
                class="collapse {{
                    request()->routeIs(
                        'fac.consultores.*',
                        'fac.busqueda.*',
                        'fac.revision.*',
                        'fac.cv.*',
                        'seg.invitaciones.*'
                    )
                        ? 'show'
                        : ''
                }}"
            >

                <div class="sidebar-submenu">

                    @foreach($consultorItems as $item)

                        <a
                            href="{{ route($item['route']) }}"
                            class="sidebar-sublink {{
                                request()->routeIs($item['active'])
                                    ? 'active'
                                    : ''
                            }}"
                        >
                            {{ $item['label'] }}
                        </a>

                    @endforeach

                </div>

            </div>

        </div>

    @endif


    @if($catalogosVisible)

        <div class="sidebar-section">
            Catálogos
        </div>

        <div
            class="sidebar-dropdown {{
                request()->routeIs('fac.catalogos.*')
                    ? 'open'
                    : ''
            }}"
        >

            <button
                class="sidebar-dropdown-toggle"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuCatalogos"
                aria-expanded="{{
                    request()->routeIs('fac.catalogos.*')
                        ? 'true'
                        : 'false'
                }}"
            >
                <span class="sidebar-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </span>

                <span class="sidebar-label">
                    Catálogos
                </span>

                <span class="sidebar-caret">
                    <i class="fa-solid fa-chevron-down"></i>
                </span>
            </button>


            <div
                id="menuCatalogos"
                class="collapse {{
                    request()->routeIs('fac.catalogos.*')
                        ? 'show'
                        : ''
                }}"
            >

                <div class="sidebar-submenu">

                    @foreach($catalogoGroups as $group)

                        <div class="sidebar-subtitle">
                            {{ $group['title'] }}
                        </div>

                        @foreach($group['items'] as $item)

                            <a
                                href="{{ route($item['route']) }}"
                                class="sidebar-sublink {{
                                    request()->routeIs($item['active'])
                                        ? 'active'
                                        : ''
                                }}"
                            >
                                {{ $item['label'] }}
                            </a>

                        @endforeach

                    @endforeach

                </div>

            </div>

        </div>

    @endif

</aside>