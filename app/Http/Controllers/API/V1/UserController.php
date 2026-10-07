<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Brand;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Project;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function designers(Request $request)
    {
        $designers = User::where('role_id', Role::DESIGNER)
            ->where('active_status', 1)
            ->checkSubscription()
            ->whereHas('shop', function ($query) use ($request) {
                $query->where('shop_status', true);
                if ($request->has('search')) {
                    $query->where('shop_name', 'like', '%' . $request->search . '%');
                }
            })
            ->with('shop');
        if ($request->has('designer')) {
            $designers = $designers->where('id', getDesignerID());
        }
        $designers = $designers->withCount('products', 'portfolio', 'inspiration', 'reviews');
        if (request()->has('no_pagination') && request()->get('no_pagination') == 1) {
            $designers = $designers->get(); // Get all records
        } else {
            $designers = $designers->paginate(perPage()); // Paginate records
        }

        return sendResponse('Designer list.', UserResource::collection($designers)->resource);
    }



    // dashboardOverviewCounts
    public function dashboardOverviewCounts(Request $request)
    {
        $user = Auth::user();

        $data = $this->getCommonCounts($user);

        switch ($user->role_id) {
            case '4': // Customer
                $data = array_merge($data, $this->customerCounts($user));
                break;

            case '3': // Designer
                $data = array_merge($data, $this->designerCounts($user));
                break;

            case '5': // Manufacturer
                $data = array_merge($data, $this->manufacturerCounts($user));
                break;
        }

        return sendResponse('Dashboard overview counts.', $data);
    }


    private function getCommonCounts($user)
    {
        $orders = Order::where('user_id', $user->id)->get();

        return [
            'total_order'     => $orders->count(),
            'confirmed_order' => $orders->where('status', 1)->count(),
            'processed_order' => $orders->where('status', 2)->count(),
            'delivered_order' => $orders->where('status', 4)->count(),
            'cart'            => Cart::where('user_id', $user->id)->count(),
            'wishlist'        => Wishlist::where('user_id', $user->id)->count(),
        ];
    }

    private function customerCounts($user)
    {
        return [
            'total_projects' => Project::where('client_id', $user->id)->count(),
        ];
    }

    private function designerCounts($user)
    {
        $products = Product::where('user_id', $user->id)->whereNull('parent_id')->get();

        return [
            'total_product'         => $products->count(),
            'published_product'     => $products->where('is_published', 1)->count(),
            'unpublished_product'   => $products->where('is_published', 0)->count(),
            'white_label_products'  => Product::where('user_id', $user->id)->whereNotNull('parent_id')->count(),
            'total_sales'           => Order::where('seller_id', $user->id)->count(),
        ];
    }


    private function manufacturerCounts($user)
    {
        return [
            'total_sales'  => Order::where('seller_id', $user->id)->count(),
        ];
    }
}
