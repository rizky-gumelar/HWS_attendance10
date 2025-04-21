<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Toko;

class TokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Toko::create([
            'nama_toko' => 'HWS',
            'alamat' => 'Jl. Wr. Supratman No.Kav 37, Gisikdrono, Kec. Semarang Barat, Kota Semarang, Jawa Tengah 50268',
        ]);

        Toko::create([
            'nama_toko' => 'Testing',
            'alamat' => 'Jl. Jalan',
        ]);
    }
}
