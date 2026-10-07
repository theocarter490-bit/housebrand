<?php

use App\Models\EventType;
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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(EventType::class)->constrained()->cascadeOnDelete();
            $table->dateTime('start_date')->nullable()->index();
            $table->dateTime('end_date')->nullable();
            $table->string('event_url')->nullable();
            $table->string('location')->nullable();
            $table->string('file')->nullable();
            $table->longText('description')->nullable();
            $table->json('guest_user_ids')->nullable();
            $table->tinyInteger('email_notify')->default(0)->index();
            $table->dateTime('email_notify_at')->nullable()->index();
            $table->tinyInteger('active_status')->default(1)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
