<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class NoticeBoard extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use CommonQueryTraits;

    protected $fillable =[
        'title',
        'type_id',
        'published_at',
        'receivers',
        'description',
        'attachments',
        'active_status'
    ];
    public function noticeType()
    {
        return $this->belongsTo(NoticeType::class, 'type_id');
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


}
