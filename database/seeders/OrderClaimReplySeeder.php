<?php

namespace Database\Seeders;

use App\Models\OrderClaimReply;
use Faker\Factory;
use Illuminate\Database\Seeder;

class OrderClaimReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $data = new OrderClaimReply();
            $data->order_claim_id = 2;
            $data->user_id = 1;
            $data->details = $faker->paragraph();
            $data->save();
        }
    }
}
