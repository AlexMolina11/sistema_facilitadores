<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;

class BusquedaAvanzadaController extends Controller
{
    public function index()
    {
        $consultores = Consultor::paginate(9);

        return view(
            'fac.consultores.busqueda-avanzada.index',
            compact('consultores')
        );
    }
}