<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

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
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_SV.UTF-8', 'Spanish');
        
        $this->loadMigrationsFrom([
            database_path('migrations/seg'),
            database_path('migrations/fac'),
        ]);
    }
}