<?php

namespace App\View\Components;

use App\Models\Order;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class WelcomeMessage extends Component
{
    /**
     * Create a new component instance.
     */
    public $data = [];

    public function __construct()
    {
        $this->data['orderCount'] = Order::isClient('seller_id')->whereBetween('order_date',
            [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateString()
            ])->count();
        $this->data['orderAmount'] = Order::isClient('seller_id')->whereBetween('order_date',
            [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateString()
            ])->sum('grand_total_amount');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $data = $this->data;
        return view('components.welcome-message', compact('data'));
    }
}
