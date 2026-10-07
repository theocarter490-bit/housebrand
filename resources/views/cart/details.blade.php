@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Cart Details'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Cart Details'), [
            '#' => _trans('keyword.Order') . ' ' . _trans('keyword.Management'),
            '##' => _trans('keyword.Customer') . ' ' . _trans('keyword.Order'),
            'cart' => _trans('keyword.Cart Details'),
        ]) !!}

        <!-- Order Details Table -->
        <form action="{{ route('cart.store') }}" method="POST">
            @csrf
            <div class="row">

                <div class="col-12 col-xl-8">
                    <div class="card mb-4">
                        <div class="card-datatable table-responsive">
                            <table class="data-table table border-top">
                                <thead>
                                <tr>
                                    <th class="col-1">{{_trans('keyword.Action')}}</th>
                                    <th class="col-3">{{ _trans('keyword.Name') }}</th>
                                    <th class="col-2">{{ _trans('keyword.Variation') }}</th>
                                    <th class="col-2">{{ _trans('keyword.Price') }}</th>
                                    <th class="col-1">{{ _trans('keyword.qty') }}</th>
                                    <th class="col-1">{{ _trans('keyword.Total') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach ($cart_items as $cart_item)
                                    <tr id="cartRow{{ $cart_item->id }}">
                                        <td><i class="ti ti-trash text-danger cursor-pointer deleteProduct"
                                               data-id="{{ $cart_item->id }}"></i></td>

                                        <td>
                                            <div class="d-flex align-items-center h-100">
                                                <img
                                                    src="{{ getFilePath(optional($cart_item->product)->thumbnail_img) }}"
                                                    class="me-2" height="50px" alt="">
                                                @if($cart_item->product->relationLoaded('shop'))
                                                    <a href="{{env('APP_FRONTEND_URL').'/designer/'.@$cart_item->product->shop->slug.'/product/'.$cart_item->product->id.'-' .@$cart_item->product->slug }}"
                                                       target="_blank">{{ optional($cart_item->product)->name }}</a>
                                                @else
                                                    <a href="{{env('APP_FRONTEND_URL').'/product/'.$cart_item->product->id.'-' .@$cart_item->product->slug}}"
                                                       target="_blank">{{ optional($cart_item->product)->name }}</a>
                                                @endif
                                                <input type="number" value="{{ $cart_item->product_id }}" hidden
                                                       name="product[{{ $cart_item->id }}][product_id]">
                                            </div>

                                        </td>
                                        <td>

                                            @foreach ($cart_item->variation as $key => $item)
                                                <span><b class="me-1">{{ @$item['attribute'] }}:</b><span
                                                        class="text-primary">{{ @$item['value'] }}</span></span><br>
                                                <input type="text" value="{{ @$item['attribute'] }}" hidden
                                                       name="product[{{ $cart_item->id }}][variant][{{ $key }}][attribute]">
                                                <input type="text" value="{{ @$item['value'] }}" hidden
                                                       name="product[{{ $cart_item->id }}][variant][{{ $key }}][value]">
                                            @endforeach

                                        </td>
                                        <td style="min-width: 150px">
                                            {{-- <span>{{ getPriceFormat($cart_item->price) }}</span> --}}
                                            <input class="form-control w-100 price"
                                                   data-product_id="{{ $cart_item->id }}"
                                                   id="product-{{ $cart_item->id }}-price" type="number"
                                                   name="product[{{ $cart_item->id }}][price]"
                                                   value="{{ $cart_item->price }}"/>
                                        </td>

                                        <td style="min-width: 150px">
                                            <input class="form-control w-100 quantity" type="number"
                                                   data-product_id="{{ $cart_item->id }}"
                                                   name="product[{{ $cart_item->id }}][quantity]"
                                                   id="product-{{ $cart_item->id }}-quantity"
                                                   value="{{ $cart_item->quantity }}" min="1"/>
                                        </td>
                                        <td >
                                          <div class="d-flex align-items-center gap-1">
                                              {{ getCurrency() }}
                                              <span
                                                  id="product-{{ $cart_item->id }}-total_price-view">{{ number_format($cart_item->price * $cart_item->quantity, 2) }}</span>
                                              <input class="total_price_of_product" type="text" hidden
                                                     id="product-{{ $cart_item->id }}-total_price"
                                                     value="{{ $cart_item->price * $cart_item->quantity }}">
                                          </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @error('product')
                            <p class="text-danger error ms-2">{{ $message }}</p>
                            @enderror
                            <div class="d-flex flex-column justify-content-end align-items-end m-3 mb-2 p-1">

                                <div class="d-flex justify-content-between mb-2 w-px-300">
                                    <span class="text-heading"><strong>{{ _trans('keyword.Subtotal') }}:</strong></span>
                                    <h6 class="mb-0"><strong>{{ getCurrency() }}<span id="sub_total">0</span> </strong>
                                    </h6>
                                    <input type="text" name="sub_total" hidden id="sub_total_input" value="0">
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="d-flex me-4 w-px-300">
                                        <select name="discount_type" class="form-select me-2 charges" id="discount_type"
                                                data-placeholder="Discount type">
                                            <option selected value="0">{{ _trans('keyword.Discount Type') }}</option>
                                            <option value="1">{{ _trans('keyword.Percentage') }}</option>
                                            <option value="2">{{ _trans('keyword.Fixed Amount') }}</option>
                                        </select>
                                        <input type="number" class="form-control w-50 charges" name="discount_value"
                                               id="discount_value" step="0.01" min="0.00" value="0.00">


                                    </div>
                                    <div class="w-px-300 justify-content-between d-flex">
                                        <span class="text-heading">{{ _trans('keyword.Discount') }}:</span>
                                        <h6 class="mb-0">{{ getCurrency() }}<span id="discount_amount">0</span></h6>
                                        <input type="text" name="discount_amount" hidden id="discount_amount_input"
                                               value="0">

                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="d-flex me-4 w-px-300">
                                        <select name="tax_type" id="tax_type" class="form-select me-2 charges"
                                                data-placeholder="Tax type">
                                            <option selected value="0">{{ _trans('keyword.Tax Type') }}</option>
                                            <option value="1">{{ _trans('keyword.Percentage') }}</option>
                                            <option value="2">{{ _trans('keyword.Fixed Amount') }}</option>
                                        </select>
                                        <input type="number" class="form-control w-50 charges" name="tax_value"
                                               id="tax_value" min="0.0" step="0.01" value="0.00">

                                    </div>

                                    <div class="w-px-300 justify-content-between d-flex">
                                        <span class="text-heading">{{ _trans('keyword.Tax') }}:</span>
                                        <h6 class="mb-0">{{ getCurrency() }}<span id="tax">0</span></h6>
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
                                                                  step="0.01"
                                                                  style="width: 100px" min="0.0" value="0.00"/>

                                    </div>
                                </div>
                                <br>
                                <div class="d-flex justify-content-between w-px-300 border-top pt-2">
                                    <h4 class=" mb-0">{{ _trans('keyword.Total') }}:</h4>
                                    <h4 class="mb-0">{{ getCurrency() }} <span id="total">0</span></h4>
                                    <input type="text" name="total" hidden id="total_input" value="0">

                                </div>

                            </div>
                        </div>
                    </div>
                    @if ((isSeller() || auth()->user()->supervisor_id != null) && hasPermission('customer_cart_list_update'))
                        <div class="col-12 d-flex justify-content-center">
                            <button class="btn btn-primary">{{ _trans('keyword.Place Order') }}
                                <span class="loader"></span>
                            </button>
                        </div>
                    @endif

                </div>
                <div class="col-12 col-xl-4 ">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title m-0"><strong>{{ _trans('keyword.Customer Details') }}</strong></h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex justify-content-start align-items-center ">
                                    <div class="avatar me-2">
                                        <img src="{{getFilePath(userInfo($user)->avatar)}}" alt="Avatar"
                                             class="rounded-circle"/>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <div class="text-body text-nowrap">
                                            <h6 class="mb-0">{{ userInfo($user)->name }}</h6>
                                        </div>
                                        <small class="text-muted">{{ _trans('keyword.Member Since') }}:
                                            {{ dateFormat(userInfo($user)->created_at) }}</small>
                                        <input type="number" hidden value="{{ $user->id }}" name="user_id">
                                    </div>
                                </div>

                                <div class="p-2 bg-label-primary rounded">
                                    <a href="{{route('live-chat.index',['directChatUser' => $user->id])}}"><i
                                            class="ti ti-brand-hipchat text-primary"></i></a>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6><strong>{{ _trans('keyword.Contact') . ' ' . _trans('keyword.Info') }}</strong></h6>
                            </div>
                            <p class="mb-1">{{ _trans('keyword.Email') }}: {{ userInfo($user)->email }}</p>
                            <p class="mb-0">{{ _trans('keyword.Phone') }}: {{ userInfo($user)->phone }}</p>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between">
                            <h6 class="card-title m-0">{{ _trans('keyword.Shipping Address') }}</h6>
                            <h6 class="m-0">
                                <a href=" javascript:void(0)" data-bs-toggle="modal"
                                   data-bs-target="#addNewAddress">{{ _trans('keyword.Add') }}</a>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row" id="shipping-address-list">
                                @foreach ($shipping_addresses as $shipping_address)
                                    <div class="col-xl-6 mb-2">
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
                                                    <small>{{ _trans('keyword.State') }}:
                                                        {{ $shipping_address->state }}</small><br>
                                                    <small>{{ _trans('keyword.Zip Code') }}:
                                                        {{ $shipping_address->zip_code }}</small><br>
                                                    <small>{{ _trans('keyword.Country') }}:
                                                        {{ $shipping_address->country }}</small><br>
                                                </span>
                                                <input name="shippingAddressId" class="form-check-input" type="radio"
                                                       value="{{ $shipping_address->id }}"
                                                       id="shippingAddress{{ $shipping_address->id }}" checked/>
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
                                      rows="5"></textarea>
                        </div>
                    </div>
                </div>

            </div>
        </form>


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
                            <input type="number" hidden value="{{ $user->id }}" name="userID">

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="fullName">{{ _trans('keyword.Full Name') }} <span
                                        class="text-danger">*</span></label>
                                <input required type="text" value="{{ $user->name }}" id="fullName" name="fullName"
                                       class="form-control"
                                       placeholder="John"/>
                                <span class="text-danger error" id="fullNameError"></span>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="phone">{{ _trans('keyword.Phone') }} <span
                                        class="text-danger">*</span></label>
                                <input required type="text" value="{{ $user->phone }}" id="phone" name="phone"
                                       class="form-control"
                                       placeholder="123456789"/>
                                <span class="text-danger error" id="phoneError"></span>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="email">{{ _trans('keyword.Email') }} <span
                                        class="text-danger">*</span></label>
                                <input type="email" required value="{{ $user->email }}" id="email" name="email"
                                       class="form-control"
                                       placeholder="example@gmail.com"/>
                                <span class="text-danger error" id="emailError"></span>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="street">{{ _trans('keyword.Street Address') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" required id="street" name="street" class="form-control"
                                       placeholder="12, Business Park"/>
                                <span class="text-danger error" id="streetError"></span>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="state">{{ _trans('keyword.State') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" required id="state" name="state" class="form-control"
                                       placeholder="California"/>
                                <span class="text-danger error" id="stateError"></span>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="zipCode">{{ _trans('keyword.Zip Code') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" required id="zipCode" name="zipCode" class="form-control"
                                       placeholder="99950"/>
                                <span class="text-danger error" id="zipCodeError"></span>
                            </div>

                            <div class="col-12">
                                <label class="form-label"
                                       for="modalAddressZipCountry">{{ _trans('keyword.Country') }}</label>
                                <select id="country" name="country" class="select2 form-select"
                                        style="width: 100%"
                                        data-placeholder="Select Country" required>
                                    <option
                                        value="">{{_trans('keyword.Select').' '._trans('keyword.Brand')}}</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 text-center">
                                <button type="button" id="submitAddressForm"
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

        <!--/ Edit User Modal -->

        <!--/ Add New Address Modal -->
    </div>

@endsection


@push('scripts')
    <script>

        $(function () {
            @foreach($errors->all() as $er)
            toastr.error("{{$er}}");
            @endforeach

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
                        toastr.success('Product remove from cart list');
                        $(`#cartRow${id}`).remove();
                        calculateTotalPrices();
                    }
                });
            });

            $('#submitAddressForm').click(function (e) {
                e.preventDefault();

                var formData = new FormData();
                formData.append('userID', $('input[name="userID"]').val());
                formData.append('fullName', $('#fullName').val());
                formData.append('phone', $('#phone').val());
                formData.append('email', $('#email').val());
                formData.append('street', $('#street').val());
                formData.append('state', $('#state').val());
                formData.append('zipCode', $('#zipCode').val());
                formData.append('country', $('#country').val());
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

                            $('#shipping-address-list').load(location.href + ' #shipping-address-list > *');
                            toastr.success(response.message);
                            clearField();
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) { // Validation error
                            var errors = xhr.responseJSON.errors;

                            // Clear previous error messages
                            $('.error').text('');

                            // Loop through errors and display them in the respective span
                            if (errors.fullName) {
                                $('#fullNameError').text(errors.fullName[0]);
                            }
                            if (errors.phone) {
                                $('#phoneError').text(errors.phone[0]);
                            }
                            if (errors.email) {
                                $('#emailError').text(errors.email[0]);
                            }
                            if (errors.street) {
                                $('#streetError').text(errors.street[0]);
                            }
                            if (errors.state) {
                                $('#stateError').text(errors.state[0]);
                            }
                            if (errors.zipCode) {
                                $('#zipCodeError').text(errors.zipCode[0]);
                            }
                            if (errors.country) {
                                $('#countryError').text(errors.country[0]);
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
            });

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
    </script>
@endpush
