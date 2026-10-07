<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_proposal_invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\ProjectProposalInvoice::class);
            $table->foreignIdFor(\App\Models\Product::class);
            $table->json('variation')->nullable();
            $table->double('unit_price')->nullable();
            $table->double('markup')->nullable();
            $table->string('discount_type', 20)->nullable();
            $table->double('discount_amount')->nullable();
            $table->integer('quantity')->default(1);
            $table->double('price')->nullable();
            $table->tinyInteger('type')->nullable()->comment('1 for product, 2 for service');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_proposal_invoice_items');
    }
};
