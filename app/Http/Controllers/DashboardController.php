<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard'); // View untuk admin
    }

    public function spv()
    {
        return view('spv.dashboard'); // View untuk supervisor
    }

    public function karyawan()
    {
        return view('karyawan.dashboard'); // View untuk karyawan
    }
}
