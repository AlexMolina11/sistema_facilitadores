<?php

namespace App\Modules\Seg\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(
            route(
                'password.reset',
                [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ],
                false
            )
        );

        return (new MailMessage)
            ->subject('Restablecimiento de contraseña · Facilitadores FEPADE')
            ->view('seg.emails.reset-password', [
                'resetUrl' => $resetUrl,
                'usuario' => $notifiable,
            ]);
    }
}