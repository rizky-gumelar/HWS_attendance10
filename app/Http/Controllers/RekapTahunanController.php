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

            // 1. Cuti
            $totalBobotCuti = $this->pengajuan_cuti()
                ->where('status', 'disetujui admin')
                ->whereYear('tanggal', $tahun)
                ->with('jenis_cuti')
                ->get()
                ->sum(function ($cuti) {
                    $status = $cuti->jenis_cuti->status;

                    if ($status == 0.5) {
                        return 0;
                    }
                    return 0;
                });

            // 2. CF
            $totalCF = $this->pengajuan_cuti()
                ->where('status', 'disetujui admin')
                ->whereYear('tanggal', $tahun)
                ->with('jenis_cuti')
                ->get()
                ->sum(function ($cuti) {
                    $status = $cuti->jenis_cuti->status;

                    if ($status == 1 || $status == 0.5) {
                        return 0;
                    } elseif ($status == 0 && $cuti->jenis_cuti->nama_cuti != 'Sakit') {
                        return 1;
                    }

                    return 0;
                });

            // 3. Sakit
            $totalSakit = $this->pengajuan_cuti()
                ->where('status', 'disetujui admin')
                ->whereYear('tanggal', $tahun)
                ->with('jenis_cuti')
                ->get()
                ->sum(function ($cuti) {
                    $status = $cuti->jenis_cuti->status;

                    if ($status == 1 || $status == 0.5) {
                        return 0;
                    } elseif ($status == 0 && $cuti->jenis_cuti->nama_cuti == 'Sakit') {
                        return 1;
                    }

                    return 0;
                });

            // 4. Setengah Hari
            $totalSetengahHari = $this->pengajuan_cuti()
                ->where('status', 'disetujui admin')
                ->whereYear('tanggal', $tahun)
                ->with('jenis_cuti')
                ->get()
                ->sum(function ($cuti) {
                    $status = $cuti->jenis_cuti->status;

                    if ($status == 1) {
                        return 0;
                    } elseif ($status == 0.5) {
                        return 1;
                    } elseif ($status == 0) {
                        return 0;
                    }

                    return 0;
                });

            // 5. Saldo Cuti dan Poin Ketidakhadiran
            $saldoCuti = $user->total_cuti;
            $poinKetidakhadiran = $user->poin_tidak_hadir;

            // Simpan data ke tabel 'rekap_tahunan'
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
