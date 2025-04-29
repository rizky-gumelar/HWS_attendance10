<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->poin_terakhir = $user->hitungPoin();
        $user->sisa_cuti = $user->hitungCuti();

        return view('dashboard.karyawan', ['karyawan' => $user]);
    }
}
