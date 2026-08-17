<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Seg\Models\Invitacion;
use App\Modules\Seg\Requests\StoreInvitacionRequest;
use App\Modules\Seg\Services\InvitacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class InvitacionController extends Controller
{
    public function __construct(
        private readonly InvitacionService $invitacionService
    ) {
    }

    public function index(Request $request): View
    {
        $buscar = trim(
            (string) $request->get('buscar')
        );

        $estado = trim(
            (string) $request->get('estado')
        );

        $invitaciones = Invitacion::query()
            ->with([
                'consultor.usuario',

                'consultor.emails' => fn ($query) =>
                    $query
                        ->where('activo', true)
                        ->orderByDesc('principal'),

                'rol',
                'creador',
            ])

            /*
            |--------------------------------------------------------------------------
            | Búsqueda
            |--------------------------------------------------------------------------
            */
            ->when(
                $buscar !== '',
                function ($query) use ($buscar) {
                    $like = "%{$buscar}%";

                    $query->where(function ($q) use ($like) {
                        $q
                            ->where('alias', 'like', $like)

                            ->orWhereHas(
                                'consultor',
                                function ($consultor) use ($like) {
                                    $consultor
                                        ->where('nombres', 'like', $like)
                                        ->orWhere('apellidos', 'like', $like)
                                        ->orWhere(
                                            'numero_identificacion',
                                            'like',
                                            $like
                                        );
                                }
                            )

                            ->orWhereHas(
                                'consultor.emails',
                                function ($email) use ($like) {
                                    $email
                                        ->where('activo', true)
                                        ->where(
                                            'email',
                                            'like',
                                            $like
                                        );
                                }
                            );
                    });
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Estado: Activa
            |--------------------------------------------------------------------------
            */
            ->when(
                $estado === 'activa',
                function ($query) {
                    $query
                        ->where('activa', true)
                        ->where('revocada', false)

                        ->where(function ($q) {
                            $q
                                ->whereNull('fecha_expiracion')
                                ->orWhere(
                                    'fecha_expiracion',
                                    '>',
                                    now()
                                );
                        })

                        ->where(function ($q) {
                            $q
                                ->whereNull('max_usos')
                                ->orWhereColumn(
                                    'usos_actuales',
                                    '<',
                                    'max_usos'
                                );
                        })

                        ->whereHas(
                            'consultor',
                            fn ($consultor) =>
                                $consultor->whereDoesntHave('usuario')
                        );
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Estado: Vencida
            |--------------------------------------------------------------------------
            */
            ->when(
                $estado === 'vencida',
                function ($query) {
                    $query
                        ->where('revocada', false)
                        ->whereNotNull('fecha_expiracion')
                        ->where(
                            'fecha_expiracion',
                            '<=',
                            now()
                        );
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Estado: Revocada
            |--------------------------------------------------------------------------
            */
            ->when(
                $estado === 'revocada',
                function ($query) {
                    $query->where('revocada', true);
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Estado: Consumida
            |--------------------------------------------------------------------------
            */
            ->when(
                $estado === 'consumida',
                function ($query) {
                    $query
                        ->where('revocada', false)

                        ->where(function ($q) {
                            $q
                                ->whereNull('fecha_expiracion')
                                ->orWhere(
                                    'fecha_expiracion',
                                    '>',
                                    now()
                                );
                        })

                        ->where(function ($q) {
                            $q
                                ->where(function ($usos) {
                                    $usos
                                        ->whereNotNull('max_usos')
                                        ->whereColumn(
                                            'usos_actuales',
                                            '>=',
                                            'max_usos'
                                        );
                                })

                                ->orWhereHas(
                                    'consultor.usuario'
                                );
                        });
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Estado: Inactiva
            |--------------------------------------------------------------------------
            */
            ->when(
                $estado === 'inactiva',
                function ($query) {
                    $query
                        ->where('activa', false)
                        ->where('revocada', false)

                        ->where(function ($q) {
                            $q
                                ->whereNull('fecha_expiracion')
                                ->orWhere(
                                    'fecha_expiracion',
                                    '>',
                                    now()
                                );
                        });
                }
            )

            ->orderByDesc('created_at')
            ->orderByDesc('id_invitacion')
            ->paginate(10)
            ->withQueryString();

        return view(
            'seg.invitaciones.index',
            compact(
                'invitaciones',
                'buscar',
                'estado'
            )
        );
    }

    public function store(
        StoreInvitacionRequest $request,
        Consultor $consultor
    ): RedirectResponse {
        $duracionHoras = $request->boolean('duracion_ilimitada')
            ? null
            : (int) $request->input('duracion_horas');

        $maxUsos = $request->boolean('usos_ilimitados')
            ? null
            : (int) $request->input('max_usos');

        $invitacion = $this->invitacionService->crear(
            consultor: $consultor,
            usuarioCreador: $request->user(),
            duracionHoras: $duracionHoras,
            maxUsos: $maxUsos,
        );

        $consultor->loadMissing('emails');

        $tieneCorreo = $consultor->emails
            ->where('activo', true)
            ->isNotEmpty();

        return redirect()
            ->route(
                'seg.invitaciones.show',
                $invitacion
            )
            ->with(
                'success',
                'La invitación fue creada correctamente.'
            )
            ->with(
                'preguntar_envio_correo',
                $tieneCorreo
            );
    }

    public function show(
        Invitacion $invitacion
    ): View {
        $invitacion = $this->invitacionService
            ->generarQr($invitacion);

        $invitacion->load([
            'consultor.usuario',

            'consultor.emails' => fn ($query) =>
                $query
                    ->where('activo', true)
                    ->orderByDesc('principal'),

            'rol',
            'creador',
            'modificador',
        ]);

        return view(
            'seg.invitaciones.show',
            compact('invitacion')
        );
    }

    public function revoke(
        Request $request,
        Invitacion $invitacion
    ): RedirectResponse {
        $this->invitacionService->revocar(
            invitacion: $invitacion,
            usuario: $request->user(),
        );

        return redirect()
            ->route('seg.invitaciones.index')
            ->with(
                'success',
                'La invitación fue revocada correctamente.'
            );
    }

    public function downloadQr(
        Invitacion $invitacion
    ): BinaryFileResponse {
        if (
            ! $invitacion->ruta_qr
            || ! Storage::disk('public')->exists($invitacion->ruta_qr)
        ) {
            abort(404, 'El código QR de esta invitación no está disponible.');
        }

        $nombreArchivo = sprintf(
            'invitacion_%s_%d.svg',
            str($invitacion->alias)
                ->slug('_')
                ->toString(),
            $invitacion->id_invitacion
        );

        return response()->download(
            Storage::disk('public')->path($invitacion->ruta_qr),
            $nombreArchivo
        );
    }

    public function sendEmail(
        Invitacion $invitacion
    ): RedirectResponse {
        $this->invitacionService->enviarCorreo(
            $invitacion
        );

        return back()->with(
            'success',
            'La invitación fue enviada correctamente por correo electrónico.'
        );
    }
}