<?php

namespace App\Modules\Fac\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\TipoAtestado;
use App\Modules\Fac\Models\NivelAcademico;
use App\Modules\Fac\Models\TipoFormacion;
use App\Modules\Fac\Models\Habilidad;
use App\Modules\Fac\Models\Sexo;
use App\Modules\Fac\Models\Pais;
use App\Modules\Fac\Models\Departamento;
use App\Modules\Fac\Models\MunicipioMh;
use App\Modules\Fac\Models\Municipio;
use App\Modules\Fac\Models\Idioma;
use App\Modules\Fac\Models\IdiomaNivel;
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

    $paises = Pais::where('activo', 1)
        ->orderBy('nombre_pais')
         ->get();

    $departamentos = Departamento::where('activo', 1)
        ->orderBy('nombre_departamento')
        ->get();

    $municipiosMh = MunicipioMh::where('activo', 1)
        ->orderBy('municipio_mh_nombre')
        ->get();

    $distritos = Municipio::where('activo', 1)
        ->orderBy('nombre_distrito')
        ->get();

    $idiomas = Idioma::where('activo', true)
        ->orderBy('nombre')
        ->get();

    $nivelesIdioma = IdiomaNivel::where('activo', true)
         ->orderBy('nombre')
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
            'paises',
            'departamentos',
            'municipiosMh',
            'distritos',
            'idiomas',
            'nivelesIdioma'


        )
    );
}

    public function departamentosPorPais(Request $request)
    {
        return Departamento::where(
                'id_pais',
                $request->id_pais
            )
            ->where('activo', true)
            ->orderBy('nombre_departamento')
            ->get();
    }

    public function municipiosPorDepartamento(Request $request)
    {
        return MunicipioMh::where(
                'id_departamento',
                $request->id_departamento
            )
            ->where('activo', true)
            ->orderBy('municipio_mh_nombre')
            ->get();
    }

    public function distritosPorMunicipio(Request $request)
    {
        return Municipio::where(
                'id_municipio_mh',
                $request->id_municipio_mh
            )
            ->where('activo', true)
            ->orderBy('nombre_distrito')
            ->get();
    }

    public function ubicacionPorDistrito(Request $request)
{
    $distrito = Municipio::with([
        'pais',
        'departamento',
        'municipioMh'
    ])
    ->find($request->id_municipio);

    return response()->json([
        'pais' => $distrito->id_pais,
        'departamento' => $distrito->id_departamento,
        'municipio' => $distrito->id_municipio_mh,
        'distrito' => $distrito->id_municipio,
    ]);
}
}
