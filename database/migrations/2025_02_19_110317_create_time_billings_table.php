<?php

use App\Models\User;
use App\Models\Project;
use App\Models\AssignProjectService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_billings', function (Blueprint $table) {
            $table->id();
            $table->mediumText('code');
            $table->foreignIdFor(User::class)->nullable();
            $table->foreignIdFor(User::class, 'employee_id')->nullable();
            $table->foreignIdFor(User::class, 'client_id')->nullable();
            $table->foreignIdFor(Project::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(AssignProjectService::class)->constrained()->cascadeOnDelete();
            $table->tinyInteger('bill_type')->default(1)->comment('1=billable, 0= non-billable');
            $table->double('rate')->default(0.00);
            $table->integer('duration')->nullable();
            $table->double('total_amount')->default(0.00);
            $table->tinyInteger('active_status')->default(1);
            $table->integer('payment_status')->default(0)->comment('0 - unpaid, 1 - paid, 2 - partial');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_billings');
    }
};
