<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();

        foreach ($roles as $key => $role) {
            $user = new \App\Models\User();
            $user->name = $role->name;
            $user->email = Str::slug($role->name) . '@gmail.com';
            $user->password = bcrypt('password');
            $user->role_id = $role->id;

            if($role->id == Role::CUSTOMER){
                $user->designer_id = 3;
            }
            if ($role->id == Role::DESIGNER || $role->id == Role::MANUFACTURER) {
                $user->is_subscribed = 1;
            }
            $user->save();
        }

    }
}
