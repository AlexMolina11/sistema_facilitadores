<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use Illuminate\Http\Request;

class ConsultorExperienciaController extends Controller
{
    public function edit(Consultor $consultor)
    {
        return view('fac.consultores.experiencia', compact('consultor'));
    }

    public function update(Request $request, Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.documentos.edit', $consultor)
            ->with('success', 'Experiencia guardada correctamente. Continúa con documentos.');
    }
}