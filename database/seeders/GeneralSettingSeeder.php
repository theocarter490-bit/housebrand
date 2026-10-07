<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = new GeneralSetting();
        $setting->site_name         = "House Brand";
        $setting->site_title        = 'House Brand Website';
        $setting->copyright_text    = '&copy; 2024 <strong>House-Brand</strong>, All Rights Reserved. Developed by <a target="_blank" href="https://mediusware.com/">Mediusware Ltd</a>';
        $setting->save();
    }
}
