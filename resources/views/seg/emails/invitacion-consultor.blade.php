<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Invitación al Sistema de Facilitadores</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #F7F9FC;
            font-family: Arial, Helvetica, sans-serif;
            color: #0D1B2A;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        img {
            border: 0;
            max-width: 100%;
        }

        .email-wrapper {
            width: 100%;
            background: #F7F9FC;
            padding: 36px 16px;
        }

        .email-container {
            width: 100%;
            max-width: 620px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #E8EDF4;
            box-shadow: 0 12px 30px rgba(13, 27, 42, 0.08);
        }

        .email-header {
            background: #0D1B2A;
            padding: 28px 32px;
            text-align: center;
        }

        .email-logo-box {
            display: inline-block;
            background: #FFFFFF;
            padding: 12px 18px;
            border-radius: 12px;
        }

        .email-logo {
            width: 190px;
            height: auto;
        }

        .email-accent {
            height: 5px;
            background: #00C896;
        }

        .email-body {
            padding: 36px 38px;
        }

        .email-kicker {
            display: inline-block;
            color: #00A97E;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .email-title {
            margin: 0 0 18px;
            color: #0D1B2A;
            font-size: 28px;
            line-height: 1.2;
        }

        .email-text {
            margin: 0 0 18px;
            color: #6B7A90;
            font-size: 15px;
            line-height: 1.65;
        }

        .email-button-wrapper {
            text-align: center;
            margin: 30px 0;
        }

        .email-button {
            display: inline-block;
            background: #0D1B2A;
            color: #FFFFFF !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            padding: 15px 26px;
            border-radius: 11px;
        }

        .email-button:hover {
            background: #162032;
        }

        .email-info {
            background: #F7F9FC;
            border: 1px solid #EEF2F8;
            border-radius: 12px;
            padding: 16px 18px;
            margin: 24px 0;
        }

        .email-info strong {
            color: #0D1B2A;
        }

        .email-info p {
            margin: 0;
            color: #6B7A90;
            font-size: 13px;
            line-height: 1.6;
        }

        .email-qr-wrapper {
            margin: 28px 0;
            text-align: center;
        }

        .email-qr-card {
            display: inline-block;
            background: #FFFFFF;
            border: 1px solid #E8EDF4;
            border-radius: 16px;
            padding: 18px;
        }

        .email-qr {
            display: block;
            width: 210px;
            max-width: 210px;
            height: auto;
            margin: 0 auto;
        }

        .email-qr-title {
            margin: 0 0 12px;
            color: #0D1B2A;
            font-size: 14px;
            font-weight: 700;
        }

        .email-qr-help {
            margin: 12px 0 0;
            color: #6B7A90;
            font-size: 12px;
            line-height: 1.5;
        }

        .email-link-box {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #EEF2F8;
        }

        .email-link-box p {
            margin: 0 0 8px;
            color: #6B7A90;
            font-size: 12px;
            line-height: 1.5;
        }

        .email-url {
            color: #0099FF;
            font-size: 12px;
            word-break: break-all;
        }

        .email-footer {
            background: #F7F9FC;
            padding: 22px 30px;
            text-align: center;
            border-top: 1px solid #EEF2F8;
        }

        .email-footer p {
            margin: 0;
            color: #6B7A90;
            font-size: 12px;
            line-height: 1.5;
        }

        .email-footer strong {
            color: #0D1B2A;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }

            .email-body {
                padding: 28px 22px;
            }

            .email-header {
                padding: 24px 20px;
            }

            .email-title {
                font-size: 23px;
            }

            .email-logo {
                width: 160px;
            }

            .email-button {
                display: block;
                width: auto;
            }

            .email-qr {
                width: 180px;
            }
        }
    </style>
</head>

<body>

<table
    role="presentation"
    width="100%"
    class="email-wrapper"
>
    <tr>
        <td>

            <table
                role="presentation"
                width="100%"
                class="email-container"
            >

                {{-- HEADER --}}
                <tr>
                    <td class="email-header">

                        <div class="email-logo-box">

                            <img
                                src="{{ asset('img/logo-fepade-horizontal-negro.png') }}"
                                alt="FEPADE"
                                class="email-logo"
                            >

                        </div>

                    </td>
                </tr>


                <tr>
                    <td class="email-accent"></td>
                </tr>


                {{-- BODY --}}
                <tr>
                    <td class="email-body">

                        <span class="email-kicker">
                            Sistema de Facilitadores
                        </span>

                        <h1 class="email-title">
                            Invitación de acceso
                        </h1>

                        <p class="email-text">
                            Hola
                            <strong>
                                {{ $consultor?->nombre_completo ?? $invitacion->alias }}
                            </strong>,
                        </p>

                        <p class="email-text">
                            FEPADE te invita a ingresar al
                            <strong>Sistema de Facilitadores</strong>
                            para crear tus credenciales de acceso y
                            completar o actualizar tu información profesional.
                        </p>

                        <p class="email-text">
                            Para comenzar, haz clic en el siguiente botón:
                        </p>


                        <div class="email-button-wrapper">

                            <a
                                href="{{ $invitacion->url_invitacion }}"
                                class="email-button"
                            >
                                Crear mi acceso
                            </a>

                        </div>


                        <div class="email-info">

                            <p>
                                <strong>Vigencia:</strong>

                                @if($invitacion->fecha_expiracion)

                                    este enlace estará disponible hasta el
                                    <strong>
                                        {{ $invitacion->fecha_expiracion->format('d/m/Y h:i A') }}
                                    </strong>.

                                @else

                                    esta invitación no tiene una fecha
                                    de expiración configurada.

                                @endif

                                @if($invitacion->max_usos !== null)

                                    El enlace permite un máximo de
                                    <strong>
                                        {{ $invitacion->max_usos }}
                                        {{ $invitacion->max_usos === 1 ? 'uso' : 'usos' }}
                                    </strong>.

                                @endif
                            </p>

                        </div>


                        @if($qrUrl)

                            <div class="email-qr-wrapper">

                                <div class="email-qr-card">

                                    <p class="email-qr-title">
                                        También puedes escanear el código QR
                                    </p>

                                    <img
                                        src="{{ $qrUrl }}"
                                        alt="Código QR de acceso"
                                        class="email-qr"
                                    >

                                    <p class="email-qr-help">
                                        Escanéalo con la cámara de tu
                                        teléfono para abrir directamente
                                        la invitación.
                                    </p>

                                </div>

                            </div>

                        @endif


                        <p class="email-text">
                            Por seguridad, este enlace está asociado
                            exclusivamente a tu perfil de consultor.
                            No lo compartas con otras personas.
                        </p>


                        <div class="email-link-box">

                            <p>
                                Si el botón o el código QR no funcionan,
                                copia y pega este enlace en tu navegador:
                            </p>

                            <a
                                href="{{ $invitacion->url_invitacion }}"
                                class="email-url"
                            >
                                {{ $invitacion->url_invitacion }}
                            </a>

                        </div>

                    </td>
                </tr>


                {{-- FOOTER --}}
                <tr>
                    <td class="email-footer">

                        <p>
                            <strong>
                                FEPADE · Sistema de Facilitadores
                            </strong>
                        </p>

                        <p>
                            Mensaje automático. No es necesario responder.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>