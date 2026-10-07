<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{generalSetting()->site_title}}| Order Invoice</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">
    <style media="all">
        * { font-family: DejaVu Sans, sans-serif; }

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

    <div style="background: #eceff4;padding: .5rem;">
        <div style="width: 85%;margin: auto;">
            <table>
                <tr>
                    <td>
                        <img class="pl-3" src="{{getFilePath(shopSetting()->logo)}}" alt="alt" height="50" width="130" />
                    </td>
                    <td class="text-right">
                        <span style="font-size: 1.5rem;" class="strong">Expense Report</span>
                        @if ($expense_type != "")
                            <br>Expense Type: {{ @$typeName }}
                        @endif
                        @if ($start_date != "")
                            <br>From: {{dateFormat($start_date)}}
                        @endif
                        @if ($end_date != "")
                            <br>To: {{dateFormat($end_date)}}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div style="border-bottom:1px solid #eceff4;margin: 0 1.5rem;"></div>

    <div style="width:90%;margin: auto; margin-top:25px; margin-bottom:25px;">
        <table class="padding text-left small border-bottom table-bordered">
            <thead>
            <tr style="background: #eceff4; font-style:bold; font-size: 14px">
                <th width="10%" class="text-center">SL</th>
                <th width="15%" class="text-center">Expense Title</th>
                <th width="15%" class="text-center">Type</th>
                <th width="15%" class="text-center">Expense Date</th>
                <th width="15%" class="text-center">Payment Method</th>
                <th width="15%" class="text-right">Amount</th>
            </tr>
            </thead>
            <tbody class="strong">
                @forelse ($expenses as $key=>$expense)
                <tr>
                    <td class="text-center">#{{$key+1}}</td>
                    <td class="text-center">{{ @$expense->title }}</td>
                    <td class="text-center">{{ @$expense->expenseType->name }}</td>
                    <td class="text-center">{{ dateFormat(@$expense->expense_date) }}</td>
                    <td class="text-center">{{ @$expense->paymentMethod->name }}</td>
                    <td class="text-right">{{ getPriceFormat(@$expense->amount) }}</td>
                </tr>
                @empty
                    <h4 style="text-align: center;">No data available</h5>
                @endforelse
            </tbody>
        </table>

        <div style="width: 100%" class="row">
            <div class="col-xl-5 col-md-6 ml-auto mr-0">
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Total Expense') }}<b></th>
                        <td class="text-right" style="font-size: 14px"><b>{{ getPriceFormat($expenses->sum('amount')) }} </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <br>
                <br>
                <br>
                <p><b>In Words</b> : <i> {{ numberTowords($expenses->sum('amount')) }} {{ generalSetting()->currency->code}} Only</i></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
