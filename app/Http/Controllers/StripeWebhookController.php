<?php

namespace App\Http\Controllers;

use App\Facades\SendMail;
use App\Mail\SubscriptionPaymentInvoice;
use App\Models\Plan;
use App\Models\ShopSetting;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Models\SubscriptionPaymentLog;
use App\Models\TrailCodeUse;
use App\Models\User;
use App\Services\StripeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class StripeWebhookController extends Controller
{
    //

    public function handleWebhook()
    {
        $stripe = new StripeService();
        $secret_key = $stripe->getStripeCredential(1);
        $webhook_key = $stripe->getStripeWebhookCredential(1);

        \Stripe\Stripe::setApiKey($secret_key);
        if ($webhook_key) {

            $payload = @file_get_contents('php://input');
            $sig_header = request()->header('Stripe-Signature');
            $event = null;

            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $sig_header,
                    $webhook_key
                );
            } catch (\UnexpectedValueException $e) {
                // Invalid payload
                http_response_code(400);
                echo json_encode(['Error parsing payload: ' => $e->getMessage()]);
                exit();
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                http_response_code(400);
                echo json_encode(['Error verifying webhook signature: ' => $e->getMessage()]);
                exit();
            }

            try {
                // Handle the event
                return match ($event->type) {
                    'customer.subscription.created' => $this->createSubscription($event->data->object),
                    'invoice.payment_succeeded' => $this->paymentSuccess($event->data->object),
                    'invoice.payment_failed' => $this->paymentFailed($event->data->object),
                    'customer.subscription.updated' => $this->updateSubscription($event->data->object),
                    'customer.subscription.deleted' => $this->deleteSubscription($event->data->object),
                    default => 'Received unknown event type ' . $event->type,
                };
            } catch (Exception $e) {
                Log::error($e->getMessage());
                http_response_code(400);
            }
            http_response_code(200);
        } else {
            http_response_code(400);
        }
    }

    public function createSubscription($object)
    {
        try {
            DB::beginTransaction();
            $subscription = Subscription::where('stripe_subscription_id', $object->id)->first();
            if (!$subscription) {
                $user = User::with('shop')->where('stripe_id', $object->customer)->first();
                $plan = Plan::where('product_id', $object->plan->product)->first();
                $subscription = new Subscription();
                $subscription->user_id = $user->id;
                $subscription->plan_id = $plan->id;
                $subscription->stripe_product_id = $object->plan->product;
                $subscription->stripe_price_id = $object->plan->id;
                $subscription->stripe_customer_id = $object->customer;
                $subscription->stripe_subscription_id = $object->id;
                $subscription->stripe_status = $object->status;
                $subscription->ends_at = date('Y-m-d H:i:s', $object->current_period_end);
                $subscription->cancel_at_period_end = $object->cancel_at_period_end;
                $subscription->type = $object->object;
                $subscription->save();
                $isEcommerceAvailable = 0;
                if ($this->hasModuleSlug($plan->modules, 'ecommerce-support')) {
                    $isEcommerceAvailable = 1;
                }
                $user->shop->update([
                    'modules' => $plan->modules ?? [],
                    'shop_status' => $isEcommerceAvailable,
                ]);
            }

            DB::commit();
            return sendResponse("Subscription created");
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function updateSubscription($object)
    {
        try {
            DB::beginTransaction();

            $subscription = Subscription::where('stripe_subscription_id', $object->id)->first();
            $user = User::with('shop')->where('stripe_id', $object->customer)->first();
            $plan = Plan::where('product_id', $object->plan->product)->first();


            // Cancel old subscription if exists and it's not the current one
            $oldSubscriptions = Subscription::where('user_id', $user->id)
                ->where('stripe_subscription_id', '!=', $object->id)
                ->whereIn('stripe_status', ['active', 'trialing'])
                ->get();

            if (!empty($oldSubscriptions)) {
                $service = new StripeService();
                foreach ($oldSubscriptions as $oldSubscription) {
                    $service->cancelSubscriptonImmediately($oldSubscription->stripe_subscription_id);
                    // Mark old subscription as canceled
                    $oldSubscription->stripe_status = 'canceled';
                    $oldSubscription->save();
                }

            }


            if (!$subscription) {
                $subscription = new Subscription();
                $subscription->user_id = $user->id;
                $subscription->plan_id = $plan?->id;
                $subscription->stripe_product_id = $object->plan->product;
                $subscription->stripe_price_id = $object->plan->id;
                $subscription->stripe_customer_id = $object->customer;
                $subscription->stripe_subscription_id = $object->id;
                $subscription->type = $object->object;
            }

            $subscription->ends_at = date('Y-m-d H:i:s', $object->current_period_end);
            $subscription->stripe_status = $object->status;
            $subscription->cancel_at_period_end = $object->cancel_at_period_end;
            $subscription->save();

            $isEcommerceAvailable = 0;
            if ($this->hasModuleSlug($plan->modules, 'ecommerce-support')) {
                $isEcommerceAvailable = 1;
            }
            if ($plan && $user->shop) {
                $user->shop->update([
                    'modules' => $plan->modules ?? [],
                    'shop_status' => $isEcommerceAvailable,
                ]);
            }

            DB::commit();
            return sendResponse("Subscription Updated (or created if new)");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error updating subscription: " . $e->getMessage());
            return sendError("An error occurred while updating subscription " . $e->getMessage(), 500);
        }
    }

    public function paymentSuccess($object)
    {
        try {
            DB::beginTransaction();
            $user = User::where('stripe_id', $object->customer)->first();
            foreach ($object->lines->data as $item) {
                $subscription_id = $item->parent->subscription_item_details->subscription ?? null;
                if ($subscription_id) {
                    $subscription = Subscription::where('stripe_subscription_id', $subscription_id)->first();
                    $subscriptionItem = new SubscriptionItem();
                    $subscriptionItem->subscription_id = $subscription->id;
                    $subscriptionItem->stripe_subscription_item_id = $item->id;
                    $subscriptionItem->stripe_product_id = $item->pricing->price_details->product;
                    $subscriptionItem->stripe_price_id = $item->pricing->price_details->price;

                    $subscriptionItem->stripe_invoice_no = $item->invoice;
                    $subscriptionItem->customer_details = json_encode([
                        'name' => $object->customer_name,
                        'email' => $object->customer_email,
                        'phone' => $object->customer_phone,
                    ]);
                    $subscriptionItem->price = $object->amount_paid;

                    $subscriptionItem->payment_status = $object->status;
                    $subscriptionItem->started_at = date('Y-m-d H:i:s', $item->period->start);
                    $subscriptionItem->expire_at = date('Y-m-d H:i:s', $item->period->end);

                    $subscriptionItem->currency = $object->currency;
                    $subscriptionItem->save();

                    $subscription->ends_at = date('Y-m-d H:i:s', $item->period->end);
                    $subscription->save();


                    $user->is_subscribed = 1;
                    if ($user->trail_mode == 1) {
                        $user->trail_mode = 3;
                    }
                    if ($user->subscription_required == 0) {
                        $user->subscription_required = 1;
                        TrailCodeUse::where('user_id', $user->id)->where('is_active', 1)->update([
                            'is_active' => 0,
                            'expire_at' => Carbon::now()
                        ]);
                    }
                    $user->active_status = 1;
                    $user->save();
                }
            }
            DB::commit();
            $stripeService = new StripeService();
            $invoice = $stripeService->generateInvoice($subscriptionItem->stripe_invoice_no);
            $invoicePDF = $this->generateInvoicePDF($invoice);
            SendMail::to($user->email)->send(new SubscriptionPaymentInvoice($invoice, $invoicePDF));
            return sendResponse("Payment complete successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function generateInvoicePDF($invoice)
    {
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('email.subscription-payment-invoice', compact('invoice'));

        return $pdf->output();
    }

    public function paymentFailed($object)
    {
        try {
            DB::beginTransaction();
            $subscription = Subscription::where('stripe_subscription_id', $object->subscription)->first();
            $subscription->user->update(['is_subscribed' => 0]);
            DB::commit();
            return sendResponse("Payment Failed");
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function deleteSubscription($object)
    {
        try {
            DB::beginTransaction();

            $subscription = Subscription::where('stripe_subscription_id', $object->id)->first();
            $user = User::where('stripe_id', $object->customer)->first();
            if ($subscription->stripe_status != 'canceled') {
                $user->is_subscribed = 0;
                $user->save();
            }
            $subscription->stripe_status = $object->status;
            $subscription->save();
            DB::commit();

            return sendResponse("Subscription Cancel successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    private function hasModuleSlug($modules, string $slug): bool
    {
        if (is_string($modules)) {
            $decoded = json_decode($modules, true);
            $modules = is_array($decoded) ? $decoded : [];
        } elseif ($modules instanceof Collection) {
            $modules = $modules->toArray();
        } elseif (is_object($modules)) {
            $modules = (array)$modules;
        }

        if (!is_array($modules)) {
            return false;
        }

        foreach ($modules as $module) {
            if (is_array($module) && ($module['slug'] ?? null) === $slug) {
                return true;
            }

            if (is_object($module) && ($module->slug ?? null) === $slug) {
                return true;
            }
        }

        return false;
    }


}
