<?php

use App\Models\Product;
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
        Schema::create('designer_shared_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designer_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('seller_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignIdFor(Product::class)->references('id')->on('products')->onDelete('cascade');
            $table->tinyInteger('is_published')->default(1);
            $table->integer('status')->default(0)->comment('pending=0, accept=1, cancel=2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designer_shared_products');
    }
};
