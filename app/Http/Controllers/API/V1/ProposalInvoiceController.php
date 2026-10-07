<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectProposalInvoiceListResource;
use App\Http\Resources\ProjectProposalResource;
use App\Models\ProjectProposal;
use App\Models\ProjectProposalInvoice;
use App\Models\ProposalInvoicePaymentDetails;
use App\Models\ShopSetting;
use App\Models\TimeBilling;
use App\Models\TimeBillingPaymentDetail;
use App\Services\StripeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;

class ProposalInvoiceController extends Controller
{
    //

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        $query = ProjectProposalInvoice::with('project')
            ->whereHas('project', function ($q) {
                $q->where('client_id', Auth::guard('sanctum')->user()->id);
            })->where('project_id', $request->project_id)
            ->where('active_status', 1);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $projectProposalInvoice = $query->latest()->paginate(perPage());
        return sendResponse('Project Proposal Invoice List',
            ProjectProposalInvoiceListResource::collection($projectProposalInvoice)->resource);
    }

    public function details($projectProposalInvoiceId)
    {
        $projectProposal = ProjectProposalInvoice::where('id', $projectProposalInvoiceId)
            ->with(['items' => function ($query) {
                $query->with('service', 'product');
            }
            ])->first();

        return sendResponse('Proposal Invoice details', new ProjectProposalInvoiceListResource($projectProposal));
    }

    public function downloadInvoice($invoice_id)
    {
        $invoice = ProjectProposalInvoice::with(['items' => function ($query) {
            $query->with('product', 'service');
        }, 'project.client'])
            ->find($invoice_id);
        $setting = ShopSetting::where('user_id', $invoice->user_id)->first();

        $pdf = \PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('project-management.project.projects.invoice.invoice', compact('invoice', 'setting'));
        return $pdf->stream();
    }

    public function makePaymentStripe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'success_url' => 'required',
            'cancel_url' => 'required',
            'invoice_id' => 'required|exists:project_proposal_invoices,id|integer',

        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        try {
            $invoice = ProjectProposalInvoice::find($request->invoice_id);
            $stripe = new StripeService();
            $redirectURL = $stripe->makeProposalInvoicePayment($invoice, $request->success_url, $request->cancel_url);
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
            'invoice_id' => 'required|exists:project_proposal_invoices,id|integer',

        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }


        try {
            $sessionId = $request->token;
            $invoice_id = $request->invoice_id;

            $invoice = ProjectProposalInvoice::find($invoice_id);

            $stripe = new StripeService();
            $secret_key = $stripe->getStripeCredential(getUserTimeBill($invoice));

            Stripe::setApiKey($secret_key);
            $payment_details = $stripe->successProposalInvoicePayment($sessionId, $invoice_id);

            $invoice->payment_status = 1;
            $payment = new ProposalInvoicePaymentDetails();
            $payment->project_proposal_invoice_id = $invoice_id;
            $payment->amount = $payment_details['payment_details']['amount_total'];
            $payment->payment_method_id = 1;
            $payment->payment_date = $payment_details['payment_details']['created'];
            $payment->current_due = 0;
            $payment->save();

            $invoice = ProjectProposalInvoice::find($invoice_id);
            if ($request->current_due <= 0) {
                $invoice->payment_status = 1;
            } else {
                $invoice->payment_status = 2;
            }
            $invoice->save();

            return sendResponse('Payment Successfull', [
                'invoice_id' => $invoice_id,
                'code' => $invoice->code,
                'payment_method' => 'stripe',
            ]);
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }
}
