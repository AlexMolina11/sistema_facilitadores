<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Recuperar contraseña | Facilitadores FEPADE</title>

    <link rel="icon" type="image/png" href="{{ asset('img/isotipo-fepade-rojo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/isotipo-fepade-rojo.png') }}">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >

    <link href="{{ asset('css/fepade.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fepade-ux-unificado.css') }}" rel="stylesheet">

    <style>
        body.auth-page {
            min-height: 100vh;
            margin: 0;
            background: var(--surface);
        }

        .auth-wrapper {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(420px, 1fr) minmax(480px, 1fr);
        }

        .auth-brand-panel {
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

            color: #ffffff;
        }

        .auth-brand-panel::before {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            right: -180px;
            bottom: -160px;
            background: rgba(255, 255, 255, .04);
        }

        .auth-brand-panel::after {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            left: -120px;
            top: -100px;
            background: rgba(0, 200, 150, .08);
        }

        .auth-brand-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 580px;
        }

        .auth-logo {
            width: 235px;
            max-width: 100%;
            height: auto;
            background: #ffffff;
            padding: 16px 20px;
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, .15);
            margin-bottom: 3rem;
        }

        .auth-kicker {
            display: inline-block;
            margin-bottom: .8rem;
            color: var(--accent);
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .auth-brand-title {
            margin: 0 0 1rem;
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.2rem, 4vw, 3.8rem);
            font-weight: 800;
            line-height: 1.05;
            color: #ffffff;
        }

        .auth-brand-description {
            max-width: 500px;
            margin: 0;
            color: rgba(255, 255, 255, .68);
            font-size: 1rem;
            line-height: 1.7;
        }

        .auth-brand-footer {
            margin-top: 3rem;
            display: flex;
            align-items: center;
            gap: .7rem;
            color: rgba(255, 255, 255, .45);
            font-size: .82rem;
        }

        .auth-brand-footer-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, .08);
            color: var(--accent);
        }

        .auth-form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: var(--surface);
        }

        .auth-form-container {
            width: 100%;
            max-width: 460px;
        }

        .auth-mobile-logo {
            display: none;
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-mobile-logo img {
            width: 200px;
            max-width: 80%;
            height: auto;
        }

        .auth-form-kicker {
            color: var(--muted);
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .10em;
            text-transform: uppercase;
            margin-bottom: .45rem;
        }

        .auth-form-title {
            margin: 0 0 .6rem;
            color: var(--text);
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
        }

        .auth-form-description {
            margin: 0 0 2rem;
            color: var(--muted);
            font-size: .95rem;
            line-height: 1.55;
        }

        .auth-card {
            padding: 2rem;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(13, 27, 42, .07);
        }

        .auth-field {
            margin-bottom: 1.25rem;
        }

        .auth-field .form-label {
            display: block;
            margin-bottom: .5rem;
            color: var(--text);
            font-size: .86rem;
            font-weight: 700;
        }

        .auth-input-wrapper {
            position: relative;
        }

        .auth-input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .9rem;
            pointer-events: none;
            z-index: 5;
        }

        .auth-input-wrapper .form-control {
            min-height: 52px;
            padding-left: 2.8rem;
            background: var(--surface);
            border: 1.5px solid var(--surface2);
            border-radius: 13px;
            font-size: .94rem;
        }

        .auth-input-wrapper .form-control:focus {
            background: #ffffff;
            border-color: var(--accent);
            box-shadow: 0 0 0 .22rem rgba(0, 200, 150, .10);
        }

        .auth-submit {
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
        }

        .auth-submit:hover {
            background: var(--navy2);
            color: #ffffff;
        }

        .auth-back {
            margin-top: 1rem;
            text-align: center;
        }

        .auth-back a {
            color: var(--navy);
            text-decoration: none;
            font-size: .88rem;
            font-weight: 700;
        }

        .auth-back a:hover {
            text-decoration: underline;
        }

        .auth-alert {
            border: 0;
            border-radius: 13px;
            padding: .85rem 1rem;
            font-size: .88rem;
        }

        .auth-alert.alert-success {
            background: rgba(0, 200, 150, .10);
            color: #007a5a;
        }

        .auth-alert.alert-danger {
            background: rgba(226, 75, 74, .09);
            color: #a12f2e;
        }

        .auth-footer {
            margin-top: 1.5rem;
            text-align: center;
            color: var(--muted);
            font-size: .78rem;
        }

        @media (max-width: 991.98px) {
            .auth-wrapper {
                grid-template-columns: 1fr;
            }

            .auth-brand-panel {
                display: none;
            }

            .auth-form-panel {
                min-height: 100vh;
                padding: 2rem 1.25rem;
            }

            .auth-mobile-logo {
                display: block;
            }
        }

        @media (max-width: 575.98px) {
            .auth-form-panel {
                align-items: flex-start;
                padding-top: 2rem;
            }

            .auth-card {
                padding: 1.4rem;
                border-radius: 18px;
            }

            .auth-form-title {
                font-size: 1.65rem;
            }
        }
    </style>
</head>

<body class="auth-page">

<div class="auth-wrapper">

    <section class="auth-brand-panel">

        <div class="auth-brand-content">

            <img
                src="{{ asset('img/logo-fepade-horizontal-negro.png') }}"
                alt="FEPADE"
                class="auth-logo"
            >

            <span class="auth-kicker">
                Seguridad de acceso
            </span>

            <h1 class="auth-brand-title">
                Recupera tu<br>
                acceso
            </h1>

            <p class="auth-brand-description">
                Ingresa el correo asociado a tu cuenta.
                Si existe una cuenta activa, recibirás un enlace seguro
                para establecer una nueva contraseña.
            </p>

            <div class="auth-brand-footer">

                <span class="auth-brand-footer-icon">
                    <i class="fa-solid fa-envelope-circle-check"></i>
                </span>

                <span>
                    El enlace de recuperación tiene tiempo limitado
                </span>

            </div>

        </div>

    </section>


    <main class="auth-form-panel">

        <div class="auth-form-container">

            <div class="auth-mobile-logo">
                <img
                    src="{{ asset('img/logo-fepade-horizontal-negro.png') }}"
                    alt="FEPADE"
                >
            </div>

            <div class="auth-form-kicker">
                Recuperación de cuenta
            </div>

            <h2 class="auth-form-title">
                ¿Olvidaste tu contraseña?
            </h2>

            <p class="auth-form-description">
                Ingresa tu correo electrónico y te enviaremos
                las instrucciones para restablecerla.
            </p>


            <div class="auth-card">

                @if (session('status'))

                    <div class="alert alert-success auth-alert mb-4">

                        <div class="d-flex gap-2 align-items-start">

                            <i class="fa-solid fa-circle-check mt-1"></i>

                            <div>
                                {{ session('status') }}
                            </div>

                        </div>

                    </div>

                @endif


                @if ($errors->any())

                    <div class="alert alert-danger auth-alert mb-4">

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
                    action="{{ route('password.email') }}"
                >

                    @csrf


                    <div class="auth-field">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Correo electrónico
                        </label>

                        <div class="auth-input-wrapper">

                            <span class="auth-input-icon">
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

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="auth-submit"
                    >

                        <span>
                            Enviar enlace de recuperación
                        </span>

                        <i class="fa-solid fa-paper-plane"></i>

                    </button>

                </form>


                <div class="auth-back">

                    <a href="{{ route('login') }}">

                        <i class="fa-solid fa-arrow-left me-1"></i>

                        Volver al inicio de sesión

                    </a>

                </div>

            </div>


            <div class="auth-footer">

                FEPADE · Sistema de Facilitadores

                <br>

                © {{ date('Y') }} Todos los derechos reservados

            </div>

        </div>

    </main>

</div>

</body>
</html>