<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskLabel extends Model
{
    use HasFactory;
    use CommonQueryTraits;

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
