<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin'); // View untuk admin
    }

    public function spv()
    {
        return view('dashboard.spv'); // View untuk supervisor
    }

    public function karyawan()
    {
        return view('dashboard.karyawan'); // View untuk karyawan
    }
}
