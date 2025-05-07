<?php

namespace App\Console\Commands;

use App\Models\JadwalKaryawan;
use App\Models\Absensi;
use App\Models\Shift;
use App\Models\Setting;
use Carbon\Carbon;
use App\Models\Libur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateJadwalBulanan extends Command
{
    protected $signature = 'generate:jadwal-bulanan {bulan} {divisi_id} {--overwrite}';
    protected $description = 'Generate jadwal karyawan untuk 1 bulan penuh berdasarkan shift dan divisi';

    public function handle()
    {
        $toleransi = Setting::where('key', 'toleransi_masuk')->value('value');
        $bulan = $this->argument('bulan');       // Misal: 4 untuk April
        $divisiId = $this->argument('divisi_id'); // Misal: 2
        $overwrite = $this->option('overwrite');

        $tahun = now()->year; // Atau kamu bisa tambah argument juga kalau perlu tahun spesifik

        // Tentukan awal dan akhir bulan
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Cek minggu ke berapa saat mulai
        $startOfWeek = $startDate->copy()->startOfWeek(Carbon::SATURDAY);
        $mingguKe = $startOfWeek->weekOfYear;

        $karyawanList = DB::table('users')
            ->join('shift', 'users.default_shift_id', '=', 'shift.id')
            ->where('users.status', 'aktif')
            ->where('role', '!=', 'admin')
            ->where('users.divisi_id', $divisiId)
            ->select('users.id as KaryawanID', 'shift.id as ShiftID', 'shift.nama_shift')
            ->get();

        foreach ($karyawanList as $karyawan) {
            for ($tanggal = $startDate->copy(); $tanggal <= $endDate; $tanggal->addDay()) {
                // Cek apakah tanggal tersebut adalah hari libur
                if (Libur::isLibur($tanggal)) {
                    continue; // Lewati jika libur
                }
                $shift_id = ($tanggal->isSunday()) ? 9999 : $karyawan->ShiftID;

                $existing = JadwalKaryawan::where('user_id', $karyawan->KaryawanID)
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                if ($overwrite) {
                    // Overwrite jika sudah ada, atau buat baru
                    if ($existing) {
                        // Ambil data absen dan shift berdasarkan ID
                        $absensi = Absensi::find($existing->absen_id);
                        // $shift = Shift::findOrFail($shift_id);
                        $shift = Shift::find($shift_id);
                        if (!$shift) {
                            $this->warn("Shift dengan ID $shift_id tidak ditemukan. Lewatkan tanggal: {$tanggal->toDateString()} untuk user ID: {$karyawan->KaryawanID}");
                            continue;
                        }

                        if (!$absensi || $absensi->jam_masuk == null) {
                            $terlambat = 2;
                        } else {
                            $shiftJamMasuk = \Carbon\Carbon::parse($shift->shift_masuk);
                            $absenJamMasuk = \Carbon\Carbon::parse($absensi->jam_masuk);

                            $shiftMasukWithTolerance = $shiftJamMasuk->copy()->addMinutes($toleransi);
                            $terlambat = $absenJamMasuk->greaterThan($shiftMasukWithTolerance) ? 1 : 0;
                        }
                        $existing->update([
                            'shift_id' => $shift_id,
                            'cek_keterlambatan' => $terlambat,
                            // 'lembur_jam' => 0,
                            // 'total_lembur' => 0,
                            // 'keterangan' => null,
                            'minggu_ke' => $tanggal->copy()->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                        ]);
                    } else {
                        JadwalKaryawan::create([
                            'user_id' => $karyawan->KaryawanID,
                            'tanggal' => $tanggal,
                            'shift_id' => $shift_id,
                            'cek_keterlambatan' => 2,
                            'lembur_jam' => 0,
                            'total_lembur' => 0,
                            'keterangan' => null,
                            'minggu_ke' => $tanggal->copy()->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                        ]);
                    }
                } else {
                    // Hanya buat jika belum ada
                    if (!$existing) {
                        JadwalKaryawan::create([
                            'user_id' => $karyawan->KaryawanID,
                            'tanggal' => $tanggal,
                            'shift_id' => $shift_id,
                            'cek_keterlambatan' => 2,
                            'lembur_jam' => 0,
                            'total_lembur' => 0,
                            'keterangan' => null,
                            'minggu_ke' => $tanggal->copy()->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                        ]);
                    }
                }
            }
        }


        $this->info("Jadwal bulan $bulan untuk divisi ID $divisiId berhasil digenerate.");
    }
}
