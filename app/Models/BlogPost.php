<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BlogPost extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use CommonQueryTraits;

    protected $fillable = [
        'title', 'slug', 'desc', 'thumbnail', 'banner', 'video_url', 'tags',
        'meta_title', 'meta_desc', 'view_count', 'serial_no', 'active_status',
        'category_id', 'created_by', 'updated_by','user_id'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function contentDetails()
    {
        return $this->hasMany(BlogPostContentDetail::class, 'blog_post_id')->orderBy('serial', 'asc');
    }

    public function category(){
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }




}
