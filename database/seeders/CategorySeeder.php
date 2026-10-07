<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $categories = ['Bed', 'Dining', 'Drawing', 'Kitchen','Office'];
        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => $category,
                'image' => 'uploads/category/'.Str::lower($category).'.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


    }
}
