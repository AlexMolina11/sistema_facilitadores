<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fac\Controllers\DashboardController;
use App\Modules\Fac\Controllers\Catalogo\IdiomaController;
use App\Modules\Fac\Controllers\Catalogo\IdiomaNivelController;
use App\Modules\Fac\Controllers\Catalogo\NivelAcademicoController;
use App\Modules\Fac\Controllers\Catalogo\TipoDisponibilidadController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('fac.dashboard');

Route::prefix('catalogos')->name('fac.catalogos.')->group(function () {
    Route::resource('idiomas', IdiomaController::class);
    Route::resource('idioma-nivel', IdiomaNivelController::class);
    Route::resource('nivel-academico', NivelAcademicoController::class);
    Route::resource('tipo-disponibilidad', TipoDisponibilidadController::class);
});