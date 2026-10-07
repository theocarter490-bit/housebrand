<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use OwenIt\Auditing\Contracts\Auditable;

class ProductRequest extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    const PENDING = 0;
    const APPROVED = 1;
    const CANCELED = 2;
    const ADDED_TO_CART = 3;
    const COMPLETED = 4;

    protected $casts = [
        'variation' => 'array',
    ];



    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                $intValue = (int) $value;
                return match ($intValue) {
                    self::APPROVED => 'Approved',
                    self::CANCELED => 'Canceled',
                    self::COMPLETED => 'Completed',
                    self::ADDED_TO_CART => 'Added to Cart',
                    default => 'Pending',
                };
            }
        );
    }

    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id');
    }
}
