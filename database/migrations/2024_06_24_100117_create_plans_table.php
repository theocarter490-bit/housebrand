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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('role_id')->nullable();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->double('price')->nullable();
            $table->double('setup_fee')->nullable();
            $table->string('plan_type')->nullable();
            $table->string('product_id')->nullable();
            $table->string('price_id')->nullable();
            $table->json('modules')->nullable();
            $table->tinyInteger('is_popular')->default(0);
            $table->tinyInteger('is_active')->default(0);
            $table->tinyInteger('is_free_trail')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
