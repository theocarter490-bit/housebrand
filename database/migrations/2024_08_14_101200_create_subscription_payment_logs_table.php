<?php

use App\Models\Plan;
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
        Schema::create('subscription_payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string("customer_id");
            $table->foreignIdFor(Plan::class)->constrained()->cascadeOnDelete();
            $table->double("price");
            $table->json("customer_details");
            $table->string("invoice_no");
            $table->string("payment_status");
            $table->string("subscription_no");
            $table->string("currency");
            $table->string("expire_at");
            $table->string("created");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payment_logs');
    }
};
