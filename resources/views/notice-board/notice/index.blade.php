@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Notices'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Notices'), [
            '#' => _trans('keyword.Notice Management'),
            'emailcampaign' => _trans('keyword.Notices'),
        ]) !!}
        <div class="app-ecommerce-category">
            <!-- Email Notice List Table -->
            <div class="card">
                {{--  <div class="d-flex gap-3  ">
                      <div class="form-group">
                          <label><strong>{{ _trans('keyword.Status') }} :</strong></label>
                          <select id='status' class="form-control filter_dropdown" style="width: 200px">
                              <option value="">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Status') }}</option>
                              <option value="1">{{ _trans('keyword.Published') }}</option>
                              <option value="0">{{ _trans('keyword.Unpublished') }}</option>
                          </select>
                      </div>

                  </div>--}}
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Title') }}</th>
                            <th>{{ _trans('keyword.Type') }}</th>
                            <th>{{ _trans('keyword.Description') }}</th>
                            <th>{{ _trans('keyword.Attachment') }}</th>
                            <th>{{ _trans('keyword.Publish Status') }}</th>
                            <th>{{ _trans('keyword.Published time') }}</th>
                            <th>{{ _trans('keyword.Send') . ' ' . _trans('keyword.Status') }}</th>
                            <th width="50px">{{ _trans('keyword.Author') }}</th>
                            <th width="100px">{{ _trans('keyword.Action') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Notice Modal -->
    <div class="modal fade" id="createNoticeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" id="closeModal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">
                            {{ _trans('keyword.Create') . ' ' . _trans('keyword.Notice') }}</h3>
                    </div>
                    <form id="createForm">
                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Title') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control"
                                   placeholder="Enter title"/>
                            <span class="text-danger titleError error"></span>
                        </div>

                        <div class="mb-2 ecommerce-select2-dropdown">
                            <label class="form-label">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Type') }}
                                <span
                                    class="text-danger">*</span></label>
                            <select id="notice_type" name="notice_type" class="select2 form-select"
                                    style="width: 100%"
                                    data-placeholder="Select type">
                                <option value="">{{ _trans('keyword.Select Type') }}</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger typeError error"></span>
                        </div>

                        <div>
                            <label for="formFileLg" class="form-label">{{ _trans('keyword.Attachment') }}</label>
                            <input class="form-control form-control mb-2" id="formFileLg" name="attachment" type="file">
                            <span class="text-danger attachmentError error"></span>
                        </div>

                        <div class="row card-body">
                            <div class="col-12 mb-2">
                                <label class="form-label">{{ _trans('keyword.Description') }}</label>
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
                                    <div class="commonEditor1 border-0 pb-4" id="createMessageEditor"></div>
                                </div>
                                <span class="text-danger messageError error"></span>
                            </div>

                        </div>
                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Add') }}
                                <span class="loader"></span>
                            </button>
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">{{ _trans('keyword.Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Create Notice Modal -->


    <!-- Edit Notice Modal -->
    <div class="modal fade" id="editNoticeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">
                            {{ _trans('keyword.Edit') . ' ' . _trans('keyword.Notice') }}</h3>
                    </div>
                    <input type="hidden" id="editId" name="id">
                    <div class="col-12 mb-3">
                        <label class="form-label">{{ _trans('keyword.Title') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="editTitle" name="title" class="form-control"
                               placeholder="Enter title"/>
                        <span class="text-danger editTitleError error"></span>
                    </div>

                    {{-- Expense Type --}}
                    <div class="mb-2 ecommerce-select2-dropdown">
                        <label class="form-label">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Type') }} <span
                                class="text-danger">*</span></label>
                        <select id="editExpenseType" name="expense_type" class="select2 form-select"
                                style="width: 100%"
                                data-placeholder="Select type" required>
                            <option value="">{{ _trans('keyword.Select Type') }}</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger editTypeError error"></span>
                    </div>
                    {{-- Expense Type --}}

                    <div class="row mb-2">
                        <div class="col-md-8">
                            <label for="editFormFileLg" class="form-label">{{ _trans('keyword.Attachment') }}</label>
                            <input class="form-control form-control mb-2" id="editFormFileLg" name="attachment"
                                   type="file">
                            <span class="text-danger editAttachmentError error"></span>

                        </div>
                        <div class="col-md-4">
                            <div id="currentAttachmentSection"></div>
                        </div>
                    </div>


                    <div class="row card-body">
                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Description') }}</label>
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
                                <div class="commonEditor border-0 pb-4" id="editMessageEditor"></div>
                            </div>
                            <span class="text-danger messageError error"></span>
                        </div>

                    </div>


                    <div class="col-12 text-center mt-2">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                id="noticeUpdate">{{ _trans('keyword.Update') }}
                            <span class="loader"></span>
                        </button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">{{ _trans('keyword.Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Notice Modal -->

@endsection

@push('scripts')
    <script>
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('notice-board.notice.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()

                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'type',
                        name: 'type_id'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function (data, type, row) {
                            const tempElement = document.createElement('div');
                            tempElement.innerHTML = data;
                            const plainText = tempElement.textContent || tempElement.innerText ||
                                '';
                            const truncated = plainText.length > 100 ? plainText.substr(0, 100) +
                                '...' : plainText;
                            return '<div style="width: 200px; white-space: normal; word-wrap: break-word;">' +
                                truncated + '</div>';
                        }
                    },
                    {
                        data: 'attachment',
                        name: 'attachment',

                    },

                    {
                        data: 'status',
                        name: 'active_status'
                    },
                    {
                        data: 'published_at',
                        name: 'published_at'
                    },
                    {
                        data: 'is_sent',
                        name: 'is_sent'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false

                    },
                ],

                order: [
                    [0, 'desc']
                ],
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
                        <select id='status' class="form-control filter_dropdown select2 expenses-management-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Status')}}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            <option value="1">{{ _trans('keyword.Active') }}</option>
                            <option value="0">{{ _trans('keyword.Inactive') }}</option>
                         </select>
                    </div>
                  </div>
                  `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.expenses-management-1').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100],
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Notice",
                },
                buttons: [{
                    text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span >Add Notice</span>',
                    className: "create-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                    action: function () {
                        $('#createNoticeModal').modal('show');
                    }
                }],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })


            function resetCreateModal() {
                $('#createForm').trigger('reset');
                $('.titleError').text('');
                $('.messageError').text('');
                $("#createMessageEditor").children().first().html('');
                $('#formFileLg').val('');
                $('.error').text('');
                $('#notice_type').val(null).trigger('change');
            }

            // Open Create notice modal
            $('.create-new').click(function () {
                resetCreateModal();
                $('#createNoticeModal').modal('show');
            });

            // Submit form to create new notice
            $('#createForm').submit(function (event) {
                event.preventDefault();

                $('.error').text('');
                let description = $("#createMessageEditor").children().first().html();

                var formData = new FormData();
                formData.append('title', $('#title').val());
                formData.append('description', description);
                formData.append('notice_type', $('#notice_type').val());
                formData.append('_token', "{{ csrf_token() }}");

                if ($('#formFileLg')[0].files.length > 0) {
                    formData.append('attachment', $('#formFileLg')[0].files[0]);
                }

                loader.show();
                submitButton.prop('disabled', true);

                $.ajax({
                    url: '{{ route('notice-board.notice.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 403) {
                            $('.titleError').text(response.errors?.title ? response.errors
                                .title[0] : '');

                            $('.attachmentError').text(response.errors?.attachment ? response
                                .errors
                                .attachment[0] : '');

                            $('.messageError').text(response.errors?.description ? response
                                .errors
                                .description[0] : '');
                            $('.typeError').text(response.errors?.notice_type ? response
                                .errors
                                .notice_type[0] : '');
                            if (response.message) {
                                toastr.error(response.message);
                            }

                        } else if (response.status === 200) {
                            $('#createNoticeModal').modal('hide');
                            table.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    },
                    error: function (error) {
                        console.error('Error creating notice:', error);
                        toastr.error('Failed to create notice. Please try again later.');
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Edit campaign modal handler
            $(document).on('click', '.edit-notice', function () {
                var id = $(this).data('id');

                $.ajax({
                    url: '/notice-board/notice/' + id + '/edit',
                    type: 'GET',
                    success: function (response) {
                        $('#editId').val(response.id);
                        $('#editTitle').val(response.title);
                        $('#editMessageEditor').html(response.description);
                        $('#editExpenseType').val(response.notice_type).trigger('change');

                        if (response.attachment) {
                            $('#currentAttachmentSection').html(
                                `<label class="form-label">Current Attachment</label>
                            <div class="mb-2">
                                ${response.attachment}
                            </div>`
                            );
                        } else {
                            $('#currentAttachmentSection').html('');
                        }
                        reInitQuillEditor();
                        $('#editNoticeModal').modal('show');

                    },
                    error: function (error) {
                        console.error('Error fetching campaign data:', error);
                        toastr.error('Failed to fetch campaign data.');
                    }
                });
            });


            $(document).ready(function () {
                // Submit form to edit notice
                $('#noticeUpdate').click(function (e) {
                    e.preventDefault();

                    var formData = new FormData();

                    let notice_id = $('#editId').val();

                    let title = $('#editTitle').val();
                    let description = $('#editMessageEditor').children().first().html();
                    let notice_type = $('#editExpenseType').val();
                    let attachment = $('#editFormFileLg')[0].files[0];

                    formData.append('id', notice_id);
                    formData.append('title', title);
                    formData.append('description', description);
                    formData.append('notice_type', notice_type);

                    if (attachment) {
                        formData.append('attachment', attachment);
                    }
                    formData.append('_token', "{{ csrf_token() }}");

                    loader.show();
                    submitButton.prop('disabled', true);

                    $.ajax({
                        url: '/notice-board/notice/update',
                        type: 'post',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.status === 403) {
                                $('.editTitleError').text(response.errors?.title ?
                                    response.errors
                                        .title[0] : '');
                                $('.editAttachmentError').text(response.errors
                                    ?.attachment ? response.errors
                                    .attachment[0] : '');
                            } else if (response.status === 200) {
                                $('#editNoticeModal').modal('hide');
                                table.ajax.reload(null, false);
                                toastr.success(response.message);
                            }
                        },
                        error: function (error) {
                            console.error('Error updating notice:', error);
                            toastr.error(
                                'Failed to update notice. Please try again later.'
                            );
                        },
                        complete: function () {
                            loader.hide();
                            submitButton.prop('disabled', false);
                        }
                    });
                });
            });

            // delete Notice
            $(document).on("click", ".delete-notice", function () {
                let id = $(this).data("id");
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
                            url: '{{ route('notice-board.notice.destroy') }}',
                            method: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.text,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
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
                            url: '{{ route('notice-board.notice.changeStatus') }}', type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error',
                                        customClass: {
                                            confirmButton: 'btn btn-success waves-effect waves-light'
                                        }
                                    });

                                }
                                table.ajax.reload(null, false);
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    } else {
                        table.ajax.reload(null, false);
                    }
                });
            });

            $(document).on('click', '.send-email-notice', function () {
                let id = $(this).data("id");
                let anchor = $(this);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will send the campaign emails!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, send it!',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        $(anchor).addClass('disabled');
                        $.ajax({
                            url: '{{ route('notice-board.notice.send.email') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                if (response.status === 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Emails Sent!',
                                        text: response.message,
                                        customClass: {
                                            confirmButton: 'btn btn-success waves-effect waves-light'
                                        }
                                    });
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Something went wrong!',
                                        text: response.message,
                                        customClass: {
                                            confirmButton: 'btn btn-success waves-effect waves-light'
                                        }
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

        function reInitQuillEditor() {

            const commonEditor = document.querySelector('.commonEditor');
            if (commonEditor) {
                new Quill(commonEditor, {
                    modules: {
                        toolbar: '.commonEditor-toolbar'
                    },
                    placeholder: 'Description',
                    theme: 'snow'
                });
            }
        }
    </script>
@endpush
