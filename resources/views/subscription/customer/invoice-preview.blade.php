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
                            class="d-flex justify-content-between flex-md-row  flex-column m-sm-3 m-0">
                            <div class="mb-xl-0 mb-4">
                                <div class="d-flex svg-illustration mb-4 gap-2 align-items-center">
                                    <div class="app-brand-logo demo">
                                        <img src="{{ getFilePath(administratorSetting()->logo) }}" width="50" height="50"
                                            alt="">
                                    </div>
                                    <span class="app-brand-text fw-bold fs-4"> {{ administratorSetting()->shop_name }} </span>
                                </div>
                                <h6 class="mb-1 fw-bold">Bill From:</h6>
                                <p class="mb-2">{!! str_replace(',', '<br>', administratorSetting()->location) !!}</p>
                                <p class="mb-0">{{ administratorSetting()->phone }}</p>
                                <p class="mb-0">{{ administratorSetting()->email }}</p>
                            </div>
                            <div>
                                <h4 class="fw-medium mb-2">INVOICE: {{ $invoice->id }}</h4>
                                <div class="mb-2 pt-1">
                                    <span>Date Issues:</span>
                                    <span class="fw-medium">{{ dateFormat($invoice->started_at) }}</span>
                                </div>

                                <h6 class="mb-1 fw-bold">Bill To:</h6>

                                <p class="mb-0">{{ $invoice->customer_name }}</p>
                                <p class="mb-0">{{ $invoice->customer_email }}</p>


                            </div>
                        </div>
                    </div>
                    <hr class="my-0" />

                    <div class="table-responsive border-top">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Created Date</th>
                                    <th>Expire Date</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->lines->data as $item)
                                    <tr>

                                        <td>
                                            {{ optional($item)->description }}
                                        </td>
                                        <td>

                                            {{ gmdate('Y-m-d', optional($item)->period->start) }}

                                        </td>
                                        <td>
                                            {{ gmdate('Y-m-d', optional($item)->period->end) }}
                                        </td>
                                        <td>
                                            {{ getPriceFormat(optional($item)->price->unit_amount / 100) }}

                                        </td>

                                    </tr>
                                @endforeach


                                <tr>
                                    <td colspan="2" class="align-top px-4 py-4">
                                        <span>Thanks for your business</span>
                                    </td>
                                    <td class="text-end pe-3 py-4">

                                        <p class="mb-0 pb-3">Total:</p>
                                    </td>
                                    <td class="ps-2 py-4">

                                        <p class="fw-medium mb-0 pb-3">{{ getPriceFormat($invoice->amount_paid / 100) }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body mx-3">
                        <div class="row">
                            <div class="col-12">
                                <span class="fw-medium">Note:</span>
                                <span>Thank you for choosing us! We appreciate your business and can’t wait to serve you again.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Invoice -->

            <!-- Invoice Actions -->
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card">
                    <div class="card-body">
                        <a class="btn btn-label-primary d-grid w-100 mb-2" target="_blank"
                            href="{{ route('subscription.customer.invoicePrint', $invoice->id) }}">
                            Print
                        </a>
                        <a class="btn btn-label-success d-grid w-100 mb-2" target="_blank"
                            href="{{ route('subscription.customer.invoiceDownload', $invoice->id) }}">
                            Download
                        </a>

                    </div>
                </div>
            </div>
            <!-- /Invoice Actions -->
        </div>

        <!-- Offcanvas -->
    @endsection

    @push('scripts')
    @endpush
