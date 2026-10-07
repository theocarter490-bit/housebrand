<?php

namespace App\Models;

use App\Http\Traits\FileUploadTrait;
use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use CommonQueryTraits;
    use HasFactory;

    public function category(){
        return $this->belongsTo(ProjectCategory::class,'project_category_id','id');
    }

    public function client(){
        return $this->belongsTo(User::class,'client_id','id');
    }

    public function status()
    {
        return $this->hasOne(ProjectStatus::class,'id','project_status_id',);
    }

    public function manager(){
        return $this->belongsTo(User::class,'project_manager_id','id');
    }

    public function IdeaBoard(){
        return $this->hasMany(IdeaBoard::class,'project_id','id');
    }

    public function timeBillings()
    {
        return $this->hasMany(TimeBilling::class,'project_id','id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }
    public function taskLabels()
    {
        return $this->hasMany(TaskLabel::class, 'project_id', 'id');
    }
    public function taskStatuses()
    {
        return $this->hasMany(TaskStatus::class, 'project_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(ProjectProposalInvoice::class, 'project_id', 'id');
    }
}
