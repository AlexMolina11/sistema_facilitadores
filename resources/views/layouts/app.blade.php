<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Facilitadores FEPADE')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/fepade.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fepade-ux-unificado.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

<div class="app-wrapper" id="appWrapper">
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    @include('layouts.partials.sidebar')

    <main class="app-main">
        @include('layouts.partials.topbar')

        <section class="app-content">
            @include('layouts.partials.alerts')
            <x-ui.alerts />
            @yield('content')
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.getElementById('appWrapper');
        const btnToggle = document.getElementById('sidebarToggle');
        const btnMobile = document.getElementById('sidebarMobileToggle');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (btnToggle) {
            btnToggle.addEventListener('click', function () {
                wrapper.classList.toggle('sidebar-collapsed');
            });
        }

        if (btnMobile) {
            btnMobile.addEventListener('click', function () {
                wrapper.classList.toggle('sidebar-open');
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', function () {
                wrapper.classList.remove('sidebar-open');
            });
        }
    });
</script>

@stack('scripts')
</body>
</html>