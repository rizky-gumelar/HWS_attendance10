<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Keuangan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(TokoSeeder::class);
        $this->call(DivisiSeeder::class);
        $this->call(ShiftSeeder::class);
        //user seeder here
        $this->call(UserSeeder::class);
        $this->call(JenisCutiSeeder::class);
        $this->call(LemburSeeder::class);
        $this->call(KeuanganSeeder::class);
        $this->call(SettingSeeder::class);
    }
}
