<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ generalSetting()->site_title }}| Order Invoice</title>
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
<div style="margin-left:auto;margin-right:auto;">

    <div style="background: #eceff4;padding: 1.5rem; margin-bottom:20px">
        <div style="width: 85%;margin: auto;">
            <table>
                <tr>
                    <td>
                        <img class="pl-3" src="{{ getFilePath($setting->logo) }}" alt="alt" height="60"
                             width="120"/>
                    </td>
                    <td style="font-size: 2.5rem;" class="text-right strong">INVOICE</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="small">
                        <p>Shop: {{ $setting->shop_name }}</p>
                        <p>Phone: {{ $setting->phone }}</p>
                        <p>Email: {{ $setting->email }}</p>
                        <p>Address: {{ $setting->location }}</p>
                    </td>
                    <td class=" text-right small ">
                        <h4>Invoice To:</h4>
                        <p class="mb-1">{{optional($invoice->project->client)->name }}</p>
                        <p class="mb-0">{{optional($invoice->project->client)->phone }}</p>
                        <p class="mb-0">{{optional($invoice->project->client)->email }}</p>
                        <p class="mb-1">{{optional($invoice->project->client)->address }}</p>
                        <br>
                        <p class="mb-0">Invoice ID : <span>#{{ $invoice->code }}</span></p>
                        <p class="mb-0">Date :<span> {{dateFormatwithTime($invoice->created_at)}}</span></p>
                        <p class="mb-0 mt-1">Payment Status:
                            @if ($invoice->payment_status == 1)
                                <span class="badge bg-label-success">{{_trans('keyword.Paid')}}</span>
                            @elseif ($invoice->payment_status == 2)
                                <span
                                    class="badge bg-label-warning">{{_trans('keyword.Partially Paid')}}</span>
                            @else
                                <span class="badge bg-label-danger">{{_trans('keyword.Unpaid')}}</span>
                            @endif
                        </p>
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
                <th width="15%" class="text-right">Type</th>
                <th width="15%" class="text-right">Variation</th>
                <th width="15%" class="text-right">Price</th>
                <th width="15%" class="text-right">Qty</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
            </thead>
            <tbody class="strong">

            @foreach($invoice->items as $item)
                <tr>
                    <td class=""> {{ $loop->iteration }} </td>
                    <td class=""> {{ $item->type == 1?$item->product->name: @$item->service->title }} </td>
                    <td class=""> {{ @$item->type == 1? 'Product' : 'Service' }} </td>
                    <td class="">
                        @forelse($item->variation as $key => $variant)
                            <span>
                                                <b class="me-1">{{ @$variant['attribute'] }}:</b>
                                                <span class="text-primary">{{ @$variant['value'] }}</span>
                                            </span>
                            <br>
                        @empty
                            <span>---</span>
                        @endforelse
                    </td>
                    <td class="text-right"><span>{{ getPriceFormat($item->unit_price) }}</span></td>
                    <td class="text-right"><span>{{ $item->quantity }}</span></td>
                    <td class="text-right"><span>{{ getPriceFormat($item->price) }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="width: 100%; margin-top:20px" class="row">
            <div class="col-xl-5 col-md-6 ml-auto mr-0">
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Sub Total') }}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($invoice->sub_total) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Discount') }}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($invoice->discount_amount) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Tax') }}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($invoice->tax_amount) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Shipping Charge') }}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($invoice->shipping_charge) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Discount') }}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($invoice->discount_amount) }}</b>
                        </td>
                    </tr>

                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __(' Total') }}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($invoice->total_amount) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Total Paid') }}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat($invoice->paymentDetails->sum('amount')) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Due Amount') }}</b></th>
                        <td class="text-right" style="font-size: 14px">
                            <b>{{ getPriceFormat(calculateProposalInvoiceDue($invoice)) }}</b>
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
                    <i> {{ numberTowords(@$invoice->total_amount) }} {{ generalSetting()->currency->code }}
                        Only</i>
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

</html>
