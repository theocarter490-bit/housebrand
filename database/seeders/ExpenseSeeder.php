<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Expense::create([
            'title' => 'Digital Marketing for Designers',
            'type_id' => 1,
            'expense_date' => now(),
            'amount' => 25.00,
            'voucher' => "x8tRu025",
            'details' => "<p>We hired Digital Marketer</p>",
            'created_by' => 1,

            'active_status' => 1,

        ]);
    }
}
