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
        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->timestamps();
        });

        DB::table('project_statuses')->insert([
            ['name' => 'New', 'color' => '#FF0000'],
            ['name' => 'In Progress', 'color' => '#FFA500'],
            ['name' => 'Completed', 'color' => '#008000'],
            ['name' => 'On Hold', 'color' => '#0000FF'],
            ['name' => 'Cancelled', 'color' => '#808080'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_statuses');
    }
};
