<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalInvoicePaymentDetails extends Model
{
    use HasFactory;

    public function invoice()
    {
        return $this->belongsTo(ProjectProposalInvoice::class,'project_proposal_invoice_id','id');
    }
    // paymentMethod
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
