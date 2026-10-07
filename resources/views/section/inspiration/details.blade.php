@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Inspiration').' '._trans('keyword.Details'))

@section('content')

    <div class="app-ecommerce" id="content-wrapper">
        {!! breadcrumb(_trans('keyword.Inspiration').' '._trans('keyword.Details'),['section/portfolioAndInspiration/2/index'=>_trans('keyword.Portfolio').' & '._trans('keyword.Inspiration'),'inspiration'=>_trans('keyword.Inspiration').' '._trans('keyword.Details')]) !!}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{_trans('keyword.Select Section For Inspiration Details')}}</h5>
            </div>
            <div class="row card-body justify-content-center align-items-center">
                <div class="col-sm-8 ecommerce-select2-dropdown">
                    <select id="design_input" class="select2 form-select" data-placeholder="Select Section">
                        <option value="">{{_trans('keyword.Select').' '._trans('keyword.Section')}} </option>
                        <option value="description">{{_trans('keyword.Description')}}</option>
                        <option value="image_gallary">{{_trans('keyword.Images Gallery')}}</option>
                        <option value="image_gallary_with_details">{{_trans('keyword.Gallery With Details')}}</option>
                        <option value="banner_image">{{_trans('keyword.Landscape Banner')}}</option>
                    </select>
                </div>
                @if(hasPermission('add_portfolio_inspiration_section'))
                    <div class="col-sm-4 mt-sm-0 mt-3">
                        <button class="btn btn-primary" id="add_section"><i
                                class="ti ti-plus ti-xs me-0 me-sm-2"></i>{{_trans('keyword.Add').' '._trans('keyword.Section')}}
                        </button>
                    </div>
                @endif
            </div>
        </div>


        @foreach ($section->details as $detail)
            @if ($detail->section_type == 'image_gallary_with_details')
                <div class="card mb-4 section" data-id="image_gallary_with_details">
                    <div class="card-header d-flex flex-row justify-content-start align-items-center">
                        <h5 class="card-title mb-0">{{_trans('keyword.Image Gallary With Details')}}</h5>
                        <input type="text" value="{{ $detail->id }}" hidden>

                        <button type="button" class="btn btn-primary ms-2 image_gallary_with_description_add_button"><i
                                class="ti ti-plus ti-xs"></i></button>

                    </div>
                    <div class="row card-body justify-content-center align-items-center">
                        <div class="col-12 border-end gallaryImageWithDetailsContainer">
                            @foreach ($detail->items as $item)
                                <div class="row position-relative gallaryImageWithDetailsCard mt-2 border-bottom pb-3 pb-md-0"
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
                                        class="col-6 col-md-2 border-end d-flex justify-content-center align-items-center position-relative" style="min-height: 150px;">
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
                                    </div>
                                    <div class="col-6 col-md-2 border-end" style="min-height: 150px;">
                                        <img id='preview_img' class=" preview_img "
                                             style="height: 140px; max-Width:100%"
                                             src="{{ getFilePath(@$item->image) }}" alt="Product Image"/>
                                    </div>
                                    <div class="col-12 col-md-4 align-self-center productListTags" style="margin-top: 10px">
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
                    <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection"
                            style="right: 0%"><i
                            class="ti ti-trash"></i></button>
                </div>
            @endif
            @if ($detail->section_type == 'description')
                @foreach ($detail->items as $item)
                    <div class="card mb-4 section" data-id="description">
                        <input type="text" value="{{ $detail->id }}" hidden>
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{_trans('keyword.Description')}}</h5>
                        </div>
                        <div class="row card-body justify-content-center align-items-center">


                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><strong>Description :</strong></label>

                                    <textarea class="ckeditor form-control"
                                              name="descriptionExits{{$item->id}}"> {{ $item->description }}</textarea>
                                    <span class="text-danger descriptionError error"></span>
                                </div>
                            </div>
                        </div>
                        <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection"
                                style="right: 0%"><i class="ti ti-trash"></i></button>
                    </div>
                @endforeach
            @endif
            @if ($detail->section_type == 'image_gallary')
                <div class="card mb-4 section" data-id="image_gallary">
                    <div class="card-header d-flex flex-row justify-content-start align-items-center">
                        <h5 class="card-title mb-0">{{_trans('keyword.Image Gallary')}}</h5>
                        <input type="text" value="{{ $detail->id }}" hidden>

                        <button type="button" class="btn btn-primary ms-2 image_gallary_add_button"><i
                                class="ti ti-plus ti-xs"></i></button>

                    </div>
                    <div class="row card-body justify-content-center align-items-center">
                        <div class="col-12 border-end gallaryImageContainer">
                            @foreach ($detail->items as $item)
                                <div class="row  position-relative mb-2 border-bottom p-1 card h-auto"
                                     style="min-height: 150px; width: 100%">
                                    <div
                                        class="col-12 col-md-4 border-end d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0"
                                        style="min-height: 150px;">

                                        <div
                                            class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                            <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                            <span
                                                style="font-size:1rem !important">{{_trans('keyword.Input').' '._trans('keyword.Image')}} </span>
                                        </div>
                                        <input name="image_gallary[${count}]"
                                               class="opacity-0 position-absolute top-50 start-50 translate-middle p-5 old_value"
                                               type="file" onchange="loadFile(event)"/>
                                    </div>
                                    <div class="col-12 col-md-4 border-end mb-3 mb-md-0" style="min-height: 150px;">
                                        <img id='preview_img' class="ms-2 preview_img "
                                             style="height: 140px; max-Width:100%" src="{{ getFilePath(@$item->image) }}" alt="Product Image"/>
                                    </div>
                                    <div class="col-12 col-md-4 align-self-center productListTags" style="margin-top: 10px">
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
                    <button title="Delete" type="button" class="btn text-danger position-absolute deleteSection"
                            style="right: 0%"><i
                            class="ti ti-trash"></i></button>

                </div>
            @endif
            @if ($detail->section_type == 'banner_image')
                @foreach ($detail->items as $item)
                    <div class="card mb-4 section" data-id="banner_image">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{_trans('keyword.Landscape Banner')}}</h5>
                            <input type="text" value="{{ $detail->id }}" hidden>
                        </div>
                        <div class="row card-body justify-content-center align-items-center h-auto" style="min-height: 200px">
                            <div class="col-12 col-md-4 border-end d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0" style="min-height: 200px;">
                                <div class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                    <span style="font-size:1rem !important">Input image</span>
                                </div>
                                <input name="banner[${count}]"
                                       class="opacity-0 position-absolute top-50 start-50 translate-middle p-5 old_value"
                                       type="file" onchange="loadFile(event)"/>
                            </div>
                            <div class="col-12 col-md-4 border-end mb-3 mb-md-0" style="min-height: 200px;">
                                <img id='preview_img' class="ms-2 preview_img"
                                     style="height: 180px; max-Width:100%"
                                     src="{{ getFilePath(@$item->image) }}" alt="Product Image"/>
                            </div>
                            <div class="col-12 col-md-4 align-self-center productListTags" style="margin-top: 10px">
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

    @if(hasPermission('add_portfolio_inspiration_section'))
        <div class="row justify-content-center">
            <div class="col-12 col-md-3">
                <button type="button" id="save" class="btn btn-primary w-100">{{_trans('keyword.Save')}}
                    <span class="loader"></span>
                </button>
            </div>
        </div>
    @endif
@endsection


@push('scripts')
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        let count = 0;

        $('#add_section').on('click', function () {
            let value = $('#design_input').val();
            let s = '';
            if (value === 'description') {
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
            return `<div class="card mb-4 section" data-id="description">
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
            return `<div class="card mb-4 section" data-id="banner_image">
            <div class="card-header">
                <h5 class="card-title mb-0">Landscape Banner</h5>
                <input type="text" value="" hidden>
            </div>
            <div class="row card-body justify-content-center align-items-center h-auto" style="min-height: 200px">
                <div class="col-12 col-md-4 border-end d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0" style="min-height: 200px;">
                    <div class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                        <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                        <span style="font-size:1rem !important">Input image</span>
                    </div>
                    <input name="banner[${count}]"
                           class="opacity-0 position-absolute top-50 start-50 translate-middle p-5 old_value"
                           type="file" onchange="loadFile(event)" />
                </div>
                <div class="col-12 col-md-4 border-end mb-3 mb-md-0" style="min-height: 200px;">
                    <img id='preview_img' class="ms-2 preview_img" style="height: 140px; max-Width:100%"
                        src="{{ asset('assets/img/placeholder/placeholder.png') }}"
                        alt="Current profile photo" />
                </div>
                <div class="col-12 col-md-4 align-self-center">
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
            return `<div class="card mb-4 section" data-id="image_gallary">
            <div class="card-header d-flex flex-row justify-content-start align-items-center">
                <h5 class="card-title mb-0">Image Gallery</h5>
                <input type="text" value="" hidden>
                <button type="button" class="btn btn-primary ms-2 image_gallary_add_button"><i class="ti ti-plus ti-xs"></i></button>

            </div>
            <div class="row card-body justify-content-center align-items-center">
                <div class="col-12 border-end gallaryImageContainer">
                    <div class="row  position-relative card h-auto" style="min-height: 150px">
                        <div class="col-12 col-md-4 border-end d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0"
                            style="min-height: 150px;">
                            <div
                                class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                <span style="font-size:1rem !important">Input image</span>
                            </div>
                            <input name="image_gallary[${count}]"
                                class="opacity-0 position-absolute top-50 start-50 translate-middle p-5" type="file"
                                onchange="loadFile(event)" />
                        </div>
                        <div class="col-12 col-md-4 border-end mb-3 mb-md-0" style="min-height: 150px;">
                            <img id='preview_img' class="ms-2 preview_img "
                                 style="height: 100%; max-Width:100%" src="{{ getFilePath(@$item->image) }}" alt="Product Image"/>
                        </div>
                        <div class="col-12 col-md-4 align-self-center">
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
            return `<div class="row mt-2 position-relative card h-auto" style="min-height: 150px">
                        <div class="col-12 col-md-4 border-end d-flex justify-content-center align-items-center position-relative mb-3 mb-md-0"
                            style="min-height: 150px;">
                            <div
                                class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                <span  style="font-size:1rem !important">Input image</span>
                            </div>
                            <input name="image_gallary[${count}]"
                                class="opacity-0 position-absolute top-50 start-50 translate-middle p-5" type="file"
                                onchange="loadFile(event)" />
                        </div>
                        <div class="col-12 col-md-4 border-end mb-3 mb-md-0" style="min-height: 150px;">
                            <img id='preview_img' class="ms-2 preview_img" style="height: 100%; max-Width:100%"
                                src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Current profile photo" />
                        </div>
                        <div class="col-12 col-md-4 align-self-center">
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
            return `<div class="card mb-4 section" data-id="image_gallary_with_details">
                <div class="card-header d-flex flex-row justify-content-start align-items-center">
                    <h5 class="card-title mb-0">Image Gallary With Details</h5>
                    <input type="text" name="id" value="" hidden>

                    <button type="button" class="btn btn-primary ms-2 image_gallary_with_description_add_button"><i
                            class="ti ti-plus ti-xs"></i></button>

                </div>
                <div class="row card-body justify-content-center align-items-center">
                    <div class="col-12 border-end gallaryImageWithDetailsContainer">
                        <div class="row position-relative gallaryImageWithDetailsCard pb-3 pb-md-0" style="min-height: 150px">
                            <div class="col-12 col-md-4 border-end mb-3 mb-md-0">
                                <label for="">Title</label>
                                <input class="form-control mb-4" name='title' id=""/>
                                <label for="">Description/Amount</label>
                                <input class="form-control" name='description' id=""/>
                            </div>
                            <div class="col-6 col-md-2 border-end d-flex justify-content-center align-items-center position-relative"
                                style="min-height: 150px;">
                                <div
                                    class="branner_input_placeholder d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                    <span  style="font-size:1rem !important">Input image</span>
                                </div>
                                <input name='image'
                                    class="opacity-0 position-absolute top-50 start-50 translate-middle p-3" type="file"
                                    onchange="loadFile(event)" />
                                    <input type="text" name="id" value="" hidden>

                            </div>
                            <div class="col-6 col-md-2 border-end" style="min-height: 150px;">
                                <img id='preview_img' class=" preview_img" style="height: 140px; max-Width:100%"
                                    src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Current profile photo" />
                            </div>
                            <div class="col-12 col-md-4 align-self-center">
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
            return `<div class="row position-relative gallaryImageWithDetailsCard pb-3 pb-md-0" style="min-height: 150px">
                            <div class="col-12 col-md-4 border-end mb-3 mb-md-0">
                                <label for="">Title</label>
                                <input class="form-control mb-4" name='title' id=""/>
                                <label for="">Description/Amount</label>
                                <input class="form-control" name='description' id=""/>
                            </div>
                            <div class="col-6 col-md-2 border-end d-flex justify-content-center align-items-center position-relative"
                                style="min-height: 150px;">
                                <div
                                    class="branner_input_placeholder  d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                    <span  style="font-size:1rem !important">Input image</span>
                                </div>
                                <input name='image'
                                    class="opacity-0 position-absolute top-50 start-50 translate-middle p-3" type="file"
                                    onchange="loadFile(event)" />
                                    <input type="text" name="id" value="" hidden>

                            </div>
                            <div class="col-6 col-md-2 border-end" style="min-height: 150px;">
                                <img id='preview_img' class=" preview_img" style="height: 140px; max-Width:100%"
                                    src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Current profile photo" />
                            </div>
                            <div class="col-12 col-md-4 align-self-center">
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
            var output = event.currentTarget.parentNode.nextElementSibling.children[0];
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function () {
                URL.revokeObjectURL(output.src) // free memory
            }
        };

        $('#save').on('click', function () {

            let isValid = true;
            $('.section').each(function (index, section) {
                let sectionName = $(section).data('id');
                if (sectionName == 'description') {
                    // No specific validation for description section
                } else if (sectionName == 'banner_image') {
                    let imageInput = $(section).find('input')[1];
                    let $image = $(imageInput);
                    let image = $image.prop('files')[0];

// Skip validation if input has class old_value
                    if (!$image.hasClass('old_value')) {
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

// Title validation
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
                                formData.append(
                                    `section[${index}][cards][${cardKey}][title]`, $(
                                        input).val());
                            }
                            if (name == 'description') {
                                formData.append(
                                    `section[${index}][cards][${cardKey}][description]`,
                                    $(
                                        input).val());
                            }
                            if (name == 'image') {
                                formData.append(
                                    `section[${index}][cards][${cardKey}][image]`,
                                    $(
                                        input).prop('files')[0] ?? '');
                            }
                            if (name == 'id') {
                                formData.append(
                                    `section[${index}][cards][${cardKey}][id]`,
                                    $(
                                        input).val() ?? null);
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
                contentType: 'multipart/form-data',
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    window.location.href = "{{ route('section.portfolioAndInspiration.index', 2) }}";
                },
                error: function (error) {

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
