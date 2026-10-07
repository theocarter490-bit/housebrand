<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewType extends Model
{
    use HasFactory;

    public function productReviews()
    {
        return $this->hasMany(ProductReviews::class);
    }
    public function shopReviews()
    {
        return $this->hasMany(DesignerReview::class);
    }
}
