<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JadwalKaryawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->poin_terakhir = $user->hitungPoin();
        $user->sisa_cuti = $user->hitungCuti();
        $tahun = Carbon::now()->year;

        $telats = JadwalKaryawan::with(['users', 'shift', 'absensi', 'lembur'])
            ->where('user_id', $user->id)
            ->whereYear('tanggal', $tahun)
            ->where('cek_keterlambatan', 1)
            ->get();

        return view('dashboard.karyawan', [
            'karyawan' => $user,
            'telats' => $telats
        ]);
    }
}
