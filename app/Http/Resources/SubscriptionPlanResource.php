<?php

namespace App\Http\Resources;

use App\Models\PlanModule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $setup_fee = $this->setup_fee;
        $user = Auth::guard('sanctum')->user();
        if ($user && $user->subscription && $user->subscription->isNotEmpty()) {
            $setup_fee = 0;
        }

        $allModules = PlanModule::where('active_status', 1)->get();
        $planModules = collect(json_decode($this->modules ?? '[]'))->keyBy('slug');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'setup_fee' => $setup_fee,
            'plan_type' => $this->plan_type,
            'is_popular' => (bool) $this->is_popular,
            'is_free_trail' => (bool) $this->is_free_trail,
            'number_of_employer' => $this->number_of_employer,
            'modules' => $allModules->map(function ($module) use ($planModules) {
                $planModule = $planModules->get($module->slug);
                return [
                    'name'  => ucfirst(str_replace('-', ' ', $module->slug)),
                    'slug'  => $module->slug,
                    'limit' => $planModule ? $planModule->limit : null,
                ];
            })->values(),
        ];
    }
}
