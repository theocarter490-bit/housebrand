<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdeaBoardItem extends Model
{
    use HasFactory;

    protected $casts = [
        'variation' => 'array',
    ];

    const PERCENTAGE = 1;
    const FIXED = 2;

    protected $appends = ["discount_amount_value"];

    public function ideaBoard()
    {
        return $this->belongsTo(IdeaBoard::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function service()
    {
        return $this->belongsTo(ProjectService::class, 'project_service_id');
    }

    protected function getDiscountAmountValueAttribute()
    {
        $discount = $this->attributes['discount_amount'];

        if ($this->discount_type == IdeaBoardItem::PERCENTAGE) {
            return ($this->unit_price * $this->quantity) * ($discount / 100);
        }

        if ($this->discount_type == IdeaBoardItem::FIXED) {
            return $discount;
        }

        return 0;
    }
}
