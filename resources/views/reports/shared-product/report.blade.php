@extends('layouts.master')

@section('title', $title ?? _trans('keyword.White Label Report'))
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
        {!! breadcrumb(_trans('keyword.White Label Report'), [
            '#' => _trans('keyword.Reports'),
            'White Label Report' => _trans('keyword.White Label Report'),
        ]) !!}

        <div class="app-ecommerce-category">
            <div class="col-lg-12 col-md-12">
                <!-- CARD ICON-->
                <div class="row">
                    <div class="col-xl-4 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-primary mb-2 rounded"><i
                                        class="ti ti-users-group ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Sharing</h5>
                                <p class="mb-2 mt-1">{{ $totalSharedProduct }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="badge p-2 bg-label-success mb-2 rounded"><i class="ti ti-users ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Distinct Product Sharing</h5>
                                <p class="mb-2 mt-1">{{ @$tatalDistinctProductShare }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column align-items-center text-center">
                                <div class="badge p-2 bg-label-primary rounded mb-3">
                                    <i class="ti ti-chart-bar ti-md" style="font-size: 2rem; color: #007bff;"></i>
                                </div>
                                <h5 class="card-title mb-2">White Label Report</h5>
                                <p class="card-text text-muted mb-2">
                                    Total Products Shared: <strong>{{ $totalSharedProduct }}</strong>
                                </p>
                                <p class="card-text text-muted mt-2">
                                    Explore our highest-rated products across various categories, tailored to meet
                                    customer satisfaction.
                                </p>
                                {{--                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"--}}
                                {{--                                        data-bs-target="#showDetailModal" aria-controls="leaveTypeList">--}}
                                {{--                                    View Graph--}}
                                {{--                                </button>--}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12">
                    <form method="GET" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-3 col-xl-2 col-md-4 col-sm-6  mb-4">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                        type="search" placeholder="Search" name="search" value="{{ @$search }}"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-3 col-xl-2 col-md-4 col-sm-6  mb-4">
                                <div class="input-control">
                                    <select class="w-100 bb  form-control select2 form-select"
                                            data-placeholder="Sort By" style="width: 100%;" aria-label="View By"
                                            name="sort_by">
                                        <option value="" selected>Sort By</option>
                                        <option value="most_shared" {{ @$sort_by == 'most_shared' ? 'selected' : '' }}>
                                            Most
                                            Shared
                                        </option>
                                        <option value="less_shared" {{ @$sort_by == 'less_shared' ? 'selected' : '' }}>
                                            Less
                                            Shared
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xl-2 col-md-4 col-sm-6  mb-4">
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
                            <div class="col-lg-3 col-xl-2 col-md-4 col-sm-6  mb-4">
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
                            <div class="col-lg-3 col-xl-2 col-md-4 col-sm-6  mb-4">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>

                @if(hasPermission('shared_product_report_export'))
                    <div class="mb-2">
                        <a href="{{ url('report/shared-product-report-export/pdf') . '?' . http_build_query(request()->all()) }}"
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
                            <th class="text-nowrap" scope="col">{{ 'Name' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Shop' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Category' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Shared With' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Show' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $key => $product)
                            <tr>
                                <th>{{ $products->firstItem() + $key }}</th>
                                <th>{{ $product->name }}</th>
                                <th>{{ @$product->user->shop->shop_name }}</br>{{ @$product->user->shop->email }}</th>
                                <th>{{ @$product->category->name }}</th>
                                <th>{{ $product->shared_product_count }} Shops</th>
                                <th>
                                    <button class="btn text-primary designer_view_button" data-bs-toggle="modal"
                                            data-bs-target="#modalCenter" data-id="{{ $product->id }}"><span
                                            class="ti ti-eye ti-md"></span></button>
                                </th>
                            </tr>
                        @endforeach
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--        <div class="modal fade" id="showDetailModal" tabindex="-1" role="dialog" aria-labelledby="leaveTypeList"--}}
        {{--            aria-hidden="true">--}}
        {{--            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">--}}
        {{--                <div class="modal-content">--}}
        {{--                    <div class="modal-header ">--}}
        {{--                        <h5 class="modal-title" id="exampleModalLabel">White Label Report</h5>--}}
        {{--                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
        {{--                    </div>--}}
        {{--                    <div class="modal-body">--}}
        {{--                        <div class="row top-product-graph-wrapper">--}}
        {{--                            <x-user-count-graph />--}}
        {{--                            <x-top-product-graph />--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}

        <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">
                            {{ _trans('keyword.Designer') . ' ' . _trans('keyword.list') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <table class="datatables-basic hrm_datatable selectable table mb-2">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ 'Logo' }}</th>
                                <th scope="col">{{ 'Designer Shop Name' }}</th>
                                <th scope="col">{{ 'Email' }}</th>
                                <th scope="col">{{ 'Phone' }}</th>
                            </tr>
                            </thead>
                            <tbody id="designer_list">
                            {{-- @foreach ($products as $key => $product)
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <th>{{ $product->name }}</th>
                                    <th>{{ @$product->user->shop->shop_name }}</br>{{ @$product->user->shop->email }}
                                    </th>
                                    <th>{{ @$product->category->name }}</th>
                                    <th>{{ $product->shared_product_count }} Shops</th>
                                    <th><button class="btn text-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalCenter" data-id="{{ $product->id }}"><span
                                                class="ti ti-eye ti-md"></span></button></th>
                                </tr>
                            @endforeach --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).on("click", ".designer_view_button", function () {
            let id = $(this).attr("data-id");
            $('#designer_list').empty();
            $.ajax({
                url: '/report/shared-product-designer-list/' + id,
                type: 'GET',
                success: function (response) {
                    $(response.designers).each(function (index, value) {

                        let s = `<tr>`;
                        s += `<th>${index + 1}</th>`;
                        s +=
                            `<th><img width="70px" height="50px" src="${value.logo}" alt=""></th>`;
                        s += `<th>${value.shop_name}</th>`;
                        s += `<th>${value.email}</th>`;
                        s += `<th>${value.phone}</th>`;
                        s += `</tr>`;
                        $('#designer_list').append(s);
                    });
                },
                error: function (error) {
                    toastr.error(error.responseJSON.message);
                }
            });


        });
        $(function () {
            $('.select2').select2({
                allowClear: true,
            });
        });

    </script>
@endpush
