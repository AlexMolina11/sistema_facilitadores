<?php

namespace App\Modules\Seg\Services;

use App\Modules\Seg\Models\BitacoraAcceso;
use Illuminate\Http\Request;

class BitacoraAccesoService
{
    public function registrar(?int $idUsuario, string $evento, ?Request $request = null): void
    {
        BitacoraAcceso::create([
            'id_usuario' => $idUsuario,
            'evento' => $evento,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'fecha_evento' => now(),
        ]);
    }
}
