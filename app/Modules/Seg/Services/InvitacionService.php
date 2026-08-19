<?php

namespace App\Modules\Seg\Services;

use App\Modules\Fac\Models\Consultor;
use App\Modules\Seg\Models\Invitacion;
use App\Modules\Seg\Models\Rol;
use App\Modules\Seg\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Modules\Seg\Notifications\ConsultorInvitationNotification;
use Illuminate\Support\Facades\Notification;

class InvitacionService
{
    public function crear(
        Consultor $consultor,
        Usuario $usuarioCreador,
        ?int $duracionHoras,
        ?int $maxUsos,
        ?string $alias = null
    ): Invitacion {
        return DB::transaction(function () use (
            $consultor,
            $usuarioCreador,
            $duracionHoras,
            $maxUsos,
            $alias
        ) 
        {
            /*
            |--------------------------------------------------------------------------
            | 1. Validar consultor
            |--------------------------------------------------------------------------
            */

            if (! $consultor->activo || $consultor->deleted_at !== null) {
                throw ValidationException::withMessages([
                    'consultor' => 'El consultor no está disponible para generar una invitación.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Verificar que todavía no tenga usuario
            |--------------------------------------------------------------------------
            */

            if ($consultor->usuario()->exists()) {
                throw ValidationException::withMessages([
                    'consultor' => 'Este consultor ya posee un usuario asociado.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Evitar múltiples invitaciones utilizables
            |--------------------------------------------------------------------------
            */

            $invitacionActiva = $consultor->invitaciones()
                ->where('activa', true)
                ->where('revocada', false)
                ->where(function ($query) {
                    $query->whereNull('fecha_expiracion')
                        ->orWhere('fecha_expiracion', '>', now());
                })
                ->where(function ($query) {
                    $query->whereNull('max_usos')
                        ->orWhereColumn('usos_actuales', '<', 'max_usos');
                })
                ->exists();

            if ($invitacionActiva) {
                throw ValidationException::withMessages([
                    'consultor' => 'Este consultor ya posee una invitación activa y disponible.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 4. Obtener rol Consultor
            |--------------------------------------------------------------------------
            */

            $rolConsultor = Rol::query()
                ->where('nombre', 'Consultor')
                ->where('activo', true)
                ->first();

            if (! $rolConsultor) {
                throw ValidationException::withMessages([
                    'rol' => 'No se encontró un rol Consultor activo en el sistema.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 5. Generar token seguro
            |--------------------------------------------------------------------------
            */

            do {
                $token = Str::random(80);
            } while (
                Invitacion::withTrashed()
                    ->where('token', $token)
                    ->exists()
            );

            /*
            |--------------------------------------------------------------------------
            | 6. Calcular expiración
            |--------------------------------------------------------------------------
            */

            $fechaExpiracion = $duracionHoras !== null
                ? now()->addHours($duracionHoras)
                : null;

            /*
            |--------------------------------------------------------------------------
            | 7. Construir URL pública
            |--------------------------------------------------------------------------
            */

            $urlInvitacion = url('/registro/' . $token);

            /*
            |--------------------------------------------------------------------------
            | 8. Crear invitación
            |--------------------------------------------------------------------------
            */

            $invitacion = Invitacion::create([
                'id_consultor' => $consultor->id_consultor,
                'id_rol' => $rolConsultor->id_rol,
                'alias' => $alias ?: $consultor->nombre_completo,
                'token' => $token,
                'url_invitacion' => $urlInvitacion,
                'ruta_qr' => null,
                'duracion_horas' => $duracionHoras,
                'max_usos' => $maxUsos,
                'usos_actuales' => 0,
                'fecha_expiracion' => $fechaExpiracion,
                'activa' => true,
                'revocada' => false,
                'usuario_crea' => $usuarioCreador->id_usuario,
                'usuario_mod' => null,
                'usuario_elim' => null,
            ]);

            $rutaQr = sprintf(
                'invitaciones/qr/invitacion_%d.svg',
                $invitacion->id_invitacion
            );

            $contenidoQr = QrCode::format('svg')
                ->size(500)
                ->margin(2)
                ->generate($urlInvitacion);

            Storage::disk('public')->put(
                $rutaQr,
                $contenidoQr
            );

            $invitacion->update([
                'ruta_qr' => $rutaQr,
            ]);

            return $invitacion->fresh();
        });
    }

    public function revocar(
        Invitacion $invitacion,
        Usuario $usuario
    ): Invitacion {
        return DB::transaction(function () use ($invitacion, $usuario) {

            $invitacion->refresh();

            if ($invitacion->revocada) {
                throw ValidationException::withMessages([
                    'invitacion' => 'La invitación ya se encuentra revocada.',
                ]);
            }

            if ($invitacion->estado() !== 'Activa') {
                throw ValidationException::withMessages([
                    'invitacion' => 'Solo se pueden revocar invitaciones activas.',
                ]);
            }

            $invitacion->update([
                'revocada' => true,
                'activa' => false,
                'usuario_mod' => $usuario->id_usuario,
            ]);

            return $invitacion->fresh();
        });
    }

    public function generarQr(
        Invitacion $invitacion
    ): Invitacion {
        if (
            $invitacion->ruta_qr
            && Storage::disk('public')->exists($invitacion->ruta_qr)
        ) {
            return $invitacion;
        }

        if (! $invitacion->url_invitacion) {
            throw ValidationException::withMessages([
                'invitacion' => 'La invitación no posee una URL válida para generar el QR.',
            ]);
        }

        $rutaQr = sprintf(
            'invitaciones/qr/invitacion_%d.svg',
            $invitacion->id_invitacion
        );

        $contenidoQr = QrCode::format('svg')
            ->size(500)
            ->margin(2)
            ->generate($invitacion->url_invitacion);

        Storage::disk('public')->put(
            $rutaQr,
            $contenidoQr
        );

        $invitacion->update([
            'ruta_qr' => $rutaQr,
        ]);

        return $invitacion->fresh();
    }

    public function enviarCorreo(
        Invitacion $invitacion
    ): void {
        $invitacion->loadMissing([
            'consultor.emails',
        ]);

        $consultor = $invitacion->consultor;

        if (! $consultor) {
            throw ValidationException::withMessages([
                'invitacion' => 'La invitación no tiene un consultor asociado.',
            ]);
        }

        $correo = $consultor->emails
            ->where('activo', true)
            ->sortByDesc('principal')
            ->first();

        if (! $correo) {
            throw ValidationException::withMessages([
                'correo' => 'El consultor no posee un correo electrónico disponible.',
            ]);
        }

        if (! $invitacion->puedeUsarse()) {
            throw ValidationException::withMessages([
                'invitacion' => 'La invitación ya no se encuentra disponible para envío.',
            ]);
        }

        Notification::route(
            'mail',
            $correo->email
        )->notify(
            new ConsultorInvitationNotification(
                $invitacion
            )
        );
    }

}