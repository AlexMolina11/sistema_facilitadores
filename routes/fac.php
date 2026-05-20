<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fac\Controllers\DashboardController;
use App\Modules\Fac\Controllers\Catalogo\IdiomaController;
use App\Modules\Fac\Controllers\ConsultorController;
use App\Modules\Fac\Controllers\ConsultorContactoController;
use App\Modules\Fac\Controllers\ConsultorFormacionController;
use App\Modules\Fac\Controllers\ConsultorExperienciaController;
use App\Modules\Fac\Controllers\ConsultorDocumentoController;


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('fac.dashboard');

Route::resource('consultores', ConsultorController::class)
    ->parameters([
        'consultores' => 'consultor',
    ])
    ->names('fac.consultores');

Route::prefix('consultores/{consultor}')
    ->name('fac.consultores.')
    ->group(function () {

        Route::get('contacto', [ConsultorContactoController::class, 'edit'])
            ->name('contacto.edit');

        Route::post('contacto', [ConsultorContactoController::class, 'update'])
            ->name('contacto.update');

        Route::get('formacion', [ConsultorFormacionController::class, 'edit'])
            ->name('formacion.edit');

        Route::post('formacion', [ConsultorFormacionController::class, 'update'])
            ->name('formacion.update');

        Route::get('experiencia', [ConsultorExperienciaController::class, 'edit'])
            ->name('experiencia.edit');

        Route::post('experiencia', [ConsultorExperienciaController::class, 'update'])
            ->name('experiencia.update');

        Route::get('documentos', [ConsultorDocumentoController::class, 'edit'])
            ->name('documentos.edit');

        Route::post('documentos', [ConsultorDocumentoController::class, 'update'])
            ->name('documentos.update');
});

Route::prefix('catalogos')->name('fac.catalogos.')->group(function () {
    Route::resource('idiomas', IdiomaController::class);
});