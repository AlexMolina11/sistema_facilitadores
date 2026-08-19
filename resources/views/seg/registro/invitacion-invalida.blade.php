<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Invitación no disponible | FEPADE
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/fepade.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/fepade-ux-unificado.css') }}"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>

    <div class="registro-invitacion-page">

        <div class="registro-invitacion-invalid">

            <div class="registro-invalid-header">

                <div class="registro-invitacion-invalid-icon">
                    <i class="fa-solid fa-link-slash"></i>
                </div>

            </div>


            <div class="registro-invitacion-accent"></div>


            <div class="registro-invalid-body">

                <div class="registro-invitacion-kicker">
                    Sistema de Facilitadores
                </div>

                <h1>
                    Invitación no disponible
                </h1>


                <div class="registro-invalid-message">
                    {{ $mensaje }}
                </div>


                <p>
                    El enlace de invitación ya no puede utilizarse
                    para crear credenciales de acceso.
                </p>


                <div class="registro-invitacion-invalid-help">

                    <i class="fa-solid fa-circle-info me-1"></i>

                    Si necesitas acceder al sistema, comunícate con
                    el personal de FEPADE responsable de gestionar
                    tu perfil para solicitar una nueva invitación.

                </div>


                <a
                    href="{{ route('login') }}"
                    class="btn btn-navy"
                >
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Ir al inicio de sesión
                </a>

            </div>

        </div>
    </div>
</body>
</html>