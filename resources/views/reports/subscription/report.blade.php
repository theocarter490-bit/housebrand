@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Plan Report'))
@push('styles')
    <style>
        /* Target your date input field */
        .primary-input.form-control[type="date"]::before {
            /* 1. Inject the text from the HTML placeholder attribute */
            content: attr(placeholder);
            color: #a0a0a0;
            pointer-events: none; /* Allows the user to click the input underneath */
            display: block;
        }

        /* 2. Hide the injected text when the field has a value */
        .primary-input.form-control[type="date"]:not([value=""])::before {
            content: none;
        }    </style>
@endpush

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Plan Report'), [
            '#' => _trans('keyword.Reports'),
            'Plan Report' => _trans('keyword.User Report'),
        ]) !!}

        <div class="app-ecommerce-category">
            <div class="col-lg-12 col-md-12">
                <!-- CARD ICON-->
                <div class="row">
                    <div class="col-xl-2 col-md-4 col-sm-6  mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-primary mb-2 rounded"><i
                                        class="ti ti-users-group ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Active Plan</h5>
                                <p class="mb-2 mt-1">{{ $totalActivePlan }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6  mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-success mb-2 rounded"><i class="ti ti-users ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Used Plan</h5>
                                <p class="mb-2 mt-1">{{ @$totalUsedPlan }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6  mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-danger mb-2 rounded"><i class="ti ti-users ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Unused Plan</h5>
                                <p class="mb-2 mt-1">{{ @$totalNonUsedPlan }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6  mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-primary mb-2 rounded"><i
                                        class="ti ti-moneybag ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Subscription Amount</h5>
                                <p class="mb-2 mt-1">{{ @getPriceFormat($totalSubscriptionAmount / 100) }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-6  mb-4">
                        <div class="card h-100">
                            <div class="card-body  d-flex flex-column align-items-center text-center">
                                <div class="badge p-2 bg-label-primary rounded mb-3">
                                    <i class="ti ti-chart-pie ti-md" style="font-size: 2rem; color: #007bff;"></i>
                                </div>
                                <h5 class="card-title mb-2">Plan Report Overview</h5>
                                <p class="card-text text-muted mb-3">
                                    Our current plan includes a total of <strong>{{ @$totalUsedPlan }}</strong> active
                                    projects, focusing on enhancing efficiency and customer satisfaction.
                                </p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#showDetailModal" aria-controls="leaveTypeList">
                                    View Graph
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class=" col-md-12 ">
                    <form method="GET" class="mb-2" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                        type="search" placeholder="search" name="search" value="{{ @$search }}"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-control">
                                    <select class="w-100 bb  form-control height-50 select2" style="width: 100%;"
                                            aria-label="View By"
                                            data-placeholder="Recurring Type"
                                            name="recuring_type">
                                        <option value="">Recurring Type</option>
                                        <option value="day" {{ @$recuring_type == 'day' ? 'selected' : '' }}>Daily
                                        </option>
                                        <option value="week" {{ @$recuring_type == 'week' ? 'selected' : '' }}>Weekly
                                        </option>
                                        <option value="month" {{ @$recuring_type == 'month' ? 'selected' : '' }}>Monthly
                                        </option>
                                        <option value="year" {{ @$recuring_type == 'year' ? 'selected' : '' }}>Yearly
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}"
                                        type="date" placeholder="Start Date *" name="start_date"
                                        value="{{ @$start_date }}" autocomplete="off">
                                    <span class="focus-border"></span>
                                    @if ($errors->has('start_date'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('start_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('end_date') ? ' is-invalid' : '' }}"
                                        type="date" name="end_date" value="{{ @$end_date }}" placeholder="End Date *"
                                        autocomplete="off">
                                    <span class="focus-border"></span>
                                    @if ($errors->has('end_date'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>

                @if(hasPermission('subscription_plan_report_export'))
                    <div class="mb-2">
                        <a href="{{ url('report/subscription-report-export/pdf') . '?' . http_build_query(request()->all()) }}"
                           target="_blank" class="btn btn-primary">Export PDF</a>
                    </div>
                @endif

            </div>
            <div class="card">

                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                        <tr>
                            <th scope="col">{{ 'SL' }}</th>
                            <th scope="col">{{ 'Name' }}</th>
                            <th scope="col">{{ 'Price' }}</th>
                            <th scope="col">{{ 'Setup Fee' }}</th>
                            <th scope="col">{{ 'Recurring' }}</th>
                            <th scope="col">{{ 'Time Used' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($plans as $key => $plan)
                            <tr>
                                <th>{{ $plans->firstItem() + $key }}</th>
                                <th>{{ $plan->name }}</th>
                                <th>{{ getPriceFormat($plan->price) }}</th>
                                <th>{{ getPriceFormat($plan->setup_fee) }}</th>
                                <th>{{ $plan->plan_type }}</th>
                                <th>{{ $plan->subscription_count }}</th>
                            </tr>
                        @endforeach
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $plans->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="showDetailModal" tabindex="-1" role="dialog" aria-labelledby="leaveTypeList"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header ">
                        <h5 class="modal-title" id="exampleModalLabel">Plan Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class=" col-12">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0 me-2">Plan Count Graph</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="orderPaymentStatusChart" class="pt-md-4"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- <x-top-product-graph /> --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(function () {
            $('.select2').select2({
                allowClear: true,
            });
        })
        $(function () {
            let cardColor, headingColor, legendColor, labelColor, borderColor;
            if (isDarkStyle) {
                cardColor = config.colors_dark.cardColor;
                labelColor = config.colors_dark.textMuted;
                legendColor = config.colors_dark.bodyColor;
                headingColor = config.colors_dark.headingColor;
                borderColor = config.colors_dark.borderColor;
            } else {
                cardColor = config.colors.cardColor;
                labelColor = config.colors.textMuted;
                legendColor = config.colors.bodyColor;
                headingColor = config.colors.headingColor;
                borderColor = config.colors.borderColor;
            }

            // Chart Colors
            const chartColors = {
                donut: {
                    series1: config.colors.success,
                    series2: "#28c76fb3",
                    series3: "#28c76f80",
                    series4: config.colors_label.success,
                },
                line: {
                    series1: config.colors.warning,
                    series2: config.colors.primary,
                    series3: "#7367f029",
                },
            };

            let data = [{
                'name': "Total Plan",
                'total': {{ $totalActivePlan }},

            },
                {
                    'name': "Total Used Plan",
                    'total': {{ $totalUsedPlan }},

                },
                {
                    'name': "Total Unused Plan",
                    'total': {{ $totalNonUsedPlan }},

                },
            ];
            let tagName = [];
            let tagData = [];
            let tagColor = [];

            $(data).each(function (index, value) {

                tagName.push(value.name);
                tagData.push(value.total);
                tagColor.push('#' + Math.floor(Math.random() * 16777215).toString(16)
                    .padStart(6, '0'));

            });

            orderPaymentStatusChart(tagName, tagData, tagColor);


            function orderPaymentStatusChart(tagName, tagData, tagColor) {

                const deliveryExceptionsChartE1 = document.querySelector(
                        "#orderPaymentStatusChart"
                    ),
                    deliveryExceptionsChartConfig = {
                        chart: {
                            height: 420,
                            parentHeightOffset: 0,
                            type: "donut",
                        },
                        labels: tagName,
                        series: tagData,
                        colors: tagColor,
                        stroke: {
                            width: 0,
                        },
                        dataLabels: {
                            enabled: false,
                            formatter: function (val, opt) {
                                return parseInt(val);
                            },
                        },
                        legend: {
                            show: true,
                            position: "bottom",
                            offsetY: 10,
                            markers: {
                                width: 8,
                                height: 8,
                                offsetX: -3,
                            },
                            itemMargin: {
                                horizontal: 15,
                                vertical: 5,
                            },
                            fontSize: "13px",
                            fontFamily: "Public Sans",
                            fontWeight: 400,
                            labels: {
                                colors: headingColor,
                                useSeriesColors: false,
                            },
                        },
                        tooltip: {
                            theme: false,
                        },
                        grid: {
                            padding: {
                                top: 15,
                            },
                        },
                        states: {
                            hover: {
                                filter: {
                                    type: "none",
                                },
                            },
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: "77%",
                                    labels: {
                                        show: true,
                                        value: {
                                            fontSize: "26px",
                                            fontFamily: "Public Sans",
                                            color: headingColor,
                                            fontWeight: 500,
                                            offsetY: -30,
                                            formatter: function (val) {
                                                return parseInt(val);
                                            },
                                        },
                                        name: {
                                            offsetY: 20,
                                            fontFamily: "Public Sans",
                                        },
                                        total: {
                                            show: true,
                                            fontSize: ".75rem",
                                            label: "Total",
                                            color: labelColor,
                                            formatter: function (w) {
                                                let total = 0;
                                                $(w.globals.series).each(function (index, value) {
                                                    total += value;
                                                })
                                                return total;
                                            },
                                        },
                                    },
                                },
                            },
                        },
                        responsive: [{
                            breakpoint: 420,
                            options: {
                                chart: {
                                    height: 360,
                                },
                            },
                        },],
                    };
                if (
                    typeof deliveryExceptionsChartE1 !== undefined &&
                    deliveryExceptionsChartE1 !== null
                ) {
                    const deliveryExceptionsChart = new ApexCharts(
                        deliveryExceptionsChartE1,
                        deliveryExceptionsChartConfig
                    );
                    deliveryExceptionsChart.render();
                }
            }
        });
    </script>
@endpush
