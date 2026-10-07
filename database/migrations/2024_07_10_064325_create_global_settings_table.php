<?php

use App\Models\GlobalSetting;
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
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->json('options')->nullable();
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('active_status')->default(1)->comment('0=inactive, 1=active');
            $table->foreignId('created_by')->nullable()->default(1)->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
    }
};
