<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    const CONFIRMED = 1;
    const PROCESSED = 2;
    const SHIPPED = 3;
    const DELIVERED = 4;
    const CANCELED = 5;
    const RETURNED = 6;
    const REFUNDED = 7;
    const CLAIM = 8;
    const PARTIAL_DELIVERY = 9;
}
