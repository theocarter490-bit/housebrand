<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        for ($i = 0; $i<10; $i++){
            $blogCategory = new BlogCategory();
            $blogCategory->name = $faker->name();
            $blogCategory->slug = Str::slug($blogCategory->name);
            $blogCategory->user_id = 1;
            $blogCategory->save();
        }
    }
}
