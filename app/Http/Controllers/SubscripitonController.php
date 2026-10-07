<?php

namespace App\Http\Controllers;

use App\Models\GatewayCredentials;
use App\Models\ShopSetting;
use App\Models\SubscriptionItem;
use App\Models\TrailCodeUse;
use PDF;
use App\Models\Role;
use App\Models\SubscriptionPaymentLog;
use App\Models\User;
use App\Services\StripeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use Yajra\DataTables\Facades\DataTables;
use Brian2694\Toastr\Facades\Toastr;

class SubscripitonController extends Controller
{

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = User::whereIn('role_id', [Role::DESIGNER, Role::MANUFACTURER])->with('lastSubscription.plan', 'shop')->whereHas('subscription');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->lastSubscription->stripe_status == 'active') {
                        return '<span class="badge bg-label-success">' . $row->lastSubscription->stripe_status . '</span>';
                    } else {
                        return '<span class="badge bg-label-danger">' . $row->lastSubscription->stripe_status . '</span>';
                    }
                })
                ->addColumn('shop_info', function ($row) {
                    // Using optional to safely access properties
                    $info = "<span>Shop Name: <span class='badge bg-info'>" . optional($row->shop)->shop_name . "</span></span><br>
                                 <span>Location: " . optional($row->shop)->location . "</span><br>
                                 <span>Phone: " . optional($row->shop)->phone . "</span><br>
                                 <span>Email: " . optional($row->shop)->email . "</span>";
                    return $info;

                })
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->editColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn("plan", function ($row) {
                    return optional(optional($row->lastSubscription)->plan)->name;
                })
                ->addColumn("expire_date", function ($row) {

                    return dateFormat($row->lastSubscription->ends_at);
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('subscription.customer.details', $row) . '" class="btn btn-primary text-white me-1">Billing Details</a>';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') === 'active' || $request->get('status') === 'canceled') {
                        $instance->whereHas('lastSubscription', function ($query) use ($request) {
                            $query->where('stripe_status', $request->get('status'));
                        });
                    }
                }, true)
                ->rawColumns(['action', 'status', 'shop_info'])
                ->make(true);
        }

        return view('subscription.customer.index');
    }

    public function freeTrail(Request $request)
    {
        if ($request->ajax()) {
            $data = User::whereIn('role_id', [Role::DESIGNER, Role::MANUFACTURER])
                ->with('trailCodeUses')
                ->whereHas('trailCodeUses');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', fn($row) => $row->name)
                ->editColumn('email', fn($row) => $row->email)
                ->addColumn('trail_code', function ($row) {
                    if ($row->trailCodeUses->isEmpty()) {
                        return '<span class="text-muted">N/A</span>';
                    }

                    $html = '<div class="">';
                    foreach ($row->trailCodeUses as $use) {
                        $expireDate = $use->expire_at ? dateFormat($use->expire_at) : 'N/A';
                        $status = $use->is_active == 1 ? 'Active' : 'Inactive';
                        $statusColor = $use->is_active == 1 ? 'success' : 'secondary';

                        $html .= '
                            <div class="border rounded p-2 me-2 mb-2 bg-light justify-content-between d-flex " style="min-width: 180px;">
                                <div>
                                    <span class="badge bg-primary text-white mb-1"
                                        style="font-size: 0.8rem; border-radius: 6px; padding: 5px 8px;">'
                            . e($use->code) .
                            '</span>
                                </div>
                                <div class="small text-muted" style="font-size: 0.8rem;">
                                    Exp: ' . e($expireDate) . '
                                </div>
                                <div>
                                    <span class="badge bg-' . $statusColor . ' text-white"
                                        style="font-size: 0.75rem; border-radius: 6px;">'
                            . e($status) .
                            '</span>
                                </div>
                            </div>';
                    }
                    $html .= '</div>';

                    return $html;
                })
                ->rawColumns(['trail_code'])
                ->addColumn('expire_date', function ($row) {
                    $first = $row->trailCodeUses->first();
                    return $first ? dateFormat($first->end_at) : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    if ($row->trailCodeUses->isEmpty()) {
                        return '<span class="text-muted">Invitation Inactive</span>';
                    }

                    $hasActive = $row->trailCodeUses->contains(function ($use) {
                        return $use->is_active == 1;
                    });

                    if ($hasActive) {
                        return '<a href="javascript:void(0);"
                                    class="btn btn-sm btn-outline-danger text-nowrap terminate"
                                    data-id="' . $row->id . '">
                                    <i class="bi bi-receipt me-1"></i> Terminate Invitation
                                </a>';
                    }
                    return '<span class="text-muted">Invitation Inactive</span>';

                })
                ->rawColumns(['trail_code', 'action'])
                ->make(true);
        }

        return view('subscription.free-trail.index');
    }

    public function freeTrailTerminate(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            TrailCodeUse::where('user_id', $user->id)->where('is_active', 1)->update([
                'is_active' => 0,
                'expire_at' => Carbon::now()
            ]);
            $user->subscription_required = 1;
            $user->save();
            return response()->json(['text' => 'Invitation has been successfully terminated.', 'icon' => 'success', 'title' => 'Deleted!']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong", 'icon' => 'error', 'title' => 'error!']);
        }
    }


    public function details($user_id)
    {
        $user = User::with('lastSubscription.plan')->where('id', $user_id)->first();
        $subscriptionsItem = SubscriptionItem::whereHas('subscription', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->with('plan', 'subscription')->orderBy('id', 'desc')->paginate(10);

        $stripeCustomerId = $user->lastSubscription->stripe_customer_id;

        $stripeService = new StripeService();
        $cardInfo = $stripeService->getCustomerCardInfo($stripeCustomerId);

        // sorting for latest card
        usort($cardInfo->data, function ($a, $b) {
            return $b->created - $a->created;
        });

        if (count($cardInfo->data) > 0) {
            $paymentMethod = $cardInfo->data[0];
            $cardDetails = [
                'card_brand' => strtolower($paymentMethod->card->brand),
                'card_last4' => $paymentMethod->card->last4,
                'card_expiry' => $paymentMethod->card->exp_month . "/" . $paymentMethod->card->exp_year,
            ];
        } else {
            $cardDetails = null;
        }

        $cardBrandImages = [
            'visa' => 'assets/img/icons/payments/visa.png',
            'mastercard' => 'assets/img/icons/payments/mastercard.png',
            'american_express' => 'assets/img/icons/payments/american-express-img.png',
            'jcb' => 'assets/img/icons/payments/jcb-light.png',
        ];

        return view('subscription.customer.details', compact('user', 'subscriptionsItem', 'cardDetails', 'cardBrandImages'));
    }

    public function stripeGenerateInvoice($invoice_no)
    {
        try {
            $stripeService = new StripeService();
            $invoice = $stripeService->generateInvoice($invoice_no);
            $invoice_hosted_url = $invoice->hosted_invoice_url;
            return redirect($invoice_hosted_url);
        } catch (Exception $e) {
            Toastr::error('Error retrieving invoice: ' . $e->getMessage());
        }
        return back();
    }

    public function invoicePreview($invoice_no)
    {
        try {
            $stripeService = new StripeService();
            $invoice = $stripeService->generateInvoice($invoice_no);
            return view('subscription.customer.invoice-preview', compact('invoice'));
        } catch (Exception $e) {
            Toastr::error('Error retrieving invoice: ' . $e->getMessage());
        }
        return back();
    }

    public function invoicePrint($invoice_no)
    {
        try {
            $stripeService = new StripeService();
            $invoice = $stripeService->generateInvoice($invoice_no);

            return view('subscription.customer.invoice', compact('invoice'));
        } catch (Exception $e) {
            Toastr::error('Error retrieving invoice: ' . $e->getMessage());
        }
        return back();
    }

    public function invoiceDownload($invoice_no)
    {
        try {
            $stripeService = new StripeService();
            $invoice = $stripeService->generateInvoice($invoice_no);

            $pdf = PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'logOutputFile' => storage_path('logs/log.htm'),
                'tempDir' => storage_path('logs/'),
            ])->loadView('subscription.customer.invoice', compact('invoice'));
            return $pdf->download($invoice_no . '.pdf');
        } catch (Exception $e) {
            Toastr::error('Error downloading invoice: ' . $e->getMessage());
        }
        return back();
    }

    public function immediateSubscriptionCancel($user_id)
    {

        try {
            DB::beginTransaction();
            $currentActiveSubscripton = Subscription::where('user_id', $user_id)
                ->where('stripe_status', 'active')
                ->first();
            User::where('id', $user_id)->update(['is_subscribed' => 0]);
            $stripeService = new StripeService();
            $stripeService->cancelSubscriptonImmediately($currentActiveSubscripton->stripe_subscription_id);

            DB::commit();

            Toastr::success('Subscription Canceled successfull');
            return redirect()->route('subscription.customer.details', $user_id);
        } catch (Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong');
            return redirect()->route('subscription.customer.details', $user_id);
        }
    }

    public function revokeSubscriptionCancel($user_id)
    {
        try {
            DB::beginTransaction();

            $currentActiveSubscription = Subscription::where('user_id', $user_id)
                ->where('stripe_status', 'active')
                ->first();
            $stripeService = new StripeService();
            $stripeService->cancelSubscriptonRevoke($currentActiveSubscription);

            DB::commit();

            Toastr::success('Subscription Canceled successfull');
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();

            Toastr::error('Something went wrong');
            return redirect()->back();
        }
    }
}
