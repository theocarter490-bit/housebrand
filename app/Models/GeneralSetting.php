<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneralSetting extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    public function currency()
    {
        return $this->hasOne(Currencies::class,'id','currency_id');
    }

    public function timezone()
    {
        return $this->hasOne(TimeZone::class,'id','time_zone_id');
    }

    public function DateFormat()
    {
        return $this->hasOne(DateFormat::class,'id','date_format_id');
    }
}
