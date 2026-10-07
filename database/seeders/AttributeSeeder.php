<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $attributes = ['Size', 'Fabric', 'Color', 'Material'];
        foreach ($attributes as $attribute) {
            DB::table('attributes')->insert([
                'name' => $attribute,
                'slug' => Str::slug($attribute),
                'description' => $faker->sentence(1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
