<?php

namespace App\View\Components;

use App\Models\Expense;
use App\Models\OrderPaymentDetail;
use App\Models\SubscriptionItem;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EarningReport extends Component
{
    /**
     * Create a new component instance.
     */
    public $totalExpense;
    public $thisMonthExpense;
    public $totalSubscriptionAmount;
    public $orderPaidAmount;

    public function __construct()
    {
        $this->totalExpense = Expense::where('created_by', getUserId())
            ->where('active_status', 1)
            ->sum('amount');

        $this->totalSubscriptionAmount = SubscriptionItem::where('payment_status', 'paid')->sum('price');
        $dateS = Carbon::now()->startOfMonth()->subMonth(0);
        $dateE = Carbon::now()->endOfMonth();
        $this->thisMonthExpense = Expense::where('created_by', getUserId())
            ->whereBetween('expense_date', [$dateS, $dateE])
            ->where('active_status', 1)
            ->sum('amount');
        $this->orderPaidAmount = OrderPaymentDetail::whereHas('order', function ($q) {
            $q->isClient('seller_id');
        })->sum('amount');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.earning-report');
    }
}
