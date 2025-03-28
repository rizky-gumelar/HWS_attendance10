<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Toko;
use App\Models\Shift;
use App\Models\Divisi;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {

        Toko::create([
            'nama_toko' => 'HWS',
            'alamat' => 'Jl. Wr. Supratman No.Kav 37, Gisikdrono, Kec. Semarang Barat, Kota Semarang, Jawa Tengah 50268',
        ]);

        Toko::create([
            'nama_toko' => 'Testing',
            'alamat' => 'Jl. Jalan',
        ]);

        Divisi::create([
            'nama_divisi' => 'Gudang',
        ]);

        Divisi::create([
            'nama_divisi' => 'Admin',
        ]);

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
            'id' => 99,
            'nama_shift' => 'Libur',
            'shift_masuk' => '23:59:58',
            'shift_keluar' => '23:59:59',
        ]);

        User::create([
            'toko_id' => 1,
            'nama_karyawan' => 'Admin User',
            'default_shift_id' => 1,
            'divisi_id' => 2,
            'no_hp' => '081234567890',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'total_cuti' => 0,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 1,
            'nama_karyawan' => 'Supervisor User',
            'default_shift_id' => 1,
            'divisi_id' => 2,
            'no_hp' => '081234567891',
            'email' => 'spv@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'spv',
            'total_cuti' => 0,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 1,
            'nama_karyawan' => 'Karyawan User',
            'default_shift_id' => 1,
            'divisi_id' => 1,
            'no_hp' => '081234567892',
            'email' => 'karyawan@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'total_cuti' => 0,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 1,
            'nama_karyawan' => 'Karyawan Nonaktif',
            'default_shift_id' => 1,
            'divisi_id' => 1,
            'no_hp' => '08827364712',
            'email' => 'karyawan2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'total_cuti' => 0,
            'status' => 'nonaktif',
        ]);
    }
}
