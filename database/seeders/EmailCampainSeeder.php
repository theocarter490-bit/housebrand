<?php

namespace Database\Seeders;

use App\Models\EmailCampain;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailCampainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run()
    {

        $userIds = User::pluck('id');


        for ($i = 1; $i <= 10; $i++) {
            EmailCampain::create([
                'title' => "Campaign Title $i",
                'message' => "This is the message for campaign $i.",
                'attachment' => null,
                'is_sent' => rand(0, 1),
                'active_status' => rand(0, 1),
            ]);
        }
    }




}
