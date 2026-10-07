<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeBillingLog extends Model
{
    use HasFactory;

    protected $fillable = ['time_billing_id', 'start_time', 'end_time', 'duration'];

    public function getStartTimeAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->setTimezone(env('app_timezone'))->toISOString();
    }

    public function getEndTimeAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->setTimezone(env('app_timezone'))->toISOString() : null;
    }
}
