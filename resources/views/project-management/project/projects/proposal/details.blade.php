@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Proposal Details'))

@section('content')

    <div class="flex-grow-1 container-p-y pt-0">
        {!! breadcrumb(_trans('keyword.Proposal Details'), [
            '#' => _trans('keyword.Project') . ' ' . _trans('keyword.Management'),
            'Project' => _trans('keyword.Project'),
            'Assign Product' => _trans('keyword.Proposal Details'),
        ]) !!}

        @if ($errors->any())
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                    @endforeach
                });
            </script>
        @endif

        <div class="row invoice-preview">
            <div class="col-lg-12 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body row">
                        <div class="col-xl-8 col-md-8 ">
                            <h6 class="mb-3 fw-bold">{{_trans('keyword.Payment Info')}}</h6>
                            <table>
                                <tbody>
                                <tr>
                                    <td class="pe-4">{{ _trans('keyword.Total Amount') }}</td>
                                    <td>: {{ getPriceFormat($proposal->sub_total) }}</td>
                                </tr>
                                <tr>
                                    <td class="pe-4">{{ _trans('keyword.Discount') }} (<span
                                            class="text-danger">-</span>)
                                    </td>
                                    <td>: {{ getPriceFormat($proposal->discount_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="pe-4">{{ _trans('keyword.Tax') }}</td>
                                    <td>: {{ getPriceFormat($proposal->tax_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="pe-4">{{ _trans('keyword.Shipping Charges') }}</td>
                                    <td>: {{ getPriceFormat($proposal->shipping_charge) }}</td>
                                </tr>
                                <tr>
                                    <td class="pe-4">{{ _trans('keyword.Deposit Request') }}</td>
                                    <td>: {{ getPriceFormat($proposal->deposit_amount) }}</td>
                                </tr>
                                <tr>
                                    <td class="pe-4">{{ _trans('keyword.Total Amount') }}:</td>
                                    <td>: {{ getPriceFormat($proposal->total_amount) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xl-4 col-md-4 ">
                            <h6 class="mb fw-bold">{{ _trans('keyword.Proposal Info') }}</h6>
                            <table class="table table-borderless">
                                <tbody>
                                <tr>
                                    <td class="pe-4">{{ _trans('keyword.Code') }}:</td>
                                    <td class="fw-medium">: {{ $proposal->code }}</td>
                                </tr>
                                <tr>
                                    <td class="pe-4">{{ _trans('keyword.Proposal Date') }}:</td>
                                    <td>: {{ dateFormat($proposal->proposal_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="pe-4">{{ _trans('keyword.Due Date') }}:</td>
                                    <td>: {{ dateFormat($proposal->due_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="pe-4">{{_trans('keyword.Active Status')}}</td>
                                    <td class="pe-4">
                                        @if ($proposal->active_status == 1)
                                            : <span class="badge bg-label-success">Active</span>
                                        @else
                                            : <span class="badge bg-label-warning">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-4">{{_trans('keyword.Approve Status')}}</td>
                                    <td class="pe-4">
                                        @if ($proposal->is_approved == 1)
                                            : <span class="badge bg-label-success">Approved</span>
                                        @else
                                            : <span class="badge bg-label-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr class="m-0">
                    <form
                        action="{{ route('project-management.project.proposal.items.store',[$proposal->project_id,$proposal->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 ">
                                <div
                                    class="card-header d-flex flex-sm-row flex-column justify-content-between align-items-center gap-2 flex-wrap">
                                    <h5 class="card-title m-0 text-nowrap">{{ _trans('keyword.Proposal Items') }}</h5>
                                    <div class="d-flex flex-sm-row flex-column gap-3 ">
                                        <button type="button" id="addItemButton" class="btn btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#addItem"><i
                                                class="ti ti-plus ti-xs me-0 me-sm-2"></i>{{ _trans('keyword.Add Product') }}
                                        </button>
                                        <button class="btn btn-primary" data-bs-toggle="modal" type="button"
                                                id="addManufacturerItemButton" data-bs-target="#addManufacturerItem">
                                            <i class="ti ti-plus ti-xs me-0 me-sm-2"></i>
                                            Add Manufacturer Product
                                        </button>
                                        <button class="btn btn-primary" type="button" id="addServiceButton"
                                                data-bs-toggle="modal" data-bs-target="#addService">
                                            <i class="ti ti-plus ti-xs me-0 me-sm-2"></i>
                                            Add Service
                                        </button>
                                    </div>
                                </div>
                                <div class="card-datatable table-responsive">
                                    <table class="data-table table border-top">
                                        <thead>
                                        <tr>
                                            <th class="col-1">#</th>
                                            <th class="col-3">{{ _trans('keyword.Name') }}</th>
                                            <th class="col-1">{{ _trans('keyword.Type') }}</th>
                                            <th class="col-2">{{ _trans('keyword.Variation') }}</th>
                                            <th class="col-1">{{ _trans('keyword.Price') }}</th>
                                            <th class="col-1">{{ _trans('keyword.Mark Up') }}</th>
                                            <th class="col-1">{{ _trans('keyword.qty') }}</th>
                                            <th class="col-1">{{ _trans('keyword.Total') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody id="orderItemWrapper">


                                        @foreach ($proposal->items as $cart_item)
                                            {{-- @dd($cart_item) --}}
                                            <tr id="cartRow{{ $cart_item->id }}">
                                                <td><i class="ti ti-trash text-danger cursor-pointer deleteProduct"
                                                       data-id="{{ $cart_item->id }}"></i></td>

                                                <td class="d-flex align-items-center">
                                                    <img
                                                        src="{{ getFilePath(optional($cart_item->product)->thumbnail_img??optional($cart_item->service)->image) }}"
                                                        class="me-2" height="50px" alt="">
                                                    {{ $cart_item->type == 1? optional($cart_item->product)->name : optional($cart_item->service)->title}}
                                                    @if(isset($cart_item->product) && $cart_item->product->user_id !== getUserId())
                                                        <label
                                                            class="ms-2 badge bg-label-primary">{{optional($cart_item->product)->shop->shop_name}}</label>
                                                    @endif
                                                    <input type="number" value="{{ $cart_item->product_id }}" hidden
                                                           name="product[{{ $cart_item->id }}][product_id]">
                                                </td>
                                                <td>
                                                    @if($cart_item->type == 1)
                                                        <span class="badge bg-label-success ">Product</span>
                                                    @else
                                                        <span class="badge bg-label-primary ">Service</span>
                                                    @endif
                                                </td>
                                                <td>

                                                    @foreach ($cart_item->variation as $key => $item)
                                                        <span><b class="me-1">{{ @$item['attribute'] }}:</b><span
                                                                class="text-primary">{{ @$item['value'] }}</span></span>
                                                        <br>
                                                        <input type="text" value="{{ @$item['attribute'] }}" hidden
                                                               name="product[{{ $cart_item->id }}][variant][{{ $key }}][attribute]">
                                                        <input type="text" value="{{ @$item['value'] }}" hidden
                                                               name="product[{{ $cart_item->id }}][variant][{{ $key }}][value]">
                                                    @endforeach

                                                </td>
                                                <td>
                                                    {{-- <span>{{ getPriceFormat($cart_item->price) }}</span> --}}
                                                    <input class="form-control w-100 price"
                                                           data-product_id="{{ $cart_item->id }}"
                                                           id="product-{{ $cart_item->id }}-price" type="number"
                                                           name="product[{{ $cart_item->id }}][price]"
                                                           value="{{ $cart_item->unit_price }}"/>
                                                    <input type="number" value="{{$cart_item->type}}"
                                                           hidden
                                                           name="product[{{ $cart_item->id }}][type]">
                                                </td>
                                                <td class="text-center">

                                                    @if(isset($cart_item->product) && $cart_item->product->user_id !== getUserId())
                                                        <div class="input-group">
                                                            <input class="form-control markup" type="number"
                                                                   data-product_id="{{ $cart_item->id }}"
                                                                   name="product[{{ $cart_item->id }}][markup]"
                                                                   id="product-{{ $cart_item->id }}-markup"
                                                                   value="{{ $cart_item->markup }}" min="0"
                                                            />
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    @else
                                                        -------
                                                    @endif
                                                </td>

                                                <td>
                                                    <input class="form-control quantity" type="number"
                                                           data-product_id="{{ $cart_item->id }}"
                                                           name="product[{{ $cart_item->id }}][quantity]"
                                                           id="product-{{ $cart_item->id }}-quantity"
                                                           value="{{ $cart_item->quantity }}" min="1"/>
                                                </td>
                                                <td>
                                                    {{ getCurrency() }}
                                                    <span
                                                        id="product-{{ $cart_item->id }}-total_price-view">{{ number_format($cart_item->price, 2) }}</span>
                                                    <input class="total_price_of_product" type="text" hidden
                                                           id="product-{{ $cart_item->id }}-total_price"
                                                           value="{{ $cart_item->price }}">
                                                </td>
                                            </tr>
                                        @endforeach


                                        </tbody>
                                    </table>
                                    <div
                                        class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-end m-3 mb-2 p-1">
                                        <div class="d-flex flex-column align-items-center mb-4 mb-md-0 w-100" style="max-width: 300px">
                                            <img src="{{getFilePath($proposal->signature)}}" class="form-control mb-2"
                                                 width="100%" height="50px" alt="Signature">
                                            <input type="file" name="signature" class="form-control mb-1">
                                            <span>Signature</span>
                                        </div>
                                        <div
                                            class="d-flex flex-column justify-content-end align-items-center align-items-md-end m-3 mb-2 p-1 w-100">
                                            <div class="d-flex justify-content-between mb-2 w-100" style="max-width: 300px">
                                                <span
                                                    class="text-heading"><strong>{{ _trans('keyword.Subtotal') }}:</strong></span>
                                                <h6 class="mb-0"><strong>{{ getCurrency() }} <span
                                                            id="sub_total"></span>
                                                    </strong></h6>
                                                <input type="text" name="sub_total" hidden id="sub_total_input"
                                                       value="{{$proposal->sub_total}}">
                                            </div>
                                            <div class="d-flex flex-md-row flex-column align-items-center align-items-md-center mb-2 w-100 justify-content-end">
                                                <div class="d-flex me-md-4 me-0 mb-2 mb-md-0 w-100" style="max-width: 300px">
                                                    <select name="discount_type" class="form-select me-2 charges"
                                                            id="discount_type" data-placeholder="Discount type">
                                                        <option selected value="0">
                                                            {{ _trans('keyword.Discount Type') }}
                                                        </option>
                                                        <option
                                                            value="1" {{$proposal->discount_type == 1? "Selected":''}}>
                                                            Percentage
                                                        </option>
                                                        <option
                                                            value="2" {{$proposal->discount_type == 2? "Selected":''}}>
                                                            Fixed Amount
                                                        </option>
                                                    </select>
                                                    <input type="number" class="form-control w-50 charges"
                                                           name="discount_value" id="discount_value" min="0.0"
                                                           value="{{$proposal->discount_value}}">


                                                </div>
                                                <div class="w-100 justify-content-between d-flex" style="max-width: 300px">
                                                    <span class="text-heading">{{ _trans('keyword.Discount') }}:</span>
                                                    <h6 class="mb-0">{{ getCurrency() }} <span
                                                            id="discount_amount">0</span></h6>
                                                    <input type="text" name="discount_amount" hidden
                                                           id="discount_amount_input"
                                                           value="0">

                                                </div>
                                            </div>
                                            <div class="d-flex flex-md-row flex-column align-items-center align-items-md-center mb-2 w-100 justify-content-end">
                                                <div class="d-flex me-md-4 me-0 mb-2 mb-md-0 w-100" style="max-width: 300px">
                                                    <select name="tax_type" id="tax_type"
                                                            class="form-select me-2 charges"
                                                            data-placeholder="Tax type">
                                                        <option selected value="0">{{ _trans('keyword.Tax Type') }}
                                                        </option>
                                                        <option value="1" {{$proposal->tax_type == 1? "Selected":''}}>
                                                            {{ _trans('keyword.Percentage') }}
                                                        </option>
                                                        <option value="2" {{$proposal->tax_type == 2? "Selected":''}}>
                                                            {{ _trans('keyword.Fixed Amount') }}
                                                        </option>
                                                    </select>
                                                    <input type="number" class="form-control w-50 charges"
                                                           name="tax_value"
                                                           id="tax_value" min="0.0" value="{{$proposal->tax_value}}">

                                                </div>

                                                <div class="w-100 justify-content-between d-flex" style="max-width: 300px">
                                                    <span class="text-heading">{{ _trans('keyword.Tax') }}:</span>
                                                    <h6 class="mb-0">{{ getCurrency() }} <span id="tax">0</span>
                                                    </h6>
                                                    <input type="text" name="tax_amount" hidden id="tax_amount_input"
                                                           value="0">

                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 w-100" style="max-width: 300px">
                                                <span
                                                    class="text-heading">{{ _trans('keyword.Shipping Charge') }}:</span>
                                                <div class="d-flex align-items-center">
                                                    {{ getCurrency() }}<input type="number" id="shipping_charge"
                                                                              name="shipping_charge"
                                                                              class="ms-1 form-control p-1 text-end charges"
                                                                              style="width: 100px" min="0.0"
                                                                              value="{{$proposal->shipping_charge}}"/>

                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 w-100" style="max-width: 300px">
                                                <span
                                                    class="text-heading">{{ _trans('keyword.Deposit Request') }}:</span>
                                                <div class="d-flex align-items-center">
                                                    {{ getCurrency() }}<input type="number" id="deposite_request"
                                                                              name="deposite_request"
                                                                              class="ms-1 form-control p-1 text-end charges"
                                                                              style="width: 100px" min="0.0"
                                                                              value="{{$proposal->deposit_amount}}"/>

                                                </div>
                                            </div>
                                            <br>
                                            <div class="d-flex justify-content-between w-100 border-top pt-2" style="max-width: 300px">
                                                <h4 class=" mb-0">Total:</h4>
                                                <h4 class="mb-0">{{ getCurrency() }} <span id="total">0</span></h4>
                                                <input type="text" name="total" hidden id="total_input"
                                                       value="0">

                                            </div>
                                        </div>


                                    </div>
                                </div>
                                @if ($proposal->is_approved == 0)
                                    <div class="col-12 d-flex justify-content-center mb-3">
                                        <button id="create_btn" class="btn btn-primary">{{ _trans('keyword.Submit') }}
                                            <span class="loader"></span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- </div> --}}
                    </form>
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
        {{-- add Manufacturer product modal --}}
        <div class="modal fade" id="addManufacturerItem" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3 class="mb-2">{{ _trans('keyword.Add New Item') }}</h3>

                        </div>
                        <form id="editUserForm" class="row g-3" onsubmit="return false">
                            <div class="col-12">
                                <label class="form-label"
                                       for="modalEditUserCountry">{{ _trans('keyword.Select Product') }}</label>
                                <select id="modalManufacturerProductSelect" name="modalEditUserCountry"
                                        class="select2 form-select" data-allow-clear="true">
                                    <option value="">{{ _trans('keyword.Select Product') }}</option>
                                    @foreach ($manufactureProducts as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div id="variantsWrapperManufacturer" class="col-12">

                            </div>
                            <div class="col-12">
                                <h6 id="priceWrapperManufacturer"> price: {{ getCurrency() }} <span
                                        id="modalPriceManufacturer"></span>
                                </h6>
                            </div>


                            <div class="col-12 text-center">
                                <button type="button" id="addItemSubmitButtonManufacturer" disabled
                                        class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Submit') }}
                                    <span class="loader"></span>
                                </button>
                                <button id="modalCloseManufacturer" type="reset" class="btn btn-label-secondary"
                                        data-bs-dismiss="modal" aria-label="Close">
                                    {{ _trans('keyword.Clear') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- add service modal --}}
        <div class="modal fade" id="addService" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3 class="mb-2">{{ _trans('keyword.Add New Item') }}</h3>

                        </div>
                        <form id="editUserForm" class="row g-3" onsubmit="return false">
                            <div class="col-12">
                                <label class="form-label"
                                       for="modalEditUserCountry">{{ _trans('keyword.Select Service') }}</label>
                                <select id="modalServiceSelect" name="modalEditUserCountryService"
                                        class="select2 form-select" data-allow-clear="true">
                                    <option value="">{{ _trans('keyword.Select Service') }}</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->title }}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-12">
                                <h6 id="priceWrapperService"> price: {{ getCurrency() }} <span
                                        id="modalPriceService"></span></h6>
                            </div>


                            <div class="col-12 text-center">
                                <button type="button" id="addItemSubmitButtonService" disabled
                                        class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Submit') }}
                                    <span class="loader"></span>
                                </button>
                                <button id="modalCloseService" type="reset" class="btn btn-label-secondary"
                                        data-bs-dismiss="modal" aria-label="Close">
                                    {{ _trans('keyword.Clear') }}
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

            $(document).on('change', '.quantity, .price, .markup', function () {
                let product_id = $(this).attr('data-product_id');


                let quantity = parseFloat($(`#product-${product_id}-quantity`).val()) || 0;
                let price = parseFloat($(`#product-${product_id}-price`).val()) || 0;
                let markup = parseFloat($(`#product-${product_id}-markup`).val()) || 0;


                let markup_amount = price * (markup / 100);


                let total_price = (price + markup_amount) * quantity;


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
                            text: 'Product/Service removed from cart list',
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


            $(document).on('click', '.attribute_value', function () {
                attribute_values[$(this).data('index')] = $(this).data('value');
                attribute[$(this).data('index')] = $(this).data('attribute');

                $(`.attribute${$(this).data('attribute')}`).addClass('bg-label-secondary');
                $(`.attribute${$(this).data('attribute')}`).removeClass('bg-primary');
                $(this).addClass('bg-primary');
                $(this).removeClass('bg-label-secondary');

                matchVariant();
            });


            function matchVariant() {
                let variationString = '';
                $(attribute_values).each(function (index, value) {
                    variationString += `${value}-`;
                });
                variationString = variationString.substring(0, variationString.length - 1);

                $(variation).each(function (index, value) {
                    if (value.variant == variationString) {
                        $('#priceWrapper').show();
                        price = (value.discount_price != null && value.discount_price != '') ? value
                            .discount_price : value.price;
                        $('#modalPrice').text(price);

                        $('#addItemSubmitButton').prop('disabled', false);
                    }
                });
            }

            // Own product JS
            $('#modalClose').on('click', function () {
                $('#modalProductSelect').val('').trigger(
                    'change'); // Clear the selection and refresh the select2 UI if applied
            });
            $('#addItemButton').on('click', function () {

                $('#variantsWrapper').empty();
                $('#priceWrapper').hide();
                attribute_values = [];

            })
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
                                                    <input type="number" value="1"
                                                    hidden
                                                    name="product[${totalItems}][type]">
                                            </td>
                                        <td>

                                                            <span class="badge bg-label-success ">Product</span>

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
                                             <td class="text-center">
                                                 -------
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


            //Manufacturer JS

            $('#addManufacturerItemButton').on('click', function () {

                $('#variantsWrapperManufacturer').empty();
                $('#priceWrapperManufacturer').hide();
                attribute_values = [];

            })
            $('#modalManufacturerProductSelect').on('change', function () {
                let product_id = $(this).val();
                attribute = [];
                attribute_values = [];
                price = 0;
                $('#priceWrapperManufacturer').hide();
                $('#modalPriceManufacturer').text('');
                $('#addItemSubmitButtonManufacturer').prop('disabled', true);
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
                                        `<span class="badge bg-label-secondary me-1 cursor-pointer attribute_value_Manufacturer attribute${value.name}" data-attribute = "${value.name}" data-value = "${data}" data-index="${index}" >${data}</span>`;

                                });

                                s += `</div>
                                    </div>
                                `;
                            });

                            if (response.choice_options.length == 0) {
                                $('#priceWrapperManufacturer').show();
                                price = response.discount_price ?? response.unit_price;
                                $('#modalPriceManufacturer').text(price);
                                $('#addItemSubmitButtonManufacturer').prop('disabled', false);
                            }

                            $('#variantsWrapperManufacturer').empty();
                            $('#variantsWrapperManufacturer').append(s);
                        },
                        error: function (error) {
                            console.log(error.responseJSON.message);
                            // handle the error case
                        }
                    });
                }
            })

            $('#addItemSubmitButtonManufacturer').on('click', function () {


                $('#variantsWrapperManufacturer').empty();

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

                    <label
                        class="ms-2 badge bg-label-primary">${product.shop.shop_name}</label>

                                                   <input type="number" value="1"
                                                    hidden
                                                    name="product[${totalItems}][type]">
                                            </td>
                                            <td>

                                                            <span class="badge bg-label-success ">Product</span>

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
                                                <div class="input-group">
                                                    <input class="form-control markup" type="number"
                                                           data-product_id="${totalItems}"
                                                           name="product[${totalItems}][markup]"
                                                           id="product-${totalItems}-markup"
                                                           value="0" min="0"
                                                    />
                                                    <span class="input-group-text">%</span>
                                                </div>
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
                $('#modalCloseManufacturer').click();


            });

            $('#modalCloseManufacturer').on('click', function () {
                $('#modalManufacturerProductSelect').val('').trigger(
                    'change'); // Clear the selection and refresh the select2 UI if applied
            });

            $(document).on('click', '.attribute_value_Manufacturer', function () {
                attribute_values[$(this).data('index')] = $(this).data('value');
                attribute[$(this).data('index')] = $(this).data('attribute');

                $(`.attribute${$(this).data('attribute')}`).addClass('bg-label-secondary');
                $(`.attribute${$(this).data('attribute')}`).removeClass('bg-primary');
                $(this).addClass('bg-primary');
                $(this).removeClass('bg-label-secondary');

                matchVariantManufacturer();
            });

            function matchVariantManufacturer() {
                let variationString = '';
                $(attribute_values).each(function (index, value) {
                    variationString += `${value}-`;
                });
                variationString = variationString.substring(0, variationString.length - 1);

                $(variation).each(function (index, value) {
                    if (value.variant == variationString) {
                        $('#priceWrapperManufacturer').show();
                        price = (value.discount_price != null && value.discount_price != '') ? value
                            .discount_price : value.price;
                        $('#modalPriceManufacturer').text(price);

                        $('#addItemSubmitButtonManufacturer').prop('disabled', false);
                    }
                });
            }


            //service JS

            //Service modal js

            $('#addServiceButton').on('click', function () {

                $('#priceWrapperService').hide();
                attribute_values = [];

            })
            $('#modalServiceSelect').on('change', function () {
                let service_id = $(this).val();
                attribute = [];
                attribute_values = [];
                price = 0;
                $('#priceWrapperService').hide();
                $('#modalPriceService').text('');
                $('#addItemSubmitButtonService').prop('disabled', true);
                if (service_id != '') {
                    $.ajax({
                        url: '/project-management/service/edit/' + service_id,
                        method: 'get',
                        success: function (response) {
                            product = response;
                            variation = [];
                            $('#priceWrapperService').show();
                            price = response.cost ?? response.cost;
                            $('#modalPriceService').text(price);
                            $('#addItemSubmitButtonService').prop('disabled', false);
                        },
                        error: function (error) {
                            console.log(error.responseJSON.message);
                        }
                    });
                }
            });

            $('#addItemSubmitButtonService').on('click', function () {

                $('#variantsWrapperService').empty();

                let s =

                    `
                <tr id="cartRow${countRow}">
                                            <td><i class="ti ti-trash text-danger cursor-pointer deleteProduct"
                                                    data-id="${countRow}"></i></td>

                                            <td>
                                                <img src="${product.image}"
                                                    class="me-2" height="50px" alt="">
                                                 ${product.title}
                                                <input type="number" value="${product.id}"
                                                    hidden
                                                    name="product[${totalItems}][product_id]">
                                                <input type="number" value="2"
                                                    hidden
                                                    name="product[${totalItems}][type]">
                                            </td>
                                            <td>

                                                       <span class="badge bg-label-primary ">Service</span>

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

                                            <td class="text-center">
                                                 -------
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
                price = null;
                calculateTotalPrices();
                $('#modalCloseService').click();
            });

            $('#modalCloseService').on('click', function () {
                $('#modalServiceSelect').val('').trigger(
                    'change'); // Clear the selection and refresh the select2 UI if applied
            });

        });
    </script>
@endpush
