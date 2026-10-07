@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Email') . ' ' . _trans('keyword.Campaign'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Email') . ' ' . _trans('keyword.Campaign'), [
            '#' => _trans('keyword.Marketing'),
            'emailcampaign' => _trans('keyword.Email') . ' ' . _trans('keyword.Campaign'),
        ]) !!}

        <!-- Email Campaign List Table -->
        <div class="card">
            {{-- <div class="d-flex gap-3  "
               >
                 <div class="form-group">
                     <label><strong>{{ _trans('keyword.Status') }} :</strong></label>
                     <select id='status' class="form-control filter_dropdown" style="width: 200px">
                         <option value="">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Status') }}</option>
                         <option value="1">{{ _trans('keyword.Active') }}</option>
                         <option value="0">{{ _trans('keyword.Deactive') }}</option>
                     </select>
                 </div>

                 <div class="form-group">
                     <label><strong>{{ _trans('keyword.Send') . ' ' . _trans('keyword.Status') }} :</strong></label>
                     <select id='sending_status' class="form-control filter_dropdown" style="width: 200px">
                         <option value="">
                             {{ _trans('keyword.Select') . ' ' . _trans('keyword.Send') . ' ' . _trans('keyword.Status') }}
                         </option>
                         <option value="1">{{ _trans('keyword.Send') }}</option>
                         <option value="0">{{ _trans('keyword.Pending') }}</option>
                     </select>
                 </div>
             </div>--}}
            <div class="card-datatable table-responsive">
                <table class="data-table table border-top">
                    <thead>
                    <tr>
                        <th>{{_trans('keyword.SL')}}</th>
                        <th>{{ _trans('keyword.Title') }}</th>
                        <th>{{ _trans('keyword.Message') }}</th>
                        <th>{{ _trans('keyword.Attachment') }}</th>
                        <th>{{ _trans('keyword.Send') . ' ' . _trans('keyword.Status') }}</th>
                        <th>{{ _trans('keyword.Status') }}</th>
                        @if(Auth::user()->role_id == \App\Models\Role::SUPER_ADMIN)
                            <th width="50px">{{ _trans('keyword.Author') }}</th>
                        @endif
                        <th width="100px">{{ _trans('keyword.Action') }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    <!-- Create Campaign Modal -->
    <div class="modal fade" id="createCampaignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" id="closeModal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">
                            {{ _trans('keyword.Create') . ' ' . _trans('keyword.Email') . ' ' . _trans('keyword.Campaign') }}
                        </h3>
                    </div>
                    <form id="createForm">
                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Title') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control"
                                   placeholder="Enter title"/>
                            <span class="text-danger titleError error"></span>
                        </div>

                        <div>
                            <label for="formFileLg" class="form-label">{{ _trans('keyword.Attachment') }}</label>
                            <input class="form-control form-control mb-2" id="formFileLg" name="attachment" type="file">
                            <span class="text-danger attachmentError error"></span>
                        </div>

                        <div class="row card-body">
                            <div class="col-12 mb-2">
                                <label class="form-label">{{ _trans('keyword.Message') }}</label>
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
    <!-- End Create Campaign Modal -->


    <!-- Edit Campaign Modal -->
    <div class="modal fade" id="editCampaignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">
                            {{ _trans('keyword.Edit') . ' ' . _trans('keyword.Email') . ' ' . _trans('keyword.Campaign') }}
                        </h3>
                    </div>
                    <input type="hidden" id="editId" name="id">
                    <div class="col-12 mb-3">
                        <label class="form-label">{{ _trans('keyword.Title') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="editTitle" name="title" class="form-control"
                               placeholder="Enter title" required/>
                        <span class="text-danger editTitleError error"></span>
                    </div>

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
                            <label class="form-label">{{ _trans('keyword.Message') }}</label>
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
                                id="campaignUpdate">{{ _trans('keyword.Update') }}
                            <span class="loader"></span>
                        </button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">{{ _trans('keyword.Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Campaign Modal -->

@endsection

@push('scripts')
    <script>
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('marketing.campaign.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()
                        d.sending_status = $('#sending_status').val()
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
                        data: 'message',
                        name: 'message',
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
                        data: 'is_sent',
                        name: 'is_sent'
                    },
                    {
                        data: 'status',
                        name: 'active_status'
                    },
                        @if(Auth::user()->role_id == 1)
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                        @endif
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
                        <label><strong>{{ _trans('keyword.Status') }} :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 email-campaign-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Status')}}">
                            <option value="">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Status') }}</option>
                            <option value="1">{{ _trans('keyword.Active') }}</option>
                            <option value="0">{{ _trans('keyword.Inactive') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>{{ _trans('keyword.Send') . ' ' . _trans('keyword.Status') }} :</strong></label>
                        <select id='sending_status' class="form-control filter_dropdown select2 email-campaign-2" style="width: 200px" data-placeholder="  {{ _trans('keyword.Select') . ' ' . _trans('keyword.Send') . ' ' . _trans('keyword.Status') }}">
                            <option value="">
                                {{ _trans('keyword.Select') . ' ' . _trans('keyword.Send') . ' ' . _trans('keyword.Status') }}
                    </option>
              <option value="1">{{ _trans('keyword.Send') }}</option>
                            <option value="0">{{ _trans('keyword.Pending') }}</option>
                        </select>
                    </div>

                  </div>
`);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.email-campaign-1').select2({
                        allowClear: true,
                    });
                    $('.email-campaign-2').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100],
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Campaign",
                },
                buttons: [{
                    text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>Add Campaign</span>',
                    className: "create-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                    action: function () {
                        $('#createCampaignModal').modal('show');
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
                $('#formFileLg').val('');
            }

            // Open Create Campaign modal
            $('.create-new').click(function () {
                resetCreateModal();
                $('#createCampaignModal').modal('show');
            });

            // Submit form to create new campaign
            $('#createForm').submit(function (event) {
                event.preventDefault();

                $('.error').text('');
                let message = $("#createMessageEditor").children().first().html();

                var formData = new FormData();
                formData.append('title', $('#title').val());
                formData.append('message', message);
                formData.append('_token', "{{ csrf_token() }}");

                if ($('#formFileLg')[0].files.length > 0) {
                    formData.append('attachment', $('#formFileLg')[0].files[0]);
                }

                loader.show();
                submitButton.prop('disabled', true);

                $.ajax({
                    url: '{{ route('marketing.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 403) {
                            if(response.message){
                                toastr.error(response.message);
                            }
                            $('.titleError').text(response.errors?.title ? response.errors
                                .title[0] : '');

                            $('.attachmentError').text(response.errors?.attachment ? response
                                .errors
                                .attachment[0] : '');

                            $('.messageError').text(response.errors?.message ? response.errors
                                .message[0] : '');

                        } else if (response.status === 200) {
                            $('#createCampaignModal').modal('hide');
                            table.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    },
                    error: function (error) {
                        console.error('Error creating campaign:', error);
                        toastr.error('Failed to create campaign. Please try again later.');
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Edit campaign modal handler
            $(document).on('click', '.edit-campaign', function () {
                $('.editTitleError').text('');
                $('.editAttachmentError').text('');

                var id = $(this).data('id');

                $.ajax({
                    url: '/marketing/campaign/' + id + '/edit',
                    type: 'GET',
                    success: function (response) {
                        $('#editId').val(response.id);
                        $('#editTitle').val(response.title);
                        $('#editMessageEditor').html(response.message);
                        if (response.attachment_element) {
                            $('#currentAttachmentSection').html(
                                `<label class="form-label">Current Attachment</label>
                            <div class="mb-2">
                                ${response.attachment_element}
                            </div>`
                            );
                        } else {
                            $('#currentAttachmentSection').html('');
                        }
                        reInitQuillEditor();
                        $('#editCampaignModal').modal('show');

                    },
                    error: function (error) {
                        console.error('Error fetching campaign data:', error);
                        toastr.error('Failed to fetch campaign data.');
                    }
                });
            });


            $(document).ready(function () {
                // Submit form to edit campaign
                $('#campaignUpdate').click(function (e) {
                    e.preventDefault();

                    var formData = new FormData();

                    let campaign_id = $('#editId').val();
                    let title = $('#editTitle').val();
                    let message = $('#editMessageEditor').children().first().html();
                    let attachment = $('#editFormFileLg')[0].files[0];

                    formData.append('id', campaign_id);
                    formData.append('title', title);
                    formData.append('message', message);
                    if (attachment) {
                        formData.append('attachment', attachment);
                    }
                    formData.append('_token', "{{ csrf_token() }}");

                    loader.show();
                    submitButton.prop('disabled', true);

                    $.ajax({
                        url: '/marketing/campaign',
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
                                $('#editCampaignModal').modal('hide');
                                table.ajax.reload(null, false);
                                toastr.success(response.message);
                            }
                        },
                        error: function (error) {
                            console.error('Error updating campaign:', error);
                            toastr.error(
                                'Failed to update campaign. Please try again later.'
                            );
                        },
                        complete: function () {
                            loader.hide();
                            submitButton.prop('disabled', false);
                        }
                    });
                });
            });

            // delete email campaign
            $(document).on("click", ".delete-campaign", function () {
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
                            url: '{{ route('marketing.delete') }}',
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
                                    text: response.message,
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

            // send emails
            $(document).on("click", ".send-campaign", function () {
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
                            url: '{{ route('marketing.send.email') }}',
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
                            url: '{{ route('marketing.changeStatus') }}',
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
                    } else {
                        table.ajax.reload(null, false);
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
