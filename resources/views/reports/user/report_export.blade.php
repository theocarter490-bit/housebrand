<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ generalSetting()->site_title }}| Order Invoice</title>
    <meta http-equiv="Content-Type" content="text/html;" />
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

        <div style="background: #eceff4;padding: .5rem;">
            <div style="width: 85%;margin: auto;">
                <table>
                    <tr>
                        <td>
                            <img class="pl-3" src="{{ getFilePath(shopSetting()->logo) }}" alt="alt"
                                height="50" width="130" />
                        </td>
                        <td>

                        </td>
                        <td class="text-right">
                            <span style="font-size: 1.5rem;" class="strong">User Report</span>
                            <br><span class="strong">{{ date('d-m-y h:i a') }}</span>
                            @if ($role_type != '')
                                <br>Role By: @if ($role_type == 3)
                                    Designer
                                @elseif($role_type == 4)
                                    Customer
                                @elseif($role_type == 5)
                                    Manufacturer
                                @endif
                            @endif
                            @if ($active_status != '')
                                <br>Published By: {{ @$active_status == 1 ? 'Active' : 'Inactive' }}
                            @endif

                            @if ($start_date != '')
                                <br>From: {{ dateFormat($start_date) }}
                            @endif
                            @if ($end_date != '')
                                <br>To: {{ dateFormat($end_date) }}
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
                        <th width="10%" class="text-center">Info</th>
                        <th width="15%" class="text-center">Role</th>
                        <th width="15%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="strong">
                    @forelse ($users as $key=>$user)
                        <tr>
                            <td class="text-center">Name: {{ $user->name }}<br>Phone: {{ $user->phone }}<br>Email:
                                {{ $user->email }} </td>
                            <td class="text-center">
                                @if ($user->role_id == 3)
                                    Designer
                                @elseif($user->role_id == 4)
                                    Customer
                                @elseif($user->role_id == 5)
                                    Manufacturer
                                @endif
                            </td>
                            <td class="text-center">{{ $user->active_status == 1 ? 'Active' : 'Deactive' }}
                            </td>

                        </tr>
                    @empty
                        <h4 style="text-align: center;">No data available</h5>
                    @endforelse
                </tbody>
            </table>

            <div style="width: 100%" class="row">
                <div class="col-xl-5 col-md-6 ml-auto mr-0">
                    {{-- <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th class="text-right" style="font-size: 14px"><b>{{ __('Total Expense') }}<b></th>
                        <td class="text-right" style="font-size: 14px"><b>{{ getPriceFormat($expenses->sum('amount')) }} </td>
                    </tr>
                    </tbody>
                </table> --}}
                </div>
                <div class="col-md-12">
                    <br>
                    <br>
                    <br>
                    {{-- <p><b>In Words</b> : <i> {{ numberTowords($expenses->sum('amount')) }} {{ generalSetting()->currency->code}} Only</i></p> --}}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
