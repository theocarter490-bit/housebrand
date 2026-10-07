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
        Schema::create('subscription_cancel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Plan::class)->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->text('comments')->nullable();
            $table->string('file')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 = pending , 1= Accepted, 2= Canceled');
            $table->timestamp('action_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_cancel_requests');
    }
};
