<?php

namespace Database\Seeders;

use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::with('role')->get();

        foreach($users as $user)
        {
            $email_setting = new EmailSetting();
            $email_setting->user_id = ($user->role->name == 'Super Admin' || $user->role->name == 'Admin') ? 1 : $user->id;
            $email_setting->save();
        }
    }
}
