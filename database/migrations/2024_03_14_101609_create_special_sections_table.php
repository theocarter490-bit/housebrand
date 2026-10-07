<?php

use App\Models\SpecialSectionCategory;
use App\Models\User;
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
        Schema::create('special_sections', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('slug')->nullable();
            $table->text('image')->nullable();
            $table->integer('serial_no')->nullable();
            $table->integer('type')->nullable()->comment('1=Portfolio, 2=Inspiration');
            $table->foreignIdFor(SpecialSectionCategory::class)->nullable();
            $table->foreignIdFor(User::class)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedBigInteger('created_by')->references('id')->on('users')->onDelete('cascade')->nullable();
            $table->unsignedBigInteger('updated_by')->references('id')->on('users')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_sections');
    }
};
