<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeSignupCode extends Model
{
    use HasFactory;

    public function uses()
    {
        return $this->hasMany(TrailCodeUse::class);
    }
}
