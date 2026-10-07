@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Create') . ' ' . _trans('keyword.Blog') . ' ' . _trans('keyword.Post'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Create') . ' ' . _trans('keyword.Post'), [
            '#' => _trans('keyword.Blog'),
            'blog/post' => _trans('keyword.Post'),
            _trans('keyword.Create') . ' ' . _trans('keyword.Post'),
        ]) !!}
        <div class="app-ecommerce">
            <div id="content-wrapper">
                <form id="createPostForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Left Column (6 columns) -->
                        <div class="col-12 col-lg-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ _trans('keyword.Title') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                               aria-label="Page title" placeholder="Post title"/>
                                        <span class="text-danger titleError error"></span>
                                    </div>

                                    <div class="mb-3 col ecommerce-select2-dropdown">
                                        <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                               for="category-org">
                                            <span>{{ _trans('keyword.Blog') . ' ' . _trans('keyword.Category') }}<span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <select id="blogCategory" name="category_id" class="select2 form-select"
                                                style="width: 100%"
                                                data-placeholder="Select Category">
                                            @foreach ($blogCategory as $widget)
                                                <option value="{{ $widget->id }}">{{ $widget->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger categoryError error"></span>
                                    </div>

                                    <!-- Tags -->
                                    <div class="mb-3">
                                        <label for="ecommerce-product-tags"
                                               class="form-label mb-1">{{ _trans('keyword.Tags') }}</label>
                                        <input id="ecommerce-product-tags" class="form-control" name="tags"
                                               aria-label="Product Tags"/>
                                    </div>

                                    {{-- Short Description --}}
                                    <div class="mb-3">
                                        <label class="form-label">{{ _trans('keyword.Short Description') }}</label>
                                        <textarea id="short_desc" class="form-control" placeholder="Short description"
                                                  name="desc" cols="30"
                                                  rows="3"></textarea>
                                        <span class="text-danger shortDescError error"></span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Right Column (6 columns) -->
                        <div class="col-12 col-lg-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <!-- Thumbnail Input -->
                                    <div class="mb-3">
                                        <label class="form-label">{{ _trans('keyword.Thumbnail') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="thumbnail" id="thumbnail"/>
                                        <span class="text-danger thumbnailError error"></span>
                                    </div>

                                    <!-- Banner Input -->
                                    <div class="mb-3">
                                        <label class="form-label">{{ _trans('keyword.Banner') }}</label>
                                        <input type="file" class="form-control" name="banner" id="banner"/>
                                        <span class="text-danger bannerError error"></span>
                                    </div>

                                    <!-- Video URL Input -->
                                    <div class="mb-3">
                                        <label class="form-label">{{ _trans('keyword.Video URL') }}</label>
                                        <input type="url" class="form-control" name="video_url" id="video_url"
                                               placeholder="http://example.com/video"/>
                                        <span class="text-danger videoUrlError error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ _trans('keyword.Select Image and Description for Post') }}</h5>
                                </div>
                                <div class="row card-body justify-content-center align-items-center">
                                    <div class="col-sm-8 ecommerce-select2-dropdown mb-sm-0 mb-3">
                                        <select id="design_input" class="select2 form-select"
                                                data-placeholder="Select Section" style="width: 100%">
                                            <option value="">
                                                {{ _trans('keyword.Select') . ' ' . _trans('keyword.Section') }}</option>
                                            <option value="banner_image">
                                                {{ _trans('keyword.Image') . ' ' . _trans('keyword.Banner') }}</option>
                                            <option value="description">{{ _trans('keyword.Description') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <button class="btn btn-primary text-nowrap w-100" type="button"
                                                id="add_section"><i
                                                class="ti ti-plus ti-xs me-0 me-sm-2"></i>
                                            {{ _trans('keyword.Add') . ' ' . _trans('keyword.Section') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="section-wrapper"></div>

            </div>
            <div class="row justify-content-center">
                <div class="col-3">
                    <button type="submit" id="save"
                            class="btn btn-primary w-100 mb-3">{{ _trans('keyword.Save') }}
                        <span class="loader"></span>
                    </button>
                </div>
            </div>
        </div>
        </form>
    </div>

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

            } else if (value === 'banner_image') {
                s = bannerHTML();
                $('#content-wrapper').append(s);

            }

            count++;
        });

        function descriptionHTML() {
            return `<div class="card mb-4 section" data-id="description">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Description</h5>

                    </div>
                    <div class="row card-body justify-content-center align-items-center">
                        <div>
                            <label class="form-label">Description</label>
                            <div class="form-control p-0 pt-1">
                               <textarea class="ckeditor ckeditor${count} form-control"
                                              name="description${count}"></textarea>
                            </div>
                            <span class="text-danger descriptionError error"></span>
                        </div>
                    </div>
                    <button type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%"><i class="ti ti-trash"></i></button>
                </div>`;
        }

        function bannerHTML() {
            return `<div class="card mb-4 section" data-id="banner_image">
                <div class="card-header">
                    <h5 class="card-title mb-0">Landscape Banner</h5>

                </div>
                <div class="row card-body justify-content-center align-items-center" style="height: 200px">
                    <div class="col-6 border-end d-flex justify-content-center align-items-center position-relative" style="height: 100%">
                        <div class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                            <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                            <span style="font-size:1rem !important">Input image</span>
                        </div>
                        <input name="banner[${count}]" id="banner_input_${count}" class="opacity-0 position-absolute top-50 start-50 translate-middle p-5" type="file" onchange="loadFile(event, ${count})" />
                    </div>

                    <div class="col-6" style="height: 100%">
                        <img id='preview_img_${count}' class="ms-2 preview_img" style="height: 100%; max-Width:100%"
                            src="{{ asset('assets/img/placeholder/placeholder.png') }}"
                            alt="Current profile photo" />
                    </div>
                    <span class="text-danger imageBannerError error"></span>
                </div>

                <button type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%"><i class="ti ti-trash"></i></button>
            </div>`;
        }


        function loadFile(event, count) {
            var output = document.getElementById(`preview_img_${count}`);
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function () {
                URL.revokeObjectURL(output.src)
            }
        }

        $(document).on('click', '.deleteSection', function () {
            $(this).closest('.section').remove();
        });

        $(document).ready(function () {
            $('#createPostForm').on('submit', function (e) {
                e.preventDefault();
                let isValid = true;

                // Clear previous errors
                $('.error').text('');

                // Check required fields
                if ($('#title').val().trim() === '') {
                    $('.titleError').text('Title is required.');
                    isValid = false;
                }

                if ($('#blogCategory').val().trim() === '') {
                    $('.categoryError').text('Category is required.');
                    isValid = false;
                }

                if ($('#thumbnail').val().trim() === '') {
                    $('.thumbnailError').text('Thumbnail is required.');
                    isValid = false;
                }

                // Check if banner image is provided and has a file
                $('.section').each(function (index, section) {
                    let sectionName = $(section).data('id');
                    if (sectionName === 'banner_image') {
                        let fileInput = $(section).find('input[type="file"]');
                        let imageFile = fileInput[0]?.files[0];
                        if (imageFile === undefined || imageFile === null) {
                            $(section).find('.imageBannerError').text('Banner image is required.');
                            isValid = false;
                        }
                    }
                });

                // Check if description is provided and not empty
                $('.section').each(function (index, section) {
                    let sectionName = $(section).data('id');
                    if (sectionName === 'description') {
                        let name = $(section).find('.ckeditor')[0].getAttribute('name');
                        let description = CKEDITOR.instances[name].getData();
                        if (description.trim() === '') {
                            $(section).find('.descriptionError').text('Description cannot be empty.');
                            isValid = false;
                        }
                    }
                });

                // Check if form is valid before submitting
                if (isValid) {
                    var formData = new FormData(this);

                    $('.section').each(function (index, section) {
                        let sectionName = $(section).data('id');
                        let fileInput = $(section).find('input[type="file"]');

                        if (sectionName === 'description') {
                            let name = $(section).find('.ckeditor')[0].getAttribute('name');
                            let description = CKEDITOR.instances[name].getData();
                            let sectionID = $(section).find('input').val() ?? null;

                            formData.append(`sections[${index}][type]`, 'description');
                            formData.append(`sections[${index}][id]`, sectionID);
                            formData.append(`sections[${index}][description]`, description);

                        } else if (sectionName === 'banner_image') {
                            let sectionID = $(section).find('input').val() ?? null;
                            let imageFile = fileInput[0]?.files[0] ?? null;

                            formData.append(`sections[${index}][type]`, 'banner_image');
                            formData.append(`sections[${index}][id]`, sectionID);
                            if (imageFile) {
                                formData.append(`sections[${index}][image]`, imageFile);
                            }
                        }
                    });

                    loader.show();
                    submitButton.prop('disabled', true);

                    $.ajax({
                        url: '{{ route('blog.post.store') }}',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            if (response.status === 200) {
                                toastr.success(response.message);
                                window.location.href = '{{ route('blog.post.index') }}';
                            } else if (response.status === 403) {
                                if (response.errors?.title) {
                                    $('.titleError').text(response.errors.title[0]);
                                }
                                if (response.errors?.thumbnail) {
                                    $('.thumbnailError').text(response.errors.thumbnail[0]);
                                }
                                if (response.message) {
                                    toastr.error(response.message);
                                }
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                            toastr.error('Error saving post.');
                        },
                        complete: function () {
                            loader.hide();
                            submitButton.prop('disabled', false);
                        }
                    });
                } else {
                    loader.hide();
                    submitButton.prop('disabled', false);
                }
            });
        });
    </script>
@endpush
