<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Inicio de sesión | Facilitadores FEPADE</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('img/isotipo-fepade-rojo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/isotipo-fepade-rojo.png') }}">

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Font Awesome --}}
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >

    {{-- Estilos FEPADE --}}
    <link href="{{ asset('css/fepade.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fepade-ux-unificado.css') }}" rel="stylesheet">

    <style>
        /*
        |--------------------------------------------------------------------------
        | LOGIN FEPADE
        |--------------------------------------------------------------------------
        | Se utilizan únicamente variables ya definidas en fepade.css.
        */

        body.login-page {
            min-height: 100vh;
            margin: 0;
            background: var(--surface);
            overflow-x: hidden;
        }

        .login-wrapper {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(420px, 1fr) minmax(480px, 1fr);
        }


        /*
        |--------------------------------------------------------------------------
        | PANEL INSTITUCIONAL
        |--------------------------------------------------------------------------
        */

        .login-brand-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;

            padding: 4rem;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(0, 200, 150, .20),
                    transparent 34%
                ),
                linear-gradient(
                    135deg,
                    var(--navy) 0%,
                    var(--navy2) 100%
                );

            color: var(--white);
        }

        .login-brand-panel::before {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            right: -180px;
            bottom: -160px;
            background: rgba(255, 255, 255, .04);
        }

        .login-brand-panel::after {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            left: -120px;
            top: -100px;
            background: rgba(0, 200, 150, .08);
        }

        .login-brand-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 580px;
        }

        .login-logo-wrapper {
            margin-bottom: 3rem;
        }

        .login-logo {
            width: 235px;
            max-width: 100%;
            height: auto;

            /*
             * El logo compartido es negro.
             * Sobre fondo navy utilizamos una tarjeta blanca para
             * conservar el archivo institucional original.
             */
            background: #ffffff;
            padding: 16px 20px;
            border-radius: 16px;

            box-shadow:
                0 16px 40px rgba(0, 0, 0, .15);
        }

        .login-kicker {
            display: inline-block;

            margin-bottom: .8rem;

            color: var(--accent);

            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .login-brand-title {
            margin: 0 0 1rem;

            font-family: 'Syne', sans-serif;
            font-size: clamp(2.2rem, 4vw, 3.8rem);
            font-weight: 800;
            line-height: 1.05;

            color: #ffffff;
        }

        .login-brand-description {
            max-width: 500px;
            margin: 0;

            color: rgba(255, 255, 255, .68);

            font-size: 1rem;
            line-height: 1.7;
        }

        .login-brand-footer {
            margin-top: 3rem;

            display: flex;
            align-items: center;
            gap: .7rem;

            color: rgba(255, 255, 255, .45);

            font-size: .82rem;
        }

        .login-brand-footer-icon {
            width: 32px;
            height: 32px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 10px;

            background: rgba(255, 255, 255, .08);
            color: var(--accent);
        }


        /*
        |--------------------------------------------------------------------------
        | PANEL DEL FORMULARIO
        |--------------------------------------------------------------------------
        */

        .login-form-panel {
            position: relative;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 3rem;

            background: var(--surface);
        }

        .login-form-container {
            width: 100%;
            max-width: 460px;
        }


        /*
        |--------------------------------------------------------------------------
        | CABECERA MÓVIL
        |--------------------------------------------------------------------------
        */

        .login-mobile-logo {
            display: none;
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-mobile-logo img {
            width: 200px;
            max-width: 80%;
            height: auto;
        }


        /*
        |--------------------------------------------------------------------------
        | CABECERA DEL FORMULARIO
        |--------------------------------------------------------------------------
        */

        .login-form-kicker {
            color: var(--muted);

            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .10em;
            text-transform: uppercase;

            margin-bottom: .45rem;
        }

        .login-form-title {
            margin: 0 0 .6rem;

            color: var(--text);

            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
        }

        .login-form-description {
            margin: 0 0 2rem;

            color: var(--muted);

            font-size: .95rem;
            line-height: 1.55;
        }


        /*
        |--------------------------------------------------------------------------
        | TARJETA
        |--------------------------------------------------------------------------
        */

        .login-card {
            padding: 2rem;

            background: #ffffff;

            border: 1px solid var(--border);
            border-radius: 22px;

            box-shadow:
                0 18px 45px rgba(13, 27, 42, .07);
        }


        /*
        |--------------------------------------------------------------------------
        | CAMPOS
        |--------------------------------------------------------------------------
        */

        .login-field {
            margin-bottom: 1.25rem;
        }

        .login-field .form-label {
            display: block;

            margin-bottom: .5rem;

            color: var(--text);

            font-size: .86rem;
            font-weight: 700;
        }

        .login-input-wrapper {
            position: relative;
        }

        .login-input-icon {
            position: absolute;

            left: 1rem;
            top: 50%;

            transform: translateY(-50%);

            color: var(--muted);

            font-size: .9rem;

            pointer-events: none;

            z-index: 5;
        }

        .login-input-wrapper .form-control {
            min-height: 52px;

            padding-left: 2.8rem;

            background: var(--surface);

            border: 1.5px solid var(--surface2);
            border-radius: 13px;

            font-size: .94rem;

            transition: .15s ease;
        }

        /*
        |--------------------------------------------------------------------------
        | MOSTRAR CONTRASEÑA
        |--------------------------------------------------------------------------
        */

        .login-password-wrapper .form-control {
            padding-right: 3.2rem;
        }

        .login-password-peek {
            position: absolute;

            right: .65rem;
            top: 50%;

            transform: translateY(-50%);

            width: 38px;
            height: 38px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border: none;
            border-radius: 10px;

            background: transparent;

            color: var(--muted);

            cursor: pointer;

            z-index: 6;

            transition:
                color .15s ease,
                background .15s ease;
        }

        .login-password-peek:hover {
            background: rgba(13, 27, 42, .05);
            color: var(--navy);
        }

        .login-password-peek:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }

        .login-password-peek.is-active {
            background: rgba(0, 200, 150, .10);
            color: #007a5a;
        }

        /*
        * Evita comportamientos táctiles extraños al mantener presionado.
        */
        .login-password-peek {
            user-select: none;
            -webkit-user-select: none;
            touch-action: none;
        }

        .login-input-wrapper .form-control::placeholder {
            color: #a0a9b6;
        }

        .login-input-wrapper .form-control:focus {
            background: #ffffff;

            border-color: var(--accent);

            box-shadow:
                0 0 0 .22rem rgba(0, 200, 150, .10);
        }


        /*
        |--------------------------------------------------------------------------
        | RECORDAR SESIÓN
        |--------------------------------------------------------------------------
        */

        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;

            margin-bottom: 1.5rem;
        }

        .login-options .form-check {
            margin: 0;
        }

        .login-options .form-check-input {
            cursor: pointer;
        }

        .login-options .form-check-input:checked {
            background-color: var(--accent);
            border-color: var(--accent);
        }

        .login-options .form-check-label {
            cursor: pointer;

            color: var(--muted);

            font-size: .88rem;
            font-weight: 600;
        }

        .login-forgot-link {
            color: var(--navy);

            font-size: .86rem;
            font-weight: 700;

            text-decoration: none;

            transition: color .15s ease;
        }

        .login-forgot-link:hover {
            color: var(--accent);
            text-decoration: underline;
        }


        /*
        |--------------------------------------------------------------------------
        | BOTÓN PRINCIPAL
        |--------------------------------------------------------------------------
        */

        .login-submit {
            min-height: 52px;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: .65rem;

            width: 100%;

            border: none;
            border-radius: 13px;

            background: var(--navy);
            color: #ffffff;

            font-weight: 700;

            transition:
                transform .15s ease,
                box-shadow .15s ease,
                background .15s ease;
        }

        .login-submit:hover {
            background: var(--navy2);
            color: #ffffff;

            transform: translateY(-1px);

            box-shadow:
                0 10px 22px rgba(13, 27, 42, .16);
        }

        .login-submit:active {
            transform: translateY(0);
        }


        /*
        |--------------------------------------------------------------------------
        | ALERTAS
        |--------------------------------------------------------------------------
        */

        .login-alert {
            border: 0;
            border-radius: 13px;

            padding: .85rem 1rem;

            font-size: .88rem;
        }

        .login-alert.alert-success {
            background: rgba(0, 200, 150, .10);
            color: #007a5a;
        }

        .login-alert.alert-danger {
            background: rgba(226, 75, 74, .09);
            color: #a12f2e;
        }


        /*
        |--------------------------------------------------------------------------
        | PIE
        |--------------------------------------------------------------------------
        */

        .login-footer {
            margin-top: 1.5rem;

            text-align: center;

            color: var(--muted);

            font-size: .78rem;
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 991.98px) {

            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .login-brand-panel {
                display: none;
            }

            .login-form-panel {
                min-height: 100vh;
                padding: 2rem 1.25rem;
            }

            .login-mobile-logo {
                display: block;
            }

        }

        @media (max-width: 575.98px) {

            .login-form-panel {
                align-items: flex-start;
                padding-top: 2rem;
            }

            .login-card {
                padding: 1.4rem;
                border-radius: 18px;
            }

            .login-form-title {
                font-size: 1.65rem;
            }

            .login-form-description {
                margin-bottom: 1.5rem;
            }

            .login-options {
                align-items: flex-start;
                flex-direction: column;
                gap: .75rem;
            }

        }
    </style>
</head>

<body class="login-page">

<div class="login-wrapper">

    {{-- =====================================================
         PANEL INSTITUCIONAL
    ====================================================== --}}
    <section class="login-brand-panel">

        <div class="login-brand-content">

            <div class="login-logo-wrapper">
                <img
                    src="{{ asset('img/logo-fepade-horizontal-negro.png') }}"
                    alt="FEPADE"
                    class="login-logo"
                >
            </div>

            <span class="login-kicker">
                Sistema institucional
            </span>

            <h1 class="login-brand-title">
                Sistema de<br>
                Facilitadores
            </h1>

            <p class="login-brand-description">
                Plataforma para la gestión, consulta y seguimiento
                de la información profesional de los facilitadores
                de FEPADE.
            </p>

            <div class="login-brand-footer">

                <span class="login-brand-footer-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>

                <span>
                    Acceso exclusivo para usuarios autorizados
                </span>

            </div>

        </div>

    </section>


    {{-- =====================================================
         PANEL LOGIN
    ====================================================== --}}
    <main class="login-form-panel">

        <div class="login-form-container">

            {{-- Logo visible únicamente en tablet / móvil --}}
            <div class="login-mobile-logo">
                <img
                    src="{{ asset('img/logo-fepade-horizontal-negro.png') }}"
                    alt="FEPADE"
                >
            </div>


            <div class="login-form-kicker">
                Bienvenido
            </div>

            <h2 class="login-form-title">
                Iniciar sesión
            </h2>

            <p class="login-form-description">
                Ingresa tus credenciales para acceder al Sistema de Facilitadores.
            </p>


            <div class="login-card">

                {{-- Mensaje de éxito --}}
                @if (session('success'))

                    <div class="alert alert-success login-alert mb-4">

                        <div class="d-flex gap-2 align-items-start">

                            <i class="fa-solid fa-circle-check mt-1"></i>

                            <div>
                                {{ session('success') }}
                            </div>

                        </div>

                    </div>

                @endif


                {{-- Errores generales --}}
                @if ($errors->any())

                    <div class="alert alert-danger login-alert mb-4">

                        <div class="d-flex gap-2 align-items-start">

                            <i class="fa-solid fa-circle-exclamation mt-1"></i>

                            <div>
                                {{ $errors->first() }}
                            </div>

                        </div>

                    </div>

                @endif


                <form
                    method="POST"
                    action="{{ route('login.store') }}"
                >

                    @csrf


                    {{-- =====================================================
                         CORREO
                    ====================================================== --}}
                    <div class="login-field">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Correo electrónico
                        </label>

                        <div class="login-input-wrapper">

                            <span class="login-input-icon">
                                <i class="fa-regular fa-envelope"></i>
                            </span>

                            <input
                                type="email"
                                name="email"
                                id="email"

                                value="{{ old('email') }}"

                                class="form-control @error('email') is-invalid @enderror"

                                placeholder="usuario@fepade.org.sv"

                                autocomplete="email"

                                required
                                autofocus
                            >

                            @error('email')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>


                    {{-- =====================================================
                         CONTRASEÑA
                    ====================================================== --}}
                    <div class="login-field">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Contraseña
                        </label>

                        <div class="login-input-wrapper login-password-wrapper">

                            <span class="login-input-icon">
                                <i class="fa-solid fa-lock"></i>
                            </span>

                            <input
                                type="password"
                                name="password"
                                id="password"

                                class="form-control @error('password') is-invalid @enderror"

                                placeholder="Ingresa tu contraseña"

                                autocomplete="current-password"

                                required
                            >

                            <button
                                type="button"
                                id="passwordPeek"
                                class="login-password-peek"
                                aria-label="Mantén presionado para mostrar la contraseña"
                                title="Mantén presionado para mostrar la contraseña"
                            >
                                <i
                                    id="passwordPeekIcon"
                                    class="fa-regular fa-eye"
                                ></i>
                            </button>

                            @error('password')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>


                    {{-- =====================================================
                         OPCIONES
                    ====================================================== --}}
                    <div class="login-options">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                id="remember"
                                name="remember"
                                {{ old('remember') ? 'checked' : '' }}
                            >

                            <label
                                class="form-check-label"
                                for="remember"
                            >
                                Recordarme
                            </label>

                        </div>


                        <a
                            href="{{ route('password.request') }}"
                            class="login-forgot-link"
                        >
                            ¿Olvidaste tu contraseña?
                        </a>

                    </div>


                    {{-- =====================================================
                         BOTÓN
                    ====================================================== --}}
                    <button
                        type="submit"
                        class="login-submit"
                    >

                        <span>
                            Iniciar sesión
                        </span>

                        <i class="fa-solid fa-arrow-right"></i>

                    </button>

                </form>

            </div>


            <div class="login-footer">

                FEPADE · Sistema de Facilitadores

                <br>

                <span>
                    © {{ date('Y') }} Todos los derechos reservados
                </span>

            </div>

        </div>

    </main>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const passwordInput = document.getElementById('password');
        const passwordPeek = document.getElementById('passwordPeek');
        const passwordPeekIcon = document.getElementById('passwordPeekIcon');

        if (!passwordInput || !passwordPeek || !passwordPeekIcon) {
            return;
        }

        /**
         * Muestra temporalmente la contraseña.
         */
        const showPassword = () => {

            passwordInput.type = 'text';

            passwordPeek.classList.add('is-active');

            passwordPeekIcon.classList.remove('fa-eye');
            passwordPeekIcon.classList.add('fa-eye-slash');

        };


        /**
         * Oculta nuevamente la contraseña.
         */
        const hidePassword = () => {

            passwordInput.type = 'password';

            passwordPeek.classList.remove('is-active');

            passwordPeekIcon.classList.remove('fa-eye-slash');
            passwordPeekIcon.classList.add('fa-eye');

        };


        /*
        |--------------------------------------------------------------------------
        | Pointer Events
        |--------------------------------------------------------------------------
        | Funcionan con:
        |
        | - Mouse
        | - Pantalla táctil
        | - Stylus
        |
        */

        passwordPeek.addEventListener('pointerdown', function (event) {

            event.preventDefault();

            /*
             * Capturamos el puntero para recibir correctamente
             * pointerup incluso si el usuario mueve ligeramente
             * el mouse o el dedo.
             */
            try {
                passwordPeek.setPointerCapture(event.pointerId);
            } catch (error) {
                // Algunos navegadores pueden no requerir captura.
            }

            showPassword();

        });


        passwordPeek.addEventListener('pointerup', function (event) {

            event.preventDefault();

            hidePassword();

            try {
                passwordPeek.releasePointerCapture(event.pointerId);
            } catch (error) {
                // No hacemos nada si el puntero ya fue liberado.
            }

        });


        /*
         * Si la interacción se cancela por el navegador
         * o dispositivo táctil, volvemos a ocultarla.
         */
        passwordPeek.addEventListener('pointercancel', hidePassword);


        /*
         * Seguridad adicional:
         * si la ventana pierde el foco, nunca dejamos
         * la contraseña visible.
         */
        window.addEventListener('blur', hidePassword);


        /*
         * También la ocultamos si la pestaña deja de estar visible.
         */
        document.addEventListener('visibilitychange', function () {

            if (document.hidden) {
                hidePassword();
            }

        });

    });
</script>

</body>
</html>