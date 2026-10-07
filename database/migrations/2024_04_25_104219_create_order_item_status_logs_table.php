<?php

use App\Models\OrderItem;
use App\Models\OrderStatus;
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
        Schema::create('order_item_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderStatus::class)->nullable();
            $table->foreignIdFor(OrderItem::class)->nullable()->references('id')->on('order_items')->onDelete('cascade');
            $table->timestamp('date_time')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_status_logs');
    }
};
