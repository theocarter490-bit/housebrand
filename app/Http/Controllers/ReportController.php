<?php

namespace App\Http\Controllers;

use App\Models\DesignerSharedProduct;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Order;
use App\Models\OrderPaymentDetail;
use App\Models\OrderStatus;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Role;
use App\Models\SearchKeyword;
use App\Models\ShopSetting;
use App\Models\SubscriptionItem;
use App\Models\SubscriptionPaymentLog;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Intervention\Image\Colors\Rgb\Channels\Red;

class ReportController extends Controller
{
    // expenseReport
    public function expenseReport(Request $request)
    {
        $start_date = "";
        $end_date = "";
        $search = "";
        $expense_type = "";

        $expenses = Expense::with('expenseType')
            ->where('created_by', getUserId())->where('active_status', 1);

        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $expenses = $expenses->where('title', 'like', '%' . $search . '%');
        }

        if ($request->has('expense_type') && $request->expense_type != "") {
            $expenses = $expenses->where('type_id', $request->expense_type);
            $expense_type = $request->expense_type;
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $expenses = $expenses->whereBetween('expense_date', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $allExpenses = $expenses->orderBy('id', 'desc')->get();
        $expenses = $expenses->orderBy('id', 'desc')->paginate(10);

        $currentMonthExpense = Expense::where('created_by', getUserId())
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $expTypes = ExpenseType::with('expenses')->byShop('user_id')->where('is_active', 1)->get();

        return view('reports.expense.expense_report', compact('expenses', 'allExpenses', 'start_date', 'end_date', 'expTypes', 'search', 'expense_type', 'currentMonthExpense'));
    }


    public function expenseDetails($id)
    {
        $data = Expense::where('id', $id)->first();
        $expense = [
            'title' => $data->title,
            'type' => $data->expenseType->name,
            'expense_date' => $data->expense_date,
            'amount' => getPriceFormat($data->amount),
            'details' => $data->details,
            'voucher' => $data->attachment_element,
        ];
        return response()->json($expense);
    }


    public function orderReport(Request $request)
    {
        $start_date = "";
        $end_date = "";
        $search = "";
        $status_type = '';
        $payment_status = '';

        $orders = Order::with('designer', 'user', 'orderStatus')->isClient('seller_id');

        if ($request->has('search') && $request->search != "") {
            $search = $request->search;

            $orders = $orders->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . strtoupper($search) . '%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('designer', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }
        if ($request->has('status_type') && $request->status_type != "") {

            $orders = $orders->where('status', $request->status_type);
            $status_type = $request->status_type;;
        }
        if ($request->has('payment_status') && $request->payment_status != "") {

            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }


        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $orders = $orders->whereBetween('order_date', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        // $allorders = $orders->orderBy('id', 'desc')->get();
        $orders = $orders->orderBy('id', 'desc')->paginate(10);
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

        $totalOrderAmount = Order::whereIn('status', [OrderStatus::DELIVERED, OrderStatus::PROCESSED, OrderStatus::CONFIRMED, OrderStatus::PARTIAL_DELIVERY, OrderStatus::SHIPPED])->isClient('seller_id');
        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $totalOrderAmount = $totalOrderAmount->where('code', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('status_type') && $request->status_type != "") {

            $totalOrderAmount = $totalOrderAmount->where('status', $request->status_type);
        }
        if ($request->has('payment_status') && $request->payment_status != "") {

            $totalOrderAmount = $totalOrderAmount->where('payment_status', $request->payment_status);
        }
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $totalOrderAmount = $totalOrderAmount->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        $totalOrderAmount = $totalOrderAmount->sum('grand_total_amount');
        $dueAmount = $totalOrderAmount - $paidAmount;
        $orderStatues = OrderStatus::all();
        return view('reports.order.order-report', compact('orders', 'orderStatues', 'status_type', 'payment_status', 'start_date', 'end_date', 'search', 'paidAmount', 'dueAmount', 'totalOrderAmount'));
    }

    public function searchKeywordReport(Request $request)
    {
        $searchQuery = SearchKeyword::query();
        $search = '';
        if ($request->filled('search')) {
            $searchQuery->where('keyword', 'like', '%' . $request->search . '%');
            $search = $request->search;
        }

        if ($request->filled('sort_by')) {
            $sortDirection = $request->sort_by == 0 ? 'desc' : 'asc';
            $searchQuery->orderBy('count', $sortDirection);
        }

        $data = $searchQuery->paginate(10);
        return view('reports.search_keyword_report', compact('data', 'search'));
    }

    // expenseReportExport
    public function expenseReportExport(Request $request)
    {
        $start_date = "";
        $end_date = "";
        $search = "";
        $expense_type = "";
        $typeName = "";

        $expenses = Expense::with('expenseType')->where('created_by', getUserId())->where('active_status', 1);

        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $expenses = $expenses->where('title', 'like', '%' . $search . '%');
        }

        if ($request->has('expense_type') && $request->expense_type != "") {
            $expenses = $expenses->where('type_id', $request->expense_type);
            $expense_type = $request->expense_type;
            $typeName = ExpenseType::where('id', $expense_type)->first()->name;
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $expenses = $expenses->whereBetween('expense_date', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $allExpenses = $expenses->orderBy('id', 'desc')->get();
        $expenses = $expenses->orderBy('id', 'desc')->get();

        $pdf = \PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('reports.expense.expense_report_export', compact('expenses', 'start_date', 'end_date', 'expense_type', 'typeName'));
        return $pdf->stream();
    }

    public function orderReportExport(Request $request)
    {
        $start_date = "";
        $end_date = "";
        $search = "";
        $status_type = '';
        $typeName = '';

        $orders = Order::with('designer', 'user', 'orderStatus')->isClient('seller_id');

        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $orders = $orders->where('code', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('status_type') && $request->status_type != "") {

            $orders = $orders->where('status', $request->status_type);
        }
        if ($request->has('payment_status') && $request->payment_status != "") {

            $orders = $orders->where('payment_status', $request->payment_status);
        }


        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $orders = $orders->whereBetween('order_date', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $orders = $orders->orderBy('id', 'desc')->get();


        $pdf = \PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('reports.order.order_report_export', compact('orders', 'start_date', 'end_date', 'status_type', 'typeName'));
        return $pdf->stream();
    }

    public function productReport(Request $request)
    {
        $search = "";
        $view_by = '';
        $publish_status = '';
        $start_date = "";
        $end_date = "";

        $products = Product::withCount('orderItem')->with('user', 'shop')->isClient();

        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $products = $products->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('view_by') && $request->view_by != "") {
            $view_by = $request->view_by;
            if ($view_by == 1) {
                $products = $products->orderBy('order_item_count', 'desc');
            } else {
                $products = $products->orderBy('view_count', 'desc');
            }
        }
        if ($request->has('publish_status') && $request->publish_status != "") {

            $publish_status = $request->publish_status;
            if ($publish_status == 1) {
                $products = $products->where('is_published', 1);
            } else {
                $products = $products->where('is_published', 0);
            }
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $products = $products->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $products = $products->paginate(perPage());
        $productCount = Product::isClient()->count();
        $publishedProductCount = Product::where('is_published', 1)->isClient()->count();
        $unPublishedProductCount = Product::where('is_published', 0)->isClient()->count();

        return view('reports.product.report', compact('products', 'search', 'productCount', 'publishedProductCount', 'unPublishedProductCount', 'view_by', 'publish_status', 'start_date', 'end_date'));
    }

    public function productReportPDF(Request $request)
    {

        $view_by = '';
        $publish_status = '';
        $start_date = "";
        $end_date = "";

        $products = Product::withCount('orderItem')->with('user', 'shop')->isClient();

        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $products = $products->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('view_by') && $request->view_by != "") {
            $view_by = $request->view_by;
            if ($view_by == 1) {
                $products = $products->orderBy('order_item_count', 'desc');
            } else {
                $products = $products->orderBy('view_count', 'desc');
            }
        }
        if ($request->has('publish_status') && $request->publish_status != "") {

            $publish_status = $request->publish_status;
            if ($publish_status == 1) {
                $products = $products->where('is_published', 1);
            } else {
                $products = $products->where('is_published', 0);
            }
        }
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $products = $products->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $products = $products->get();

        $pdf = \PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('reports.product.report_export', compact('products', 'view_by', 'publish_status', 'start_date', 'end_date'));
        return $pdf->stream();
    }

    public function userReport(Request $request)
    {
        $search = "";
        $role_type = '';
        $active_status = '';
        $start_date = "";
        $end_date = "";

        $users = User::whereIn('role_id', [Role::DESIGNER, Role::MANUFACTURER, Role::CUSTOMER]);

        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $users = $users->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('role_type') && $request->role_type != "") {
            $role_type = $request->role_type;

            $users = $users->where('role_id', $request->role_type);
        }
        if ($request->has('active_status') && $request->active_status != "") {

            $active_status = $request->active_status;

            $users = $users->where('active_status', $request->active_status);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $users = $users->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $users = $users->paginate(perPage());
        $customerCount = User::where('role_id', Role::CUSTOMER)->count();
        $designerCount = User::where('role_id', Role::DESIGNER)->count();
        $manufacturerCount = User::where('role_id', Role::MANUFACTURER)->count();
        return view('reports.user.report', compact('users', 'search', 'customerCount', 'designerCount', 'manufacturerCount', 'role_type', 'active_status', 'start_date', 'end_date'));
    }

    public function userReportPDF(Request $request)
    {
        $search = "";
        $role_type = '';
        $active_status = '';
        $start_date = "";
        $end_date = "";

        $users = User::whereIn('role_id', [Role::DESIGNER, Role::MANUFACTURER, Role::CUSTOMER]);

        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $users = $users->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('role_type') && $request->role_type != "") {
            $role_type = $request->role_type;

            $users = $users->where('role_id', $request->role_type);
        }
        if ($request->has('active_status') && $request->active_status != "") {

            $active_status = $request->active_status;

            $users = $users->where('active_status', $request->active_status);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $users = $users->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $users = $users->get();

        $pdf = \PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('reports.user.report_export', compact('users', 'role_type', 'active_status', 'start_date', 'end_date'));
        return $pdf->stream();
    }

    public function subscriptionReport(Request $request)
    {
        $search = "";
        $recuring_type = '';
        $start_date = "";
        $end_date = "";

        $plans = Plan::where('is_active', 1)->withCount('subscription');

        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $plans = $plans->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('recuring_type') && $request->recuring_type != "") {
            $recuring_type = $request->recuring_type;

            $plans = $plans->where('plan_type', $request->recuring_type);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $plans = $plans->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $plans = $plans->paginate(perPage());

        $totalActivePlan = Plan::where('is_active', 1);
        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $totalActivePlan = $totalActivePlan->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('recuring_type') && $request->recuring_type != "") {


            $totalActivePlan = $totalActivePlan->where('plan_type', $request->recuring_type);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $totalActivePlan = $totalActivePlan->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $totalActivePlan = $totalActivePlan->count();
        $totalUsedPlan = Plan::where('is_active', 1)->has('subscription');
        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $totalUsedPlan = $totalUsedPlan->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('recuring_type') && $request->recuring_type != "") {
            $totalUsedPlan = $totalUsedPlan->where('plan_type', $request->recuring_type);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $totalUsedPlan = $totalUsedPlan->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $totalUsedPlan = $totalUsedPlan->count();
        $totalNonUsedPlan = Plan::where('is_active', 1);
        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $totalNonUsedPlan = $totalNonUsedPlan->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('recuring_type') && $request->recuring_type != "") {
            $totalNonUsedPlan = $totalNonUsedPlan->where('plan_type', $request->recuring_type);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $totalNonUsedPlan = $totalNonUsedPlan->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $totalNonUsedPlan = $totalNonUsedPlan->has('subscription', '<', 1)->count();
        $totalSubscriptionAmount = SubscriptionItem::sum('price');

        return view('reports.subscription.report', compact('plans', 'search', 'totalActivePlan', 'totalUsedPlan', 'totalNonUsedPlan', 'totalSubscriptionAmount', 'recuring_type', 'start_date', 'end_date'));
    }

    public function subscriptionReportPDF(Request $request)
    {
        $search = "";
        $recuring_type = '';
        $start_date = "";
        $end_date = "";

        $plans = Plan::where('is_active', 1)->withCount('subscription');

        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $plans = $plans->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('recuring_type') && $request->recuring_type != "") {
            $recuring_type = $request->recuring_type;

            $plans = $plans->where('plan_type', $request->recuring_type);
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $plans = $plans->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $plans = $plans->get();


        $pdf = \PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('reports.subscription.report_export', compact('plans', 'start_date', 'end_date'));
        return $pdf->stream();
    }

    public function sharedProductReport(Request $request)
    {
        $search = "";
        $sort_by = '';
        $start_date = "";
        $end_date = "";

        $products = Product::has('sharedProduct')->with(['category', 'user.shop'])->withCount('sharedProduct')->isClient('user_id');

        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $products = $products->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('sort_by') && $request->sort_by != "") {
            $sort_by = $request->sort_by;
            if ($sort_by == 'most_shared') {
                $products = $products->orderBy('shared_product_count', 'desc');
            } elseif ($sort_by == 'less_shared') {
                $products = $products->orderBy('shared_product_count', 'asc');
            }
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $products = $products->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $products = $products->paginate(perPage());

        $totalSharedProduct = DesignerSharedProduct::isClient('seller_id')->count();
        $tatalDistinctProductShare = Product::isClient('user_id')->has('sharedProduct')->count();

        return view('reports.shared-product.report', compact('products', 'totalSharedProduct', 'tatalDistinctProductShare', 'sort_by', 'search', 'start_date', 'end_date'));
    }

    public function sharedProductReportPDF(Request $request)
    {
        $search = "";
        $sort_by = '';
        $start_date = "";
        $end_date = "";

        $products = Product::has('sharedProduct')->with('category')->withCount('sharedProduct');

        if ($request->has('search') && $request->search != "") {

            $search = $request->search;
            $products = $products->where('name', 'like', '%' . strtoupper($search) . '%');
        }
        if ($request->has('sort_by') && $request->sort_by != "") {
            $sort_by = $request->sort_by;
            if ($sort_by == 'most_shared') {
                $products = $products->orderBy('shared_product_count', 'desc');
            } elseif ($sort_by == 'less_shared') {
                $products = $products->orderBy('shared_product_count', 'asc');
            }
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != "" && $request->end_date != "") {
            $products = $products->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        $products = $products->get();


        $pdf = \PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('reports.shared-product.report_export', compact('products', 'sort_by', 'search', 'start_date', 'end_date'));
        return $pdf->stream();
    }

    public function sharedProductDesignerList($id)
    {
        $designers = DesignerSharedProduct::where('product_id', $id)->with('user.shop')->get();
        $designers = $designers->map(function ($designer) {
            return [
                'name' => $designer->user->name,
                'shop_name' => $designer->user->shop->shop_name,
                'email' => $designer->user->shop->email,
                'phone' => $designer->user->shop->phone,
                'logo' => getFilePath($designer->user->shop->logo),
            ];
        });

        return response()->json([
            'designers' => $designers,
        ]);
    }
}
