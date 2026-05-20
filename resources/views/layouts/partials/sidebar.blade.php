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
    <a href="#" class="sidebar-link">Consultores</a>
    <a href="#" class="sidebar-link">Búsqueda avanzada</a>
    <a href="#" class="sidebar-link">Revisión de perfiles</a>
    <a href="#" class="sidebar-link">Exportación CV</a>

    <div class="sidebar-section">Catálogos</div>
    <a href="{{ route('fac.catalogos.idiomas.index') }}" 
        class="sidebar-link {{ request()->routeIs('fac.catalogos.idiomas.*') ? 'active' : '' }}">
        Idiomas
    </a>
    <a href="{{ route('fac.catalogos.idioma-nivel.index') }}" 
        class="sidebar-link {{ request()->routeIs('fac.catalogos.idioma-nivel.*') ? 'active' : '' }}">
        Niveles de idioma

        <a href="{{ route('fac.catalogos.nivel-academico.index') }}"
        class="sidebar-link {{ request()->routeIs('fac.catalogos.nivel-academico.*') ? 'active' : '' }}">      
        Niveles académicos
    <a href="{{ route('fac.catalogos.tipo-disponibilidad.index') }}"
    class="sidebar-link {{ request()->routeIs('fac.catalogos.tipo-disponibilidad.*') ? 'active' : '' }}">     
        Tipo disponibilidad


</a>
</a>
    </a>
    <a href="#" class="sidebar-link">Países</a>
    <a href="#" class="sidebar-link">Departamentos</a>
    <a href="#" class="sidebar-link">Municipios</a>
    <a href="#" class="sidebar-link">Habilidades</a>
    <a href="#" class="sidebar-link">Tipos de consultoría</a>

</aside>