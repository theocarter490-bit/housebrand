<?php

use App\Models\Plan;
use App\Models\PlanModule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plan_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->timestamps();
        });

        $modules = ['Product Management', 'Project Management', 'Appointment Scheduler', 'Employee Management', 'Event Management', 'Marketing', 'Expense Management', 'Notice Management', 'Blog', 'Portfolio & Inspiration', 'Gallery Setup', 'Custom Theme', 'Custom Email Setup','Real Time Communication'];

        foreach ($modules as $key => $item) {
            $module = new PlanModule();
            $module->title = $item;
            $module->slug = Str::slug($item);
            $module->description = 'This is ' . $item . ' module';
            $module->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_modules');
    }
};
