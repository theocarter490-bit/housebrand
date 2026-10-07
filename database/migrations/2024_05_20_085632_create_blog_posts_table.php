<?php

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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->longText('desc')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('banner')->nullable();
            $table->string('video_url')->nullable();
            $table->text('tags')->nullable();
            $table->text('meta_title')->nullable();
            $table->longText('meta_desc')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('serial_no')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->tinyInteger('publish_status')->default(0);
            $table->timestamp('publish_at')->nullable();
            $table->foreignId('category_id')->references('id')->on('blog_categories')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('created_by')->references('id')->on('users')->onDelete('cascade')->nullable();
            $table->unsignedBigInteger('updated_by')->references('id')->on('users')->onDelete('cascade')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
