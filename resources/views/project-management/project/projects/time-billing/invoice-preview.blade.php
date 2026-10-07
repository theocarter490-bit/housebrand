@php use App\Models\OrderStatus; @endphp
@extends('layouts.master')

@section('title', $title ?? __('Invoice'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        <div class="row invoice-preview">
            <!-- Invoice -->
            {!! breadcrumb('Invoice', ['#' => 'Project Management', '' => 'Time Billing', '##'=>'Invoice']) !!}

            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card" id="invoice-preview-card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between flex-md-row  flex-column m-sm-3 m-0">
                            <div class="mb-xl-0 mb-4">
                                <div class="d-flex flex-column svg-illustration mb-4 align-items-start">
                                    <div class=" demo">
                                        <img src="{{ getFilePath($shop->logo) }}" width="80" height="40"
                                             alt="">
                                    </div>
                                    <span class="ml-0 fw-bold fs-4"> {{ $shop->shop_name }} </span>
                                </div>
                                <p class="mb-2">{!! str_replace(',', '<br>', $shop->location) !!}</p>
                                <p class="mb-0">{{ $shop->phone }}</p>
                                <p class="mb-0">{{ $shop->email }}</p>
                            </div>
                            <div>
                                <h4 class="fw-medium mb-2">INVOICE</h4>
                                <div class="mb-2 pt-1">
                                    <p class="mb-0">Invoice ID : <span>#{{ $timeBilling->code }}</span></p>
                                    <p class="mb-0">Date :<span> {{dateFormatwithTime($timeBilling->created_at)}}</span>
                                    </p>

                                    <p class="mb-0 mt-1">Bill Type:
                                        @if ($timeBilling->bill_type == 1)
                                            <span class="badge bg-label-success">{{_trans('keyword.Billable')}}</span>
                                        @else
                                            <span
                                                class="badge bg-label-danger">{{_trans('keyword.Non Billable')}}</span>
                                        @endif
                                    </p>

                                    <p class="mb-0 mt-1">Payment Status:
                                        @if ($timeBilling->payment_status == 1)
                                            <span class="badge bg-label-success">{{_trans('keyword.Paid')}}</span>
                                        @elseif ($timeBilling->payment_status == 2)
                                            <span
                                                class="badge bg-label-warning">{{_trans('keyword.Partially Paid')}}</span>
                                        @else
                                            <span class="badge bg-label-danger">{{_trans('keyword.Unpaid')}}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="fw-medium mb-2">
                                    <h5 class="mb-2 pt-1">Invoice To:</h5>
                                    <p class="mb-1">{{ optional($timeBilling->client)->name }}</p>
                                    <p class="mb-0">{{ optional($timeBilling->client)->phone }}</p>
                                    <p class="mb-0">{{ optional($timeBilling->client)->email }}</p>
                                    <p class="mb-1">{{ optional($timeBilling->client)->address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0"/>
                    <div class="table-responsive border-top">
                        <table class="table m-0">
                            <thead>
                            <tr style="background: #eceff4; font-style:bold; font-size: 14px">
                                <th width="10%" class="text-center">SL</th>
                                <th width="15%" class="text-center">Service</th>
                                <th width="15%" class="text-right">Rate</th>
                                <th width="15%" class="text-right">Time</th>
                                <th width="15%" class="text-right">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-center"> 1</td>
                                <td class="text-center"> {{ @$timeBilling->serviceType->projectService->title }} </td>

                                <td class="text-right"><span>{{ getPriceFormat($timeBilling->rate) }}</span></td>
                                </td>
                                <td class="text-right"><span>{{ secondsToHMS($timeBilling->duration) }}</span></td>
                                <td class="text-right">
                                    <span>{{ getPriceFormat($timeBilling->total_amount) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="align-top px-4 py-3">
                                    <div class="h-100 d-flex flex-column justify-content-between w-100">
                                        <div style="margin-top: 10rem">
                                            <p>{{ numberTowords(@$timeBilling->total_amount) }} {{ generalSetting()->currency->code }}
                                                Only</p> <!-- Bottom-aligned -->
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-3 py-4">
                                    <p class="mb-2 fw-bold">Grand Total:</p>
                                    <p class="mb-2 fw-bold">Total Paid:</p>
                                    <p class="mb-2 fw-bold">Due Amount:</p>
                                </td>
                                <td class="text-end ps-2 py-4">
                                    <p class="fw-bold mb-2">{{ getPriceFormat($timeBilling->total_amount) }}</p>
                                    <p class="fw-bold mb-2 ">{{ getPriceFormat(@$timeBilling->paymentDetails->sum('amount')) }}</p>
                                    <p class="fw-bold mb-2">{{ getPriceFormat(calculateTimeBillingDue($timeBilling)) }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <p class="mt-0 text-center fw-bold">Your satisfaction is our priority. Please
                                        contact us if you have any questions.
                                    </p>
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

                        @if(hasPermission('time_billing_invoice_print'))
                            <a class="btn btn-label-primary d-grid w-100 mb-2" target="_blank"
                               href="{{ route('project-management.project.time-billing.invoicePrint', $timeBilling->id) }}">
                                Print
                            </a>
                        @endif

                        @if(hasPermission('time_billing_invoice_download'))
                            <a class="btn btn-label-success d-grid w-100 mb-2" target="_blank"
                               href="{{ route('project-management.project.time-billing.invoiceDownload', $timeBilling->id) }}">
                                Download
                            </a>
                        @endif

                        @if ($timeBilling->payment_status != 0 && $timeBilling->bill_type != 0)
                            <a class="btn btn-label-primary d-grid w-100 mb-2"
                               href="{{ route('project-management.project.time-billing.paymentHistry', $timeBilling->id) }}"
                               title="Payment History">{{ __('Payment History') }}</a>
                        @endif

                        @if ( IsSeller() && $timeBilling->payment_status != 1 && $timeBilling->bill_type != 0)
                            <a class="btn btn-primary d-grid w-100 mb-2" href="javascript:void(0)" title="View Details"
                               onclick="makePayment({{ $timeBilling->id }},'{{ $timeBilling->code }}','{{ @$timeBilling->lastPayment ? $timeBilling->lastPayment->current_due : @$timeBilling->total_amount }}')">{{ __('Add Payment') }}</a>
                        @endif

                        @if(hasPermission('time_billing_send_invoice'))
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
                <form action="{{route('project-management.project.time-billing.sendInvoice')}}" method="post">
                    @csrf
                    <input name="time_billing_id" type="hidden" value="{{$timeBilling->id}}">
                    <div class="mb-3">
                        <label for="invoice-to" class="form-label">To</label>
                        <input
                            name="invoice-to"
                            type="text"
                            class="form-control"
                            id="invoice-to"
                            value="{{($timeBilling->client->email)}}"
                            placeholder="user@email.com"/>
                    </div>
                    <div class="mb-3">
                        <label for="invoice-subject" class="form-label">Subject</label>
                        <input
                            name="invoice-subject"
                            type="text"
                            class="form-control"
                            id="invoice-subject"
                            value="Invoice of {{$timeBilling->code}}"
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
                            <form action="{{ route('project-management.project.time-billing.paymentStore') }}"
                                  method="POST" enctype="multipart/form-data"
                                  class="form">
                                @csrf
                                <input type="hidden" name="time_billing_id" id="time_billing_id">
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
                                            <input class="height-50 form-control" type="number"
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
                    $("#time_billing_id").val(id);
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
