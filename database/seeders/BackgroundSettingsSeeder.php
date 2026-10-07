<?php

namespace Database\Seeders;


use App\Models\BackgroundSetting;
use Faker\Factory;
use Illuminate\Database\Seeder;

class BackgroundSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        for ($i = 0; $i<10; $i++){
            $data = new BackgroundSetting();
            $data->title = $faker->name();
            $data->short_desc = $faker->text;
            $data->purpose = 1;
            $data->type = 'image';
            $data->color = '#ffff';

            $data->save();
        }

    }
}
