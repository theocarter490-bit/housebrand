<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class OrderItem extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $casts = [
        'variation' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function statusLog()
    {
        return $this->hasMany(OrderItemStatusLog::class);
    }
    public function latestStatus()
    {
        return $this->hasOne(OrderItemStatusLog::class)->latest();
    }
}
