@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Dashboard'))

@section('content')
    <div class="row">
        {{-- @include('assets.layouts.breadcrumb' , ['title' => @$title], ['breadcrumb'=>'dashboard']) --}}

        <!-- View sales -->
        @if(hasPermission('best_seller_read'))
            <x-best-seller/>
        @endif

        @if(hasPermission('welcome_message') && auth()->user()->role_id != \App\Models\Role::SUPER_ADMIN)
            <x-welcome-message/>
        @endif
        <!-- View sales -->
        <!-- Statistics -->
        @if(hasPermission('statistics_read'))
            <x-dashboard-statistic/>
        @endif
        <!--/ Statistics -->

        {{--    Order Status Graph--}}
        @if(hasPermission('order_status_graph_read') && hasPermission('order_management_read'))
            <x-oder-status-graph/>
        @endif
        {{--    Order Status Graph end--}}

        {{--    Order payment graph--}}
        @if(hasPermission('order_payment_graph_read') && hasPermission('order_management_read'))
            <x-oder-payment-graph/>
        @endif
        {{--    Order payment graph end--}}

        <!-- Upcoming Event -->
        @if(hasPermission('upcoming_event_read'))
            <x-event-detail/>
        @endif
        <!-- Upcoming Event -->

        <!-- Earning Reports -->
        @if(hasPermission('earning_report_read'))
            <x-earning-report/>
        @endif
        <!--/ Earning Reports -->

        <!-- Popular Product -->
        @if(hasPermission('popular_product_read'))
            <div class="col-xl-8 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between pb-2">
                        <div class="card-title m-0">
                            <h5 class="m-0">{{ _trans('keyword.Most Popular Products') }}</h5>
                        </div>
                    </div>
                    <div class="card-body table-responsive custom-scroll max-h-420">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col" class="text-nowrap fw-bold bg-light">Image</th>
                                <th scope="col" class="text-nowrap fw-bold bg-light">Title</th>
                                <th scope="col" class="text-nowrap fw-bold bg-light">Sold</th>
                                <th scope="col" class="text-nowrap fw-bold bg-light">Views</th>
                                <th scope="col" class="text-nowrap fw-bold bg-light">Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($data['popularProducts'] as $product)
                                <tr>
                                    <td>
                                        <img src="{{ getFilePath($product->thumbnail_img) }}" alt="Image"
                                             class="rounded"
                                             width="46">
                                    </td>
                                    <td>
                                        <h6 class="mb-0">
                                            @if($product->relationLoaded('shop'))
                                                <a href="{{env('APP_FRONTEND_URL').'/designer/'.@$product->shop->slug.'/product/'.@$product->id.'-' .@$product->slug }}"
                                                   target="_blank">{{ optional($product)->name }}</a>
                                            @else
                                                <a href="{{env('APP_FRONTEND_URL').'/product/'.@$product->id.'-' .@$product->slug}}"
                                                   target="_blank">{{ optional($product)->name }}</a>
                                            @endif

                                        </h6>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-success">{{ $product->num_of_sale }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $product->view_count }}</span>
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-medium">{{ getPriceFormat($product->unit_price) }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" style="text-align: center;">
                                        No data found
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        <!--/ Popular Product -->

        <!--/ Notice -->
        @if(hasPermission('recent_notice_read'))
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">{{ _trans('keyword.Recent Notice') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @forelse($data['recentNotice'] as $notice)
                                <li class="d-flex mb-4 pb-1 align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="ti ti-info-circle ti-md"></i> <!-- Info icon for all notices -->
                                </span>
                                    </div>
                                    <div class="row w-100 align-items-center">
                                        <div class="col-sm-8 col-lg-12 col-xxl-8 mb-1 mb-sm-0 mb-lg-1 mb-xxl-0">
                                            <p class="mb-0 fw-medium">{{ $notice->title }}</p>
                                        </div>
                                        <div
                                            class="col-sm-4 col-lg-12 col-xxl-4 d-flex  justify-content-md-start justify-content-xxl-end justify-content-between">
                                            <div
                                                class="badge bg-label-secondary">{{ dateFormatwithTime($notice->published_at) }}
                                            </div>
                                            <a href="{{ route('notice-board.notice.noticeBoard') }}" class="ms-2">
                                                <i class="ti ti-eye ti-md"></i>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <p>No Notice Found</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        <!--/ Notice -->

        <!-- Claim Tracker -->
        @if(hasPermission('order_claim_graph_read'))
            <x-order-claim-graph/>
        @endif
        <!-- Claim Tracker end -->

        @if(hasPermission('expense_graph_read'))
            <x-expenses-graph/>
        @endif

        @if (Auth::user()->role_id == \App\Models\Role::SUPER_ADMIN)
            <x-google-analytics/>
        @endif
        <x-initial-setting-modal/>

    </div>
    <script>
        $(document).ready(function () {

            @if(!shopSetting()->shop_name || !shopSetting()->phone || !shopSetting()->email || !shopSetting()->location)
            $('#multiStepModal').modal('show');
            @endif

        });

    </script>
@endsection
