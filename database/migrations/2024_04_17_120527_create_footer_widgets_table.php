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
        Schema::create('footer_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->tinyInteger('serial')->unique()->nullable();
            $table->tinyInteger('active_status')->default(1);

            $table->unsignedBigInteger('created_by')->nullable()->default(1);
            $table->unsignedBigInteger('updated_by')->nullable()->default(1);

            // database table index create
            $table->index(['title', 'serial', 'active_status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_widgets');
    }
};
