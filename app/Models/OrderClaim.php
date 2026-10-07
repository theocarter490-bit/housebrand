<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrderClaim extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    const PENDING = '0';
    const ACCEPTED = '1';
    const Closed = '2';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderClaimIssueType()
    {
        return $this->belongsTo(OrderClaimIssueType::class, 'order_claim_issue_type_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function replies()
    {
        return $this->hasMany(OrderClaimReply::class);
    }



    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                return match ($value) {
                    self::PENDING => 'Pending',
                    self::ACCEPTED => 'Accepted',
                    self::Closed => 'Closed',
                    default => 'Pending',
                };
            }
        );
    }
}
