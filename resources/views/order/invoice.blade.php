<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{generalSetting()->site_title}}| Order Invoice</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">
    <style media="all">
        * {
            font-family: DejaVu Sans, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            line-height: 1.1;
            font-family: 'Roboto';
            color: #333542;
        }

        body {
            font-size: 0.688rem;
        }

        .gry-color *,
        .gry-color {
            color: #878f9c;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table td {
            font-family: 'SolaimanLipi', sans-serif !important;
        }

        table.padding th {
            padding: 1rem .7rem;
        }

        table.padding td {
            padding: 1rem .7rem;
        }

        table.sm-padding td {
            padding: 1rem .7rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

    </style>
</head>
<body>
@if(!empty($messageContent))
    <div style="margin-bottom: 20px;">
        {!! nl2br(e($messageContent)) !!}
    </div>
@endif
<div style="margin-left:auto;margin-right:auto;">

    <div style="background: #eceff4;padding: 1.5rem; margin-bottom:20px">
        <div style="width: 85%;margin: auto;">
            <table>
                <tr>
                    <td>
                        <img class="pl-3" src="{{getFilePath($setting->logo)}}" alt="alt" height="60" width="120"/>
                    </td>
                    <td style="font-size: 2.5rem;" class="text-right strong">INVOICE</td>
                </tr>
            </table>
            <table>

                <tr>
                    <td class="small">Shop: {{$setting->shop_name}} </td>
                    <td class="text-right small"><span class=" small">Invoice ID : #{{ @$order->code }}</td>

                </tr>
                <tr>
                    <td class="small">Phone: {{$setting->phone}}</td>
                    <td class="text-right small"><span
                            class=" small">Order Date:{{dateFormatwithTime($order->order_date)}}</span></td>
                </tr>
                <tr>
                    <td class=" small">Email: {{$setting->email}}</td>
                    <td class="text-right small">
                        <span class=" small">Order Status:<strong>{{ $order->orderStatus->name }}</strong></span>
                    </td>
                </tr>
                <tr>
                    <td class="small">Address: {{$setting->location}}</td>
                    <td class="text-right small"><span class=" small">Payment Status:
                        @if ($order->payment_status == 'paid')
                                {{_trans('keyword.Paid')}}
                            @elseif ($order->payment_status == 'partial')
                                {{_trans('keyword.Partially Paid')}}
                            @else
                                {{_trans('keyword.Unpaid')}}
                            @endif
                        </span>
                    </td>

                </tr>
                <tr>
                    <td class="small"></td>
                    <td style="padding-top: 10px;" class=" text-right small "><h4>Invoice To:</h4>
                        <p class="mb-1">{{ optional($order->shipping_address)->name }}</p>
                        <p class="mb-0">{{ optional($order->shipping_address)->phone }}</p>
                        <p class="mb-0">{{ optional($order->shipping_address)->email }}</p>
                        <p class="mb-1">{{ optional($order->shipping_address)->street_address }}
                            , {{ optional($order->shipping_address)->state }}
                            ,{{ optional($order->shipping_address)->country }}
                            , {{ optional($order->shipping_address)->zip_code }}</p>
                    </td>
                </tr>
            </table>

        </div>
    </div>
    <div style="width:80%;margin: auto;">
        <table class="padding text-left small border-bottom table-bordered">
            <thead>
            <tr style="background: #eceff4; font-style:bold; font-size: 14px">
                <th width="10%" class="text-center">SL</th>
                <th width="15%" class="text-center">Item</th>
                <th width="15%" class="text-center">Variation</th>
                <th width="15%" class="text-right">Price</th>
                <th width="15%" class="text-right">Qty</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
            </thead>
            <tbody class="strong">

            @foreach ($order->items as $key=>$cart_item)
                <tr>
                    <td class="text-center"> {{ $key + 1 }} </td>
                    <td class="text-center"> {{ optional($cart_item->product)->name }} </td>
                    <td class="text-center">
                        @foreach ($cart_item->variation as $key => $item)
                            <span><b class="me-1">{{ @$item['attribute'] }}:</b><span
                                    class="text-primary">{{ @$item['value'] }}</span></span><br>
                        @endforeach
                    </td>
                    <td class="text-right"><span>{{ getPriceFormat($cart_item->price) }}</span></td>
                    </td>
                    <td class="text-right"><span>{{ $cart_item->quantity }}</span></td>
                    <td class="text-right">
                        {{ getCurrency() }}
                        <span>{{ number_format($cart_item->price * $cart_item->quantity, 2) }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="width: 100%; margin-top:20px" class="row">
            <div class="col-xl-5 col-md-6 ml-auto mr-0">
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Subtotal') }}<b></th>
                        <td class="text-right" style="font-size: 14px"><b>{{ getPriceFormat($order->sub_total_amount) }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Discount')}}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($order->admin_discount_amount) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Tax')}}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($order->tax_amount) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Shipping Charge')}}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($order->shipping_charges) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Grand Total')}}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($order->grand_total_amount) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Total Paid')}}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($order->paymentDetails->sum('amount')) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Due Amount')}}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat(calculateOrderDue($order)) }}</b>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <br>
                <br>
                <br>
                <p><b>In Words</b> :
                    <i> {{ numberTowords(@$order->grand_total_amount) }} {{ generalSetting()->currency->code}} Only</i>
                </p>

            </div>

            <div style="margin-top: 100px">
                <p class="mt-0 text-center fw-bold">Thanks for your business</p>
                <p class="text-center"><span class="fw-medium">Note:</span> Thank you for choosing us! We appreciate
                    your business and can’t wait to serve you again.</p>
            </div>
        </div>
    </div>
</div>
</body>
@if(isset($forPrint))
    <script>
        window.onload = function () {
            window.print();
        };
    </script>
@endif
</html>
