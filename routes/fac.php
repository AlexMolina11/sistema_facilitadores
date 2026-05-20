<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fac\Controllers\DashboardController;
use App\Modules\Fac\Controllers\Catalogo\IdiomaController;
use App\Modules\Fac\Controllers\Catalogo\TipoReferenciaController;
use App\Modules\Fac\Controllers\Catalogo\TipoFormacionController;
use App\Modules\Fac\Controllers\Catalogo\TipoAtestadoController;
use App\Modules\Fac\Controllers\Catalogo\TipoRedSocialController;
use App\Modules\Fac\Controllers\Catalogo\TipoHabilidadController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('fac.dashboard');

Route::prefix('catalogos')->name('fac.catalogos.')->group(function () {
    Route::resource('idiomas', IdiomaController::class);
    Route::resource('tipo-referencia', TipoReferenciaController::class)
    ->parameters([
        'tipo-referencia' => 'tipoReferencia',
    ]);

    Route::resource('tipo-formacion', TipoFormacionController::class)
    ->parameters([
        'tipo-formacion' => 'tipoFormacion',
    ]);

    Route::resource('tipo-atestado', TipoAtestadoController::class)
    ->parameters([
        'tipo-atestado' => 'tipoAtestado',
    ]);

    Route::resource('tipo-red-social', TipoRedSocialController::class)
    ->parameters([
        'tipo-red-social' => 'tipoRedSocial',
    ]);

    Route::resource('tipo-habilidad', TipoHabilidadController::class)
    ->parameters([
        'tipo-habilidad' => 'tipoHabilidad',
    ]);   
});