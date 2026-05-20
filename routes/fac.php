<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fac\Controllers\DashboardController;
use App\Modules\Fac\Controllers\Catalogo\IdiomaController;
use App\Modules\Fac\Controllers\Catalogo\PaisController;
use App\Modules\Fac\Controllers\Catalogo\DepartamentoController;
use App\Modules\Fac\Controllers\Catalogo\MunicipioMhController;
use App\Modules\Fac\Controllers\Catalogo\MunicipioController;
use App\Modules\Fac\Controllers\Catalogo\TipoTelefonoController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('fac.dashboard');

Route::prefix('catalogos')->name('fac.catalogos.')->group(function () {

    Route::get('municipios/departamentos-por-pais',
        [MunicipioController::class, 'departamentosPorPais'])
        ->name('municipios.departamentos_por_pais');

    Route::get('municipios/municipios-mh-por-departamento',
        [MunicipioController::class, 'municipiosMhPorDepartamento'])
        ->name('municipios.municipios_mh_por_departamento');

    Route::resource('idiomas', IdiomaController::class);
    Route::resource('paises', PaisController::class);
    Route::resource('departamentos', DepartamentoController::class);
    Route::resource('municipios_mh', MunicipioMhController::class);
    Route::resource('municipios', MunicipioController::class);
    Route::resource('tipo_telefono', TipoTelefonoController::class);
});