<?php

namespace Database\Seeders;

use App\Models\DesignerContact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesignerContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            DesignerContact::create([
                'name' => "Customer $i",
                'designer_id' => 3,
                'email' => "customer$i@gmail.com",
                'phone' => "0187743761$i",
                'message' => "This is the Designer Contact Message $i.",
                'active_status' => rand(0, 1),
            ]);
        }
    }
}
