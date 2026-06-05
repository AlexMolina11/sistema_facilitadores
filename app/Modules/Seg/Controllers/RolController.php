<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Seg\Models\Permiso;
use App\Modules\Seg\Models\Rol;
use App\Modules\Seg\Services\BitacoraAccesoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RolController extends Controller
{
    public function __construct(private readonly BitacoraAccesoService $bitacora) {}

    public function index(Request $request): View
    {
        $roles = Rol::withCount(['usuarios', 'permisos'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nombre', 'like', "%{$request->q}%")
                        ->orWhere('descripcion', 'like', "%{$request->q}%");
                });
            })
            ->when($request->filled('activo'), fn ($q) => $q->where('activo', $request->boolean('activo')))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('seg.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('seg.roles.create', [
            'rol' => null,
            'permisosPorModulo' => $this->permisosPorModulo(),
            'permisosSeleccionados' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $rol = Rol::create($data + ['activo' => $request->boolean('activo', true)]);
        $rol->permisos()->sync($request->input('permisos', []));
        $this->bitacora->registrarActual('seg_rol_creado', $request);

        return redirect()->route('seg.roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(Rol $role): View
    {
        return view('seg.roles.edit', [
            'rol' => $role->load('permisos'),
            'permisosPorModulo' => $this->permisosPorModulo(),
            'permisosSeleccionados' => $role->permisos->pluck('id_permiso')->toArray(),
        ]);
    }

    public function update(Request $request, Rol $role): RedirectResponse
    {
        $data = $this->validatedData($request, $role);

        if ($role->nombre === 'Administrador') {
            $data['activo'] = true;
        } else {
            $data['activo'] = $request->boolean('activo');
        }

        $role->update($data);
        $role->permisos()->sync($request->input('permisos', []));
        $this->bitacora->registrarActual('seg_rol_actualizado', $request);

        return redirect()->route('seg.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Request $request, Rol $role): RedirectResponse
    {
        if ($role->nombre === 'Administrador') {
            return back()->with('error', 'El rol Administrador no puede eliminarse.');
        }

        if ($role->usuarios()->exists()) {
            return back()->with('error', 'No se puede eliminar un rol asignado a usuarios. Puedes desactivarlo.');
        }

        $role->update(['activo' => false]);
        $role->delete();
        $this->bitacora->registrarActual('seg_rol_eliminado', $request);

        return back()->with('success', 'Rol eliminado correctamente.');
    }

    private function validatedData(Request $request, ?Rol $rol = null): array
    {
        return $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('seg_roles', 'nombre')
                    ->ignore($rol?->id_rol, 'id_rol')
                    ->whereNull('deleted_at'),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
            'permisos' => ['nullable', 'array'],
            'permisos.*' => ['integer', 'exists:seg_permisos,id_permiso'],
        ]);
    }

    private function permisosPorModulo()
    {
        return Permiso::where('activo', true)
            ->orderBy('modulo')
            ->orderBy('nombre')
            ->get()
            ->groupBy(fn ($permiso) => $permiso->modulo ?: 'General');
    }
}
