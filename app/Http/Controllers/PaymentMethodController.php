<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodStoreRequest;
use App\Http\Requests\PaymentMethodUpdateRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\GatewayCredentials;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodStatus;
use App\Models\Role;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class PaymentMethodController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        $methods = PaymentMethod::all();
        return view('setting.payment-method.index', compact('methods'));
    }

    public function stripeCredentialStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'public_key' => 'required',
            'secret_key' => 'required',
            'webhook_key' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        }

        try {

            PaymentMethodStatus::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 1,
            ], [
                'setup_status' => 1,
            ]);

            GatewayCredentials::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 1,
                'key' => 'public_key',
            ], [
                'value' => $request->public_key,
            ]);
            GatewayCredentials::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 1,
                'key' => 'secret_key',
            ], [
                'value' => $request->secret_key,
            ]);
            GatewayCredentials::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 1,
                'key' => 'webhook_key',
            ], [
                'value' => $request->webhook_key,
            ]);

            return response()->json(['message' => 'Stripe Credential Updated', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function paypalCredentialStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'client_secret' => 'required',
            'paypal_mode_input' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        }

        try {
            DB::beginTransaction();

            PaymentMethodStatus::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 2,
            ], [
                'setup_status' => 1,
            ]);

            GatewayCredentials::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 2,
                'key' => 'client_id',
            ], [
                'value' => $request->client_id,
            ]);
            GatewayCredentials::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 2,
                'key' => 'client_secret',
            ], [
                'value' => $request->client_secret,
            ]);
            GatewayCredentials::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 2,
                'key' => 'mode',
            ], [
                'value' => $request->paypal_mode_input == 'true' ? 1 : 0,
            ]);
            DB::commit();

            return response()->json(['message' => 'Paypal Creadential Updated', 'status' => 200], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function stripeChangeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        }

        try {

            PaymentMethodStatus::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 1,
            ], [
                'active_status' => $request->status == 'true' ? 1 : 0,
            ]);

            return response()->json(['message' => 'Stripe Status Updated Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went Wrong!"], 500);
        }
    }

    public function paypalChangeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        }

        try {

            PaymentMethodStatus::updateOrCreate([
                'user_id' => getUserId(),
                'payment_method_id' => 2,
            ], [
                'active_status' => $request->status == 'true' ? 1 : 0,
            ]);

            return response()->json(['message' => 'Paypal Status Updated Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went Wrong!"], 500);
        }
    }

    // changePublishStatus
    public function changePublishStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        }

        try {
            $status = PaymentMethodStatus::where('user_id', getUserId())->where('payment_method_id', $request->id)->first();
            if ($status) {
                $status->update([
                    'active_status' => $status->active_status == 0 ? 1 : 0,
                ]);
            } else {
                PaymentMethodStatus::create([
                    'user_id' => getUserId(),
                    'payment_method_id' => $request->id,
                    'active_status' => 1,
                ]);
            }
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went Wrong!"], 500);
        }
    }

    public function store(PaymentMethodStoreRequest $request)
    {
        try {
            $paymentMethod = new PaymentMethod();
            $paymentMethod->user_id = Auth::user()->id;
            $paymentMethod->name = $request->name;
            if ($request->hasFile('logo')) {
                $path = $this->uploadFile($request->file('logo'), 'payment-method');
                $paymentMethod->logo = $path;
            }
            $paymentMethod->type = 1;
            $paymentMethod->save();
            Toastr::success('Payment Method added successfully!', 'Success');
        } catch (\Exception $e) {
            Toastr::error('Something Went Wrong!', 'error');
        }
        return back();
    }

    public function delete($id)
    {
        try {
            $paymentMethod = PaymentMethod::find($id);
            if ($paymentMethod) {
                $paymentMethod->delete();
                return response()->json(['message' => 'Payment Method deleted successfully!', 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'Payment Method not found!', 'status' => 404], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        try {
            $paymentMethod = PaymentMethod::find($id);
            $paymentMethod->logo = getFilePath($paymentMethod->logo);
            return response()->json($paymentMethod);

        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500], 500);
        }
    }

    public function update(PaymentMethodUpdateRequest $request)
    {
        try {
            $paymentMethod = PaymentMethod::find($request->payment_method_id);
            if ($paymentMethod) {
                $paymentMethod->name = $request->name;
                if ($request->hasFile('logo')) {
                    $this->deleteFile($paymentMethod->logo);
                    $path = $this->uploadFile($request->file('logo'), 'payment-method');
                    $paymentMethod->logo = $path;
                }
                $paymentMethod->save();
                Toastr::success('Payment Method updated successfully!', 'Success');
            } else {
                Toastr::error('Payment Method not found!', 'error');
            }
        } catch (\Exception $e) {
            Toastr::error('Something Went Wrong!', 'error');
        }
        return back();
    }

    public function verifyPassword(Request $request)
    {
        $user = User::find(getUserId());
        if (Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password Verified', 'status' => 200], 200);
        } else {
            return response()->json(['message' => 'Password Invalid', 'status' => 404], 404);
        }
    }


}
