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
        // Ambil semua pengguna (user)
        $users = User::all();
        $rekapSemuaUser = [];
        foreach ($users as $user) {
            $userId = $user->id;

            // 1. Cuti
            $totalCuti = $this->pengajuan_cuti($userId, $tahun)
                ->where('status', 'disetujui admin')
                ->whereYear('tanggal', $tahun)
                ->whereHas('jenis_cuti', function ($query) {
                    $query->where('status', '1');
                })
                ->count();



            // 2. CF
            $totalCF = $this->pengajuan_cuti($userId, $tahun)
                ->where('status', 'disetujui admin')
                ->whereYear('tanggal', $tahun)
                ->whereHas('jenis_cuti', function ($query) {
                    $query->where('status', '0')
                        ->where('nama_cuti', '!=', 'Sakit');
                })
                ->count();


            // 3. Sakit
            $totalSakit = $this->pengajuan_cuti($userId, $tahun)
                ->where('status', 'disetujui admin')
                ->whereYear('tanggal', $tahun)
                ->whereHas('jenis_cuti', function ($query) {
                    $query->where('status', '0')
                        ->where('nama_cuti', 'Sakit');
                })
                ->count();


            // 4. Setengah Hari
            $totalSetengahHari = $this->pengajuan_cuti($userId, $tahun)
                ->where('status', 'disetujui admin')
                ->whereYear('tanggal', $tahun)
                ->whereHas('jenis_cuti', function ($query) {
                    $query->where('status', '0.5');
                })
                ->count() / 2;


            // 5. Saldo Cuti dan Poin Ketidakhadiran
            $saldoCuti = $user->total_cuti;  // Ambil saldo cuti dari relasi atau atribut lain
            $poinKetidakhadiran = $user->poin_tidak_hadir;  // Ambil poin ketidakhadiran

            $rekapSemuaUser[] = [
                'user_id' => $userId,
                'cuti' => $totalCuti,
                'cf' => $totalCF,
                'sakit' => $totalSakit,
                'setengah_hari' => $totalSetengahHari,
                'saldo_cuti' => $saldoCuti,
                'poin_ketidakhadiran' => $poinKetidakhadiran,
            ];

            // Simpan data ke tabel 'rekap_tahunan'
            RekapTahunan::updateOrCreate(
                ['user_id' => $userId, 'tahun' => $tahun],
                [
                    'cuti' => $totalCuti,
                    'cf' => $totalCF,
                    'sakit' => $totalSakit,
                    'setengah_hari' => $totalSetengahHari,
                    'saldo_cuti' => $saldoCuti,
                    'poin_ketidakhadiran' => $poinKetidakhadiran,
                ]
            );
        }
        // dd($rekapSemuaUser); // Dump semua data
        return redirect()->route('rekap_tahunan.index', ['tahun' => $tahun])
            ->with('success', 'Rekap tahunan berhasil diperbarui.');
    }

    // Helper function untuk mengambil pengajuan cuti yang relevan
    private function pengajuan_cuti($userId, $tahun)
    {
        return PengajuanCuti::where('user_id', $userId)->whereYear('tanggal', $tahun);
    }
}
