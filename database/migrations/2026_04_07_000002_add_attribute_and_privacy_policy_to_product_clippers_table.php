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
        Schema::table('product_clippers', function (Blueprint $table) {
            $table->json('attributes')->nullable()->after('product_url');
            $table->longText('warranty_policy')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_clippers', function (Blueprint $table) {
            $table->dropColumn(['attributes', 'warranty_policy']);
        });
    }
};
