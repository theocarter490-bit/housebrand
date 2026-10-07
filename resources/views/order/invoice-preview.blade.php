@php use App\Models\OrderStatus; @endphp
@extends('layouts.master')

@section('title', $title ?? __('Invoice'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        <div class="row invoice-preview">
            <!-- Invoice -->
            {!! breadcrumb('Invoice', ['#' => 'Order Management', 'order' => 'Invoice']) !!}

            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card" id="invoice-preview-card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between flex-md-row flex-column  m-sm-3 m-0">
                            <div class="mb-xl-0 mb-4">
                                <div class="d-flex flex-column svg-illustration mb-4 align-items-start">
                                    <div class=" demo">
                                        <img src="{{ getFilePath($shop->logo) }}" style="object-fit: contain" width="80" height="40"
                                             alt="company logo">
                                    </div>
                                    <span class="ml-0 fw-bold fs-4"> {{ $shop->shop_name }} </span>
                                </div>
                                <p class="mb-2">{!! str_replace(',', '<br>', $shop->location) !!}</p>
                                <p class="mb-0">{{ $shop->phone }}</p>
                                <p class="mb-0">{{ $shop->email }}</p>
                            </div>
                            <div style="width: 40%">
                                <h4 class="fw-medium mb-2">INVOICE</h4>
                                <div class="mb-2 pt-1">
                                    <p class="mb-0">Invoice ID : <span>{{ @$order->code }}</span></p>
                                    <p class="mb-0">Order Date :<span> {{dateFormatwithTime($order->order_date)}}</span>
                                    </p>
                                    <p class="mb-0 mt-1">Order Status : <span class="badge"
                                                                              style="color:{{ optional($order->orderStatus)->color }};background-color: {{ optional($order->orderStatus)->color }}20">{{ optional($order->orderStatus)->name }}</span>
                                    </p>
                                    <p class="mb-0 mt-1">Payment Status:
                                        @if ($order->payment_status == 'paid')
                                            <span class="badge bg-label-success">{{_trans('keyword.Paid')}}</span>
                                        @elseif ($order->payment_status == 'partial')
                                            <span
                                                class="badge bg-label-warning">{{_trans('keyword.Partially Paid')}}</span>
                                        @else
                                            <span class="badge bg-label-danger">{{_trans('keyword.Unpaid')}}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="fw-medium mb-2">
                                    <h5 class="mb-2 pt-1">Invoice To:</h5>
                                    <p class="mb-1">{{ optional($order->shipping_address)->name }}</p>
                                    <p class="mb-0">{{ optional($order->shipping_address)->phone }}</p>
                                    <p class="mb-0">{{ optional($order->shipping_address)->email }}</p>
                                    <p class="mb-1">{{ optional($order->shipping_address)->street_address }}
                                        , {{ optional($order->shipping_address)->state }}
                                        ,{{ optional($order->shipping_address)->country }}
                                        , {{ optional($order->shipping_address)->zip_code }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0"/>
                    <div class="table-responsive border-top">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th>Variation</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($order->items as $cart_item)
                                <tr>

                                    <td>
                                        {{ optional($cart_item->product)->name }}
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
                                        <div class="d-flex align-items-center gap-1">
                                            {{ getCurrency() }}
                                            <span>{{ number_format($cart_item->price * $cart_item->quantity, 2) }}</span>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="align-top px-4 py-3">
                                    <div class="h-100 d-flex flex-column justify-content-between w-100">
                                        <div style="margin-top: 10rem">
                                            <p>{{ numberTowords(@$order->grand_total_amount) }} {{ generalSetting()->currency->code }}
                                                Only</p> <!-- Bottom-aligned -->
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-3 py-4">
                                    <p class="mb-2 text-nowrap pt-3 fw-bold">Subtotal:</p>
                                    <p class="mb-2 text-nowrap">Discount:</p>
                                    <p class="mb-2 text-nowrap">Tax:</p>
                                    <p class="mb-2 text-nowrap">Shipping Charge:</p>
                                    <p class="mb-2 text-nowrap fw-bold">Grand Total:</p>
                                    <p class="mb-2 text-nowrap fw-bold">Total Paid:</p>
                                    <p class="mb-2 text-nowrap fw-bold">Due Amount:</p>
                                </td>
                                <td class="ps-2 py-4">
                                    <p class="fw-bold text-nowrap mb-2 pt-3">{{ getPriceFormat($order->sub_total_amount) }}</p>
                                    <p class="fw-medium text-nowrap mb-2">{{ getPriceFormat($order->admin_discount_amount) }}</p>
                                    <p class="fw-medium text-nowrap mb-2">{{ getPriceFormat($order->tax_amount) }}</p>
                                    <p class="fw-medium text-nowrap mb-2">{{ getPriceFormat($order->shipping_charges) }}</p>
                                    <p class="fw-bold text-nowrap mb-2">{{ getPriceFormat($order->grand_total_amount) }}</p>
                                    <p class="fw-bold text-nowrap mb-2 ">{{ getPriceFormat($order->paymentDetails->sum('amount')) }}</p>
                                    <p class="fw-bold text-nowrap mb-2">{{ getPriceFormat(calculateOrderDue($order)) }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <p class="mt-0 text-center fw-bold">Thanks for your business</p>
                                    <p class="text-center"><span class="fw-medium">Note:</span> Thank you for choosing us! We appreciate your business and can’t wait to serve you again.</p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /Invoice -->

            <!-- Invoice Actions -->
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card">
                    <div class="card-body">

                        @if(hasPermission('customer_order_list_print_invoice'))
                            <a class="btn btn-label-primary d-grid w-100 mb-2" target="_blank"
                               href="{{ route('order.invoicePrint', $order->id) }}">
                                Print
                            </a>

                        @endif

                        @if(hasPermission('customer_order_list_download_invoice'))
                            <a class="btn btn-label-success d-grid w-100 mb-2" target="_blank"
                               href="{{ route('order.invoiceDownload', $order->id) }}">
                                Download
                            </a>

                        @endif
                        @if(hasPermission('customer_order_list_edit') && $order->payment_status == 'unpaid')
                            <a href="{{ route('order.edit', $order->id) }}"
                               class="btn btn-label-warning d-grid w-100 mb-2">
                                Edit Invoice
                            </a>
                        @endif

                        @if ($order->payment_status != 'unpaid' && $order->status != OrderStatus::CANCELED)
                            <a class="btn btn-label-primary d-grid w-100 mb-2"
                               href="{{ route('order.paymentHistry', $order->id) }}"
                               title="Payment History">{{ __('Payment History') }}</a>
                        @endif

                        @if ( IsSeller() && $order->payment_status != 'paid')
                            <a class="btn btn-primary d-grid w-100 mb-2" href="javascript:void(0)" title="View Details"
                               onclick="makePayment({{ $order->id }},'{{ $order->code }}','{{ @$order->lastPayment ? $order->lastPayment->current_due : @$order->grand_total_amount }}')">{{ __('Add Payment') }}</a>
                        @endif


                        @if(hasPermission('customer_order_list_send_invoice'))
                            <button
                                id="sendInvoice"
                                class="btn btn-primary d-grid w-100"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#sendInvoiceOffcanvas">
                                <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                                        class="ti ti-send ti-xs me-2"></i>Send Invoice</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- /Invoice Actions -->
        </div>


        <!-- Send Invoice Sidebar -->
        <div class="offcanvas offcanvas-end" id="sendInvoiceOffcanvas" aria-hidden="true">
            <div class="offcanvas-header my-1">
                <h5 class="offcanvas-title">Send Invoice</h5>
                <button
                    type="button"
                    class="btn-close text-reset"
                    data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body pt-0 flex-grow-1">
                <form action="{{route('order.sendInvoice')}}" method="post">
                    @csrf
                    <input name="order_id" type="hidden" value="{{$order->id}}">
                    <div class="mb-3">
                        <label for="invoice-to" class="form-label">To</label>
                        <input
                            name="invoice-to"
                            type="text"
                            class="form-control"
                            id="invoice-to"
                            value="{{($order->user->email)}}"
                            placeholder="user@email.com"/>
                    </div>
                    <div class="mb-3">
                        <label for="invoice-subject" class="form-label">Subject</label>
                        <input
                            name="invoice-subject"
                            type="text"
                            class="form-control"
                            id="invoice-subject"
                            value="Invoice of {{$order->code}}"
                            placeholder="Invoice of your product"/>
                    </div>
                    <div class="mb-3">
                        <label for="invoice-message" class="form-label">Message</label>
                        <textarea class="form-control" name="message" id="invoice-message" cols="3" rows="8"></textarea>
                    </div>
                    <div class="mb-4">
                      <span class="badge bg-label-primary">
                        <i class="ti ti-link ti-xs"></i>
                        <span class="align-middle">Invoice Attached</span>
                      </span>
                    </div>
                    <div class="mb-3 d-flex flex-wrap">
                        <button type="submit" class="btn btn-primary me-3" data-bs-dismiss="offcanvas">Send</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- payment modal  --}}
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="verifyModalContent"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header ">
                        <h5 class="modal-title" id="exampleModalLabel">Make Payment For (<span id="code"></span>)</h5>
                        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" id="closeModal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="template-demo">
                            <form action="{{ route('order.paymentStore') }}" method="POST" enctype="multipart/form-data"
                                  class="form">
                                @csrf
                                <input type="hidden" name="order_id" id="order_id">
                                <div class="form-group row">
                                    <div class="col-4 col-lg-4">
                                        <label class="col-form-label">Payable Amount<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-lg-8">
                                        <div class="input-control">
                                            <input class="height-50 form-control" type="text"
                                                   placeholder="Payable Amount *" disabled
                                                   id="payable_amount" value="payable_amount" step="any" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mt-2">
                                    <div class="col-4 col-lg-4">
                                        <label class="col-form-label">Pay<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-lg-8">
                                        <div class="input-control">
                                            <input min="1" class="height-50 form-control" type="number"
                                                   placeholder="Pay Amount *"
                                                   id="pay" name="pay" step="any">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mt-2">
                                    <div class="col-4 col-lg-4">
                                        <label class="col-form-label">Due Amount</label>
                                    </div>
                                    <div class="col-8 col-lg-8">
                                        <div class="input-control">
                                            <input class="height-50 form-control" type="text" placeholder="Due Amount *"
                                                   id="current_due" value="0" name="current_due" step="any" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mt-2">
                                    <div class="col-4 col-lg-4">
                                        <label class="col-form-label">Payment Method<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-lg-8">
                                        <div class="input-control">
                                            <select class="w-100 bb  form-control height-50" aria-label="Select One"
                                                    name="payment_method">
                                                <option selected>Select One</option>
                                                @foreach ($paymentMethos as $methos)
                                                    <option value="{{ $methos->id }}">{{ $methos->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('payment_method'))
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('payment_method') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group row mt-2">
                                    <div class="col-4 col-lg-4">
                                        <label class="col-form-label">Payment Date<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-lg-8">
                                        <div class="input-control">
                                            <input
                                                class="height-50 form-control{{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
                                                type="date" placeholder="From Date *" name="payment_date">
                                            <span class="focus-border"></span>
                                            @if ($errors->has('payment_date'))
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('payment_date') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <div class="promo-btn d-flex justify-content-center mt-4">
                                    <button class="btn btn-primary border-0" type="submit">Submit</button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        {{-- payment modal  --}}

        @endsection

        @push('scripts')

            <script>
                // makePayment
                function makePayment(id, code, amount) {
                    $("#order_id").val(id);
                    $("#code").html(code);
                    $("#payable_amount").val(parseFloat(amount).toFixed(2));
                    $("#pay").val(parseFloat(amount).toFixed(2));
                    $('#paymentModal').modal('show');
                    $('#paymentModal .close-btn').click(function () {
                        $('#paymentModal').modal('hide');
                    });
                };

                $('#pay').on('keyup', function (e) {
                    var inputAmount = e.target.value;
                    var payable = $("#payable_amount").val();
                    var due = payable - inputAmount;
                    if (parseFloat(inputAmount) < 0) {
                        $(this).val(payable);
                    }
                    if (parseFloat(inputAmount) > parseFloat(payable)) {
                        $(this).val(payable);
                    }
                    if (!$.isNumeric(inputAmount) && inputAmount !== '') {
                        $(this).val(payable)
                    }
                    if (due < 0) {
                        due = 0;
                    }

                    $("#current_due").val(parseFloat(due).toFixed(2));
                });

            </script>

    @endpush
