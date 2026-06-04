<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Seg\Controllers\AuthController;
use App\Modules\Seg\Controllers\UsuarioController;
use App\Modules\Seg\Controllers\BitacoraAccesoController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
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

        Route::get('bitacora', [BitacoraAccesoController::class, 'index'])
            ->middleware('permission:seg.bitacora.ver')
            ->name('bitacora.index');
    });