<?php

namespace App\Models;

use App\Models\OrderPaymentDetail;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CommonQueryTraits;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use CommonQueryTraits;


    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designer()
    { 
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    public function orderStatus()
    {
        return $this->hasOne(OrderStatus::class, 'id', 'status');
    }

    public function shop()
    {
        return $this->belongsTo(ShopSetting::class, 'seller_id', 'user_id');
    }

    public function paymentDetails()
    {
        return $this->hasMany(OrderPaymentDetail::class);
    }
    
    public function lastPayment(){
        return $this->belongsTo(OrderPaymentDetail::class,'id','order_id')->latest();
    }

    protected function shippingAddress(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value),
        );
    }
}
