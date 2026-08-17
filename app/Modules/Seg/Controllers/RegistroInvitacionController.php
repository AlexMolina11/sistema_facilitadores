<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Seg\Models\Invitacion;
use App\Modules\Seg\Models\Usuario;
use App\Modules\Seg\Requests\RegistroPorInvitacionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegistroInvitacionController extends Controller
{
    public function show(string $token): View
    {
        $invitacion = Invitacion::query()
            ->where('token', $token)
            ->with([
                'consultor.emails' => fn ($query) =>
                    $query
                        ->where('activo', true)
                        ->orderByDesc('principal'),

                'consultor.documentos' => fn ($query) =>
                    $query
                        ->where('activo', true)
                        ->with('tipoDocumento'),

                'consultor.usuario',
                'rol',
            ])
            ->first();

        if (! $invitacion) {
            return $this->vistaInvalida(
                'La invitación solicitada no existe o ya no se encuentra disponible.'
            );
        }

        if (! $invitacion->puedeUsarse()) {
            return $this->vistaInvalida(
                $this->mensajeEstadoInvitacion($invitacion)
            );
        }

        $consultor = $invitacion->consultor;

        $correoPrincipal = $consultor
            ->emails
            ->firstWhere('principal', true)
            ?->email
            ?? $consultor
                ->emails
                ->first()
                ?->email;

        $documento = $consultor
            ->documentos
            ->first();

        return view(
            'seg.registro.invitacion',
            compact(
                'invitacion',
                'consultor',
                'correoPrincipal',
                'documento'
            )
        );
    }

    public function store(
        RegistroPorInvitacionRequest $request,
        string $token
    ): RedirectResponse {
        DB::transaction(function () use ($request, $token) {

            /*
            |--------------------------------------------------------------------------
            | Bloqueo de la invitación
            |--------------------------------------------------------------------------
            |
            | Evita que dos solicitudes simultáneas creen dos cuentas.
            |
            */
            $invitacion = Invitacion::query()
                ->where('token', $token)
                ->lockForUpdate()
                ->first();

            if (! $invitacion) {
                throw ValidationException::withMessages([
                    'invitacion' =>
                        'La invitación no existe o ya no se encuentra disponible.',
                ]);
            }

            $invitacion->load([
                'consultor.emails',
                'consultor.usuario',
                'rol',
            ]);

            if (! $invitacion->puedeUsarse()) {
                throw ValidationException::withMessages([
                    'invitacion' =>
                        $this->mensajeEstadoInvitacion($invitacion),
                ]);
            }

            $consultor = $invitacion->consultor;

            if (! $consultor) {
                throw ValidationException::withMessages([
                    'invitacion' =>
                        'La invitación no posee un consultor asociado.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Protección 1: relación usuario-consultor
            |--------------------------------------------------------------------------
            */
            if ($consultor->usuario()->exists()) {
                throw ValidationException::withMessages([
                    'invitacion' =>
                        'Este consultor ya posee credenciales de acceso.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Obtener correo del consultor
            |--------------------------------------------------------------------------
            */
            $correo = $consultor
                ->emails()
                ->where('activo', true)
                ->orderByDesc('principal')
                ->first();

            if (! $correo) {
                throw ValidationException::withMessages([
                    'email' =>
                        'El consultor no posee un correo electrónico disponible para crear su usuario.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Protección 2: correo único
            |--------------------------------------------------------------------------
            */
            if (
                Usuario::query()
                    ->where('email', $correo->email)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'email' =>
                        'Ya existe una cuenta registrada con el correo electrónico de este consultor.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Crear usuario
            |--------------------------------------------------------------------------
            */
            $usuario = Usuario::create([
                'id_consultor' => $consultor->id_consultor,
                'nombres' => $consultor->nombres,
                'apellidos' => $consultor->apellidos,
                'email' => $correo->email,
                'password' => $request->input('password'),
                'activo' => true,
                'usuario_crea' => null,
                'usuario_mod' => null,
                'usuario_elim' => null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Auditoría del propio registro
            |--------------------------------------------------------------------------
            */
            $usuario->update([
                'usuario_crea' => $usuario->id_usuario,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Asignar rol definido en la invitación
            |--------------------------------------------------------------------------
            */
            DB::table('seg_usuario_rol')->insert([
                'id_usuario' => $usuario->id_usuario,
                'id_rol' => $invitacion->id_rol,
                'usuario_crea' => $usuario->id_usuario,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Registrar consumo
            |--------------------------------------------------------------------------
            */
            $invitacion->increment('usos_actuales');

            $invitacion->update([
                'usuario_mod' => $usuario->id_usuario,
            ]);
        });

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Tu cuenta fue creada correctamente. Ya puedes iniciar sesión con tu correo y contraseña.'
            );
    }

    private function vistaInvalida(
        string $mensaje
    ): View {
        return view(
            'seg.registro.invitacion-invalida',
            compact('mensaje')
        );
    }

    private function mensajeEstadoInvitacion(
        Invitacion $invitacion
    ): string {
        if ($invitacion->estaRevocada()) {
            return 'Esta invitación fue revocada y ya no puede utilizarse.';
        }

        if ($invitacion->estaVencida()) {
            return 'Esta invitación ha vencido.';
        }

        if ($invitacion->alcanzoMaximoUsos()) {
            return 'Esta invitación alcanzó el máximo de usos permitidos.';
        }

        if (
            $invitacion->consultor
            && $invitacion->consultor->usuario()->exists()
        ) {
            return 'Este consultor ya creó sus credenciales de acceso.';
        }

        if (! $invitacion->estaActiva()) {
            return 'Esta invitación ya no se encuentra activa.';
        }

        return 'Esta invitación ya no se encuentra disponible.';
    }
}