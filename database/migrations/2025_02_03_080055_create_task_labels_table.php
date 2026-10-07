<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->timestamps();
        });

        $attributes = ['UX', 'Review', 'Chart', 'Design','Interior','Exterior'];
        foreach ($attributes as $attribute) {
            DB::table('task_labels')->insert([
                'name' => $attribute,
                'color' => '#002254',
                'active_status' => 1
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_labels');
    }
};
