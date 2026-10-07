<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DesignerContact extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use CommonQueryTraits;
    protected $fillable=[
        'designer_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
    ];


    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }


}
