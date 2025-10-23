<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord.
     */
    public function index()
    {
        return view('layouts.index');
    }
}