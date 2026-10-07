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
        Schema::table('idea_boards', function (Blueprint $table) {
            // $table->tinyInteger('is_generate_proposal')->default(0)->comment(' 1=Yes, 0=No')->after('active_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idea_boards', function (Blueprint $table) {
            //
        });
    }
};
