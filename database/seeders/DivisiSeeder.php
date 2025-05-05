<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Divisi::create([
            'id' => 1,
            'nama_divisi' => 'Admin General',
            'mingguan' => true,
            'kedatangan' => true,
        ]);

        Divisi::create([
            'id' => 2,
            'nama_divisi' => 'Admin Online',
            'mingguan' => true,
            'kedatangan' => true,

        ]);

        Divisi::create([
            'id' => 3,
            'nama_divisi' => 'Gudang Distribusi',
            'mingguan' => true,
            'kedatangan' => true,
        ]);

        Divisi::create([
            'id' => 4,
            'nama_divisi' => 'Gudang Online',
            'mingguan' => true,
            'kedatangan' => true,
        ]);

        Divisi::create([
            'id' => 5,
            'nama_divisi' => 'Retail WR',
            'mingguan' => true,
            'kedatangan' => true,
        ]);

        Divisi::create([
            'id' => 6,
            'nama_divisi' => 'Sales',
            'mingguan' => true,
            'kedatangan' => false,
        ]);

        Divisi::create([
            'id' => 7,
            'nama_divisi' => 'Support Distribusi',
            'mingguan' => true,
            'kedatangan' => true,
        ]);

        Divisi::create([
            'id' => 8,
            'nama_divisi' => 'Teknis',
            'mingguan' => true,
            'kedatangan' => true,
        ]);

        Divisi::create([
            'id' => 9,
            'nama_divisi' => 'Admin & Data Analyst',
            'mingguan' => true,
            'kedatangan' => true,
        ]);
    }
}
