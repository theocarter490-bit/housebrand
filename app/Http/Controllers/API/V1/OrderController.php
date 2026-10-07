<?php

namespace App\Http\Controllers\API\V1;

use App\Services\PaypalService;
use GuzzleHttp\Client;
use PDF;
use Exception;
use Stripe\Stripe;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\OrderStatus;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use App\Services\StripeService;
use App\Models\OrderPaymentDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderListResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OrderDetailsResource;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $ordersQuery = Order::where('user_id', Auth::user()->id)
            ->withCount('items')
            ->with('orderStatus', 'shop');

        if ($request->filled('search')) {
            $ordersQuery->where(function ($query) use ($request) {
                $query->where('code', 'like', '%' . $request->search . '%')
                    ->orWhereHas('shop', function ($query) use ($request) {
                        $query->where('shop_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('order_status')) {
            $ordersQuery->where('status', $request->order_status);
        }

        $orders = $ordersQuery->latest();
        if (request()->has('no_pagination') && request()->get('no_pagination') == true) {
            $orders = $orders->get(); // Get all records
        } else {
            $orders = $orders->paginate(perPage()); // Paginate records
        }

        return sendResponse('Order List.', OrderListResource::collection($orders)->resource);
    }

    public function details($id)
    {

        $order = Order::with(['items.product', 'shop', 'items.statusLog.status', 'orderStatus', 'paymentDetails.paymentMethod'])
            ->withSum('paymentDetails as paid_amount', 'amount')
            ->find($id);

        if ($order) {
            return sendResponse('Order List.', new OrderDetailsResource($order));
        }

        return sendError('Order Not Found!', null, 404);
    }

    public function count()
    {
        $order_count = Order::where('user_id', Auth::user()->id)->get();

        $total_order = $order_count->count();
        $confirmed_order = $order_count->where('status', '1')->count();
        $procesed_order = $order_count->where('status', '2')->count();
        $delivered_order = $order_count->where('status', '4')->count();
        $cart = Cart::where('user_id', Auth::user()->id)->count();
        $wishlist = Wishlist::where('user_id', Auth::user()->id)->count();

        return sendResponse('Order Count.', [
            'total_order' => $total_order,
            'confirmed_order' => $confirmed_order,
            'procesed_order' => $procesed_order,
            'delivered_order' => $delivered_order,
            'cart' => $cart,
            'wishlist' => $wishlist,
        ]);
    }

    public function invoiceDownload($order_id)
    {
        $order = Order::with(['items.product', 'user'])
            ->find($order_id);
        if (!$order) {
            return sendError('Order does not exits');
        }
        $setting = ShopSetting::where('user_id', $order->seller_id)->first();

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('order.invoice', compact('order', 'setting'));
        // return $pdf->download($order->code . '.pdf');
        return $pdf->stream($order->code . '.pdf');
    }

    public function makePaymentStripe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'success_url' => 'required',
            'cancel_url' => 'required',
            'order_id' => 'required|exists:orders,id|integer',

        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }


        $order = Order::find($request->order_id);
        $stripe = new StripeService();
        $redirectURL = $stripe->makeOrderPayment($order, $request->success_url, $request->cancel_url);
        if ($redirectURL) {
            return sendResponse('Payment URL', $redirectURL);
        }

        return sendError('Payment gateway is not setup. Please contact to seller');
    }

    public function makePaymentPaypal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'success_url' => 'required',
            'cancel_url' => 'required',
            'order_id' => 'required|exists:orders,id|integer',

        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }


        $order = Order::find($request->order_id);
        $paypal = new PaypalService();
        $redirectURL = $paypal->makeOrderPayment($order, $request->success_url, $request->cancel_url);
        if ($redirectURL) {
            return sendResponse('Payment URL', $redirectURL);
        }

        return sendError('Payment gateway is not setup. Please contact to seller');
    }

    public function checkoutSuccessPaypal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'order_id' => 'required|exists:orders,id|integer',

        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }


        try {
            $token = $request->token;
            $order_id = $request->order_id;

            $order = Order::find($order_id);

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

            return sendResponse('Payment Successfull', [
                'order_id' => $order_id,
                'code' => $order->code,
                'payment_method' => 'paypal',
            ]);
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }

    // checkoutSuccess
    public function checkoutSuccessStripe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'order_id' => 'required|exists:orders,id|integer',

        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }


        try {
            $sessionId = $request->token;
            $order_id = $request->order_id;

            $order = Order::find($order_id);

            $stripe = new StripeService();
            $secret_key = $stripe->getStripeCredential($order->seller_id);

            Stripe::setApiKey($secret_key);
            $payment_details = $stripe->successPayment($sessionId, $order_id);

            $order->payment_status = $payment_details['payment_status'];
            $payment = new OrderPaymentDetail();
            $payment->order_id = $order_id;
            $payment->amount = $payment_details['payment_details']['amount_total'];
            $payment->payment_method_id = 1; // stripe
            $payment->payment_date = $payment_details['payment_details']['created'];
            $payment->tnx_id = $payment_details['payment_details']['payment_intent_id'];
            $payment->current_due = 0;
            $payment->save();

            if ($payment->current_due <= 0) {
                $order->payment_status = 'paid';
            } else {
                $order->payment_status = 'partial';
            }
            $order->save();

            return sendResponse('Payment Successfull', [
                'order_id' => $order_id,
                'code' => $order->code,
                'payment_method' => 'stripe',
            ]);
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }

    public function orderStatus()
    {
        $data = OrderStatus::select('id', 'name', 'color')->where('active_status', 1)->get();

        return sendResponse('Order Status List', $data);
    }

    public function cancelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id|integer',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }
        try {
            $order = Order::where('user_id', Auth::user()->id)->where('id', $request->order_id)->first();
            if (!$order) {
                return sendError("Order don't Exits");
            }
            if ($order->status != 1) {
                return sendError("Order can't Be Canceled. Contact with your seller");
            }
            $order->status = 5;
            $order->save();
            return sendResponse('Order Cancelled Successfully');
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }
}
