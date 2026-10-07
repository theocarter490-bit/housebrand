<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BlogPostContentDetail extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable =
    [
        'blog_post_id',
        'section_type',
        'is_active'
    ];

    public function items()
    {
        return $this->hasMany(BlogPostContentDetailItem::class, 'blog_detail_id');
    }


    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
