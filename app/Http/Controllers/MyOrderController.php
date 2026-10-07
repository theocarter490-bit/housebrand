<?php

namespace App\Http\Controllers;

use App\Services\PaypalService;
use Exception;
use GuzzleHttp\Client;
use Stripe\Stripe;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ShippingAddress;
use App\Services\StripeService;
use App\Models\GatewayCredentials;
use App\Models\OrderPaymentDetail;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethodStatus;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Stripe\Checkout\Session as StripeSession;


class MyOrderController extends Controller
{
    //

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Order::where('user_id', getUserId())->withCount('items')->with(['designer', 'orderStatus'])->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('order_date', function ($row) {
                    return dateFormat($row->order_date);
                })
                ->addColumn('name', function ($row) {
                    return $row->designer->name;
                })
                ->editColumn('status', function ($row) {
                    // return $row->orderStatus->name;
                    return '<span class="btn btn btn-label-secondary waves-effect"' . 'style="color:' . $row->orderStatus->color . '!important;">' . $row->orderStatus->name . '</span>';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status')) {
                        $instance->whereHas('orderStatus', function ($query) use ($request) {
                            $query->where('id', $request->get('status'));
                        });
                    }
                }, true)
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (hasPermission('my_order_list_details')) {
                        $btn .= '<a href="' . route('myOrder.order.details', $row) . '" class="btn btn-primary text-white me-1" >Details</a>';
                    }
                    return $btn;
                })
                ->editColumn('payment_status', function ($row) {
                    return match ($row->payment_status) {
                        'paid' => '<span class="btn btn btn-label-secondary waves-effect text-success">' . ucfirst($row->payment_status) . '</span>',
                        'unpaid' => '<span class="btn btn btn-label-secondary waves-effect text-danger">' . ucfirst($row->payment_status) . '</span>',
                        'partial' => '<span class="btn btn btn-label-secondary waves-effect text-warning">' . ucfirst($row->payment_status) . '</span>',
                        default => $row->payment_status,
                    };
                })
                ->rawColumns(['action', 'status', 'payment_status'])
                ->make(true);
        }

        $orderStatus = OrderStatus::all();
        return view('my-order.order.index', compact('orderStatus'));
    }

    public function details($order_id)
    {
        $order = Order::with(['items.product.shop', 'designer', 'items.statusLog.status', 'orderStatus'])
            ->find($order_id);

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Order not found.');
        }

        foreach ($order->items as $item) {
            $item->latestStatus = $item->statusLog()->latest()->first();
        }

        hasPermissionForOperation($order, 'user_id');
        return view('my-order.order.details', compact('order'));
    }


    public function invoicePreview($order_id)
    {
        $order = Order::with(['items.product', 'user', 'shop'])
            ->find($order_id);
//        dd($order->shop);
        hasPermissionForOperation($order, 'user_id');

        return view('my-order.order.invoice-preview', compact('order'));
    }

    public function cartList(Request $request)
    {
        $carts = Cart::with('seller.shop')->where('user_id', Auth::id())->get();
        return view('my-order.cart.index', compact('carts'));
    }

    public function destroy(Request $request)
    {
        $cart = Cart::find($request->cart_id)->delete();
        return response()->json(['message' => 'Cart Deleted', 'status' => 200]);
    }

    // makePayment
    public function makePaymentStripe($order_id)
    {
        $order = Order::find($order_id);
        $stripe = new StripeService();
        $redirectURL = $stripe->makeOrderPayment($order, route('myOrder.order.checkout-success.stripe', [], true), route('myOrder.order.checkout-cancel.stripe', [], true));
        if ($redirectURL) {
            return redirect($redirectURL);
        }

        Toastr::error('Payment gateway is not setup. Please contact to seller');
        return view('my-order.order.details', compact('order'));
    }

    public function makePaymentPaypal($order_id)
    {
        $order = Order::find($order_id);
        $paypal = new PaypalService();
        $redirectURL = $paypal->makeOrderPayment($order, route('myOrder.order.checkout-success.paypal', [], true), route('myOrder.order.checkout-cancel.paypal', [], true));
        if ($redirectURL) {
            return redirect($redirectURL);
        }

        Toastr::error('Payment gateway is not setup. Please contact to seller');
        return view('my-order.order.details', compact('order'));
    }

    // checkoutSuccess
    public function checkoutSuccessStripe(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');
            $order_id = $request->query('order_id');

            $order = Order::find($order_id);

            $stripe = new StripeService();
            $secret_key = $stripe->getStripeCredential($order->seller_id);

            Stripe::setApiKey($secret_key);
            $payment_detials = $stripe->successPayment($sessionId, $order_id);

            $order->payment_status = $payment_detials['payment_status'];

            $payment = new OrderPaymentDetail();
            $payment->order_id = $order_id;
            $payment->amount = $payment_detials['payment_details']['amount_total'];
            $payment->payment_method_id = 1; // stripe
            $payment->payment_date = $payment_detials['payment_details']['created'];
            $payment->tnx_id = $payment_detials['payment_details']['payment_intent_id'];
            $payment->current_due = 0;
            $payment->save();

            if ($payment->current_due <= 0) {
                $order->payment_status = 'paid';
            } else {
                $order->payment_status = 'partial';
            }

            $order->save();
            Toastr::success('Payment successfull');

            return redirect()->route('myOrder.order.details', $order_id);
        } catch (Exception $e) {
            abort(404, 'Something went wrong.');
        }
    }

    public function checkoutSuccessPaypal(Request $request)
    {

        $token = $request->query('token');
        $order_id = $request->query('order_id');
        $order = Order::find($order_id);
        try {
            $paypalService = new PaypalService();
            $accessToken = $paypalService->getAccessToken($order);
            $client = new Client();
            $response = $client->post("https://api-m.sandbox.paypal.com/v2/checkout/orders/" . $token . "/capture", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
            ],
            );
            $responseBody = json_decode($response->getBody(), true);


            $payment = new OrderPaymentDetail();
            $payment->order_id = $order_id;
            $payment->amount = $responseBody['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $payment->payment_method_id = 2; // paypal
            $payment->payment_date = date_format(date_create($responseBody['purchase_units'][0]['payments']['captures'][0]['create_time']), 'Y-m-d');
            $payment->tnx_id = $responseBody['purchase_units'][0]['payments']['captures'][0]['id'];
            $payment->current_due = 0;
            $payment->save();

            if ($request->current_due <= 0) {
                $order->payment_status = 'paid';
            } else {
                $order->payment_status = 'partial';
            }

            $order->save();

            Toastr::success('Payment successfull');

            return redirect()->route('myOrder.order.details', $order_id);
        } catch (Exception $e) {

            Toastr::error('Something went wrong.');

            return redirect()->route('myOrder.order.details', $order_id);
        }
    }


}
