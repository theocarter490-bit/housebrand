<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrailCodeUse extends Model
{
    use HasFactory;

    protected $fillable = ['is_active','expire_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
