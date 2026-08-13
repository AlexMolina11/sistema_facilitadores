<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Seg\Models\Invitacion;
use App\Modules\Seg\Requests\StoreInvitacionRequest;
use App\Modules\Seg\Services\InvitacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvitacionController extends Controller
{
    public function __construct(
        private readonly InvitacionService $invitacionService
    ) {
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

        return redirect()
            ->route('seg.invitaciones.show', $invitacion)
            ->with(
                'success',
                'La invitación fue creada correctamente.'
            );
    }

    public function show(Invitacion $invitacion): View
    {
        $invitacion->load([
            'consultor',
            'rol',
            'creador',
        ]);

        return view(
            'seg.invitaciones.show',
            compact('invitacion')
        );
    }
}