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
            'nama_toko' => 'BJ',
            'alamat' => '-',
        ]);

        Toko::create([
            'nama_toko' => 'HIJ',
            'alamat' => '-',
        ]);

        Toko::create([
            'nama_toko' => 'HWS',
            'alamat' => '-',
        ]);

        Toko::create([
            'nama_toko' => 'SS',
            'alamat' => '-',
        ]);

        Toko::create([
            'nama_toko' => 'GSN',
            'alamat' => '-',
        ]);
    }
}
