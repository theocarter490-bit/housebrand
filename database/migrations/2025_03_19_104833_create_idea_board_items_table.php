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
        Schema::create('idea_board_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idea_board_id');
            $table->foreign('idea_board_id')->references('id')->on('idea_boards')->onDelete('cascade');

            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->unsignedBigInteger('project_service_id')->nullable();
            $table->foreign('project_service_id')->references('id')->on('project_services')->onDelete('cascade');

            $table->json('variation')->nullable();
            $table->double('unit_price')->nullable();

            $table->integer('discount_type')->nullable();
            $table->double('discount_amount')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->double('shipping_charge')->default(0.00);
            $table->double('markup')->default(0.00);

            $table->double('price')->nullable();
            $table->integer('type')->default(1)->comment('1 = product, 2 = service');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idea_board_items');
    }
};
