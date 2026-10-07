<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Faker\Factory;
use Faker\Provider\Lorem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = Factory::create();
        $categories = BlogCategory::all('id');

        for ($i = 0; $i < 20; $i++) {
            $blogPost = new BlogPost();
            $blogPost->title = $faker->text(10);
            $blogPost->slug = Str::slug($blogPost->title);
            $blogPost->desc = $faker->paragraph;
            $blogPost->serial_no = $i+1;
            $blogPost->category_id = $categories->random()->id;
            $blogPost->created_by = 1;
            $blogPost->save();
        }
    }
}
