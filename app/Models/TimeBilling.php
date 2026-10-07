<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeBilling extends Model
{
    use HasFactory;

    const BILLABLE = 1;
    const NON_BILLABLE = 0;

    protected $fillable = ['project_id', 'client_id', 'employee_id', 'assign_project_service_id', 'rate', 'description', 'active_status', 'user_id', 'code', 'bill_type', 'duration', 'total_amount'];
    protected $appends = ['bill_type_name'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(AssignProjectService::class, 'assign_project_service_id');
    }

    public function logs()
    {
        return $this->hasMany(TimeBillingLog::class);
    }

    public function currentActiveLog()
    {
        return $this->hasOne(TimeBillingLog::class)->where('end_time', null)->latest();
    }

    public function paymentDetails()
    {
        return $this->hasMany(TimeBillingPaymentDetail::class);
    }

    public function lastPayment()
    {
        return $this->belongsTo(TimeBillingPaymentDetail::class, 'id', 'time_billing_id')->latest();
    }

    public function getBillTypeNameAttribute()
    {
        return match ($this->bill_type) {
            1 => "Billable",
            2 => "Non Billable",
            default => "N/A"
        };
    }

}
