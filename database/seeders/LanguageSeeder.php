<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native' => 'English',
                'rtl' => 0,
                'is_default' => 1,
            ]
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }

    }
}
