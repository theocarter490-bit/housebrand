<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CommonQueryTraits;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseType extends Model implements Auditable
{
    use HasFactory;
    use CommonQueryTraits;
    use \OwenIt\Auditing\Auditable;

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'type_id');
    }
}
