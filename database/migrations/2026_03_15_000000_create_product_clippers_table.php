<?php

use App\Models\Brand;
use App\Models\Category;
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
        Schema::create('product_clippers', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->text('slug')->unique();
            $table->foreignIdFor(User::class)->references('id')->on('users')->onDelete('cascade');
            $table->text('thumbnail_img')->nullable();
            $table->text('description')->nullable();
            $table->text('unit_price')->nullable();
            $table->text('product_url')->nullable();
            $table->tinyInteger('is_approved')->default(0);
            $table->tinyInteger('is_published')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_clippers');
    }
};
