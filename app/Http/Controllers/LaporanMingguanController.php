<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\Shift;
use App\Models\Divisi;
use App\Models\LaporanMingguan;
use App\Models\JadwalKaryawan;
use App\Models\Libur;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanMingguanController extends Controller
{
    public function index(Request $request)
    {
        // $karyawans = User::where('status', 'aktif')->get();
        // $mingguans = LaporanMingguan::all();
        // $karyawans = User::all();
        $mingguKe = $request->query('minggu_ke', Carbon::today()->startOfWeek(Carbon::SATURDAY)->weekOfYear);
        // Hitung tanggal awal dan akhir dari minggu_ke
        $tahun = Carbon::now()->year;
        $startDate = Carbon::now()->setISODate($tahun, $mingguKe + 1)->startOfWeek(Carbon::SATURDAY);
        $endDate = $startDate->copy()->addDays(6);

        //------------------------------------------------
        $mingguans = LaporanMingguan::where('minggu_ke', $mingguKe)
            ->with('users') // Pastikan relasi users dipanggil
            ->get();

        $karyawans = User::where('status', 'aktif')->get();
        // $mingguan = User::orderBy('status', 'asc')->orderBy('id', 'asc')->get();
        return view('mingguan_view.index', compact('mingguans', 'karyawans', 'mingguKe', 'startDate', 'endDate'));
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
                'd1'  => null,
                'd2' => null,
                'd3'  => null,
                'd4' => null,
                'd5'   => null,
                'd6'  => null,
                'd7'  => null,
            ];

            $row = 7;
            $mingguan = 0;
            $tottelat = 0;
            $kedatangan = 0;
            $totlembur = 0;
            $status = 'selesai';

            // Proses setiap jadwal karyawan untuk user ini
            foreach ($jadwals as $jadwalKaryawan) {
                // Ambil hari dalam minggu dari tanggal
                $tanggal = Carbon::parse($jadwalKaryawan->tanggal);
                $dayOfWeek = $tanggal->dayOfWeek; // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu

                $shift = $jadwalKaryawan->shift->nama_shift;
                $jamMasuk = $jadwalKaryawan->absensi->jam_masuk ?? '-';
                $isLibur = Libur::isLibur($tanggal);
                $keteranganLibur = $isLibur ? Libur::getLibur($tanggal)->keterangan : null;

                $value = json_encode([
                    'shift' => $shift,
                    'jam_masuk' => $jamMasuk,
                    'libur' => $isLibur,
                    'keterangan_libur' => $keteranganLibur
                ]);

                // Tentukan hari sesuai dengan dayOfWeek
                switch ($dayOfWeek) {
                    case 0:
                        $hari['d2'] = $value;
                        break;
                    case 1:
                        $hari['d3'] = $value;
                        break;
                    case 2:
                        $hari['d4'] = $value;
                        break;
                    case 3:
                        $hari['d5'] = $value;
                        break;
                    case 4:
                        $hari['d6'] = $value;
                        break;
                    case 5:
                        $hari['d7'] = $value;
                        break;
                    case 6:
                        $hari['d1'] = $value;
                        break;
                }

                if ($jadwalKaryawan->cek_keterlambatan == 0 || $isLibur) {
                    if (
                        stripos($jadwalKaryawan->shift->nama_shift, 'Libur') === false &&
                        (stripos($jadwalKaryawan->shift->nama_shift, 'Cuti') === false || $jadwalKaryawan->users->total_cuti > 0)
                        // stripos($jadwalKaryawan->shift->nama_shift, 'Cuti') === false
                    ) {
                        $mingguan = $mingguan + 15000;
                    }
                } else if ($jadwalKaryawan->cek_keterlambatan == 2) {
                    $status = 'kurang';
                } else {
                    $tottelat++;
                }
                $totlembur = $totlembur + $jadwalKaryawan->total_lembur;
            }

            if ($status == 'kurang') {
                $kedatangan = 0;
            } else {
                if ($jadwalKaryawan->users->divisi->nama_divisi != 'Sales') {
                    // if ($jadwalKaryawan->users->total_cuti > 0) 
                    $kedatangan = 40000;
                }
                // $status = 'selesai';
            }

            $existingSchedule = LaporanMingguan::where('user_id', $userId)
                ->where('minggu_ke', $mingguKe)
                ->first();

            // JIKA JADWAL SUDAH ADA MAKA UPDATE
            if ($existingSchedule) {
                // Jika jadwal sudah ada, update jadwal yang ada
                $existingSchedule->update([
                    'user_id' => $userId,
                    'minggu_ke' => $mingguKe,
                    'd1' => $hari['d1'],
                    'd2' => $hari['d2'],
                    'd3' => $hari['d3'],
                    'd4' => $hari['d4'],
                    'd5' => $hari['d5'],
                    'd6' => $hari['d6'],
                    'd7' => $hari['d7'],
                    'uang_mingguan' => $mingguan,  // Sementara kosongkan
                    'uang_kedatangan' => $kedatangan,  // Sementara kosongkan
                    'uang_lembur_mingguan' => $totlembur,  // Sementara kosongkan
                    'status' => $status,  // Sementara kosongkan
                ]);
            } else {
                // Simpan laporan mingguan untuk user
                $laporanMingguan = LaporanMingguan::create([
                    'user_id' => $userId,
                    'minggu_ke' => $mingguKe,
                    'd1' => $hari['d1'],
                    'd2' => $hari['d2'],
                    'd3' => $hari['d3'],
                    'd4' => $hari['d4'],
                    'd5' => $hari['d5'],
                    'd6' => $hari['d6'],
                    'd7' => $hari['d7'],
                    'uang_mingguan' => $mingguan,  // Sementara kosongkan
                    'uang_kedatangan' => $kedatangan,  // Sementara kosongkan
                    'uang_lembur_mingguan' => $totlembur,  // Sementara kosongkan
                    'status' => $status,  // Sementara kosongkan
                ]);
            }
        }

        // return response()->json([
        //     'message' => 'Laporan mingguan berhasil dibuat untuk seluruh karyawan.',
        // ]);
        return redirect()->route('mingguan.index')->with('success', 'Jadwal berhasil diimpor.');
    }
}
