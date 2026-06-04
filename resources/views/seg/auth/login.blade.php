<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio de sesión | Facilitadores FEPADE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/fepade.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-sm border-0" style="max-width: 430px; width: 100%;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <img src="{{ asset('img/logo-fepade-horizontal-negro.png') }}" alt="FEPADE" style="max-height: 64px;" class="mb-3">
                <h1 class="h4 mb-1">Sistema de Facilitadores</h1>
                <p class="text-muted mb-0">Ingrese sus credenciales para continuar</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Recordarme</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
