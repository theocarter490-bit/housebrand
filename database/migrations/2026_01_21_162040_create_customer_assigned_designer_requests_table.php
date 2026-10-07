<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_assigned_designer_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('current_designer_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('new_designer_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('status')->default(0)->comment('0-waiting for approval, 1-approved, 2-declined, 3-canceled');
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_assigned_designer_requests');
    }
};
