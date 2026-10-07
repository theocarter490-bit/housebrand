<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Task extends Model
{
    use HasFactory;
    use HasJsonRelationships;

    protected $fillable = ['task_status_id'];

    protected $casts = [
        'assigned_users' => 'array',
    ];


    public function taskLabel()
    {
        return $this->belongsTo(TaskLabel::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function taskThumb()
    {
        return $this->belongsTo(TaskFile::class, 'id', 'task_id');
    }

    public function taskFiles()
    {
        return $this->hasMany(TaskFile::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(TaskActivityLog::class);
    }

    public function taskComments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToJson(User::class, 'assigned_users');
    }


}
