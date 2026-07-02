<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\CvPlantilla;
use App\Modules\Fac\Requests\StoreCvPlantillaRequest;
use App\Modules\Fac\Requests\UpdateCvPlantillaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\View;

class CvPlantillaController extends Controller
{
    public function index()
    {
        $plantillas = CvPlantilla::query()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(15);

        return view('fac.cv.plantillas.index', compact('plantillas'));
    }

    public function create()
    {
        return view('fac.cv.plantillas.create', [
            'plantilla' => new CvPlantilla([
                'tamanio_papel' => 'letter',
                'orientacion' => 'portrait',
                'orden' => 1,
                'activa' => true,
            ]),
        ]);
    }

    public function store(StoreCvPlantillaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['vista_blade'] = 'fac.cv.pdf.' . $data['codigo'];
        $data['activo'] = true;

        CvPlantilla::create($data);

        return redirect()
            ->route('fac.catalogos.cv-plantillas.index')
            ->with('success', 'Plantilla de CV creada correctamente.');
    }

    public function edit(CvPlantilla $cvPlantilla)
    {
        return view('fac.cv.plantillas.edit', [
            'plantilla' => $cvPlantilla,
        ]);
    }

    public function update(UpdateCvPlantillaRequest $request, CvPlantilla $cvPlantilla): RedirectResponse
    {
        $data = $request->validated();
        $data['vista_blade'] = 'fac.cv.pdf.' . $data['codigo'];

        $cvPlantilla->update($data);

        return redirect()
            ->route('fac.catalogos.cv-plantillas.index')
            ->with('success', 'Plantilla de CV actualizada correctamente.');
    }

    public function destroy(CvPlantilla $cvPlantilla): RedirectResponse
    {
        $cvPlantilla->update([
            'activo' => false,
            'activa' => false,
        ]);

        $cvPlantilla->delete();

        return redirect()
            ->route('fac.catalogos.cv-plantillas.index')
            ->with('success', 'Plantilla de CV eliminada correctamente.');
    }

    public function toggle(CvPlantilla $cvPlantilla): RedirectResponse
    {
        $cvPlantilla->update([
            'activa' => ! $cvPlantilla->activa,
        ]);

        return redirect()
            ->route('fac.catalogos.cv-plantillas.index')
            ->with('success', 'Estado de la plantilla actualizado correctamente.');
    }

    public function verificarVista(CvPlantilla $cvPlantilla): RedirectResponse
    {
        if (View::exists($cvPlantilla->vista_blade)) {
            return redirect()
                ->route('fac.catalogos.cv-plantillas.index')
                ->with('success', "La vista {$cvPlantilla->vista_blade} existe.");
        }

        return redirect()
            ->route('fac.catalogos.cv-plantillas.index')
            ->with('error', "No existe la vista {$cvPlantilla->vista_blade}.");
    }
}