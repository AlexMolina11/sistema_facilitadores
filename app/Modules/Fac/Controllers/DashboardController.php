<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('fac.dashboard');
    }
}