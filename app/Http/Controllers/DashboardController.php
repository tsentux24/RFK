<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil role dari user login
        $role = Auth::user()->role;

        return match ($role) {
            'superadmin' => view('dashboard.superadmin'),
            'administrator' => view('dashboard.administrator'),
            'staff' => view('dashboard.staff'),
            'kepala_opd' => view('dashboard.kepala_opd'),
            default => redirect('/'),
        };
    }
}
