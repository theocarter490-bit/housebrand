<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SpecialSection extends Model implements Auditable
{
    use CommonQueryTraits;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'title',
        'image',
        'user_id',
        'is_active',
        'created_by',
        'special_section_category_id',
        'slug',
        'type',
    ];

    public function category()
    {
        return $this->belongsTo(SpecialSectionCategory::class, 'special_section_category_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(SpecialSectionDetail::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
