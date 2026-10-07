<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Plan extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasJsonRelationships;

    protected $fillable = [
        'is_popular',
    ];


    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }

    public function planModule()
    {
        return $this->belongsToJson(PlanModule::class,'module_ids','slug');
    }
}
