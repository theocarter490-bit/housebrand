@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Update').' '._trans('keyword.Products'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Update').' '._trans('keyword.Products'),['#'=>_trans('keyword.Product').' '._trans('keyword.Management'),'product/index'=>_trans('keyword.Product'), 'product'=>_trans('keyword.Update').' '._trans('keyword.Products')]) !!}
        <div class="app-ecommerce">
            <!-- Edit Product -->
            <form action="" id="product-add" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <!-- First column-->
                    <div class="col-12 col-xl-8">
                        <!-- Product Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-tile mb-0">{{_trans('keyword.Product').' '. _trans('keyword.Information')}}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-9">
                                        <label class="form-label"
                                               for="ecommerce-product-name">{{_trans('keyword.Title')}} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title"
                                               placeholder="Product title" name="title" value="{{ $product->name }}"
                                               aria-label="Product title"/>
                                        <span class="text-danger titleError error"></span>
                                        <input type="number" name="product_id" hidden value="{{ $product->id }}">
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label"
                                               for="ecommerce-product-barcode">{{_trans('keyword.Barcode')}}</label>
                                        <input type="text" class="form-control" id="barcode"
                                               value="{{ $product->barcode }}" placeholder="0123-4567" name="barcode"
                                               aria-label="Product barcode"/>
                                        <span class="text-danger barcodeError error"></span>
                                    </div>
                                </div>
                                <!-- Description -->
                                <div>
                                    <label class="form-label">{{_trans('keyword.Description')}} (Optional)</label>
                                    <div class="form-control p-0 pt-1">
                                        <div class="comment-toolbar border-0 border-bottom">
                                            <div class="d-flex justify-content-start">
                                                <span class="ql-formats me-0">
                                                    <button class="ql-bold"></button>
                                                    <button class="ql-italic"></button>
                                                    <button class="ql-underline"></button>
                                                    <button class="ql-list" value="ordered"></button>
                                                    <button class="ql-list" value="bullet"></button>
                                                    <button class="ql-link"></button>
                                                    {{-- <button class="ql-image"></button> --}}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="product-editor border-0 pb-4"
                                             id="description">{!! $product->description !!}
                                        </div>
                                    </div>
                                    <span class="text-danger descriptionError error"></span>
                                </div>
                            </div>
                        </div>
                        <!-- /Product Information -->

                        <!-- Product Image -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{_trans('keyword.Product') .' '._trans('keyword.Image')}}
                                    <button type="button" class="border border-0 text-primary bg-transparent m-0 p-0"
                                            data-bs-toggle="popover" data-bs-placement="right"
                                            data-bs-content="You can upload multiple image of a product"
                                            title="Variants"><small
                                            class="rounded-circle p-0 m-0 px-1 bg-primary"><i
                                                class="fa-solid fa-question text-white"
                                                style="font-size: 10px !important"></i></small>
                                    </button>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="multiple-uploader" id="multiple-uploader">
                                    <div class="mup-msg">
                                        <span
                                            class="mup-main-msg">{{_trans('keyword.Click') .' '._trans('keyword.To').' '. _trans('keyword.Upload').' '._trans('keyword.Image')}}</span>
                                        <span class="mup-msg"
                                              id="max-upload-number">{{_trans('keyword.Upload'). _trans('keyword.Up'). _trans('keyword.To'). 10 ._trans('keyword.Image')}}</span>
                                        <span
                                            class="mup-msg">{{_trans('keyword.Select') .' '._trans('keyword.Multiple').' '. _trans('keyword.Image').' '. _trans('keyword.Together')}}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    @foreach ($product->images as $image)
                                        <div class="col-1 position-relative ">
                                            <img src="{{ getFilePath($image->path) }}"
                                                 class="rounded px-0 mx-1 img-fluid"
                                                 width="100%" alt="...">
                                            <i class="fa-solid fa-trash-can text-danger position-absolute top-0 end-0 cursor-pointer deleteProductImage"
                                               data-id="{{ $image->id }}"></i>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- Product Image -->

                        <!-- Variants -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{_trans('keyword.Variants')}}
                                    <button type="button" class="border border-0 text-primary bg-transparent m-0 p-0"
                                            data-bs-toggle="popover" data-bs-placement="right"
                                            data-bs-content="Select Variant to set variant wise product price"
                                            title="Variants"><small class="rounded-circle p-0 m-0 px-1 bg-primary"><i
                                                class="fa-solid fa-question text-white"
                                                style="font-size: 10px !important"></i></small>
                                    </button>
                                </h5>
                            </div>
                            <div class="card-body">


                                <div class="row mb-2">
                                    <div
                                        class="col-4 d-flex align-items-center justify-content-center bg-secondary rounded bg-opacity-50 border border-success">
                                        <h6 class="mb-0 text-dark">{{_trans('keyword.Select').' '. _trans('keyword.Variants').' '._trans('keyword.Attribute')}}</h6>
                                    </div>
                                    <div class="col-8">
                                        <div class="select2-primary">

                                            <select id="attributes" name="attributes[]" class="select2 form-select"
                                                    style="width: 100%;"
                                                    data-placeholder="Select Attribute" multiple>
                                                @foreach ($attributes as $attribute)

                                                    <option value="{{ $attribute->id }}"
                                                        {{ (($product->attributes!=null) && in_array($attribute->id, $product->attributes)) ? 'selected' : '' }}>
                                                        {{ $attribute->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <small><span
                                            class="text-danger">*</span>{{_trans('keyword.Choose attribute of a product and then input values of each attribute')}}
                                    </small>
                                </div>

                                <div class="row" id="attribute_value_container">
                                    @foreach ($product->choiceOptions as $choiceOption)
                                        <div
                                            class="col-4 mb-3 d-flex align-items-center justify-content-center bg-secondary rounded bg-opacity-50 border border-primary">
                                            <h6 class="mb-0 text-dark">{{ $choiceOption->name }}</h6>
                                        </div>
                                        <div class="col-8 mb-3">
                                            <div class="select2-primary w-100">
                                                <input hidden
                                                       name="attribute_values[{{ $choiceOption->id }}][attribute_id]"
                                                       value="{{ $choiceOption->id }}"/>
                                                <select id=""
                                                        name="attribute_values[{{ $choiceOption->id }}][value][]"
                                                        class="select2 form-select attribute_values"
                                                        style="width: 100%;"
                                                        data-placeholder="Select Attribute" multiple>`;

                                                    @if(isset($choiceOption->pivot->value))
                                                        @foreach ($choiceOption->values as $value)
                                                            <option value="{{ $value->name }}"
                                                                {{ in_array($value->name, $choiceOption->pivot->value) ? 'selected' : '' }}>
                                                                {{ $value->name }}</option>
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>

                                    @endforeach

                                </div>
                                <span class="text-danger attributeValueError error"></span>
                                <div class="row" id="attribute_value_combination">
                                    <div class="d-flex flex-row align-items-center justify-content-between">
                                        <h6>Set Variants Price</h6>
                                        <span id="discount_type_message" class="badge bg-label-danger">
                                            @if($product->discount_type == 1)
                                                Discount will be effect in percentage
                                            @elseif($product->discount_type == 2)
                                                Discount will be effect in numerical value
                                            @else
                                                Discount price won't affect upon price (No discount upon price is
                                                selected)
                                            @endif
                                        </span>

                                    </div>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th scope="col">{{_trans('keyword.Variant')}}</th>
                                            <th scope="col">{{_trans('keyword.Price')}}</th>
                                            <th scope="col">{{_trans('keyword.Discount')}}</th>
                                            <th scope="col">{{_trans('keyword.Quantity')}}</th>
                                            <th scope="col">{{_trans('keyword.Image')}}</th>
                                            <th scope="col">{{_trans('keyword.Status')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($product->variants as $variant)
                                            <tr class='combination' data-id='{{ $variant->id }}'>
                                                <td style="width: 30%"><input type="text" class="form-control "
                                                                              id="variant_value_name{{ $variant->id }}"
                                                                              value="{{ $variant->variant }}"
                                                                              name="variant[{{ $variant->id }}][name]"
                                                                              readonly/>
                                                </td>
                                                <td><input type="number" onkeyup="if(value<0) value=0;"
                                                           class="form-control"
                                                           id="variant_value_price{{ $variant->id }}"
                                                           placeholder="Price"
                                                           name="variant[{{ $variant->id }}][price]" aria-label="Price"
                                                           value="{{ $variant->price }}"/>
                                                </td>
                                                <td style="width: 15%"><input type="number"
                                                                              onkeyup="if(value<0) value=0;"
                                                                              class="form-control discount_input_field"
                                                                              id="variant_value_discount{{ $variant->id }}"
                                                                              placeholder="discount"
                                                                              name="variant[{{ $variant->id }}][discount]"
                                                                              aria-label="Discount"
                                                                              value="{{ $variant->discount_amount }}"/>
                                                </td>
                                                <td style="width: 10%"><input type="number"
                                                                              onkeyup="if(value<0) value=0;"
                                                                              class="form-control"
                                                                              id="variant_value_quantity{{ $variant->id }}"
                                                                              value="0"
                                                                              placeholder="Quantity"
                                                                              name="variant[{{ $variant->id }}][quantity]"
                                                                              aria-label="Quantity"
                                                                              value="{{ $variant->qty }}"/>
                                                </td>
                                                <td class="d-flex justify-content-center align-items-center"><input
                                                        type="file"
                                                        class="form-control variation_combination_image"
                                                        id="variant_value_image{{ $variant->id }}"
                                                        placeholder="Image" data-id="{{ $variant->id }}"
                                                        name="variant[{{ $variant->id }}][image]"
                                                        aria-label="Image"/>
                                                    <img class="ms-1" src="{{ getFilePath($variant->image) }}"
                                                         alt="" width="35x" height="35px">
                                                    <input type="number" hidden
                                                           name="variant[{{ $variant->id }}][combination_id]"
                                                           value="{{ $variant->id }}">
                                                </td>
                                                <td>
                                                    <label class="switch switch-success">
                                                        <input type="checkbox" class="switch-input"
                                                               id="variant_value_active{{ $variant->id }}"
                                                               name="variant[{{ $variant->id }}][active]"
                                                               @if($variant->is_active == 1) checked @endif
                                                               aria-label="Active"/>
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on">
                                                                <i class="ti ti-check"></i>
                                                            </span>
                                                            <span class="switch-off">
                                                                <i class="ti ti-x"></i>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>


                            </div>
                        </div>
                        <!-- /Variants -->
                        <!-- /Diamension & Specifications-->

                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{_trans('keyword.Dimension & Specifications')}}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row" id="weightAndDiamensionsContainer">
                                    <div class="row justify-content-between">
                                        <div class="col-6">
                                            <h6>{{_trans('keyword.Weight And Dimension')}}:</h6>
                                        </div>
                                        <div class="col-3 text-end">
                                            <button class="btn btn-primary btn-sm" type="button"
                                                    id="addMoreWeightAndDiamensions"><i
                                                    class="ti ti-plus ti-xs me-0"></i></button>
                                        </div>
                                    </div>

                                    @forelse($product->weight_dimensions as $weight_dimension)
                                        <div class="row mb-3 parentWeightAndDiamensions">
                                            <div class="col-4">
                                                <label class="form-label" for="weightAndDiamensions">Title</label>
                                                <input type="text" class="form-control parentWeightAndDiamensionsTitle"
                                                       id="ecommerce-product-name"
                                                       placeholder="Weight or Dimensions Title"
                                                       name="weightAndDiamensions[title][]"
                                                       value="{{ $weight_dimension['title'] }}"
                                                       aria-label="Weight or Dimensions Title"/>
                                            </div>
                                            <div class="col-8">
                                                <label class="form-label"
                                                       for="ecommerce-product-name"> {{_trans('keyword.Details')}} </label>
                                                <div class="row">
                                                    <div class='col-11'>
                                                        <input type="text"
                                                               class="form-control parentWeightAndDiamensionsDescription"
                                                               id="ecommerce-product-name"
                                                               placeholder="Weight or Diamensions Details"
                                                               name="weightAndDiamensions[details][]"
                                                               value="{{ $weight_dimension['details'] }}"
                                                               aria-label="Weight or Dimensions Details"/>
                                                    </div>
                                                    <div class="col-1 text-danger deleteWeightAndDiamensions">
                                                        <i class="ti ti-trash "></i>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="row mb-3">
                                            <div class="col-4">
                                                <label class="form-label"
                                                       for="weightAndDiamensions">{{_trans('keyword.Title')}}</label>
                                                <input type="text" class="form-control parentWeightAndDiamensionsTitle"
                                                       id="ecommerce-product-name"
                                                       placeholder="Weight or Diamensions Title"
                                                       name="weightAndDiamensions[title][]"
                                                       aria-label="Weight or Dimensions Title"/>
                                            </div>
                                            <div class="col-8">
                                                <label class="form-label" for="ecommerce-product-name">Value</label>
                                                <input type="text"
                                                       class="form-control parentWeightAndDiamensionsDescription"
                                                       id="ecommerce-product-name"
                                                       placeholder="Weight or Diamensions Details"
                                                       name="weightAndDiamensions[details][]"
                                                       aria-label="Weight or Dimensions Details"/>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="row mt-4" id="specificationsContainer">
                                    <div class="row justify-content-between">
                                        <div class="col-6">
                                            <h6>{{_trans('keyword.Specifications')}}:</h6>
                                        </div>
                                        <div class="col-3 text-end">
                                            <button class="btn btn-primary btn-sm" type="button"
                                                    id="addMoreSpecifications"><i class="ti ti-plus ti-xs me-0"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @forelse ($product->specifications as $specification)
                                        <div class="row mb-3 parentSpecifications">
                                            <div class="col-4">
                                                <label class="form-label" for="Specifications">Title</label>
                                                <input type="text" class="form-control parentSpecificationsTitle"
                                                       id="ecommerce-product-name" placeholder="Product title"
                                                       value="{{ $specification['title'] }}"
                                                       name="specifications[title][]"
                                                       aria-label="Specifications Title"/>
                                            </div>
                                            <div class="col-8">
                                                <label class="form-label" for="ecommerce-product-name"> Details </label>
                                                <div class="row">
                                                    <div class='col-11'>
                                                        <input type="text"
                                                               class="form-control parentSpecificationsDescription"
                                                               id="ecommerce-product-name" placeholder="Product title"
                                                               name="specifications[value][]"
                                                               value="{{ $specification['details'] }}"
                                                               aria-label="Specifications Details"/>
                                                    </div>
                                                    <div class="col-1 text-danger deleteSpecifications">
                                                        <i class="ti ti-trash "></i>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @empty

                                        <div class="row mb-3">
                                            <div class="col-4">
                                                <label class="form-label"
                                                       for="Specifications">{{_trans('keyword.Title')}}</label>
                                                <input type="text" class="form-control parentSpecificationsTitle"
                                                       id="ecommerce-product-name" placeholder="Specifications title"
                                                       name="specifications[title][]"
                                                       aria-label="Specifications Title"/>
                                            </div>
                                            <div class="col-8">
                                                <label class="form-label"
                                                       for="ecommerce-product-name">{{_trans('keyword.Value')}}</label>
                                                <input type="text" class="form-control parentSpecificationsDescription"
                                                       id="ecommerce-product-name" placeholder="Specifications"
                                                       name="specifications[value][]" aria-label="Specifications"/>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- Diamension & Specifications -->
                    </div>
                    <!-- /Second column -->

                    <!-- Second column -->
                    <div class="col-12 col-xl-4">

                        <!-- Media -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 card-title">{{_trans('keyword.Thumbnail')}} <span
                                        class="text-danger">*</span></h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <img src="{{ getFilePath($product->thumbnail_img) }}" id="darkLogo"
                                         onerror="this.onerror=null;this.src='{{ asset('assets/img/illustrations/page-pricing-enterprise.png') }}'"
                                         alt="user-avatar" class="d-block w-px-200 h-px-auto rounded"/>
                                    <div class="button-wrapper">
                                        <label for="darkLogoInput"
                                               class="btn btn-primary me-2 mb-3 waves-effect waves-light" tabindex="0">
                                            <span class="d-none d-sm-block">{{_trans('keyword.Upload Image')}}</span>
                                            <i class="ti ti-upload d-block d-sm-none"></i>
                                            <input type="file" id="darkLogoInput" class="darkLogo-account-file-input"
                                                   name="dark_logo" hidden=""
                                                   accept="image/png, image/jpeg, image/jpg">
                                        </label>
                                        <button type="button"
                                                class="btn btn-label-secondary darkLogo-account-image-reset mb-3 waves-effect">
                                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">{{_trans('keyword.Reset')}}</span>
                                        </button>

                                        <div
                                            class="text-muted">{{_trans('keyword.Allowed JPG, GIF or PNG. Max size of 800KB')}}</div>
                                        <span class="text-danger dark_logoError error"></span>
                                    </div>
                                </div>
                                <span class="text-danger thumbnailImageError error"></span>
                                <div class="mb-3 mt-3">
                                    <h6 class="mb-1 card-title">{{_trans('keyword.Video Link')}}</h6>
                                    <input type="text" class="form-control" placeholder="ex. www.youtube.com/abc"
                                           value="{{ $product->video_link }}" id="video_link" name="video_link"
                                           aria-label="Product title"/>

                                    <span class="text-danger videoLinkError error"></span>

                                </div>
                            </div>
                        </div>
                        <!-- /Media -->
                        <!-- Pricing Card -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="card-title mb-0">{{_trans('keyword.Pricing')}}
                                    <button type="button" class="border border-0 text-primary bg-transparent m-0 p-0"
                                            data-bs-toggle="popover" data-bs-placement="right"
                                            data-bs-content="Setting a base price for our essential product is required. If there are no discounts, the base price will stay the same."
                                            title="Pricing"><small class="rounded-circle p-0 m-0 px-1 bg-primary"><i
                                                class="fa-solid fa-question text-white"
                                                style="font-size: 10px !important"></i></small>
                                    </button>
                                </h5>
                                <div>

                                    <label class="switch switch-success" style="margin-right: 40px;">
                                        <input type="checkbox" class="switch-input" id="is_price_hidden"
                                               name="is_price_hidden" @if($product->is_price_hidden == 1) checked
                                               @endif aria-label="Active"/>
                                        <span class="switch-toggle-slider">
                                        <span class="switch-on">
                                            <i class="ti ti-check"></i>
                                        </span>
                                        <span class="switch-off">
                                            <i class="ti ti-x"></i>
                                        </span>
                                    </span>
                                    </label>
                                    <button type="button" class="border border-0 text-primary bg-transparent m-0 p-0"
                                            data-bs-toggle="popover" data-bs-placement="right"
                                            data-bs-content="Enable this toggle to hide the product price from customers."
                                            title="Pricing"><small class="rounded-circle p-0 m-0 px-1 bg-primary"><i
                                                class="fa-solid fa-question text-white"
                                                style="font-size: 10px !important"></i></small>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Base Price -->
                                <div class="mb-3">
                                    <label class="form-label"
                                           for="ecommerce-product-price">{{_trans('keyword.Base Price')}} <span
                                            class="text-danger">*</span></label>
                                    <input type="number" onkeyup="if(value<0) value=0;" class="form-control"
                                           id="product_price" placeholder="Price"
                                           value="{{ $product->unit_price }}" name="unit_price"
                                           aria-label="Product price"/>
                                    <span class="text-danger unitPriceError error"></span>
                                </div>

                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                           for="discout_type">
                                        <span>{{_trans('keyword.Discount Type')}}</span>
                                    </label>
                                    <select id="discout_type" name="discount_type" class="select2 form-select"
                                            data-placeholder="Select Discount Type">
                                        <option
                                            value="0" {{ $product->discount_type == 0 ? 'selected' : '' }}>{{_trans('keyword.Select').' '._trans('keyword.Discount Type')}}</option>
                                        <option
                                            value="1" {{ $product->discount_type == 1 ? 'selected' : '' }}>{{_trans('keyword.Percentage')}}
                                            %
                                        </option>
                                        <option
                                            value="2" {{ $product->discount_type == 2 ? 'selected' : '' }}>{{_trans('keyword.Fixed')}}
                                        </option>
                                    </select>
                                </div>
                                <span class="text-danger discountTypeError error"></span>


                                <!-- Discounted Price -->
                                <div class="mb-3">
                                    <label class="form-label"
                                           for="ecommerce-product-discount-price">{{_trans('keyword.Discount')}}</label>
                                    <input type="number" class="form-control" id="discount_price"
                                           placeholder="Discounted Price" name="discount_value"
                                           value="{{ $product->discount }}" aria-label="Product discounted price"/>

                                    <span class="text-danger discountValueError error"></span>
                                    <br/>
                                    <small class="text-primary"><span
                                            class="text-danger">*</span>{{_trans("keyword.Don't need input if product don't have discount.")}}
                                    </small>
                                </div>


                            </div>
                        </div>
                        <!-- /Pricing Card -->
                        <!-- Organize Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{_trans('keyword.Organize')}}</h5>
                            </div>
                            <div class="card-body">

                                <!-- Category -->
                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                           for="category-org">
                                        <span>{{_trans('keyword.Category')}} <span class="text-danger">*</span></span>
                                    </label>
                                    <select id="category" name="category" class="select2 form-select"
                                            style="width: 100%"
                                            data-placeholder="Select Category">
                                        <option
                                            value="">{{_trans('keyword.Select'). ''. _trans('keyword.Category')}}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger categoryError error"></span>
                                </div>
                                <!-- Collection -->
                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1"
                                           for="collection">{{_trans('keyword.Select').' '._trans('keyword.Brand')}}</label>
                                    <select id="brand" name="brand_id" class="select2 form-select" style="width: 100%"
                                            data-placeholder="Select Brand">
                                        <option
                                            value="">{{_trans('keyword.Select').' '._trans('keyword.Brand')}}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Vendor -->
                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1" for="unit">{{_trans('keyword.Unit')}} <span
                                            class="text-danger">*</span></label>
                                    <select id="unit" name="unit" class="select2 form-select" style="width: 100%"
                                            data-placeholder="Select Unit">
                                        <option value="">{{_trans('keyword.Select')._trans('keyword.Unit')}}</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->name }}"
                                                {{ $product->unit == $unit->name ? 'selected' : '' }}>{{ $unit->name }}
                                            </option>
                                        @endforeach
                                        <span class="text-danger unitError error"></span>
                                    </select>
                                </div>
                                <!-- Status -->
                                @if (hasPermission('product_status_change'))
                                    <div class="mb-3 col ecommerce-select2-dropdown">
                                        <label class="form-label mb-1"
                                               for="status-org">{{_trans('keyword.Status')}} </label>
                                        <select id="status" name="status" class="select2 form-select"
                                                style="width: 100%"
                                                data-placeholder="Published">
                                            <option value="1" {{ $product->is_published == 1 ? 'selected' : '' }}>
                                                Published
                                            </option>
                                            <option value="0" {{ $product->is_published == 0 ? 'selected' : '' }}>
                                                Unpublished
                                            </option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- /Organize Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{_trans('keyword.Terms & Polices')}}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div>
                                        <h6 class="collapse-toggle d-flex align-items-center justify-content-between gap-3"
                                            data-target="shipping-policy-content" style="cursor: pointer;">
                                            {{_trans('keyword.Shipping Policy')}}<i
                                                class="ti ti-minus ti-md me-2 c-ti-minus" style="display: none"></i> <i
                                                class="ti ti-plus ti-md me-2 c-ti-plus"></i>
                                        </h6>
                                        <div id="shipping-policy-content" style="display: none;">
                                            <div class="form-control p-0 pt-1">
                                                <div class="shipping-policy-toolbar border-0 border-bottom">
                                                    <div class="d-flex justify-content-start">
                                <span class="ql-formats me-0">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                    <button class="ql-list" value="ordered"></button>
                                    <button class="ql-list" value="bullet"></button>
                                    <button class="ql-link"></button>
                                </span>
                                                    </div>
                                                </div>
                                                <div class="shipping_policy border-0 pb-4"
                                                     id="shipping_policy-description">
                                                    {!! $product->shipping_policy !!}
                                                </div>
                                            </div>
                                            <span class="text-danger shippingPolicyError error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div>
                                        <h6 class="collapse-toggle d-flex align-items-center justify-content-between gap-3"
                                            data-target="return-policy-content" style="cursor: pointer;">
                                            {{_trans('keyword.Return Policy')}} <i
                                                class="ti ti-minus ti-md me-2 c-ti-minus" style="display: none"></i> <i
                                                class="ti ti-plus ti-md me-2 c-ti-plus"></i>
                                        </h6>
                                        <div id="return-policy-content" style="display: none;">
                                            <div class="form-control p-0 pt-1">
                                                <div class="return-policy-toolbar border-0 border-bottom">
                                                    <div class="d-flex justify-content-start">
                                <span class="ql-formats me-0">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                    <button class="ql-list" value="ordered"></button>
                                    <button class="ql-list" value="bullet"></button>
                                    <button class="ql-link"></button>
                                </span>
                                                    </div>
                                                </div>
                                                <div class="return_policy border-0 pb-4" id="return_policy-description">
                                                    {!! $product->return_policy !!}
                                                </div>
                                            </div>
                                            <span class="text-danger returnPolicyError error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div>
                                        <h6 class="collapse-toggle d-flex align-items-center justify-content-between gap-3"
                                            data-target="disclaimer-content" style="cursor: pointer;">
                                            {{_trans('keyword.Disclaimer')}}<i class="ti ti-minus ti-md me-2 c-ti-minus"
                                                                               style="display: none"></i> <i
                                                class="ti ti-plus ti-md me-2 c-ti-plus"></i>
                                        </h6>
                                        <div id="disclaimer-content" style="display: none;">
                                            <div class="form-control p-0 pt-1">
                                                <div class="disclaimer-toolbar border-0 border-bottom">
                                                    <div class="d-flex justify-content-start">
                                <span class="ql-formats me-0">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                    <button class="ql-list" value="ordered"></button>
                                    <button class="ql-list" value="bullet"></button>
                                    <button class="ql-link"></button>
                                </span>
                                                    </div>
                                                </div>
                                                <div class="disclaimer border-0 pb-4" id="disclaimer-description">
                                                    {!! $product->disclaimer !!}
                                                </div>
                                            </div>
                                            <span class="text-danger disclaimerError error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- /Second column -->
                </div>

                @if(hasPermission('product_update') && isSeller())
                    <div class="row justify-content-center">
                        <button type="submit" id="addProduct" class="btn btn-primary col-4">{{_trans('keyword.Submit')}}
                            <span class="loader"></span>
                        </button>
                    </div>
                @endif
            </form>
        </div>

        @endsection

        @push('scripts')
            <script src="{{ asset('assets/js/multiple-uploader.js') }}"></script>
            <script>
                $(document).ready(function () {

                    let darkLogoImage = document.getElementById('darkLogo');
                    const darkLogofileInput = document.querySelector('.darkLogo-account-file-input'),
                        darkLogoresetFileInput = document.querySelector('.darkLogo-account-image-reset');

                    if (darkLogoImage) {
                        const resetImage = darkLogoImage.src;
                        darkLogofileInput.onchange = () => {
                            if (darkLogofileInput.files[0]) {
                                darkLogoImage.src = window.URL.createObjectURL(darkLogofileInput.files[0]);
                            }
                        };
                        darkLogoresetFileInput.onclick = () => {
                            darkLogofileInput.value = '';
                            darkLogoImage.src = resetImage;
                        };
                    }


                    $('#addMoreWeightAndDiamensions').on('click', function () {
                        let s = $(
                            `<div class="row mb-3 parentWeightAndDiamensions">
                        <div class="col-4" >
                            <label class="form-label" for= "weightAndDiamensions">Title</label>
                            <input type = "text" class="form-control parentWeightAndDiamensionsTitle" id = "ecommerce-product-name" placeholder = "Weight or Diamensions Title" name = "weightAndDiamensions[title][]" aria-label = "Weight or Diamensions Title" />
                        </div>
                        <div class="col-8" >
                            <label class="form-label" for="ecommerce-product-name"> Details </label>
                            <div class="row">
                                <div class='col-11'>
                                    <input type="text" class="form-control parentWeightAndDiamensionsDescription" id="ecommerce-product-name" placeholder = "Weight or Diamensions Details" name = "weightAndDiamensions[details][]" aria-label = "Weight or Diamensions Details" />
                                </div>
                                <div class="col-1 text-danger deleteWeightAndDiamensions" >
                                    <i class="ti ti-trash "></i>
                                </div>

                            </div>
                        </div>
                    </div>`
                        );

                        $('#weightAndDiamensionsContainer').append(s);

                    });

                    $('#addMoreSpecifications').on('click', function () {
                        let s = $(
                            `<div class="row mb-3 parentSpecifications">
                        <div class="col-4" >
                            <label class="form-label" for= "Specifications">Title</label>
                            <input type = "text"class = "form-control parentSpecificationsTitle" id = "ecommerce-product-name" placeholder = "Product title" name="specifications[title][]" aria-label = "Specifications Title" />
                        </div>
                        <div class="col-8" >
                            <label class="form-label" for="ecommerce-product-name"> Details </label>
                            <div class="row">
                                <div class='col-11'>
                                    <input type="text" class="form-control parentSpecificationsDescription" id = "ecommerce-product-name" placeholder = "Product title" name="specifications[value][]" aria-label = "Specifications Details" />
                                </div>
                                <div class="col-1 text-danger deleteSpecifications" >
                                    <i class="ti ti-trash "></i>
                                </div>

                            </div>
                        </div>
                    </div>`
                        );

                        $('#specificationsContainer').append(s);

                    });

                    $(document).on('click', '.deleteWeightAndDiamensions', function () {
                        $(this).closest('.parentWeightAndDiamensions').remove();

                    });
                    $(document).on('click', '.deleteSpecifications', function () {
                        $(this).closest('.parentSpecifications').remove();

                    });

                    $('#discout_type').on('change', function () {
                        let type_value = $(this).val();
                        if (type_value == 1) {
                            $('#discount_type_message').text('Discount will be effect in percentage');
                        } else if (type_value == 2) {
                            $('#discount_type_message').text('Discount will be effect in numerical value');
                        } else {
                            $('#discount_type_message').text("Discount price won't affect upon price (No discount upon price is selected)");
                        }
                    });

                    $('#discount_price').on('input', function () {
                        $('.discount_input_field').val($(this).val());
                    })
                    $('#product_price').on('input', function () {
                        $('.base_price_input_field').val($(this).val());
                    })

                    $('#attributes').on('change', function () {
                        var data = $("#attributes").val();
                        $.ajax({
                            url: '{{ route('product.attribute_value.list') }}',
                            type: 'POST',
                            data: {
                                '_token': "{{ csrf_token() }}",
                                'attibute_ids': data,
                            },
                            success: function (response) {
                                $('#attribute_value_container').empty();
                                $('#attribute_value_combination').empty();
                                let s = '<h6 class="mb-2 text-dark">Select Values</h6>';
                                $('#attribute_value_container').append(s);

                                $.each(response.data, function (index, value) {
                                    let s = `<div class="row mb-2">
                                    <div
                                        class="col-4 d-flex align-items-center justify-content-center bg-secondary rounded bg-opacity-50 border border-primary">
                                        <h6 class="mb-0 text-dark">${value.name}</h6>
                                    </div>
                                    <div class="col-8">
                                        <div class="select2-primary">
                                            <input hidden name="attribute_values[${value.id}][attribute_id]" value="${value.id}" />
                                            <select id="${'attribute' + value.id}" name="attribute_values[${value.id}][value][]" class="select2 form-select attribute_values"
                                                data-placeholder="Select Attribute" multiple>`;

                                    $.each(value.values, function (index2, valueName) {
                                        s +=
                                            `<option value="${valueName.name}">${valueName.name}</option>`;
                                    });


                                    s += `</select>
                                        </div>
                                    </div>
                                </div>`
                                    $('#attribute_value_container').append(s);
                                    $(`#${'attribute' + value.id}`).select2();
                                });

                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    });


                    $(document).on('change', '.attribute_values', function () {

                        let attibute_value = [];

                        $.each($('.attribute_values'), function (index, value) {
                            if ($(value).val().length > 0) {
                                attibute_value.push($(value).val());
                            }
                        });
                        let discount_value = $('#discount_price').val();
                        let product_price = $('#product_price').val();

                        let s = `
                <div class="d-flex flex-row align-items-center justify-content-between">
                <h6>Set Variants Price</h6>
                <span id="discount_type_message" class="badge bg-label-danger">Discount price won't affect upon price (No discount upon price is selected)</span>

</div>
                <table class="table">
                            <thead>
                                <tr>
                                <th scope="col">Variant</th>
                                <th scope="col">Price</th>
                                <th scope="col">Discount</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Image</th>
                                <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>`;


                        let combination = cartesian(attibute_value);
                        $('#attribute_value_combination').empty();
                        $(combination).each(function (index, value) {

                            s += `<tr class='combination' data-id='${index}'>`;
                            let variant = '';

                            $(value).each(function (index2, value2) {
                                variant += value2;
                                if (value.length - 1 != index2) {
                                    variant += '-';
                                }
                            });
                            s += `<td style="width: 30%"> <input type="text" class="form-control " id="variant_value_name${index}"   value="${variant}" name="variant[${index}][name]" readonly />  </td>
                    <td> <input type="number" onkeyup="if(value<0) value=0;" class="form-control base_price_input_field" min="0"   id="variant_value_price${index}" value="${product_price}" placeholder="Price" name="variant[${index}][price]" aria-label="Price" /> </td>
                    <td style="width: 15%"> <input type="number" onkeyup="if(value<0) value=0;" class="form-control discount_input_field" value="${discount_value}" min="0"   id="variant_value_discount${index}" placeholder="Price" name="variant[${index}][discount]" aria-label="Discount" /> </td>
                    <td style="width: 10%"><input type="number" onkeyup="if(value<0) value=0;" class="form-control"   id="variant_value_quantity${index}" value="0" placeholder="Quantity" name="variant[${index}][quantity]" aria-label="Quantity" /> </td>
                    <td> <input type="file" class="form-control variation_combination_image"   id="variant_value_image${index}" placeholder="Image" name="variant[${index}][image]" aria-label="Image" /> </td>
                    <td>
                        <label class="switch switch-success" style="margin-right: 10px;">
                            <input type="checkbox" class="switch-input"   id="variant_value_active${index}"  name="variant[${index}][active]" checked aria-label="Active" />
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="ti ti-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="ti ti-x"></i>
                                </span>
                            </span>
                        </label>
                    </td>
                    </tr>`
                        });
                        s += `</tbody>
                    </table>`;

                        $('#attribute_value_combination').append('<p>' + s + '</p>');
                    });

                    // Funtion to get all combinations
                    function cartesian(args) {

                        var r = [],
                            max = args.length - 1;

                        function helper(arr, i) {
                            for (var j = 0, l = args[i].length; j < l; j++) {
                                var a = arr.slice(0); // clone arr
                                a.push(args[i][j]);
                                if (i == max)
                                    r.push(a);
                                else
                                    helper(a, i + 1);
                            }
                        }

                        helper([], 0);
                        return r;
                    }


                    $('#addProduct').click(function () {
                        event.preventDefault();

                        let discountValue = $("#discount_price").val()
                        if (discountValue < 0) {
                            $('.discountValueError').text('Discount value must be greater than 0');
                            return;
                        }

                        var frm = $('#product-add');
                        var formData = new FormData(frm[0]);
                        formData.append('description', $('#description').children().first().html());
                        formData.append('shipping_policy', $('#shipping_policy-description').children().first()
                            .html());
                        formData.append('return_policy', $('#return_policy-description').children().first().html());
                        formData.append('disclaimer', $('#disclaimer-description').children().first().html());

                        $.each($('.variation_combination_image'), function (index, value) {
                            let id = $(value).data('id');
                            if (id) {
                                index = id;
                            }
                            formData.append(`variant[${index}][image]`, $(value).prop('files')[0]);
                        });
                        formData.append(`thumbnail`, $('#darkLogoInput').prop('files')[0] ?? '');

                        loader.show();
                        submitButton.prop('disabled', true);

                        $('.error').empty();


                        $.ajax({
                            url: '{{ route('product.update') }}',
                            type: 'POST',
                            data: formData,
                            contentType: 'multipart/form-data',
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                toastr.success(response.message);
                                console.log(response);
                                window.location.href = "{{ route('product.index') }}";
                            },
                            error: function (error) {
                                if (error.status == 422) {
                                    let response = error.responseJSON;
                                    $('.titleError').text(response.errors?.title ? response.errors
                                        ?.title[0] : '');
                                    $('.videoLinkError').text(response.errors?.video_link ? response.errors
                                        ?.video_link[0] : '');
                                    $('.barcodeError').text(response.errors?.barcode ? response.errors
                                        ?.barcode[0] : '');
                                    $('.descriptionError').text(response.errors?.description ? response
                                        .errors?.description[0] : '');
                                    $('.unitPriceError').text(response.errors?.unit_price ? response
                                        .errors?.unit_price[0] : '');
                                    $('.discountTypeError').text(response.errors?.discount_type ?
                                        response.errors?.discount_type[0] : '');
                                    $('.discountValueError').text(response.errors?.discount_value ?
                                        response.errors?.discount_value[0] : '');
                                    $('.shippingPolicyError').text(response.errors?.shipping_policy ?
                                        response.errors?.shipping_policy[0] : '');
                                    $('.returnPolicyError').text(response.errors?.return_policy ?
                                        response.errors?.return_policy[0] : '');
                                    $('.disclaimerError').text(response.errors?.disclaimer ? response
                                        .errors?.disclaimer[0] : '');
                                    $('.thumbnailImageError').text(response.errors?.thumbnail ? response
                                        .errors
                                        ?.thumbnail[0] : '');

                                    $('.categoryError').text(response.errors?.category ? response.errors
                                        ?.category[0] : '');
                                    $('.unitError').text(response.errors?.unit ? response.errors
                                        ?.unit[0] : '');
                                    $.each(response.errors, function (index, field) {
                                        if (index.includes('variant')) {
                                            let message = field[0].replaceAll(".", " ")
                                            toastr.error(message);
                                        }
                                    });
                                    $.each(response.errors, function (index, field) {
                                        if (index.includes('attribute_values')) {
                                            let message = field[0].replaceAll(".", " ")
                                            console.log(message);
                                            $('.attributeValueError').text(message);
                                            toastr.error(message);
                                        }
                                    });

                                    $.each($('.error'), function (index, value) {
                                        if (!$(value).is(':empty')) {
                                            $('html').animate({
                                                scrollTop: $(value).offset().top - 400
                                            }, 500);
                                            return false;
                                        }
                                    });
                                } else {
                                    toastr.error(error.responseJSON.message);
                                    console.log(error);
                                }
                            },
                            complete: function () {
                                loader.hide();
                                submitButton.prop('disabled', false);
                            }
                        });
                    });


                    let multipleUploader = new MultipleUploader('#multiple-uploader').init({
                        maxUpload: 20, // maximum number of uploaded images
                        maxSize: 2, // in size in mb
                        filesInpName: 'images', // input name sent to backend
                        formSelector: '#product-add', // form selector
                    });


                    $(document).on("click", ".deleteProductImage", function () {

                        let id = $(this).attr("data-id");
                        let element = $(this);
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
                                    url: '{{ route('product.image.destroy') }}',
                                    method: 'POST',
                                    data: {
                                        "_token": "{{ csrf_token() }}",
                                        product_image_id: id,
                                    },
                                    success: function (response) {
                                        $(element).parent('div').remove();
                                        Swal.fire({
                                            icon: response.icon,
                                            title: 'Deleted!',
                                            text: response.text,
                                            customClass: {
                                                confirmButton: 'btn btn-success waves-effect waves-light'
                                            }
                                        });
                                    },
                                    error: function (error) {
                                        console.log(error.responseJSON.message);
                                    }
                                });
                            }
                        });
                    });


                });
                document.addEventListener('DOMContentLoaded', function () {
                    const toggles = document.querySelectorAll('.collapse-toggle');

                    toggles.forEach(toggle => {
                        toggle.addEventListener('click', function () {
                            const targetId = this.getAttribute('data-target');
                            const content = document.getElementById(targetId);
                            const tiPlus = this.querySelector('.c-ti-plus');
                            const tiMinus = this.querySelector('.c-ti-minus');

                            if (content.style.display === 'none' || content.style.display === '') {
                                content.style.display = 'block';
                                tiPlus.style.display = 'none';
                                tiMinus.style.display = 'inline';
                            } else {
                                content.style.display = 'none';
                                tiPlus.style.display = 'inline';
                                tiMinus.style.display = 'none';
                            }
                        });
                    });
                });
            </script>
    @endpush
