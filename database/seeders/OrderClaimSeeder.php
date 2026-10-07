<?php

namespace Database\Seeders;

use App\Models\OrderClaim;
use Faker\Factory;
use Illuminate\Database\Seeder;

class OrderClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $data = new OrderClaim();
            $data->user_id = 4;
            $data->order_claim_issue_type_id = 1;
            $data->details = $faker->paragraph();
            $data->date_time = $faker->dateTime();
            $data->created_by = 1;
            $data->updated_by = 2;
            $data->save();
        }
    }
}
