<?php

use App\Models\User;
use App\Models\FooterWidget;
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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('short_desc')->nullable();
            $table->string('image')->nullable();
            $table->longtext('content')->nullable();
            $table->longText('our_mission')->nullable();
            $table->text('meta_title')->nullable();
            $table->longText('meta_description', 1000)->nullable();
            $table->longText('keywords')->nullable();
            $table->string('meta_image')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->foreignIdFor(User::class)->references('id')->on('users')->onDelete('cascade');
            $table->foreignIdFor(FooterWidget::class)->references('id')->on('footer_widgets')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->default(1)->nullable();
            $table->unsignedBigInteger('updated_by')->default(1)->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
