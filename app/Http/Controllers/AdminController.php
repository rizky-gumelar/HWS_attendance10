<?php

namespace App\Http\Controllers;

use App\Models\RekapTahunan;
use App\Models\Keuangan;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $divisiList = Divisi::all();
        return view('dashboard.admin', compact('divisiList'));
        // return view('dashboard.admin');
    }

    public function pengaturan()
    {
        $keuangan = Keuangan::first();
        return view('pengaturan_view.index', compact('keuangan'));
    }
}
