@extends('layouts.master')

@section('title', $title ?? __('Inspiration'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('Inspiration List',['section/portfolioAndInspiration/2/index'=>'Portfolio & Inspiration','inspiration'=>'Inspiration List']) !!}
        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">

                <div class="card-datatable table-responsive">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Title')}}</th>
                            <th>{{_trans('keyword.image')}}</th>
                            <th>{{_trans('keyword.Category')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            @if(Auth::user()->role_id == 1)
                                <th width="50px">{{_trans('keyword.Info')}}</th>
                            @endif
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- Offcanvas to add new customer -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEBrandList"
                 aria-labelledby="offcanvasEcommerceListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Add').' '._trans('keyword.Inspiration')}} </h5>
                    <button type="button" class="btn-close bg-label-secondary text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="addModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="ecommerce-category-title">{{_trans('keyword.Title')}}<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ecommerce-category-title"
                                   placeholder="Enter Inspiration name" name="name" aria-label="Inspiration Name"
                                   />
                            <span class="text-danger nameError error"></span>
                        </div>
                        <!-- Image -->
                        <div class="mb-3">
                            <label class="form-label"
                                   for="brand-image">{{_trans('keyword.Inspiration').' '._trans('keyword.Image')}}<span
                                    class="text-danger">*</span></label>
                            <input class="form-control" type="file" name="image" id="brand-image" />
                            <span class="text-danger imageError error"></span>
                        </div>
                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Category')}}<span
                                    class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" class="select2 form-select"
                                    data-placeholder="Select category" >
                                <option
                                    value="">{{_trans('keyword.Select').' '._trans('keyword.Section').' '._trans('keyword.Category')}}</option>
                                @foreach ($special_categories as $special_category)
                                    <option value="{{ $special_category->id }}">{{ $special_category->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger categoryError error"></span>
                        </div>

                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Inspiration').' '._trans('keyword.Status')}}</label>
                            <select id="brand-status" name="status" class="select2 form-select" required
                                    data-placeholder="Select category status">
                                <option value="1" selected>{{_trans('keyword.Active')}}</option>
                                <option value="0">{{_trans('keyword.Inactive')}}</option>
                            </select>
                            <span class="text-danger statusError error"></span>
                        </div>
                        <!-- Submit and reset -->
                        <div class="mb-3">
                            <button type="submit"
                                    class="btn btn-primary me-sm-3 me-1 data-submit">{{_trans('keyword.Add')}}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" id="closeAddModal" class="btn bg-label-danger"
                                    data-bs-dismiss="offcanvas">{{_trans('keyword.Discard')}}</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Offcanvas to edit category -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBrandEditModal"
                 aria-labelledby="offcanvasEcommerceCategoryListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Edit').' '._trans('keyword.Portfolio')}}</h5>
                    <button type="button" class="btn-close bg-label-secondary text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="updateCategoryModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="edit_name">{{_trans('keyword.Title')}}</label>
                            <input type="text" class="form-control" id="edit_name" placeholder="Enter portfolio name"
                                   name="edit_name" aria-label="portfolio name"/>
                            <input type="text" hidden value="" name="brand_id" id="brand_id">
                            <span class="text-danger editNameError error"></span>
                        </div>
                        <!-- Image -->
                        <div class="mb-3" id="currentImageSection">
                            <img src="" id="currentImage" alt="" width="60px" height="60px">
                        </div>
                        <!-- Image -->
                        <div class="mb-3">
                            <label class="form-label"
                                   for="brand-image">{{_trans('keyword.Portfolio').' '._trans('keyword.Image')}}</label>
                            <input class="form-control" type="file" name="edit_image" id="edit_image"/>
                            <span class="text-danger editImageError error"></span>
                        </div>
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Section').' '._trans('keyword.Category')}} </label>
                            <select id="edit_category_id" name="edit_category_id" class="select2 form-select"
                                    data-placeholder="Select category">
                                @foreach ($special_categories as $special_category)
                                    <option value="{{ $special_category->id }}">{{ $special_category->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger editCategoryError error"></span>
                        </div>
                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Portfolio').' '._trans('keyword.Status')}}</label>
                            <select id="edit_status" name="edit_status" class="select2 form-select"
                                    data-placeholder="Select category status">
                                <option value="1">{{_trans('keyword.Active')}}</option>
                                <option value="0">{{_trans('keyword.Inactive')}}</option>
                            </select>
                            <span class="text-danger editStatusError error"></span>
                        </div>
                        <!-- Submit and reset -->
                        <div class="mb-3">
                            <button type="submit"
                                    class="btn btn-primary me-sm-3 me-1 data-submit">{{_trans('keyword.Update')}}
                                <span class="loader"></span>
                            </button>
                            <button id="btnDismiss" type="reset" class="btn bg-label-danger"
                                    data-bs-dismiss="offcanvas">{{_trans('keyword.Discard')}}</button>
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

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('section.portfolioAndInspiration.index', 2) }}',
                    data: function (d) {
                        d.status = $('#status').val()
                        d.filter_category = $('#filter_category').val()

                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                        @if(Auth::user()->role_id == 1)
                    {
                        data: 'info',
                        name: 'info',
                    },
                        @endif
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [2, "desc"], //set any columns order asc/desc
                dom: '<"card-header d-flex flex-wrap pb-2 c-list-header"' +
                    '<f m-0><"custom-text-div">' +
                    '<" d-flex justify-content-center justify-content-md-end align-items-baseline right-side-buttons "<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline gap-xl-0 gap-3"lB>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                initComplete: function () {
                    // Set the inner div to display your name
                    $('.custom-text-div').html(`
                    <div class="d-flex gap-3  ">
                    <div class="form-group">
                        <label><strong>Status :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 inspiration-1" style="width: 200px" data-placeholder='{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}'>
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Category')}} :</strong></label>
                        <select id='filter_category' class="form-control filter_dropdown select2 inspiration-2" style="width: 200px" data-placeholder='{{ _trans('keyword.Select') }} {{ _trans('keyword.Category') }}'>
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Category')}}</option>

                            @foreach($special_categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                    </select>
                </div>

            </div>
            `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.inspiration-1').select2({
                        allowClear: true,
                    });
                    $('.inspiration-2').select2({
                        allowClear: true,
                    });
                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Inspiration",
                },
                // Button for offcanvas
                buttons: [

                        @if(hasPermission('portfolio_and_inspiration_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span >Add Inspiration</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "offcanvas",
                            "data-bs-target": "#offcanvasEBrandList",
                        },
                    },
                    @endif

                ],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })


            $('#addModal').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let name = $("input[name=name]").val();
                let status = $("#brand-status option:selected").val();
                var image = $('#brand-image').prop('files')[0] ?? '';
                let category_id = $("#category_id option:selected").val();

                formData.append('name', name);
                formData.append('status', status);
                formData.append('image', image);
                formData.append('category_id', category_id);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('section.portfolioAndInspiration.store', 2) }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        if (response.status == 403) {
                            $('.nameError').text(response.errors?.name ? response.errors
                                ?.name[0] : '');
                            $('.imageError').text(response.errors?.image ? response.errors
                                ?.image[0] : '');
                            $('.statusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                            $('.categoryError').text(response.errors?.category_id ? response
                                    .errors
                                    ?.category_id[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false);
                            $('#closeAddModal').click();
                            $("input[name=name]").val('');
                            $("#brand-descripton").val('');
                            $('#brand-image').val('');
                            $('#category_id').val(null).trigger('change');
                        }
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });


            $(document).on("click", ".brand_edit_button", function () {

                $id = $(this).attr("data-id");
                $.ajax({
                    url: '/section/portfolioAndInspiration/2/' + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#brand_id').val(response.data.id);
                        $('#edit_name').val(response.data.title);

                        // Set selected values for select2
                        $('#edit_status').val(response.data.is_active).trigger('change');
                        $('#edit_category_id').val(response.data.special_section_category_id).trigger('change');

                        $("#currentImage").attr("src", response.data.image);
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });


            $('#updateCategoryModal').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let section_id = $('#brand_id').val();
                let name = $("input[name=edit_name]").val();
                let status = $("#edit_status option:selected").val();
                let edit_category_id = $("#edit_category_id option:selected").val();
                var image = $('#edit_image').prop('files')[0] ?? '';


                formData.append('section_id', section_id);
                formData.append('name', name);
                formData.append('status', status);
                formData.append('image', image);
                formData.append('category_id', edit_category_id);
                formData.append('_token', "{{ csrf_token() }}");


                loader.show();
                submitButton.prop('disabled', true);


                $('.error').text('');
                $.ajax({
                    url: '{{ route('section.portfolioAndInspiration.update', 2) }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        if (response.status == 403) {
                            $('.editNameError').text(response.errors?.name ? response.errors
                                ?.name[0] : '');
                            $('.editImageError').text(response.errors?.image ? response.errors
                                ?.image[0] : '');
                            $('.editStatusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                            $('.editCategoryError').text(response.errors?.category_id ? response
                                    .errors
                                    ?.category_id[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false)
                            $('#btnDismiss').click();
                        }
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });

            $(document).on("click", ".brand_delete_button", function () {

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
                            url: '{{ route('section.portfolioAndInspiration.destroy', 2) }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                section_id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false)
                                toastr.success(response.text);
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                                // handle the error case
                            }
                        });
                    }
                });
            });

            $(document).on('change', '.changeStatus', function() {
                const id = $(this).data('id');
                const formData = new FormData();
                formData.append('id', id);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to change the status of this?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it',
                    cancelButtonText: 'No, cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('section.portfolioAndInspiration.changeStatus') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    }
                });
            });


        });
    </script>
@endpush
