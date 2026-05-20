<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fac\Controllers\DashboardController;
use App\Modules\Fac\Controllers\Catalogo\IdiomaController;
use App\Modules\Fac\Controllers\ConsultorController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('fac.dashboard');

Route::resource('consultores', ConsultorController::class)
    ->parameters([
        'consultores' => 'consultor',
    ])
    ->names('fac.consultores');

Route::prefix('catalogos')->name('fac.catalogos.')->group(function () {
    Route::resource('idiomas', IdiomaController::class);
});