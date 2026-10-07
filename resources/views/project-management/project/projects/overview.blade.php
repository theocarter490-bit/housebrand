@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Project'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Project'), [
            '#' => _trans('keyword.Project') . ' ' . _trans('keyword.Management'),
            'project' => _trans('keyword.Project') . ' ' . _trans('keyword.Overview'),
        ]) !!}
        <div class="row">

            <!-- Customer Content -->
            <div class="col-12 order-0 order-md-1">
                <!-- Customer Pills -->
                {!! projectTabMenu($project, 'project', $project->id) !!}
                <div class="row">

                    <div class="col-xl-4 col-lg-4 col-md-4 order-1 order-md-0">
                        <!--/ Customer Pills -->
                        <div class="order-1 order-md-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <div class="customer-avatar-section">
                                                <div class="d-flex align-items-center flex-column">
                                                    <img class="rounded my-3" src="{{ getFilePath($project->banner) }}"
                                                         height="auto" width="100%" alt="Project banner"/>
                                                    <div class="customer-info text-center">
                                                        <h4 class="mb-1">{{ $project->title }}</h4>
                                                        <small>Project Code: #{{ $project->project_code }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-around flex-wrap my-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar">
                                                        <div class="avatar-initial rounded bg-label-primary">
                                                            <i class="ti ti-calendar ti-md"></i>
                                                        </div>
                                                    </div>
                                                    <div class="gap-0 d-flex flex-column">
                                                        <p class="mb-0 fw-medium">
                                                            Start: {{isSet($project->start_date)?dateFormat($project->start_date):'N/A'}}</p>
                                                        <small>End: {{isSet($project->end_date)?dateFormat($project->end_date):'N/A'}}</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar">
                                                        <div class="avatar-initial rounded bg-label-primary">
                                                            <i class='ti ti-currency-dollar ti-md'></i>
                                                        </div>
                                                    </div>
                                                    <div class="gap-0 d-flex flex-column">
                                                        <p class="mb-0 fw-medium">{{ getPriceFormat(0) }}
                                                            /{{ getPriceFormat($project->total_cost) }}</p>
                                                        <small>Budget</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress mb-4">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{$totalProgress}}%;"
                                                     aria-valuenow="{{$totalProgress}}" aria-valuemin="0"
                                                     aria-valuemax="100">{{$totalProgress}}%
                                                </div>
                                            </div>

                                            <div class="info-container">
                                                <h6 class="d-block pt-4 border-top my-3">DETAILS</h6>

                                                <div class="row">
                                                    <!-- Left Column -->
                                                    <div class="col-md-6">
                                                        <ul class="list-unstyled">
                                                            <li class="mb-3">
                                                                <span class="fw-medium me-2">Category:</span>
                                                                <span
                                                                    class="badge bg-label-info">{{ $project->category->name }}</span>
                                                            </li>
                                                            <li class="d-flex align-items-center">
                                                                <span class="fw-medium me-2">Customer:</span>
                                                                <a target="_blank"
                                                                   href="{{isset($project->client->id)? route('user.profile',$project->client->id):'#' }}">
                                                                    <img
                                                                        src="{{ getFilePath(@$project->client->avatar) }}"
                                                                        alt="Avatar"
                                                                        class="rounded-circle m-2"
                                                                        width="40"
                                                                        height="40"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        title="{{ @$project->client->name }}">
                                                                </a>
                                                            </li>
                                                            <li class="d-flex align-items-center">
                                                                <span class="fw-medium me-2">Manager:</span>
                                                                @if($project->manager)
                                                                    <a target="_blank"
                                                                       href="{{route("user.profile", $project->manager->id)}}">
                                                                        <img
                                                                            src="{{ getFilePath($project->manager->avatar) }}"
                                                                            alt="Avatar" class="rounded-circle m-2"
                                                                            width="40" height="40">
                                                                    </a>
                                                                @endif
                                                            </li>

                                                        </ul>
                                                    </div>

                                                    <!-- Right Column -->
                                                    <div class="col-md-6">
                                                        <ul class="list-unstyled">
                                                            <li class="mb-3 d-flex align-items-center">
                                                                <span class="fw-medium me-2">Active Status:</span>
                                                                <span
                                                                    class="badge {{ $project->active_status == 1 ? 'bg-label-success' : 'bg-label-danger' }}">
                                                                    {{ $project->active_status == 1 ? 'Active' : 'Inactive' }}
                                                                </span>
                                                            </li>
                                                            <li class="mb-3 d-flex align-items-center">
                                                                <span class="fw-medium me-2">Progress Status:</span>
                                                                <span class="badge"
                                                                      style="background-color: {{@$project->status->color}}">{{@$project->status->name}}</span>
                                                            </li>

                                                            <li class="mb-3 d-flex align-items-center">
                                                                <span class="fw-medium me-2">Priority:</span>
                                                                <span
                                                                    class="badge bg-label-warning">{{ strtoupper($project->priority) }}</span>
                                                            </li>
                                                            <li class="mb-3">
                                                                <span class="fw-medium me-2">Budget:</span>
                                                                <span
                                                                    class="fw-bold">{{getPriceFormat($project->budget)}}</span>
                                                            </li>
                                                            <li class="mb-3">
                                                                <span class="fw-medium me-2">Tax:</span>
                                                                <span
                                                                    class="fw-bold">{{$project->tax_type == 1 ? 'Percentage' : 'Fixed'}} {{ $project->tax?"($project->tax)":'' }}</span>
                                                            </li>
                                                            <li class="mb-3">
                                                                <span class="fw-medium me-2">Total Cost:</span>
                                                                <span
                                                                    class="fw-bold">{{ getPriceFormat($project->total_cost) }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <span class="fw-medium me-2">Address:</span>
                                                    <span>{{ $project->address }}</span>
                                                </div>

                                                <div class="mb-3">
                                                    <span class="fw-medium me-2">Description:</span>
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top">
                                                        {!! $project->description !!}
                                                    </span>
                                                </div>

                                                @if($project->map_location)
                                                    <div class="mb-3">
                                                        <span class="fw-medium me-2">Map:</span>
                                                        <div
                                                            style="border-radius: 8px; overflow: hidden; height: 250px; margin-top: 10px">
                                                            {!! $project->map_location !!}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(hasPermission('update_projects'))
                                                    <!-- Edit Button -->
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{route('project-management.project.edit',$project->id)}}"
                                                           class="btn btn-primary me-3">Edit Details</a>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8 order-1 order-md-0">
                        <div class="row">

                            <!-- Donut Chart -->
                            <div class="col-md-5  mb-5">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-0">Task Report</h5>
                                            <small class="text-muted">Spending on various categories</small>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center">
                                        <div id="orderPaymentStatusChart" class="pt-md-4"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Donut Chart -->

                            <div class="col-xl-7 col-lg-7 col-md-7 order-1 order-md-0">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card h-100">
                                            <div class="card-header d-flex justify-content-between pb-2 mb-1">
                                                <div class="card-title mb-1">
                                                    <h5 class="m-0 me-2">Task List</h5>
                                                    <small class="text-muted">{{$project->tasks->count()}} total
                                                        Task</small>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="nav-align-top">
                                                    <ul class="nav nav-tabs nav-fill" role="tablist">
                                                        @foreach($taskStatus as $index => $status)
                                                            <li class="nav-item">
                                                                <button type="button"
                                                                        class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                                        role="tab"
                                                                        data-bs-toggle="tab"
                                                                        data-bs-target="#tab-{{ $status->id }}"
                                                                        aria-controls="tab-{{ $status->id }}"
                                                                        aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                                                    {{ $status->name }}
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    <div class="tab-content pb-0">
                                                        @foreach($taskStatus as $index => $status)
                                                            <div
                                                                class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                                                id="tab-{{ $status->id }}" role="tabpanel">
                                                                <ul class="timeline timeline-advance mb-2 pb-1">
                                                                    @foreach($status->tasks as $task)
                                                                        <li class="timeline-item ps-4 border-left-dashed">
                                                                            <span class="timeline-indicator">
                                                                                <i class="ti ti-circle-check"
                                                                                   style="color: {{$status->color}}"></i>
                                                                            </span>
                                                                            <div class="timeline-event ps-0 pb-0">
                                                                                <div class="timeline-header">
                                                                                    <a href="{{route('project-management.project.task.details',$task->id)}}"
                                                                                       style="color: {{$status->color}}"
                                                                                       class="fw-medium">{{ $task->title }}</a>
                                                                                    <small
                                                                                        class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                                                                                </div>
                                                                                <div
                                                                                    class="d-flex align-items-center gap-2">
                                                                                    <i class="ti ti-calendar ti-sm"></i>
                                                                                    <span>Due: {{ dateFormatwithTime($task->due_date) }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-12 mb-5 order-5 order-xxl-0">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0">Recent Time Billing</h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">

                                            <table class="datatables-basic hrm_datatable selectable table">
                                                <thead>
                                                <tr>
                                                    <th>{{_trans('keyword.SL')}}</th>
                                                    <th>{{_trans('keyword.Client')}}</th>
                                                    <th>{{_trans('keyword.Service Type')}}</th>
                                                    <th>{{_trans('keyword.Rate(Hourly)')}}</th>
                                                    <th>{{_trans('keyword.Time')}}</th>
                                                    <th>{{_trans('keyword.Billed')}}</th>
                                                    <th>{{_trans('keyword.Payment Status')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($project->timeBillings as $billing)
                                                    <tr>
                                                        <td> {{ $loop->iteration }} </td>
                                                        <td>
                                                            <a href="{{ route('user.profile', $billing->client->id) }}">
                                                                <img src="{{ getFilePath($billing->client->avatar) }}"
                                                                     alt="Avatar" class="rounded-circle m-2" width="40"
                                                                     height="40">
                                                            </a>
                                                            <a href="{{ route('user.profile', $billing->client->id) }}">
                                                                {{ $billing->client->name }}
                                                            </a>
                                                        </td>
                                                        <td>{{ @$billing->serviceType->name }}</td>
                                                        <td>{{ getPriceFormat($billing->rate) }}</td>
                                                        <td>{{ secondsToHMS($billing->duration) }} H</td>
                                                        <td>{{ getPriceFormat($billing->total_amount) }}</td>
                                                        <td>
                                                            @if ($billing->payment_status == 1)
                                                                <span class="badge custom-bg-success">Paid</span>
                                                            @elseif ($billing->payment_status == 0)
                                                                <span class="badge bg-label-warning">Unpaid</span>
                                                            @elseif ($billing->payment_status == 2)
                                                                <span class="badge custom-bg-danger">Partial Paid</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-12 mb-7 order-7 order-xxl-0">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title mb-0">
                                            <h5 class="m-0">Recent Invoice</h5>
                                        </div>
                                    </div>
                                    <div class="card-datatable table-responsive pt-0">
                                        <table class="datatables-basic hrm_datatable selectable table">
                                            <thead>
                                            <tr>
                                                <th>{{_trans('keyword.SL')}}</th>
                                                <th>{{_trans('keyword.Code')}}</th>
                                                <th>{{_trans('keyword.Client')}}</th>
                                                <th>{{_trans('keyword.Issue Date')}}</th>
                                                <th>{{_trans('keyword.Amount')}}</th>
                                                <th>{{_trans('keyword.Status')}}</th>
                                                <th>{{_trans('keyword.Action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($project->invoices as $key => $invoice)
                                                <tr>
                                                    <th>{{ $loop->iteration }}</th>
                                                    <td>{{ $invoice->code }}</td>
                                                    <td>{{ $invoice->client->name }}</td>
                                                    <td class="text-center">{{ dateFormat($invoice->invoice_date) }}</td>
                                                    <td class="text-center">{{ getPriceFormat($invoice->total_amount); }}</td>
                                                    <td>
                                                        @if ($invoice->status == 0)
                                                            <span class="badge custom-bg-success">Active</span>
                                                        @elseif ($invoice->status == 1)
                                                            <span class="badge bg-label-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <a href="{{route('project-management.project.invoice.preview', [$invoice->project_id, $invoice->id])}}"
                                                               class="text-body" data-bs-toggle="tooltip"
                                                               aria-label="Preview" data-bs-original-title="Preview"><i
                                                                    class="ti ti-eye mx-2 ti-sm"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
            @endsection

            @push('scripts')
                <script>
                    (function () {
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

                        $.ajax({
                            url: "{{ route('project-management.essentials.task.status.count', $project->id) }}",
                            method: "get",
                            data: {
                                _token: "{{ csrf_token() }}",
                            },
                            success: function (response) {
                                console.log(response)
                                let tagName = [];
                                let tagData = [];
                                let tagColor = [];

                                $(response.data).each(function (index, value) {
                                    tagName.push(value.name);
                                    tagData.push(value.tasks_count);
                                    tagColor.push(value.color);
                                });

                                orderPaymentStatusChart(tagName, tagData, tagColor);
                            },
                            error: function (error) {
                                console.log(error.responseJSON.message);
                                // handle the error case
                            },
                        });

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
                    })();
                </script>
    @endpush
