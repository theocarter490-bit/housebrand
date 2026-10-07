<!DOCTYPE html>

<html lang="en" class="light-style layout-wide" dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>House Brand</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->

</head>

<body>
    <!-- Content -->

    <div>
        <div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="vertical-align: top;">
                        <div class="d-flex svg-illustration mb-4 gap-2 align-items-center">
                            <div class="app-brand-logo demo">
                                <img src="{{ getFilePath(shopSetting()->logo) }}" width="50" height="50"
                                    alt="">
                            </div>
                            <span class="app-brand-text fw-bold fs-4"> {{ shopSetting()->shop_name }} </span>
                        </div>
                        <p class="mb-2">{!! str_replace(',', '<br>', shopSetting()->location) !!}</p>
                        <p class="mb-0">{{ shopSetting()->phone }}</p>
                        <p class="mb-0">{{ shopSetting()->email }}</p>
                    </td>
                    <td style="vertical-align: top; text-align: right">
                        <h4>INVOICE: {{ $order->code }}</h4>
                        <div>
                            <span>Date Issues:</span>
                            <span class="fw-medium">{{ dateFormat(date('Y/m/d')) }}</span>
                        </div>
                    </td>
                </tr>
            </table>
            <hr class="my-0" />
            <div class="card-body">
                <div class="row p-sm-3 p-0">
                    <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-4 mb-sm-0 mb-4">
                        <h6 class="mb-3">Invoice To:</h6>
                        <p class="mb-1">{{ optional($order->shipping_address)->name }}</p>
                        <p class="mb-1">{{ optional($order->shipping_address)->street_address }}</p>
                        <p class="mb-0">{{ optional($order->shipping_address)->state }}</p>
                        <p class="mb-0">{{ optional($order->shipping_address)->country }}
                            {{ optional($order->shipping_address)->zip_code }}</p>
                        <p class="mb-0">{{ optional($order->shipping_address)->phone }}</p>
                        <p class="mb-0">{{ optional($order->shipping_address)->email }}</p>
                    </div>
                </div>
            </div>
            <div class="table-responsive border-top">
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                    <thead>
                        <tr>

                            <th style="border: 1px solid #4e4e4e; padding: 10px; background-color: #f2f2f2;">Item </th>
                            <th style="border: 1px solid #4e4e4e; padding: 10px; background-color: #f2f2f2;">Variation
                            </th>
                            <th style="border: 1px solid #4e4e4e; padding: 10px; background-color: #f2f2f2;">Price</th>
                            <th style="border: 1px solid #4e4e4e; padding: 10px; background-color: #f2f2f2;">Qty</th>
                            <th style="border: 1px solid #4e4e4e; padding: 10px; background-color: #f2f2f2;">Total</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $cart_item)
                            <tr>

                                <td style="border: 1px solid #4e4e4e; padding: 10px;">
                                    {{ optional($cart_item->product)->name }}
                                </td>
                                <td style="border: 1px solid #4e4e4e; padding: 10px;">

                                    @foreach ($cart_item->variation as $key => $item)
                                        <span><b class="me-1">{{ $item['attribute'] }}:</b><span
                                                class="text-primary">{{ $item['value'] }}</span></span><br>
                                    @endforeach

                                </td>
                                <td style="border: 1px solid #4e4e4e; padding: 10px;">
                                    <span>{{ getPriceFormat($cart_item->price) }}</span>
                                </td>

                                </td>
                                <td style="border: 1px solid #4e4e4e; padding: 10px;">

                                    <span>{{ $cart_item->quantity }}</span>

                                </td>
                                <td style="border: 1px solid #4e4e4e; padding: 10px;">
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

</body>

</html>
