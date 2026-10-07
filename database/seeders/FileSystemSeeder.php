<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keys = ['AWS_ACCESS_KEY_ID','AWS_SECRET_ACCESS_KEY','AWS_DEFAULT_REGION','AWS_BUCKET'];

        foreach ($keys as $key) {
            \App\Models\FileSystem::create([
                'type' => 's3',
                'key' => $key,
                'value' => 'test' . $key,
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
