<?php

namespace App\Modules\Seg\Middleware;

use App\Modules\Fac\Models\Consultor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureConsultorOwnerOrPermission
{
    public function handle(Request $request, Closure $next, string $permiso = 'fac.consultores.gestionar'): Response
    {
        $usuario = $request->user();

        if (!$usuario || !$usuario->activo) {
            abort(403, 'Usuario inactivo o no autenticado.');
        }

        if ($usuario->tienePermiso($permiso)) {
            return $next($request);
        }

        $consultorParam = $request->route('consultor');
        $idConsultor = $consultorParam instanceof Consultor
            ? $consultorParam->getKey()
            : (int) $consultorParam;

        if ($idConsultor && $usuario->esConsultorPropietario($idConsultor)) {
            return $next($request);
        }

        abort(403, 'Solo puede editar su propio perfil.');
    }
}
