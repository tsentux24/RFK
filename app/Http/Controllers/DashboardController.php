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

        $opds = \App\Models\Opd::orderBy('nama_opd', 'asc')->get();

        return match ($role) {
            'superadmin' => view('dashboard.superadmin', compact('opds')),
            'administrator' => view('dashboard.administrator', compact('opds')),
            'staff' => view('dashboard.staff'),
            'kepala_opd' => view('dashboard.kepala_opd'),
            default => redirect('/'),
        };
    }

    public function panduan()
    {
        $role = Auth::user()->role;
        if ($role === 'staff') {
            return view('dashboard.panduan_staff');
        }
        if ($role === 'kepala_opd') {
            return view('dashboard.panduan_kepala_opd');
        }
        if ($role === 'superadmin') {
            return view('dashboard.panduan_superadmin');
        }
        return view('dashboard.panduan');
    }
}
