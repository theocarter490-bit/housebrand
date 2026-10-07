<?php

namespace App\Services;


use App\Models\GatewayCredentials;
use GuzzleHttp\Client;

class PaypalService
{
    private $baseURL = "https://sandbox.paypal.com";
    private $accessToken;

    public function makeOrderPayment($order, $success_url, $cancel_url)
    {
        if (globalSetting('paypal_payment')->value == 1) { //check if paypal is enable globally
            if (checkIfPaypalIsSetup($order->seller_id)) {
                try {
                    $this->accessToken = $this->getAccessToken($order);

                    $payableAmount = @$order->lastPayment ? $order->lastPayment->current_due : @$order->grand_total_amount;
                    $orderPayload = $this->createOrderPaymentPayload($order->code, $payableAmount);

                    $client = new Client();
                    $response = $client->post($this->baseURL . '/v2/checkout/orders', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $this->accessToken,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'intent' => 'CAPTURE',
                            'purchase_units' => $orderPayload,
                            "user_action"=> "PAY_NOW",
                            'application_context' => [
                                "payment_method_preference"=> "IMMEDIATE_PAYMENT_REQUIRED",
                                'return_url' => $success_url."?order_id=".$order->id.'&payment_method=paypal', // Append order_id to the success URL
                                'cancel_url' => $cancel_url,
                            ],
                        ],
                    ],
                    );

                    $responseBody = json_decode($response->getBody(), true);
                    foreach ($responseBody['links'] as $link) {
                        if ($link['rel'] === 'approve') {
                            return $link['href'];
                        }
                    }

                    throw new \Exception('Failed to create PayPal order.');
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            }
        }

        return false;

    }

    public function createOrderPaymentPayload($order_code, $amount, $quantity = 1)
    {
        return [[
            "order_code" => $order_code,
            "amount" => [
                "currency_code" => "USD",
                "value" => $amount
            ]
        ]];
    }

    public function getPaypalSecretKeyCredential($id)
    {
        $gatewayCredential = GatewayCredentials::where('payment_method_id', 2)->where('key', 'client_secret')->where('user_id', $id)->first();

        return $gatewayCredential->value;
    }

    public function getPaypalClientIDCredential($id)
    {
        $gatewayCredential = GatewayCredentials::where('payment_method_id', 2)->where('key', 'client_id')->where('user_id', $id)->first();

        return $gatewayCredential->value;
    }


    public function getAccessToken($order)
    {
        $client = new Client();
        $response = $client->post($this->baseURL . '/v1/oauth2/token', [
            'auth' => [$this->getPaypalClientIDCredential($order->seller_id), $this->getPaypalSecretKeyCredential($order->seller_id)],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody['access_token'] ?? null;
    }

}
