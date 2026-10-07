<?php

namespace App\View\Components;

use App\Models\OrderStatus;
use Closure;
use App\Models\Role;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\DesignerSharedProduct;
use Illuminate\Support\Facades\Auth;

class DashboardStatistic extends Component
{
    /**
     * Create a new component instance.
     */
    public $data;

    public function __construct()
    {
        $data = collect([]);
        if (Auth::user()->role_id == Role::MANUFACTURER) {
            $totalCount = User::join('orders', 'users.id', '=', 'orders.user_id')
                ->where('orders.seller_id', getUserId())
                ->distinct('users.id')
                ->count('users.id');

            $activeCount = User::join('orders', 'users.id', '=', 'orders.user_id')
                ->where('orders.seller_id', getUserId())
                ->where('users.active_status', 1)
                ->distinct('users.id')
                ->count('users.id');

            $inactiveCount = User::join('orders', 'users.id', '=', 'orders.user_id')
                ->where('orders.seller_id', getUserId())
                ->where('users.active_status', 0)
                ->distinct('users.id')
                ->count('users.id');

            $customer = collect([
                'value' => $totalCount,
                'label' => 'Traders',
                'icon_class' => 'ti ti-users',
                'bg_class' => 'bg-label-info',
                'border_class' => 'card-border-shadow-info',
                'stat_class' => 'text-info',
                'tooltip' => 'Total Traders on the platform',
                'items' => [
                    [
                        'label' => 'Active',
                        'stat_class' => 'text-success',
                        'value' => $activeCount,
                    ],
                    [
                        'label' => 'Inactive',
                        'stat_class' => 'text-danger',
                        'value' => $inactiveCount,
                    ],
                ],
                'start' => ($totalCount > 0) ? ($activeCount * 100 / $totalCount) : 0,
                'end' => ($totalCount > 0) ? (100 - ($activeCount * 100 / $totalCount)) : 0,
            ]);
        } else {
            $totalCount = User::isCLient('designer_id')->where('role_id', Role::CUSTOMER)->count();

            $activeCount = User::isCLient('designer_id')->where('role_id', Role::CUSTOMER)->where('active_status', 1)->count();
            $inactiveCount = User::isCLient('designer_id')->where('role_id', Role::CUSTOMER)->where('active_status', 0)->count();

            $customer = collect([
                'value' => $totalCount,
                'label' => 'Customer',
                'icon_class' => 'ti ti-users',
                'bg_class' => 'bg-label-info',
                'border_class' => 'card-border-shadow-info',
                'stat_class' => 'text-info',
                'tooltip' => 'Total customers on the platform',
                'items' => [
                    [
                        'label' => 'Active',
                        'stat_class' => 'text-success',
                        'value' => $activeCount,
                    ],
                    [
                        'label' => 'Inactive',
                        'stat_class' => 'text-danger',
                        'value' => $inactiveCount,
                    ],
                ],
                'start' => ($totalCount > 0) ? ($activeCount * 100 / $totalCount) : 0,
                'end' => ($totalCount > 0) ? (100 - ($activeCount * 100 / $totalCount)) : 0,
                'route' => route('user.index', Role::CUSTOMER),
            ]);
        }


        $data->put('customer', $customer);
        if (Auth::user()->role_id == Role::SUPER_ADMIN) {
            $totalCount = User::where('role_id', Role::DESIGNER)->count();
            $activeCount = User::where('role_id', Role::DESIGNER)->where('active_status', 1)->count();
            $inactiveCount = User::where('role_id', Role::DESIGNER)->where('active_status', 0)->count();

            $designer = collect([
                'value' => $totalCount,
                'label' => 'Designer',
                'icon_class' => 'ti ti-user-star',
                'bg_class' => 'bg-label-warning',
                'border_class' => 'card-border-shadow-warning',
                'stat_class' => 'text-warning',
                'tooltip' => 'Total designers on the platform',
                'items' => [
                    [
                        'label' => 'Active',
                        'stat_class' => 'text-success',
                        'value' => $activeCount,
                    ],
                    [
                        'label' => 'Inactive',
                        'stat_class' => 'text-danger',
                        'value' => $inactiveCount,
                    ],
                ],
                'start' => ($totalCount > 0) ? ($activeCount * 100 / $totalCount) : 0,
                'end' => ($totalCount > 0) ? (100 - ($activeCount * 100 / $totalCount)) : 0,
                'route' => route('user.index', Role::DESIGNER),
            ]);

            $data->put('designer', $designer);

            $manufacturerCount = User::where('role_id', Role::MANUFACTURER)->count();
            $activeCount = User::where('role_id', Role::MANUFACTURER)->where('active_status', 1)->count();
            $inactiveCount = User::where('role_id', Role::MANUFACTURER)->where('active_status', 0)->count();

            $manufacturer = collect([
                'value' => $manufacturerCount,
                'label' => 'Manufacturer',
                'icon_class' => 'ti ti-user-bolt',
                'bg_class' => 'bg-label-success',
                'border_class' => 'card-border-shadow-success',
                'stat_class' => 'text-success',
                'tooltip' => 'Total manufacturers on the platform',
                'items' => [
                    [
                        'label' => 'Active',
                        'stat_class' => 'text-success',
                        'value' => $activeCount,
                    ],
                    [
                        'label' => 'Inactive',
                        'stat_class' => 'text-danger',
                        'value' => $inactiveCount,
                    ],
                ],
                'start' => ($manufacturerCount > 0) ? ($activeCount * 100 / $manufacturerCount) : 0,
                'end' => ($manufacturerCount > 0) ? (100 - ($activeCount * 100 / $manufacturerCount)) : 0,
                'route' => route('user.index', Role::MANUFACTURER),
            ]);

            $data->put('manufacturer', $manufacturer);
        }

        $totalCount = Product::isClient('user_id')->count();
        $activeCount = Product::isClient('user_id')->where('is_published', 1)->count();
        $inactiveCount = Product::isClient('user_id')->where('is_published', 0)->count();

        $product = collect([
            'value' => $totalCount,
            'label' => 'Products',
            'icon_class' => 'ti ti-building-factory-2',
            'bg_class' => 'bg-label-primary',
            'border_class' => 'card-border-shadow-primary',
            'stat_class' => 'text-primary',
            'tooltip' => 'Total products on the platform',
            'items' => [
                [
                    'label' => 'Active',
                    'stat_class' => 'text-success',
                    'value' => $activeCount,
                ],
                [
                    'label' => 'Inactive',
                    'stat_class' => 'text-danger',
                    'value' => $inactiveCount,
                ],
            ],
            'start' => ($totalCount > 0) ? ($activeCount * 100 / $totalCount) : 0,
            'end' => ($totalCount > 0) ? (100 - ($activeCount * 100 / $totalCount)) : 0,
            'route' => route('product.index'),
        ]);

        $data->put('product', $product);

        $totalPublished = DesignerSharedProduct::query();
        if (Auth::user()->role_id == Role::DESIGNER) {
            $totalPublished = $totalPublished->isClient('designer_id');
        } elseif (Auth::user()->role_id == Role::MANUFACTURER) {
            $totalPublished = $totalPublished->isClient('seller_id');
        }
        $totalPublished = $totalPublished->count();

        $approved = DesignerSharedProduct::query();
        if (Auth::user()->role_id == Role::DESIGNER) {
            $approved = $approved->isClient('designer_id');
        } elseif (Auth::user()->role_id == Role::MANUFACTURER) {
            $approved = $approved->isClient('seller_id');
        }
        $approved = $approved->where('status', 1)->count();

        $canceled = DesignerSharedProduct::query();
        if (Auth::user()->role_id == Role::DESIGNER) {
            $canceled = $canceled->isClient('designer_id');
        } elseif (Auth::user()->role_id == Role::MANUFACTURER) {
            $canceled = $canceled->isClient('seller_id');
        }
        $canceled = $canceled->where('status', 1)->whereIsPublished(0)->count();


        $sharedProduct = collect([
            'value' => $totalPublished,
            'label' => 'White Label Products',
            'icon_class' => 'ti ti-share',
            'bg_class' => 'bg-label-info',
            'border_class' => 'card-border-shadow-info',
            'stat_class' => 'text-info',
            'tooltip' => 'Total shared products on the platform',
            'items' => [
                [
                    'label' => 'Approved',
                    'stat_class' => 'text-success',
                    'value' => $approved,
                ],
                [
                    'label' => 'Canceled',
                    'stat_class' => 'text-danger',
                    'value' => $canceled,
                ],
                [
                    'label' => 'Pending',
                    'stat_class' => 'text-danger',
                    'value' => $totalPublished - ($approved + $canceled),
                ],
            ],
            'start' => ($totalPublished > 0) ? ($approved * 100 / $totalPublished) : 0,
            'end' => ($totalPublished > 0) ? (100 - ($approved * 100 / $totalPublished)) : 0,
            'route' => route('sharedProduct.index'),
        ]);

        $data->put('shared_product', $sharedProduct);

        $totalCount = Order::isClient('seller_id')->count();
        $deliveredCount = Order::isClient('seller_id')->where('status', OrderStatus::DELIVERED)->count();
        $canceledCount = Order::isClient('seller_id')->where('status', OrderStatus::CANCELED)->count();
        $processingCount = Order::isClient('seller_id')->where('status', OrderStatus::PROCESSED)->count();

        $order = collect([
            'value' => $totalCount,
            'label' => 'Orders',
            'icon_class' => 'ti ti-truck-delivery',
            'bg_class' => 'bg-label-success',
            'border_class' => 'card-border-shadow-success',
            'stat_class' => 'text-success',
            'tooltip' => 'Total orders on the platform',
            'items' => [
                [
                    'label' => 'Processing',
                    'stat_class' => 'text-danger',
                    'value' => $processingCount,
                ],
                [
                    'label' => 'Cancelled',
                    'stat_class' => 'text-danger',
                    'value' => $canceledCount,
                ],
                [
                    'label' => 'Delivered',
                    'stat_class' => 'text-success',
                    'value' => $deliveredCount,
                ],
            ],
            'start' => ($totalCount > 0) ? ($deliveredCount * 100 / $totalCount) : 0,
            'end' => ($totalCount > 0) ? (100 - ($deliveredCount * 100 / $totalCount)) : 0,
            'route' => route('order.index'),
        ]);

        $data->put('order', $order);
        $this->data = $data;
    }


    public function render(): View|Closure|string
    {
        $data = $this->data;

        return view('components.dashboard-statistic', compact('data'));
    }
}
