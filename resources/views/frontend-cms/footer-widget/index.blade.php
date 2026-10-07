@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Footer').' '._trans('keyword.Widget'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Footer').' '._trans('keyword.Widget'),['#'=>_trans('keyword.Frontend').' '._trans('keyword.CMS'),'widget'=>_trans('keyword.Footer').' '._trans('keyword.Widget')]) !!}
        <div class="app-ecommerce-category">
            <!-- Widget List Table -->
            <div class="card">
               {{-- <div class="d-flex gap-3 position-absolute ps-4 p-2 " style="z-index: 100; margin-top: 10px; margin-left: 240px">
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown" style="width: 200px">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>
                </div>--}}
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Title')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th width="50px">{{_trans('keyword.Info')}}</th>
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Offcanvas to edit category -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCategoryEditModal"
                 aria-labelledby="offcanvasEcommerceCategoryListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel" class="offcanvas-title">{{_trans('keyword.Edit').' '._trans('keyword.Widget')}}</h5>
                    <button type="button" class="btn-close bg-label-secondary text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="updateCategoryModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="edit_name">{{_trans('keyword.Name')}}</label>
                            <input type="text" class="form-control" id="edit_name"
                                   placeholder="Enter Footer Widget Name" name="edit_name" aria-label="brand name" />
                            <input type="text" hidden value="" name="brand_id" id="special_sections_category_id">
                            <span class="text-danger editNameError error"></span>
                        </div>
                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</label>
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
                    url : '{{ route('cms.footer-widget.index') }}',
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
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'info',
                        name: 'info'
                    },
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
                    <div class="d-xl-flex gap-3   d-block  " >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown footer-select-2 select2 form-select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active') }}</option>
                            <option value="0">{{_trans('keyword.Inactive') }}</option>
                         </select>
                    </div>
                  </div>
                   `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $('.footer-select-2').select2({
                        allowClear: true,
                    });
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Footer Widget",
                },
                // Button for offcanvas
                buttons: [ ],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })

            $(document).on("click", ".category_edit_button", function() {

                $id = $(this).attr("data-id");
                $.ajax({
                    url: '/cms/footer-widget/' + $id,
                    type: 'GET',
                    success: function(response) {
                        $('#special_sections_category_id').val(response.data.id);
                        $('#edit_name').val(response.data.title);
                        $('#edit_status').val(response.data.active_status).trigger('change');

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

                formData.append('title', name);
                formData.append('status', status);
                formData.append('id', special_sections_category_id);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('cms.footer-widget.update') }}',
                    type: 'POST',
                    // contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.status === 403) {
                            $('.editNameError').text(response.errors?.title ? response.errors
                                ?.title[0] : '');
                            $('.editStatusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                        } else if (response.status === 200) {
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

            $(document).on('change', '.changeStatus', function() {
                const id = $(this).data('id');
                const formData = new FormData();
                formData.append('id', id);
                formData.append('_token', "{{ csrf_token() }}");

                // Show SweetAlert2 confirmation dialog
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
                        // Proceed with the AJAX request if confirmed
                        $.ajax({
                            url: '{{ route('cms.footer-widget.changeStatus') }}',
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
                                    toastr.error(response.message);
                                }
                            },
                            error: function (error) {
                                console.error(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });


        });
    </script>
@endpush
