<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Subscription;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\CommonQueryTraits;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Stripe\Review;

class User extends Authenticatable implements Auditable, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;
    use \OwenIt\Auditing\Auditable;
    use CommonQueryTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
    ];

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function shop()
    {
        return $this->hasOne(ShopSetting::class);
    }

    public function portfolio()
    {
        return $this->hasMany(SpecialSection::class)->where('type', 1);
    }

    public function inspiration()
    {
        return $this->hasMany(SpecialSection::class)->where('type', 2);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sellerOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function shippingAddress()
    {
        return $this->hasMany(ShippingAddress::class)->latest();
    }

    public function gatewayCredentials()
    {
        return $this->hasMany(GatewayCredentials::class);
    }

    public function paymentMethodStatus()
    {
        return $this->hasMany(PaymentMethodStatus::class);
    }

    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class, 'stripe_customer_id', 'stripe_id')->where('stripe_status', 'active');
    }

    public function lastSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->latestOfMany();
    }

    // wishlist
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function sharedProduct()
    {
        return $this->hasMany(DesignerSharedProduct::class, 'designer_id');
    }

    public function avgRating()
    {
        return $this->hasMany(DesignerReview::class, 'designer_id')->where('active_status', 1);
    }

    public function reviews()
    {
        return $this->hasMany(DesignerReview::class, 'designer_id')->where('active_status', 1);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function managerProjects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->whereJsonContains('assigned_users', $this->id);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // TrailCodeUse
    public function trailCodeUses()
    {
        return $this->hasMany(TrailCodeUse::class, 'user_id');
    }

    public function freeTrailCode()
    {
        return $this->hasOne(TrailCodeUse::class)->where('is_active', 1);
    }

    public function designerJoinRequest()
    {
        return $this->hasOne(CustomerAssignedDesignerRequest::class, 'new_designer_id')->where('customer_id', \Auth::user()->id);
    }

    public function assignedDesigners()
    {
        return $this->hasOne(DesignerCustomerAssignment::class, 'designer_id')->where('customer_id', \Auth::user()->id);
    }

}
