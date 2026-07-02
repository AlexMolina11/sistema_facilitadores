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
        $siguienteOrden = CvPlantilla::withTrashed()->max('orden') + 1;

        return view('fac.cv.plantillas.create', [
            'plantilla' => new CvPlantilla([
                'tamanio_papel' => 'letter',
                'orientacion' => 'portrait',
                'orden' => $siguienteOrden ?: 1,
                'activa' => false,
            ]),
        ]);
    }

    public function store(StoreCvPlantillaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['vista_blade'] = 'fac.cv.pdf.' . $data['codigo'];
        $data['vista_verificada'] = View::exists($data['vista_blade']);
        $data['fecha_verificacion'] = $data['vista_verificada'] ? now() : null;

        $data['orden'] = CvPlantilla::withTrashed()->max('orden') + 1;
        $data['activa'] = false;
        $data['activo'] = true;

        CvPlantilla::create($data);

        return redirect()
            ->route('fac.catalogos.cv-plantillas.index')
            ->with(
                $data['vista_verificada'] ? 'success' : 'warning',
                $data['vista_verificada']
                    ? 'Plantilla creada correctamente. Puede activarse cuando lo necesite.'
                    : 'Plantilla creada, pero queda inactiva porque aún no existe o no se ha verificado su vista Blade.'
            );
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

        $vistaExiste = View::exists($data['vista_blade']);

        if ($cvPlantilla->vista_blade !== $data['vista_blade']) {
            $data['vista_verificada'] = $vistaExiste;
            $data['fecha_verificacion'] = $vistaExiste ? now() : null;

            if (! $vistaExiste) {
                $data['activa'] = false;
            }
        }

        if (($data['activa'] ?? false) && (! $vistaExiste || ! ($data['vista_verificada'] ?? $cvPlantilla->vista_verificada))) {
            $data['activa'] = false;
        }

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
        if (! $cvPlantilla->activa) {
            if (! View::exists($cvPlantilla->vista_blade)) {
                $cvPlantilla->update([
                    'activa' => false,
                    'vista_verificada' => false,
                    'fecha_verificacion' => null,
                ]);

                return redirect()
                    ->route('fac.catalogos.cv-plantillas.index')
                    ->with('error', 'No se puede activar la plantilla porque la vista Blade no existe.');
            }

            if (! $cvPlantilla->vista_verificada) {
                return redirect()
                    ->route('fac.catalogos.cv-plantillas.index')
                    ->with('error', 'Debe verificar la vista Blade antes de activar esta plantilla.');
            }
        }

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
            $cvPlantilla->update([
                'vista_verificada' => true,
                'fecha_verificacion' => now(),
            ]);

            return redirect()
                ->route('fac.catalogos.cv-plantillas.index')
                ->with('success', "La vista {$cvPlantilla->vista_blade} existe y fue verificada.");
        }

        $cvPlantilla->update([
            'activa' => false,
            'vista_verificada' => false,
            'fecha_verificacion' => null,
        ]);

        return redirect()
            ->route('fac.catalogos.cv-plantillas.index')
            ->with('error', "No existe la vista {$cvPlantilla->vista_blade}. La plantilla queda inactiva.");
    }
}