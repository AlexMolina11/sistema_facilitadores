<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Seg\Models\Permiso;
use App\Modules\Seg\Models\Rol;
use App\Modules\Seg\Models\Usuario;
use App\Modules\Seg\Services\BitacoraAccesoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function __construct(private readonly BitacoraAccesoService $bitacora) {}

    public function index(Request $request): View
    {
        $usuarios = Usuario::with(['roles', 'consultor'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nombres', 'like', "%{$request->q}%")
                        ->orWhere('apellidos', 'like', "%{$request->q}%")
                        ->orWhere('email', 'like', "%{$request->q}%");
                });
            })
            ->when($request->filled('activo'), fn ($q) => $q->where('activo', $request->boolean('activo')))
            ->when($request->filled('id_rol'), function ($q) use ($request) {
                $q->whereHas('roles', function ($rolQuery) use ($request) {
                    $rolQuery->where('seg_roles.id_rol', $request->integer('id_rol'));
                });
            })
            ->latest('id_usuario')
            ->paginate(15)
            ->withQueryString();

        $rolesFiltro = Rol::where('activo', true)
            ->orderBy('nombre')
            ->get(['id_rol', 'nombre']);

        return view('seg.usuarios.index', compact('usuarios', 'rolesFiltro'));
    }

    public function create(): View
    {
        return view('seg.usuarios.create', [
            'roles' => Rol::where('activo', true)->orderBy('nombre')->get(),
            'permisosPorModulo' => $this->permisosPorModulo(),
            'consultores' => Consultor::orderBy('nombres')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($request, $data) {
            $usuario = Usuario::create($data + [
                'usuario_crea' => auth()->id(),
                'activo' => true,
            ]);

            $usuario->roles()->sync($request->input('roles', []));
            $this->syncPermisosDirectos($usuario, $request);
            $this->bitacora->usuariosCrear($request);
        });

        return redirect()->route('seg.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Usuario $usuario): View
    {
        return view('seg.usuarios.edit', [
            'usuario' => $usuario->load(['roles', 'permisosDirectos']),
            'roles' => Rol::where('activo', true)->orderBy('nombre')->get(),
            'permisosPorModulo' => $this->permisosPorModulo(),
            'consultores' => Consultor::orderBy('nombres')->get(),
            'permisosPermitidos' => $usuario->permisosDirectos->where('pivot.permitido', true)->pluck('id_permiso')->toArray(),
            'permisosDenegados' => $usuario->permisosDirectos->where('pivot.permitido', false)->pluck('id_permiso')->toArray(),
            'permisosEfectivos' => $usuario->permisosEfectivos(),
        ]);
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $data = $this->validatedData($request, $usuario);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['activo'] = $request->boolean('activo');
        $data['usuario_mod'] = auth()->id();

        if (!$data['activo'] && $this->esUltimoAdministradorActivo($usuario)) {
            return back()->withInput()->with('error', 'No puedes desactivar el último usuario administrador activo.');
        }

        if ((int) auth()->id() === (int) $usuario->id_usuario && !$this->rolesIncluyenAdministrador($request->input('roles', []))) {
            return back()->withInput()->with('error', 'No puedes quitarte a ti mismo el rol Administrador.');
        }

        DB::transaction(function () use ($request, $usuario, $data) {
            $usuario->update($data);
            $usuario->roles()->sync($request->input('roles', []));
            $this->syncPermisosDirectos($usuario, $request);
            $this->bitacora->usuariosActualizar($request);
        });

        return redirect()->route('seg.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, Usuario $usuario): RedirectResponse
    {
        if ((int) auth()->id() === (int) $usuario->id_usuario) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($this->esUltimoAdministradorActivo($usuario)) {
            return back()->with('error', 'No puedes eliminar el último usuario administrador activo.');
        }

        DB::transaction(function () use ($request, $usuario) {
            $usuario->update(['usuario_elim' => auth()->id(), 'activo' => false]);
            $usuario->delete();
            $this->bitacora->usuariosEliminar($request);
        });

        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    private function validatedData(Request $request, ?Usuario $usuario = null): array
    {
        return $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('seg_usuarios', 'email')->ignore($usuario?->id_usuario, 'id_usuario')->whereNull('deleted_at'),
            ],
            'password' => [
                $usuario ? 'nullable' : 'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'id_consultor' => [
                'nullable', 'integer', 'exists:tbl_consultor,id_consultor',
                Rule::unique('seg_usuarios', 'id_consultor')->ignore($usuario?->id_usuario, 'id_usuario')->whereNull('deleted_at'),
            ],
            'activo' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:seg_roles,id_rol'],
            'permisos_permitidos' => ['nullable', 'array'],
            'permisos_permitidos.*' => ['integer', 'exists:seg_permisos,id_permiso'],
            'permisos_denegados' => ['nullable', 'array'],
            'permisos_denegados.*' => ['integer', 'exists:seg_permisos,id_permiso'],
        ]);
    }

    private function syncPermisosDirectos(Usuario $usuario, Request $request): void
    {
        $permitidos = collect($request->input('permisos_permitidos', []))->map(fn ($id) => (int) $id);
        $denegados = collect($request->input('permisos_denegados', []))->map(fn ($id) => (int) $id);

        $payload = [];

        foreach ($permitidos as $idPermiso) {
            if (!$denegados->contains($idPermiso)) {
                $payload[$idPermiso] = ['permitido' => true, 'usuario_crea' => auth()->id()];
            }
        }

        foreach ($denegados as $idPermiso) {
            $payload[$idPermiso] = ['permitido' => false, 'usuario_crea' => auth()->id()];
        }

        $usuario->permisosDirectos()->sync($payload);
    }

    private function permisosPorModulo()
    {
        return Permiso::where('activo', true)
            ->orderBy('modulo')
            ->orderBy('nombre')
            ->get()
            ->groupBy(fn ($permiso) => $permiso->modulo ?: 'General');
    }

    private function esUltimoAdministradorActivo(Usuario $usuario): bool
    {
        if (!$usuario->tieneRol('Administrador')) {
            return false;
        }

        return Usuario::where('activo', true)
            ->where('id_usuario', '<>', $usuario->id_usuario)
            ->whereHas('roles', fn ($q) => $q->where('nombre', 'Administrador')->where('seg_roles.activo', true))
            ->doesntExist();
    }

    private function rolesIncluyenAdministrador(array $roles): bool
    {
        return Rol::whereIn('id_rol', $roles)->where('nombre', 'Administrador')->exists();
    }
}
