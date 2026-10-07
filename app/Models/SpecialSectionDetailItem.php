<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Illuminate\Database\Eloquent\Builder;

class SpecialSectionDetailItem extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use HasJsonRelationships;

    protected $casts = [
        'product_ids' => 'array', // Ensures the JSON column is cast to an array
    ];

    /**
     * Define the relationship with the Product model.
     */
    public function products()
    {
        return $this->belongsToJson(Product::class,'product_ids');
    }
}
