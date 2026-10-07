<?php

namespace Database\Seeders;

use App\Models\OrderClaimIssueType;
use Illuminate\Database\Seeder;

class OrderClaimIssueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // order claim types
        $orderClaimTypes = [
            'Order Not Received',
            'Damaged Condition',
            'Wrong Product',
            'Product Missing',
            'Payment Issue',
            'Others',
        ];

        foreach ($orderClaimTypes as $orderClaimType) {
            OrderClaimIssueType::create([
                'name' => $orderClaimType,
            ]);
        }

    }
}
