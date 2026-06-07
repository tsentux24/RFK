<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class administratorController extends Controller
{
    public function index(){
        return view('superadmin');
    }
}
