<?php

use App\Models\Order;
use App\Models\OrderClaimIssueType;
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
        Schema::create('order_claims', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignIdFor(Order::class)->nullable()->references('id')->on('orders')->onDelete('cascade');
            $table->foreignIdFor(OrderClaimIssueType::class)->nullable()->references('id')->on('order_claim_issue_types')->onDelete('cascade');

            $table->text('subject')->nullable();
            $table->longText('details')->nullable();
            $table->string('file')->nullable();
            $table->tinyInteger('status')->default(0)->comment('O = pending, 1 = Accepted, 2 = Closed');
            $table->dateTime('date_time')->nullable();
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
        Schema::dropIfExists('order_claims');
    }
};
