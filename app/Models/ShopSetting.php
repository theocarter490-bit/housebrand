<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopSetting extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $casts = ['conditions' => 'array'];
    protected $guarded = [];

    public function seller()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
