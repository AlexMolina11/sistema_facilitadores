<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(
    'saf:procesar-importaciones',
    [
        '--limite-instructores' => 1000,
        '--limite-capacitaciones' => 5000,
    ]
)
    ->dailyAt('05:00')
    ->timezone('America/El_Salvador')
    ->withoutOverlapping(120);
