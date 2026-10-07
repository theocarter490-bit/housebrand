<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\ShippingAddressStoreRequest;
use App\Http\Requests\ShippingAddressUpdateRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\ShippingAddressResource;
use App\Models\Country;
use App\Models\ShippingAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ShippingAddressController extends Controller
{
    //


    public function list()
    {
        $shipping_address = ShippingAddress::where('user_id', Auth::user()->id)->latest()->get();
        return sendResponse('All products list.', ShippingAddressResource::collection($shipping_address));
    }



    public function store(ShippingAddressStoreRequest $request)
    {
        try {
            $user = Auth::user();
            ShippingAddress::where('user_id', $user->id)->update(['is_default' => 0]);
            $shipping_address = new ShippingAddress();
            $shipping_address->user_id = $user->id;
            $shipping_address->name = $request->name;
            $shipping_address->email = $request->email;
            $shipping_address->phone = $request->phone;
            $shipping_address->country = $request->country;
            $shipping_address->state = $request->state;
            $shipping_address->street_address = $request->street_address;
            $shipping_address->zip_code = $request->zip_code;
            $shipping_address->is_default = 1;
            $shipping_address->created_by = $user->id;
            $shipping_address->save();

            return sendResponse('Shipping Address Added Successfully');
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }


    public function update(ShippingAddressUpdateRequest $request)
    {
        try {
            $shipping_address = ShippingAddress::find($request->shipping_address_id);
            $shipping_address->name = $request->name;
            $shipping_address->email = $request->email;
            $shipping_address->phone = $request->phone;
            $shipping_address->country = $request->country;
            $shipping_address->state = $request->state;
            $shipping_address->street_address = $request->street_address;
            $shipping_address->zip_code = $request->zip_code;
            $shipping_address->updated_by = Auth::user()->id;
            $shipping_address->save();
            return sendResponse('Shipping Address Updated Successfully');
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_addresses_id' => 'required|exists:shipping_addresses,id|integer',
        ]);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        try {
            $shippingAddress = ShippingAddress::find($request->shipping_addresses_id);

            if ($shippingAddress->is_default == 1) {
                return sendError('Cannot delete default shipping address');
            }

            $shippingAddress->delete();

            return sendResponse('Shipping Address Deleted Successfully');
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }


    public function details($id)
    {
        try {
            $shipping_address = ShippingAddress::findOrFail($id);

            return sendResponse('Shipping Address Details.', new ShippingAddressResource($shipping_address));
        } catch (\Exception $e) {
            return sendError('Shipping Address Not Found!', null, 404);
        }
    }


    public function changeStatus(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $shippingAddress = ShippingAddress::findOrFail($id);

            if ($user->id !== $shippingAddress->user_id) {
                return sendError('Unauthorized action', [], 403);
            }

            if ($shippingAddress->is_default == 1) {
                return sendResponse('This shipping address is already the default');
            }

            ShippingAddress::where('user_id', $user->id)->update(['is_default' => 0]);

            $shippingAddress->is_default = 1;
            $shippingAddress->save();

            return sendResponse('Default Shipping Address Updated Successfully');
        } catch (\Exception $e) {
            return sendError('Something went wrong');
        }
    }

    public function countries()
    {
        $countries = Country::all();
        return sendResponse('All countries.', CountryResource::collection($countries));
    }
}
