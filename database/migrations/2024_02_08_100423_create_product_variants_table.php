<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->references('id')->on('products')->onDelete('cascade');
            $table->string('variant')->nullable();
            $table->string('sku')->nullable();
            $table->double('price')->default(0);
            $table->integer('discount_type')->nullable();
            $table->double('discount_amount')->nullable();
            $table->integer('qty')->default(0);
            $table->string('image')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
