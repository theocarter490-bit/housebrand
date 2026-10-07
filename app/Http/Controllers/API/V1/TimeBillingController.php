<?php

namespace App\Http\Controllers\API\V1;

use PDF;
use Stripe\Stripe;
use App\Models\ShopSetting;
use App\Models\TimeBilling;
use Illuminate\Http\Request;
use App\Models\TimeBillingLog;
use App\Services\StripeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TimeBillingPaymentDetail;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TimeBillingResource;
use App\Http\Resources\TimeBillingLogResource;
use App\Http\Resources\TimeBillingDetailResource;

class TimeBillingController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }
        $query = TimeBilling::with('client', 'serviceType', 'paymentDetails.paymentMethod')
            ->where('project_id', $request->project_id)
            ->where('client_id', Auth::id())
            ->where('active_status', 0);

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $timeBilling = $query->latest()->paginate(perPage());

        return sendResponse('Time Billings List', TimeBillingResource::collection($timeBilling)->resource);
    }

    public function timeBreakdown($id)
    {
        $timeBillingLog = TimeBillingLog::where('time_billing_id', $id)->paginate(perPage());
        return sendResponse('Time Breakdown', TimeBillingLogResource::collection($timeBillingLog)->resource);
    }

    public function details($id)
    {
        $timeBilling = TimeBilling::with('client', 'serviceType')->withSum('paymentDetails as paid_amount', 'amount')->find($id);
        return sendResponse('Time Billing Details', new TimeBillingDetailResource($timeBilling));
    }

    public function invoice($id)
    {
        $timeBilling = TimeBilling::with(['logs', 'client', 'serviceType.projectService'])
            ->find($id);

        $userID = getUserTimeBill($timeBilling);

        $setting = ShopSetting::where('user_id', $userID)->first();

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('project-management.project.projects.time-billing.invoice', compact('timeBilling', 'setting'));

        return $pdf->stream();
    }

    public function makePaymentStripe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'success_url' => 'required',
            'cancel_url' => 'required',
            'time_billing_id' => 'required|exists:time_billings,id|integer',

        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        try {
            $timeBill = TimeBilling::find($request->time_billing_id);
            $stripe = new StripeService();
            $redirectURL = $stripe->makeTimeBillPayment($timeBill, $request->success_url, $request->cancel_url);
            if ($redirectURL) {
                return sendResponse('Payment URL', $redirectURL);
            }

            return sendError('Payment gateway is not setup. Please contact to seller');
        } catch (\Throwable $th) {
            return sendError($th->getMessage());
        }

    }

    public function checkoutSuccessStripe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'time_billing_id' => 'required|exists:time_billings,id|integer',

        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }


        try {
            $sessionId = $request->token;
            $time_billing_id = $request->time_billing_id;

            $timeBill = TimeBilling::find($time_billing_id);

            $stripe = new StripeService();
            $secret_key = $stripe->getStripeCredential(getUserTimeBill($timeBill));

            Stripe::setApiKey($secret_key);
            $payment_details = $stripe->successTimeBillPayment($sessionId, $time_billing_id);

            $timeBill->payment_status = 1;
            $payment = new TimeBillingPaymentDetail();
            $payment->time_billing_id = $time_billing_id;
            $payment->amount = $payment_details['payment_details']['amount_total'];
            $payment->payment_method_id = 1;
            $payment->payment_date = $payment_details['payment_details']['created'];
            $payment->current_due = 0;
            $payment->save();

            $timeBill = TimeBilling::find($time_billing_id);
            if ($request->current_due <= 0) {
                $timeBill->payment_status = 1;
            } else {
                $timeBill->payment_status = 2;
            }
            $timeBill->save();

            return sendResponse('Payment Successfull', [
                'time_billing_id' => $time_billing_id,
                'code' => $timeBill->code,
                'payment_method' => 'stripe',
            ]);
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }
}
