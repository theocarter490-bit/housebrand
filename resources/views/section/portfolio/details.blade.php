@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Portfolio').' '._trans('keyword.Details'))

@push('styles')
    <style>
        .portfolio-details-page {
            padding-bottom: 320px;
        }

        .section-picker-card {
            border: 1px solid #d6e0ef;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 10px 25px rgba(36, 56, 99, 0.08);
            overflow: visible;
        }

        .floating-selector {
            position: fixed;
            left: 50%;
            transform: translateX(-50%);
            bottom: 24px;
            z-index: 20;
            width: min(900px, calc(100vw - 24px));
            margin-bottom: 0 !important;
        }

        .section-picker-card .card-header {
            border-bottom: 1px solid #edf0f7;
            background: linear-gradient(120deg, #e9f3ff, #f6fbff);
            padding: 1rem 1.25rem;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .floating-selector .select2-container {
            z-index: 2100;
        }

        .select2-container--open {
            z-index: 2200;
        }

        .section-picker-headline {
            margin-bottom: .35rem;
        }

        .section-picker-card .card-body {
            border-radius: 1rem;
            padding: 1rem 1.25rem 1.25rem;
        }

        .section-picker-actions > [class*='col-'] {
            margin-top: .45rem;
        }

        .section-picker-card .form-select {
            min-height: 44px;
        }

        .section-picker-hint {
            color: #6b7380;
            font-size: .825rem;
            margin: 0;
        }

        .section-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .section-card .card-header {
            background: linear-gradient(120deg, #e9f3ff, #f6fbff);
            border-bottom: 1px solid #edf0f6;
            padding: .9rem 1rem;
        }

        .section-card .card-body {
            padding: 1rem;
        }

        .section-card .deleteSection {
            border-radius: .65rem;
            background: #fff;
            border: 1px solid #f1d1d6;
            top: .55rem !important;
            right: .55rem !important;
            margin: 0;
        }

        .gallaryImageWithDetailsCard,
        .gallaryImageContainer > .card,
        .gallaryImageContainer > .row {
            background: #fff;
            border: 1px solid #edf0f6;
            border-radius: .85rem;
            padding: .6rem .45rem;
        }

        .gallaryImageWithDetailsContainer > .gallaryImageWithDetailsCard,
        .gallaryImageContainer > .card,
        .gallaryImageContainer > .row {
            margin-top: .85rem !important;
            margin-bottom: .85rem !important;
        }

        .gallery-item-row {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center;
            gap: .75rem;
        }

        .gallery-item-row.card {
            flex-direction: row !important;
        }

        .gallery-item-row > .gallery-media-col {
            flex: 0 0 41.6667%;
            max-width: 41.6667%;
            width: 41.6667%;
            margin-bottom: 0 !important;
        }

        .gallery-item-row > .gallery-product-col {
            flex: 0 0 58.3333%;
            max-width: 58.3333%;
            width: 58.3333%;
            margin-bottom: 0 !important;
        }

        .branner_input_placeholder {
            border: 1px solid rgba(255, 255, 255, .95);
            border-radius: .75rem;
            min-width: 124px;
            min-height: 124px;
            background: rgba(255, 255, 255, .10);
            color: #fff;
            padding: .5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, .35);
        }

        .preview_img {
            border-radius: .5rem;
            object-fit: cover;
        }

        .image-media-col {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            min-height: 230px;
            background: #f8fbff;
            border-radius: .75rem;
            overflow: hidden;
            padding: .5rem;
            cursor: pointer;
        }

        .image-media-col .branner_input_placeholder {
            position: absolute;
            inset: .55rem;
            z-index: 4;
            opacity: 0;
            min-width: 0;
            min-height: 0;
            width: auto;
            height: auto;
            transition: opacity .2s ease;
            pointer-events: none;
        }

        .image-media-col input[type='file'] {
            position: absolute !important;
            inset: 0;
            opacity: 0;
            z-index: 6;
            cursor: pointer;
        }

        .image-media-col .preview_img {
            width: 100%;
            height: 100%;
            max-height: 280px;
            object-fit: cover;
            z-index: 2;
            transition: filter .2s ease, transform .2s ease;
        }

        .gallery-item-row .image-media-col,
        .section[data-id='banner_image'] .image-media-col {
            min-height: 230px;
            height: 230px;
        }

        .gallery-item-row .preview_img,
        .section[data-id='banner_image'] .preview_img {
            height: 100% !important;
            width: auto !important;
            max-width: 100%;
            object-fit: contain;
        }

        .gallaryImageWithDetailsCard .preview_img {
            height: 100% !important;
            width: auto !important;
            max-width: 100%;
            object-fit: contain;
        }

        .image-media-col::after {
            content: '';
            position: absolute;
            inset: .5rem;
            background: rgba(15, 23, 42, .42);
            backdrop-filter: blur(2px);
            opacity: 0;
            z-index: 3;
            border-radius: .6rem;
            transition: opacity .2s ease;
            pointer-events: none;
        }

        .image-media-col:hover::after {
            opacity: 1;
        }

        .image-media-col:hover .branner_input_placeholder {
            opacity: 1;
        }

        .image-media-col:hover .preview_img {
            filter: brightness(.75);
        }

        .product-select-col,
        .productListTags {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 991.98px) {
            .floating-selector {
                bottom: 12px;
                width: calc(100vw - 16px);
            }
        }
    </style>
@endpush

@section('content')

    <div class="app-ecommerce portfolio-details-page" id="content-wrapper">
        {!! breadcrumb(_trans('keyword.Portfolio').' '._trans('keyword.Details'),['section/portfolioAndInspiration/1/index'=>_trans('keyword.Portfolio').' & '._trans('keyword.Inspiration'),'portfolio'=>_trans('keyword.Portfolio').' '._trans('keyword.Details')]) !!}

        <div class="card mb-4 section-picker-card floating-selector">
            <div class="card-header">
                <h5 class="card-title section-picker-headline">{{_trans('keyword.Select Section For Portfolio Details')}}</h5>
                <p class="section-picker-hint">Choose a section type and click Add Section to build your portfolio
                    layout.</p>
            </div>
            <div class="row card-body justify-content-center align-items-center ps-1 section-picker-actions">
                <div class="col-lg-6 col-md-12 mb-3 mb-lg-0 ecommerce-select2-dropdown">
                    <select id="design_input" class="select2 form-select" data-placeholder="Select Section">
                        <option value="">{{_trans('keyword.Select').' '._trans('keyword.Section')}} </option>
                        <option value="description">{{_trans('keyword.Description')}}</option>
                        <option value="image_gallary">{{_trans('keyword.Images Gallery')}}</option>
                        <option value="image_gallary_with_details">{{_trans('keyword.Gallery With Details')}}</option>
                        <option value="banner_image">{{_trans('keyword.Landscape Banner')}}</option>
                    </select>
                </div>
                @if(hasPermission('add_portfolio_inspiration_section'))
                    <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg-0">
                        <button class="btn btn-primary w-100" id="add_section"><i
                                class="ti ti-plus ti-xs me-0 me-sm-2"></i>{{_trans('keyword.Add').' '._trans('keyword.Section')}}
                        </button>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <button type="button" id="save" class="btn btn-success w-100">{{_trans('keyword.Save')}}
                            <span class="loader"></span>
                        </button>
                    </div>
                @endif
            </div>
        </div>


        @foreach ($section->details as $detail)
            @if ($detail->section_type == 'image_gallary_with_details')
                <div class="card mb-4 section section-card" data-id="image_gallary_with_details">
                    <div class="card-header d-flex flex-row justify-content-start align-items-center">
                        <h5 class="card-title mb-0">{{_trans('keyword.Image Gallary With Details')}}</h5>
                        <input type="text" value="{{ $detail->id }}" hidden>

                        <button type="button" class="btn btn-primary ms-2 image_gallary_with_description_add_button"><i
                                class="ti ti-plus ti-xs"></i></button>

                    </div>
                    <div class="row card-body justify-content-center align-items-center">
                        <div class="col-12 border-end gallaryImageWithDetailsContainer">
                            @foreach ($detail->items as $item)
                                <div class="row position-relative gallaryImageWithDetailsCard mt-2 border-bottom pb-3"
                                     style="min-height: 150px">
                                    <div class="col-12 col-md-4 border-end mb-3 mb-md-0">
                                        <label for="">{{_trans('keyword.Title')}}</label>
                                        <input class="form-control mb-4" value="{{ $item->title }}" name='title'
                                               id=""/>
                                        <label
                                            for="">{{_trans('keyword.Description').' / '._trans('keyword.Amount')}}</label>
                                        <input class="form-control" value="{{ $item->description }}" name='description'
                                               id=""/>
                                    </div>
                                    <div
                                        class="col-12 col-md-4 border-end d-flex justify-content-center align-items-center position-relative h-100 mb-3 mb-md-0 image-media-col">
                                        <div
                                            class="branner_input_placeholder d-flex flex-column justify-content-center align-items-center">
                                            <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                            <span
                                                style="font-size:1rem !important">{{_trans('keyword.Input').' '._trans('keyword.Image')}}</span>
                                        </div>
                                        <input name='image'
                                               class="opacity-0 position-absolute top-50 start-50 translate-middle p-3 old_value"
                                               type="file" onchange="loadFile(event)"/>
                                        <input type="text" name="id" value="{{ $item->id }}" hidden>
                                        <img id='preview_img' class=" preview_img "
                                             style="height: 100%; width: auto; max-width:100%"
                                             src="{{ getFilePath($item->image) }}" alt="Current profile photo"/>
                                    </div>
                                    <div class="col-12 col-md-4 align-self-center productListTags product-select-col"
                                         style="margin-top: 10px">
                                        @php
                                            $selectedProductsList = $item->products->map(function ($product) {
                                                return [
                                                    'value' => $product->id,
                                                    'name' => $product->name,
                                                    'unit_price' => getPriceFormat($product->unit_price),
                                                    'thumbnail_img' => getFilePath($product->thumbnail_img),
                                                ];
                                            });
                                        @endphp
                                        <label for="TagifyUserList" class="form-label">Products List</label>
                                        <input
                                            id="TagifyUserList"
                                            name="TagifyUserList"
                                            class="TagifyUserList form-control"
                                            value='@php echo json_encode($selectedProductsList) @endphp'
                                        />
                                    </div>
                                    <button title="Delete" type="button"
                                            class="btn text-danger position-absolute deleteSection"
                                            style="right: 0%; width:50px;"><i class="ti ti-trash"></i></button>


                                </div>
                            @endforeach
                        </div>
                        <div class="col-3">
                        </div>
                    </div>
                    <button type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%"><i
                            class="ti ti-trash"></i></button>
                </div>
            @endif
            @if ($detail->section_type == 'description')
                @foreach ($detail->items as $item)
                    <div class="card mb-4 section section-card" data-id="description">
                        <input type="text" value="{{ $detail->id }}" hidden>
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{_trans('keyword.Description')}}</h5>
                        </div>
                        <div class="row card-body justify-content-center align-items-center">
                            <div class="form-group">
                                <label><strong>Description :</strong></label>

                                <textarea class="ckeditor form-control"
                                          name="descriptionExits{{$item->id}}"> {{ $item->description }}</textarea>
                                <span class="text-danger descriptionError error"></span>
                            </div>
                        </div>
                        <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection"
                                style="right: 0%"><i class="ti ti-trash"></i></button>
                    </div>
                @endforeach
            @endif
            @if ($detail->section_type == 'image_gallary')
                <div class="card mb-4 section section-card" data-id="image_gallary">
                    <div class="card-header d-flex flex-row justify-content-start align-items-center">
                        <h5 class="card-title mb-0">{{_trans('keyword.Image Gallary')}}</h5>
                        <input type="text" value="{{ $detail->id }}" hidden>

                        <button type="button" class="btn btn-primary ms-2 image_gallary_add_button"><i
                                class="ti ti-plus ti-xs"></i></button>

                    </div>
                    <div class="row card-body justify-content-center align-items-center">
                        <div class="col-12 gallaryImageContainer">
                            @foreach ($detail->items as $item)
                                <div class="row position-relative mb-2 border-bottom p-1 card h-auto gallery-item-row"
                                     style="min-height: 150px; width: 100%">
                                    <div
                                        class="col-5 col-md-5 d-flex justify-content-center align-items-center position-relative image-media-col gallery-media-col"
                                        style="min-height: 150px">

                                        <div
                                            class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                            <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                            <span
                                                style="font-size:1rem !important">{{_trans('keyword.Input').' '._trans('keyword.Image')}} </span>
                                        </div>
                                        <input name="image_gallary[${count}]"
                                               class="opacity-0 position-absolute top-50 start-50 translate-middle p-5 old_value"
                                               type="file" onchange="loadFile(event)"/>
                                        <img class="ms-2 preview_img "
                                             style="height: 100%; width: auto; max-width:100%" src="{{ getFilePath($item->image) }}"
                                             alt="Current profile photo"/>
                                    </div>
                                    <div
                                        class="col-7 col-md-7 align-self-center productListTags product-select-col gallery-product-col">
                                        <label for="TagifyUserList" class="form-label">Products List</label>
                                        @php
                                            $selectedProductsList = $item->products->map(function ($product) {
                                                return [
                                                    'value' => $product->id,
                                                    'name' => $product->name,
                                                    'unit_price' => getPriceFormat($product->unit_price),
                                                    'thumbnail_img' => getFilePath($product->thumbnail_img),
                                                ];
                                            });
                                        @endphp

                                        <input
                                            id="TagifyUserList"
                                            name="TagifyUserList"
                                            class="TagifyUserList form-control"
                                            value='@php echo json_encode($selectedProductsList) @endphp'
                                        />
                                    </div>
                                    <input type="text" value="{{ $item->id }}" hidden>

                                    <button title="Delete" type="button"
                                            class="btn text-danger position-absolute deleteSection"
                                            style="right: 0%;width:50px;"><i class="ti ti-trash"></i></button>
                                </div>
                            @endforeach

                        </div>
                        <div class="col-4">

                        </div>

                    </div>
                    <button type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%"><i
                            class="ti ti-trash"></i></button>

                </div>
            @endif
            @if ($detail->section_type == 'banner_image')
                @foreach ($detail->items as $item)
                    <div class="card mb-4 section section-card" data-id="banner_image">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{_trans('keyword.Landscape Banner')}}</h5>
                            <input type="text" value="{{ $detail->id }}" hidden>
                        </div>
                        <div class="row card-body justify-content-center align-items-center" style="min-height: 200px">
                            <div
                                class="col-12 col-md-5 border-end d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0 image-media-col"
                                style="min-height: 150px">
                                <div
                                    class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                    <span
                                        style="font-size:1rem !important">{{_trans('keyword.Input').' '._trans('keyword.Image')}}</span>
                                </div>
                                <input name="banner[${count}]"
                                       class="opacity-0 position-absolute top-50 start-50 translate-middle p-5 old_value"
                                       type="file" onchange="loadFile(event)"/>
                                <img id='preview_img' class="ms-2 preview_img "
                                     style="height: 100%; width: auto; max-width:100%"
                                     src="{{ getFilePath($item->image) }}" alt="Current profile photo"/>
                            </div>
                            <div class="col-12 col-md-7 align-self-center productListTags product-select-col"
                                 style="margin-top: 10px">
                                @php
                                    $selectedProductsList = $item->products->map(function ($product) {
                                        return [
                                            'value' => $product->id,
                                            'name' => $product->name,
                                            'unit_price' => getPriceFormat($product->unit_price),
                                            'thumbnail_img' => getFilePath($product->thumbnail_img),
                                        ];
                                    });
                                @endphp
                                <label for="TagifyUserList" class="form-label">Products List</label>
                                <input
                                    id="TagifyUserList"
                                    name="TagifyUserList"
                                    class="TagifyUserList form-control"
                                    value='@php echo json_encode($selectedProductsList) @endphp'
                                />
                            </div>
                        </div>
                        <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection"
                                style="right: 0%"><i class="ti ti-trash"></i></button>
                    </div>
                @endforeach
            @endif
        @endforeach


    </div>
@endsection


@push('scripts')
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        let count = 0;

        $('#add_section').on('click', function () {
            let value = $('#design_input').val();
            let s = '';
            if (value == 'description') {
                s = descriptionHTML();
                $('#content-wrapper').append(s);
                const editor = document.querySelector(`.ckeditor${count}`);
                if (editor) {
                    CKEDITOR.replace(editor);
                }

            } else if (value == 'banner_image') {
                s = bannerHTML();
                $('#content-wrapper').append(s);
                tagifyProductList();
            } else if (value == 'image_gallary') {
                s = imageGallaryHTML();
                $('#content-wrapper').append(s);
                tagifyProductList();
            } else if (value == 'image_gallary_with_details') {
                s = imageWithDetailsHTML();
                $('#content-wrapper').append(s);
                tagifyProductList();
            }

            count++;

        });

        $(document).on("click", ".image_gallary_add_button", function () {
            $(this).parent().siblings('div').eq(0).children('.gallaryImageContainer').append(
                imageGallaryImageContainerDesignHTML());
            tagifyProductList();
        });
        $(document).on("click", ".image_gallary_with_description_add_button", function () {
            $(this).parent().siblings('div').eq(0).children('.gallaryImageWithDetailsContainer').append(
                imageGallaryWithDetailsContainerDesignHTML());
            tagifyProductList();
        });


        function descriptionHTML() {
            return `<div class="card mb-4 section section-card" data-id="description">
            <div class="card-header">
                <h5 class="card-title mb-0">Description</h5>
            </div>
            <div class="row card-body justify-content-center align-items-center">
                <div>
                    <label class="form-label">Description</label>
                    <textarea class="ckeditor ckeditor${count} form-control"
                                              name="description${count}"></textarea>
                    <span class="text-danger descriptionError error"></span>
                </div>
            </div>
            <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%"><i class="ti ti-trash"></i></button>
        </div>`;
        }

        function bannerHTML() {
            return `<div class="card mb-4 section section-card" data-id="banner_image">
            <div class="card-header">
                <h5 class="card-title mb-0">Landscape Banner</h5>
                <input type="text" value="" hidden>
            </div>
            <div class="row card-body justify-content-center align-items-center" style="min-height: 200px">
                <div class="col-12 col-md-5 border-end d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0 image-media-col" style="min-height: 150px">
                    <div class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                        <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                        <span style="font-size:1rem !important">Input image</span>
                    </div>
                    <input name="banner[${count}]" class="opacity-0 position-absolute top-50 start-50 translate-middle p-5" type="file" onchange="loadFile(event)" />
                    <img id='preview_img' class="ms-2 preview_img relative" style="height: 100%; width: auto; max-width:100%"
                        src="{{ asset('assets/img/placeholder/placeholder.png') }}"
                        alt="Current profile photo" />
                </div>
                <div class="col-12 col-md-7 align-self-center product-select-col">
                    <label for="TagifyUserList" class="form-label">Products List</label>
                    <input
                        id="TagifyUserList"
                        name="TagifyUserList[${count}]"
                        class="TagifyUserList form-control"/>
                </div>
            </div>
            <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%"><i class="ti ti-trash"></i></button>
        </div>`;
        }

        function imageGallaryHTML() {
            return `<div class="card mb-4 section section-card" data-id="image_gallary">
            <div class="card-header d-flex flex-row justify-content-start align-items-center">
                <h5 class="card-title mb-0">Image Gallery</h5>
                <input type="text" value="" hidden>
                <button type="button" class="btn btn-primary ms-2 image_gallary_add_button"><i class="ti ti-plus ti-xs"></i></button>

            </div>
            <div class="row card-body justify-content-center align-items-center">
                <div class="col-12 gallaryImageContainer">
                    <div class="row position-relative card h-auto gallery-item-row" style="min-height: 150px">
                        <div class="col-5 col-md-5 d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0 image-media-col gallery-media-col"
                            style="min-height: 150px">
                            <div
                                class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                <span style="font-size:1rem !important">Input image</span>
                            </div>
                            <input name="image_gallary[${count}]"
                                class="opacity-0 position-absolute top-50 start-50 translate-middle p-5" type="file"
                                onchange="loadFile(event)" />
                            <img class="ms-2 preview_img" style="height: 100%; width: auto; max-width:100%;"
                                src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Current profile photo" />
                        </div>
                        <div class="col-7 col-md-7 align-self-center product-select-col gallery-product-col">
                                <label for="TagifyUserList" class="form-label">Products List</label>
                                <input
                                    id="TagifyUserList"
                                    name="TagifyUserList"
                                    class="TagifyUserList form-control"/>
                            </div>
                        <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%;width:50px;"><i class="ti ti-trash"></i></button>
                    </div>

                </div>
                <div class="col-4">

                </div>

            </div>
            <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%"><i class="ti ti-trash"></i></button>

        </div>`;
        }

        function imageGallaryImageContainerDesignHTML() {
            return `<div class="row mt-2 position-relative card h-auto gallery-item-row" style="min-height: 150px">
                        <div class="col-5 col-md-5 d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0 image-media-col gallery-media-col"
                            style="min-height: 150px">
                            <div
                                class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                <span  style="font-size:1rem !important">Input image</span>
                            </div>
                            <input name="image_gallary[${count}]"
                                class="opacity-0 position-absolute top-50 start-50 translate-middle p-5" type="file"
                                onchange="loadFile(event)" />
                            <img id='preview_img' class="ms-2 preview_img" style="height: 100%; width: auto; max-width:100%"
                                src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Current profile photo" />
                        </div>
                        <div class="col-7 col-md-7 align-self-center product-select-col gallery-product-col">
                                <label for="TagifyUserList" class="form-label">Products List</label>
                                <input
                                    id="TagifyUserList"
                                    name="TagifyUserList"
                                    class="TagifyUserList form-control"/>
                            </div>

                        <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%;width:50px;"><i class="ti ti-trash"></i></button>

                    </div>`;
        }

        function imageWithDetailsHTML() {
            return `<div class="card mb-4 section section-card" data-id="image_gallary_with_details">
                <div class="card-header d-flex flex-row justify-content-start align-items-center">
                    <h5 class="card-title mb-0">Image Gallary With Details</h5>
                    <input type="text" name="id" value="" hidden>

                    <button type="button" class="btn btn-primary ms-2 image_gallary_with_description_add_button"><i
                            class="ti ti-plus ti-xs"></i></button>

                </div>
                <div class="row card-body justify-content-center align-items-center">
                    <div class="col-12 border-end gallaryImageWithDetailsContainer">
                        <div class="row position-relative gallaryImageWithDetailsCard pb-3" style="min-height: 150px">
                            <div class="col-12 col-md-4 border-end mb-3 mb-md-0">
                                <label for="">Title</label>
                                <input class="form-control mb-4" name='title' id=""/>
                                <label for="">Description/Amount</label>
                                <input class="form-control" name='description' id=""/>
                            </div>
                            <div class="col-12 col-md-4 border-end d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0 image-media-col"
                                style="height: 100%">
                                <div
                                    class="branner_input_placeholder d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                    <span  style="font-size:1rem !important">Input image</span>
                                </div>
                                <input name='image'
                                    class="opacity-0 position-absolute top-50 start-50 translate-middle p-3" type="file"
                                    onchange="loadFile(event)" />
                                    <input type="text" name="id" value="" hidden>

                                <img id='preview_img' class=" preview_img" style="height: 100%; width: auto; max-width:100%"
                                    src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Current profile photo" />
                            </div>
                            <div class="col-12 col-md-4 align-self-center product-select-col">
                                <label for="TagifyUserList" class="form-label">Products List</label>
                                <input
                                    id="TagifyUserList"
                                    name="TagifyUserList"
                                    class="TagifyUserList form-control"/>
                            </div>
                            <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection"  style="top:0%; right: 0%; width:50px;"><i class="ti ti-trash"></i></button>
                        </div>
                    </div>
                    <div class="col-3">
                    </div>
                </div>
                <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%"><i class="ti ti-trash"></i></button>
            </div>`;
        }

        function imageGallaryWithDetailsContainerDesignHTML() {
            return `<div class="row position-relative gallaryImageWithDetailsCard pb-3" style="min-height: 150px">
                            <div class="col-12 col-md-4 border-end mb-3 mb-md-0">
                                <label for="">Title</label>
                                <input class="form-control mb-4" name='title' id=""/>
                                <label for="">Description/Amount</label>
                                <input class="form-control" name='description' id=""/>
                            </div>
                            <div class="col-12 col-md-4 border-end d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0 image-media-col"
                                style="height: 100%">
                                <div
                                    class="branner_input_placeholder  d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                    <span  style="font-size:1rem !important">Input image</span>
                                </div>
                                <input name='image'
                                    class="opacity-0 position-absolute top-50 start-50 translate-middle p-3" type="file"
                                    onchange="loadFile(event)" />
                                    <input type="text" name="id" value="" hidden>

                                <img id='preview_img' class=" preview_img" style="height: 100%; width: auto; max-width:100%"
                                    src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Current profile photo" />
                            </div>
                            <div class="col-12 col-md-4 align-self-center product-select-col">
                                <label for="TagifyUserList" class="form-label">Products List</label>
                                <input
                                    id="TagifyUserList"
                                    name="TagifyUserList"
                                    class="TagifyUserList form-control"/>
                            </div>
                            <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection"  style="top:0%; right: 0%; width:50px;"><i class="ti ti-trash"></i></button>

                        </div>`;
        }


        $(document).on("click", ".deleteSection", function () {

            let button = $(this);
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
                        text: 'Section Removed',
                        customClass: {
                            confirmButton: 'btn btn-success waves-effect waves-light'
                        }
                    });
                    $(button).parent().remove();
                }
            });
        });

        var loadFile = function (event) {

            var input = event.target;
            var file = input.files[0];
            var type = file.type;

            var mediaCol = event.currentTarget.closest('.image-media-col');
            var output = mediaCol ? mediaCol.querySelector('.preview_img') : null;

            if (!output && event.currentTarget.parentNode.nextElementSibling) {
                output = event.currentTarget.parentNode.nextElementSibling.children[0];
            }

            if (!output) {
                return;
            }

            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function () {
                URL.revokeObjectURL(output.src) // free memory
            }
            event.currentTarget.blur();
        };

        $('#save').on('click', function () {
            let isValid = true;
            $('.section').each(function (index, section) {
                let sectionName = $(section).data('id');
                if (sectionName == 'description') {
                    // No specific validation for description section
                } else if (sectionName == 'banner_image') {
                    let $imageInput = $($(section).find('input')[1]);
                    let image = $imageInput.prop('files')[0];
                    if (!$imageInput.hasClass('old_value')) {
                        if (!image) {
                            isValid = false;
                            if ($(section).find('#preview_img').next('.text-danger').length === 0) {
                                $(section).find('#preview_img').after('<div class="text-danger position-absolute top-0 left-0 w-100 h-100 z-3">Image is required</div>');
                            }
                        }
                    }
                } else if (sectionName == 'image_gallary') {
                    let cards = $(section).find('.card');
                    cards.each(function (key, card) {
                        let $imageInput = $(card).find('input[type="file"]');
                        let image = $imageInput.prop('files')[0];
                        if (!$imageInput.hasClass('old_value')) {
                            if (!image) {
                                isValid = false;
                                if ($(card).find('#preview_img').next('.text-danger').length === 0) {
                                    $(card).find('#preview_img').after('<div class="text-danger">Image is required</div>');
                                }
                            }
                        }
                    });
                } else if (sectionName == 'image_gallary_with_details') {
                    let cards = $(section).find('.gallaryImageWithDetailsCard');
                    cards.each(function (cardKey, card) {
                        let title = $(card).find('input[name="title"]').val();
                        let $imageInput = $(card).find('input[name="image"]');
                        let image = $imageInput.prop('files')[0];

                        if (!title) {
                            isValid = false;
                            if ($(card).find('input[name="title"]').prev('.text-danger').length === 0) {
                                $(card).find('input[name="title"]').before('<div class="text-danger">Title is required</div>');
                            }
                        }

                        if (!$imageInput.hasClass('old_value')) {
                            if (!image) {
                                isValid = false;
                                if ($(card).find('#preview_img').next('.text-danger').length === 0) {
                                    $(card).find('#preview_img').after('<div class="text-danger">Image is required</div>');
                                }
                            }
                        }
                    });
                }
            });

            // return;

            if (!isValid) {
                return false;
            }

            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");

            $('.section').each(function (index, section) {
                let sectionName = $(section).data('id');
                if (sectionName == 'description') {
                    let name = $(section).find('.ckeditor')[0].getAttribute('name');
                    let description = CKEDITOR.instances[name].getData();
                    let sectionID = $($(section).find('input')[0]).val() ?? null;
                    formData.append(`section[${index}][id]`, sectionID);
                    formData.append(`section[${index}][name]`, 'description');
                    formData.append(`section[${index}][description]`, description);
                } else if (sectionName == 'banner_image') {
                    let sectionID = $($(section).find('input')[0]).val() ?? null;
                    formData.append(`section[${index}][id]`, sectionID);
                    let image = $($(section).find('input')[1]).prop('files')[0] ?? '';
                    let productIds = $($(section).find('input')[2]).val() ?? [];
                    formData.append(`section[${index}][name]`, 'banner_image');
                    formData.append(`section[${index}][image]`, image);
                    formData.append(`section[${index}][productIds]`, productIds);
                } else if (sectionName == 'image_gallary') {
                    let cards = $(section).find('.card');
                    let sectionID = $($(section).find('input')[0]).val() ?? null;
                    formData.append(`section[${index}][sectionID]`, sectionID);
                    formData.append(`section[${index}][name]`, 'image_gallary');
                    cards.each(function (key, card) {
                        let data = $(card).find('input');
                        let image = $(data[0]).prop('files')[0] ?? '';
                        let productIds = $(data[1]).val() ?? [];
                        formData.append(`section[${index}][image][${key}]`, image);
                        formData.append(`section[${index}][productIds][${key}]`, productIds);
                        formData.append(`section[${index}][id][${key}]`, $(data[2]).val() ?? null);
                    });
                } else if (sectionName == 'image_gallary_with_details') {
                    formData.append(`section[${index}][name]`, 'image_gallary_with_details');
                    formData.append(`section[${index}][id]`, $($(section).find('input')[0]).val());
                    let cards = $(section).find('.gallaryImageWithDetailsCard');
                    cards.each(function (cardKey, card) {
                        let inputs = $(card).find('input');
                        inputs.each(function (key, input) {
                            var name = $(input).attr('name');
                            if (name == 'title') {
                                formData.append(`section[${index}][cards][${cardKey}][title]`, $(input).val());
                            }
                            if (name == 'description') {
                                formData.append(`section[${index}][cards][${cardKey}][description]`, $(input).val());
                            }
                            if (name == 'image') {
                                formData.append(`section[${index}][cards][${cardKey}][image]`, $(input).prop('files')[0] ?? '');
                            }
                            if (name == 'id') {
                                formData.append(`section[${index}][cards][${cardKey}][id]`, $(input).val() ?? null);
                            }
                            if (name == 'TagifyUserList') {
                                formData.append(`section[${index}][cards][${cardKey}][productIds]`, $(input).val() ?? []);
                            }
                        });
                    });
                }
            });

            loader.show();
            submitButton.prop('disabled', true);

            $.ajax({
                url: '{{ route('section.portfolioAndInspiration.details.store', $section->id) }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    window.location.href = "{{ route('section.portfolioAndInspiration.index', 1) }}";
                },
                error: function (error) {

                    // Handle error
                },
                complete: function () {
                    loader.hide();
                    submitButton.prop('disabled', false);
                }
            });
        });


        const productList = @php echo json_encode($products) @endphp;

        function tagifyProductList() {
            const TagifyUserListEl = document.querySelectorAll(".TagifyUserList");

            if (TagifyUserListEl.length) {
                function tagTemplate(tagData) {
                    return `
        <tag title="${tagData.name}"
          contenteditable='false'
          spellcheck='false'
          tabIndex="-1"
          class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ''}"
          ${this.getAttributes(tagData)}
        >
          <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
          <div>
            <div class='tagify__tag__avatar-wrap'>
              <img onerror="this.style.visibility='hidden'" src="${tagData.thumbnail_img}">
            </div>
            <span class='tagify__tag-text'>${tagData.name}</span>
          </div>
        </tag>
      `;
                }

                function suggestionItemTemplate(tagData) {
                    return `
        <div ${this.getAttributes(tagData)}
          class='tagify__dropdown__item align-items-center ${tagData.class ? tagData.class : ''}'
          tabindex="0"
          role="option"
        >
          ${
                        tagData.thumbnail_img
                            ? `<div class='tagify__dropdown__item__avatar-wrap'>
              <img onerror="this.style.visibility='hidden'" src="${tagData.thumbnail_img}">
            </div>`
                            : ''
                    }
          <div class="fw-medium">${tagData.name}</div>
            <span>${tagData.unit_price}</span>
        </div>
      `;
                }

                function dropdownHeaderTemplate(suggestions) {
                    return `
            <div class="${this.settings.classNames.dropdownItem} ${this.settings.classNames.dropdownItem}__addAll">
                <strong>${this.value.length ? `Select remaining` : 'Select All'}</strong>
                <span>${suggestions.length} products</span>
            </div>
        `;
                }

                $(TagifyUserListEl).each(function (index, element) {

                    if ($(element).siblings().length > 1) {

                        return;
                    }

                    // initialize Tagify on the above input node reference
                    TagifyUserList = new Tagify(element, {
                        tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
                        enforceWhitelist: true,
                        skipInvalid: true, // do not remporarily add invalid tags
                        dropdown: {
                            closeOnSelect: false,
                            enabled: 0,
                            classname: 'users-list',
                            searchKeys: ['name'] // very important to set by which keys to search for suggesttions when typing
                        },
                        templates: {
                            tag: tagTemplate,
                            dropdownItem: suggestionItemTemplate,
                            dropdownHeader: dropdownHeaderTemplate
                        },
                        whitelist: productList
                    });
                });


                // attach events listeners
                TagifyUserList.on('dropdown:select', onSelectSuggestion) // allows selecting all the suggested (whitelist) items
                    .on('edit:start', onEditStart); // show custom text in the tag while in edit-mode

                function onSelectSuggestion(e) {
                    // custom class from "dropdownHeaderTemplate"
                    if (e.detail.elm.classList.contains(`${TagifyUserList.settings.classNames.dropdownItem}__addAll`))
                        TagifyUserList.dropdown.selectAll();
                }

                function onEditStart({detail: {tag, data}}) {
                    TagifyUserList.setTagTextNode(tag, `${data.name}`);
                }
            }

        }

        tagifyProductList();

    </script>
@endpush
