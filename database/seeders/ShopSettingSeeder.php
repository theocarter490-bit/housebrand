<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\ShopSetting;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShopSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereIn('role_id',[Role::SUPER_ADMIN, Role::DESIGNER, Role::MANUFACTURER])->get();
        foreach ($users as $key => $user) {
            $setting            = new ShopSetting();
            $setting->user_id   = $user->id;
            $setting->shop_name = $user->role_id == 1? "House Brand": $user->name;
            $setting->logo      = 'uploads/house-brand/icon/logo.png';
            $setting->favicon   = 'uploads/house-brand/icon/favicon.png';
            $setting->slug      = Str::slug($user->role_id == 1? "House Brand": $user->name);
            $setting->location  = 'USA';
            $setting->phone     = +8547855;
            $setting->email     = $user->email;
            $setting->save();
        }
    }
}
