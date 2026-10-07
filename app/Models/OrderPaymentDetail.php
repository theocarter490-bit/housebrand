<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class OrderPaymentDetail extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    // order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    // paymentMethod
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
