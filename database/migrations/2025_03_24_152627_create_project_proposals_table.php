<?php

use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->date('proposal_date')->nullable();
            $table->date('due_date')->nullable();

            $table->double('sub_total')->default(0);
            $table->tinyInteger('tax_type')->default(0)->nullable()->comment('0=no tax, 1=percentage, 2=fixed');
            $table->integer('tax_value')->nullable();
            $table->double('tax_amount')->default('0.00')->nullable();

            $table->tinyInteger('discount_type')->default(0)->nullable()->comment('0=no discount, 1=percentage, 2=fixed');
            $table->integer('discount_value')->nullable();
            $table->double('discount_amount')->default('0.00')->nullable();

            $table->double('shipping_charge')->default('0.00')->nullable();
            $table->double('deposit_amount')->default('0.00')->nullable();

            $table->double('total_amount')->default('0.00')->nullable();

            $table->string('signature')->nullable();
            $table->tinyInteger('active_status')->default(0)->comment(' 1=Active, 0=Inactive');
            $table->tinyInteger('is_published')->default(0)->comment(' 1=Yes, 0=No');
            $table->tinyInteger('is_approved')->default(0)->comment(' 1=Yes, 0=No');
            $table->foreignIdFor(User::class)->constrained('users')->onDelete('cascade');
            $table->foreignIdFor(Project::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_proposals');
    }
};
