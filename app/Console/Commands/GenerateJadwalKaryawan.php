<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
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
            ->select('users.id as KaryawanID', 'shift.id as ShiftID', 'shift.nama_shift')
            ->get();

        $today = Carbon::today();

        foreach ($karyawanList as $karyawan) {
            for ($i = 0; $i < 7; $i++) {
                // Menentukan tanggal yang akan diproses
                $tanggal = $today->copy()->addDays($i);

                // Jika hari tersebut adalah Minggu (Carbon::SUNDAY == 0)
                if ($tanggal->dayOfWeek == Carbon::SUNDAY) {
                    $shift_id = 99; // Ganti shift_id menjadi 99 untuk hari Minggu
                } else {
                    $shift_id = $karyawan->ShiftID; // Gunakan shift_id default jika bukan Minggu
                }

                JadwalKaryawan::updateOrCreate(
                    [
                        'user_id' => $karyawan->KaryawanID,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'shift_id' => $shift_id,
                        'cek_keterlambatan' => null,
                        'lembur_jam' => 0,
                        'total_lembur' => 0,
                        'keterangan' => null,
                        'minggu_ke' => $today->copy()->addDays($i)->weekOfYear,
                    ]
                );
            }
        }

        $this->info('Jadwal karyawan berhasil digenerate untuk semua shift selama 7 hari ke depan.');
    }
}
