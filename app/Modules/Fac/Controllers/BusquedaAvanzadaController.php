<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\TipoAtestado;
use App\Modules\Fac\Models\NivelAcademico;
use App\Modules\Fac\Models\TipoFormacion;
use App\Modules\Fac\Models\Habilidad;
use App\Modules\Fac\Models\Sexo;
use App\Modules\Fac\Models\Pais;
use App\Modules\Fac\Models\Departamento;
use Illuminate\Support\Collection;



class BusquedaAvanzadaController extends Controller
{
  public function index()
{
    $areaEspecializacion = Habilidad::where('id_tipo_habilidad', 1)
        ->where('Activo', true)
        ->orderBy('nombre')
        ->get();

    $habilidadesBlandas = Habilidad::where('activo', 1)
        ->where('id_tipo_habilidad', 2)
        ->orderBy('nombre')
        ->get();

    $habilidadesTecnicas = Habilidad::where('activo', 1)
        ->where('id_tipo_habilidad', 3)
        ->orderBy('nombre')
        ->get();

    $sexos = Sexo::where('activo', 1)
        ->orderBy('nombre')
        ->get();

    $pais = Pais::where('Activo', 1)
        ->orderBy('nombre_pais')
         ->get();

    $Departamentos = Departamento::where('Activo', 1)
        ->orderBy('nombre_pais')
        ->get();


    $tiposFormacion = TipoFormacion::where('activo', true)
        ->orderBy('nombre')
        ->get();

    $nivelesAcademicos = NivelAcademico::where('activo', true)
        ->orderBy('nombre')
        ->get();

    $tiposAtestado = TipoAtestado::where('activo', true)
        ->orderBy('nombre')
        ->get();

    $consultores = collect();

    return view(
        'fac.consultores.busqueda-avanzada.index',
        compact(
            'tiposFormacion',
            'nivelesAcademicos',
            'tiposAtestado',
            'consultores',
            'areaEspecializacion',
            'habilidadesBlandas',
            'habilidadesTecnicas',
            'sexos',
            'pais',
            'departamento';

        )
    );
}
}