<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Retourner une vue simple avec seulement les informations de base
        return view('dashboard', [
            'user' => $user
        ]);
    }
}