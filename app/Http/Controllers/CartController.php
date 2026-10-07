<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Country;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OrderStoreRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    //

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Cart::isClient('seller_id')
                ->join('users', 'carts.user_id', '=', 'users.id')
                ->select('user_id', 'users.name', DB::raw('count(*) as total'))
                ->groupBy('user_id')
                ->latest('users.created_at');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '';
                    if (hasPermission('customer_cart_list_details')) {
                        $btn .= '<a href="' . route('cart.details', $row->user_id) . '" class="btn btn-success text-white" >Details</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('cart.index');
    }

    public function details($user_id, Request $request)
    {

        $cart_items = Cart::where('user_id', $user_id)
            ->with('product.shop')
            ->isClient('seller_id')
            ->select('*')->get();
        hasPermissionForOperation($cart_items, 'seller_id');

        $shipping_addresses = ShippingAddress::where('user_id', $user_id)->orderBy('id', 'desc')->get();

        $user = User::find($user_id);
        $countries = Country::all();
        return view('cart.details', compact('cart_items', 'user', 'shipping_addresses', 'countries'));
    }


    /**
     * @param Request $request
     *
     * @return [json]
     */
    public function store(OrderStoreRequest $request)
    {

        try {
            DB::beginTransaction();

            $shipping_addresses = ShippingAddress::where('id', $request->shippingAddressId)->where('user_id', $request->user_id)->first();


            if (!$shipping_addresses) {
                DB::rollBack();
                Toastr::error('Shipping Address did not found');
                return redirect()->back();
            }

            $shipping_addresses_arary = [];

            $shipping_addresses_arary['name'] = $shipping_addresses->name;
            $shipping_addresses_arary['email'] = $shipping_addresses->email;
            $shipping_addresses_arary['phone'] = $shipping_addresses->phone;
            $shipping_addresses_arary['country'] = $shipping_addresses->country;
            $shipping_addresses_arary['state'] = $shipping_addresses->state;
            $shipping_addresses_arary['street_address'] = $shipping_addresses->street_address;
            $shipping_addresses_arary['zip_code'] = $shipping_addresses->zip_code;

            $order = new Order();
            $order->user_id = $request->user_id;
            $order->seller_id = getUserId(); //here user id refer to designer id
            $order->code = $this->generateOrderID();
            $order->shipping_address = json_encode($shipping_addresses_arary);
            $order->note = $request->note;
            $order->sub_total_amount = $request->sub_total;
            $order->admin_discount_type = $request->discount_type;
            $order->admin_discount_value = $request->discount_value;
            $order->admin_discount_amount = $request->discount_amount;
            $order->tax_type = $request->tax_type;
            $order->tax_value = $request->tax_value;
            $order->tax_amount = $request->tax_amount;
            $order->shipping_charges = $request->shipping_charge;
            $order->grand_total_amount = $request->total;
            $order->order_date = Carbon::now();

            $order->save();

            foreach ($request->product as $item) {
                $order_item = new OrderItem();

                $order_item->order_id = $order->id;
                $order_item->seller_id = getUserId();
                $order_item->user_id = $request->user_id;
                $order_item->product_id = $item['product_id'];
                $order_item->unit_price = $item['price'];
                $order_item->price = $item['price'];
                $order_item->quantity = $item['quantity'];
                $variant_value = [];
                if (array_key_exists('variant', $item) && sizeof($item['variant'])) {
                    foreach ($item['variant'] as $variant) {
                        $value = [
                            'attribute' => $variant['attribute'],
                            'value' => $variant['value'],
                        ];
                        array_push($variant_value, $value);
                    }
                }

                $order_item->variation = $variant_value;

                $order_item->save();

                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->num_of_sale += $item['quantity'];
                    $product->save();
                }
            }

            Cart::where('user_id', $request->user_id)->where('seller_id', getUserId())->delete();

            $orderController = new OrderController();
            $orderController->processInvoice(
                $order->id,
                $order->user->email,
                $order->code,
            );

            DB::commit();

            Toastr::success('Order Placed Successfully');
            return redirect()->route('cart.index');
        } catch (Exception $e) {
            DB::rollBack();
            return abort(404, "Something went wrong");
        }
    }

    // Generate custom order ID
    public function generateOrderID()
    {
        $timestamp = time();
        return "ORD-{$timestamp}";
    }
}
