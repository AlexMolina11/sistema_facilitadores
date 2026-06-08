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
use App\Modules\Fac\Controllers\Catalogo\SexoController;
use App\Modules\Fac\Controllers\BusquedaAvanzadaController;

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:fac.dashboard.ver')
        ->name('fac.dashboard');

    Route::resource('consultores', ConsultorController::class)
        ->middleware('permission:fac.consultores.ver,fac.consultores.gestionar')
        ->parameters([
            'consultores' => 'consultor',
        ])
        ->names('fac.consultores');

    
    Route::get( 'busqueda-avanzada',  [BusquedaAvanzadaController::class, 'index'])->name('fac.consultores.busqueda-avanzada');

    Route::prefix('consultores/{consultor}')
        ->name('fac.consultores.')
        ->middleware('consultor.owner:fac.consultores.gestionar')
        ->group(function () {

            Route::get('contacto', [ConsultorContactoController::class, 'edit'])
                ->name('contacto.edit');

            Route::post('contacto/emails', [ConsultorContactoController::class, 'storeEmail'])->name('contacto.emails.store');
            Route::put('contacto/emails/{email}', [ConsultorContactoController::class, 'updateEmail'])->name('contacto.emails.update');
            Route::delete('contacto/emails/{email}', [ConsultorContactoController::class, 'destroyEmail'])->name('contacto.emails.destroy');

            Route::post('contacto/telefonos', [ConsultorContactoController::class, 'storeTelefono'])->name('contacto.telefonos.store');
            Route::put('contacto/telefonos/{telefono}', [ConsultorContactoController::class, 'updateTelefono'])->name('contacto.telefonos.update');
            Route::delete('contacto/telefonos/{telefono}', [ConsultorContactoController::class, 'destroyTelefono'])->name('contacto.telefonos.destroy');

            Route::post('contacto/redes', [ConsultorContactoController::class, 'storeRed'])->name('contacto.redes.store');
            Route::put('contacto/redes/{red}', [ConsultorContactoController::class, 'updateRed'])->name('contacto.redes.update');
            Route::delete('contacto/redes/{red}', [ConsultorContactoController::class, 'destroyRed'])->name('contacto.redes.destroy');

            Route::post('contacto/emergencias', [ConsultorContactoController::class, 'storeEmergencia'])->name('contacto.emergencias.store');
            Route::put('contacto/emergencias/{emergencia}', [ConsultorContactoController::class, 'updateEmergencia'])->name('contacto.emergencias.update');
            Route::delete('contacto/emergencias/{emergencia}', [ConsultorContactoController::class, 'destroyEmergencia'])->name('contacto.emergencias.destroy');

            Route::post('contacto/continuar', [ConsultorContactoController::class, 'continuar'])->name('contacto.continuar');

            Route::get('formacion', [ConsultorFormacionController::class, 'edit'])->name('formacion.edit');
            Route::post('formacion/atestados', [ConsultorFormacionController::class, 'store'])->name('formacion.store');
            Route::put('formacion/atestados/{formacion}', [ConsultorFormacionController::class, 'updateAtestado'])->name('formacion.atestados.update');
            Route::delete('formacion/atestados/{formacion}', [ConsultorFormacionController::class, 'destroyAtestado'])->name('formacion.atestados.destroy');
            Route::post('formacion/continuar', [ConsultorFormacionController::class, 'continuar'])->name('formacion.continuar');

            Route::get('experiencia', [ConsultorExperienciaController::class, 'editExperiencia'])->name('experiencia.edit');
            Route::post('experiencia/laboral', [ConsultorExperienciaController::class, 'storeExperiencia'])->name('experiencia.laboral.store');
            Route::put('experiencia/laboral/{experiencia}', [ConsultorExperienciaController::class, 'updateExperiencia'])->name('experiencia.laboral.update');
            Route::delete('experiencia/laboral/{experiencia}', [ConsultorExperienciaController::class, 'destroyExperiencia'])->name('experiencia.laboral.destroy');
            Route::post('experiencia/continuar', [ConsultorExperienciaController::class, 'continuar'])->name('experiencia.continuar');

            Route::get('habilidades', [ConsultorExperienciaController::class, 'editHabilidades'])->name('habilidades.edit');
            Route::post('habilidades', [ConsultorExperienciaController::class, 'updateCompetencias'])->name('habilidades.update');
            Route::post('habilidades/continuar', [ConsultorExperienciaController::class, 'continuarHabilidades'])->name('habilidades.continuar');

            Route::get('idiomas', [ConsultorExperienciaController::class, 'editIdiomas'])->name('idiomas.edit');
            Route::post('idiomas', [ConsultorExperienciaController::class, 'storeIdioma'])->name('idiomas.store');
            Route::put('idiomas/{idioma}', [ConsultorExperienciaController::class, 'updateIdioma'])->name('idiomas.update');
            Route::delete('idiomas/{idioma}', [ConsultorExperienciaController::class, 'destroyIdioma'])->name('idiomas.destroy');
            Route::post('idiomas/continuar', [ConsultorExperienciaController::class, 'continuarIdiomas'])->name('idiomas.continuar');

            Route::get('referencias', [ConsultorExperienciaController::class, 'editReferencias'])->name('referencias.edit');
            Route::post('referencias', [ConsultorExperienciaController::class, 'storeReferencia'])->name('referencias.store');
            Route::put('referencias/{referencia}', [ConsultorExperienciaController::class, 'updateReferencia'])->name('referencias.update');
            Route::delete('referencias/{referencia}', [ConsultorExperienciaController::class, 'destroyReferencia'])->name('referencias.destroy');
            Route::post('referencias/continuar', [ConsultorExperienciaController::class, 'continuarReferencias'])->name('referencias.continuar');

            Route::get('disponibilidad', [ConsultorExperienciaController::class, 'editDisponibilidad'])->name('disponibilidad.edit');
            Route::post('disponibilidad', [ConsultorExperienciaController::class, 'updateDisponibilidad'])->name('disponibilidad.update');
            Route::post('disponibilidad/continuar', [ConsultorExperienciaController::class, 'continuarDisponibilidad'])->name('disponibilidad.continuar');

            Route::get('documentos', [ConsultorDocumentoController::class, 'edit'])->name('documentos.edit');
            Route::post('documentos', [ConsultorDocumentoController::class, 'update'])->name('documentos.update');

        });

        
    Route::prefix('catalogos')
        ->name('fac.catalogos.')
        ->middleware('permission:fac.catalogos.gestionar')
        ->group(function () {

            Route::get('municipios/departamentos-por-pais', [MunicipioController::class, 'departamentosPorPais'])
                ->name('municipios.departamentos_por_pais');

            Route::get('municipios/municipios-mh-por-departamento', [MunicipioController::class, 'municipiosMhPorDepartamento'])
                ->name('municipios.municipios_mh_por_departamento');

            Route::resource('idiomas', IdiomaController::class);

            Route::resource('tipo-referencia', TipoReferenciaController::class)
                ->parameters(['tipo-referencia' => 'tipoReferencia']);

            Route::resource('tipo-formacion', TipoFormacionController::class)
                ->parameters(['tipo-formacion' => 'tipoFormacion']);

            Route::resource('tipo-atestado', TipoAtestadoController::class)
                ->parameters(['tipo-atestado' => 'tipoAtestado']);

            Route::resource('tipo-red-social', TipoRedSocialController::class)
                ->parameters(['tipo-red-social' => 'tipoRedSocial']);

            Route::resource('tipo-habilidad', TipoHabilidadController::class)
                ->parameters(['tipo-habilidad' => 'tipoHabilidad']);

            Route::resource('habilidad', HabilidadController::class)
                ->parameters(['habilidad' => 'habilidad']);

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
            Route::resource('sexo', SexoController::class);
        });
});