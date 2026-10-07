<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DesignerSharedProduct extends Model implements Auditable
{
    use CommonQueryTraits;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    const PENDING = 0;
    const APPROVED = 1;
    const CANCELED = 2;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                return match ($value) {
                    self::PENDING => 'Pending',
                    self::APPROVED => 'Approved',
                    self::CANCELED => 'Canceled',
                    default => 'Pending',
                };
            }
        );
    }
}
