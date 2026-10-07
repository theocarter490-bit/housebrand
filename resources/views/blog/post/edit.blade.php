@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Edit') . ' ' . _trans('keyword.Blog') . ' ' . _trans('keyword.Post'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Edit') . ' ' . _trans('keyword.Post'), [
            '#' => _trans('keyword.Blog'),
            'blog/post' => 'Post',
            _trans('keyword.Edit') . ' ' . _trans('keyword.Post'),
        ]) !!}
        <div class="app-ecommerce">
            <div id="content-wrapper">
                <form id="updatePostForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="id" value="{{ $blogPost->id }}">
                    <div class="row">
                        <!-- Left Column (6 columns) -->
                        <div class="col-12 col-lg-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ _trans('keyword.Title') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                               value="{{ old('title', $blogPost->title) }}" aria-label="Page title"
                                               placeholder="Post title"/>
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
                                                <option value="{{ $widget->id }}"
                                                    {{ old('category_id', $blogPost->category_id) == $widget->id ? 'selected' : '' }}>
                                                    {{ $widget->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Tags -->
                                    <div class="mb-3">
                                        <label for="ecommerce-product-tags"
                                               class="form-label mb-1">{{ _trans('keyword.Tags') }}</label>
                                        <input id="ecommerce-product-tags" class="form-control" name="tags"
                                               value="{{ old('tags', $blogPost->tags) }}" aria-label="Product Tags"/>
                                    </div>

                                    <!-- Short Description -->
                                    <div class="mb-3">
                                        <label class="form-label">{{ _trans('keyword.Short Description') }}</label>
                                        <textarea id="short_desc" class="form-control" placeholder="Short description"
                                                  name="desc" cols="30"
                                                  rows="3">{{ old('desc', $blogPost->desc) }}</textarea>
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
                                        <input type="hidden" id="oldThumbnail" value="{{ $blogPost->thumbnail }}">
                                        @if ($blogPost->thumbnail)
                                            <label class="mt-2"
                                                   for="thumbnail">{{ _trans('keyword.Old') . ' ' . _trans('keyword.Thumbnail') }}
                                                :
                                            </label>
                                            {!! $blogPost->thumbnail !!}
                                        @endif
                                        <span class="text-danger editThumbError error"></span>
                                    </div>

                                    <!-- Banner Input -->
                                    <div class="mb-3">
                                        <label class="form-label">{{ _trans('keyword.Banner') }}</label>
                                        <input type="file" class="form-control" name="banner" id="banner"/>
                                        <span class="text-danger bannerError error"></span>
                                        @if ($blogPost->banner)
                                            @if ($tempVariable == 0)
                                                <div id="oldBannerContainer">
                                                    <label class="mt-2"
                                                           for="banner">{{ _trans('keyword.Old') . ' ' . _trans('keyword.Banner') }}
                                                        :
                                                    </label>
                                                    {!! $blogPost->banner !!}
                                                    <button type="button" class="btn btn-danger mt-2"
                                                            id="removeBannerButton">
                                                        {{ _trans('keyword.Remove') . ' ' . _trans('keyword.Banner') }}
                                                    </button>
                                                    <input type="hidden" name="remove_banner" id="removeBanner"/>
                                                    <input type="hidden" value="0" name="isClicked" id="isClicked">
                                                    <p id="bannerMessage" class="bg-label-danger mt-2 p-2"
                                                       style="display: none;">
                                                        {{ _trans('keyword.Banner has been removed, Click on Update for changes') }}
                                                    </p>
                                                </div>
                                            @endif

                                        @endif
                                    </div>

                                    <!-- Video URL Input -->
                                    <div class="mb-3">
                                        <label class="form-label">{{ _trans('keyword.Video URL') }}</label>
                                        <input type="url" class="form-control" name="video_url" id="video_url"
                                               value="{{ old('video_url', $blogPost->video_url) }}"
                                               placeholder="http://example.com/video"/>
                                        <span class="text-danger videoUrlError error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ _trans('keyword.Select Image and Description for Post') }}</h5>
                        </div>
                        <div class="row card-body justify-content-center align-items-center">
                            <div class="col-8 ecommerce-select2-dropdown">
                                <select id="design_input" class="select2 form-select" data-placeholder="Select Section"
                                        style="width: 100%">
                                    <option value="">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Section') }}
                                    </option>
                                    <option value="banner_image">
                                        {{ _trans('keyword.Image') . ' ' . _trans('keyword.Banner') }}
                                    </option>
                                    <option value="description">{{ _trans('keyword.Description') }}</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-primary" type="button" id="add_section"><i
                                        class="ti ti-plus ti-xs me-0 me-sm-2"></i>
                                    {{ _trans('keyword.Add') . ' ' . _trans('keyword.Section') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="section-content-wrapper">

                        @foreach ($blogPost->contentDetails as $section)
                            @if ($section->section_type === 'description')
                                @foreach ($section->items as $item)
                                    <div class="card mb-4 section" data-id="description">
                                        <div class="card-header">
                                            <h5 class="card-title">{{ _trans('keyword.Description') }}</h5>
                                        </div>
                                        <div class="row card-body justify-content-center align-items-center">
                                            <div>
                                                <label class="form-label">{{ _trans('keyword.Description') }}</label>
                                                <div class="form-control p-0 pt-1">
                                                    <textarea class="ckeditor form-control"
                                                              name="descriptionExits{{$item->id}}"> {{ $item->description }}</textarea>
                                                </div>
                                                <span class="text-danger descriptionError error"></span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn text-danger position-absolute deleteSection"
                                                style="right: 0%"><i class="ti ti-trash"></i></button>
                                        <div class="position-absolute  mt-2 d-flex align-items-center gap-2"
                                             style="right: 5%;">
                                            <input type="text" value="{{ $section->id }}" hidden>
                                            <label class="form-label m-0">{{ _trans('keyword.Serial') }}</label>
                                            <input type="number" class="form-control p-0 "
                                                   style="max-width: 48px; text-align:center; text-align: center; height: 32px;"
                                                   value="{{ old('serial', $section->serial) }}"
                                                   name="sections[{{ $section->id }}][serial]"
                                                   placeholder="serial Order"
                                                   min="1"/>
                                            <input type="text" name="section_id" hidden value="{{ $section->id }}">

                                        </div>
                                    </div>
                                @endforeach
                            @elseif ($section->section_type === 'banner_image')
                                @foreach ($section->items as $item)
                                    <div class="card mb-4 section" data-id="banner_image">
                                        <div class="card-header">
                                            <h5 class="card-title">{{ _trans('keyword.Landscape Banner') }} </h5>
                                        </div>
                                        <div class="row card-body justify-content-center align-items-center"
                                             style="height: 200px">
                                            <div
                                                class="col-6 border-end d-flex justify-content-center align-items-center position-relative"
                                                style="height: 100%">
                                                <div
                                                    class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                                    <i class="ti ti-plus" style="font-size:2rem !important"></i>
                                                    <span
                                                        style="font-size:1rem !important">{{ _trans('keyword.Uploade image') }}</span>
                                                </div>
                                                <input name="banner[{{ $section->id }}]"
                                                       id="banner_input_{{ $section->id }}"
                                                       class="opacity-0 position-absolute top-50 start-50 translate-middle p-5"
                                                       type="file" onchange="loadFile(event, {{ $section->id }})"/>
                                                <input type="text" name="section_id" hidden
                                                       value="{{ $section->id }}">
                                                <input type="hidden" name="old_image_banner[{{ $section->id }}]"
                                                       value="{{ $item->image }}">

                                            </div>
                                            <div class="col-6" style="height: 100%">
                                                <img id='preview_img' class="ms-2 preview_img"
                                                     style="height: 100%; max-Width:100%"
                                                     src="{!! getFilePath($item->image) !!}"
                                                     alt="Current profile photo"/>
                                            </div>
                                        </div>
                                        <button type="button" class="btn text-danger position-absolute deleteSection"
                                                style="right: 0%"><i class="ti ti-trash"></i></button>
                                        <div class="position-absolute mt-2 d-flex align-items-center gap-2"
                                             style="right: 5%;">
                                            <input type="text" value="{{ $section->id }}" hidden>
                                            <label class="form-label m-0">{{ _trans('keyword.Serial') }}</label>
                                            <input type="number" class="form-control p-0"
                                                   style="max-width: 48px; text-align:center; text-align: center; height: 32px;"
                                                   value="{{ old('serial', $section->serial) }}"
                                                   name="sections[{{ $section->id }}][serial]"
                                                   placeholder="serial Order"/>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-3">
                    <button type="submit" id="save"
                            class="btn btn-primary w-100 mb-3">{{ _trans('keyword.Update') }}
                        <span class="loader"></span>
                    </button>
                </div>
            </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
        <script>
            let count = 0;
            $('#removeBannerButton').on('click', function () {
                $('#bannerMessage').show();
                $('#removeBanner').val('1');
                $('#isClicked').val('1');
            });
            $('#add_section').on('click', function () {
                let value = $('#design_input').val();
                let s = '';
                if (value === 'description') {
                    s = descriptionHTML();
                    $('#section-content-wrapper').append(s);

                    const editor = document.querySelector(`.editor${count}`);
                    if (editor) {
                        new Quill(editor, {
                            modules: {
                                toolbar: `.toolbar${count}`
                            },
                            placeholder: 'Description',
                            theme: 'snow'
                        });
                    }

                } else if (value === 'banner_image') {
                    s = bannerHTML();
                    $('#section-content-wrapper').append(s);

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
                            <span style="font-size:1rem !important">Uploade image</span>
                        </div>
                        <input name="banner[${count}]" id="banner_input_${count}" class="opacity-0 position-absolute top-50 start-50 translate-middle p-5" type="file" onchange="loadFile(event, ${count})" />
                    </div>
                    <div class="col-6" style="height: 100%">
                        <img id='preview_img_${count}' class="ms-2 preview_img" style="height: 100%; max-Width:100%"
                            src="{{ asset('assets/img/placeholder/placeholder.png') }}"
                            alt="Current profile photo" />
                    </div>
                </div>
                <button type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%"><i class="ti ti-trash"></i></button>
            </div>`;
            }


            $(document).on('click', '.deleteSection', function () {
                $(this).closest('.section').remove();
            });


            function loadFile(event, count) {
                var output = document.getElementById(`preview_img_${count}`);
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function () {
                    URL.revokeObjectURL(output.src);
                }
            }

            $('#updatePostForm').on('submit', function (event) {
                event.preventDefault();

                var formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('title', $('#title').val());
                formData.append('category_id', $('#blogCategory').val());
                formData.append('tags', $('#ecommerce-product-tags').val());
                formData.append('desc', $('#short_desc').val());
                formData.append('video_url', $('#video_url').val() ?? '');
                formData.append('thumbnail', $('#thumbnail')[0].files[0] ?? '');

                if ($('#isClicked').val() === '1') {
                    formData.append('isClicked', '1');
                }

                formData.append('banner', $('#banner')[0].files[0] ?? '');

                $('.section').each(function (index, section) {
                    let sectionName = $(section).data('id');
                    let sectionID = $(section).find('input[name="section_id"]').val() ?? null;
                    let serialInput = $(section).find('input[name*="[serial]"]').val() ?? 0;


                    if (sectionName === 'description') {
                        let name = $(section).find('.ckeditor')[0].getAttribute('name');
                        let description = CKEDITOR.instances[name].getData();

                        formData.append(`sections[${index}][type]`, 'description');
                        formData.append(`sections[${index}][id]`, sectionID);
                        formData.append(`sections[${index}][description]`, description);
                        formData.append(`sections[${index}][serial]`, serialInput);


                    } else if (sectionName === 'banner_image') {

                        let imageInput = $(section).find('input[type="file"]')[0];
                        let fileInput = $(section).find('input[type="file"]');
                        let newImage = imageInput.files[0] ?? null;
                        let imageFile = fileInput[0]?.files[0] ?? null;

                        if (imageFile) {
                            formData.append(`sections[${index}][image]`, imageFile);
                        }

                        formData.append(`sections[${index}][type]`, 'banner_image');
                        formData.append(`sections[${index}][id]`, sectionID);
                        formData.append(`sections[${index}][serial]`, serialInput);

                    }
                });

                loader.show();
                submitButton.prop('disabled', true);

                $.ajax({
                    url: "{{ route('blog.post.update', $blogPost->id) }}",
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
                                $('.editThumbError').text(response.errors.thumbnail[0]);
                            }
                        } else {
                            toastr.success(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        toastr.error('Error updating post.');
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });


            function initQuill() {
                let toolsBars = document.querySelectorAll('.toolbar');
                let editor = document.querySelectorAll('.editor');
                editor.forEach(function (value, index) {
                    const editorElement = value;
                    const toolbarElement = toolsBars[index];
                    if (editorElement) {
                        new Quill(editorElement, {
                            modules: {
                                toolbar: toolbarElement,
                            },
                            placeholder: 'Description',
                            theme: 'snow'
                        });
                    }

                });
            }

            initQuill();
        </script>
    @endpush

@endsection
