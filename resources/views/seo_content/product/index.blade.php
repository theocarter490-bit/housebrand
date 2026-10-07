@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Product List'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Product List'),['#'=>_trans('keyword.SEO Content'), ''=>trans('Product List')]) !!}

        <div class="col-lg-12 col-md-12 mb-4">
            <form method="GET" class="mb-2" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-2 mb-lg-0 mb-3 ">
                        <div class="input-effect">
                            <input
                                class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                type="text" placeholder="Search here" name="search" value="{{ request('search') }}"
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-6 mb-lg-0 mb-3 ">
                        <div class="input-control">
                            <select class="w-100 bb form-control height-50 select2" style="width: 100%"
                                    aria-label="Select Type"
                                    data-placeholder="Select Category"
                                    name="category_type">
                                <option value=""
                                        selected>{{ _trans('keyword.Select') }} {{ _trans('keyword.Category') }}</option>
                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}" {{ request('category_type') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-6 mb-lg-0 mb-3 ">
                        <div class="input-control">
                            <select id="status" class="form-control height-50 select2 form-select2" style="width: 200px"
                                    data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}"
                                    name="status_type">
                                <option value=""
                                        selected>{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}</option>
                                <option
                                    value="1" {{ request('status_type') != '' && (int)request('status_type') == 1 ? 'selected' : '' }}>{{ _trans('keyword.Published') }}</option>
                                <option
                                    value="0" {{ request('status_type') != '' && (int)request('status_type') == 0 ? 'selected' : '' }}>{{ _trans('keyword.Unpublished') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 mb-lg-0 ">
                        <button class="btn btn-primary" type="submit">{{ _trans('keyword.Submit') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="app-ecommerce-category">
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                        <tr>
                            <th>{{ _trans('keyword.SL') }}</th>
                            <th>{{ _trans('keyword.Image') }}</th>
                            <th>{{ _trans('keyword.Name') }}</th>
                            <th>{{ _trans('keyword.Category') }}</th>
                            <th>{{ _trans('keyword.Price') }}</th>
                            <th>{{ _trans('keyword.Status') }}</th>
                            <th>{{ _trans('keyword.Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($products as $key=>$product)
                            <tr>
                                <th>{{ $products->firstItem() + $key }}</th>
                                <td>
                                    <img src="{{ getFilePath($product->thumbnail_img) }}" alt="{{ @$product->name }}"
                                         class="img-thumbnail" style="width: 100px; height: 100px;">
                                </td>
                                <td>
                                    @if($product->relationLoaded('shop'))
                                        <a href="{{env('APP_FRONTEND_URL').'/designer/'.@$product->shop->slug.'/product/'.@$product->id.'-' .@$product->slug }}"
                                           target="_blank">{{ optional($product)->name }}</a>
                                    @else
                                        <a href="{{env('APP_FRONTEND_URL').'/product/'.@$product->id.'-' .@$product->slug}}"
                                           target="_blank">{{ optional($product)->name }}</a>
                                    @endif
                                </td>
                                <td>{{ @$product->category->name }}</td>
                                <td>{{ getPriceFormat(@$product->unit_price) }}</td>
                                <td>
                                    @if($product->is_published == 1)
                                        <span class="badge bg-label-success">{{_trans('keyword.Published')}}</span>
                                    @else
                                        <span class="badge bg-label-danger">{{_trans('keyword.Unpublished')}}</span>
                                    @endif
                                </td>
                                @if(hasPermission('seo_product_list_content_update'))
                                    <td>
                                        <button data-id="{{$product->id}}" data-bs-target="#metaDetailsModal"
                                                data-bs-toggle="modal"
                                                class="btn btn-primary btn-sm editButton">{{ _trans('keyword.Edit Content')}}</button>
                                    </td>
                                @endif

                            </tr>
                        @empty
                            <tr>
                            <td colspan="10" class="text-center">No Product Found</td>

                            </tr>
                        @endforelse
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="metaDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{ _trans('keyword.Edit').' '._trans('keyword.Meta Details') }}</h3>
                    </div>
                    <!-- Form Start -->
                    <form id="metaDetailsForm" action="{{ route('seoContent.product.update') }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="col-12 mb-3">
                            <label class="form-label" for="meta_title">{{ _trans('keyword.Meta Title') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" id="meta_title" name="meta_title" class="form-control"
                                   placeholder="Enter Meta Title" value="{{ old('meta_title') }}"/>
                            <span class="text-danger metaTitleError error"></span>
                        </div>

                        <input type="hidden" name="product_id" id="product_id" value="">

                        <div class="col-12 mb-3">
                            <label class="form-label" for="meta_description">{{ _trans('keyword.Meta Description') }}
                                <span class="text-danger">*</span></label>
                            <textarea rows="8" id="meta_description" name="meta_description" class="form-control"
                                      placeholder="Enter Meta Description">{{ old('meta_description') }}</textarea>
                            <span class="text-danger metaDescError error"></span>
                        </div>

                        <div class="row mb-3">
                            <div class="col-8">
                                <label class="form-label" for="meta_image">{{ _trans('keyword.Meta Image') }}</label>
                                <input type="file" id="meta_image" name="meta_image" class="form-control"/>
                                <span class="text-danger metaImageError error"></span>
                            </div>

                            <div class="col-4">
                                <label class="form-label">{{ _trans('keyword.Current Image') }}</label>
                                <img src="" id="current_image" alt="" class="img-fluid"/>
                            </div>
                        </div>


                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Submit') }}
                                <span class="loader"></span>
                            </button>
                            <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                {{ _trans('keyword.Cancel') }}
                            </button>
                        </div>
                    </form>
                    <!-- Form End -->

                </div>
            </div>
        </div>
    </div>
    <!--/ Edit Modal -->

@endsection

@push('scripts')

    <script>

        $(document).ready(function () {
            $('.editButton').on('click', function () {
                const id = $(this).data('id');

                clearFields()

                $.ajax({
                    url: '/seo-content/product/edit/' + id,
                    type: 'GET',
                    success: function (response) {
                        console.log(response);

                        $('#meta_title').val(response.meta_title);
                        $('#meta_description').val(response.meta_description);
                        $('#ecommerce-product-tags').val(response.meta_keywords);
                        $('#product_id').val(response.id);

                        if (response.meta_img != null) {
                            $('#current_image').attr('src', response.meta_img);
                        } else {
                            $('#current_image').attr('src', '');
                        }
                    },
                    error: function (xhr) {
                        console.error('An error occurred:', xhr);
                    }
                });
            });


            $('#metaDetailsForm').submit(function (e) {
                e.preventDefault();

                let formData = new FormData(this);
                let url = $(this).attr('action');
                let method = $(this).attr('method');
                loader.show();
                submitButton.prop('disabled', true);

                clearFields()

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response)
                        if (response.status === 200) {
                            $('#metaDetailsModal').modal('hide');
                            toastr.success(response.message)
                        } else if (response.status === 403) {
                            $('.metaTitleError').text(response.errors?.meta_title ? response.errors
                                ?.meta_title[0] : '');
                            $('.metaDescError').text(response.errors?.meta_description ? response.errors
                                ?.meta_description[0] : '');
                            $('.metaImageError').text(response.errors?.meta_image ? response.errors
                                    ?.meta_image[0] :
                                '');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('.loader').hide(); // Hide loader
                        toastr.error('Something went wrong!')
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });

            function clearFields() {
                $('.metaTitleError').text('');
                $('.metaDescError').text('');
                $('.metaImageError').text('');
            }

            $('.select2').select2({
                allowClear: true,
            });
        });
    </script>

@endpush
