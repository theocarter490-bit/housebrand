<?php

use App\Models\PaymentMethod;
use App\Models\ProjectProposalInvoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proposal_invoice_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProjectProposalInvoice::class);
            $table->date('payment_date')->nullable();
            $table->double('amount')->default(0.00);
            $table->double('current_due')->default(0.00);
            $table->foreignIdFor(PaymentMethod::class)->constrained();
            $table->string('tnx_id')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable()->default(1)->unsigned();
            $table->integer('updated_by')->nullable()->default(1)->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_invoice_payment_details');
    }
};
