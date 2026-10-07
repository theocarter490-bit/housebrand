<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = ['HATIL', 'Otobi', 'Regal', 'Navana', 'Partex'];

        foreach ($brands as $brand) {
            DB::table('brands')->insert([
                'name' => $brand,
                'slug' => Str::slug($brand),
                'image' => 'uploads/brand/'.Str::lower($brand).'.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
