<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Slider extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasJsonRelationships;

    protected $fillable = [
        'description',
        'title',
        'user_id',
        'type',
        'active_status',
        'file_type',
        'image',
    ];


    protected $casts = [
        'product_ids' => 'array', // Ensures the JSON column is cast to an array
    ];

    public function products()
    {
        return $this->belongsToJson(Product::class, 'product_ids[]->id');
    }
}
