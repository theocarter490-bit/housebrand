<?php

namespace App\View\Components;

use App\Models\Order;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class OderStatusGraph extends Component
{
    /**
     * Create a new component instance.
     */
    public $orderCount;
    public function __construct()
    {
        $this->orderCount = Order::select('order_statuses.name', 'order_statuses.color', DB::raw('COUNT(orders.id) as total'))
            ->join('order_statuses', 'orders.status', '=', 'order_statuses.id')
            ->isClient('seller_id')
            ->groupBy('status')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.oder-status-graph');
    }
}
