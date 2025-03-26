<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\Shift;
use App\Models\Divisi;
use App\Models\LaporanMingguan;
use App\Models\JadwalKaryawan;
use Carbon\Carbon;

class LaporanMingguanController extends Controller
{
    public function index()
    {
        // $karyawans = User::where('status', 'aktif')->get();
        $mingguans = LaporanMingguan::all();
        $karyawans = User::all();
        // $mingguan = User::orderBy('status', 'asc')->orderBy('id', 'asc')->get();
        return view('mingguan_view.index', compact('mingguans', 'karyawans'));
    }

    // Fungsi untuk membuat laporan mingguan untuk seluruh user
    public function buatLaporanMingguan(Request $request)
    {
        // Validasi input untuk minggu_ke
        $request->validate([
            'minggu_ke' => 'required|integer'
        ]);

        // Ambil seluruh jadwal karyawan untuk minggu_ke tertentu
        $jadwalKaryawan = JadwalKaryawan::where('minggu_ke', $request->minggu_ke)
            ->orderBy('user_id') // Mengurutkan berdasarkan user_id
            ->get();

        // Jika tidak ada data jadwal, return error
        if ($jadwalKaryawan->isEmpty()) {
            return response()->json(['message' => 'Data jadwal karyawan tidak ditemukan untuk minggu ini.'], 404);
        }

        // Loop untuk setiap user yang ada dalam minggu_ke tersebut
        $laporanMingguans = [];
        $userIds = $jadwalKaryawan->pluck('user_id')->unique(); // Ambil seluruh user_id yang unik

        foreach ($userIds as $userId) {
            // Ambil data jadwal karyawan untuk masing-masing user
            $userJadwal = $jadwalKaryawan->where('user_id', $userId);

            // Menyusun laporan mingguan untuk user ini
            $laporanMingguan = new LaporanMingguan();
            $laporanMingguan->user_id = $userId;
            $laporanMingguan->minggu_ke = $request->minggu_ke;

            // D1-D7 diambil berdasarkan hari Sabtu hingga Jumat
            $laporanMingguan->D1 = $userJadwal->where('tanggal', 'Saturday')->first()->shift_id ?? 'N/A';
            $laporanMingguan->D2 = $userJadwal->where('tanggal', 'Sunday')->first()->shift_id ?? 'N/A';
            $laporanMingguan->D3 = $userJadwal->where('tanggal', 'Monday')->first()->shift_id ?? 'N/A';
            $laporanMingguan->D4 = $userJadwal->where('tanggal', 'Tuesday')->first()->shift_id ?? 'N/A';
            $laporanMingguan->D5 = $userJadwal->where('tanggal', 'Wednesday')->first()->shift_id ?? 'N/A';
            $laporanMingguan->D6 = $userJadwal->where('tanggal', 'Thursday')->first()->shift_id ?? 'N/A';
            $laporanMingguan->D7 = $userJadwal->where('tanggal', 'Friday')->first()->shift_id ?? 'N/A';

            // Menghitung uang mingguan, uang kedatangan, dan uang lembur
            $laporanMingguan->uang_mingguan = $this->hitungUangMingguan($userId, $request->minggu_ke);
            $laporanMingguan->uang_kedatangan = $this->hitungUangKedatangan($userId, $request->minggu_ke);
            $laporanMingguan->uang_lembur_mingguan = $this->hitungUangLemburMingguan($userId, $request->minggu_ke);

            // Simpan laporan mingguan untuk user ini
            $laporanMingguan->save();

            // Masukkan laporan mingguan ke array agar bisa di-return atau dilihat lebih lanjut
            $laporanMingguans[] = $laporanMingguan;
        }

        // Return response dengan data laporan mingguan yang telah digenerate
        return response()->json([
            'message' => 'Laporan mingguan berhasil dibuat untuk seluruh user.',
            'laporan_mingguan' => $laporanMingguans
        ]);
    }

    // Fungsi untuk menghitung uang mingguan
    private function hitungUangMingguan($userId, $mingguKe)
    {
        return JadwalKaryawan::where('user_id', $userId)
            ->where('minggu_ke', $mingguKe)
            ->sum('total_lembur');
    }

    // Fungsi untuk menghitung uang kedatangan
    private function hitungUangKedatangan($userId, $mingguKe)
    {
        return JadwalKaryawan::where('user_id', $userId)
            ->where('minggu_ke', $mingguKe)
            ->whereNotNull('cek_keterlambatan')
            ->count() * 5000; // Misalnya, 5000 untuk setiap kedatangan tepat waktu
    }

    // Fungsi untuk menghitung uang lembur mingguan
    private function hitungUangLemburMingguan($userId, $mingguKe)
    {
        return JadwalKaryawan::where('user_id', $userId)
            ->where('minggu_ke', $mingguKe)
            ->sum('total_lembur');
    }

    public function generateLaporanMingguanForAll($mingguKe)
    {
        // Ambil semua user yang memiliki jadwal karyawan untuk minggu ke tertentu
        $jadwalKaryawan = JadwalKaryawan::where('minggu_ke', $mingguKe)
            ->get()
            ->groupBy('user_id'); // Kelompokkan berdasarkan user_id

        // Proses setiap user untuk menghasilkan laporan mingguan
        foreach ($jadwalKaryawan as $userId => $jadwals) {
            // Inisialisasi array untuk menyimpan hari-hari dalam minggu
            $hari = [
                'D1' => null, // Sabtu
                'D2' => null, // Minggu
                'D3' => null, // Senin
                'D4' => null, // Selasa
                'D5' => null, // Rabu
                'D6' => null, // Kamis
                'D7' => null, // Jumat
            ];

            // Proses setiap jadwal karyawan untuk user ini
            foreach ($jadwals as $jadwalKaryawan) {
                // Ambil hari dalam minggu dari tanggal
                $tanggal = Carbon::parse($jadwalKaryawan->tanggal);
                $dayOfWeek = $tanggal->dayOfWeek; // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu

                // Tentukan hari sesuai dengan dayOfWeek
                switch ($dayOfWeek) {
                    case 0:
                        $hari['D2'] = $jadwalKaryawan->shift_id;
                        break;
                    case 1:
                        $hari['D3'] = $jadwalKaryawan->shift_id;
                        break;
                    case 2:
                        $hari['D4'] = $jadwalKaryawan->shift_id;
                        break;
                    case 3:
                        $hari['D5'] = $jadwalKaryawan->shift_id;
                        break;
                    case 4:
                        $hari['D6'] = $jadwalKaryawan->shift_id;
                        break;
                    case 5:
                        $hari['D7'] = $jadwalKaryawan->shift_id;
                        break;
                    case 6:
                        $hari['D1'] = $jadwalKaryawan->shift_id;
                        break;
                }
            }

            // Simpan laporan mingguan untuk user
            $laporanMingguan = LaporanMingguan::create([
                'user_id' => $userId,
                'minggu_ke' => $mingguKe,
                'D1' => $hari['D1'],
                'D2' => $hari['D2'],
                'D3' => $hari['D3'],
                'D4' => $hari['D4'],
                'D5' => $hari['D5'],
                'D6' => $hari['D6'],
                'D7' => $hari['D7'],
                'uang_mingguan' => 0,  // Sementara kosongkan
                'uang_kedatangan' => 0,  // Sementara kosongkan
                'uang_lembur_mingguan' => 0,  // Sementara kosongkan
            ]);
        }

        return response()->json([
            'message' => 'Laporan mingguan berhasil dibuat untuk seluruh karyawan.',
        ]);
    }
}
