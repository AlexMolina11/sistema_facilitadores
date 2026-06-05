<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Seg\Models\Permiso;
use App\Modules\Seg\Services\BitacoraAccesoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PermisoController extends Controller
{
    public function __construct(private readonly BitacoraAccesoService $bitacora) {}

    public function index(Request $request): View
    {
        $permisos = Permiso::withCount(['roles', 'usuariosDirectos'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('codigo', 'like', "%{$request->q}%")
                        ->orWhere('nombre', 'like', "%{$request->q}%")
                        ->orWhere('descripcion', 'like', "%{$request->q}%")
                        ->orWhere('modulo', 'like', "%{$request->q}%");
                });
            })
            ->when($request->filled('modulo'), fn ($q) => $q->where('modulo', $request->modulo))
            ->when($request->filled('activo'), fn ($q) => $q->where('activo', $request->boolean('activo')))
            ->orderBy('modulo')
            ->orderBy('codigo')
            ->paginate(20)
            ->withQueryString();

        $modulos = Permiso::query()
            ->select('modulo')
            ->whereNotNull('modulo')
            ->distinct()
            ->orderBy('modulo')
            ->pluck('modulo');

        return view('seg.permisos.index', compact('permisos', 'modulos'));
    }

    public function create(): View
    {
        return view('seg.permisos.create', ['permiso' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['codigo'] = strtolower(trim($data['codigo']));
        $data['modulo'] = strtoupper(trim($data['modulo'] ?? '')) ?: null;
        $data['activo'] = $request->boolean('activo', true);

        Permiso::create($data);
        $this->bitacora->registrarActual('seg_permiso_creado', $request);

        return redirect()->route('seg.permisos.index')->with('success', 'Permiso creado correctamente.');
    }

    public function edit(Permiso $permiso): View
    {
        return view('seg.permisos.edit', compact('permiso'));
    }

    public function update(Request $request, Permiso $permiso): RedirectResponse
    {
        $data = $this->validatedData($request, $permiso);
        $data['codigo'] = strtolower(trim($data['codigo']));
        $data['modulo'] = strtoupper(trim($data['modulo'] ?? '')) ?: null;
        $data['activo'] = $request->boolean('activo');

        $permiso->update($data);
        $this->bitacora->registrarActual('seg_permiso_actualizado', $request);

        return redirect()->route('seg.permisos.index')->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroy(Request $request, Permiso $permiso): RedirectResponse
    {
        if ($permiso->roles()->exists() || $permiso->usuariosDirectos()->exists()) {
            return back()->with('error', 'No se puede eliminar un permiso asignado a roles o usuarios. Puedes desactivarlo.');
        }

        $permiso->update(['activo' => false]);
        $permiso->delete();
        $this->bitacora->registrarActual('seg_permiso_eliminado', $request);

        return back()->with('success', 'Permiso eliminado correctamente.');
    }

    private function validatedData(Request $request, ?Permiso $permiso = null): array
    {
        return $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(\.[a-z0-9]+)*$/',
                Rule::unique('seg_permisos', 'codigo')
                    ->ignore($permiso?->id_permiso, 'id_permiso')
                    ->whereNull('deleted_at'),
            ],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'modulo' => ['nullable', 'string', 'max:50'],
            'activo' => ['nullable', 'boolean'],
        ], [
            'codigo.regex' => 'El código debe usar el formato modulo.recurso.accion, por ejemplo: seg.roles.gestionar.',
        ]);
    }
}
