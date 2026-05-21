<header class="app-topbar">
    <div class="topbar-left">
        <button type="button" class="topbar-sidebar-btn d-none d-lg-inline-flex" id="sidebarToggle">
            ☰
        </button>

        <button type="button" class="topbar-sidebar-btn d-inline-flex d-lg-none" id="sidebarMobileToggle">
            ☰
        </button>

        {{-- Desktop --}}
        <div class="topbar-page-info d-none d-lg-block">
            <strong>
                @yield('page-title', 'Panel principal')
            </strong>

            <div class="small">
                @yield('page-subtitle', 'Sistema de gestión de facilitadores')
            </div>
        </div>

        {{-- Mobile logo --}}
        <div class="topbar-mobile-logo d-lg-none">
            <img src="{{ asset('img/isotipo-fepade-rojo.png') }}"
                alt="FEPADE">
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="dropdown">
            <button class="topbar-menu-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Seguridad
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Usuarios</a></li>
                <li><a class="dropdown-item" href="#">Roles</a></li>
                <li><a class="dropdown-item" href="#">Permisos</a></li>
                <li><a class="dropdown-item" href="#">Invitaciones</a></li>
                <li><a class="dropdown-item" href="#">Bitácora de acceso</a></li>
            </ul>
        </div>

        <div class="text-end d-none d-sm-block">
            <div class="fw-bold">{{ auth()->user()->nombre ?? 'Usuario demo' }}</div>
            <div class="small">Administrador</div>
        </div>

        <div class="rounded-circle d-flex align-items-center justify-content-center"
             style="width:42px;height:42px;background:#00C896;color:white;font-weight:bold;">
            {{ strtoupper(substr(auth()->user()->nombre ?? 'U', 0, 1)) }}
        </div>
    </div>
</header>