<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        App::setLocale('es');

        Carbon::setLocale('es');

        setlocale(
            LC_TIME,
            'es_ES.UTF-8',
            'es_SV.UTF-8',
            'Spanish'
        );

        /*
        |--------------------------------------------------------------------------
        | Directiva Blade para permisos
        |--------------------------------------------------------------------------
        |
        | Permite controlar elementos visuales utilizando:
        |
        | @permiso('fac.consultores.editar')
        |     ...
        | @endpermiso
        |
        */

        Blade::if('permiso', function (string $codigo): bool {
            return auth()->check()
                && auth()->user()->tienePermiso($codigo);
        });

        /*
        |--------------------------------------------------------------------------
        | Migraciones modulares
        |--------------------------------------------------------------------------
        */

        $this->loadMigrationsFrom([
            database_path('migrations/seg'),
            database_path('migrations/fac'),
        ]);
    }
}