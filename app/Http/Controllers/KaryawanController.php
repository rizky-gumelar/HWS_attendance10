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
        $tahun = Carbon::now()->year;
        $user = auth()->user();
        $user->poin_terakhir = $user->hitungPoin($tahun);
        $user->sisa_cuti = $user->hitungCuti($tahun);

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
