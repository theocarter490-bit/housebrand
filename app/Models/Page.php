<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Page extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use CommonQueryTraits;

    const pages = ['About Us', 'Contact Us','Our Story','Team', 'Blog', 'Privacy Policy', 'Terms & Conditions', 'Return Policy', 'Shipping Policy', 'FAQ'];

    public  function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function footer()
    {
        return $this->belongsTo(FooterWidget::class, 'footer_widget_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(PageSectionItems::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function sliders()
    {
        return $this->hasMany(Slider::class, 'user_id','user_id')->where('type',1);
    }
}
