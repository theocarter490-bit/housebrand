<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CommonQueryTraits;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use CommonQueryTraits;
    use HasFactory;
    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $guarded = [];

    protected $casts = [
        'attributes' => 'array',
        'choice_options' => 'array',
        'weight_dimensions' => 'array',
        'specifications' => 'array',
    ];

    protected $appends = ["discount_price", 'discount_percentage'];

    const PERCENTAGE = 1;
    const FIXED = 2;

    public function choiceOptions()
    {
        return $this->belongsToJson(Attribute::class, 'choice_options[]->attribute_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function sharedProduct()
    {
        return $this->hasMany(DesignerSharedProduct::class);
    }

    protected function getDiscountPriceAttribute()
    {
        $discount = $this->attributes['discount'];

        if ($this->discount_type == Product::PERCENTAGE) {
            return $this->unit_price - ($this->unit_price * ($discount / 100));
        }
        if ($this->discount_type == Product::FIXED) {
            return $this->unit_price - $discount;
        }
        return null;
    }

    protected function getDiscountPercentageAttribute()
    {
        if ($this->discount_type == Product::PERCENTAGE) {
            return $this->discount;
        }
        if ($this->discount_type == Product::FIXED) {
            return ($this->discount / $this->unit_price) * 100;
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shop()
    {
        return $this->belongsTo(ShopSetting::class, 'user_id', 'user_id');
    }
    
    public function whiteLableProduct()
    {
        return $this->belongsTo(Product::class, 'parent_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReviews::class);
    }

    public function getVariantMinDiscountPriceAttribute()
    {
        if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            return $this->variants->min(function ($v) {
                if ($v->discount_type == ProductVariant::PERCENTAGE) {
                    return $v->price - ($v->price * ($v->discount_amount / 100));
                }
                if ($v->discount_type == ProductVariant::FIXED) {
                    return $v->price - $v->discount_amount;
                }
                return $v->price; // no discount
            });
        }
        return null;
    }

    public function getVariantMaxDiscountPriceAttribute()
    {
        if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            return $this->variants->max(function ($v) {
                if ($v->discount_type == ProductVariant::PERCENTAGE) {
                    return $v->price - ($v->price * ($v->discount_amount / 100));
                }
                if ($v->discount_type == ProductVariant::FIXED) {
                    return $v->price - $v->discount_amount;
                }
                return $v->price; // no discount
            });
        }
        return null;
    }
}
