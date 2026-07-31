<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Restablecimiento de contraseña</title>

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
                            Restablecimiento de contraseña
                        </h1>

                        <p class="email-text">
                            Hola,
                        </p>

                        <p class="email-text">
                            Recibimos una solicitud para restablecer
                            la contraseña de tu cuenta en el
                            <strong>Sistema de Facilitadores FEPADE</strong>.
                        </p>

                        <p class="email-text">
                            Para definir una nueva contraseña,
                            haz clic en el siguiente botón:
                        </p>


                        <div class="email-button-wrapper">

                            <a
                                href="{{ $resetUrl }}"
                                class="email-button"
                            >
                                Restablecer contraseña
                            </a>

                        </div>


                        <div class="email-info">

                            <p>
                                <strong>Importante:</strong>
                                este enlace tiene una vigencia de
                                <strong>60 minutos</strong>.
                                Si no solicitaste este cambio,
                                puedes ignorar este mensaje.
                            </p>

                        </div>


                        <p class="email-text">
                            Por seguridad, no compartas este enlace
                            con otras personas.
                        </p>


                        <div class="email-link-box">

                            <p>
                                Si el botón no funciona, copia y pega
                                este enlace en tu navegador:
                            </p>

                            <a
                                href="{{ $resetUrl }}"
                                class="email-url"
                            >
                                {{ $resetUrl }}
                            </a>

                        </div>

                    </td>
                </tr>


                {{-- FOOTER --}}
                <tr>
                    <td class="email-footer">

                        <p>
                            <strong>FEPADE · Sistema de Facilitadores</strong>
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