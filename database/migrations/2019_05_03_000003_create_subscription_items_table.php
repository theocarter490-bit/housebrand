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
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id');
            $table->string('stripe_subscription_item_id')->unique();
            $table->string('stripe_product_id');
            $table->string('stripe_price_id');
            $table->string("stripe_invoice_no");
            $table->json("customer_details");
            $table->double("price");
            $table->string("payment_status");
            $table->string("currency");
            $table->timestamp("started_at");
            $table->timestamp("expire_at");
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
    }
};
