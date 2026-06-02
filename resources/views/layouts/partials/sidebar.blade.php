<aside class="app-sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('img/isotipo-fepade-rojo.png') }}" alt="FEPADE">

        <div class="sidebar-brand-text">
            <div class="sidebar-brand-title">CONSULTORES</div>
            <div class="sidebar-brand-subtitle">FEPADE 2026</div>
        </div>
    </div>

    <div class="sidebar-section">Principal</div>

    <a href="{{ route('fac.dashboard') }}"
       class="sidebar-link {{ request()->routeIs('fac.dashboard') ? 'active' : '' }}">
        <span class="sidebar-icon">⌂</span>
        <span class="sidebar-label">Dashboard</span>
    </a>

    <div class="sidebar-section">Módulos</div>

    <div class="sidebar-dropdown {{ request()->routeIs('fac.consultores.*') ? 'open' : '' }}">
        <button class="sidebar-dropdown-toggle"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuConsultores"
                aria-expanded="{{ request()->routeIs('fac.consultores.*') ? 'true' : 'false' }}">
            <span class="sidebar-icon">◉</span>
            <span class="sidebar-label">Consultores</span>
            <span class="sidebar-caret">▾</span>
        </button>

        <div id="menuConsultores"
             class="collapse {{ request()->routeIs('fac.consultores.*') ? 'show' : '' }}">
            <div class="sidebar-submenu">
                <a href="{{ route('fac.consultores.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.consultores.index') ? 'active' : '' }}">
                    Listado de consultores
                </a>

                <a href="#" class="sidebar-sublink">
                    Búsqueda avanzada
                </a>

                <a href="#" class="sidebar-sublink">
                    Revisión de perfiles
                </a>

                <a href="#" class="sidebar-sublink">
                    Exportación CV
                </a>
            </div>
        </div>
    </div>

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

                <div class="sidebar-subtitle">Perfil académico</div>

                <a href="{{ route('fac.catalogos.nivel-academico.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.nivel-academico.*') ? 'active' : '' }}">
                    Niveles académicos
                </a>

                <a href="{{ route('fac.catalogos.tipo-formacion.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.tipo-formacion.*') ? 'active' : '' }}">
                    Tipos de formación
                </a>

                <a href="{{ route('fac.catalogos.tipo-atestado.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.tipo-atestado.*') ? 'active' : '' }}">
                    Tipos de atestado
                </a>

                <div class="sidebar-subtitle">Idiomas</div>

                <a href="{{ route('fac.catalogos.idiomas.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.idiomas.*') ? 'active' : '' }}">
                    Idiomas
                </a>

                <a href="{{ route('fac.catalogos.idioma-nivel.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.idioma-nivel.*') ? 'active' : '' }}">
                    Niveles de idioma
                </a>

                <div class="sidebar-subtitle">Ubicación</div>

                <a href="{{ route('fac.catalogos.paises.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.paises.*') ? 'active' : '' }}">
                    Países
                </a>

                <a href="{{ route('fac.catalogos.departamentos.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.departamentos.*') ? 'active' : '' }}">
                    Departamentos
                </a>

                <a href="{{ route('fac.catalogos.municipios_mh.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.municipios_mh.*') ? 'active' : '' }}">
                    Municipios
                </a>

                <a href="{{ route('fac.catalogos.municipios.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.municipios.*') ? 'active' : '' }}">
                    Distritos
                </a>

                <div class="sidebar-subtitle">Datos del consultor</div>

                <a href="{{ route('fac.catalogos.tipo_telefono.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.tipo_telefono.*') ? 'active' : '' }}">
                    Tipos de teléfono
                </a>

                <a href="{{ route('fac.catalogos.tipo-referencia.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.tipo-referencia.*') ? 'active' : '' }}">
                    Tipos de referencia
                </a>

                <a href="{{ route('fac.catalogos.tipo-red-social.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.tipo-red-social.*') ? 'active' : '' }}">
                    Tipos de red social
                </a>

                <a href="{{ route('fac.catalogos.tipo-documento.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.tipo-documento.*') ? 'active' : '' }}">
                    Tipos de documento
                </a>

                <div class="sidebar-subtitle">Consultoría</div>

                <a href="{{ route('fac.catalogos.tipo-consultoria.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.tipo-consultoria.*') ? 'active' : '' }}">
                    Tipos de consultoría
                </a>

                <a href="{{ route('fac.catalogos.tipo-habilidad.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.tipo-habilidad.*') ? 'active' : '' }}">
                    Tipos de habilidad
                </a>

                <a href="{{ route('fac.catalogos.habilidad.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.habilidad.*') ? 'active' : '' }}">
                    Habilidades
                </a>

                <a href="{{ route('fac.catalogos.tipo-disponibilidad.index') }}"
                   class="sidebar-sublink {{ request()->routeIs('fac.catalogos.tipo-disponibilidad.*') ? 'active' : '' }}">
                    Tipos de disponibilidad
                </a>

                <a href="{{ route('fac.catalogos.sexo.index') }}"
                   class="sidebar-link {{ request()->routeIs('fac.catalogos.sexo.*') ? 'active' : '' }}">
                         Sexo
                </a>

            </div>
        </div>
    </div>
</aside>