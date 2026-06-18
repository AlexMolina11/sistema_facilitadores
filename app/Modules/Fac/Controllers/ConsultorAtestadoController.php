<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorAtestado;
use App\Modules\Fac\Requests\StoreConsultorAtestadoRequest;
use App\Modules\Fac\Requests\UpdateConsultorAtestadoRequest;
use Illuminate\Support\Facades\Storage;

class ConsultorAtestadoController extends Controller
{
    public function store(StoreConsultorAtestadoRequest $request, Consultor $consultor)
    {
        $data = $request->validated();
        $archivo = $request->file('archivo_atestado');

        if ($archivo) {
            $data['url_archivo'] = $archivo->store("consultores/{$consultor->id_consultor}/atestados", 'public');
            $data['nombre_archivo_original'] = $archivo->getClientOriginalName();
        }

        unset($data['archivo_atestado']);

        ConsultorAtestado::create(array_merge($data, [
            'id_consultor' => $consultor->id_consultor,
            'activo' => true,
            'usuario_crea' => auth()->id(),
        ]));

        return back()->with('success', 'Registro de trayectoria agregado correctamente.');
    }

    public function update(UpdateConsultorAtestadoRequest $request, Consultor $consultor, ConsultorAtestado $atestado)
    {
        $this->validarPertenencia($consultor, $atestado);

        $data = $request->validated();
        $archivo = $request->file('archivo_atestado');

        if ($archivo) {
            if ($atestado->url_archivo && Storage::disk('public')->exists($atestado->url_archivo)) {
                Storage::disk('public')->delete($atestado->url_archivo);
            }

            $data['url_archivo'] = $archivo->store("consultores/{$consultor->id_consultor}/atestados", 'public');
            $data['nombre_archivo_original'] = $archivo->getClientOriginalName();
        }

        unset($data['archivo_atestado']);

        $atestado->update(array_merge($data, [
            'usuario_mod' => auth()->id(),
        ]));

        return back()->with('success', 'Registro de trayectoria actualizado correctamente.');
    }

    public function destroy(Consultor $consultor, ConsultorAtestado $atestado)
    {
        $this->validarPertenencia($consultor, $atestado);

        if ($atestado->url_archivo && Storage::disk('public')->exists($atestado->url_archivo)) {
            Storage::disk('public')->delete($atestado->url_archivo);
        }

        $atestado->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $atestado->delete();

        return back()->with('success', 'Registro de trayectoria eliminado correctamente.');
    }

    private function validarPertenencia(Consultor $consultor, ConsultorAtestado $atestado): void
    {
        if ((int) $atestado->id_consultor !== (int) $consultor->id_consultor) {
            abort(403, 'Este registro no pertenece al consultor seleccionado.');
        }
    }
}
