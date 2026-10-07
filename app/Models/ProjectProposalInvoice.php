<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProposalInvoice extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(ProjectProposalInvoiceItem::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectProposal()
    {
        return $this->belongsTo(ProjectProposal::class);
    }

    public function paymentDetails()
    {
        return $this->hasMany(ProposalInvoicePaymentDetails::class);
    }

    public function lastPayment()
    {
        return $this->hasOne(ProposalInvoicePaymentDetails::class)->latest();
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
