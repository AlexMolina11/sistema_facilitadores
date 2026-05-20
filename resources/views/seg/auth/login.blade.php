<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Facilitadores FEPADE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/fepade.css') }}" rel="stylesheet">
</head>
<body>

<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background:#f5f7f2;">
    <div class="card border-0 shadow-lg" style="width:100%;max-width:430px;border-radius:24px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold" style="color:#385506;">FEPADE</h3>
                <p class="text-muted mb-0">Sistema de Facilitadores 2026</p>
            </div>

            @include('layouts.partials.alerts')

            <form method="POST" action="#">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="usuario@fepade.org.sv">
                </div>

                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="********">
                </div>

                <button type="submit" class="btn btn-fepade w-100">
                    Iniciar sesión
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>