<?php

namespace App\Models\Traits;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;

trait CommonQueryTraits
{

    public function scopeIsClient($query, $columnName = 'user_id')
    {
        if (Auth::user()->supervisor_id != null) {
            $query = $query->where($columnName, Auth::user()->supervisor_id);
        }
        if (isSeller()) {
            $query = $query->where($columnName, Auth::user()->id);
        }
        return $query;
    }

    public function scopeByShop($query, $columnName = 'user_id')
    {
        if (Auth::user()->supervisor_id != null) {
            return $query->where($columnName, Auth::user()->supervisor_id);
        }
        if (isSeller()) {
            return $query->where($columnName, Auth::user()->id);
        }
        return $query->where($columnName, 1);
    }

    public function scopeCheckSubscription($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($q2) {
                $q2->where('subscription_required', 1)
                    ->where('is_subscribed', 1);
            })
                ->orWhere('subscription_required', 0)
                ->orWhere('trail_mode', 1);
        });
    }
}
