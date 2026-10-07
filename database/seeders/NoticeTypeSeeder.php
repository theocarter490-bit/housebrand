<?php

namespace Database\Seeders;

use App\Models\NoticeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoticeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NoticeType::create([
            'name' => 'General',
            'user_id' => 1,
            'is_active' => 1,
        ]);
    }
}
