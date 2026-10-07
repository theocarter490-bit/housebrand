@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Edit Order'))

@section('content')

    <div class="flex-grow-1 container-p-y pt-0">
        {!! breadcrumb(_trans('keyword.Edit Order'), [
            '#' => _trans('keyword.Order') . ' ' . _trans('keyword.Management'),
            '##' => _trans('keyword.Customer') . ' ' . _trans('keyword.Order'),
            'order' => _trans('keyword.Edit Order'),
        ]) !!}

        <form action="{{ route('order.update') }}" method="POST">
            @csrf
            <div class="row invoice-preview">
                <div class="col-md-8 col-12 mb-md-0 mb-4">
                    <div class="card invoice-preview-card">
                        <div class="card-body">
                            <div class="row p-sm-3 p-0">
                                <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-4 mb-sm-0 mb-4">
                                    <h6 class="mb-3 fw-bold">{{ _trans('keyword.Payment Info') }}</h6>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td class="pe-4">{{ _trans('keyword.Payment status') }}:</td>
                                            <td class="fw-medium">
                                                @if ($order->payment_status == 'paid')
                                                    <span
                                                        class="badge bg-label-success">{{_trans('keyword.Paid')}}</span>
                                                @elseif ($order->payment_status == 'partial')
                                                    <span
                                                        class="badge bg-label-info">{{_trans('keyword.Partially Paid')}}</span>
                                                    <a href="{{ route('order.paymentHistry', $order->id) }}"
                                                       class="badge bg-label-primary"
                                                       title="Payment History"> {{_trans('keyword.History')}}</a>
                                                @else
                                                    <span
                                                        class="badge bg-label-danger">{{_trans('keyword.Unpaid')}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pe-4">{{ _trans('keyword.Total Amount') }}:</td>
                                            <td>{{ getPriceFormat($order->sub_total_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-4">{{ _trans('keyword.Admin Discount') }}:</td>
                                            <td>{{ getPriceFormat($order->admin_discount_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-4">{{ _trans('keyword.Tax') }}:</td>
                                            <td>{{ getPriceFormat($order->tax_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-4">{{ _trans('keyword.Shipping Charges') }}:</td>
                                            <td>{{ getPriceFormat($order->shipping_charges) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-4">{{ _trans('keyword.Total Order Amount') }}:</td>
                                            <td>{{ getPriceFormat($order->grand_total_amount) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="text" hidden required name="order_id" value="{{ $order->id }}">
                                </div>
                                <div class="col-xl-6 col-md-12 col-sm-7 col-12">
                                    <h6 class="mb fw-bold">{{ _trans('keyword.Order Info') }}</h6>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td class="pe-4">{{ _trans('keyword.Order Code') }}:</td>
                                            <td class="fw-medium">{{ $order->code }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-4">{{ _trans('keyword.Order Date') }}:</td>
                                            <td>{{ dateFormatwithTime($order->order_date) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-4">{{ _trans('keyword.Order Status') }}:</td>
                                            <td><span
                                                    class="badge bg-label-info">{{ $order->orderStatus->name }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Order Details Table -->
                        <div>

                            <div class="row">
                                <div class="col-12 ">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title m-0">{{ _trans('keyword.Order Items') }}</h5>
                                        <button type="button" id="addItemButton" class="btn btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#addItem"><i
                                                class="ti ti-plus ti-xs me-0 me-sm-2"></i>{{ _trans('keyword.Add Item') }}
                                        </button>
                                    </div>
                                    <div class="card-datatable table-responsive">
                                        <table class="data-table table border-top">
                                            <thead>
                                            <tr>
                                                <th class="col-1">#</th>
                                                <th class="col-3">{{ _trans('keyword.Name') }}</th>
                                                <th class="col-2">{{ _trans('keyword.Variation') }}</th>
                                                <th class="col-2">{{ _trans('keyword.Price') }}</th>
                                                <th class="col-1">{{ _trans('keyword.quantity') }}</th>
                                                <th class="col-1">{{ _trans('keyword.Total Amount') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody id="orderItemWrapper">

                                            @foreach ($order->items as $key => $cart_item)
                                                <tr id="cartRow{{ $cart_item->id }}">
                                                    <td><i class="ti ti-trash text-danger cursor-pointer deleteProduct"
                                                           data-id="{{ $cart_item->id }}"></i></td>

                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img
                                                                src="{{ getFilePath(optional($cart_item->product)->thumbnail_img) }}"
                                                                class="me-2" height="50px" alt="">
                                                            {{ optional($cart_item->product)->name }}
                                                            <input type="number"
                                                                   value="{{ optional($cart_item->product)->id }}"
                                                                   hidden
                                                                   name="product[{{ $key }}][product_id]">
                                                            <input type="number"
                                                                   value="{{ $cart_item->id }}" hidden
                                                                   name="product[{{ $key }}][cart_item_id]">
                                                        </div>
                                                    </td>
                                                    <td>

                                                        @foreach ($cart_item->variation as $index => $item)
                                                            <span><b class="me-1">{{ @$item['attribute'] }}:</b><span
                                                                    class="text-primary">{{ @$item['value'] }}</span></span>
                                                            <br>
                                                            <input type="text" value="{{ @$item['attribute'] }}"
                                                                   hidden
                                                                   name="product[{{ $key }}][variant][{{ $index }}][attribute]">
                                                            <input type="text" value="{{ @$item['value'] }}" hidden
                                                                   name="product[{{ $key }}][variant][{{ $index }}][value]">
                                                        @endforeach

                                                    </td>
                                                    <td>
                                                        {{-- <span>{{ getPriceFormat($cart_item->price) }}</span> --}}
                                                        <input class="form-control w-100 price"
                                                               data-product_id="{{ $key }}"
                                                               id="product-{{ $key }}-price" type="number"
                                                               name="product[{{ $key }}][price]"
                                                               value="{{ $cart_item->price }}"/>
                                                    </td>

                                                    <td>
                                                        <input class="form-control w-100 quantity" type="number"
                                                               data-product_id="{{ $key }}"
                                                               name="product[{{ $key }}][quantity]"
                                                               id="product-{{ $key }}-quantity"
                                                               value="{{ $cart_item->quantity }}" min="1"/>
                                                    </td>
                                                    <td>
                                                        {{ getCurrency() }}
                                                        <span
                                                            id="product-{{ $key }}-total_price-view">{{ number_format($cart_item->price * $cart_item->quantity, 2) }}</span>
                                                        <input class="total_price_of_product" type="text" hidden
                                                               id="product-{{ $key }}-total_price"
                                                               value="{{ $cart_item->price * $cart_item->quantity }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <div
                                            class="d-flex flex-column justify-content-end align-items-end m-3 mb-2 p-1">

                                            <div class="d-flex justify-content-between mb-2 w-px-300">
                                                <span
                                                    class="text-heading"><strong>{{ _trans('keyword.Subtotal') }}:</strong></span>
                                                <h6 class="mb-0"><strong>{{ getCurrency() }} <span
                                                            id="sub_total">{{ $order->sub_total_amount }}</span>
                                                    </strong>
                                                </h6>
                                                <input type="text" name="sub_total" hidden id="sub_total_input"
                                                       value="{{ $order->sub_total_amount }}">
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="d-flex me-4 w-px-300">
                                                    <select name="discount_type" class="form-select me-2 charges"
                                                            id="discount_type" data-placeholder="Discount type">
                                                        <option selected value="0">
                                                            {{ _trans('keyword.Discount Type') }}
                                                        </option>
                                                        <option value="1"
                                                            {{ $order->admin_discount_type == 1 ? 'selected' : '' }}>
                                                            Percentage
                                                        </option>
                                                        <option value="2"
                                                            {{ $order->admin_discount_type == 2 ? 'selected' : '' }}>
                                                            Fixed Amount
                                                        </option>
                                                    </select>
                                                    <input type="number" class="form-control w-50 charges"
                                                           name="discount_value" id="discount_value"
                                                           step="0.01"
                                                           min="0.00"
                                                           value="{{ $order->admin_discount_value }}"/>


                                                </div>
                                                <div class="w-px-300 justify-content-between d-flex">
                                                    <span class="text-heading">{{ _trans('keyword.Discount') }}:</span>
                                                    <h6 class="mb-0">{{ getCurrency() }} <span
                                                            id="discount_amount">{{ $order->admin_discount_amount }}</span>
                                                    </h6>
                                                    <input type="number" name="discount_amount" hidden
                                                           id="discount_amount_input" step="0.01"
                                                           value="{{ $order->admin_discount_amount }}"/>

                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="d-flex me-4 w-px-300">
                                                    <select name="tax_type" id="tax_type"
                                                            class="form-select me-2 charges"
                                                            data-placeholder="Tax type">
                                                        <option selected value="0">{{ _trans('keyword.Tax Type') }}
                                                        </option>
                                                        <option value="1"
                                                            {{ $order->tax_type == 1 ? 'selected' : '' }}>
                                                            {{ _trans('keyword.Percentage') }}
                                                        </option>
                                                        <option value="2"
                                                            {{ $order->tax_type == 2 ? 'selected' : '' }}>
                                                            {{ _trans('keyword.Fixed Amount') }}
                                                        </option>
                                                    </select>
                                                    <input type="number" class="form-control w-50 charges"
                                                           name="tax_value" id="tax_value" min="0.0" step="0.01"
                                                           value="{{ $order->tax_value }}">

                                                </div>

                                                <div class="w-px-300 justify-content-between d-flex">
                                                    <span class="text-heading">{{ _trans('keyword.Tax') }}:</span>
                                                    <h6 class="mb-0">{{ getCurrency() }} <span
                                                            id="tax">{{ $order->tax_amount }}</span></h6>
                                                    <input type="text" name="tax_amount" hidden id="tax_amount_input"
                                                           step="0.01"
                                                           value="{{ $order->tax_amount }}">

                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 w-px-300">
                                                <span
                                                    class="text-heading">{{ _trans('keyword.Shipping Charge') }}:</span>
                                                <div class="d-flex align-items-center">
                                                    {{ getCurrency() }}<input type="number" id="shipping_charge"
                                                                              name="shipping_charge"
                                                                              class="ms-1 form-control p-1 text-end charges"
                                                                              style="width: 100px" min="0.0" step="any"
                                                                              value="{{ $order->shipping_charges }}"/>

                                                </div>
                                            </div>
                                            <br>
                                            <div class="d-flex justify-content-between w-px-300 border-top pt-2">
                                                <h4 class=" mb-0">Total:</h4>
                                                <h4 class="mb-0">{{ getCurrency() }} <span id="total">0</span>
                                                </h4>
                                                <input type="text" name="total" hidden id="total_input"
                                                       value="{{ $order->grand_total_amount }}">

                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-center mb-3">
                                        @if($order -> payment_status == 'unpaid')
                                            <button class="btn btn-primary"
                                                    type="submit">{{ _trans('keyword.Update') }}
                                                <span class="loader"></span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{--            Customer Detals --}}
                <div class="col-12 col-lg-4 ">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title m-0"><strong>{{ _trans('keyword.Customer Details') }}</strong></h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-start align-items-center mb-4">
                                <div class="avatar me-2">
                                    <img src="{{ getFilePath(optional(userInfo($order->user))->avatar) }}" alt="Avatar"
                                         class="rounded-circle"
                                         style="object-fit: contain; border: 1px solid var(--bs-primary);"/>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="{{route('user.profile', optional($order->user)->id)}}"
                                       class="text-body text-nowrap">
                                        <h6 class="mb-0">{{ optional(userInfo($order->user))->name }}</h6>
                                    </a>
                                    <small class="text-muted">{{_trans('keyword.Member since')}}
                                        : {{monthFormat(optional(userInfo($order->user))->created_at)}}</small>
                                </div>
                            </div>
                            {{-- <div class="d-flex justify-content-start align-items-center mb-4">
                            <span
                                class="avatar rounded-circle bg-label-success me-2 d-flex align-items-center justify-content-center"><i
                                    class="ti ti-shopping-cart ti-sm"></i></span>
                            <h6 class="text-body text-nowrap mb-0">12 Orders</h6>
                        </div> --}}
                            <div class="d-flex justify-content-between">
                                <h6><strong>{{ _trans('keyword.Contact') . ' ' . _trans('keyword.Info') }}</strong></h6>
                            </div>
                            <p class="mb-1">Email: {{ $order->user->email }}</p>
                            <p class="mb-0">Phone: {{ $order->user->phone }}</p>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between">
                            <h6 class="card-title m-0">{{ _trans('keyword.Shipping Address') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <p class="mb-0">{{_trans('keyword.Name')}}
                                        : {{ optional($order->shipping_address)->name }}
                                    </p>
                                    <p class="mb-0">{{_trans('keyword.Email')}}
                                        : {{ optional($order->shipping_address)->email }}
                                    </p>
                                    <p class="mb-0">{{_trans('keyword.Phone')}}
                                        : {{ optional($order->shipping_address)->phone }}
                                    </p>
                                    <p class="mb-0">{{_trans('keyword.Shipping address')}}
                                        : {{ optional($order->shipping_address)->street_address }}
                                    </p>
                                    <p class="mb-0">{{_trans('keyword.State')}}
                                        : {{ optional($order->shipping_address)->state }}
                                    </p>
                                    <p class="mb-0">{{_trans('keyword.Zip Code')}}
                                        : {{ optional($order->shipping_address)->zip_code }}
                                    </p>
                                    <p class="mb-0">{{_trans('keyword.Country')}}
                                        : {{ optional($order->shipping_address)->country }}
                                    </p>
                                </div>

                                @foreach ($shipping_addresses as $shipping_address)
                                    <div class="col-6 mb-2">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content"
                                                   for="shippingAddress{{ $shipping_address->id }}">
                                                <span class="custom-option-body">

                                                    <small>{{ _trans('keyword.Name') }}:
                                                        {{ $shipping_address->name }}</small>
                                                    <br>
                                                    <small>{{ _trans('keyword.Email') }}:
                                                        {{ $shipping_address->email }}</small><br>
                                                    <small>{{ _trans('keyword.Phone') }}:
                                                        {{ $shipping_address->phone }}</small><br>
                                                    <small>{{ _trans('keyword.Street') }}:
                                                        {{ $shipping_address->street_address }}</small><br>
                                                    <small>State:
                                                        {{ $shipping_address->state }}</small><br>
                                                    <small>{{ _trans('keyword.ZIP Code') }}:
                                                        {{ $shipping_address->zip_code }}</small><br>
                                                    <small>{{ _trans('keyword.Country') }}:
                                                        {{ $shipping_address->country }}</small><br>
                                                </span>
                                                <input name="shippingAddressId" class="form-check-input" type="radio"
                                                       value="{{ $shipping_address->id }}"
                                                       id="shippingAddress{{ $shipping_address->id }}"/>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach

                                @error('shippingAddressId')
                                <span class="text-danger editDescriptionError error">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between">
                            <h6 class="card-title m-0">{{ _trans('keyword.Note') }}</h6>
                        </div>
                        <div class="card-body">
                            <textarea name="note" placeholder="Note" class="form-control" id="note" cols="30"
                                      rows="5">{{ $order->note }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="modal fade" id="addItem" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3 class="mb-2">{{ _trans('keyword.Add New Product') }}</h3>
                        </div>
                        <form id="editUserForm" class="row g-3" onsubmit="return false">
                            <div class="col-12">
                                <label class="form-label"
                                       for="modalEditUserCountry">{{ _trans('keyword.Select Product') }}</label>
                                <select id="modalProductSelect" name="modalEditUserCountry" class="select2 form-select"
                                        data-allow-clear="true" data-placeholder="Select a product">
                                    <option value=""></option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="variantsWrapper" class="col-12">

                            </div>
                            <div class="col-12">
                                <h6 id="priceWrapper"> price: {{ getCurrency() }} <span id="modalPrice"></span></h6>
                            </div>


                            <div class="col-12 text-center">
                                <button type="button" id="addItemSubmitButton" disabled
                                        class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Submit') }}</button>
                                <button id="modalClose" type="reset" class="btn btn-label-secondary"
                                        data-bs-dismiss="modal" aria-label="Close">
                                    {{ _trans('keyword.Cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


@push('scripts')
    <script>
        $(function () {

            calculateTotalPrices();


            $(document).on('change', '.charges', function () {

                calculateTotalPrices();
            });

            $(document).on('change', '.quantity', function () {
                let product_id = $(this).attr('data-product_id');
                let quantity = $(this).val();
                let price = $(`#product-${product_id}-price`).val();
                let total_price = parseFloat(price) * parseFloat(quantity);


                $(`#product-${product_id}-total_price-view`).text(total_price.toFixed(2));
                $(`#product-${product_id}-total_price`).val(total_price.toFixed(2));
                calculateTotalPrices();
            });

            $(document).on('change', '.price', function () {
                let product_id = $(this).attr('data-product_id');
                let price = $(this).val();
                let quantity = $(`#product-${product_id}-quantity`).val();
                let total_price = parseFloat(price) * parseFloat(quantity);

                $(`#product-${product_id}-total_price-view`).text(total_price.toFixed(2));
                $(`#product-${product_id}-total_price`).val(total_price.toFixed(2));
                calculateTotalPrices();
            });


            function calculateTotalPrices() {

                let subtotal = 0.00;
                let total = 0
                $(".total_price_of_product").each(function (index, element) {
                    let value = $(element).val();

                    subtotal += parseFloat(value);
                });

                total += parseFloat(subtotal).toFixed(2);

                let discount_amount = parseFloat(calculateDiscountAmount(total));
                total -= discount_amount;
                let tax_amount = parseFloat(calculateTaxAmount(total));
                let shipping_charge = parseFloat($('#shipping_charge').val());

                total += tax_amount;
                total += shipping_charge;

                $('#sub_total').text(parseFloat(subtotal).toFixed(2));
                $('#sub_total_input').val(parseFloat(subtotal).toFixed(2));
                $('#total').text(parseFloat(total).toFixed(2));
                $('#total_input').val(parseFloat(total).toFixed(2));
            }

            function calculateDiscountAmount(subtotal) {
                let discount_type = $('#discount_type').find(":selected").val();
                let discount_value = $('#discount_value').val() == "" ? 0.00 : $('#discount_value').val();
                let totalAmount = $('#total_input').val();
                let discount_amount = 0;
                discount_value = parseFloat(discount_value);
                totalAmount = parseFloat(totalAmount);

                if (discount_type == 1) {
                    if (discount_value > 100) {
                        return discount_amount;
                    }
                    discount_amount = (discount_value / 100) * subtotal;
                }
                if (discount_type == 2) {
                    if (discount_value > totalAmount) {
                        return discount_amount;
                    }
                    discount_amount = discount_value;
                }

                $('#discount_amount').text(parseFloat(discount_amount).toFixed(2));
                $('#discount_amount_input').val(parseFloat(discount_amount).toFixed(2));

                return parseFloat(discount_amount).toFixed(2);
            }

            function calculateTaxAmount(subtotal) {
                let tax_type = $('#tax_type').find(":selected").val();
                let tax_value = $('#tax_value').val() == "" ? 0.00 : $('#tax_value').val();

                let tax_amount = 0;


                if (tax_type == 1) {
                    tax_amount = (tax_value / 100) * subtotal;
                }
                if (tax_type == 2) {
                    tax_amount = tax_value;
                }
                $('#tax').text(parseFloat(tax_amount).toFixed(2));
                $('#tax_amount_input').val(parseFloat(tax_amount).toFixed(2));

                return parseFloat(tax_amount).toFixed(2);
            }


            $(document).on("click", ".deleteProduct", function () {

                let id = $(this).attr("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Deleted!',
                            text: 'Product remove from cart list',
                            customClass: {
                                confirmButton: 'btn btn-success waves-effect waves-light'
                            }
                        });
                        $(`#cartRow${id}`).remove();
                        calculateTotalPrices();
                    }
                });
            });


            let variation = [];
            let attribute = [];
            let attribute_values = [];
            let price = 0;
            let product = {};
            let countRow = -1;
            let totalItems = {{ count($order->items) }} + 1;


            $('#modalProductSelect').on('change', function () {
                let product_id = $(this).val();
                attribute = [];
                attribute_values = [];
                price = 0;
                $('#priceWrapper').hide();
                $('#modalPrice').text('');
                $('#addItemSubmitButton').prop('disabled', true);
                if (product_id != '') {
                    $.ajax({
                        url: '/order/product/' + product_id,
                        method: 'get',
                        success: function (response) {
                            product = response;

                            variation = response.variants ?? [];
                            let s = '';
                            $(response.choice_options).each(function (index, value) {
                                s +=
                                    `
                                    <div class="row mb-1">
                                        <div class="col-2">${value.name}:</div>
                                        <div class="col-8">`;
                                $(value.pivot.value).each(function (row, data) {
                                    s +=
                                        `<span class="badge bg-label-secondary me-1 cursor-pointer attribute_value attribute${value.name}" data-attribute = "${value.name}" data-value = "${data}" data-index="${index}" >${data}</span>`;

                                });

                                s += `</div>
                                    </div>
                                `;
                            });

                            if (response.choice_options.length == 0) {
                                $('#priceWrapper').show();
                                price = response.discount_price ?? response.unit_price;
                                $('#modalPrice').text(price);
                                $('#addItemSubmitButton').prop('disabled', false);
                            }

                            $('#variantsWrapper').empty();
                            $('#variantsWrapper').append(s);
                        },
                        error: function (error) {
                            console.log(error.responseJSON.message);
                            // handle the error case
                        }
                    });
                }
            })


            $(document).on('click', '.attribute_value', function () {
                attribute_values[$(this).data('index')] = $(this).data('value');
                attribute[$(this).data('index')] = $(this).data('attribute');

                $(`.attribute${$(this).data('attribute')}`).addClass('bg-label-secondary');
                $(`.attribute${$(this).data('attribute')}`).removeClass('bg-primary');
                $(this).addClass('bg-primary');
                $(this).removeClass('bg-label-secondary');

                matchVariant();
            });

            $('#addItemButton').on('click', function () {

                $('#variantsWrapper').empty();
                $('#priceWrapper').hide();
                attribute_values = [];

            })

            function matchVariant() {
                let variationString = '';
                $(attribute_values).each(function (index, value) {
                    variationString += `${value}-`;
                });
                variationString = variationString.substring(0, variationString.length - 1);

                $(variation).each(function (index, value) {
                    if (value.variant == variationString) {
                        $('#priceWrapper').show();
                        $('#modalPrice').text(value.price);
                        price = value.price;
                        $('#addItemSubmitButton').prop('disabled', false);
                    }
                });
            }

            $('#addItemSubmitButton').on('click', function () {
                let s =

                    `
                <tr id="cartRow${countRow}">
                                            <td><i class="ti ti-trash text-danger cursor-pointer deleteProduct"
                                                    data-id="${countRow}"></i></td>

                                            <td>
                                                <img src="${product.thumbnail_img}"
                                                    class="me-2" height="50px" alt="">
                                                 ${product.name}
                                                <input type="number" value="${product.id}"
                                                    hidden
                                                    name="product[${totalItems}][product_id]">
                                            </td>
                                            <td>`;
                if (attribute.length > 0) {
                    $(attribute).each(function (index, value) {
                        s +=
                            `
                        <span><b class="me-1">${value}:</b><span
                                                                class="text-primary">${attribute_values[index]}</span></span><br>
                                                        <input type="text" value="${value}" hidden
                                                            name="product[${totalItems}][variant][${index}][attribute]">
                                                        <input type="text" value="${attribute_values[index]}" hidden
                                                            name="product[${totalItems}][variant][${index}][value]">

                        `;
                    });

                } else {
                    s += '';
                }


                s += `</td>
                                            <td>

                                                <input class="form-control w-100 price"
                                                    id="product-${totalItems}-price"
                                                    data-product_id="${totalItems}"
                                                    type="number"
                                                    name="product[${totalItems}][price]"
                                                    value="${price}" />
                                            </td>

                                            </td>
                                            <td>
                                                <input class="form-control w-100 quantity" type="number"
                                                    data-product_id="${totalItems}"
                                                    id="product-${totalItems}-quantity"
                                                    name="product[${totalItems}][quantity]"
                                                    value="1" min="1" />
                                            </td>
                                            <td>
                                                $
                                                <span
                                                    id="product-${totalItems}-total_price-view">${price}</span>
                                                <input class="total_price_of_product" type="text" hidden
                                                    id="product-${totalItems}-total_price"
                                                    value="${price}">
                                            </td>
                                        </tr>

                `;

                $('#orderItemWrapper').append(s);
                countRow--;
                totalItems++;
                attribute = [];
                attribute_values = [];
                calculateTotalPrices();
                $('#modalClose').click();

            });

        });
    </script>
@endpush
