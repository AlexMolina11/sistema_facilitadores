<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('fac.dashboard');
});

require __DIR__.'/fac.php';
require __DIR__.'/seg.php';