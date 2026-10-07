<?php

namespace App\Http\Resources;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'                => $this->id,
            'name'              => $this->name,
            'phone'             => $this->phone,
            'email'             => $this->email,
            'address'           => $this->address,
            'avatar'            => asset(getFilePath($this->avatar)),
            'status'            => $this->active_status,
            'role'              => $this->role->name,
            'role_id'           => $this->role_id,
        ];

        if ($this->role_id == Role::CUSTOMER) {
            $data['designer_id'] = $this->designer_id;
        }elseif ($this->role_id == Role::DESIGNER || $this->role_id == Role::MANUFACTURER) {
            $data['subscription_required'] = $this->subscription_required;
            $data['is_subscribed'] = $this->is_subscribed;
            $data['trial_mode'] = $this->trail_mode;
            $data['plan_id'] = $this->lastSubscription && $this->lastSubscription->stripe_status == 'active'? $this->lastSubscription->plan_id : null;
        }
        $data['shop_slug'] = "";
        if ($this->role_id == Role::DESIGNER) {
            $data['shop_slug'] = $this->shop->slug;
        } else if ($this->role_id == Role::CUSTOMER) {
            $data['shop_slug'] = $this->designer->shop->slug;
            $data['my_designer'] = ShopResource::make($this->designer->shop);
        }
        $data['active_status'] = $this->active_status == 1 ? true : false;
        $data['is_email_verified'] = $this->hasVerifiedEmail();

        return $data;
    }
}
