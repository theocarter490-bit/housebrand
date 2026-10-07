<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignerReview extends Model
{
    use HasFactory;
    use CommonQueryTraits;

    public function reviewType()
    {
        return $this->belongsTo(ReviewType::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id')->withDefault();
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id')->withDefault();
    }


}
