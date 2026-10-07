<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use App\Models\GatewayCredentials;
use App\Models\PaymentMethodStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = ['stripe','paypal','cash'];
        $stripeKeys = ['public_key','secret_key'];
        $paypalKeys = ['client_id','client_secret','mode'];

        foreach ($methods as $method) {
            $meth = new PaymentMethod();
            $meth->name = textCapitalize($method) . ' Payment';
            $meth->user_id = 1;
            $meth->logo = 'assets/img/payment-method/'.$method.'.png';
            $meth->type = 0;
            $meth->save();

            // GatewayCredentials keys
            if ($method == 'stripe') {
                foreach ($stripeKeys as $key) {
                    $cred = new GatewayCredentials();
                    $cred->user_id = 1;
                    $cred->payment_method_id = $meth->id;
                    $cred->key = $key;
                    $cred->value = '';
                    $cred->save();
                }
            } elseif ($method == 'paypal') {
                foreach ($paypalKeys as $key) {
                    $cred = new GatewayCredentials();
                    $cred->user_id = 1;
                    $cred->payment_method_id = $meth->id;
                    $cred->key = $key;
                    $cred->value = '';
                    $cred->save();
                }
            }

            $status = new PaymentMethodStatus();
            $status->user_id = 1;
            $status->payment_method_id = $meth->id;
            $status->save();
        }
    }
}
