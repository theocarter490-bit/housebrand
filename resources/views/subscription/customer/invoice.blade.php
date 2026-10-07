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
                                <img src="{{ getFilePath(administratorSetting()->logo) }}" width="50" height="50"
                                    alt="">
                            </div>
                            <span class="app-brand-text fw-bold fs-4"> {{ administratorSetting()->shop_name }} </span>
                        </div>
                        <h6 class="mb-1 fw-bold">Bill From:</h6>
                        <p class="mb-2">{!! str_replace(',', '<br>', administratorSetting()->location) !!}</p>
                        <p class="mb-0">{{ administratorSetting()->phone }}</p>
                        <p class="mb-0">{{ administratorSetting()->email }}</p>
                    </td>
                    <td style="vertical-align: top; text-align: right">
                        <h4>INVOICE: {{ $invoice->id }}</h4>
                        <div>
                            <span>Date Issues:</span>
                            <span class="fw-medium">{{ dateFormat($invoice->started_at) }}</span>
                        </div>

                        <h6 class="mb-1 fw-bold">Bill To:</h6>

                        <p class="mb-0">{{ $invoice->customer_name }}</p>
                        <p class="mb-0">{{ $invoice->customer_email }}</p>
                </tr>
            </table>
            <hr class="my-0" />

            <div class="table-responsive border-top">
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                    <thead>
                        <tr>

                            <th style="border: 1px solid #4e4e4e; padding: 10px; background-color: #f2f2f2;">Item </th>
                            <th style="border: 1px solid #4e4e4e; padding: 10px; background-color: #f2f2f2;">Created
                                Date
                            </th>
                            <th style="border: 1px solid #4e4e4e; padding: 10px; background-color: #f2f2f2;">Expire Date
                            </th>
                            <th style="border: 1px solid #4e4e4e; padding: 10px; background-color: #f2f2f2;">Price</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->lines->data as $item)
                        <tr>

                            <td style="border: 1px solid #4e4e4e; padding: 10px;">
                                {{ optional($item)->description }}
                            </td>

                            <td style="border: 1px solid #4e4e4e; padding: 10px;">
                                <span>{{ gmdate('Y-m-d', optional($item)->period->start) }}</span>
                            </td>

                            </td>
                            <td style="border: 1px solid #4e4e4e; padding: 10px;">

                                <span>{{ gmdate('Y-m-d', optional($item)->period->end) }}</span>

                            </td>
                            <td style="border: 1px solid #4e4e4e; padding: 10px;">

                                <span>{{ getPriceFormat(optional($item)->price->unit_amount / 100) }}</span>

                            </td>
                        </tr>
                        @endforeach

                        <tr>
                            <td colspan="2" class="align-top px-4 py-4">
                                <span>Thanks for your business</span>
                            </td>
                            <td class="text-end pe-3 py-4">

                                <p class="mb-0 pb-3">Total:</p>
                            </td>
                            <td class="ps-2 py-4">

                                <p class="fw-medium mb-0 pb-3">{{ getPriceFormat($invoice->amount_paid / 100) }}</p>
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
