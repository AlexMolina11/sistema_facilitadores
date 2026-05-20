<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use Illuminate\Http\Request;

class ConsultorFormacionController extends Controller
{
    public function edit(Consultor $consultor)
    {
        return view('fac.consultores.formacion', compact('consultor'));
    }

    public function update(Request $request, Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.experiencia.edit', $consultor)
            ->with('success', 'Formación guardada correctamente. Continúa con experiencia.');
    }
}