<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentMethod extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    public function gatewayCredentials()
    {
        return $this->hasMany(GatewayCredentials::class);
    }
    public function activeStatus()
    {
        return $this->hasOne(PaymentMethodStatus::class);
    }
}
