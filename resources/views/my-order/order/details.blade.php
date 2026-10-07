@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Order Details'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Order Details'), ['#' => _trans('keyword.Order').' '. _trans('keyword.Management'), '##'=>_trans('keyword.My').' '._trans('keyword.Order'), 'order' => _trans('keyword.My Order')]) !!}
        <div class="row invoice-preview">
            <!-- Invoice -->
            <div class="col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        <div class="row p-sm-3 p-0">
                            <div class="col-xl-6 col-md-6 col-sm-5 col-12 mb-xl-0 mb-md-4 mb-sm-0 mb-4">
                                <h6 class="mb-3 fw-bold">{{_trans('keyword.Payment Info')}}</h6>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Payment status')}}:</td>
                                        <td class="fw-medium">


                                            @if ($order->payment_status == 'paid')
                                                <span class="badge bg-label-success">{{_trans('keyword.Paid')}}</span>
                                            @elseif ($order->payment_status == 'partial')
                                                <span
                                                    class="badge bg-label-info">{{_trans('keyword.Partially Paid')}}</span>
                                                <a href="{{ route('order.paymentHistry', $order->id) }}"
                                                   class="badge bg-label-primary"
                                                   title="Payment History"> {{_trans('keyword.History')}}</a>
                                            @else
                                                <span class="badge bg-label-danger">{{_trans('keyword.Unpaid')}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Total Amount')}}:</td>
                                        <td>{{ getPriceFormat($order->sub_total_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Admin Discount')}}:</td>
                                        <td>{{ getPriceFormat($order->admin_discount_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Tax')}}:</td>
                                        <td>{{ getPriceFormat($order->tax_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Shipping Charges')}}:</td>
                                        <td>{{ getPriceFormat($order->shipping_charges) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Total Order Amount')}}:</td>
                                        <td>{{ getPriceFormat($order->grand_total_amount) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-7 col-12">
                                <h6 class="mb fw-bold">{{_trans('keyword.Order Info')}}</h6>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Order Code')}}:</td>
                                        <td class="fw-medium">{{ $order->code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Order Date')}}:</td>
                                        <td>{{ dateFormat($order->order_date) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Order Status')}}:</td>
                                        <td>{{ $order->orderStatus->name }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="row mt-2">
                                    <div class="col-4">
                                        <a href="{{ route('myOrder.order.invoicePreview', $order->id) }}"
                                           class="btn btn-label-warning d-grid w-100 mb-2 waves-effect">{{_trans('keyword.Invoice')}}</a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('myOrder.order-claim.index') . '?order_id=' . $order->code }}"
                                           class="btn btn-label-primary d-grid w-100 mb-2 waves-effect">{{_trans('keyword.Claim List')}}</a>
                                    </div>
                                    <div class="col-4">
                                        @if ($order->status == 1)
                                            <button class="btn btn-label-danger delete-order" data-id="{{ $order->id }}"
                                                    id="delete_order">{{_trans('keyword.Cancel Order')}}</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-0">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">{{_trans('keyword.Order Items')}}</h6>
                    </div>
                    <div class="card-datatable table-responsive">

                        <table class="datatables-order-details table border-top">
                            <thead>
                            <tr>
                                <th class="col-1">#</th>
                                <th class="col-3">{{_trans('keyword.Name')}}</th>
                                <th class="col-3">{{_trans('keyword.Variation')}}</th>
                                <th class="col-1">{{_trans('keyword.Price')}}</th>
                                <th class="col-2">{{_trans('keyword.qty')}}</th>
                                <th class="col-2">{{_trans('keyword.Total')}}</th>
                                <th class="col-2">{{_trans('keyword.Order Status')}}</th>
                                <th class="col-2">{{_trans('keyword.Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($order->items as $cart_item)
                                <tr id="cartRow{{ $cart_item->id }}">
                                    <td><span>{{ $loop->iteration }}</span></td>

                                    <td class="d-flex align-items-center">
                                        <img src="{{ getFilePath(optional($cart_item->product)->thumbnail_img) }}"
                                             class="me-2" height="50px" alt="">
                                        @if($cart_item->product->relationLoaded('shop'))
                                            <a href="{{env('APP_FRONTEND_URL').'/designer/'.@$cart_item->product->shop->slug.'/product/'.@$cart_item->product->id.'-' .@$cart_item->product->slug }}"
                                               target="_blank">{{ optional($cart_item->product)->name }}</a>
                                        @else
                                            <a href="{{env('APP_FRONTEND_URL').'/product/'.@$cart_item->product->id.'-' .@$cart_item->product->slug}}"
                                               target="_blank">{{ optional($cart_item->product)->name }}</a>
                                        @endif

                                    </td>
                                    <td>

                                        @foreach ($cart_item->variation as $key => $item)
                                            <span><b class="me-1">{{ @$item['attribute'] }}:</b><span
                                                    class="text-primary">{{ @$item['value'] }}</span></span><br>
                                        @endforeach

                                    </td>
                                    <td>
                                        <span>{{ getPriceFormat($cart_item->price) }}</span>

                                    </td>

                                    </td>
                                    <td>

                                        <span>{{ $cart_item->quantity }}</span>

                                    </td>
                                    <td>
                                        {{ getCurrency() }}
                                        <span>{{ number_format($cart_item->price * $cart_item->quantity, 2) }}</span>

                                    </td>

                                    <td>
                                            <span class="badge bg-label"
                                                  style="background-color: {{ optional($cart_item->latestStatus)->status->color ?? '#FFC107' }};">
                                                {{ optional($cart_item->latestStatus)->status->name ?? 'No status available' }}
                                            </span>
                                    </td>
                                    <td>

                                            <span><button type="button" data-id="{{ $cart_item->id }}"
                                                          class="btn btn-label-dark trackbutton" data-bs-toggle="modal"
                                                          data-bs-target="#trackModal{{ $cart_item->id }}">
                                                    {{_trans('keyword.Track')}}
                                                </button>

                                    </td>
                                </tr>

                                <div class="modal fade" id="trackModal{{ $cart_item->id }}" tabindex="-1"
                                     aria-hidden="true">
                                    <div
                                        class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
                                        <div class="modal-content p-3 p-md-5">
                                            <div class="modal-body">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                <div class="text-center mb-4">
                                                    <h3 class="mb-1">{{_trans('keyword.Order Item Track')}}</h3>
                                                    <p class="text-muted">{{_trans('keyword.Track the order item status')}}</p>
                                                </div>
                                                <hr>
                                                <h5 class="card-header mb-3">{{_trans('keyword.Order Item Log Timeline')}}</h5>
                                                <div class="card-body pb-0">
                                                    <ul class="timeline mb-0">
                                                        @forelse  ($cart_item->statusLog as $log)
                                                            <li
                                                                class="timeline-item timeline-item-transparent @if ($loop->last) border-transparent @endif">
                                                                    <span class="timeline-point timeline-point-primary"
                                                                          style="background-color: {{ $log->status->color }} !important"></span>
                                                                <div class="timeline-event">
                                                                    <div class="timeline-header mb-1">
                                                                        <h6 class="mb-0">{{ $log->status->name }}
                                                                        </h6>
                                                                        <small
                                                                            class="text-muted">{{ dateFormatwithTime($log->date_time) }}</small>
                                                                    </div>
                                                                    <p class="mb-2">{{ $log->note }}</p>
                                                                    <div class="d-flex">

                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @empty
                                                            <!-- Display a message if the collection is empty -->
                                                            <p>No Log Added.</p>
                                                        @endforelse
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body mx-3">
                        <div class="row">
                            <div class="col-12">
                                <span class="fw-medium">{{_trans('keyword.Note')}}:</span>
                                <span>{{_trans('keyword.Thank you for choosing us! We appreciate your business and can’t wait to serve you again.')}}</span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /Invoice -->

            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title m-0">{{_trans('keyword.Seller Details')}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <div class="avatar me-2">
                                <img src="{{ getFilePath(optional($order->designer)->avatar) }}" alt="Avatar"
                                     class="rounded-circle"/>
                            </div>
                            <div class="d-flex flex-column">
                                <div class="text-body text-nowrap">
                                    <h6 class="mb-0">{{ optional($order->designer)->name }}</h6>
                                </div>
                                <small class="text-muted">{{_trans('keyword.Member since')}}
                                    : {{monthFormat(optional($order->designer)->created_at)}}</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <h6>{{_trans('keyword.Contact info')}}</h6>

                        </div>
                        <p class="mb-1">{{_trans('keyword.Email')}}: {{ optional($order->designer)->email }}</p>
                        <p class="mb-0">{{_trans('keyword.Mobile')}}: {{ optional($order->designer)->phone }}</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title m-0">{{_trans('keyword.Shipping address')}}</h6>

                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{_trans('keyword.Name')}}: {{ optional($order->shipping_address)->name }}
                        </p>
                        <p class="mb-0">{{_trans('keyword.Email')}}: {{ optional($order->shipping_address)->email }}
                        </p>
                        <p class="mb-0">{{_trans('keyword.Phone')}}: {{ optional($order->shipping_address)->phone }}
                        </p>
                        <p class="mb-0">{{_trans('keyword.Shipping address')}}
                            : {{ optional($order->shipping_address)->street_address }}
                        </p>
                        <p class="mb-0">{{_trans('keyword.State')}}: {{ optional($order->shipping_address)->state }}
                        </p>
                        <p class="mb-0">{{_trans('keyword.Zip Code')}}
                            : {{ optional($order->shipping_address)->zip_code }}
                        </p>
                        <p class="mb-0">{{_trans('keyword.Country')}}: {{ optional($order->shipping_address)->country }}
                        </p>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title m-0">{{_trans('keyword.Note')}}</h6>
                    </div>
                    <div class="card-body">
                        <p>{{ $order->note }}</p>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        $(function () {

            $(document).on("click", "#delete_order", function () {

                let id = $(this).attr("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
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

                        $.ajax({
                            url: '{{ route('order.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                order_id: id,
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: response.icon,
                                    title: 'Canceled!',
                                    text: response.text,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                }).then(function (result) {
                                    window.location.href =
                                        "{{ route('myOrder.order.index') }}";
                                });
                            },
                            error: function (error) {
                                console.log(error.responseJSON.message);
                                // handle the error case
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
