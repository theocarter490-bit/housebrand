<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->timestamps();
        });

        $status = [
            'Confirmed',
            'Processed',
            'Shipped',
            'Delivered',
            'Cancelled',
            'Returned',
            'Refunded',
            'Claim',
            'Partial Delivery',
        ];
        $colors = [
            '#2DCCFF',
            '#ff9f43',
            '#FFB302',
            '#28c76f',
            '#FF3838',
            '#A4ABB6',
            '#e3b7eb',
            '#ffcd85',
            '#7367f0',
        ];

        foreach ($status as $key => $value) {
            $status = new \App\Models\OrderStatus();
            $status->name = $value;
            $status->color = $colors[$key];
            $status->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
