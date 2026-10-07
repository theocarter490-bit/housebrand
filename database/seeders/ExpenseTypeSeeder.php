<?php

namespace Database\Seeders;

use App\Models\ExpenseType;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        ExpenseType::create([
            'name' => 'Designer Expense',
            'user_id' => 1,
            'is_active' => 1,
        ]);
    }
}
