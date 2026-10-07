@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Time Billing'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb( _trans('keyword.Idea Board Details'),['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),'Idea Board'=>   _trans('keyword.Idea Board Details')]) !!}

        <div class="app-ecommerce-category">
            {!! projectTabMenu($ideaBoard->project, 'idea-board', $ideaBoard->project->id) !!}
            <!-- Category List Table -->
            <div class="d-flex flex-row justify-content-end">
                <a class="add-new text-white btn btn-primary mb-3 me-3" data-bs-toggle="modal"
                   data-bs-target="#addManufacturerItem">
                    <i class="ti ti-plus ti-xs me-0 me-sm-2"></i>
                    Add Manufacturer Product
                </a>
                <a class="add-new text-white btn btn-primary mb-3 me-3" data-bs-toggle="modal"
                   data-bs-target="#addItem">
                    <i class="ti ti-plus ti-xs me-0 me-sm-2"></i>
                    Add Product
                </a>
                <a class="add-new text-white btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addService">
                    <i class="ti ti-plus ti-xs me-0 me-sm-2"></i>
                    Add Service
                </a>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <img src="{{getFilePath($ideaBoard->image)}}" style="max-height: 190px; overflow: hidden"
                             class="card-img-top"
                             alt="Hollywood Sign on The Hill"/>
                        <div class="card-body">
                            <h5 class="card-title">{{$ideaBoard->title}}</h5>
                            <p class="card-text">
                                {{$ideaBoard->description}}
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial rounded bg-label-primary">
                                        <i class='ti ti-currency-dollar ti-md'></i>
                                    </div>
                                </div>
                                <div class="gap-0 d-flex flex-column mb-2">
                                    <p class="mb-0 fw-medium">{{getPriceFormat($ideaBoard->items_sum_price)}}
                                        /{{getPriceFormat($ideaBoard->budget)}}</p>
                                    <small>Budget</small>
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{round(($ideaBoard->items_sum_price/$ideaBoard->budget)*100)}}%;   @if(round(($ideaBoard->items_sum_price/$ideaBoard->budget)*100)>90) background-color:darkred;
                                    @endif"
                                     aria-valuenow="{{round(($ideaBoard->items_sum_price/$ideaBoard->budget)*100)}}"
                                     aria-valuemin="0"

                                     aria-valuemax="100">{{round(($ideaBoard->items_sum_price/$ideaBoard->budget)*100)}}
                                    %
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex flex-row align-items-center justify-content-end">
                                <div>
                                    <a href="#"
                                       class="rounded btn btn-label-danger generateProposal"><i
                                            class="ti ti-home-edit me-1"></i>@if ($ideaBoard->is_generate_proposal == 1)
                                            Regenerate
                                        @else
                                            Generate Proposal
                                        @endif
                                    </a>
                                    @if ($ideaBoard->is_generate_proposal == 1)
                                        <a href="{{ route('project-management.project.proposal.index', $ideaBoard->project->id) }}"
                                           class="rounded btn btn-label-primary"><i
                                                class="ti ti-file-description me-1"></i>View
                                            Proposal
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-9">
                    <div class="row" id="productItemWrapper">
                        @foreach($ideaBoardItem as $item)
                            <div class="col-md-3 col-xl-3 mb-4">
                                <div class="card">
                                    <div>
                                        <img class="card-img-top" style="height: 200px"
                                             src="{{getFilePath($item['image'])}}"
                                             alt="Card image cap"/>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{$item['name']}}</h5>
                                        @forelse ($item['variation'] as $variation)
                                            <span><b class="me-1">{{$variation['attribute']}}:</b><span
                                                    class="text-primary">{{$variation['value']}}</span></span>
                                            <br>
                                        @empty
                                            ---
                                        @endforelse


                                        <div
                                            class="card-text text-dark">
                                            <p class="m-0">{{getPriceFormat($item['unit_price'])}}
                                                x {{$item['quantity']}} =
                                                <strong>{{getPriceFormat($item['unit_price']*$item['quantity'])}} </strong>
                                            </p>
                                            <small class="text-muted m-0" style="font-size: 12px">
                                                (Ship: {{getPriceFormat($item['shipping_charge'])}}
                                                Discount: {{ getPriceFormat($item['discount_price']) }}
{{--                                                Mark: {{ getPriceFormat($item['mark_up']) }}--}}
                                                )
                                            </small>
                                            <p>
                                                Total: <strong>{{ getPriceFormat($item['total_price']) }}</strong>
                                            </p>
                                        </div>

                                        <div
                                            class="card-text text-dark d-flex justify-content-between align-items-center">
                                            <div>
                                                <button class="rounded btn-label-warning category_edit_button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal" data-id="{{$item['id']}}"><i
                                                        class="ti ti-edit"></i></button>
                                                <button class="rounded btn-label-danger category_delete_button"
                                                        data-id="{{$item['id']}}"><i
                                                        class="ti ti-trash"></i></button>

                                            </div>
                                            @if($item['type'] === 1)
                                                <span class="badge bg-label-success">Product</span>
                                            @else()
                                                <span class="badge bg-label-primary">Service</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                {{-- add product modal --}}
                <div class="modal fade" id="addItem" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                        <div class="modal-content p-3 p-md-5">
                            <div class="modal-body">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                <div class="text-center mb-4">
                                    <h3 class="mb-2">{{ _trans('keyword.Add New Items ') }}</h3>

                                </div>
                                <form id="editUserForm" class="row g-3" onsubmit="return false">
                                    <div class="col-12">
                                        <label class="form-label"
                                               for="modalEditUserCountry">{{ _trans('keyword.Select Product') }}</label>
                                        <select id="modalProductSelect" name="modalEditUserCountry"
                                                class="select2 form-select" data-allow-clear="true">
                                            <option value="">{{ _trans('keyword.Select Product') }}</option>
                                        </select>
                                    </div>

                                    <div id="variantsWrapper" class="col-12">

                                    </div>
                                    <div class="col-12">
                                        <h6 id="priceWrapper"> price: {{ getCurrency() }} <span id="modalPrice"></span>
                                        </h6>
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                <div class="text-center mb-4">
                                    <h3 class="mb-2">{{ _trans('keyword.Add New Item') }}</h3>

                                </div>
                                <form id="editUserForm" class="row g-3" onsubmit="return false">
                                    <div class="col-12">
                                        <label class="form-label"
                                               for="modalEditUserCountry">{{ _trans('keyword.Select Product') }}</label>

                                    </div>
                                    <select id="modalManufacturerProductSelect" name="modalEditUserCountry"
                                            class="select2 form-select"
                                            data-allow-clear="true">
                                        <option value="">{{ _trans('keyword.Select Product') }}</option>
                                    </select>


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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                <div class="text-center mb-4">
                                    <h3 class="mb-2">{{ _trans('keyword.Add New Item') }}</h3>

                                </div>
                                <form id="editUserForm" class="row g-3" onsubmit="return false">
                                    <div class="col-12">
                                        <label class="form-label"
                                               for="modalEditUserCountry">{{ _trans('keyword.Select Service') }}</label>
                                        <select id="modalServiceSelect" name="modalEditUserCountryService"
                                                class="select2 form-select"
                                                data-allow-clear="true">
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

                <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">

                    <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{_trans('keyword.Edit Item')}}</h5>
                                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal"
                                        id="closeEditModal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <div class="row mb-2 align-items-center">
                                    <div class="col-6">

                                        <img id="currentImage" src="" width="100%">
                                    </div>
                                    <div class="col-6">
                                        <h5 id="item_name"></h5>
                                        <div id="item_variation_edit_modal"></div>
                                        <input id="edit_item_id" hidden value=""/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <label class="form-label">{{_trans('keyword.Price')}}</label>
                                        <input type="number" id="edit_price" name="edit_price" class="form-control"
                                               step="1"
                                               tabindex="-1" required/>
                                        <span id="priceError" class="text-danger"></span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="form-label">{{_trans('keyword.Quantity')}}</label>
                                        <input type="number" id="edit_qty" name="edit_qty" class="form-control" step="1"
                                               tabindex="-1" required/>
                                        <span id="qtyError" class="text-danger"></span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                               for="discout_type">
                                            <span>{{_trans('keyword.Discount Type')}} <span class="text-danger">*</span>
                                            </span>
                                        </label>
                                        <select id="edit_discount_type" name="edit_discount_type"
                                                class="select2 form-select"
                                                style="width: 100%"
                                                data-placeholder="Select Category">
                                            <option
                                                value="0">{{_trans('keyword.Select').' '._trans('keyword.Discount Type')}}</option>
                                            <option value="1">{{_trans('keyword.Percentage')}} %</option>
                                            <option value="2">{{_trans('keyword.Fixed')}}</option>
                                        </select>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="form-label">{{_trans('keyword.Discount')}}</label>
                                        <input type="number" id="edit_discount" name="edit_discount"
                                               class="form-control"
                                               tabindex="-1"/>
                                        <span id="discountError" class="text-danger"></span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="form-label">{{_trans('keyword.Shipping Charge')}} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="edit_shipping_charge" name="edit_shipping_charge"
                                               class="form-control"
                                               tabindex="-1" required/>
                                    </div>
{{--                                    <div class="col-6 mb-2">--}}
{{--                                        <label class="form-label">{{_trans('keyword.Markup')}} <span--}}
{{--                                                class="text-danger">*</span></label>--}}
{{--                                        <input type="number" id="edit_markup" name="edit_markup"--}}
{{--                                               class="form-control"--}}
{{--                                               tabindex="-1" required/>--}}
{{--                                    </div>--}}
                                    <div class="col-6 mb-2">
                                        <label class="form-label">{{_trans('keyword.Total')}} <span class="text-danger">*</span></label>
                                        <input type="number" id="edit_total" name="edit_total"
                                               class="form-control"
                                               tabindex="-1" readonly/>

                                    </div>

                                </div>


                                <div class="col-12 text-center mt-2">
                                    <button id="updateItemModal" type="submit"
                                            class="btn btn-primary me-sm-3 me-1">{{_trans('keyword.Update')}}
                                        <span class="loader"></span>

                                    </button>
                                    <button id="reset" type="reset" class="btn btn-label-secondary"
                                            data-bs-dismiss="modal" aria-label="Close">
                                        {{_trans('keyword.Cancel')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endsection

        @push('scripts')
            <script>
                $(document).ready(function () {
                    $.ajax({
                        url: "/project-management/project/idea-board/products",
                        type: "GET",
                        dataType: "json",
                        success: function (response) {
                            console.log(response)
                            if (response.products) {
                                var select = $("#modalProductSelect");
                                select.empty().append('<option value="">{{ _trans("keyword.Select Product") }}</option>');

                                $.each(response.products, function (index, product) {
                                    select.append('<option value="' + product.id + '">' + product.name + '</option>');
                                });

                                select.trigger("change"); // Refresh select2 if applicable
                            }
                        },
                        error: function (xhr) {
                            console.log("Error loading products:", xhr.responseText);
                        }
                    });
                });

                $(document).ready(function () {
                    $.ajax({
                        url: "/project-management/project/idea-board/manufacturer/products",
                        type: "GET",
                        dataType: "json",
                        success: function (response) {
                            if (response.products) {
                                var select = $("#modalManufacturerProductSelect");
                                select.empty().append('<option value="">{{ _trans("keyword.Select Product") }}</option>');

                                $.each(response.products, function (index, product) {
                                    select.append('<option value="' + product.id + '">' + product.name + '</option>');
                                });

                                select.trigger("change"); // Refresh select2 if applicable
                            }
                        },
                        error: function (xhr) {
                            console.log("Error loading manufacturer products:", xhr.responseText);
                        }
                    });
                });

                $(function () {


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
                    let price = null;
                    let product = {};
                    let countRow = -1;
                    let totalItems = 0;
                    $('#priceWrapper').hide();

                    $('#modalClose').on('click', function () {
                        $('#modalProductSelect').val('').trigger('change'); // Clear the selection and refresh the select2 UI if applied
                    });
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

                    $('#addItemSubmitButton').on('click', async function () {


                        $('#variantsWrapper').empty();
                        let data = await storeItem(1);

                        if (data) {
                            let s =

                                `<div class="col-md-3 col-xl-3 mb-4" id="cartRow${countRow}">
                                <div class="card">
                                    <img class="card-img-top" src="${product.thumbnail_img}" alt="Card image cap" />
                                    <div class="card-body">
                                    <h5 class="card-title">${product.name}</h5>
                                    {{-- <p class="card-text">{{substr(strip_tags($product->description), 0, 30)}}</p> --}}
                                `;
                            if (attribute.length > 0) {
                                $(attribute).each(function (index, value) {
                                    s +=
                                        `
                        <span><b class="me-1">${value}:</b><span
                                                                class="text-primary">${attribute_values[index]}</span></span><br>

                        `;
                                });

                            } else {
                                s += '---'
                            }

                            s += `<div class="card-text text-dark d-flex justify-content-between align-items-center">
                        <p>$${price} x 1 = <strong>$${price * 1} </strong></p>
                    </div>`;

                            s += `<div class="card-text text-dark d-flex justify-content-between align-items-center">
                            <div>
                                <button class="rounded btn-label-warning category_edit_button" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-id="${data.id}"><i
                                        class="ti ti-edit"></i></button>
                                <button class="rounded btn-label-danger category_delete_button" data-id="${data.id}"><i
                                        class="ti ti-trash"></i></button>
                            </div>
                            <span class="badge bg-label-success">Product</span>
                        </div>
                    </div>
                </div>`;
                            $('#productItemWrapper').append(s);
                        }


                        console.log(data, 'this is return data');
                        countRow--;
                        totalItems++;
                        attribute = [];
                        attribute_values = [];
                        product = {};
                        price = 0;
                        $('#modalClose').click();

                    });

                    //Service modal js
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

                    $('#addItemSubmitButtonService').on('click', async function () {


                        $('#variantsWrapperService').empty();
                        let data = await storeItem(2);
                        console.log(data);

                        if (data) {
                            let s = `<div class="col-md-3 col-xl-3 mb-4" id="cartRow${countRow}">
                                <div class="card">
                                    <img class="card-img-top" src="${product.image}" alt="Card image cap" />
                                    <div class="card-body">
                                    <h5 class="card-title">${product.title}</h5>
                                    {{-- <p class="card-text">{{substr(strip_tags($product->description), 0, 30)}}</p> --}}
                            `;

                            s += `<div class="card-text text-dark d-flex justify-content-between align-items-center">
                        <p>$${price} x 1 = <strong>$${price * 1} </strong></p>
                    </div>`;


                            s += `<div class="card-text text-dark d-flex justify-content-between align-items-center">
                            <div>
                                <button class="rounded btn-label-warning category_edit_button" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-id="${data.id}"><i
                                        class="ti ti-edit"></i></button>
                                <button class="rounded btn-label-danger category_delete_button" data-id="${data.id}"><i
                                        class="ti ti-trash"></i></button>
                            </div>
                            <span class="badge bg-label-primary">Service</span>
                        </div>
                    </div>
                </div>`;


                            $('#productItemWrapper').append(s);
                        }

                        countRow--;
                        totalItems++;
                        attribute = [];
                        attribute_values = [];
                        product = {};
                        price = null;
                        $('#modalCloseService').click();
                    });

                    $('#modalCloseService').on('click', function () {
                        $('#modalServiceSelect').val('').trigger('change'); // Clear the selection and refresh the select2 UI if applied
                    });


                    //Manufacturer JS

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

                    $('#addItemSubmitButtonManufacturer').on('click', async function () {


                        $('#variantsWrapperManufacturer').empty();
                        let data = await storeItem(1);

                        if (data) {
                            let s =

                                `<div class="col-md-3 col-xl-3 mb-4" id="cartRow${countRow}">
                                <div class="card">
                                    <img class="card-img-top" src="${product.thumbnail_img}" alt="Card image cap" />
                                    <div class="card-body">
                                    <h5 class="card-title">${product.name}</h5>
                                    {{-- <p class="card-text">{{substr(strip_tags($product->description), 0, 30)}}</p> --}}
                                `;
                            if (attribute.length > 0) {
                                $(attribute).each(function (index, value) {
                                    s +=
                                        `
                        <span><b class="me-1">${value}:</b><span
                                                                class="text-primary">${attribute_values[index]}</span></span><br>

                        `;
                                });

                            } else {
                                s += '---'
                            }

                            s += `<div class="card-text text-dark d-flex justify-content-between align-items-center">
                        <p>$${price} x 1 = <strong>$${price * 1} </strong></p>
                    </div>`;

                            s += `<div class="card-text text-dark d-flex justify-content-between align-items-center">
                            <div>
                                <button class="rounded btn-label-warning category_edit_button" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-id="${data.id}"><i
                                        class="ti ti-edit"></i></button>
                                <button class="rounded btn-label-danger category_delete_button" data-id="${data.id}"><i
                                        class="ti ti-trash"></i></button>
                            </div>
                            <span class="badge bg-label-success">Product</span>
                        </div>
                    </div>
                </div>`;

                            $('#productItemWrapper').append(s);
                        }

                        countRow--;
                        totalItems++;
                        attribute = [];
                        attribute_values = [];
                        product = {};
                        price = 0;
                        $('#modalCloseManufacturer').click();


                    });

                    $('#modalCloseManufacturer').on('click', function () {
                        $('#modalManufacturerProductSelect').val('').trigger('change'); // Clear the selection and refresh the select2 UI if applied
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
                                price = (value.discount_price != null && value.discount_price != '') ? value.discount_price : value.price;
                                $('#modalPriceManufacturer').text(price);

                                $('#addItemSubmitButtonManufacturer').prop('disabled', false);
                            }
                        });
                    }


                    async function storeItem(service_type) {
                        let success = false;

                        var formData = new FormData();
                        formData.append('price', price);
                        formData.append('attribute', attribute);
                        formData.append('attribute_values', attribute_values);
                        formData.append('product_id', product.id);
                        formData.append('service_type', service_type);
                        formData.append('_token', "{{ csrf_token() }}");
                        await $.ajax({
                            url: '{{ route('project-management.project.idea-board.items.store',[$ideaBoard->project->id,$ideaBoard->id]) }}',
                            type: 'POST',
                            contentType: false,
                            cache: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                console.log(response.data, 'res');
                                toastr.success(response.message);
                                success = response.data;
                                location.reload();
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                        return success;
                    }

                    $(document).on("click", ".category_edit_button", function () {
                        let id = $(this).data('id');
                        $('#item_variation_edit_modal').empty();

                        $('#edit_discount_type').find('option:selected').attr("selected", false);
                        $('#edit_discount_type').trigger('change.select2');
                        $.ajax({
                            url: '/project-management/project/idea-board/' + {{$ideaBoard->project->id}} + '/item/edit/' + id,
                            type: 'GET',
                            success: function (response) {
                                $('#item_name').text(response.data.name);
                                $('#edit_price').val(response.data.unit_price);
                                $('#edit_item_id').val(response.data.id);
                                $('#edit_qty').val(response.data.quantity);
                                $('#edit_discount').val(response.data.discount_amount);
                                $('#edit_shipping_charge').val(response.data.shipping_charge);
                                $('#edit_markup').val(response.data.markup);
                                $('#edit_total').val(response.data.price);
                                $('#edit_discount_type').find('option[value="' + response.data.discount_type +
                                    '"]').attr("selected", "selected");

                                $("#currentImage").attr("src", ``);

                                if (response.data.image != null) {
                                    $("#currentImage").attr("src", `${response.data.image}`);
                                }

                                $('#edit_discount_type').trigger('change.select2');

                                let s = '';
                                $(response.data.variation).each(function (index, value) {
                                    s +=
                                        `
                                    <div class="row mb-1">
                                        <div class="col">${value.attribute}: ${value.value}</div>
                                    </div>
                                `;
                                });


                                $('#item_variation_edit_modal').empty();
                                $('#item_variation_edit_modal').append(s);

                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    });

                    $('#updateItemModal').on('click', function (e) {
                        e.preventDefault();

                        var formData = new FormData();

                        let item_id = $('#edit_item_id').val();
                        let qty = $('#edit_qty').val();
                        let unit_price = $('#edit_price').val();
                        let total_price = $('#edit_total').val();
                        let discount = $('#edit_discount').val();
                        let shipping_charge = $('#edit_shipping_charge').val();
                        let markup = $('#edit_markup').val();
                        let discount_type = $("#edit_discount_type option:selected").val();

                        formData.append('quantity', qty);
                        formData.append('unit_price', unit_price);
                        formData.append('total_price', total_price);
                        formData.append('discount', discount);
                        formData.append('shipping_charge', shipping_charge);
                        formData.append('markup', markup);
                        formData.append('discount_type', discount_type);
                        formData.append('item_id', item_id);
                        formData.append('_token', "{{ csrf_token() }}");


                        $('.error').text('');
                        $.ajax({
                            url: '{{ route('project-management.project.idea-board.items.update',[$ideaBoard->project->id,$ideaBoard->id]) }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                console.log(response);

                                if (response.status == 403) {
                                    // Use ID selectors instead of class selectors
                                    $('#priceError').text(response.errors?.unit_price ? response.errors.unit_price[0] : '');
                                    $('#qtyError').text(response.errors?.quantity ? response.errors.quantity[0] : '');
                                    $('#shippingChargeError').text(response.errors?.shipping_charge ? response.errors.shipping_charge[0] : '');
                                    $('#markupError').text(response.errors?.markup ? response.errors.markup[0] : '');
                                    $('#discountError').text(response.errors?.discount ? response.errors.discount[0] : '');

                                } else if (response.status == 200) {
                                    location.reload();
                                    $('#closeEditModal').click();
                                }
                            },
                            error: function (error) {
                                if (error.responseJSON && error.responseJSON.errors) {
                                    $('#priceError').text(error.responseJSON.errors.unit_price ? error.responseJSON.errors.unit_price[0] : '');
                                    $('#qtyError').text(error.responseJSON.errors.quantity ? error.responseJSON.errors.quantity[0] : '');
                                    $('#shippingChargeError').text(error.responseJSON.errors.shipping_charge ? error.responseJSON.errors.shipping_charge[0] : '');
                                    $('#markupError').text(error.responseJSON.errors.markup ? error.responseJSON.errors.markup[0] : '');
                                    $('#discountError').text(error.responseJSON.errors.discount ? error.responseJSON.errors.discount[0] : '');
                                } else {
                                    toastr.error(error.responseJSON.message);
                                }
                            }
                        });
                    });

                    $(document).on("click", ".category_delete_button", function () {

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

                                $.ajax({
                                    url: '{{ route('project-management.project.idea-board.items.destroy',[$ideaBoard->project->id,$ideaBoard->id]) }}',
                                    method: 'POST',
                                    data: {
                                        "_token": "{{ csrf_token() }}",
                                        item_id: id,
                                    },
                                    success: function (response) {

                                        Swal.fire({
                                            icon: response.icon,
                                            title: "Success",
                                            text: response.text,
                                            customClass: {
                                                confirmButton: 'btn btn-success waves-effect waves-light'
                                            }
                                        });

                                        location.reload();
                                    },
                                    error: function (error) {
                                        console.log(error.responseJSON.message);
                                        // handle the error case
                                    }
                                });
                            }
                        });
                    });

                    $('#edit_qty, #edit_price, #edit_discount, #edit_shipping_charge, #edit_discount_type').on('change keyup', function () {

                        let qty = parseFloat($('#edit_qty').val()) || 0;
                        let unit_price = parseFloat($('#edit_price').val()) || 0;
                        let discount = parseFloat($('#edit_discount').val()) || 0;
                        let shipping_charge = parseFloat($('#edit_shipping_charge').val()) || 0;
                        let discount_type = $("#edit_discount_type").val();

                        // Base total
                        let total = qty * unit_price;

                        // Apply discount
                        if (discount_type == "1") {
                            // percentage
                            total = total - (total * (discount / 100));
                        } else if (discount_type == "2") {
                            // flat value
                            total = total - discount;
                        }

                        // Add shipping charge
                        total += shipping_charge;

                        // Update total field
                        $('#edit_total').val(total.toFixed(2));
                    });


                    $(document).on("click", ".generateProposal", function () {
                        Swal.fire({
                            title: 'Are you sure to Generate Proposal?',
                            // text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Generate!',
                            customClass: {
                                confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                                cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        }).then(function (result) {
                            if (result.value) {

                                $.ajax({
                                    url: '{{ route('project-management.project.proposal.generateProposal',[$ideaBoard->project->id,$ideaBoard->id]) }}',
                                    success: function (response) {

                                        Swal.fire({
                                            icon: 'success',
                                            title: "Success",
                                            text: response.message,
                                            customClass: {
                                                confirmButton: 'btn btn-success waves-effect waves-light'
                                            }
                                        });
                                        console.log(response.redirect_route);
                                        window.location.href = response.redirect_route;
                                    },
                                    error: function (error) {
                                        console.log(error.responseJSON);
                                        // handle the error case
                                    }
                                });
                            }
                        });
                    });


                });

            </script>
    @endpush
