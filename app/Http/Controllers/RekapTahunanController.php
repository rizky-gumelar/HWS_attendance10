<?php

namespace App\Http\Controllers;

use App\Models\RekapTahunan;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PengajuanCuti;
use App\Models\JadwalKaryawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        $tahun = intval($tahun);
        // Ambil semua pengguna (user)
        $users = User::all();
        foreach ($users as $user) {
            $userId = (int)$user->id;

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


            $totalTerlambat = $this->jadwal_karyawan($userId, $tahun)->where('cek_keterlambatan', 1)
                ->whereYear('tanggal', $tahun)->count() / 2;  // Menghitung keterlambatan
            // 5. Saldo Cuti dan Poin Ketidakhadiran
            $saldoCuti = $user->total_cuti;  // Ambil saldo cuti dari relasi atau atribut lain
            $poinKetidakhadiran = $user->poin_tidak_hadir;  // Ambil poin ketidakhadiran

            Log::info('user_id:', ['type' => gettype($userId), 'value' => $userId]);
            Log::info('tahun:', ['type' => gettype($tahun), 'value' => $tahun]);
            // Simpan data ke tabel 'rekap_tahunan'
            RekapTahunan::updateOrCreate(
                ['user_id' => $userId, 'tahun' => $tahun],
                [
                    'cuti' => $totalCuti,
                    'cf' => $totalCF,
                    'sakit' => $totalSakit,
                    'setengah_hari' => $totalSetengahHari,
                    'terlambat' => $totalTerlambat,
                    'saldo_cuti' => $saldoCuti,
                    'poin_ketidakhadiran' => $poinKetidakhadiran,
                ]
            );
        }
        return redirect()->route('rekap_tahunan.index', ['tahun' => $tahun])
            ->with('success', 'Rekap tahunan berhasil diperbarui.');
    }

    // Helper function untuk mengambil pengajuan cuti yang relevan
    private function pengajuan_cuti($userId, $tahun)
    {
        return PengajuanCuti::where('user_id', $userId)->whereYear('tanggal', $tahun);
    }

    // Helper function untuk mengambil pengajuan cuti yang relevan
    private function jadwal_karyawan($userId, $tahun)
    {
        return JadwalKaryawan::where('user_id', $userId)->whereYear('tanggal', $tahun);
    }
}
