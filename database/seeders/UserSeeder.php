<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {

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
