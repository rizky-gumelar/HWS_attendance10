<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Keuangan;

class KeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Keuangan::create([
            'id' => 1,
            'uang_mingguan' => 15000,
            'uang_kedatangan' => 40000,
        ]);
    }
}
