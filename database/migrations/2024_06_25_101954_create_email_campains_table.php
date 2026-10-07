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
        Schema::create('email_campains', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('message')->nullable();
            $table->string('attachment')->nullable();
            $table->json('emails')->nullable();
            $table->tinyInteger('is_sent')->default(0);
            $table->tinyInteger('active_status')->default(1);

            $table->unsignedBigInteger('created_by')->references('id')->on('users')->onDelete('cascade')->default(1);
            $table->unsignedBigInteger('updated_by')->references('id')->on('users')->onDelete('cascade')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campains');
    }
};
