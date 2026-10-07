<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('color_palettes', function (Blueprint $table) {
            $table->id();
            $table->string('primary', 30)->nullable();
            $table->string('secondary', 30)->nullable();
            $table->string('bg_primary', 30)->nullable();
            $table->string('bg_secondary', 30)->nullable();
            $table->string('text_primary', 30)->nullable();
            $table->string('text_secondary', 30)->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_palettes');
    }
};
