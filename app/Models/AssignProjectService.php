<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignProjectService extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projectService()
    {
        return $this->belongsTo(ProjectService::class, 'project_service_id', 'id');
    }

    public function timeBilling()
    {
        return $this->hasMany(TimeBilling::class, 'assign_project_service_id');
    }
}
