<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use Illuminate\Http\Request;

class ConsultorDocumentoController extends Controller
{
    public function edit(Consultor $consultor)
    {
        return view('fac.consultores.documentos', compact('consultor'));
    }

    public function update(Request $request, Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.show', $consultor)
            ->with('success', 'Expediente del consultor completado correctamente.');
    }
}