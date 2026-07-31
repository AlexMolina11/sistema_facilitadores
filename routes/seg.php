<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Seg\Controllers\AuthController;
use App\Modules\Seg\Controllers\UsuarioController;
use App\Modules\Seg\Controllers\RolController;
use App\Modules\Seg\Controllers\PermisoController;
use App\Modules\Seg\Controllers\BitacoraAccesoController;
use App\Modules\Seg\Controllers\BitacoraSafController;
use App\Modules\Seg\Controllers\PasswordResetController;

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');


    /*
    |--------------------------------------------------------------------------
    | Recuperación de contraseña
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/forgot-password',
        [PasswordResetController::class, 'showForgotPassword']
    )->name('password.request');


    Route::post(
        '/forgot-password',
        [PasswordResetController::class, 'sendResetLink']
    )->name('password.email');


    Route::get(
        '/reset-password/{token}',
        [PasswordResetController::class, 'showResetPassword']
    )->name('password.reset');


    Route::post(
        '/reset-password',
        [PasswordResetController::class, 'resetPassword']
    )->name('password.update');

});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('seg')
    ->name('seg.')
    ->middleware(['auth'])
    ->group(function () {

        Route::resource('usuarios', UsuarioController::class)
            ->middleware('permission:seg.usuarios.gestionar')
            ->parameters([
                'usuarios' => 'usuario',
            ]);



        Route::resource('roles', RolController::class)
            ->middleware('permission:seg.roles.gestionar')
            ->parameters([
                'roles' => 'role',
            ]);

        Route::resource('permisos', PermisoController::class)
            ->middleware('permission:seg.permisos.gestionar')
            ->parameters([
                'permisos' => 'permiso',
            ]);

        Route::get('bitacora', [BitacoraAccesoController::class, 'index'])
            ->middleware('permission:seg.bitacora.ver')
            ->name('bitacora.index');

        Route::get('bitacora-saf', [BitacoraSafController::class, 'index'])
            ->middleware('permission:seg.bitacora-saf.ver')
            ->name('bitacora-saf.index');

        Route::get('bitacora-saf/{sincronizacionSaf}', [BitacoraSafController::class, 'show'])
            ->middleware('permission:seg.bitacora-saf.ver')
            ->name('bitacora-saf.show');
    });