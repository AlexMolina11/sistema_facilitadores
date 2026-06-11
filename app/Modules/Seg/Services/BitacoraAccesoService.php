<?php

namespace App\Modules\Seg\Services;

use App\Modules\Seg\Models\BitacoraAcceso;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class BitacoraAccesoService
{
    /**
     * Registra un evento en seg_bitacora_accesos.
     *
     * Nota técnica:
     * La columna oficial evento es VARCHAR(50), por eso este servicio
     * normaliza y recorta el texto para evitar errores SQL.
     */
    public function registrar(?int $idUsuario, string $evento, ?Request $request = null): void
    {
        try {
            $request ??= request();

            BitacoraAcceso::create([
                'id_usuario' => $idUsuario,
                'evento' => $this->normalizarEvento($evento),
                'ip' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'fecha_evento' => now(),
            ]);
        } catch (Throwable $exception) {
            // La bitácora nunca debe romper el flujo principal del sistema.
            report($exception);
        }
    }

    public function registrarActual(string $evento, ?Request $request = null): void
    {
        $this->registrar(auth()->id(), $evento, $request);
    }

    public function registrarAccion(string $modulo, string $accion, ?Request $request = null, ?int $idUsuario = null): void
    {
        $this->registrar(
            $idUsuario ?? auth()->id(),
            "{$modulo}: {$accion}",
            $request
        );
    }

    public function seguridadLoginExitoso(int $idUsuario, Request $request): void
    {
        $this->registrar($idUsuario, 'Seguridad: login exitoso', $request);
    }

    public function seguridadLoginFallido(?int $idUsuario, Request $request): void
    {
        $this->registrar($idUsuario, 'Seguridad: login fallido', $request);
    }

    public function seguridadUsuarioInactivo(int $idUsuario, Request $request): void
    {
        $this->registrar($idUsuario, 'Seguridad: usuario inactivo', $request);
    }

    public function seguridadLogout(?int $idUsuario, Request $request): void
    {
        $this->registrar($idUsuario, 'Seguridad: logout', $request);
    }

    public function usuariosCrear(?Request $request = null): void
    {
        $this->registrarActual('Usuarios: crear', $request);
    }

    public function usuariosActualizar(?Request $request = null): void
    {
        $this->registrarActual('Usuarios: actualizar', $request);
    }

    public function usuariosEliminar(?Request $request = null): void
    {
        $this->registrarActual('Usuarios: eliminar', $request);
    }

    public function rolesCrear(?Request $request = null): void
    {
        $this->registrarActual('Roles: crear', $request);
    }

    public function rolesActualizar(?Request $request = null): void
    {
        $this->registrarActual('Roles: actualizar', $request);
    }

    public function rolesEliminar(?Request $request = null): void
    {
        $this->registrarActual('Roles: eliminar', $request);
    }

    public function permisosCrear(?Request $request = null): void
    {
        $this->registrarActual('Permisos: crear', $request);
    }

    public function permisosActualizar(?Request $request = null): void
    {
        $this->registrarActual('Permisos: actualizar', $request);
    }

    public function permisosEliminar(?Request $request = null): void
    {
        $this->registrarActual('Permisos: eliminar', $request);
    }

    public static function eventosBase(): array
    {
        return [
            'Seguridad: login exitoso',
            'Seguridad: login fallido',
            'Seguridad: usuario inactivo',
            'Seguridad: logout',
            'Usuarios: crear',
            'Usuarios: actualizar',
            'Usuarios: eliminar',
            'Roles: crear',
            'Roles: actualizar',
            'Roles: eliminar',
            'Permisos: crear',
            'Permisos: actualizar',
            'Permisos: eliminar',
        ];
    }

    private function normalizarEvento(string $evento): string
    {
        $evento = trim(preg_replace('/\s+/', ' ', $evento));

        return Str::limit($evento, 50, '');
    }
}
