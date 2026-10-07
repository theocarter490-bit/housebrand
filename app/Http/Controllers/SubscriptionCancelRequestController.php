<?php

namespace App\Http\Controllers;

use App\Http\Traits\FileUploadTrait;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\ProductRequest;
use App\Models\SubscriptionCancelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Facades\DB;

class SubscriptionCancelRequestController extends Controller
{
    use FileUploadTrait;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SubscriptionCancelRequest::with('user', 'plan');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_info', function ($row) {

                    $info = "<span>Name: " . optional($row->user)->name . "</span>
                    <br><span>Email: "  . optional($row->user)->email . "</span><br>
                    <span>Phone: " . optional($row->user)->phone . " </span>";
                    return $info;
                })
                ->addColumn('plan_name', function ($row) {
                    return $row->plan->name ?? '';
                })
                ->editColumn('file', function ($row) {
                    return getFileElement(getFilePath($row->file));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row->getRawOriginal('status') == SubscriptionCancelRequest::PENDING) {

                        if (hasPermission('subscription_calcel_list_approve')) {
                            $btn .= '<a href="#" class="btn btn-success text-white product_request_approve_button mb-2" data-id="' . $row->id . '">Approve</a>';
                        }

                        if (hasPermission('subscription_calcel_list_cancel')) {
                            $btn .= '<a href="#" class="btn btn-danger text-white ms-2 product_request_cancel_button" data-id="' . $row->id . '">Cancel</a>';
                        }
                    }
                    if ($row->getRawOriginal('status') == SubscriptionCancelRequest::APPROVED) {

                        $btn .= '<a href="#" class="badge bg-success text-white ms-2">Approved</a>';
                    }
                    if ($row->getRawOriginal('status') == SubscriptionCancelRequest::CANCELED) {
                        $btn .= '<a href="#" class="badge bg-danger text-white ms-2" >Canceled</a>';
                    }

                    return $btn;
                })
                ->addColumn('details_button', function ($row) {
                    $btn = '';
                    if (hasPermission('subscription_calcel_list_details')) {
                        $btn .= '<button type="button" class="btn btn-primary details" data-bs-toggle="modal" data-bs-target="#enableOTP" data-id="' . $row->user->id . '">
                        Show
                      </button>';

                    }
                    return $btn;
                })
                ->rawColumns(['action', 'product_name',  'shop_info', 'file', 'details_button', 'user_info'])
                ->make(true);
        }
        return view('subscription.cancel-request.index');
    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Toastr::error($error);
            }
            return redirect()->back();
        }

        $user = Auth::user()->load('activeSubscription.plan');

        if ($user->activeSubscription) {
            $cancelRequest = new SubscriptionCancelRequest();

            $cancelRequest->subject = $request->title;
            $cancelRequest->description = $request->description;
            $cancelRequest->comments = $request->comment;
            $cancelRequest->user_id = $user->id;
            $cancelRequest->plan_id = $user->activeSubscription->plan_id;

            if ($request->hasFile('file')) {
                $path = $this->uploadFile($request->file('file'), 'cancel-request');
                $cancelRequest->file = $path;
            }

            $cancelRequest->save();

            Toastr::success("Your Subscription cancellation request has been added");

            return redirect()->back();
        }

        Toastr::error("Your Don't have any active subscription");

        return redirect()->back();
    }

    public function approve(Request $request)
    {
        try {
            $cancelRequest = SubscriptionCancelRequest::find($request->request_id);

            $cancelRequest->status = SubscriptionCancelRequest::APPROVED;

            $cancelRequest->save();
            return response()->json(['text' => 'Cancel Request has been Approved.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }
    public function cancel(Request $request)
    {
        try {

            $cancelRequest = SubscriptionCancelRequest::find($request->request_id);
            $cancelRequest->status = SubscriptionCancelRequest::CANCELED;
            $cancelRequest->save();

            return response()->json(['text' => 'Subscription cancel successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function orderCount(Request $request)
    {
        try {
            $customerOrderCount = Order::select('order_statuses.name', 'order_statuses.color', DB::raw('COUNT(orders.id) as total'))
                ->join('order_statuses', 'orders.status', '=', 'order_statuses.id')
                ->where('seller_id', $request->request_id)
                ->groupBy('status')
                ->get();

            $ownOrderCount = Order::select('order_statuses.name', 'order_statuses.color', DB::raw('COUNT(orders.id) as total'))
                ->join('order_statuses', 'orders.status', '=', 'order_statuses.id')
                ->where('user_id', $request->request_id)
                ->groupBy('status')
                ->get();


            return response()->json(['customerOrderCount' => $customerOrderCount, 'ownOrderCount' => $ownOrderCount]);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }
}
