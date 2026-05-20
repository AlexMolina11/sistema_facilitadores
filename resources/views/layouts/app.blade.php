<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Facilitadores FEPADE')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/fepade.css') }}" rel="stylesheet">
</head>
<body>

<div class="app-wrapper">
    @include('layouts.partials.sidebar')

    <main class="app-main">
        @include('layouts.partials.topbar')

        <section class="app-content">
            @include('layouts.partials.alerts')

            @yield('content')
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>