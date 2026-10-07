<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeBillingPaymentDetail extends Model
{
    use HasFactory;

    public function timeBilling()
    {
        return $this->belongsTo(TimeBilling::class);
    }
    // paymentMethod
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
