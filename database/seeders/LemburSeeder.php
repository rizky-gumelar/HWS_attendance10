<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lembur;

class LemburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lembur::create([
            'tipe_lembur' => 'Packing',
            'biaya' => '20000',
        ]);
        Lembur::create([
            'tipe_lembur' => 'Bazar',
            'biaya' => '10000',
        ]);
        Lembur::create([
            'tipe_lembur' => 'Online',
            'biaya' => '20000',
        ]);
        Lembur::create([
            'tipe_lembur' => 'Muat',
            'biaya' => '20000',
        ]);
    }
}
