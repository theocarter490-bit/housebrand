@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Create Order'))

@section('content')

    {!! breadcrumb(_trans('keyword.Create Order'), [
        '#' => _trans('keyword.Order') . ' ' . _trans('keyword.Management'),
        '##' => _trans('keyword.Customer') . ' ' . _trans('keyword.Order'),
        'order' => _trans('keyword.Create Order'),
    ]) !!}
    <form action="{{ route('order.store') }}" method="POST">
        <div class="row invoice-preview">
            <div class="col-lg-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div>

                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div
                                    class="card-header d-flex flex-sm-row flex-column justify-content-between align-items-center gap-2">
                                    <h5 class="card-title m-0 text-nowrap">{{ _trans('keyword.Order Items') }}</h5>
                                    <div class="d-flex flex-sm-row flex-column gap-3 ">
                                        <button type="button" id="addItemButton" class="btn btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#addItem"><i
                                                class="ti ti-plus ti-xs me-0 me-sm-2"></i>{{ _trans('keyword.Add Item') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="card-datatable table-responsive">
                                    <table class="data-table table border-top">
                                        <thead>
                                        <tr>
                                            <th class="col-1">#</th>
                                            <th class="col-3">{{ _trans('keyword.Name') }}</th>
                                            <th class="col-1">{{ _trans('keyword.Variation') }}</th>
                                            <th class="col-1">{{ _trans('keyword.Price') }}</th>
                                            <th class="col-1">{{ _trans('keyword.qty') }}</th>
                                            <th class="col-1">{{ _trans('keyword.Total') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody id="orderItemWrapper">


                                        </tbody>
                                    </table>
                                    <div
                                        class="d-flex flex-column justify-content-md-end align-items-md-end m-3 mb-2 p-1">
                                        <div class="d-flex justify-content-between mb-2 w-px-300">
                                                <span
                                                    class="text-heading"><strong>{{ _trans('keyword.Subtotal') }}:</strong></span>
                                            <h6 class="mb-0"><strong>{{ getCurrency() }} <span
                                                        id="sub_total"></span>
                                                </strong></h6>
                                            <input type="text" name="sub_total" hidden id="sub_total_input"
                                                   value="0">
                                        </div>
                                        <div class="d-flex flex-md-row flex-column align-items-md-center  mb-2">
                                            <div class="d-flex me-4 w-px-300">
                                                <select name="discount_type" class="form-select me-2 charges"
                                                        id="discount_type" data-placeholder="Discount type">
                                                    <option selected value="0">{{ _trans('keyword.Discount Type') }}
                                                    </option>
                                                    <option value="1">
                                                        Percentage
                                                    </option>
                                                    <option value="2">
                                                        Fixed Amount
                                                    </option>
                                                </select>
                                                <input type="number" class="form-control w-50 charges"
                                                       name="discount_value" id="discount_value"
                                                       step="any"
                                                       min="0.00" value="0.00">
                                            </div>
                                            <div class="w-px-300 justify-content-between d-flex">
                                                <span class="text-heading">{{ _trans('keyword.Discount') }}:</span>
                                                <h6 class="mb-0">{{ getCurrency() }} <span
                                                        id="discount_amount">0</span></h6>
                                                <input type="number" name="discount_amount" hidden
                                                       id="discount_amount_input" step="any"
                                                       min="0.00" value="0.00">

                                            </div>
                                        </div>
                                        <div class="d-flex flex-md-row flex-column align-items-md-center mb-2">
                                            <div class="d-flex me-4 w-px-300">
                                                <select name="tax_type" id="tax_type"
                                                        class="form-select me-2 charges"
                                                        data-placeholder="Tax type">
                                                    <option selected value="0">{{ _trans('keyword.Tax Type') }}
                                                    </option>
                                                    <option value="1">
                                                        {{ _trans('keyword.Percentage') }}
                                                    </option>
                                                    <option value="2">
                                                        {{ _trans('keyword.Fixed Amount') }}
                                                    </option>
                                                </select>
                                                <input type="number"
                                                       class="form-control w-50 charges"
                                                       name="tax_value"
                                                       id="tax_value"
                                                       step="any"
                                                       min="0.00" value="0.00"/>

                                            </div>

                                            <div class="w-px-300 justify-content-between d-flex">
                                                <span class="text-heading">{{ _trans('keyword.Tax') }}:</span>
                                                <h6 class="mb-0">{{ getCurrency() }} <span id="tax">0</span>
                                                </h6>
                                                <input type="text" name="tax_amount" hidden id="tax_amount_input"
                                                       value="0">

                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 w-px-300">
                                            <span class="text-heading">{{ _trans('keyword.Shipping Charge') }}:</span>
                                            <div class="d-flex align-items-center">
                                                {{ getCurrency() }}<input type="number" id="shipping_charge"
                                                                          name="shipping_charge"
                                                                          class="ms-1 form-control p-1 text-end charges"
                                                                          style="width: 100px"
                                                                          step="any"
                                                                          min="0.00" value="0.00"/>

                                            </div>
                                        </div>
                                        <br>
                                        <div class="d-flex justify-content-between w-px-300 border-top pt-2">
                                            <h4 class=" mb-0">Total:</h4>
                                            <h4 class="mb-0">{{ getCurrency() }} <span id="total">0</span></h4>
                                            <input type="text" name="total" hidden id="total_input"
                                                   value="0">

                                        </div>

                                    </div>
                                </div>
                                @if(isSeller() && hasPermission('customer_order_create'))
                                    <div class="col-12 d-flex justify-content-center mb-3">
                                        <button id="create_btn"
                                                class="btn btn-primary">{{ _trans('keyword.Create') }}
                                            <span class="loader"></span>
                                        </button>
                                    </div>
                                @endif
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
                        <select name="user_id" required class="form-select me-2 " data-placeholder="Select User"
                                id="selected_user">
                            <option value="">Select A Customer
                            </option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <div class="avatar me-2">
                                <img src="../../assets/img/avatars/1.png" id="user_avatar" alt="Avatar"
                                     class="rounded-circle"/>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-body text-nowrap">
                                    <strong class="mb-0 ms-2" id="user_name"></strong>
                                </a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <h6><strong>Contact</strong></h6>
                        </div>
                        <p class="mb-1">Email: <span id="user_email"></span></p>
                        <p class="mb-0">Phone: <span id="user_phone"></span></p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title m-0">{{ _trans('keyword.Shipping Address') }}</h6>
                        <h6 class="m-0">
                            <button id="addBtn" disabled type="button" data-bs-toggle="modal"
                                    data-bs-target="#addNewAddress" style="background: transparent; border: none"
                                    class="text-grey">{{ _trans('keyword.Add') }}</button>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row" id="shippingAddressWrapper">


                        </div>
                        {{--                            @error('shippingAddressId')--}}
                        <span class="text-danger shippingAddressError error"></span>
                        {{--                            @enderror--}}
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title m-0">{{ _trans('keyword.Note') }}</h6>
                    </div>
                    <div class="card-body">
                            <textarea name="note" placeholder="Note" class="form-control" id="note" cols="30"
                                      rows="5"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{-- add product modal --}}
    <div class="modal fade" id="addItem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-edit-user">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">{{ _trans('keyword.Add New Item') }}</h3>
                        <p id="checkCustomerSelectedErrorMessage"
                           class="text-white bg-danger fs-bold rounded p-1 checkCustomerSelectedErrorMessage">
                            Please Select User First</p>
                    </div>
                    <form id="editUserForm" class="row g-3" onsubmit="return false">
                        <div class="col-12">
                            <label class="form-label"
                                   for="modalEditUserCountry">{{ _trans('keyword.Select Product') }}</label>
                            <select id="modalProductSelect" name="modalEditUserCountry" class="select2 form-select"
                                    data-allow-clear="true">
                                <option value="">{{ _trans('keyword.Select Product') }}</option>
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
                                    class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Submit') }}
                                <span class="loader"></span>
                            </button>
                            <button id="modalClose" type="reset" class="btn btn-label-secondary"
                                    data-bs-dismiss="modal" aria-label="Close">
                                {{ _trans('keyword.Clear') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addNewAddress" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="address-title mb-2">{{ _trans('keyword.Add New Address') }}</h3>
                        <p class="text-muted address-subtitle">{{ _trans('keyword.Add new address for Shipping') }}</p>
                    </div>
                    <form id="addNewAddressForm" class="row g-3">
                        <input type="number" id="modalUserID" required hidden value="" name="modalUserID">
                        <div class="col-12 col-md-6">
                            <label class="form-label"
                                   for="modalAddressFirstName">{{ _trans('keyword.Full Name') }} <span
                                    class="text-danger">*</span></label>
                            <input required type="text" id="modalAddressFirstName" name="modalAddressFullName"
                                   class="form-control" placeholder="John"/>
                            <span class="text-danger fullNameError error"></span>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label"
                                   for="modalAddressLastName">{{ _trans('keyword.Phone') }} <span
                                    class="text-danger">*</span></label>
                            <input required type="text" id="modalAddressLastName" name="modalAddressPhone"
                                   class="form-control" placeholder="Doe"/>
                            <span class="text-danger phoneError error"></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="modalAddressEmail">{{ _trans('keyword.Email') }}<span
                                    class="text-danger">*</span></label>
                            <input type="email" required id="modalAddressEmail" name="modalAddressEmail"
                                   class="form-control" placeholder="Doe"/>
                            <span class="text-danger emailError error"></span>

                        </div>
                        <div class="col-12">
                            <label class="form-label"
                                   for="modalAddressAddress1">{{ _trans('keyword.Street Address') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" required id="modalAddressAddress1" name="modalAddressStreet"
                                   class="form-control" placeholder="12, Business Park"/>
                            <span class="text-danger streetError error"></span>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label"
                                   for="modalAddressLandmark">{{ _trans('keyword.State') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" required id="modalAddressState" name="modalAddressState"
                                   class="form-control" placeholder="California"/>
                            <span class="text-danger stateError error"></span>

                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label"
                                   for="modalAddressZipCode">{{ _trans('keyword.Zip Code') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" required id="modalAddressZipCode" name="modalAddressZipCode"
                                   class="form-control" placeholder="99950"/>
                            <span class="text-danger zipCodeError error"></span>

                        </div>
                        <div class="col-12">
                            <label class="form-label"
                                   for="modalAddressZipCountry">{{ _trans('keyword.Country') }}<span
                                    class="text-danger">*</span></label>
                            <select id="modalAddressZipCountry" name="modalAddressCountry" class="select2 form-select"
                                    style="width: 100%"
                                    data-placeholder="Select Country" required>
                                <option
                                    value="">{{_trans('keyword.Select').' '._trans('keyword.Brand')}}</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->name }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger countryError error"></span>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" id="submitAddressForm"
                                    class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Submit') }}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">{{ _trans('keyword.Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        $(function () {

            calculateTotalPrices();
            $('#modalClose').on('click', function () {
                $('#modalProductSelect').val('').trigger('change'); // Clear the selection and refresh the select2 UI if applied
            });

            $('#modalCloseRequestedProduct').on('click', function () {
                $('#modalRequestedProductSelect').val('').trigger('change'); // Clear the selection and refresh the select2 UI if applied
            });

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

                let discount_amount = 0;

                if (discount_type == 1) {
                    discount_amount = (discount_value / 100) * subtotal;
                }
                if (discount_type == 2) {
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
                            icon: 'success',
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
            let totalItems = 0;
            let product_request_id = null;
            $('#priceWrapper').hide();


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
                        price = (value.discount_price != null && value.discount_price != '') ? value.discount_price : value.price;
                        $('#modalPrice').text(price);

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
                    s += '---'
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
                product = {};
                price = 0;
                calculateTotalPrices();
                $('#modalClose').click();

            });


            let users = {!! json_encode($users) !!};

            $('#selected_user').on('change', function () {

                let user_id = $(this).val();

                if (user_id) {
                    $('#addBtn').prop('disabled', false)
                        .addClass('text-primary');
                } else {
                    $('#addBtn').prop('disabled', true)
                        .removeClass('text-primary')
                        .css('color', 'grey');
                }

                $('#orderItemWrapper').empty();

                $('#user_name').text('');
                $('#user_email').text('');
                $('#user_phone').text('');
                $('#user_avatar').attr("src", ``);
                $('#modalUserID').val('');
                $('.checkCustomerSelectedErrorMessage').show();
                $('#shippingAddressWrapper').empty();
                $('#modalRequestedProductSelect').empty();
                $(users).each(function (index, user) {

                    if (user.id == user_id) {
                        $('#user_name').text(user.name);
                        $('#user_email').text(user.email);
                        $('#user_phone').text(user.phone);
                        $('#modalUserID').val(user.id);
                        $('#user_avatar').attr("src", `${user.avatar}`);

                        if (user.shipping_address.length === 0) {
                            $('.shippingAddressError')
                                .text('Shipping address is required.')
                                .show();

                            $('#create_btn').prop('disabled', true);
                        } else {
                            $('#create_btn').prop('disabled', false);
                        }
                        shippingAddress(user.shipping_address);
                        $('.checkCustomerSelectedErrorMessage').hide();
                        return false;
                    }
                });
            });

            function shippingAddress(address) {
                let s = '';
                $(address).each(function (index, value) {

                    s += `<div class="col-6 mb-2">
                                            <div class="form-check custom-option custom-option-icon">
                                                <label class="form-check-label custom-option-content"
                                                    for="shippingAddress${value.id}">
                                                    <span class="custom-option-body">

                                                        <small>Name:
                                                        ${value.name}</small>
                                                        <br>
                                                        <small>Email:
                                                           ${value.email}</small><br>
                                                        <small>Phone:
                                                           ${value.phone} </small><br>
                                                        <small>Street:
                                                          ${value.street_address}  </small><br>
                                                        <small>State:
                                                            ${value.state}</small><br>
                                                        <small>Zip Code:
                                                           ${value.zip_code} </small><br>
                                                        <small>Country:
                                                           ${value.country}</small><br>
                                                    </span>
                                                    <input name="shippingAddressId" class="form-check-input" type="radio"
                                                        value="${value.id}"
                                                        id="shippingAddress${value.id}" ${value.is_default == 1 ? 'checked' : ''} />
                                                </label>
                                            </div>
                                        </div>`;
                });

                $('#shippingAddressWrapper').append(s);

            }


            $('#submitAddressForm').click(function (e) {
                e.preventDefault();

                var formData = new FormData();
                formData.append('userID', $('#modalUserID').val());
                formData.append('fullName', $('#modalAddressFirstName').val());
                formData.append('phone', $('#modalAddressLastName').val());
                formData.append('email', $('#modalAddressEmail').val());
                formData.append('street', $('#modalAddressAddress1').val());
                formData.append('state', $('#modalAddressState').val());
                formData.append('zipCode', $('#modalAddressZipCode').val());
                formData.append('country', $('#modalAddressZipCountry').val());
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                $('#submitAddressForm').prop('disabled', true);

                $.ajax({
                    url: '{{route('shippingAddress.store')}}',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 200) {
                            $('#addNewAddress').modal('hide');
                            shippingAddress(response.data);
                            toastr.success(response.message);
                            clearField();
                            $('#create_btn').prop('disabled', false);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) { // Validation error
                            var errors = xhr.responseJSON.errors;
                            console.log(errors);
                            // Clear previous error messages
                            $('.error').text('');

                            // Loop through errors and display them in the respective span
                            if (errors.fullName) {
                                $('.fullNameError').text(errors.fullName[0]);
                            }
                            if (errors.phone) {
                                $('.phoneError').text(errors.phone[0]);
                            }
                            if (errors.email) {
                                $('.emailError').text(errors.email[0]);
                            }
                            if (errors.street) {
                                $('.streetError').text(errors.street[0]);
                            }
                            if (errors.state) {
                                $('.stateError').text(errors.state[0]);
                            }
                            if (errors.zipCode) {
                                $('.zipCodeError').text(errors.zipCode[0]);
                            }
                            if (errors.country) {
                                $('.countryError').text(errors.country[0]);
                            }
                        } else {
                            toastr.error('Failed to add address. Please try again later.');
                        }
                    },
                    complete: function () {
                        loader.hide();
                        $('#submitAddressForm').prop('disabled', false);
                    }
                });
            })

            function clearField() {
                $('#fullName').val('');
                $('#phone').val('');
                $('#email').val('');
                $('#street').val('');
                $('#state').val('');
                $('#zipCode').val('');
                $('#country').val('');

                // clear span error
                $('.error').text('');
            }


        });

        function resetUI() {
            $('#priceWrapperRequestedProduct').hide();
            $('#modalPriceRequestedProduct').text('');
            $('#variantsWrapperRequestedProduct').text('');
            $('#addItemSubmitButtonRequestedProduct').prop('disabled', true);
        }
    </script>
@endpush
