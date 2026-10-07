@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Category'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('Category List',['section/category/index'=>'Portfolio & Inspiration','section-category'=>'Category List']) !!}
        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
             {{--   <div class="d-flex gap-3  ">
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown" style="width: 200px">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Deactive')}}</option>
                        </select>
                    </div>
                </div>--}}
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Name')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            @if(Auth::user()->role_id == \App\Models\Role::SUPER_ADMIN)
                                <th width="50px">{{_trans('keyword.Info')}}</th>
                            @endif
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- Offcanvas to add new customer -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSectionCategoryList"
                aria-labelledby="offcanvasEcommerceListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel" class="offcanvas-title">{{_trans('keyword.Add').' '._trans('keyword.Category')}}</h5>
                    <button type="button" class="btn-close bg-label-secondary text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="addModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="ecommerce-category-title">{{_trans('keyword.Name')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ecommerce-category-title"
                                placeholder="Enter Section Category Name" name="name" aria-label="Brand Name" />
                            <span class="text-danger nameError error"></span>
                        </div>
                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Status')}} <span class="text-danger">*</span></label>
                            <select id="brand-status" name="status" class="select2 form-select"
                                data-placeholder="Select category status">
                                <option value="1" selected>{{_trans('keyword.Active')}}</option>
                                <option value="0">{{_trans('keyword.Inactive')}}</option>
                            </select>
                            <span class="text-danger statusError error"></span>
                        </div>
                        <!-- Submit and reset -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">{{_trans('keyword.Add')}}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" id="closeAddModal" class="btn bg-label-danger"
                                data-bs-dismiss="offcanvas">{{_trans('keyword.Discard')}}</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Offcanvas to edit category -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCategoryEditModal"
                aria-labelledby="offcanvasEcommerceCategoryListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel" class="offcanvas-title">{{_trans('keyword.Edit').' '._trans('keyword.Category')}}</h5>
                    <button type="button" class="btn-close bg-label-secondary text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="updateCategoryModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="edit_name">{{_trans('keyword.Name')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name"
                                placeholder="Enter Section Category Name" name="edit_name" aria-label="brand name" />
                            <input type="text" hidden value="" name="brand_id" id="special_sections_category_id">
                            <span class="text-danger editNameError error"></span>
                        </div>
                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Status')}} <span class="text-danger">*</span></label>
                            <select id="edit_status" name="edit_status" class="select2 form-select"
                                data-placeholder="Select category status">
                                <option value="1">{{_trans('keyword.Active')}}</option>
                                <option value="0">{{_trans('keyword.Inactive')}}</option>
                            </select>
                            <span class="text-danger editStatusError error"></span>
                        </div>
                        <!-- Submit and reset -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">{{_trans('keyword.Update')}}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" id="closeUpdateModal" class="btn bg-label-danger"
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
        $(function() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : '{{ route('section.category.index') }}',
                    data: function (d){
                        d.status = $('#status').val()
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                        @if(Auth::user()->role_id == 1)
                    {
                        data: 'info',
                        name: 'info'
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
                dom: '<"card-header d-flex flex-wrap pb-2 c-list-header gap-2"' +
                    '<f m-0><"custom-text-div">' +
                    '<"d-flex justify-content-center justify-content-md-end align-items-baseline right-side-buttons "<"dt-action-buttons   d-flex gap-sm-3 justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline flex-sm-row flex-column "lB>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                initComplete: function () {
                    // Set the inner div to display your name
                    $('.custom-text-div').html(`
                    <div class="d-xl-flex gap-3   d-block  " >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 section-category-list-1" style="width: 200px" data-placeholder='{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}' >
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active') }}</option>
                            <option value="0">{{_trans('keyword.Inactive') }}</option>
                        // </select>
                    </div>
                  </div>
`);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.section-category-list-1').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Category",
                },
                // Button for offcanvas
                buttons: [
                    @if(hasPermission('section_category_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>Add Section Category</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "offcanvas",
                            "data-bs-target": "#offcanvasSectionCategoryList",
                        },
                    },
                    @endif

                ],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })


            $('#addModal').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData();

                let name = $("input[name=name]").val();
                let status = $("#brand-status option:selected").val();


                formData.append('name', name);
                formData.append('status', status);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('section.category.store') }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.status == 403) {
                            $('.nameError').text(response.errors?.name ? response.errors
                                ?.name[0] : '');
                            $('.statusError').text(response.errors?.status ? response.errors
                                ?.status[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false);
                            $('#closeAddModal').click();
                            $("input[name=name]").val('');
                        }
                    },
                    error: function(error) {
                        toastr.error(error.responseJSON.message);
                    },
                    complete: function() {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });


            $(document).on("click", ".category_edit_button", function() {

                $id = $(this).attr("data-id");
                $.ajax({
                    url: '/section/category/' + $id,
                    type: 'GET',
                    success: function(response) {
                        $('#special_sections_category_id').val(response.data.id);
                        $('#edit_name').val(response.data.name);
                        $('#edit_status').val(response.data.is_active).trigger('change');

                    },
                    error: function(error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });


            $('#updateCategoryModal').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData();

                let special_sections_category_id = $('#special_sections_category_id').val();
                let name = $("input[name=edit_name]").val();
                let status = $("#edit_status option:selected").val();


                formData.append('name', name);
                formData.append('status', status);
                formData.append('special_sections_category_id', special_sections_category_id);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('section.category.update') }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.status == 403) {
                            $('.editNameError').text(response.errors?.name ? response.errors
                                ?.name[0] : '');
                            $('.editStatusError').text(response.errors?.status ? response.errors
                                ?.status[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            $('#closeUpdateModal').click();
                            table.ajax.reload(null, false)
                        }
                    },
                    error: function(error) {
                        toastr.error(error.responseJSON.message);
                    },
                    complete: function() {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });

            $(document).on("click", ".category_delete_button", function() {

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
                }).then(function(result) {
                    if (result.value) {

                        $.ajax({
                            url: '{{ route('section.category.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                special_sections_category_id: id,
                            },
                            success: function(response) {
                                table.ajax.reload(null, false)
                                toastr.success(response.text);
                            },
                            error: function(error) {
                                console.log(error.responseJSON.message);
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
                            url: '{{ route('section.category.changeStatus') }}',
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
