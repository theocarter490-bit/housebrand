<?php

namespace App\Http\Controllers;

use App\Models\OrderPaymentDetail;
use App\Models\Role;
use App\Models\Event;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Product;
use App\Models\OrderClaim;
use App\Models\NoticeBoard;
use App\Services\Ga4AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DesignerSharedProduct;
use App\Models\ExpenseType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use function PHPSTORM_META\map;

class DashboardController extends Controller
{
    public function dashboard()
    {


        $data = [];
        $data['popularProducts'] = $this->getPopularProducts();
        $data['recentNotice'] = $this->getNotice();

        return view('dashboard', compact('data'));
    }


    private function getOrderClaims()
    {
        return OrderClaim::whereHas('order', function ($query) {
            $query->isClient('seller_id');
        })
            ->select('status', DB::raw('COUNT(id) as total'))
            ->groupBy('status')
            ->get();
    }

    private function getPopularProducts()
    {
        $popularProductsQuery = Product::with('shop')->isClient()->orderBy('num_of_sale', 'desc')->limit(5);

        return $popularProductsQuery->get();
    }

    private function getNotice()
    {
        return NoticeBoard::isClient('created_by')
            ->where('active_status', 1)
            ->orderBy('published_at', 'desc')
            ->where(function (Builder $query) {
                $userId = Auth::user()->id;
                $query->where('created_by', $userId)
                    ->orWhereJsonContains('receivers', str($userId));
            })
            ->limit(5)
            ->get();
    }

    private function getRoleWiseUserCount()
    {
        return Role::join('users', 'roles.id', 'users.role_id')->whereIn('roles.id', [Role::DESIGNER, Role::MANUFACTURER, Role::CUSTOMER])
            ->select('roles.name', DB::raw('COUNT(users.id) as total'))
            ->groupBy('roles.name')
            ->get();

    }

    private function getTotalProducts()
    {
        return Product::isClient()->count();
    }

    private function getTotalSharedProducts()
    {
        return DesignerSharedProduct::isClient('seller_id')->count();
    }

    private function getTotalEvents()
    {
        return Event::count();
    }

    private function getTotalExpense()
    {
        return Expense::sum('amount');
    }


    public function getOrderCount(Request $request)
    {
        $customerOrderCount = Order::select('order_statuses.name', 'order_statuses.color', DB::raw('COUNT(orders.id) as total'))
            ->join('order_statuses', 'orders.status', '=', 'order_statuses.id')
            ->isClient('seller_id');
        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $customerOrderCount = $customerOrderCount->where('code', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('status_type') && $request->status_type != "") {
            $customerOrderCount = $customerOrderCount->where('status', $request->status_type);
        }
        if ($request->has('payment_status') && $request->payment_status != "") {
            $customerOrderCount = $customerOrderCount->where('payment_status', $request->payment_status);
        }
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $customerOrderCount = $customerOrderCount->whereBetween('order_date', [$request->start_date, $request->end_date]);

        }

        $customerOrderCount = $customerOrderCount->groupBy('status')
            ->get();

        return response()->json(['orderCount' => $customerOrderCount]);
    }

    public function getOrderPaymentCount(Request $request)
    {
        $orderPaymentCount = Order::isClient('seller_id');

        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $orderPaymentCount = $orderPaymentCount->where('code', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('status_type') && $request->status_type != "") {
            $orderPaymentCount = $orderPaymentCount->where('status', $request->status_type);
        }
        if ($request->has('payment_status') && $request->payment_status != "") {
            $orderPaymentCount = $orderPaymentCount->where('payment_status', $request->payment_status);
        }
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $orderPaymentCount = $orderPaymentCount->whereBetween('order_date', [$request->start_date, $request->end_date]);

        }
        $orderPaymentCount = $orderPaymentCount->sum('grand_total_amount');

        $paidAmount = OrderPaymentDetail::whereHas('order', function ($q) use ($request) {
            $q->isClient('seller_id');
            if ($request->has('search') && $request->search != "") {
                $search = $request->search;
                $q->where('code', 'like', '%' . strtoupper($search) . '%');
            }
            if ($request->has('status_type') && $request->status_type != "") {
                $q->where('status', $request->status_type);
            }
            if ($request->has('payment_status') && $request->payment_status != "") {

                $q->where('payment_status', $request->payment_status);
            }
            if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
                $q->whereBetween('order_date', [$request->start_date, $request->end_date]);
            }
        });

        $paidAmount = $paidAmount->sum('amount');
        $dueAmount = max(0, $orderPaymentCount - $paidAmount);

        $orderPaymentTotal = collect([[
            'name' => 'Paid',
            'total' => round($paidAmount, 2),
        ], [
            'name' => 'Unpaid',
            'total' => round($dueAmount, 2),
        ]]);


        return response()->json(['orderPaymentCount' => $orderPaymentTotal]);
    }

    public function orderClaimCount()
    {
        $orderClaimCount = OrderClaim::whereHas('order', function ($q) {
            $q->isClient('seller_id');
        })->select('status', DB::raw('COUNT(order_claims.id) as total'));


        $orderClaimCount = $orderClaimCount->groupBy('status')
            ->get();

        return response()->json(['orderClaimCount' => $orderClaimCount]);
    }

    public function productStatusCount(Request $request)
    {
        $productStatusCount = DB::table('products')->select('is_published', DB::raw('COUNT(products.id) as total'));
        if (Auth::user()->role_id == Role::DESIGNER || Auth::user()->role_id == Role::MANUFACTURER) {
            $productStatusCount = $productStatusCount->where('user_id', Auth::user()->id);
        }

        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $productStatusCount = $productStatusCount->where('name', 'like', '%' . strtoupper($search) . '%');
            $productStatusCount = $productStatusCount->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('publish_status') && $request->publish_status != "") {

            $publish_status = $request->publish_status;
            if ($publish_status == 1) {
                $productStatusCount = $productStatusCount->where('is_published', 1);
            } else {
                $productStatusCount = $productStatusCount->where('is_published', 0);
            }
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $productStatusCount = $productStatusCount->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $productStatusCount = $productStatusCount->groupBy('is_published')
            ->get();

        return response()->json(['productStatusCount' => $productStatusCount]);
    }

    public function topProductSaleCount(Request $request)
    {
        $products = Product::withCount('orderItem')->isClient();
        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $products = $products->where('name', 'like', '%' . $search . '%');
        }
        if ($request->has('status_type') && $request->status_type != "") {
            $products = $products->where('status', $request->status_type);
        }
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $products = $products->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        $products = $products->orderBy('order_item_count', 'desc')->paginate(10);

        return response()->json(['productCount' => $products]);
    }

    public function userRoleCount(Request $request)
    {
        $userRoleCount = User::select('roles.name', DB::raw('COUNT(users.role_id) as total'))
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->whereIn('roles.id', [Role::CUSTOMER, Role::MANUFACTURER, Role::DESIGNER]);

        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $userRoleCount = $userRoleCount->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('role_type') && $request->role_type != "") {
            $userRoleCount = $userRoleCount->where('role_id', $request->role_type);
        }
        if ($request->has('active_status') && $request->active_status != "") {
            $userRoleCount = $userRoleCount->where('users.active_status', $request->active_status);
        }
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $userRoleCount = $userRoleCount->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $userRoleCount = $userRoleCount->groupBy('role_id')
            ->get();
        return response()->json(['userRoleCount' => $userRoleCount]);
    }

    // expenseGraph
    public function expenseGraph()
    {
        $expenseTypes = ExpenseType::withSum('expenses', 'amount')
            ->whereUserId(getUserId())
            ->where('is_active', 1)
            ->has('expenses')
            ->get();

//        $totalAmount = $expenseTypes->sum('expenses_sum_amount');

        return response()->json(['expenseGraph' => $expenseTypes]);
    }

    public function googleAnalytics(Request $request)
    {

        $ga = new Ga4AnalyticsService();
        $analysisData = $ga->getUserAndPageViews($request->data);
        return response()->json(['analysisData' => $analysisData]);
    }

    public function googlePageView(Request $request)
    {

        $ga = new Ga4AnalyticsService();
        $pageViewData = $ga->getPagesViews($request->data);
        return response()->json(['pageView' => $pageViewData]);
    }

    public function googleTopCountries(Request $request)
    {
        $ga = new Ga4AnalyticsService();
        $topCountries = $ga->getTopCountries($request->data);
        return response()->json(['pageView' => $topCountries]);
    }

}
