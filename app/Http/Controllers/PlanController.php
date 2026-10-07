<?php

namespace App\Http\Controllers;

use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Models\GatewayCredentials;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\PlanCondition;
use App\Models\PlanModule;
use App\Services\StripeService;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use Yajra\DataTables\Facades\DataTables;

class PlanController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Plan::select('*')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {

                    $isChecked = $row->is_active == 1 ? 'checked' : '';
                    $statusLabel = $row->is_active == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->is_active == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    return '<div class="custom-status-container">
                    <label class="switch switch-success" style="margin-right: 10px;">
                        <input type="checkbox" class="switch-input changeStatus" data-id="' . $row->id . '" ' . $isChecked . ' />
                        <span class="switch-toggle-slider">
                            <span class="switch-on">
                                <i class="ti ti-check"></i>
                            </span>
                            <span class="switch-off">
                                <i class="ti ti-x"></i>
                            </span>
                        </span>
                    </label>
                    <span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span>
                    </div>';
                })
                ->editColumn('price', function ($row) {
                    return getPriceFormat($row->price);
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('is_active', $request->get('status'));
                    }

                    if ($request->get('plan_type') != '') {
                        $instance->where('plan_type', $request->get('plan_type'));
                    }

                    if ($request->get('plan_for') == '3' || $request->get('plan_for') == '5') {
                        $instance->where('role_id', $request->get('plan_for'));
                    }
                }, true)
                ->editColumn('setup_fee', function ($row) {
                    return getPriceFormat($row->setup_fee);
                })
                ->editColumn('modules', function ($row) {

                    if ($row->modules !== null) {
//                        return json_e($row->modules);
                        $s = "<ul>";
                        foreach (json_decode($row->modules) as $module) {
                            $s .= "<li>" . $module->slug . "</li>";
                        }
                        $s .= "</ul>";
                        return $s;
                    }
                    return '----';

                })
                ->editColumn('plan_type', function ($row) {
                    return ucfirst($row->plan_type);
                })
                ->editColumn('is_popular', function ($row) {
                    $isChecked = $row->is_popular == 1 ? 'checked' : '';
                    $statusBadgeClass = $row->is_popular == 1 ? 'custom-bg-success' : 'custom-bg-danger';
                    if (hasPermission('plan_make_popular')) {
                        return '<div class="custom-status-container">
                    <label class="switch switch-success" style="margin-right: 10px;">
                        <input type="checkbox" class="switch-input popularStatus" data-id="' . $row->id . '" ' . $isChecked . ' />
                        <span class="switch-toggle-slider">
                            <span class="switch-on">
                                <i class="ti ti-check"></i>
                            </span>
                            <span class="switch-off">
                                <i class="ti ti-x"></i>
                            </span>
                        </span>
                    </label>
                    <span class="badge ' . $statusBadgeClass . '"></span>
                    </div>';
                    }

                    return '';

                })
                ->addColumn('plan_for', function ($row) {
                    if ($row->role_id == 3) {
                        return '<span class="badge bg-label-success">Designer</span>';
                    } else if ($row->role_id == 5) {
                        return '<span class="badge bg-label-info">Manufacturer</span>';
                    } else {
                        return '<span class="badge bg-label-warning">Free trial</span>';
                    }
                })
                ->addColumn('action', function ($row) {

                    $btn = '<div class="d-inline-block text-nowrap">' .
                        '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                        '<div class="dropdown-menu dropdown-menu-end m-0">';


                    $btn .= '<a href="javascript:0;" class="dropdown-item category_edit_button" data-bs-toggle="modal" data-bs-target="#modalCenterEdit" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>';

                    // $btn .= '<a href="javascript:0;" class="dropdown-item category_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                    //     '</div>' .
                    //     '</div>';

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'description', 'is_popular', 'plan_for', 'modules'])
                ->make(true);
        }
        $modules = PlanModule::where('active_status', 1)->get();
        return view('subscription.plan.index', compact('modules'));
    }

    public function store(PlanStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $plan = new Plan();

            $plan->name = $request->name;
            $plan->description = $request->description;
            $plan->is_active = $request->status;
            $plan->price = $request->price;
            $plan->setup_fee = $request->setupFee;
            $plan->plan_type = $request->planType;
            $plan->role_id = $request->palnFor;
            $plan->modules = $request->modules;
            $credential = GatewayCredentials::where('user_id', getUserId())
                ->where('payment_method_id', 1)
                ->where(
                    'key',
                    'secret_key'
                )
                ->first();

            if ($credential->value) {
                $stripe = new StripeClient($credential->value);

                $stripeProductDetails = $stripe->products->create([
                    'name' => $plan->name,
                    'description' => strip_tags($plan->description),
                    'active' => $plan->is_active == 1 ? true : false,
                ]);

                $stripePriceDetails = $stripe->prices->create([
                    'currency' => 'usd',
                    'unit_amount' => $plan->price * 100,
                    'recurring' => ['interval' => $plan->plan_type],
                    'product' => $stripeProductDetails->id,
                ]);
                $plan->product_id = $stripeProductDetails->id;
                $plan->price_id = $stripePriceDetails->id;
                $plan->save();

                DB::commit();
                return response()->json(['message' => 'Plan Created Successfully', 'status' => 200], 200);
            }
            DB::rollback();
            return response()->json(['message' => 'Payment method credential did not found', 'status' => 500], 500);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        $plan = Plan::with('planModule')->find($id);

        return response()->json(['data' => $plan, 'status' => 200], 200);
    }

    public function update(PlanUpdateRequest $request)
    {
        try {
            $plan = Plan::find($request->id);
            $popularPlan = Plan::where('role_id', $request->planFor)->where('is_popular', 1)->first();

            // If the plan is popular and the status is inactive then make the plan not popular
            if ($plan->is_active == 1 && $request->status == 0 && $plan->is_popular == 1) {
                $plan->is_popular = 0;
            }

            // Update the plan details
            $plan->name = $request->name;
            $plan->description = $request->description;
            $plan->is_active = $request->status;
            $plan->role_id = in_array($request->planFor, [3, 5]) ? $request->planFor : null;
            $plan->modules = $request->modules;


            if ($popularPlan && $popularPlan->id != $plan->id) {
                $plan->is_popular = 0;
            }

            $plan->save();
            if (in_array($request->planFor, ['3', '5'])) {

                $credential = GatewayCredentials::where('user_id', getUserId())
                    ->where('payment_method_id', 1)
                    ->where('key', 'secret_key')
                    ->first();

                if ($credential && $credential->value) {
                    $stripe = new StripeClient($credential->value);
                    $stripe->products->update($plan->product_id, [
                        'name' => $plan->name,
                        'description' => strip_tags($plan->description),
                        'active' => $plan->is_active == 1,
                    ]);
                }
            }

            return response()->json(['message' => 'Plan Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $plan = Plan::find($request->id);

            $stripe = new StripeClient(env('STRIPE_SECRET'));
            // $stripeProductDetails = $stripe->products->delete($plan->product_id, []);
            $stripeProductDetails = $stripe->products->update($plan->product_id, [
                'active' => $plan->is_active == 1 ? true : false,
            ]);
            $plan->delete();
            return response()->json(['text' => 'Plan has been deleted Successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:plans,id',
        ]);

        try {
            $plan = Plan::findOrFail($request->id);
            $previousIsActive = $plan->is_active;

            $plan->is_active = !$plan->is_active;
            $plan->save();

            if ($previousIsActive && !$plan->is_active && $plan->is_popular) {
                $plan->is_popular = 0;
                $plan->save();
            }

            $credential = GatewayCredentials::where('user_id', 1)->where('payment_method_id', 1)->where(
                'key',
                'secret_key'
            )->first();

            $stripe = new StripeClient($credential->value);
            $stripeProductDetails = $stripe->products->update($plan->product_id, [
                'active' => $plan->is_active == 1 ? true : false,
            ]);

            $this->cancelAllSubscriptionsForPlan($plan);

            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went Wrong!"], 500);
        }
    }

    public function makePopular(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->id);

        if ($plan->is_active == 0) {
            return response()->json(['message' => "Please make the plan active first!", 'status' => 402], 200);
        }

        if ($plan->is_popular) {
            $plan->is_popular = 0;
            $plan->save();
            return response()->json(['message' => 'Status changed successfully!', 'status' => 200], 200);
        }

        Plan::where('role_id', $plan->role_id)
            ->where('is_popular', 1)
            ->update(['is_popular' => 0]);

        $plan->is_popular = 1;
        $plan->save();

        return response()->json(['message' => 'Status changed successfully!', 'status' => 200], 200);
    }

    public function buyPlan()
    {

        try {

            $user = auth()->user();

            if (isSubscribed($user)) {
                return redirect()->route('dashboard');
            }

            $plans = Plan::where('is_active', 1)->whereNot('role_id', null)->where('role_id', auth()->user()->role_id)->get();

            $setup_fee = null;
            if ($user->subscription->isNotEmpty()) {
                $setup_fee = 0;
            }
            foreach ($plans as $plan) {
                if ($setup_fee == 0) {
                    $plan->setup_fee = 0;
                }
            }
            $paymentMethods = PaymentMethod::all();

            $stripeEnabled = globalSetting('stripe_payment')->value == 1;
            $paypalEnabled = globalSetting('paypal_payment')->value == 1;

            $paymentMethods = $paymentMethods->filter(function ($paymentMethod) use ($stripeEnabled, $paypalEnabled) {
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
                    'logo' => $paymentMethod->logo,
                ];
            });

            return view('subscription.plan.buy-plan', compact('plans', 'paymentMethods'));
        } catch (Exception $e) {
            abort(404);
        }
    }

    public function makePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        try {
            $plan = Plan::find($request->plan_id);
            if ($plan) {
                $stripeService = new StripeService();
                $data = $stripeService->makeSubscriptionPlanPayment($plan, route('dashboard'), route('dashboard'));
                if ($data['status']) {
                    return redirect($data['url']);
                }
            }
            Toastr::error($data['message']);
            return redirect()->back()->with('error', $data['message']);
        } catch (Exception $e) {
            return sendResponse("Something went wrong!");
        }
    }

    function cancelAllSubscriptionsForPlan($plan)
    {
        $activeSubscriptionsOfPlan = \App\Models\Subscription::where('stripe_product_id', $plan->product_id)->where('stripe_price_id', $plan->price_id)->where('stripe_status', 'active')->get();
        $stripeService = new StripeService();
        foreach ($activeSubscriptionsOfPlan as $activeSubscription) {
            $stripeService->cancelSubscriptonRevoke($activeSubscription);
        }
    }

}
