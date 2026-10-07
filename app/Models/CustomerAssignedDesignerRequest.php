<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAssignedDesignerRequest extends Model
{
    use HasFactory;
    use CommonQueryTraits;

    const WAITING_FOR_APPROVAL = 0;
    const APPROVED = 1;
    const DECLINED = 2;
    const CANCELED = 3;

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function newDesigner()
    {
        return $this->belongsTo(User::class, 'new_designer_id');
    }

    public function currentDesigner()
    {
        return $this->belongsTo(User::class, 'current_designer_id');
    }
}
