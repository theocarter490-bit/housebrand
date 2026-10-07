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
        Schema::create('background_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title',255)->nullable();
            $table->text('short_desc')->nullable();
            $table->tinyInteger('purpose')->default(0)->comment('0=Login Page, 1=Signup Page, 2=Admin Login Page');
            $table->string('type',255)->nullable()->comment('image, color');
            $table->string('image',255)->nullable();
            $table->string('color',255)->nullable();
            $table->integer('is_active')->default(0);

            $table->unsignedBigInteger('created_by')->nullable()->default(1);
            $table->unsignedBigInteger('updated_by')->nullable()->default(1);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_settings');
    }
};
