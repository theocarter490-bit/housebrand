@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Payment & Billing Details'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Payment & Billing Details'), [
            '#' => _trans('keyword.Payment & Billing Details') . ' ' . _trans('keyword.Management'),
            'payment' => _trans('keyword.Payment & Billing Details'),
        ]) !!}


        <div class="row">
            <div class="col-md-12">
                <!-- Current Plan -->
                <div class="row mb-4">
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                @if($user->lastSubscription)
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="card-title mb-2"><strong>{{_trans('keyword.Current Active Plan')}}</strong> </h5>
                                            <div class="mb-3 row">
                                                <div class="col-4">
                                                    Name:
                                                </div>
                                                <div class="col-8">
                                                    <h6 class="mb-1">{{ optional(optional($user->lastSubscription)->plan)->name }}</h6>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <div class="col-4">
                                                    Expired:
                                                </div>
                                                <div class="col-8">
                                                    <h6 class="mb-1">Active until
                                                        {{ dateFormat(optional($user->lastSubscription)->ends_at) }}
                                                    </h6>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <div class="col-4">
                                                    Price:
                                                </div>
                                                <div class="col-8">
                                                    <h6 class="mb-1">
                                                <span
                                                    class="me-2">{{ getPriceFormat(optional(optional($user->lastSubscription)->plan)->price) }}
                                                    Per
                                                    {{ ucfirst(optional(optional($user->lastSubscription)->plan)->plan_type) }}</span>
                                                        @if (optional(optional($user->lastSubscription)->plan)->is_popular)
                                                            <span class="badge bg-label-primary">Popular</span>
                                                        @endif
                                                    </h6>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    Status:
                                                </div>
                                                <div class="col-8">
                                                    <h6 class="mb-1">
                                                        @if($user->lastSubscription->stripe_status == 'active')
                                                            <span class="badge bg-label-success">{{$user->lastSubscription->stripe_status}}</span>
                                                        @else
                                                            <span class="badge bg-label-danger">{{$user->lastSubscription->stripe_status}} </span>
                                                        @endif
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($user->lastSubscription->stripe_status == 'active' && $user->lastSubscription->cancel_at_period_end === 0)
                                            <div class="col-7">
                                                <h5 class="card-title mb-2"><strong>{{_trans('keyword.Current Active Plan')}}</strong> </h5>
                                                <div class="row">
                                                    @if($user->lastSubscription->plan->modules)
                                                        <div class="col-6">
                                                            @foreach(json_decode($user->lastSubscription->plan->modules) as $module)
                                                                <li>{{ucfirst(str_replace("-"," ",$module->slug))}}</li>
                                                            @endforeach
                                                        </div>
                                                        <div class="col-6">
                                                            @foreach(json_decode($user->lastSubscription->plan->modules) as $module)
                                                                <p class="m-0 p-0">: {{ucfirst($module->limit)}}</p>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="d-flex gap-2 mt-4">
                                                    <button
                                                        href="{{ route('subscription.customer.immediateSubscriptionCancel', $user->id) }}"
                                                        class="btn btn-danger" id="immediateSubscriptionCancel"
                                                        title="Immediate Subscription Cancellation">Terminate
                                                    </button>
                                                    <button
                                                        href="{{ route('subscription.customer.revokeSubscriptionCancel', $user->id) }}"
                                                        class="btn btn-warning" id="revokeSubscriptionCancel"
                                                        title="Cancellation After the Subscription Period">Discontinue
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        @if($user->lastSubscription->cancel_at_period_end === 1)
                                            <p class="text-danger mt-1">Subscription cancellation request have been
                                                send. Subscription will be
                                                cancel after the current period.</p>
                                        @endif
                                    </div>
                                @else
                                    <p>No Current Active Subscription</p>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="d-flex flex-wrap justify-content-between align-items-start p-3">
                                <!-- Customer Details (Left) -->
                                <div>
                                    <div class="flex-grow-1 pe-3 ">
                                        <strong style="margin-top: 10px">{{ _trans('keyword.Customer Details') }}</strong>
                                        <div class="">
                                            <div class="avatar me-3">
                                                <img src="{{ getFilePath($user->avatar) }}" alt="Avatar"
                                                     class="rounded-circle" style="width: 50px; height: 50px;"/>
                                            </div>
                                            <div class="d-flex flex-column" style="padding: 12px 0">
                                                <a href="{{ route('user.profile', $user->id) }}"
                                                   class="text-body text-nowrap" title="{{ $user->name }}">
                                                    <h6 class="mb-0">Name: {{ $user->name }}</h6>
                                                </a>
                                                <input type="hidden" value="{{ $user->id }}" name="user_id">
                                            </div>
                                        </div>
                                        <div>
                                            <p class="mb-1">Email: {{ $user->email }}</p>
                                            <p class="mb-0">Phone: {{ $user->phone }}</p>
                                            <p class="mb-0">Member Since: {{ dateFormat($user->created_at) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Card Details (Right) -->
                                <div>
                                    <div class="text-end">
                                        <button type="button"
                                                class="border border-0 text-primary bg-transparent m-0 p-0"
                                                data-bs-toggle="popover" data-bs-placement="right"
                                                data-bs-content="Details of your most recent payment card"
                                                aria-label="Pricing" aria-describedby="popover59341"><small
                                                class="rounded-circle p-0 m-0 px-1 bg-primary"><i
                                                    class="fa-solid fa-question text-white"
                                                    style="font-size: 10px !important"></i></small>
                                        </button>
                                    </div>
                                    <div class="cardMaster p-3 rounded shadow-sm text-end"
                                         style="background-color: burlywood">

                                        @if(isset($cardDetails['card_brand']) && isset($cardBrandImages[$cardDetails['card_brand']]))
                                            <img src="{{ asset($cardBrandImages[$cardDetails['card_brand']]) }}"
                                                 alt="{{ $cardDetails['card_brand'] }}" class="mb-3"
                                                 style="width: 50px;">
                                        @else
                                            <img src="{{ asset('assets/img/icons/payments/visa.png') }}"
                                                 alt="Default Card" class="mb-3" style="width: 50px;">
                                        @endif

                                        <div
                                            style="height: 120px; display: flex; flex-direction: column; justify-content: space-around;">
                                            <div class="text-start">
                                                <h1 class="" style="width: 100%; font-size: 20px; letter-spacing: 9px;">
                                                    **** **** **** {{ $cardDetails['card_last4'] ?? '****' }}
                                                </h1>
                                            </div>

                                            <div class="d-flex align-items-between justify-content-between">
                                                <p class="mb-0 me-2"><strong>{{ $user->name ?? 'User Name' }}</strong>
                                                </p>
                                                <div class="text-end">
                                                    <small class="text-muted d-block">
                                                        Expires: {{ $cardDetails['card_expiry'] ?? 'MM/YY' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- /Current Plan -->
                <div class="card">
                    <div class="card-header">
                        <h5>Billing History</h5>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="data-table table border-top">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Plan Name</th>
                                <th>Customer</th>
                                <th>Subscription No</th>
                                <th>Invoice</th>
                                <th>Price</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($subscriptionsItem as $list)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ optional(optional($list->subscription)->plan)->name }}</td>
                                    <td>
                                       <strong>{{ json_decode($list->customer_details)->name }}</strong> </br>{{ json_decode($list->customer_details)->email }}</br>{{ optional(json_decode($list->customer_details))->phone }}
                                    </td>
                                    <td>{{ optional($list->subscription)->stripe_subscription_id }}</td>
                                    <td>
                                        <a href="{{ route('subscription.customer.invoicePreview', $list->stripe_invoice_no) }}">HB Invoice</a></br>
                                        <a href="{{ route('subscription.customer.stripeGenerateInvoice', $list->stripe_invoice_no) }}">Stripe Invoice</a>
                                    </td>
                                    <td>{{ getPriceFormat($list->price / 100) }}</td>
                                    <td>
                                        <p>Purchase: {{ dateFormat( $list->started_at) }}</p>
                                        <p>Expire: {{ dateFormat( $list->expire_at) }}</p>
                                    </td>
                                    <td>
                                        @if ($list->payment_status == 'paid')
                                            <span class="badge bg-label-success">{{ $list->payment_status }}</span>
                                        @else
                                            <span class="badge bg-label-danger">{{ $list->payment_status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-felx justify-content-center">
                        {{ $subscriptionsItem->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(function () {
            $(document).on("click", "#immediateSubscriptionCancel", function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will immediately cancel the subsctipion!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Cancel it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {

                        window.location.href =
                            "{{ route('subscription.customer.immediateSubscriptionCancel', $user->id) }}";
                    }
                });
            });
            $(document).on("click", "#revokeSubscriptionCancel", function () {


                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will cancel the subsctipion after the subscription period!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, cancel it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {

                        window.location.href =
                            "{{ route('subscription.customer.revokeSubscriptionCancel', $user->id) }}";

                    }
                });
            });

        });
    </script>
@endpush
