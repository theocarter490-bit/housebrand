<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderShippingAddressStoreRequest;
use App\Models\ShippingAddress;
use Exception;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{

    public function store(OrderShippingAddressStoreRequest $request)
    {

        try {
            ShippingAddress::where('user_id', $request->userID)->update(['is_default' => 0]);
            $shipping_address = new ShippingAddress();
            $shipping_address->user_id = $request->userID;
            $shipping_address->name = $request->fullName;
            $shipping_address->email = $request->email;
            $shipping_address->phone = $request->phone;
            $shipping_address->country = $request->country;
            $shipping_address->state = $request->state;
            $shipping_address->street_address = $request->street;
            $shipping_address->zip_code = $request->zipCode;
            $shipping_address->created_by = Auth::user()->id;
            $shipping_address->is_default = 1;
            $shipping_address->save();
            return response()->json(['message' => 'Shipping Address Added Successfully', 'status' => 200, 'data' => $shipping_address]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'status' => 500]);
        }
    }
}
