<?php

namespace App\Http\Controllers;

use App\Facades\SendMail;
use App\Models\Country;
use App\Models\OrderItemStatusLog;
use PDF;
use Exception;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Role;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Mail\InvoiceMail;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ProductRequest;
use App\Models\ShippingAddress;
use Faker\Provider\ar_EG\Payment;

use App\Models\OrderPaymentDetail;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\OrderStoreRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\OrderUpdateRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\IdValidationRequest;
use App\Models\PaymentMethodStatus;

use function Aws\map;

class OrderController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Order::isClient('seller_id')->withCount('items')->with(['user', 'orderStatus']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('order_date', function ($row) {
                    return dateFormat($row->order_date);
                })
                ->addColumn('name', function ($row) {
                    return $row->user->name;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status')) {
                        $instance->whereHas('orderStatus', function ($query) use ($request) {
                            $query->where('id', $request->get('status'));
                        });
                    }
                }, true)
                ->editColumn('status', function ($row) {
                    // return $row->orderStatus->name;
                    return '<span class="btn btn btn-label-secondary waves-effect"' . 'style="color:' . $row->orderStatus->color . '!important;">' . $row->orderStatus->name . '</span>';
                })
                ->editColumn('payment_status', function ($row) {
                    return match ($row->payment_status) {
                        'paid' => '<span class="btn btn btn-label-secondary waves-effect text-success">' . ucfirst($row->payment_status) . '</span>',
                        'unpaid' => '<span class="btn btn btn-label-secondary waves-effect text-danger">' . ucfirst($row->payment_status) . '</span>',
                        'partial' => '<span class="btn btn btn-label-secondary waves-effect text-warning">' . ucfirst($row->payment_status) . '</span>',
                        default => $row->payment_status,
                    };
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-2">';
                    if ($row->payment_status == 'unpaid' && hasPermission('customer_order_list_edit') && isSeller() && in_array($row->status, [OrderStatus::CONFIRMED, OrderStatus::PROCESSED])) {
                        $btn .= '<a href="' . route('order.edit', $row) . '" class="btn btn-success text-white " >Edit</a>';
                    }
                    if (hasPermission('customer_order_list_details')) {
                        $btn .= '<a href="' . route('order.details', $row) . '" class="btn btn-primary text-white " >Details</a>';
                    }
                    if (hasPermission('customer_order_list_read_invoice')) {
                        $btn .= '<a href="' . route('order.invoicePreview', $row) . '" class="btn btn-warning text-white" ><i class="menu-icon tf-icons ti ti-file-dollar"></i>Invoice</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status', 'payment_status'])
                ->make(true);
        }

        $orderStatus = OrderStatus::all();
        return view('order.index', compact('orderStatus'));
    }


    public function details($order_id)
    {
        $order = Order::with(['items.product.shop', 'user', 'items.statusLog.status', 'orderStatus'])->find($order_id);

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Order not found.');
        }
        hasPermissionForOperation($order, 'seller_id');
        $order->cancelOrderPermission = true;
        foreach ($order->items as $item) {
            $latestStatus = $item->statusLog()->latest()->first();
            if (!empty($latestStatus?->status?->id) && in_array($latestStatus->status->id, [
                    OrderStatus::DELIVERED,
                    OrderStatus::PROCESSED,
                    OrderStatus::SHIPPED,
                    OrderStatus::PARTIAL_DELIVERY
                ])) {
                $order->cancelOrderPermission = false;
            }
        }

        $status = OrderStatus::where('active_status', 1)->get();

        return view('order.details', compact('order', 'status'));
    }


    public function create()
    {
        $products = Product::where('user_id', getUserId())->where('is_published', 1)->get();
        $users = User::with('shippingAddress')->where('designer_id', getUserId())->where('role_id', Role::CUSTOMER)->get()
            ->map(function ($user) {
                $user->avatar = getFilePath($user->avatar);
                return $user;
            });

        $countries = Country::all();

        // $shipping_addresses = ShippingAddress::where('user_id', $order->user_id)->orderBy('id', 'desc')->get();

        return view('order.create', compact('products', 'users', 'countries'));
    }


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
            $order->admin_discount_value = $request->discount_type != 0 ? $request->discount_value : 0;
            $order->admin_discount_amount = $request->discount_type != 0 ? $request->discount_amount : 0;
            $order->tax_type = $request->tax_type;
            $order->tax_value = $request->tax_type != 0 ? $request->tax_value : 0;
            $order->tax_amount = $request->tax_type != 0 ? $request->tax_amount : 0;
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

            DB::commit();

            Toastr::success('Order Placed Successfully');
            return redirect()->route('order.index');
        } catch (Exception $e) {
            DB::rollBack();
            return abort(404, "Something went wrong");
        }
    }

    public function edit($order_id)
    {

        $order = Order::with(['items.product', 'user'])
            ->isClient('seller_id')
            ->where('status', '!=', OrderStatus::DELIVERED)
            ->find($order_id);
        hasPermissionForOperation($order, 'seller_id');

        $products = Product::where('user_id', getUserId())->get();
        $shipping_addresses = ShippingAddress::where('user_id', $order->user_id)->orderBy('id', 'desc')->get();

        return view('order.edit', compact('order', 'products', 'shipping_addresses'));
    }

    public function update(OrderUpdateRequest $request)
    {
        try {
            DB::beginTransaction();

            $order = Order::find($request->order_id);

            hasPermissionForOperation($order, 'seller_id');

            if ($request->has('shippingAddressId') && $request->filled('shippingAddressId')) {
                $shipping_addresses = ShippingAddress::where('id', $request->shippingAddressId)->where('user_id', $order->user_id)->first();


                if (!$shipping_addresses) {
                    DB::rollBack();
                    Toastr::error('Shipping Address did not found');
                    return redirect()->back();
                }

                $shipping_addresses_array = [];

                $shipping_addresses_array['name'] = $shipping_addresses->name;
                $shipping_addresses_array['email'] = $shipping_addresses->email;
                $shipping_addresses_array['phone'] = $shipping_addresses->phone;
                $shipping_addresses_array['country'] = $shipping_addresses->country;
                $shipping_addresses_array['state'] = $shipping_addresses->state;
                $shipping_addresses_array['street_address'] = $shipping_addresses->street_address;
                $shipping_addresses_array['zip_code'] = $shipping_addresses->zip_code;

                $order->shipping_address = json_encode($shipping_addresses_array);
            }

            $order->note = $request->note;
            $order->sub_total_amount = $request->sub_total;
            $order->admin_discount_type = $request->discount_type;
            $order->admin_discount_value = $request->discount_type != 0 ? $request->discount_value : 0;
            $order->admin_discount_amount = $request->discount_type != 0 ? $request->discount_amount : 0;
            $order->tax_type = $request->tax_type;
            $order->tax_value = $request->tax_type != 0 ? $request->tax_value : 0;
            $order->tax_amount = $request->tax_type != 0 ? $request->tax_amount : 0;
            $order->shipping_charges = $request->shipping_charge;
            $order->grand_total_amount = $request->total;

            $order->save();

            $exitingItemsIds = OrderItem::where('order_id', $request->order_id)->pluck('id')->toArray();
            $updatedIds = [];

            foreach ($request->product as $item) {
                $order_item = new OrderItem();
                if (isset($item['cart_item_id'])) {
                    $order_item = OrderItem::find($item['cart_item_id']);
                    array_push($updatedIds, $order_item->id);
                }

                $order_item->order_id = $order->id;
                $order_item->seller_id = $order->seller_id;
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
            }

            $result = array_diff($exitingItemsIds, $updatedIds);
            if (!empty($result)) {
                OrderItem::destroy($result);
                OrderItemStatusLog::whereIn('order_item_id', $result)->delete();
            }

            DB::commit();

            Toastr::success('Order Update Successfully');
            return redirect()->route('order.index');
        } catch (Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong');
            return back();
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $order = Order::find($request->order_id);
            hasPermissionForOperation($order, ['seller_id', 'user_id']);
            $order->status = 5;
            $order->save();

            return response()->json(['text' => 'Order has been Canceled Successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $order = Order::find($request->order_id);
            hasPermissionForOperation($order, 'seller_id');
            $order->status = $request->status_id;
            $order->save();
            Toastr::success('Order status has been changed.', 'Success');
            return response()->json(['message' => 'Order Status Has been Changed.', 'icon' => 'success', 'status' => 200]);
        } catch (Exception $e) {
            Toastr::error('Something went wrong');
            return response()->json(['message' => "Something went wrong", 'status' => 403]);
        }
    }

    public function invoicePreview($order_id)
    {
        $order = Order::with(['items.product', 'user'])
            ->isClient('seller_id')
            ->find($order_id);
        hasPermissionForOperation($order, ['seller_id', 'user_id']);
        $shop = ShopSetting::where('user_id', $order->seller_id)->first();
        $paymentMethos = PaymentMethod::whereHas('activeStatus', function ($query) use ($order) {
            $query->where('user_id', $order->seller_id)->where('active_status', 1);
        })->get();
        return view('order.invoice-preview', compact('order', 'paymentMethos', 'shop'));
    }

    public function invoicePrint($order_id)
    {

        $order = Order::with(['items.product', 'user'])
            ->find($order_id);
        $forPrint = 1;
        $setting = ShopSetting::where('user_id', $order->seller_id)->first();
        hasPermissionForOperation($order, ['seller_id', 'user_id']);
        return view('order.invoice', compact('order', 'setting', 'forPrint'));
    }

    public function invoiceDownload($order_id)
    {

        $order = Order::with(['items.product', 'user'])
            ->find($order_id);
        $setting = ShopSetting::where('user_id', $order->seller_id)->first();
        hasPermissionForOperation($order, ['seller_id', 'user_id']);

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('order.invoice', compact('order', 'setting'));
        // return $pdf->loadview($order->code . '.pdf');
        return $pdf->stream();
    }

    public function generateInvoicePDF($order_id)
    {

        $order = Order::with(['items.product', 'user'])
            ->find($order_id);
        $setting = ShopSetting::where('user_id', $order->seller_id)->first();

        hasPermissionForOperation($order, 'seller_id');

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('order.invoice', compact('order', 'setting'));

        return $pdf->output();
    }


    public function sendInvoice(Request $request)
    {
        try {
            $this->processInvoice(
                $request->input('order_id'),
                $request->input('invoice-to'),
                $request->input('invoice-subject'),
                $request->input('message')
            );

            Toastr::success('Success', "Invoice sent successfully");
        } catch (\Exception $e) {
            Toastr::error('Error', "Failed to send invoice!");
        }

        return redirect()->back();
    }

    /**
     * Reusable method for sending invoices.
     */
    public function processInvoice($orderId, $recipientEmail, $subject, $messageContent = null)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($orderId);
        $shopSetting = ShopSetting::where('user_id', $order->seller_id)->first();

        $pdf = $this->generateInvoicePDF($orderId);

        SendMail::sender($shopSetting->user_id)->to($recipientEmail)->send(new InvoiceMail($order, $pdf, $messageContent, $subject, $shopSetting));
    }


    // Generate custom order ID
    public function generateOrderID()
    {
        $timestamp = time();
        return "ORD-{$timestamp}";
    }

    public function getProduct($id)
    {
        $product = Product::with('choiceOptions', 'variants', 'shop')->find($id);
        $product->thumbnail_img = getFilePath($product->thumbnail_img);
        return $product;
    }

    public function getRequestedProduct($user_id)
    {
        $requestedProduct = ProductRequest::with('product')->where('user_id', $user_id)
            ->where('status', ProductRequest::ADDED_TO_CART)->get();

        return $requestedProduct;
    }


    public function paymentStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'payment_method' => 'required',
            'payment_date' => 'required',
            'current_due' => 'required',
            'pay' => 'required',
        ]);

        $messages = $validator->messages();
        foreach ($messages->all() as $message) {
            Toastr::error($message, 'Failed', ['timeOut' => 2000]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $payment = new OrderPaymentDetail();
            $payment->order_id = $request->order_id;
            $payment->amount = $request->pay;
            $payment->tnx_id = 'TNX-' . date('Ymd') . '-' . rand(1000, 9999);
            $payment->payment_method_id = $request->payment_method;
            $payment->payment_date = $request->payment_date;
            $payment->current_due = $request->current_due;
            $payment->save();

            $order = Order::find($request->order_id);
            if ($request->current_due <= 0) {
                $order->payment_status = 'paid';
            } else {
                $order->payment_status = 'partial';
            }
            $order->save();
            DB::commit();
            Toastr::success(__('Payment Added Successfully'), 'Success', ['timeOut' => 2000]);
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error(__('Something went wrong!'), 'Error', ['timeOut' => 2000]);
            return redirect()->back();
        }
    }

    // paymentHistry
    public function paymentHistry($id, Request $request)
    {
        try {
            $query = OrderPaymentDetail::with('order')->where('order_id', $id);

            if ($request->filled('search')) {
                $searchTerm = $request->search;
//                dd($searchTerm);
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('amount', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('order', function ($q) use ($searchTerm) {
                            $q->where('code', 'LIKE', "%{$searchTerm}%");
                        });
                });
            }

            $payments = $query->latest()->paginate(perPage());
            return view('order.payment_history', compact('payments'));
        } catch (\Throwable $th) {
            Toastr::error(__('Something went wrong!'), 'Error', ['timeOut' => 2000]);
            return redirect()->back();
        }
    }

    public function deleteHistory(Request $request)
    {
        try {
            $paymentHistory = OrderPaymentDetail::whereId($request->payment_id)->first();
            $order = $paymentHistory->order;
            if ($paymentHistory) {
                $paymentHistory->delete();
                $order->payment_status = 'unpaid';
                if ($order->paymentDetails()->exists()) {
                    $order->payment_status = 'partial';
                }
                $order->save();

                return response()->json(['message' => 'Payment history deleted successfully', 'status' => 200]);
            } else {
                return response()->json(['message' => 'Payment history not found!', 'status' => 404]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!' . $e->getMessage(), 'status' => 500]);
        }
    }
}
