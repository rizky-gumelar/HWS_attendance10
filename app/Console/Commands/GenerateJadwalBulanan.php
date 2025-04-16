<?php

namespace App\Console\Commands;

use App\Models\JadwalKaryawan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateJadwalBulanan extends Command
{
    protected $signature = 'generate:jadwal-bulanan {bulan} {divisi_id}';
    protected $description = 'Generate jadwal karyawan untuk 1 bulan penuh berdasarkan shift dan divisi';

    public function handle()
    {
        $bulan = $this->argument('bulan');       // Misal: 4 untuk April
        $divisiId = $this->argument('divisi_id'); // Misal: 2

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
            ->where('users.divisi_id', $divisiId)
            ->select('users.id as KaryawanID', 'shift.id as ShiftID', 'shift.nama_shift')
            ->get();

        foreach ($karyawanList as $karyawan) {
            for ($tanggal = $startDate->copy(); $tanggal <= $endDate; $tanggal->addDay()) {
                $shift_id = ($tanggal->isSunday()) ? 9999 : $karyawan->ShiftID;

                JadwalKaryawan::updateOrCreate(
                    [
                        'user_id' => $karyawan->KaryawanID,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'shift_id' => $shift_id,
                        'cek_keterlambatan' => 2,
                        'lembur_jam' => 0,
                        'total_lembur' => 0,
                        'keterangan' => null,
                        'minggu_ke' => $tanggal->copy()->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                    ]
                );
            }
        }

        $this->info("Jadwal bulan $bulan untuk divisi ID $divisiId berhasil digenerate.");
    }
}
