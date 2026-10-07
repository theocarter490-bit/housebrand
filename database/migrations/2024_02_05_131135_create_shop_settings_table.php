<?php

use App\Models\User;
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
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->references('id')->on('users')->onDelete('cascade');
            $table->string('shop_name')->nullable();
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('loader')->nullable();
            $table->string('banner')->nullable();
            $table->text('location')->nullable();
            $table->text('map_location')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->longText('shipping_policy')->nullable();
            $table->longText('return_policy')->nullable();
            $table->longText('disclaimer')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('home_slider_style')->default('image');
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter_url')->nullable();
            $table->longText('emergency_notice')->nullable();
            $table->tinyInteger('emergency_notice_status')->default(0);
            $table->tinyInteger('product_setting')->default(0);
            $table->tinyInteger('shop_status')->default(0);
            $table->json('modules')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};
