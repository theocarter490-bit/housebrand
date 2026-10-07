<?php

namespace App\View\Components;

use Closure;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Order;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class BestSeller extends Component
{
    /**
     * Create a new component instance.
     */
    public $bestSeller;
    public $month;
    public $order_count;
    public function __construct()
    {
        $topOrders = Order::with('designer')
            ->select('seller_id', DB::raw('COUNT(*) as order_count'))
            ->whereMonth('order_date', Carbon::now()->month)
            ->whereYear('order_date', Carbon::now()->year)
            ->groupBy('seller_id')
            ->orderByDesc('order_count')
            ->with(['designer' => function ($query) {
                $query->select('id', 'name'); // Select relevant columns
            }])
            ->first();

        $this->bestSeller = $topOrders->designer ?? null;
        $this->order_count = $topOrders->order_count ?? 0;
        $this->month = Carbon::now()->format('F');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $data = $this->bestSeller;
        $order_count = $this->order_count;
        $month = $this->month;

        return view('components.best-seller', compact('data', 'month', 'order_count'));
    }
}
