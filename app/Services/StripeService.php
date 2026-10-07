<?php

namespace App\Services;

use App\Models\GatewayCredentials;
use App\Models\PaymentMethodStatus;
use App\Models\Plan;
use App\Models\SubscriptionItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Subscription;

class StripeService
{

    public function makeOrderPayment($order, $success_url, $cancel_url)
    {
        if (globalSetting('stripe_payment')->value == 1) { //check if stripe is enable globally

            $payment_status = checkIfStripeIsSetup($order->seller_id);
            if ($payment_status) {
                $secret_key = $this->getStripeCredential($order->seller_id);
                Stripe::setApiKey($secret_key);

                $payableAmount = @$order->lastPayment ? $order->lastPayment->current_due : @$order->grand_total_amount;

                $unitAmount = $this->calculatePrice($payableAmount);

                // Define the product and price details
                $lineItems = $this->createOrderPaymentPayload($order->code, $unitAmount);

                // Create the Checkout Session
                $checkoutSession = StripeSession::create([
                    'payment_method_types' => ['card', 'link'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => $success_url . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id . '&payment_method=stripe',
                    'cancel_url' => $cancel_url,
                ]);

                return $checkoutSession->url;
            }
        }
        return false;
    }

    public function makeSubscriptionPlanPayment($plan, $success_url, $cancel_url)
    {
        if (globalSetting('stripe_payment')->value == 1) {

            $user = User::find(auth()->user()->id);
            $secret_key = $this->getStripeCredential(1);
            Stripe::setApiKey($secret_key);

            if (!$user->stripe_id) {
                // Create a new Stripe customer
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);

                $user->stripe_id = $customer->id;
                $user->save();
            }

            if (Auth::user()->is_subscribed == 1) {
                $result = $this->currentPlanLimitCheck($plan);
                if (!$result['status']) {
//                    if ($this->checkIfUserHasActiveSubscription($user)) {
//                        $this->cancelSubscriptonImmediately($user->activeSubscription->stripe_subscription_id);
//                    }
                    $messages = [];

                    foreach ($result['messages'] as $msg) {
                        $messages[] = $msg;
                    }

                    return [
                        'status' => false,
                        'message' => implode("<br>", $messages), // or use <br> for HTML: implode('<br>', $messages)
                    ];
                }
            }

            $subscriptionItem = $this->createSubscriptionPaymentPayload($plan);
            $session = StripeSession::create([
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card', 'link'],
                'success_url' => $success_url,
                'cancel_url' => $cancel_url,
                'mode' => 'subscription',
                'line_items' => $subscriptionItem,
            ]);
            return [
                'status' => true,
                'url' => $session->url,
            ];
        }
        return false;
    }

    public function checkIfUserHasActiveSubscription($user)
    {
        $subscriptions = Subscription::all([
            'customer' => $user->stripe_id,
            'status' => 'active',
        ]);
        $planIDs = Plan::all()->pluck('price_id')->toArray();
        // Check if the user already has the subscription
        $hasActiveSubscription = false;
        foreach ($subscriptions->data as $subscription) {
            foreach ($subscription->items->data as $item) {
                if (in_array($item->price->id, $planIDs)) {
                    $hasActiveSubscription = true;
                    break;
                }
            }
            if ($hasActiveSubscription) {
                break;
            }
        }

        if ($hasActiveSubscription) {
            // Notify the user they already have an active subscription
            return true;
        }

        return false;
    }


    protected $moduleModelMap = [
        'project-management' => \App\Models\Project::class,
        'employee-management' => \App\Models\User::class,
        'event-management' => \App\Models\Event::class,
        'marketing' => \App\Models\EmailCampain::class,
        'expense-management' => \App\Models\Expense::class,
        'notice-management' => \App\Models\NoticeBoard::class,
        'blog' => \App\Models\BlogPost::class,
        'portfolio-inspiration' => \App\Models\SpecialSection::class,
        'gallery-setup' => \App\Models\Gallery::class,
        'custom-theme' => \App\Models\ColorTheme::class,
    ];

    protected function normalizePlanMap(array $newPlanMap): array
    {
        return collect($newPlanMap)->mapWithKeys(function ($limit, $slug) {
            $val = strtolower($limit) === 'unlimited' ? null : (int)$limit;
            return [$slug => $val];
        })->all();
    }

    protected function checkModuleCounts(array $newPlanMap): array
    {
        $newPlanMap = $this->normalizePlanMap($newPlanMap);
        $messages = [];
        $status = true;

        foreach ($this->moduleModelMap as $slug => $modelClass) {
            if ($slug === 'employee-management') {
                $count = $modelClass::where('supervisor_id', Auth::user()->id)->count();
            } elseif (in_array($slug, ['marketing', 'expense-management', 'notice-management', 'blog'])) {
                $count = $modelClass::where('created_by', Auth::user()->id)->count();
            } else {
                $count = $modelClass::IsClient()->count();
            }

            if (!array_key_exists($slug, $newPlanMap)) {
                if ($count > 0) {
                    $messages[] = "You have {$count} {$slug} items, but this module is not included in the new plan.";
                    $status = false;
                }
                continue;
            }

            $newLimit = $newPlanMap[$slug];
            if (is_null($newLimit)) {
                continue;
            }
            if ($count > $newLimit) {
                $temp = ucwords(str_replace('-', ' ', $slug));
                $messages[] = "Module '{$temp}' has {$count} items, exceeding the new plan limit of {$newLimit}.";
                $status = false;
            }
        }

        return [$status, $messages];
    }

    public function currentPlanLimitCheck($plan)
    {
        $newPlanModules = json_decode($plan->modules);
        $newPlanMap = [];

        foreach ($newPlanModules as $module) {
            $newPlanMap[$module->slug] = $module->limit;
        }

        [$status, $messages] = $this->checkModuleCounts($newPlanMap);
        return ['status' => $status, 'messages' => $messages];
    }


    public function createSubscriptionPaymentPayload($plan)
    {
        $priceList = [
            [
                'price' => $plan->price_id,
                'quantity' => 1,
            ],
        ];
        $subscriptionPaymentLog = SubscriptionItem::whereHas('subscription', function ($q) {
            $q->where('user_id', Auth::user()->id);
        })->get();

        if ($subscriptionPaymentLog->isEmpty()) {
            if ($plan->setup_fee) {
                $oneTimeFee = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'One-time Setup Fee',
                        ],
                        'unit_amount' => $plan->setup_fee * 100 ?? 0,
                    ],
                    'quantity' => 1,
                ];
                array_push($priceList, $oneTimeFee);
            }
        }
        return $priceList;
    }

    public function createOrderPaymentPayload($order_code, $amount, $quantity = 1)
    {
        return [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $order_code,
                ],
                'unit_amount' => $amount, // Amount in cents
            ],
            'quantity' => $quantity,
        ]];
    }

    public function calculatePrice($amount)
    {

        return intval($amount * 100);
    }

    public function getStripeCredential($id)
    {
        $gatewayCredential = GatewayCredentials::where('payment_method_id', 1)->where('key', 'secret_key')->where('user_id', $id)->first();

        return $gatewayCredential->value;
    }

    public function getStripeWebhookCredential($id)
    {
        $webhook_key = GatewayCredentials::where('payment_method_id', 1)->where('key', 'webhook_key')->where('user_id', $id)->first();
        if ($webhook_key) {
            return $webhook_key->value;
        }

        return null;
    }

    public function successPayment($sessionId, $order_id)
    {
        $session = StripeSession::retrieve($sessionId);

        // Access session data
        $paymentIntentId = $session->payment_intent;
        $amountTotal = $session->amount_total / 100;
        $currency = $session->currency;
        $customer_details = $session->customer_details;
        $payment_status = $session->payment_status;
        $created = $session->created;

        $payment_details = [
            'payment_intent_id' => $paymentIntentId,
            'amount_total' => $amountTotal,
            'currency' => $currency,
            'customer_details' => [
                'name' => $customer_details->name,
                'email' => $customer_details->email,
            ],
            'payment_status' => $payment_status,
            'created' => date('Y-m-d H:i:s', $created),
            'order_id' => $order_id,
        ];

        return [
            'payment_status' => $payment_status,
            'payment_details' => $payment_details,
        ];
    }

    public function generateInvoice($invoice_no)
    {

        $secret_key = $this->getStripeCredential(1);
        Stripe::setApiKey($secret_key);
        $invoice = \Stripe\Invoice::retrieve($invoice_no);
        return $invoice;
    }

    public function cancelSubscriptonImmediately($currentActiveSubscription_no)
    {
        $secret_key = $this->getStripeCredential(1);
        Stripe::setApiKey($secret_key);
        $subscription = Subscription::retrieve($currentActiveSubscription_no, [])->cancel();

        return $subscription;
    }

    public function cancelSubscriptonRevoke($currentActiveSubscription)
    {
        $secret_key = $this->getStripeCredential(1);
        Stripe::setApiKey($secret_key);
        $subscription = Subscription::update($currentActiveSubscription->stripe_subscription_id, [
            'cancel_at_period_end' => true,
        ]);
        return $subscription;
    }

    public function getCustomerCardInfo($customerId)
    {
        $secret_key = $this->getStripeCredential(1);
        Stripe::setApiKey($secret_key);

        return PaymentMethod::all([
            'customer' => $customerId,
            'type' => 'card',
        ]);
    }


    // Time bill payment
    public function makeTimeBillPayment($timeBill, $success_url, $cancel_url)
    {
        if (globalSetting('stripe_payment')->value == 1) { //check if stripe is enable globally

            $payment_status = checkIfStripeIsSetup(getUserTimeBill($timeBill));
            if ($payment_status) {
                $secret_key = $this->getStripeCredential(getUserTimeBill($timeBill));
                Stripe::setApiKey($secret_key);

                $payableAmount = @$timeBill->lastPayment ? $timeBill->lastPayment->current_due : @$timeBill->total_amount;

                $unitAmount = $this->calculatePrice($payableAmount);

                // Define the product and price details
                $lineItems = $this->createOrderPaymentPayload($timeBill->code, $unitAmount);

                // Create the Checkout Session
                $checkoutSession = StripeSession::create([
                    'payment_method_types' => ['card', 'link'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => $success_url . '?session_id={CHECKOUT_SESSION_ID}&time_billing_id=' . $timeBill->id . '&payment_method=stripe',
                    'cancel_url' => $cancel_url,
                ]);

                return $checkoutSession->url;
            }
        }
        return false;
    }

    public function successTimeBillPayment($sessionId, $time_billing_id)
    {
        $session = StripeSession::retrieve($sessionId);

        // Access session data
        $paymentIntentId = $session->payment_intent;
        $amountTotal = $session->amount_total / 100;
        $currency = $session->currency;
        $customer_details = $session->customer_details;
        $payment_status = $session->payment_status;
        $created = $session->created;

        $payment_details = [
            'payment_intent_id' => $paymentIntentId,
            'amount_total' => $amountTotal,
            'currency' => $currency,
            'customer_details' => [
                'name' => $customer_details->name,
                'email' => $customer_details->email,
            ],
            'payment_status' => $payment_status,
            'created' => date('Y-m-d H:i:s', $created),
            'time_billing_id' => $time_billing_id,
        ];

        return [
            'payment_status' => $payment_status,
            'payment_details' => $payment_details,
        ];
    }


    public function makeProposalInvoicePayment($invoice, $success_url, $cancel_url)
    {
        if (globalSetting('stripe_payment')->value == 1) { //check if stripe is enable globally

            $payment_status = checkIfStripeIsSetup(getUserTimeBill($invoice));
            if ($payment_status) {
                $secret_key = $this->getStripeCredential(getUserTimeBill($invoice));
                Stripe::setApiKey($secret_key);

                $payableAmount = @$invoice->lastPayment ? $invoice->lastPayment->current_due : @$invoice->total_amount;

                $unitAmount = $this->calculatePrice($payableAmount);

                // Define the product and price details
                $lineItems = $this->createOrderPaymentPayload($invoice->code, $unitAmount);

                // Create the Checkout Session
                $checkoutSession = StripeSession::create([
                    'payment_method_types' => ['card', 'link'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => $success_url . '?session_id={CHECKOUT_SESSION_ID}&invoice_id=' . $invoice->id . '&payment_method=stripe',
                    'cancel_url' => $cancel_url,
                ]);

                return $checkoutSession->url;
            }
        }
        return false;
    }

    public function successProposalInvoicePayment($sessionId, $time_billing_id)
    {
        $session = StripeSession::retrieve($sessionId);

        // Access session data
        $paymentIntentId = $session->payment_intent;
        $amountTotal = $session->amount_total / 100;
        $currency = $session->currency;
        $customer_details = $session->customer_details;
        $payment_status = $session->payment_status;
        $created = $session->created;

        $payment_details = [
            'payment_intent_id' => $paymentIntentId,
            'amount_total' => $amountTotal,
            'currency' => $currency,
            'customer_details' => [
                'name' => $customer_details->name,
                'email' => $customer_details->email,
            ],
            'payment_status' => $payment_status,
            'created' => date('Y-m-d H:i:s', $created),
            'time_billing_id' => $time_billing_id,
        ];

        return [
            'payment_status' => $payment_status,
            'payment_details' => $payment_details,
        ];
    }
}
