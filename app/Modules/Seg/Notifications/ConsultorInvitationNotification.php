<?php

namespace App\Modules\Seg\Notifications;

use App\Modules\Seg\Models\Invitacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ConsultorInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Invitacion $invitacion
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->invitacion->loadMissing([
            'consultor',
        ]);

        $consultor = $this->invitacion->consultor;

        $qrUrl = null;

        if (
            $this->invitacion->ruta_qr
            && Storage::disk('public')->exists(
                $this->invitacion->ruta_qr
            )
        ) {
            $qrUrl = url(
                Storage::disk('public')->url(
                    $this->invitacion->ruta_qr
                )
            );
        }

        return (new MailMessage)
            ->subject(
                'Invitación al Sistema de Facilitadores FEPADE'
            )
            ->view(
                'seg.emails.invitacion-consultor',
                [
                    'invitacion' => $this->invitacion,
                    'consultor' => $consultor,
                    'qrUrl' => $qrUrl,
                ]
            );
    }
}