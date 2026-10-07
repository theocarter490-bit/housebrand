<?php

use App\Models\ShippingAddress;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');

            $table->mediumText('code');
            $table->string('payment_type', 20)->nullable();
            $table->string('payment_status', 20)->default('unpaid');
            $table->json('payment_details')->nullable();

            $table->timestamp('order_date');
            $table->integer('is_view')->default('0');

            $table->json('shipping_address')->nullable();
            $table->double('shipping_charges')->nullable();

            $table->double('sub_total_amount')->default('0.00')->nullable();

            $table->tinyInteger('admin_discount_type')->default(0)->nullable()->comment('0=no discount, 1=percentage, 2=fixed');
            $table->double('admin_discount_value')->nullable();
            $table->double('admin_discount_amount')->default('0.00')->nullable();
            $table->tinyInteger('tax_type')->default(0)->nullable()->comment('0=no tax, 1=percentage, 2=fixed');
            $table->double('tax_value')->nullable();
            $table->double('tax_amount')->default('0.00')->nullable();
            $table->string('coupon_code', 191)->nullable();
            $table->double('coupon_discount')->default('0.00')->nullable();
            $table->double('grand_total_amount')->default('0.00')->nullable();
            $table->double('due_amount')->default('0.00')->nullable();

            $table->tinyInteger('status')->nullable()->default(1);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
