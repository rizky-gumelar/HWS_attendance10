<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        Setting::updateOrCreate(
            ['key' => 'toleransi_masuk'],
            [
                'value' => '0',
                'type' => 'int',
                'description' => 'Toleransi keterlambatan dalam menit'
            ]
        );
    }
}
