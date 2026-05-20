<header class="app-topbar">
    <div>
        <strong>@yield('page-title', 'Panel principal')</strong>
        <div class="small">@yield('page-subtitle', 'Sistema de gestión de facilitadores')</div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="text-end">
            <div class="fw-bold">{{ auth()->user()->nombre ?? 'Usuario demo' }}</div>
            <div class="small">Administrador</div>
        </div>

        <div class="rounded-circle d-flex align-items-center justify-content-center"
             style="width:42px;height:42px;background:#00C896;color:white;font-weight:bold;">
            {{ strtoupper(substr(auth()->user()->nombre ?? 'U', 0, 1)) }}
        </div>
    </div>
</header>