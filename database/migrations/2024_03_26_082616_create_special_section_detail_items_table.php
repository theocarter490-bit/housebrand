<?php

use App\Models\SpecialSectionDetail;
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
        Schema::create('special_section_detail_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SpecialSectionDetail::class)->nullable();
            $table->longText('description')->nullable();
            $table->string('title')->nullable();
            $table->string('amount')->nullable();
            $table->string('image')->nullable();
            $table->json('product_ids')->nullable();
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
        Schema::dropIfExists('special_section_detail_items');
    }
};
