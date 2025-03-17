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
            'divisi' => 'Management',
            'no_hp' => '081234567890',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'total_cuti' => 10,
        ]);

        User::create([
            'toko_id' => 1,
            'nama_karyawan' => 'Supervisor User',
            'divisi' => 'Supervisor',
            'no_hp' => '081234567891',
            'email' => 'spv@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'spv',
            'total_cuti' => 8,
        ]);

        User::create([
            'toko_id' => 1,
            'nama_karyawan' => 'Karyawan User',
            'divisi' => 'Produksi',
            'no_hp' => '081234567892',
            'email' => 'karyawan@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'total_cuti' => 5,
        ]);
    }
}
