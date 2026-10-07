<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusStoreRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemStatusLog;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;

class OrderItemController extends Controller
{
    public function statusStore(StatusStoreRequest $request)
    {

        try {

            $orderItemStatusLog = new OrderItemStatusLog();
            $orderItemStatusLog->order_item_id = $request->order_item_id;
            $orderItemStatusLog->order_status_id = $request->order_item_status;
            $orderItemStatusLog->date_time = $request->order_item_status_date;
            $orderItemStatusLog->note = $request->note;
            $orderItemStatusLog->save();

            Toastr::success('Order Item Status Added');
            return response()->json(['message' => 'Order Item Status Added', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }
}
