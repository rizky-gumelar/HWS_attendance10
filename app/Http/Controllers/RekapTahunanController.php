<?php

namespace App\Http\Controllers;

use App\Models\RekapTahunan;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PengajuanCuti;
use Carbon\Carbon;

class RekapTahunanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year); // Tahun yang dipilih, default tahun sekarang

        $rekap = RekapTahunan::where('tahun', $tahun)
            ->with('user') // Mengambil relasi user untuk menampilkan nama
            ->get();

        return view('rekap_tahunan_view.index', compact('rekap'));
    }

    public function generateRekapTahunan($tahun)
    {
        $users = User::where('role', '!=', 'admin')->get();

        foreach ($users as $user) {
            $userId = $user->id;

            // Ambil semua pengajuan cuti user ini pada tahun tersebut
            $cutis = PengajuanCuti::where('user_id', $userId)
                ->where('status', 'disetujui admin')
                ->whereYear('tanggal', $tahun)
                ->with('jenis_cuti')
                ->get();

            // 1. Cuti (menjumlahkan semua status 1 dan 0.5, anggap sebagai bobot cuti)
            $totalBobotCuti = $cutis->sum(function ($cuti) {
                $status = $cuti->jenis_cuti->status;
                return in_array($status, [1, 0.5]) ? $status : 0;
            });

            // 2. CF (status 0, tapi bukan cuti sakit)
            $totalCF = $cutis->sum(function ($cuti) {
                return ($cuti->jenis_cuti->status == 0 && $cuti->jenis_cuti->nama_cuti != 'Sakit') ? 1 : 0;
            });

            // 3. Sakit (status 0 dan nama cuti = 'Sakit')
            $totalSakit = $cutis->sum(function ($cuti) {
                return ($cuti->jenis_cuti->status == 0 && $cuti->jenis_cuti->nama_cuti == 'Sakit') ? 1 : 0;
            });

            // 4. Setengah Hari (status 0.5)
            $totalSetengahHari = $cutis->sum(function ($cuti) {
                return $cuti->jenis_cuti->status == 0.5 ? 1 : 0;
            });

            // 5. Saldo Cuti & Poin Ketidakhadiran
            $saldoCuti = $user->total_cuti;
            $poinKetidakhadiran = $user->poin_tidak_hadir;

            // Simpan ke database
            RekapTahunan::updateOrCreate(
                ['user_id' => $userId, 'tahun' => $tahun],
                [
                    'cuti' => $totalBobotCuti,
                    'cf' => $totalCF,
                    'sakit' => $totalSakit,
                    'setengah_hari' => $totalSetengahHari,
                    'saldo_cuti' => $saldoCuti,
                    'poin_ketidakhadiran' => $poinKetidakhadiran,
                ]
            );
        }

        return redirect()->route('rekap_tahunan.index', ['tahun' => $tahun])
            ->with('success', 'Rekap tahunan berhasil diperbarui.');
    }



    // Helper function untuk mengambil pengajuan cuti yang relevan
    private function pengajuan_cuti()
    {
        return PengajuanCuti::query();
    }
}
