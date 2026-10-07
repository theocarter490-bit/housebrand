<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\View\View;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\SpecialSection;
use App\Models\SubscriptionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\SubscriptionPaymentLog;
use Illuminate\Support\Facades\Redirect;
use App\Models\SubscriptionCancelRequest;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{


    public function profile()
    {
        $user = User::with(['activeSubscription' => function ($query) {
            $query->with('plan', 'latestItem');
        }, 'role', 'freeTrailCode'])->where('id', Auth::user()->id)->first();
        $subscriptionPaymentList = SubscriptionItem::whereHas('subscription', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('plan', 'subscription')->orderBy('id', 'desc')->paginate(10)->appends(['tab' => 'subscription']);

        $cancelRequests = SubscriptionCancelRequest::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->paginate(3)->appends(['tab' => 'subscription']);
        $latestCancelRequest = SubscriptionCancelRequest::where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->first();

        $dataCount['product_count'] = Product::isClient()->count();
        $dataCount['order_count'] = Order::isClient('seller_id')->count();
        $dataCount['my_order_count'] = Order::isClient('user_id')->count();
        $dataCount['cart_count'] = Cart::isClient()->count();
        $dataCount['wishlist_count'] = Wishlist::isClient()->count();
        $dataCount['portfolio_count'] = SpecialSection::isClient()->where('type', 1)->count();
        $dataCount['inspiration_count'] = SpecialSection::isClient()->where('type', 2)->count();

        // plan
        $user = auth()->user();
        $plans = Plan::where('is_active', 1)->where('role_id', auth()->user()->role_id)->get();
        $setup_fee = null;
        if ($user->subscription->isNotEmpty()) {
            $setup_fee = 0;
        }
        foreach ($plans as $plan) {
            if ($setup_fee == 0) {
                $plan->setup_fee = 0;
            }
        }
        $paymentMethods = PaymentMethod::all();
        $stripeEnabled = globalSetting('stripe_payment')->value == 1;
        $paypalEnabled = globalSetting('paypal_payment')->value == 1;
        $paymentMethods = $paymentMethods->filter(function ($paymentMethod) use ($stripeEnabled, $paypalEnabled) {
            if ($paymentMethod->id == 1 && $stripeEnabled) {
                return true;
            }
            if ($paymentMethod->id == 2 && $paypalEnabled) {
                return true;
            }
            return false;
        })->map(function ($paymentMethod) {
            return [
                'id' => $paymentMethod->id,
                'name' => $paymentMethod->name,
                'logo' => $paymentMethod->logo,
            ];
        });
        $freeTrailPlan = Plan::where('role_id', null)->latest()->first();

        // plan end

        return view('profile.profile', compact('user', 'subscriptionPaymentList', 'cancelRequests', 'dataCount', 'latestCancelRequest', 'plans', 'paymentMethods','freeTrailPlan'));
    }

}
