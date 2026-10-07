@extends('layouts.master')

@section('title', $title ?? __('Invoice'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        <div class="row invoice-preview">
            <!-- Invoice -->
            {!! breadcrumb('Invoice', ['#' => 'Order Management', 'order' => 'Invoice']) !!}

            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card" id="invoice-preview-card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between flex-md-row flex-column m-sm-3 m-0">
                            <div class="mb-xl-0 mb-4">
                                <div class="d-flex svg-illustration mb-4 gap-2 align-items-center">
                                    <div class="app-brand-logo demo">
                                        <img src="{{ getFilePath(@$order->shop->logo) }}" width="50" height="50"
                                             alt="">
                                    </div>
                                </div>
                                <span class="app-brand-text fw-bold fs-4"> {{ @$order->shop->shop_name }} </span>
                                <p class="mb-2">{!! str_replace(',', '<br>', @$order->shop->location) !!}</p>
                                <p class="mb-0">{{ @$order->shop->phone }}</p>
                                <p class="mb-0">{{ @$order->shop->email }}</p>
                            </div>
                            <div>
                                <h4 class="fw-medium mb-2">INVOICE</h4>
                                <div class="mb-2 pt-1">
                                    <p class="mb-0">Invoice ID : <span>#{{ @$order->code }}</span></p>
                                    <p class="mb-0">Order Date :<span> {{dateFormatwithTime($order->order_date)}}</span>
                                    </p>
                                    <p class="mb-0">Order Status :<span
                                            class="fw-medium"> {{ $order->orderStatus->name }}</span></p>
                                    <p class="mb-0">Payment Status:
                                        @if ($order->payment_status == 'paid')
                                            <strong>{{_trans('keyword.Paid')}}</strong>
                                        @elseif ($order->payment_status == 'partial')
                                            <strong>{{_trans('keyword.Partially Paid')}}</strong>
                                        @else
                                            <strong>{{_trans('keyword.Unpaid')}}</strong>
                                        @endif
                                    </p>
                                </div>
                                <div class="fw-medium mb-2">
                                    <h5 class="mb-2 pt-1">Invoice To:</h5>
                                    <p class="mb-1">{{ optional($order->shipping_address)->name }}</p>
                                    <p class="mb-0">{{ optional($order->shipping_address)->phone }}</p>
                                    <p class="mb-0">{{ optional($order->shipping_address)->email }}</p>
                                    <p class="mb-1">{{ optional($order->shipping_address)->street_address }}
                                        , {{ optional($order->shipping_address)->state }}
                                        ,{{ optional($order->shipping_address)->country }}
                                        , {{ optional($order->shipping_address)->zip_code }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0"/>
                    <div class="table-responsive border-top">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th>Variation</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($order->items as $cart_item)
                                <tr>

                                    <td>
                                        {{ optional($cart_item->product)->name }}
                                    </td>
                                    <td>

                                        @foreach ($cart_item->variation as $key => $item)
                                            <span><b class="me-1">{{ @$item['attribute'] }}:</b><span
                                                    class="text-primary">{{ @$item['value'] }}</span></span><br>
                                        @endforeach

                                    </td>
                                    <td>
                                        <span>{{ getPriceFormat($cart_item->price) }}</span>
                                    </td>

                                    </td>
                                    <td>

                                        <span>{{ $cart_item->quantity }}</span>

                                    </td>
                                    <td>
                                        {{ getCurrency() }}
                                        <span>{{ number_format($cart_item->price * $cart_item->quantity, 2) }}</span>

                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="align-top px-4 py-4">
                                    <span>Thanks for your business</span>
                                </td>
                                <td class="text-end pe-3 py-4">
                                    <p class="mb-2 pt-3 fw-bold">Subtotal:</p>
                                    <p class="mb-2">Discount:</p>
                                    <p class="mb-2">Tax:</p>
                                    <p class="mb-2">Shipping Change:</p>
                                    <p class="mb-2 fw-bold">Grand Total:</p>
                                    <p class="mb-2 fw-bold">Total Paid:</p>
                                    <p class="mb-2 fw-bold">Due Amount:</p>
                                </td>
                                <td class="ps-2 py-4">
                                    <p class="fw-bold mb-2 pt-3">{{ getPriceFormat($order->sub_total_amount) }}</p>
                                    <p class="fw-medium mb-2">{{ getPriceFormat($order->admin_discount_amount) }}</p>
                                    <p class="fw-medium mb-2">{{ getPriceFormat($order->tax_amount) }}</p>
                                    <p class="fw-medium mb-2">{{ getPriceFormat($order->shipping_charges) }}</p>
                                    <p class="fw-bold mb-2">{{ getPriceFormat($order->grand_total_amount) }}</p>
                                    <p class="fw-bold mb-2 ">{{ getPriceFormat($order->paymentDetails->sum('amount')) }}</p>
                                    <p class="fw-bold mb-2">{{ getPriceFormat(calculateOrderDue($order)) }}</p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body mx-3">
                        <div class="row">
                            <div class="col-12">
                                <span class="fw-medium">Note:</span>
                                <span>Thank you for choosing us! We appreciate your business and can’t wait to serve you again.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Invoice -->

            <!-- Invoice Actions -->
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card">
                    <div class="card-body">
                        <a class="btn btn-label-primary d-grid w-100 mb-2" target="_blank"
                           href="{{ route('order.invoicePrint', $order->id) }}">
                            Print
                        </a>
                        <a class="btn btn-label-success d-grid w-100 mb-2" target="_blank"
                           href="{{ route('order.invoiceDownload', $order->id) }}">
                            Download
                        </a>
                        @if ($order->seller_id == auth()->id() && $order->payment_status != 'paid')
                            <a href="{{ route('order.edit', $order->id) }}" class="btn btn-label-warning d-grid w-100 mb-2">
                                Edit Invoice
                            </a>
                        @endif
                        @if ($order->payment_status != 'unpaid')
                            <a class="btn btn-label-primary d-grid w-100 mb-2 waves-effect d-flex align-items-center"
                               href="{{ route('order.paymentHistry', $order->id) }}"
                               title="Payment History">{{ __('Payment History') }}</a>
                        @endif
                        @if ($order->payment_status != 'paid')
                            @if(checkIfStripeIsSetup($order->seller_id))
                                <a href="{{ route('myOrder.order.makePayment.stripe', $order->id) }}"
                                   class="btn btn-label-primary d-grid w-100 mb-2 waves-effect d-flex align-items-center">
                                    {{_trans('keyword.Make Payment')}} -> <img
                                        src="{{asset('assets\img\payment-method\stripe.png')}}" alt="Stripe Payment"
                                        height="30" width="auto">

                                </a>
                            @endif
                            @if(checkIfPaypalIsSetup($order->seller_id))
                                <a href="{{ route('myOrder.order.makePayment.paypal', $order->id) }}"
                                   class="btn btn-label-primary d-grid w-100 mb-2 waves-effect d-flex align-items-center">
                                    {{_trans('keyword.Make Payment')}} -> <img
                                        src="{{asset('assets\img\payment-method\paypal.png')}}" alt="Stripe Payment"
                                        height="20" width="auto">
                                </a>
                            @endif
                        @endif

                    </div>
                </div>
            </div>

            <!-- /Invoice Actions -->
        </div>

@endsection
