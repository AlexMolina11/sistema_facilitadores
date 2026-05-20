<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Requests\StoreConsultorRequest;
use App\Modules\Fac\Requests\UpdateConsultorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultorController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $estado = $request->get('estado');

        $consultores = Consultor::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('numero_identificacion', 'like', "%{$buscar}%")
                        ->orWhere('nit', 'like', "%{$buscar}%");
                });
            })
            ->when($estado !== null && $estado !== '', function ($query) use ($estado) {
                $query->where('activo', $estado);
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(10)
            ->withQueryString();

        return view('fac.consultores.index', compact('consultores', 'buscar', 'estado'));
    }

    public function create()
    {
        $catalogos = $this->catalogosFormulario();

        return view('fac.consultores.create', compact('catalogos'));
    }

    public function store(StoreConsultorRequest $request)
    {
        Consultor::create([
            ...$request->validated(),
            'vigente' => $request->boolean('vigente'),
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.consultores.index')
            ->with('success', 'Consultor creado correctamente.');
    }

    public function show(Consultor $consultor)
    {
        return view('fac.consultores.show', compact('consultor'));
    }

    public function edit(Consultor $consultor)
    {
        $catalogos = $this->catalogosFormulario();

        return view('fac.consultores.edit', compact('consultor', 'catalogos'));
    }

    public function update(UpdateConsultorRequest $request, Consultor $consultor)
    {
        $consultor->update([
            ...$request->validated(),
            'vigente' => $request->boolean('vigente'),
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.consultores.index')
            ->with('success', 'Consultor actualizado correctamente.');
    }

    public function destroy(Consultor $consultor)
    {
        $consultor->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $consultor->delete();

        return redirect()
            ->route('fac.consultores.index')
            ->with('success', 'Consultor eliminado correctamente.');
    }

    private function catalogosFormulario(): array
    {
        return [
            'paises' => DB::table('tbl_pais')
                ->where('activo', true)
                ->orderBy('nombre_pais')
                ->get(),

            'municipios' => DB::table('tbl_municipio')
                ->where('activo', true)
                ->orderBy('nombre_distrito')
                ->get(),
        ];
    }
}