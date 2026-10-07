<?php

use App\Models\User;
use App\Models\ReviewType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('designer_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ReviewType::class)->constrained();
            $table->foreignIdFor(User::class, 'customer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignIdFor(User::class, 'designer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('review')->nullable();
            $table->float('rating')->default(0);
            $table->tinyInteger('active_status')->default(1);
            $table->foreignIdFor(User::class, 'created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignIdFor(User::class, 'updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designer_reviews');
    }
};
