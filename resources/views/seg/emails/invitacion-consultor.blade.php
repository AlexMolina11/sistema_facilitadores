<!DOCTYPE html>
<html lang="es" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Invitación al Sistema de Facilitadores</title>
    <!--[if mso]>
    <xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    <![endif]-->
    <style>
        :root { color-scheme: light; supported-color-schemes: light; }
        body, table, td, a, p { font-family: Arial, Helvetica, sans-serif; }
        table { border-collapse: collapse; border-spacing: 0; }
        img { border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        a { text-decoration: none; }
        @media only screen and (max-width: 620px) {
            .outer-pad { padding: 18px 10px !important; }
            .inner-pad { padding: 28px 22px !important; }
            .header-pad { padding: 24px 18px !important; }
            .fluid { width: 100% !important; max-width: 100% !important; }
            .title { font-size: 24px !important; }
            .logo { width: 160px !important; }
            .qr { width: 180px !important; height: 180px !important; }
        }
        /* Algunos clientes ignoran estos metadatos; la protección no es absoluta. */
        @media (prefers-color-scheme: dark) {
            .logo-surface { background-color: #FFFFFF !important; }
            .main-surface { background-color: #FFFFFF !important; }
            .text-dark { color: #0D1B2A !important; }
            .text-muted { color: #52647B !important; }
        }
        [data-ogsc] .logo-surface { background-color: #FFFFFF !important; }
        [data-ogsc] .main-surface { background-color: #FFFFFF !important; }
        [data-ogsc] .text-dark { color: #0D1B2A !important; }
    </style>
</head>
<body style="margin:0;padding:0;width:100%;background-color:#F7F9FC;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#F7F9FC" style="width:100%;background-color:#F7F9FC;">
<tr><td class="outer-pad" align="center" style="padding:36px 16px;">
<!--[if mso]><table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600"><tr><td><![endif]-->
<table role="presentation" class="fluid" cellpadding="0" cellspacing="0" border="0" width="600" style="width:100%;max-width:600px;background-color:#FFFFFF;" bgcolor="#FFFFFF">
    <tr>
        <td class="header-pad" align="center" bgcolor="#0D1B2A" style="padding:28px 32px;background-color:#0D1B2A;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF" class="logo-surface" style="background-color:#FFFFFF;border:1px solid #FFFFFF;">
                <tr><td align="center" bgcolor="#FFFFFF" class="logo-surface" style="padding:12px 18px;background-color:#FFFFFF;">
                    <img class="logo" src="{{ asset('img/logo-fepade-horizontal-negro.png') }}" alt="FEPADE" width="190" style="display:block;width:190px;max-width:100%;height:auto;color:#0D1B2A;" />
                </td></tr>
            </table>
        </td>
    </tr>
    <tr><td bgcolor="#00C896" height="5" style="height:5px;line-height:5px;font-size:0;background-color:#00C896;">&nbsp;</td></tr>
    <tr><td class="inner-pad main-surface" bgcolor="#FFFFFF" style="padding:36px 38px;background-color:#FFFFFF;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr><td style="padding:0 0 10px;color:#008A69;font-size:12px;line-height:18px;font-weight:bold;letter-spacing:1px;">SISTEMA DE FACILITADORES</td></tr>
            <tr><td class="title text-dark" style="padding:0 0 18px;color:#0D1B2A;font-size:28px;line-height:34px;font-weight:bold;">Invitación de acceso</td></tr>
            <tr><td class="text-muted" style="padding:0 0 18px;color:#52647B;font-size:15px;line-height:25px;">Hola <strong>{{ $consultor?->nombre_completo ?? $invitacion->alias }}</strong>,</td></tr>
            <tr><td class="text-muted" style="padding:0 0 18px;color:#52647B;font-size:15px;line-height:25px;">FEPADE te invita a ingresar al <strong>Sistema de Facilitadores</strong> para crear tus credenciales de acceso y completar o actualizar tu información profesional.</td></tr>
            <tr><td class="text-muted" style="padding:0 0 12px;color:#52647B;font-size:15px;line-height:25px;">Para comenzar, haz clic en el siguiente botón:</td></tr>
            <tr><td align="center" style="padding:18px 0 30px;">
                <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $invitacion->url_invitacion }}" style="height:48px;v-text-anchor:middle;width:220px;" arcsize="20%" stroke="f" fillcolor="#0D1B2A">
                <w:anchorlock xmlns:w="urn:schemas-microsoft-com:office:word"/>
                <center style="color:#FFFFFF;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;">Crear mi acceso</center>
                </v:roundrect>
                <![endif]-->
                <!--[if !mso]><!-->
                <a href="{{ $invitacion->url_invitacion }}" style="display:inline-block;background-color:#0D1B2A;border:1px solid #0D1B2A;border-radius:10px;color:#FFFFFF;font-size:15px;font-weight:bold;line-height:20px;padding:14px 26px;text-align:center;text-decoration:none;mso-hide:all;">Crear mi acceso</a>
                <!--<![endif]-->
            </td></tr>
            <tr><td bgcolor="#F7F9FC" style="padding:16px 18px;background-color:#F7F9FC;border:1px solid #EEF2F8;color:#52647B;font-size:13px;line-height:21px;">
                <strong style="color:#0D1B2A;">Vigencia:</strong>
                @if($invitacion->fecha_expiracion)
                    este enlace estará disponible hasta el <strong style="color:#0D1B2A;">{{ $invitacion->fecha_expiracion->format('d/m/Y h:i A') }}</strong>.
                @else
                    esta invitación no tiene una fecha de expiración configurada.
                @endif
                @if($invitacion->max_usos !== null)
                    El enlace permite un máximo de <strong style="color:#0D1B2A;">{{ $invitacion->max_usos }} {{ $invitacion->max_usos === 1 ? 'uso' : 'usos' }}</strong>.
                @endif
            </td></tr>
            @if($qrUrl)
            <tr><td align="center" style="padding:28px 0;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF" style="background-color:#FFFFFF;border:1px solid #E8EDF4;">
                    <tr><td align="center" style="padding:18px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                            <tr><td class="text-dark" align="center" style="padding:0 0 12px;color:#0D1B2A;font-size:14px;line-height:20px;font-weight:bold;">También puedes escanear el código QR</td></tr>
                            <tr><td align="center" bgcolor="#FFFFFF" style="background-color:#FFFFFF;"><img class="qr" src="{{ $qrUrl }}" width="210" height="210" alt="Código QR de acceso" style="display:block;width:210px;height:210px;" /></td></tr>
                            <tr><td class="text-muted" align="center" style="padding:12px 0 0;color:#52647B;font-size:12px;line-height:18px;">Escanéalo con la cámara de tu teléfono<br>para abrir directamente la invitación.</td></tr>
                        </table>
                    </td></tr>
                </table>
            </td></tr>
            @endif
            <tr><td class="text-muted" style="padding:24px 0;color:#52647B;font-size:15px;line-height:25px;">Por seguridad, este enlace está asociado exclusivamente a tu perfil de consultor. No lo compartas con otras personas.</td></tr>
            <tr><td style="border-top:1px solid #EEF2F8;padding:20px 0 0;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr><td class="text-muted" style="padding:0 0 8px;color:#52647B;font-size:12px;line-height:18px;">Si el botón o el código QR no funcionan, copia y pega este enlace en tu navegador:</td></tr>
                    <tr><td style="font-size:12px;line-height:20px;word-break:break-all;overflow-wrap:anywhere;"><a href="{{ $invitacion->url_invitacion }}" style="color:#0078CC;text-decoration:underline;word-break:break-all;">{{ $invitacion->url_invitacion }}</a></td></tr>
                </table>
            </td></tr>
        </table>
    </td></tr>
    <tr><td align="center" bgcolor="#F7F9FC" style="padding:22px 30px;background-color:#F7F9FC;border-top:1px solid #EEF2F8;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr><td align="center" style="color:#0D1B2A;font-size:12px;line-height:18px;font-weight:bold;">FEPADE · Sistema de Facilitadores</td></tr>
            <tr><td align="center" style="padding-top:5px;color:#52647B;font-size:12px;line-height:18px;">Mensaje automático. No es necesario responder.</td></tr>
        </table>
    </td></tr>
</table>
<!--[if mso]></td></tr></table><![endif]-->
</td></tr>
</table>
</body>
</html>
