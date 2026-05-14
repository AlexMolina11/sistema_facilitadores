<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Facilitadores FEPADE 2026')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header style="padding: 16px; background: #0f172a; color: white;">
        <strong>Facilitadores FEPADE 2026</strong>
    </header>

    <main style="padding: 24px;">
        @yield('content')
    </main>
</body>
</html>