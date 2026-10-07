<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CommonQueryTraits;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NoticeType extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use CommonQueryTraits;

    public function notices(){
        return $this->hasMany(NoticeBoard::class, 'type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
