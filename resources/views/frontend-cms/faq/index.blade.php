@extends('layouts.master')

@section('title', $title ?? _trans('keyword.FAQs'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.FAQs'), ['#'=> 'Frontend CMS', '/cms/faq' => _trans('keyword.FAQs'), 'faq' =>_trans('keyword.FAQs').' '._trans('keyword.List')]) !!}
        <div class="app-ecommerce-category">
            <!-- FAQ List Table -->
            <div class="card">
                {{--<div class="d-flex gap-3 position-absolute ps-4 p-2 " style="z-index: 100; margin-top: 10px; margin-left: 240px">
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='filter_status' class="form-control filter_dropdown" style="width: 200px">
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
                            <th>{{_trans('keyword.Description')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            @if(Auth::user()->role_id == \App\Models\Role::SUPER_ADMIN)
                            <th>{{_trans('keyword.Info')}}</th>
                            @endif
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addFaqModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{_trans('keyword.Add').' '._trans('keyword.New').' '._trans('keyword.FAQs')}}</h3>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">{{_trans('keyword.Question')}} <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="Enter title"
                               tabindex="-1" required/>
                        <span class="text-danger titleError error"></span>

                    </div>

                    <!--Faq Description -->
                    <div class="mt-3">
                        <label>{{_trans('keyword.Answer')}} </label>
                        <div class="form-control p-0 pt-1">
                            <div class="commonEditor-toolbar border-0 border-bottom">
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
                            <div class="commonEditor border-0 pb-4" id="description">
                            </div>
                        </div>
                        <span class="text-danger shippingPolicyError error"></span>
                    </div>

                    <!-- Status -->
                    <div class="mt-3 col ecommerce-select2-dropdown">
                        <label class="form-label mb-1" for="status-org">{{_trans('keyword.Status')}}</label>
                        <select id="status" name="status" class="select2 form-select">
                            <option value="1" selected>{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>


                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1" id="addFaq">{{_trans('keyword.Submit')}}
                            <span class="loader"></span>
                        </button>
                        <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">
                            {{_trans('keyword.Cancel')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Add Modal -->


    <!-- Edit Modal -->
    <div class="modal fade" id="editFaqModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" id="closeModal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{_trans('keyword.Edit').' '._trans('keyword.FAQs')}}</h3>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">{{_trans('keyword.Question')}} <span class="text-danger">*</span></label>
                        <input type="text" id="editTitle" name="editTitle" class="form-control"
                               placeholder="Enter title"
                               tabindex="-1" required/>
                        <span class="text-danger titleError error"></span>
                    </div>

                    <input type="text" id="faq_id" name="faq_id" hidden>


                    <!--Faq Description -->
                    <div class="mt-3">
                        <label>{{_trans('keyword.Answer')}}</label>
                        <div class="form-control p-0 pt-1">
                            <div class="commonEditor1-toolbar border-0 border-bottom">
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
                            <div class="commonEditor1 border-0 pb-4" id="editDescription">
                            </div>
                        </div>
                        <span class="text-danger shippingPolicyError error"></span>
                    </div>

                    <!-- Status -->
                    <div class="mt-3 col ecommerce-select2-dropdown">
                        <label class="form-label mb-1" for="status-org">{{_trans('keyword.Status')}}</label>
                        <select id="editStatus" name="editStatus" class="select2 form-select">
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>


                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1" id="updateFaq">{{_trans('keyword.Submit')}}
                            <span class="loader"></span>
                        </button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">
                            {{_trans('keyword.Cancel')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Edit Modal -->

@endsection

@push('scripts')
    <script>
        $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : '{{ route('cms.faq.index') }}',
                    data: function (d){
                        d.filter_status = $('#filter_status').val()
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false,
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function (data, type, row) {
                            const truncated = data.length > 100 ? data.substr(0, 100) + '...' :
                                data;
                            return '<div style="width: 200px; white-space: normal; word-wrap: break-word;">' +
                                truncated + '</div>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    @if(Auth::user()->role_id == 1)
                    {
                        data: 'user',
                        name: 'user'
                    },
                    @endif
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [0, "desc"], //set any columns order asc/desc
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
                        <select id='filter_status' class="form-control filter_dropdown footer-select-2 select2 form-select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
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
                    searchPlaceholder: "Search FAQ",
                },
                // Button for offcanvas
                buttons: [
                    @if(hasPermission('faqs_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2 "></i><span class="d-none d-sm-inline-block">Add FAQ</span>',
                        className: "create-new btn btn-primary ms-2 waves-effect waves-light d-flex align-items-center justify-content-center text-nowrap",
                        attr: {
                            "data-bs-toggle": "modal",
                            "data-bs-target": "#addFaqModal",
                        },
                    },
                    @endif
                ],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })

            $(document).ready(function () {
                $('#addFaq').click(function (e) {
                    e.preventDefault();
                    $('.error').text('')

                    var formData = new FormData();

                    let title = $("input[name=title]").val();
                    let status = $("#status option:selected").val();
                    let description = $("#description").children().first().html();

                    formData.append('title', title);
                    formData.append('description', description);
                    formData.append('status', status);
                    formData.append('_token', "{{ csrf_token() }}");

                    loader.show();
                    submitButton.prop('disabled', true);


                    $.ajax({
                        url: '{{ route('cms.faq.store') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 403) {
                                $('.titleError').text(response.errors?.title ? response.errors
                                    .title[0] : '');
                                $('.descError').text(response.errors?.short_desc ? response
                                    .errors.short_desc[0] : '');
                            } else if (response.status === 200) {
                                // Clear form fields
                                $("input[name=title]").val('');
                                $("#status").val('1').trigger('change'); // Reset status to "Active"
                                $("#description").children().first().html('');

                                toastr.success(response.message);
                                $('#closeUpdateModal').click();
                                table.ajax.reload(null, false);
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
            });

            $(document).ready(function (){
                $('#reset').click(function(e) {
                    e.preventDefault();
                    $("input[name=title]").val('');
                    $("#status option:selected").prop('selected', false);
                    $("#description").children().first().html('');
                });
            })

            $(document).on("click", ".category_edit_button", function () {
                $('.error').text('')
                $id = $(this).attr("data-id");
                $('#editStatus').val(null).trigger('change');
                $.ajax({
                    url: '/cms/faq/edit/' + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#faq_id').val(response.data.id);
                        $("#editTitle").val(response.data.title);
                        $("#editDescription").html(response.data.description);
                        $('#editStatus').val(response.data.active_status).trigger('change');

                        reInitQuillEditor();

                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $(document).ready(function () {
                $('#updateFaq').click(function (e) {
                    e.preventDefault();

                    var formData = new FormData();

                    let faq_id = $("input[name=faq_id]").val();
                    let title = $("input[name=editTitle]").val();
                    let status = $("#editStatus option:selected").val();
                    let description = $("#editDescription").children().first().html();

                    formData.append('id', faq_id);
                    formData.append('title', title);
                    formData.append('description', description);
                    formData.append('status', status);
                    formData.append('_token', "{{ csrf_token() }}");

                    loader.show();
                    submitButton.prop('disabled', true);

                    $('.error').text('')
                    $.ajax({
                        url: '{{ route('cms.faq.update') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 403) {
                                $('.titleError').text(response.errors?.title ? response.errors
                                    .title[0] : '');
                                $('.descError').text(response.errors?.short_desc ? response
                                    .errors.short_desc[0] : '');
                            } else if (response.status === 200) {
                                toastr.success(response.message);
                                $('#closeModal').click();
                                table.ajax.reload(null, false);
                            }
                        },
                        error: function (error) {
                            toastr.error(error.responseJSON.message);
                        },

                        complete: function() {
                            loader.hide();
                            submitButton.prop('disabled', false);
                        }
                    });
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
                            url: '{{ route('cms.faq.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function(response) {
                                table.ajax.reload(null, false)
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
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
                            url: '{{ route('cms.faq.changeStatus') }}',
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

        function reInitQuillEditor() {

            const commonEditor1 = document.querySelector('.commonEditor1');
            if (commonEditor1) {
                new Quill(commonEditor1, {
                    modules: {
                        toolbar: '.commonEditor1-toolbar'
                    },
                    placeholder: 'Description',
                    theme: 'snow'
                });
            }
        }
    </script>
@endpush
