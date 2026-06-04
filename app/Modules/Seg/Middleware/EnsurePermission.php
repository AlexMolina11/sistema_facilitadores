<?php

namespace App\Modules\Seg\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string ...$permisos): Response
    {
        $usuario = $request->user();

        if (!$usuario || !$usuario->activo) {
            abort(403, 'Usuario inactivo o no autenticado.');
        }

        foreach ($permisos as $permiso) {
            if ($usuario->tienePermiso($permiso)) {
                return $next($request);
            }
        }

        abort(403, 'No tiene permisos para acceder a esta sección.');
    }
}
