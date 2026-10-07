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
            table-layout: fixed; /* Add this */
        }


        table th {
            font-weight: normal;
        }


        table td p {
            margin: 0; /* Remove margins from paragraphs to avoid extra space */
        }


        table td {
            font-family: 'SolaimanLipi', sans-serif !important;
            word-wrap: break-word; /* Allow long words to be broken */
            overflow-wrap: break-word; /* Similar to word-wrap */
            white-space: normal; /* Allow text to wrap to the next line */
        }

        table.padding th, table.padding td {
            padding: 0.5rem; /* Reduced padding */
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
                        <span style="font-size: 1.5rem;" class="strong">Activity Log Report</span>
                        @if($user)
                            <br><span class="strong">User: {{ $user->name }}</span>
                            <br><span class="strong">Designation: {{$user->role->name}}</span>
                        @endif
                        <br><span class="strong">{{ date('d-m-y h:i a') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div style="border-bottom:1px solid #eceff4;margin: 0 1.5rem;"></div>

    <div style="width:90%;margin: auto; margin-top:25px; margin-bottom:25px;">
        <table class="padding text-left small border-bottom table-bordered" style="table-layout: fixed;">
            <thead>
            <tr style="background: #eceff4; font-weight: bold; font-size: 14px">
                <th scope="col" width="5%">SL</th>
                <th scope="col" width="10%">Model</th>
                <th scope="col" width="10%">Event</th>
                <th scope="col" width="20%">Old Data</th>
                <th scope="col" width="20%">New Data</th>
                <th scope="col" width="10%">IP</th>
                <th scope="col" width="15%">Agent</th>
                <th scope="col" width="10%">Time</th>
            </tr>
            </thead>
            <tbody class="strong">
            @forelse ($audits as $key => $audit)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ Str::afterLast($audit['auditable_type'], '\\') }}</td>
                    <td>{{ $audit['event'] }}</td>
                    <td>
                        @foreach ($audit['old_values'] as $oldValKey => $item)
                            <p>{{ $oldValKey }}: <span class="text-danger">{{ $item }}</span></p>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($audit['new_values'] as $newValKey => $item)
                            <p>{{ $newValKey }}: <span class="text-success">{{ $item === false ? "0" : $item }}</span></p>
                        @endforeach
                    </td>
                    <td>{{ $audit['ip_address'] }}</td>
                    <td>{{ $audit['user_agent'] }}</td>
                    <td>{{ dateFormatwithTime($audit['created_at']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center"><h4>No data available</h4></td>
                </tr>
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
