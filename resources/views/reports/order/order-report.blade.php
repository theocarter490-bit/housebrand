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
        {!! breadcrumb(_trans('keyword.Order Report'), [
            '#' => _trans('keyword.Reports'),
            'Order Report' => _trans('keyword.Order Report'),
        ]) !!}

        <div class="col-md-12">
            <div class="app-ecommerce-category">
                <!-- CARD ICON-->
                <div class="row">
                    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-primary mb-2 rounded"><i
                                        class="ti ti-currency-dollar ti-md"></i></div>
                                <h5 class="card-title mb-1 pt-2">Total Amount</h5>
                                <p class="mb-2 mt-1">{{ getPriceFormat(@$totalOrderAmount) }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-success mb-2 rounded"><i
                                        class="ti ti-currency-dollar ti-md"></i></div>
                                <h5 class="card-title mb-1 pt-2">Paid Amount</h5>
                                <p class="mb-2 mt-1">{{ getPriceFormat(@$paidAmount) }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-danger mb-2 rounded"><i
                                        class="ti ti-currency-dollar ti-md"></i></div>
                                <h5 class="card-title mb-1 pt-2">Due Amount</h5>
                                <p class="mb-2 mt-1">{{ getPriceFormat(@$dueAmount) }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column align-items-center text-center">
                                <div class="badge p-2 bg-label-primary  rounded mb-3">
                                    <i class="ti ti-currency-dollar ti-md" style="font-size: 2rem; color: #007bff;"></i>
                                </div>
                                <h5 class="card-title mb-2">Sales Overview</h5>
                                <p class="card-text text-muted mb-3">
                                    Total sales for this quarter increased by 15% compared to last quarter.
                                </p>
                                <button type="button" class="btn btn-primary  mt-auto" data-bs-toggle="modal"
                                        data-bs-target="#showDetailModal" aria-controls="leaveTypeList">
                                    <i class="bi bi-bar-chart-fill me-2"></i> View Graph
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 mb-4">
                    <form method="GET" class="mb-2" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                        type="search" placeholder="Search" name="search" value="{{ @$search }}"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-control">
                                    <select class="w-100 bb  form-control height-50 select2" style="width: 100%"
                                            data-placeholder="Select Type"
                                            name="status_type">
                                        <option value="" selected>Order Status</option>
                                        @foreach ($orderStatues as $status)
                                            <option value="{{ $status->id }}"
                                                {{ @$status_type == $status->id ? 'selected' : '' }}>{{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-control">
                                    <select class="w-100 bb  form-control height-50 select2" style="width: 100%"
                                            data-placeholder="Payment Status"
                                            name="payment_status">
                                        <option value="" selected>Payment Status</option>
                                        <option value="paid" {{ @$payment_status == 'paid' ? 'selected' : '' }}>Paid
                                        </option>
                                        <option value="unpaid" {{ @$payment_status == 'unpaid' ? 'selected' : '' }}>
                                            Unpaid
                                        </option>
                                        <option value="partial" {{ @$payment_status == 'partial' ? 'selected' : '' }}>
                                            Partial
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

                @if(hasPermission('order_report_export'))
                    <div class="mb-2">
                        <a href="{{ url('report/order-report-export/pdf') . '?' . http_build_query(request()->all()) }}"
                           target="_blank" class="btn btn-primary">Export PDF</a>
                    </div>
                @endif

            </div>
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                        <tr>
                            <th class="text-nowrap" scope="col">{{ 'SL' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Order Code' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Seller' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Customer' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Order Status' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Payment Status' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Amount' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($orders as $key => $order)
                            <tr>
                                <th>{{ $orders->firstItem() + $key }}</th>
                                <th>{{ $order->code }}</th>
                                <td>{{ $order->designer->name }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td><span class="badge text-dark"
                                          style="background:rgb(from {{ $order->orderStatus->color }} r g b / 50%) ">{{ $order->orderStatus->name }}</span>
                                </td>
                                <td class="{{ $order->payment_status == 'unpaid' ? 'text-danger' : 'text-success' }}">
                                    {{ ucfirst($order->payment_status) }}</td>
                                <td>{{ getPriceFormat($order->grand_total_amount)  }}</td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $orders->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="showDetailModal" tabindex="-1" role="dialog" aria-labelledby="leaveTypeList"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header ">
                        <h5 class="modal-title" id="exampleModalLabel">Sales Report Graph</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <x-oder-status-graph/>
                            <x-oder-payment-graph/>
                            <x-order-claim-graph/>

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

            </script>
    @endpush
