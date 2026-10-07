<?php

namespace App\Models;

use App\Http\Traits\FileUploadTrait;
use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasFactory;
    use CommonQueryTraits;


    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
