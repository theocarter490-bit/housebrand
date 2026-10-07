<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CommonQueryTraits;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model implements Auditable
{
    use HasFactory;
    use CommonQueryTraits;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'title',
        'type_id',
        'expense_date',
        'amount',
        'voucher',
        'details',
        'active_status',
    ];

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'type_id');
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // paymentMethod
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

}
