<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'title' => 'How to create an account?',
                'description' => 'To create an account, click on the "Sign Up" button on the top right corner of the page. Fill in the required information and click on the "Sign Up" button.',
            ],
            [
                'title' => 'How to reset my password?',
                'description' => 'To reset your password, click on the "Forgot Password" link on the login page. Enter your email address and click on the "Reset Password" button. You will receive an email with a link to reset your password.',
            ],
            [
                'title' => 'How to place an order?',
                'description' => 'To place an order, follow these steps:
                1. Browse the website and add the items you want to purchase to your cart.
                2. Click on the cart icon on the top right corner of the page to view your cart.
                3. Review the items in your cart and click on the "Checkout" button.
                4. Enter your shipping address and payment information.
                5. Review your order and click on the "Place Order" button to complete your purchase.',
            ],
            [
                'title' => 'How to track my order?',
                'description' => 'To track your order, click on the "Track Order" link on the top right corner of the page. Enter your order number and email address to view the status of your order.',
            ],
            [
                'title' => 'How to contact customer support?',
                'description' => 'To contact customer support, click on the "Contact Us" link on the top right corner of the page. Fill in the required information and click on the "Submit" button. A customer support representative will get back to you as soon as possible.',
            ],
        ];


        foreach ($faqs as $f) {
            $faq                = new Faq();
            $faq->title         = $f['title'];
            $faq->description   = $f['description'];
            $faq->user_id       = Role::SUPER_ADMIN;
            $faq->save();
        }
    }
}
