<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
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
        Schema::create('color_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->tinyInteger('type')->default(0)->comment("0 = Frontend, 1 = Admin Panel");
            $table->string('primary', 30)->nullable();
            $table->string('secondary', 30)->nullable();
            $table->string('bg_primary', 30)->nullable();
            $table->string('bg_secondary', 30)->nullable();
            $table->string('text_primary', 30)->nullable();
            $table->string('text_secondary', 30)->nullable();
            $table->tinyInteger('active_status')->default(0);
            $table->tinyInteger('theme_status')->default(1)->comment("0 = Admin Define, 1 = Custom Define");
            $table->foreignIdFor(User::class)->nullable()->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('color_themes');
    }
};
