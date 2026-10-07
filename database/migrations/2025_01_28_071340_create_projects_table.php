<?php

use App\Models\ProjectCategory;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('project_code')->unique();
            $table->string('banner')->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignIdFor(User::class)->constrained('users')->onDelete('cascade');
            $table->foreignIdFor(ProjectStatus::class);
            $table->foreignIdFor(User::class, 'client_id')->nullable();
            $table->foreignIdFor(ProjectCategory::class);
            $table->foreignIdFor(User::class, 'project_manager_id')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
            $table->double('budget')->default(0);
            $table->integer('tax_type')->nullable();
            $table->double('tax')->nullable();
            $table->double('total_cost')->default(0);
            $table->tinyInteger('active_status')->default(1);
            $table->string('address')->nullable();
            $table->longText('map_location')->nullable();
            $table->longText('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
