<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKaryawan;
use App\Models\User;
use App\Models\Shift;
use App\Models\Absensi;
use App\Models\Lembur;

class InputJadwalKaryawanController extends Controller
{
    public function index()
    {
        $input_jadwals = JadwalKaryawan::all();
        return view('input-jadwal_view.index', compact('input_jadwals'));
    }

    public function create()
    {
        $users = User::all();
        $shifts = Shift::all();
        return view('input-jadwal_view.create', compact('users', 'shifts'));
    }
}
