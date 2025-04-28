<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::create([
            'nama_shift' => 'Shift Pagi (8-17)',
            'shift_masuk' => '08:00:00',
            'shift_keluar' => '17:00:00',
        ]);
        Shift::create([
            'nama_shift' => 'Shift Siang (10-21)',
            'shift_masuk' => '10:00:00',
            'shift_keluar' => '21:00:00',
        ]);
        Shift::create([
            'id' => 999,
            'nama_shift' => 'Libur Pengganti',
            'shift_masuk' => '23:59:58',
            'shift_keluar' => '23:59:59',
        ]);
        Shift::create([
            'id' => 9999,
            'nama_shift' => 'Libur',
            'shift_masuk' => '23:59:58',
            'shift_keluar' => '23:59:59',
        ]);
        Shift::create([
            'id' => 9998,
            'nama_shift' => 'Cuti Free',
            'shift_masuk' => '23:59:58',
            'shift_keluar' => '23:59:59',
        ]);
        Shift::create([
            'id' => 9997,
            'nama_shift' => 'Cuti',
            'shift_masuk' => '00:00:01',
            'shift_keluar' => '00:00:01',
        ]);
        Shift::create([
            'id' => 9996,
            'nama_shift' => 'Cuti Setengah Hari',
            'shift_masuk' => '00:00:01',
            'shift_keluar' => '00:00:01',
        ]);

        // User::create([
        //     'toko_id' => 1,
        //     'nama_karyawan' => 'Admin User',
        //     'default_shift_id' => 1,
        //     'divisi_id' => 2,
        //     'no_hp' => '081234567890',
        //     'email' => 'admin@example.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'role' => 'admin',
        //     'total_cuti' => 24,
        //     'status' => 'aktif',
        // ]);
    }
}
