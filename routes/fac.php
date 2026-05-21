<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fac\Controllers\DashboardController;
use App\Modules\Fac\Controllers\Catalogo\IdiomaController;
use App\Modules\Fac\Controllers\ConsultorController;
use App\Modules\Fac\Controllers\ConsultorContactoController;
use App\Modules\Fac\Controllers\ConsultorFormacionController;
use App\Modules\Fac\Controllers\ConsultorExperienciaController;
use App\Modules\Fac\Controllers\ConsultorDocumentoController;

use App\Modules\Fac\Controllers\Catalogo\TipoReferenciaController;
use App\Modules\Fac\Controllers\Catalogo\TipoFormacionController;
use App\Modules\Fac\Controllers\Catalogo\TipoAtestadoController;
use App\Modules\Fac\Controllers\Catalogo\TipoRedSocialController;
use App\Modules\Fac\Controllers\Catalogo\TipoHabilidadController;
use App\Modules\Fac\Controllers\Catalogo\HabilidadController;
use App\Modules\Fac\Controllers\Catalogo\PaisController;
use App\Modules\Fac\Controllers\Catalogo\DepartamentoController;
use App\Modules\Fac\Controllers\Catalogo\MunicipioMhController;
use App\Modules\Fac\Controllers\Catalogo\MunicipioController;
use App\Modules\Fac\Controllers\Catalogo\TipoTelefonoController;
use App\Modules\Fac\Controllers\Catalogo\IdiomaNivelController;
use App\Modules\Fac\Controllers\Catalogo\NivelAcademicoController;
use App\Modules\Fac\Controllers\Catalogo\TipoDisponibilidadController;
use App\Modules\Fac\Controllers\Catalogo\TipoDocumentoController;
use App\Modules\Fac\Controllers\Catalogo\TipoConsultoriaController;

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

    Route::get('municipios/departamentos-por-pais',
        [MunicipioController::class, 'departamentosPorPais'])
        ->name('municipios.departamentos_por_pais');

    Route::get('municipios/municipios-mh-por-departamento',
        [MunicipioController::class, 'municipiosMhPorDepartamento'])
        ->name('municipios.municipios_mh_por_departamento');

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
     
    Route::resource('habilidad', HabilidadController::class)
    ->parameters([
        'habilidad' => 'habilidad',
    ]);
    Route::resource('paises', PaisController::class);
    Route::resource('departamentos', DepartamentoController::class);
    Route::resource('municipios_mh', MunicipioMhController::class);
    Route::resource('municipios', MunicipioController::class);
    Route::resource('tipo_telefono', TipoTelefonoController::class);
    Route::resource('idioma-nivel', IdiomaNivelController::class);
    Route::resource('nivel-academico', NivelAcademicoController::class);
    Route::resource('tipo-disponibilidad', TipoDisponibilidadController::class);
    Route::resource('tipo-documento', TipoDocumentoController::class);
    Route::resource('tipo-consultoria', TipoConsultoriaController::class);
});