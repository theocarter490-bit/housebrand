<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Faker\Factory;
use Illuminate\Database\Seeder;

class SubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++){
            $subscriber = new Subscriber();
            $subscriber->email = $faker->email();
            $subscriber->save();
        }
    }
}
