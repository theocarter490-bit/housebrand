<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CommonQueryTraits;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use CommonQueryTraits;


    const SUPER_ADMIN = 1;
    const ADMIN = 2;
    const DESIGNER = 3;
    const CUSTOMER = 4;
    const MANUFACTURER = 5;

    protected $casts = [
        'permissions' => 'array',
    ];



    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
