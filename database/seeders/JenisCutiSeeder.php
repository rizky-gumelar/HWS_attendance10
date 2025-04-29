<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisCuti;

class JenisCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisCuti::create([
            'id' => 1,
            'nama_cuti' => 'Cuti Tahunan',
            'status' => '1',
        ]);
        JenisCuti::create([
            'id' => 2,
            'nama_cuti' => 'Sakit',
            'status' => '0',
        ]);
        JenisCuti::create([
            'id' => 3,
            'nama_cuti' => 'Kedukaan',
            'status' => '0',
        ]);
        JenisCuti::create([
            'id' => 4,
            'nama_cuti' => 'Melahirkan',
            'status' => '0',
        ]);
        JenisCuti::create([
            'id' => 5,
            'nama_cuti' => 'Cuti Bersama',
            'status' => '1',
        ]);
        JenisCuti::create([
            'id' => 6,
            'nama_cuti' => 'Pernikahan',
            'status' => '0',
        ]);
        JenisCuti::create([
            'id' => 7,
            'nama_cuti' => 'Setengah Hari (Pagi)',
            'status' => '0.5',
        ]);
        JenisCuti::create([
            'id' => 8,
            'nama_cuti' => 'Setengah Hari (Sore)',
            'status' => '0.5',
        ]);
    }
}
