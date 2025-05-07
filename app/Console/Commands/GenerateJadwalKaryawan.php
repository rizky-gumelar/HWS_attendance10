<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use App\Models\Libur;
use App\Models\JadwalKaryawan;
use Illuminate\Support\Facades\DB;

use Illuminate\Console\Command;

class GenerateJadwalKaryawan extends Command
{
    protected $signature = 'generate:jadwal';
    protected $description = 'Generate jadwal karyawan untuk 7 hari ke depan berdasarkan shift masing-masing';

    public function handle()
    {
        $karyawanList = DB::table('users')
            ->join('shift', 'users.default_shift_id', '=', 'shift.id') // Ambil shift berdasarkan default_shift_id
            ->where('users.status', 'aktif')
            ->where('role', '!=', 'admin')
            ->select('users.id as KaryawanID', 'shift.id as ShiftID', 'shift.nama_shift')
            ->get();

        $today = Carbon::today();
        // Tentukan awal minggu (Sabtu) berdasarkan hari ini
        $startOfWeek = $today->copy()->startOfWeek(Carbon::SATURDAY);
        $endOfWeek = $startOfWeek->copy()->addDays(6); // Jumat
        $mingguKe = $startOfWeek->weekOfYear; // Pastikan minggu ke-berapa

        foreach ($karyawanList as $karyawan) {
            for ($tanggal = $startOfWeek->copy(); $tanggal <= $endOfWeek; $tanggal->addDay()) {

                // Cek apakah tanggal tersebut adalah hari libur
                if (Libur::isLibur($tanggal)) {
                    continue; // Lewati jika libur
                }

                // Jika hari tersebut adalah Minggu, shift_id = 999
                $shift_id = ($tanggal->dayOfWeek == Carbon::SUNDAY) ? 9999 : $karyawan->ShiftID;

                JadwalKaryawan::updateOrCreate(
                    [
                        'user_id' => $karyawan->KaryawanID,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'shift_id' => $shift_id,
                        // 'cek_keterlambatan' => 2,
                        // 'lembur_jam' => 0,
                        // 'total_lembur' => 0,
                        // 'keterangan' => null,
                        'minggu_ke' => $mingguKe,
                    ]
                );
            }
        }

        $this->info('Jadwal karyawan berhasil digenerate untuk semua shift selama 7 hari ke depan.');
    }
}
