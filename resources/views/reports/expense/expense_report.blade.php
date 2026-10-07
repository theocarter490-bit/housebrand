@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Expense Report'))

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
        {!! breadcrumb(_trans('keyword.Expense Report'), [
            '#' => _trans('keyword.Reports'),
            'Expense Report' => _trans('keyword.Expense Report'),
        ]) !!}

        <div class="app-ecommerce-category">
            <div class="col-lg-12 col-md-12">
                <!-- CARD ICON-->
                <div class="row">
                    <div class="col-xl-4 col-md-4  mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-danger mb-2 rounded"><i
                                        class="ti ti-currency-dollar ti-md"></i></div>
                                <h5 class="card-title mb-1 pt-2">Total Expense</h5>
                                <p class="mb-2 mt-1">{{ getPriceFormat(@$allExpenses->sum('amount')) }}</p>
                                <div class="pt-1">
                                    <span
                                        class="badge bg-label-secondary">{{ getPriceFormat(@$allExpenses->sum('amount')) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-success mb-2 rounded">
                                    <i class="ti ti-trending-up ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Current Month Expense</h5>
                                <p class="mb-2 mt-1 text-success font-weight-bold">{{ getPriceFormat(@$currentMonthExpense) }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary">{{ getPriceFormat(@$currentMonthExpense) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12 mb-4">
                        <div class="card h-100">
                            <div class="card-body  d-flex flex-column align-items-center text-center">
                                <div class="badge p-2 bg-label-primary rounded mb-3">
                                    <i class="ti ti-wallet ti-md" style="font-size: 2rem; color: #007bff;"></i>
                                </div>
                                <h5 class="card-title mb-2">Expense Report Overview</h5>
                                <p class="card-text text-muted mb-3">
                                    Total expenses for this quarter amount to <strong>{{ getPriceFormat(@$allExpenses->sum('amount')) }}</strong>, with a focus on optimizing spending across all departments.
                                </p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#showExpenseGraph" aria-controls="showExpenseGraph">
                                    View Graph
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 mb-4">
                    <form method="GET" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-lg-0 mb-4">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                        type="search" placeholder="Search" name="search"
                                        value="{{ @$search }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-lg-0 mb-4">
                                <div class="input-control">
                                    <select class="w-100 bb  form-control height-50 select2"  style="width: 100%" aria-label="Select Type"
                                            data-placeholder="Select Type"
                                        name="expense_type">
                                        <option value="" selected>Select Type</option>
                                        @foreach ($expTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ @$expense_type == $type->id ? 'selected' : '' }}>{{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-lg-0 mb-4">
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
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-lg-0 mb-4">
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
                            <div class="col-lg-2 col-md-4 col-sm-6 ">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>

                @if(hasPermission('expense_report_export'))
                    <div class="mb-3">
                        <a href="{{ url('report/expense-report-export/pdf').'?'.http_build_query(request()->all()) }}" target="_blank"
                           class="btn btn-primary">Export PDF</a>
                    </div>
                @endif

            </div>
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                            <tr>
                                <th class="text-nowrap" scope="col">{{ __('SL') }}</th>
                                <th class="text-nowrap" scope="col">{{ __('Expense Title') }}</th>
                                <th class="text-nowrap" scope="col">{{ __('Type') }}</th>
                                <th class="text-nowrap" scope="col">{{ __('Expense Date') }}</th>
                                <th class="text-nowrap" scope="col">{{ __('Amount') }}({{ generalSetting()->currency->symbol }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenses as $key => $expense)
                                <tr>
                                    <th>{{ $expenses->firstItem() + $key }}</th>
                                    <td style="width: 40%"><a href="javascript:void(0)" title="View Details" data-id="{{ $expense->id }}"
                                            onclick="viewDetails(event.target)">{{ $expense->title }}</a></td>
                                    <td>{{ $expense->expenseType->name }}</td>
                                    <td>{{ $expense->expense_date }}</td>
                                    <td>{{ getPriceFormat($expense->amount) }}</td>
                                </tr>
                            @endforeach
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $expenses->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="showExpenseGraph" tabindex="-1" role="dialog" aria-labelledby="verifyModalContent"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Expense Graph</h5>
                        </div>
                        <div class="card-body">
                            <div id="radialBarChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="showDetailModal" tabindex="-1" role="dialog" aria-labelledby="verifyModalContent"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header ">
                        <h5 class="modal-title" id="exampleModalLabel">Expense Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="template-demo">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row border-bottom-gray-200">
                                        <div class="col-3 col-lg-3">
                                            <label class="col-form-label">Title</label>
                                        </div>
                                        <div class="col-9 col-lg-9">
                                            <label class="col-form-label" id="title"></label>
                                        </div>
                                    </div>

                                    <div class="form-group row border-bottom-gray-200">
                                        <div class="col-3 col-lg-3">
                                            <label class="col-form-label">Type</label>
                                        </div>
                                        <div class="col-9 col-lg-9">
                                            <label class="col-form-label" id="type"></label>
                                        </div>
                                    </div>
                                    <div class="form-group row border-bottom-gray-200">
                                        <div class="col-3 col-lg-3">
                                            <label class="col-form-label">Expense Date</label>
                                        </div>
                                        <div class="col-9 col-lg-9">
                                            <label class="col-form-label" id="expense_date"></label>
                                        </div>
                                    </div>
                                    <div class="form-group row border-bottom-gray-200">
                                        <div class="col-3 col-lg-3">
                                            <label class="col-form-label">Amount</label>
                                        </div>
                                        <div class="col-9 col-lg-9">
                                            <label class="col-form-label" id="amount"></label>
                                        </div>
                                    </div>
                                    <div class="form-group row border-bottom-gray-200">
                                        <div class="col-3 col-lg-3">
                                            <label class="col-form-label">Details</label>
                                        </div>
                                        <div class="col-9 col-lg-9">
                                            <label class="col-form-label" id="details"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
            <script>

                (function() {
                        let headingColor, borderColor, legendColor;
                        legendColor = config.colors_dark.bodyColor;
                        borderColor = config.colors_dark.borderColor;
                        headingColor = config.colors_dark.headingColor;

                        $.ajax({
                            url: "/dashboard/expense-graph",
                            method: "get",
                            data: {
                                _token: "{{ csrf_token() }}",
                            },
                            success: function(response) {
                                console.log(response)
                                let tagName = [];
                                let tagData = [];
                                let tagColor = [];

                                $(response.expenseGraph).each(function(index, value) {
                                    tagName.push(value.name);
                                    tagData.push(value.expenses_sum_amount);
                                    tagColor.push('#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'));
                                });

                                expenseGraph(tagName, tagData,tagColor);
                            },
                            error: function(error) {
                                console.log(error.responseJSON.message);
                            },
                        });

                        function expenseGraph(tagName, tagData,tagColor) {
                            const radialBarChartEl = document.querySelector('#radialBarChart'),
                                radialBarChartConfig = {
                                    chart: {
                                        height: 400,
                                        type: 'radialBar'
                                    },
                                    colors: tagColor,
                                    plotOptions: {
                                        radialBar: {
                                            size: 185,
                                            hollow: {
                                                size: '40%'
                                            },
                                            track: {
                                                margin: 10,
                                                background: config.colors_label.secondary
                                            },
                                            dataLabels: {
                                                name: {
                                                    fontSize: '2rem',
                                                    fontFamily: 'Public Sans'
                                                },
                                                value: {
                                                    fontSize: '1.2rem',
                                                    color: legendColor,
                                                    fontFamily: 'Public Sans',
                                                    formatter: function (val) {
                                                        return "$" + parseInt(val).toFixed(2);
                                                    },
                                                },
                                                total: {
                                                    show: true,
                                                    fontWeight: 400,
                                                    fontSize: '1.3rem',
                                                    color: headingColor,
                                                    label: tagName[0],
                                                    formatter: function (w) {
                                                        return "$" + parseInt(w.globals.series).toFixed(2);
                                                    },
                                                }
                                            }
                                        }
                                    },
                                    grid: {
                                        borderColor: borderColor,
                                        padding: {
                                            top: -25,
                                            bottom: -20
                                        }
                                    },
                                    legend: {
                                        show: true,
                                        position: 'bottom',
                                        labels: {
                                            colors: legendColor,
                                            useSeriesColors: false
                                        }
                                    },
                                    stroke: {
                                        lineCap: 'round'
                                    },
                                    series: tagData,
                                    labels: tagName,
                                };
                            if (typeof radialBarChartEl !== undefined && radialBarChartEl !== null) {
                                const radialChart = new ApexCharts(radialBarChartEl, radialBarChartConfig);
                                radialChart.render();
                            }
                        }
                    }
                )();

                function viewDetails(event) {
                    var id = $(event).data("id");
                    let _url = `expense-details/${id}`;
                    $.ajax({
                        url: _url,
                        type: "GET",
                        success: function(response) {
                            if (response) {
                                $("#title").html(response.title);
                                $("#type").html(response.type);
                                $("#expense_date").html(response.expense_date);
                                $("#amount").html(response.amount);
                                $("#details").html(response.details);
                                $('#showDetailModal').modal('show');
                                $('#showDetailModal .close-btn').click(function() {
                                    $('#showDetailModal').modal('hide');
                                });
                            }
                        }
                    });
                }
                $(function () {
                    $('.select2').select2({
                        allowClear: true,
                    });
                })

                function initializePieChart() {
                    let echartElem5 = document.getElementById('pieChart');
                    if (echartElem5) {
                        var pieChart = echarts.init(echartElem5);
                        var expenses = @json($allExpenses); // Pass the expense types from the controller

                        // Ensure expenses is an array or set it to an empty array if undefined
                        if (!Array.isArray(expenses)) {
                            console.error('Expenses data is not an array:', expenses);
                            expenses = [];
                        }

                        var expenseData = {}; // Use an object to accumulate amounts per type

                        // Aggregate expenses by type
                        expenses.forEach(function(expense) {
                            if (expense.expense_type && expense.expense_type.name) {
                                if (!expenseData[expense.expense_type.name]) {
                                    expenseData[expense.expense_type.name] = 0;
                                }
                                expenseData[expense.expense_type.name] += parseFloat(expense.amount) || 0;
                            }
                        });

                        // Convert expenseData object to arrays for pie chart data
                        var pieData = Object.keys(expenseData).map(function(type) {
                            return {
                                name: type,
                                value: expenseData[type],
                                itemStyle: {
                                    color: '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'),
                                }
                            };
                        });

                        // Chart options
                        var option = {
                            grid: {
                                top: '8px',
                                right: '8px',
                                bottom: '8px',
                                left: '8px'
                            },
                            tooltip: {
                                show: true,
                                backgroundColor: 'rgba(0, 0, 0, .8)',
                                formatter: function(params) {
                                    return `${params.name}: $${params.value.toFixed(2)}`;
                                }
                            },
                            series: [{
                                type: 'pie',
                                radius: '75%',
                                center: ['60%', '50%'],
                                data: pieData,
                                label: {
                                    normal: {
                                        formatter: '{b}: ${c}',
                                        textStyle: {
                                            color: '#333'
                                        }
                                    }
                                },
                                labelLine: {
                                    normal: {
                                        lineStyle: {
                                            color: '#333'
                                        }
                                    }
                                }
                            }]
                        };

                        // Set the pie chart option
                        pieChart.setOption(option);

                        // Resize the chart on window resize
                        $(window).on('resize', function() {
                            setTimeout(function() {
                                pieChart.resize();
                            }, 500);
                        });
                    }
                }

                // Initialize the pie chart when the modal is shown
                $('#showDetailModal').on('shown.bs.modal', function () {
                    initializePieChart();
                });
            </script>

    {{-- <script>
         function viewDetails(event) {
             var id = $(event).data("id");
             let _url = `expense-details/${id}`;
             $.ajax({
                 url: _url,
                 type: "GET",
                 success: function(response) {

                     if (response) {
                         $("#title").html(response.title);
                         $("#type").html(response.type);
                         $("#expense_date").html(response.expense_date);
                         $("#amount").html(response.amount);
                         $("#details").html(response.details);
                         $('#showDetailModal').modal('show');
                         $('#showDetailModal .close-btn').click(function() {
                             $('#showDetailModal').modal('hide');
                         });

                     }
                 }
             });
         }
         function initializePieChart() {
             let echartElem5 = document.getElementById('pieChart');
             if (echartElem5) {
                 var pieChart = echarts.init(echartElem5);
                 var expenses = @json($allExpenses); // Pass the expense types from the controller

             // Ensure expenses is an array or set it to an empty array if undefined
             if (!Array.isArray(expenses)) {
                 console.error('Expenses data is not an array:', expenses);
                 expenses = []; // Fallback to an empty array to prevent errors
             }

             var expenseData = {}; // Use an object to accumulate amounts per type
             var colors = ['#802bff', '#2b87ff', '#24d1f0', '#8a7ac7', '#9b8bd0', '#a69ad6', '#b2a8df', '#c3b9e8', '#d4caf1',
                 '#e5dbfa'
             ];

             // Aggregate expenses by type
             expenses.forEach(function(expense) {
                 if (expense.expense_type && expense.expense_type.name) {
                     if (!expenseData[expense.expense_type.name]) {
                         expenseData[expense.expense_type.name] = 0;
                     }
                     expenseData[expense.expense_type.name] += parseFloat(expense.amount) ||
                     0; // Ensure numeric summation
                 }
             });
             // Convert expenseData object to arrays for pie chart data
             var pieData = Object.keys(expenseData).map(function(type, index) {
                 return {
                     name: type,
                     value: expenseData[type],
                     itemStyle: {
                         // random color
                         color: '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'),
                     }
                 });
                 console.log(expenseData);

                 // Convert expenseData object to arrays for pie chart data
                 var pieData = Object.keys(expenseData).map(function(type, index) {
                     console.log(type);

                     return {
                         name: type,
                         value: expenseData[type],
                         itemStyle: {
                             // random color
                             color: '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'),
                         }
                     };
                 });

                 // Chart options
                 var option = {
                     grid: {
                         top: '8px',
                         right: '8px',
                         bottom: '8px',
                         left: '8px'
                     },
                     tooltip: {
                         show: true,
                         backgroundColor: 'rgba(0, 0, 0, .8)',
                         formatter: function(params) {
                             return `${params.name}: $${params.value.toFixed(2)}`; // Add dollar sign and format to 2 decimal places
                         }
                     },
                     series: [{
                         type: 'pie',
                         radius: '75%',
                         center: ['60%', '50%'],
                         data: pieData, // Use the transformed pieData array
                         label: {
                             normal: {
                                 formatter: '{b}: ${c}', // Display value with dollar sign
                                 textStyle: {
                                     color: '#333'
                                 }
                             }
                         },
                         labelLine: {
                             normal: {
                                 lineStyle: {
                                     color: '#333'
                                 }
                             }
                         }
                     }]
                 };

                 // Set the pie chart option
                 pieChart.setOption(option);

                 // Resize the chart on window resize
                 $(window).on('resize', function() {
                     setTimeout(function() {
                         pieChart.resize();
                     }, 500);
                 });
             }
         }
         $('#showDetailModal').on('shown.bs.modal', function () {
             initializePieChart();
         });

     </script>--}}
    @endpush
