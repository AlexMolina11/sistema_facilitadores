<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fac\Controllers\DashboardController;
use App\Modules\Fac\Controllers\Catalogo\IdiomaController;
use App\Modules\Fac\Controllers\Catalogo\TipoReferenciaController;
use App\Modules\Fac\Controllers\Catalogo\TipoFormacionController;

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
});