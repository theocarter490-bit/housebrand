<?php

namespace App\Http\Controllers\API\V1;

use App\Facades\SendMail;
use App\Mail\FreeTrialSubscriptionInvoice;
use App\Mail\SubscriptionPaymentInvoice;
use App\Models\PlanModule;
use App\Models\Role;
use Exception;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Services\StripeService;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
use Illuminate\Support\Facades\Auth;
use stdClass;

class SubscriptionController extends Controller
{

    public function index()
    {
        try {
            $message = "All Subscription Plans.";
            $plans = Plan::where('is_active', 1)->with('planModule');

            if (request()->query('plan_for') !== null && request()->query('type') !== null) {
                $planFor = request()->query('plan_for');
                if ($planFor == 3) {
                    $plans->where('role_id', Role::DESIGNER);
                    $message = "Plan For Designer.";
                } elseif ($planFor == 5) {
                    $plans->where('role_id', Role::MANUFACTURER);
                    $message = "Plan For Manufacturer.";
                }
                $availableTypes = clone($plans);

                $plans->where('plan_type', request()->query('type'));
            } else {
                $availableTypes = clone($plans);
            }

            $plans = $plans->get();

            $availableTypes = $availableTypes?->groupBy('plan_type')->pluck('plan_type');

            return sendResponse($message, [
                'plans' => SubscriptionPlanResource::collection($plans),
                'available_types' => $availableTypes
            ]);
        } catch (Exception $e) {
            return sendError('Something went wrong!');
        }
    }

    public function details($id)
    {
        try {
            $plan = Plan::find($id);
            if ($plan) {
                return sendResponse("Plan Details", new SubscriptionPlanResource($plan));
            }
            return sendError('Plan not found!');
        } catch (Exception $e) {
            return sendError("Something went wrong!");
        }
    }

    public function makePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'success_url' => 'required',
            'cancel_url' => 'required',
        ]);

        try {
            $plan = Plan::find($request->plan_id);
            if (Auth::user()->role_id != $plan->role_id) {
                return sendError("You don't have access to this plan!");
            }
            if ($plan) {
                $stripeService = new StripeService();
                $data = $stripeService->makeSubscriptionPlanPayment($plan, $request->success_url, $request->cancel_url);
                if ($data) {
                    return sendResponse("Checkout Url.", $data);
                }
            }
            return sendError('Stripe is not setup please contact with administrator.');
        } catch (Exception $e) {
            return sendError("Something went wrong!", [], 500);
        }
    }


    // paymentMethods
    public function paymentMethods()
    {
        try {
            $paymentMethods = PaymentMethod::with('activeStatus')->whereRelation('activeStatus', function ($query) {
                $query->where('active_status', 1)->where('user_id', Role::SUPER_ADMIN);
            })->get();


            $stripeEnabled = globalSetting('stripe_payment')->value == 1;
            $paypalEnabled = globalSetting('paypal_payment')->value == 1;

            $filteredPaymentMethods = $paymentMethods->filter(function ($paymentMethod) use ($stripeEnabled, $paypalEnabled) {
                if ($paymentMethod->id == 1 && $stripeEnabled) {
                    return true;
                }
                if ($paymentMethod->id == 2 && $paypalEnabled) {
                    return true;
                }
                return false;
            })->map(function ($paymentMethod) {
                return [
                    'id' => $paymentMethod->id,
                    'name' => $paymentMethod->name,
                    'logo' => asset($paymentMethod->logo),
                ];
            });

            return sendResponse('Payment Methods', $filteredPaymentMethods);
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }

    // freeTrail
    public function freeTrail(Request $request)
    {
        try {
            $plan = Plan::where('is_free_trail', 1)->first();
            if ($plan) {
                if (Auth::user() && Auth::user()->trail_mode) {
                    return sendError("You are already on Free Trail Plan!");
                }
                return sendResponse("Free Trail Plan Details", new SubscriptionPlanResource($plan));
            }

        } catch (Exception $e) {
            return sendError("Something went wrong!", [], 500);
        }
    }

    // freeTrailStart
    public function freeTrailStart(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);
        try {
            $plan = Plan::where('id', $request->plan_id)->first();
            if ($plan) {
                if (Auth::user() && Auth::user()->is_subscribed == 1) {
                    return sendError("Already you have active subscription! Please contact to administrator.");
                }

                if (Auth::user() && Auth::user()->trail_mode == 1) {
                    return sendError("You are already on Free Trail Plan!");
                }
                if (Auth::user() && Auth::user()->trail_mode == 3) {
                    return sendError("You are no longer eligible for a free trial.");
                }

                $user = Auth::user();
                $user->trail_mode = 1;
                $shop = $user->shop;
                $shop->modules = $plan->modules;
                $shop->save();
                $user->save();
                SendMail::to($user->email)->send(new FreeTrialSubscriptionInvoice($user));
                return sendResponse("Free Trail Plan activated successfully!", new SubscriptionPlanResource($plan));
            }
            return sendError('Free Trail Plan not found!');
        } catch (Exception $e) {
            return sendError("Something went wrong!", [], 500);
        }
    }
}
