<?php

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->double('cost')->default(0);
            $table->text('description')->nullable();
            $table->integer('tax_type')->nullable();
            $table->double('tax')->nullable();
            $table->foreignIdFor(ServiceCategory::class)->nullable();
            $table->foreignIdFor(User::class)->constrained('users')->onDelete('cascade');
            $table->tinyInteger('active_status')->default(1)->comment('1=Active, 2=Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_services');
    }
};
