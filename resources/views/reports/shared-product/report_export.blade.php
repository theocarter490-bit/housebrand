<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ generalSetting()->site_title }}| Report</title>
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

        footer {
                position: fixed;
                bottom: 0px;
                left: 0px;
                right: 0px;
                height: 30px;
                font-size: 14px !important;
                background-color: #eceff4;
                color: #000000;
                text-align: center;
                line-height: 25px;
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
                            <span style="font-size: 1.5rem;" class="strong">Shared Product Report</span>

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
                        <th width="10%" class="text-center">Name</th>
                        <th width="15%" class="text-center">Category</th>
                        <th width="15%" class="text-center">Shared With</th>
                </thead>
                <tbody class="strong">
                    @forelse ($products as $key=>$product)
                        <tr>
                            <td class="text-center">{{ $product->name }}</td>
                            <td class="text-center">{{ $product->category->name }}</td>
                            <td class="text-center">{{ $product->shared_product_count }} Shop</td>
                        </tr>
                    @empty
                        <h4 style="text-align: center;">No data available</h5>
                    @endforelse
                </tbody>
            </table>

            <div style="width:70%;margin: auto; margin-top:25px; margin-bottom:25px;" class="row">
                <div class="col-xl-5 col-md-6 ml-auto mr-0">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th class="text-center" style="font-size: 14px"><b>{{ __('Total Shared Product') }}<b></th>
                                <td class="text-left" style="font-size: 14px"><b>{{ $products->sum('shared_product_count') }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <footer>
            Date: <?php echo date("Y-m-d h:m:s A");?>
        </footer>
    </div>
</body>

</html>
