<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\Shift;
use App\Models\Divisi;

class LaporanMingguanController extends Controller
{
    public function index()
    {
        // $karyawans = User::where('status', 'aktif')->get();
        $karyawans = User::all();
        // $karyawans = User::orderBy('status', 'asc')->orderBy('id', 'asc')->get();
        return view('mingguan_view.index', compact('karyawans'));
    }
}
