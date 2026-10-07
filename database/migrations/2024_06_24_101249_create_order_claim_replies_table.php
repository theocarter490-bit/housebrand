<?php

use App\Models\OrderClaim;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_claim_replies', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(OrderClaim::class)->references('id')->on('order_claims')->onDelete('cascade');
            $table->foreignIdFor(User::class)->references('id')->on('users')->onDelete('cascade');
            $table->longText('details')->nullable();
            $table->string('file')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_claim_replies');
    }
};
