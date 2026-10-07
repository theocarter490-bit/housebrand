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
        {!! breadcrumb(_trans('keyword.Product Report'), [
            '#' => _trans('keyword.Reports'),
            'Product Report' => _trans('keyword.Product Report'),
        ]) !!}

        <div class="app-ecommerce-category">
            <div class="col-lg-12 col-md-12">
                <!-- CARD ICON-->
                <div class="row">
                    <div class="col-xl-3 col-md-4 col-sm-6  mb-4">
                        <div class="card h-100">
                            <div class="card-body ">
                                <div class="badge p-2 bg-label-primary mb-2 rounded"><i class="ti ti-home ti-md"></i>
                                </div>
                                <h5 class="card-title mb-1 pt-2">Total Product</h5>
                                <p class="mb-2 mt-1">{{ $productCount }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6  mb-4">
                        <div class="card h-100">
                            <div class="card-body ">
                                <div class="badge p-2 bg-label-success mb-2 rounded"><i
                                        class="ti ti-building-factory-2 ti-md"></i></div>
                                <h5 class="card-title mb-1 pt-2">Published Product</h5>
                                <p class="mb-2 mt-1">{{ @$publishedProductCount }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6  mb-4">
                        <div class="card h-100">
                            <div class="card-body ">
                                <div class="badge p-2 bg-label-danger mb-2 rounded"><i
                                        class="ti ti-building-factory-2 ti-md"></i></div>
                                <h5 class="card-title mb-1 pt-2">Unpublished Product</h5>
                                <p class="mb-2 mt-1">{{ @$unPublishedProductCount }}</p>
                                <div class="pt-1">
                                    <span class="badge bg-label-secondary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6  mb-4">
                        <div class="card h-100">
                            <div class="card-body  d-flex flex-column align-items-center text-center">
                                <div class="badge p-2 bg-label-primary rounded mb-3">
                                    <i class="ti ti-package ti-md" style="font-size: 2rem; color: #007bff;"></i>
                                </div>
                                <h5 class="card-title mb-2">Product Overview</h5>
                                <p class="card-text text-muted mb-3">
                                    We currently have a total of <strong>{{ $productCount }}</strong> products
                                    available, with top ratings for quality and design.
                                </p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#showDetailModal" aria-controls="leaveTypeList">
                                    View Graph
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
                                        type="search" placeholder="Title" name="search" value="{{ @$search }}"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-control">
                                    <select class="w-100 bb  form-control height-50 select2" aria-label="View By"
                                            data-placeholder="View By"
                                            name="view_by" style="width: 100%">
                                        <option value="">View By</option>
                                        <option value="1" {{ @$view_by == 1 ? 'selected' : '' }}>Most Sold Product
                                        </option>
                                        <option value="2" {{ @$view_by == 2 ? 'selected' : '' }}>Most Viewed Product
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-control">
                                    <select class="w-100   form-control height-50 select2" style="width: 100%"
                                            aria-label="Publish Status"
                                            data-placeholder="Publish Status"
                                            name="publish_status">
                                        <option value="">Publish Status</option>
                                        <option value="1" {{ @$publish_status == 1 ? 'selected' : '' }}>Published
                                        </option>
                                        <option value="2" {{ @$publish_status == 2 ? 'selected' : '' }}>Unpublished
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

                @if(hasPermission('product_report_export'))
                    <div class="mb-2">
                        <a href="{{ url('report/product-report-export/pdf') . '?' . http_build_query(request()->all()) }}"
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
                            <th class="text-nowrap" scope="col">{{ 'Seller' }}</th>
                            <th class="text-nowrap text-center" scope="col">{{ 'No of Sale' }}</th>
                            <th class="text-nowrap text-center" scope="col">{{ 'No of View' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Price' }}</th>
                            <th class="text-nowrap" scope="col">{{ 'Status' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($products as $key => $product)
                            <tr>
                                <th>{{ $products->firstItem() + $key }}</th>
                                <th>
                                    @if($product->relationLoaded('shop'))
                                        <a href="{{env('APP_FRONTEND_URL').'/designer/'.@$product->shop->slug.'/product/'.@$product->id.'-' .@$product->slug }}"
                                           target="_blank">{{ optional($product)->name }}</a>
                                    @else
                                        <a href="{{env('APP_FRONTEND_URL').'/product/'.@$product->id.'-' .@$product->slug}}"
                                           target="_blank">{{ optional($product)->name }}</a>
                                    @endif
                                </th>
                                <td>
                                    <p class="p-0 m-0">Name:{{ $product->user->name }}</p>
                                    <p class="p-0 m-0">Shop:{{ @$product->shop->shop_name }}</p></td>
                                <td class="text-center">{{ $product->order_item_count }}</td>
                                <td class="text-center">{{ $product->view_count }}</td>
                                <td>{{ getPriceFormat($product->unit_price) }}</td>
                                <td>{!!  $product->is_published == 1 ? '<span class="text-primary">Published</span>' : '<span class="text-danger">Unpublished</span>' !!}</td>
                            </tr>
                        @empty
                            <td colspan="20" class="text-center text-warning">No Data Found</td>
                        @endforelse

                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
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
                        <h5 class="modal-title" id="exampleModalLabel">Product Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <x-product-status-graph/>
                            <x-top-product-graph/>
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
