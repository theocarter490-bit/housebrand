@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Coupons'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Coupons').' '. _trans('keyword.List'),['#'=>_trans('keyword.Subscription').' '._trans('keyword.Management'),'Coupons'=> _trans('keyword.Coupons').' '. _trans('keyword.List')]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Title')}}</th>
                            <th>{{_trans('keyword.Code')}}</th>
                            <th>{{_trans('keyword.Date')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th>{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- Offcanvas to add new customer -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCategoryList"
                 aria-labelledby="offcanvasEcommerceCategoryListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Add')}} {{_trans('keyword.Coupon')}}</h5>
                    <button type="button" id="closeAddModal" class="btn-close bg-label-secondary text-reset"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="addModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="ecommerce-category-title">{{_trans('keyword.Title')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ecommerce-category-title"
                                   placeholder="Enter Coupon Title" name="name" aria-label="category title"/>
                            <span class="text-danger nameError error"></span>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image">{{_trans('keyword.Code')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="code" placeholder="Enter Coupon Code"
                                   id="code"/>
                            <span class="text-danger codeError error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image">{{_trans('keyword.Start Date')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="datetime-local" name="start_date" id="start_date"/>
                            <span class="text-danger start_dateError error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image">{{_trans('keyword.End Date')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="datetime-local" name="end_date" id="end_date"/>
                            <span class="text-danger end_dateError error"></span>
                        </div>

                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select')}} {{_trans('keyword.Status')}}</label>
                            <select id="category-status" name="status" class="select2 form-select"
                                    data-placeholder="Select status">
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
                            <button type="reset" class="btn bg-label-danger"
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
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Edit')}} {{_trans('keyword.Coupon')}}</h5>
                    <button type="button" id="closeEditModal" class="btn-close bg-label-secondary text-reset"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="updateCategoryModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="edit_name">{{_trans('keyword.Title')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" placeholder="Enter category name"
                                   name="edit_name" aria-label="category title"/>
                            <input type="text" hidden value="" name="category_id" id="category_id">
                            <span class="text-danger editNameError error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image">{{_trans('keyword.Code')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="code" placeholder="Enter Coupon Code"
                                   id="edit_code"/>
                            <span class="text-danger editCodeError error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image">{{_trans('keyword.Start Date')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="datetime-local" name="edit_start_date"
                                   id="edit_start_date"/>
                            <span class="text-danger edit_start_dateError error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image">{{_trans('keyword.End Date')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="datetime-local" name="edit_end_date" id="edit_end_date"/>
                            <span class="text-danger edit_end_dateError error"></span>
                        </div>
                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select')}} {{_trans('keyword.Category')}} {{_trans('keyword.Status')}}</label>
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
                            <button type="reset" class="btn bg-label-danger"
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
                    url: '{{ route('coupon.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'code',
                        name: 'code'
                    }, {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [2, "desc"], //set any columns order asc/desc
                dom: '<"card-header d-flex flex-xl-row flex-column  pb-3 gap-3 align-items-xl-end align-items-start category-list-header"' +
                    '<f m-0><"custom-text-div">' +
                    '<"d-flex justify-content-center justify-content-md-end align-items-baseline right-side-buttons"<"dt-action-buttons d-flex xl-gap-0 gap-3 justify-content-center flex-md-row flex-column mb-3 mb-md-0 ps-1 ms-1 align-items-baseline"lB>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                initComplete: function () {
                    // Set the inner div to display your name
                    $('.custom-text-div').html(`
                  <div class="d-xl-flex gap-3   d-block" >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown category-select2 select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active') }}</option>
                            <option value="0">{{_trans('keyword.Inactive') }}</option>
                        </select>
                    </div>
                </div>
`);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('category-list-inner');
                    });
                    $('.category-select2').select2({
                        allowClear: true,
                    });
                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search..",
                },
                // Button for offcanvas
                buttons: [
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>{{_trans('keyword.Add')}} {{_trans('keyword.Coupon')}}</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "offcanvas",
                            "data-bs-target": "#offcanvasEcommerceCategoryList",
                        },
                    },

                ],
            });
            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })

            $('#addModal').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let name = $("input[name=name]").val();
                let code = $("#code").val();
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                let status = $("#category-status option:selected").val();

                formData.append('name', name);
                formData.append('code', code);
                formData.append('status', status);
                formData.append('start_date', start_date);
                formData.append('end_date', end_date);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('coupon.store') }}',
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
                            $('.codeError').text(response.errors?.code ? response.errors
                                ?.code[0] : '');
                            $('.start_dateError').text(response.errors?.start_date ? response
                                    .errors
                                    ?.start_date[0] :
                                '');
                            $('.end_dateError').text(response.errors?.end_date ? response
                                    .errors
                                    ?.end_date[0] :
                                '');
                            $('.statusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false)

                            $("input[name=name]").val('');
                            $("#code").val('');
                            $("#start_date").val('');
                            $("#end_date").val('');


                            $('#closeAddModal').click();

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


            $(document).on("click", ".category_edit_button", function () {
                $id = $(this).attr("data-id");
                $('#edit_status').find('option:selected').attr("selected", false);
                $('#edit_status').trigger('change.select2');
                $.ajax({
                    url: '/coupon/' + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#category_id').val(response.data.id);
                        $('#edit_name').val(response.data.title);
                        $('#edit_code').val(response.data.title);
                        $('#edit_status').find('option[value="' + response.data.is_active +
                            '"]').attr("selected", "selected");
                        $('#edit_start_date').val(response.data.starts_at);
                        $('#edit_end_date').val(response.data.expires_at);


                        $('#edit_status').trigger('change.select2');

                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $(document).on('change', '.changeStatus', function () {
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
                            url: '{{ route('coupon.changeStatus') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
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
                    table.ajax.reload(null, false);
                });
            });


            $('#updateCategoryModal').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let category_id = $('#category_id').val();
                let name = $("input[name=edit_name]").val();
                let code = $("#edit_code").val();
                let status = $("#edit_status option:selected").val();
                let start_date = $("#edit_start_date").val();
                let end_date = $("#edit_end_date").val();


                formData.append('name', name);
                formData.append('code', code);
                formData.append('status', status);
                formData.append('start_date', start_date);
                formData.append('end_date', end_date);

                formData.append('category_id', category_id);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('coupon.update') }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        if (response.status == 403) {
                            table.ajax.reload(null, false)
                            $('.editNameError').text(response.errors?.name ? response.errors
                                ?.name[0] : '');

                            $('.editCodeError').text(response.errors?.code ?
                                response
                                    .errors
                                    ?.code[0] :
                                '');
                            $('.edit_start_dateError').text(response.errors?.start_date ?
                                response
                                    .errors
                                    ?.start_date[0] :
                                '');
                            $('.edit_end_dateError').text(response.errors?.end_date ?
                                response
                                    .errors
                                    ?.end_date[0] :
                                '');
                            $('.editStatusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false)
                            $('#closeEditModal').click();
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
                            url: '{{ route('coupon.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                category_id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false);
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

        });
    </script>
@endpush
