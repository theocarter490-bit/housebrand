<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BlogPostContentDetailItem extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable= [
        'blog_detail_id',
        'description',
        'image',
        'serial',
        'is_active'
    ];


    public function contentDetail()
    {
        return $this->belongsTo(BlogPostContentDetail::class, 'blog_detail_id');
    }
}
