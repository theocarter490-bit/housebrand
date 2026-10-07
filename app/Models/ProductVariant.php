<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProductVariant extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $appends = ["discount_price", 'discount_percentage'];
    const PERCENTAGE = 1;
    const FIXED = 2;

    protected function getDiscountPriceAttribute()
    {
        $discount = $this->attributes['discount_amount'];

        if ($this->discount_type == ProductVariant::PERCENTAGE) {
            return $this->price - ($this->price * ($discount / 100));
        }
        if ($this->discount_type == ProductVariant::FIXED) {
            return $this->price - $discount;
        }
        return null;
    }

    protected function getDiscountPercentageAttribute()
    {
        if ($this->discount_type == ProductVariant::PERCENTAGE) {
            return $this->discount_amount;
        }
        if ($this->discount_type == ProductVariant::FIXED) {
            return ($this->discount_amount / $this->price) * 100;
        }
        return null;
    }
}
