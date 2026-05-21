<aside class="app-sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('img/isotipo-fepade-rojo.png') }}" alt="FEPADE">
        <div>
            <div class="sidebar-brand-title">CONSULTORES</div>
            <div class="sidebar-brand-subtitle">FEPADE 2026</div>
        </div>
    </div>

    <div class="sidebar-section">Principal</div>
        <a href="{{ route('fac.dashboard') }}" class="sidebar-link">
            Dashboard
        </a>

    <div class="sidebar-section">Seguridad</div>

        <a href="#" class="sidebar-link">Usuarios</a>
        <a href="#" class="sidebar-link">Roles</a>
        <a href="#" class="sidebar-link">Permisos</a>
        <a href="#" class="sidebar-link">Invitaciones</a>
        <a href="#" class="sidebar-link">Bitácora de acceso</a>

    <div class="sidebar-section">Consultores</div>
        <a href="{{ route('fac.consultores.index') }}" 
            class="sidebar-link {{ request()->routeIs('fac.consultores.*') ? 'active' : '' }}">
            Consultores
        </a>
        <a href="#" class="sidebar-link">Búsqueda avanzada</a>
        <a href="#" class="sidebar-link">Revisión de perfiles</a>
        <a href="#" class="sidebar-link">Exportación CV</a>

    <div class="sidebar-section">Catálogos</div>
        <a href="{{ route('fac.catalogos.idiomas.index') }}" 
            class="sidebar-link {{ request()->routeIs('fac.catalogos.idiomas.*') ? 'active' : '' }}">
            Idiomas   
        </a>
        <a href="{{ route('fac.catalogos.tipo-referencia.index') }}"
            class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo-referencia.*') ? 'active' : '' }}">
            Tipos de referencia
        </a>
        <a href="{{ route('fac.catalogos.tipo-formacion.index') }}"
        class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo-formacion.*') ? 'active' : '' }}">
            Tipos de formación
        </a>

            <a href="{{ route('fac.catalogos.tipo-atestado.index') }}"
        class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo-atestado.*') ? 'active' : '' }}">
            Tipos de atestado
        </a>

        <a href="{{ route('fac.catalogos.tipo-red-social.index') }}"
        class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo-red-social.*') ? 'active' : '' }}">
            Tipos de red social
        </a>

        <a href="{{ route('fac.catalogos.tipo-habilidad.index') }}"
        class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo-habilidad.*') ? 'active' : '' }}">
            Tipos de habilidad
        </a>

        <a href="{{ route('fac.catalogos.habilidad.index') }}"
        class="sidebar-link {{ request()->routeIs('fac.catalogos.habilidad.*') ? 'active' : '' }}">
            Habilidades
        </a>
        <a href="{{ route('fac.catalogos.paises.index') }}"
            class="sidebar-link {{ request()->routeIs('fac.catalogos.paises.*') ? 'active' : '' }}">
            Países
        </a>

        <a href="{{ route('fac.catalogos.departamentos.index') }}"
            class="sidebar-link {{ request()->routeIs('fac.catalogos.departamentos.*') ? 'active' : '' }}">
            Departamentos
        </a>
        <a href="{{ route('fac.catalogos.municipios_mh.index') }}"
            class="sidebar-link {{ request()->routeIs('fac.catalogos.municipios_mh.*') ? 'active' : '' }}">
            Municipios 
        </a>
        <a href="{{ route('fac.catalogos.municipios.index') }}"
            class="sidebar-link {{ request()->routeIs('fac.catalogos.municipios.*') ? 'active' : '' }}">
            Distritos
        </a>
        <a href="{{ route('fac.catalogos.tipo_telefono.index') }}"
            class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo_telefono.*') ? 'active' : '' }}">
            Tipos de teléfono
        </a>
        <a href="{{ route('fac.catalogos.idioma-nivel.index') }}" 
            class="sidebar-link {{ request()->routeIs('fac.catalogos.idioma-nivel.*') ? 'active' : '' }}">
            Niveles de idioma
        </a>

        <a href="{{ route('fac.catalogos.nivel-academico.index') }}"
            class="sidebar-link {{ request()->routeIs('fac.catalogos.nivel-academico.*') ? 'active' : '' }}">      
            Niveles académicos
        </a>

        <a href="{{ route('fac.catalogos.tipo-disponibilidad.index') }}"
        class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo-disponibilidad.*') ? 'active' : '' }}">     
            Tipo disponibilidad
        </a>

        <a href="{{ route('fac.catalogos.tipo-documento.index') }}"
        class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo-documento.*') ? 'active' : '' }}">
            Tipo documento
        </a>

        <a href="{{ route('fac.catalogos.tipo-consultoria.index') }}"
        class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo-consultoria.*') ? 'active' : '' }}">
            Tipo consultoría
        </a>

</aside>