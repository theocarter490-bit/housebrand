@extends('layouts.master')

@section('title', $title ?? _trans('keyword.User Report'))
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
        {!! breadcrumb(_trans('keyword.User Report'), [
            '#' => _trans('keyword.Reports'),
            'User Report' => _trans('keyword.User Report'),
        ]) !!}

        <div class="app-ecommerce-category">
            <div class="col-lg-12 col-md-12">
                <!-- CARD ICON-->
                <div class="row">
                    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-primary mb-2 rounded"><i
                                        class="ti ti-users-group ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Customer</h5>
                                <p class="mb-2 mt-1">{{ $customerCount }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-success mb-2 rounded"><i class="ti ti-users ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Designer</h5>
                                <p class="mb-2 mt-1">{{ @$designerCount }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-warning mb-2 rounded"><i class="ti ti-users ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Manufacturer</h5>
                                <p class="mb-2 mt-1">{{ @$manufacturerCount }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center flex-column justify-content-center">
                                <div class="graph-icon mb-3">
                                    <i class="ti ti-users ti-md" style="font-size: 2rem; color: #007bff;"></i>
                                </div>
                                <h5 class="card-title mb-2">User Overview</h5>
                                <p class="card-text text-muted mb-3 text-center">
                                    There are currently <span class="fw-bold text-primary">{{ $customerCount }}</span>
                                    active users this month.
                                </p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#showDetailModal" aria-controls="leaveTypeList">
                                    View Graph
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
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
                                    <select class="w-100 bb  form-control height-50 select2" style="width: 100%"
                                            aria-label="View By" data-placeholder="Select User Type" name="role_type">
                                        <option value="" selected>Role By</option>
                                        <option value="4" {{ @$role_type == 4 ? 'selected' : '' }}>Customer
                                        </option>
                                        <option value="3" {{ @$role_type == 3 ? 'selected' : '' }}>Designer
                                        </option>
                                        <option value="5" {{ @$role_type == 5 ? 'selected' : '' }}>Manufacturer
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-control">
                                    <select class="w-100 bb  form-control height-50 select2 form-select2"
                                            style="width: 100%"
                                            aria-label="Active Status"
                                            data-placeholder="Select Status"
                                            name="active_status">
                                        <option value="" selected>Active Status</option>
                                        <option value="1" {{ @$active_status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ @$active_status == 0 ? 'selected' : '' }}>Inactive
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

                @if(hasPermission('user_report_export'))
                    <div class="mb-2">
                        <a href="{{ url('report/user-report-export/pdf') . '?' . http_build_query(request()->all()) }}"
                           target="_blank" class="btn btn-primary">Export PDF</a>
                    </div>
                @endif

            </div>
            <div class="card">

                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                        <tr>
                            <th scope="col">{{'SL'}}</th>
                            <th scope="col">{{ 'Info' }}</th>
                            <th scope="col">{{ 'Role' }}</th>
                            <th scope="col">{{ 'Status' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $key => $user)
                            <tr>
                                <th>{{ $users->firstItem() + $key }}</th>
                                <th>Name: {{ $user->name }}<br>Phone: {{ $user->phone }}<br>Email:
                                    {{ $user->email }}</th>
                                <th>
                                    @if ($user->role_id == 3)
                                        Designer
                                    @elseif($user->role_id == 4)
                                        Customer
                                    @elseif($user->role_id == 5)
                                        Manufacturer
                                    @endif


                                </th>
                                <th>{{ $user->active_status == 1 ? 'ACTIVE' : 'INACTIVE' }}</th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
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
                        <h5 class="modal-title" id="exampleModalLabel">User Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row user-report-graph-wrapper">
                            <x-user-count-graph/>
                            {{-- <x-top-product-graph /> --}}
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
