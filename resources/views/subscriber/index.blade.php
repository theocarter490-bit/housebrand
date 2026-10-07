@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Subscribers'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Subscribers'), ['/subscriber/index'=> _trans('keyword.Subscribers'), _trans('keyword.Subscribers').' '._trans('keyword.List')]) !!}
        <div class="app-ecommerce-category">
            <!-- Subscribers List Table -->
            <div class="card">
                {{-- <div class="d-flex gap-3">
                     <div class="form-group">
                         <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                         <select id='status' class="form-control filter_dropdown" style="width: 200px">
                             <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                             <option value="1">{{_trans('keyword.Active')}}</option>
                             <option value="0">{{_trans('keyword.Inactive')}}</option>
                         </select>
                     </div>

                     <div class="form-group">
                         <label><strong>{{_trans('keyword.Replied')}}:</strong></label>
                         <select id='reply' class="form-control filter_dropdown" style="width: 200px">
                             <option value="">{{_trans('keyword.Select').' '._trans('keyword.Reply').' '._trans('keyword.Type')}} </option>
                             <option value="1">{{_trans('keyword.Yes')}}</option>
                             <option value="0">{{_trans('keyword.No')}}</option>
                         </select>
                     </div>
                 </div>--}}
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Email')}}</th>
                            <th>{{_trans('keyword.Replied')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Send Reply Modal-->
    <div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" id="closeModal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{_trans('keyword.Send Reply')}}</h3>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">{{_trans('keyword.Subject')}} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="subject" name="subject" class="form-control"
                               placeholder="subject" tabindex="-1" required/>
                        <span class="text-danger subjectError error"></span>
                    </div>

                    <div class="col-12 mb-2">
                        <label class="form-label">{{_trans('keyword.To Email')}}</label>
                        <input type="email" id="to_email" name="to_email" class="form-control"
                               tabindex="-1" required readonly/>
                    </div>

                    <input type="hidden" value="" id="user_name" name="user_name">

                    <!-- Message Body -->
                    <div class="row card-body">
                        <div>
                            <label class="form-label">{{_trans('keyword.Message')}} <span
                                    class="text-danger">*</span></label>
                            <div class="form-control p-0 pt-1">
                                <div class="commonEditor-toolbar border-0 border-bottom">
                                    <div class="d-flex justify-content-start">
                                            <span class="ql-formats">
                                                <select class="ql-font"></select>
                                                <select class="ql-size"></select>
                                            </span>
                                        <span class="ql-formats me-0">
                                                <button class="ql-bold"></button>
                                                <button class="ql-italic"></button>
                                                <button class="ql-underline"></button>

                                                <button class="ql-strike"></button>
                                                <button class="ql-list" value="ordered"></button>
                                                <button class="ql-list" value="bullet"></button>
                                                <button class="ql-link"></button>
                                            </span>

                                        <span class="ql-formats">
                                                <button class="ql-header" value="1"></button>
                                                <button class="ql-header" value="2"></button>
                                                <button class="ql-blockquote"></button>
                                                <button class="ql-code-block"></button>
                                        </span>
                                    </div>
                                </div>
                                <div id="message" class="commonEditor border-0 pb-4">

                                </div>
                            </div>
                            <span class="text-danger descriptionError error"></span>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <button id="sendReply" type="submit"
                                class="btn btn-primary me-sm-3 me-1">{{_trans('keyword.Send')}}
                            <span class="loader"></span>
                        </button>
                        <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">
                            {{_trans('keyword.Cancel')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Send Reply Modal-->

@endsection

@push('scripts')
    <script>
        $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('subscriber.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()
                        d.reply = $('#reply').val()
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'replied',
                        name: 'replied'
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
                        <select id='status' class="form-control filter_dropdown select2 subscribers-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Status')}}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Replied')}}:</strong></label>
                        <select id='reply' class="form-control filter_dropdown select2 subscribers-2" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Reply').' '._trans('keyword.Type')}}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Reply').' '._trans('keyword.Type')}} </option>
                            <option value="1">{{_trans('keyword.Yes')}}</option>
                            <option value="0">{{_trans('keyword.No')}}</option>
                        </select>
                    </div>
                  </div>
`);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.subscribers-2').select2({
                        allowClear: true,
                    });
                    $('.subscribers-1').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Email",
                },
                buttons: [
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>{{ _trans('keyword.Export') }} {{ _trans('keyword.Emails') }}</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        action: function () {
                            // Get selected filter values
                            var status = $('#status').val();
                            var reply = $('#reply').val();

                            // Build query string
                            var query = $.param({
                                status: status,
                                reply: reply
                            });

                            // Redirect with query parameters
                            window.location.href = '{{ route('subscriber.exportEmails') }}' + '?' + query;
                        },
                    },
                ],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })


            $(document).on("click", ".reply_button", function () {
                $('.error').text('')
                $id = $(this).attr("data-id");
                $.ajax({
                    url: '/subscriber/' + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#to_email').val(response.data.email);
                        $('#user_name').val(response.data.name);
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $(document).ready(function () {
                $('#sendReply').click(function (e) {
                    e.preventDefault();

                    $('.error').text('');

                    // Get form data
                    var formData = new FormData();

                    let subject = $("input[name=subject]").val();
                    let to_email = $("input[name=to_email]").val();
                    let user_name = $("input[name=user_name]").val();

                    let message = $("#message").children().first().html();

                    if (!subject.trim()) {
                        $('.subjectError').text('The subject is required.');
                        return;
                    }

                    if (!message.trim() || message === '<p><br></p>') {
                        $('.descriptionError').text('The message is required.');
                        return;
                    }


                    formData.append('subject', subject);
                    formData.append('to_email', to_email);
                    formData.append('user_name', user_name);
                    formData.append('message', message);
                    formData.append('_token', "{{ csrf_token() }}");


                    loader.show();
                    submitButton.prop('disabled', true);

                    // Make AJAX request
                    $.ajax({
                        url: '{{ route('subscriber.sendReply') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 403) {
                                // Handle validation errors from the server
                                $('.subjectError').text(response.errors?.subject ? response.errors.subject[0] : '');
                                $('.messageError').text(response.errors?.message ? response.errors.message[0] : '');
                            } else if (response.status === 200) {
                                toastr.success(response.message);
                                $('#closeModal').click();
                                $("input[name=subject]").val('');
                                $("#message").children().first().html('');
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


            $(document).on('change', '.changeStatus', function () {
                const id = $(this).data('id');
                const formData = new FormData();
                formData.append('id', id);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to change the status of this item?",
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
                            url: '{{ route('subscriber.changeStatus') }}',
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

            $(document).ready(function () {
                $('#reset').click(function (e) {
                    e.preventDefault();
                    $("input[name=subject]").val('');
                    $("#message").children().first().html('');
                });
            })

            $(document).on("click", ".delete_button", function () {

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
                            url: '{{ route('subscriber.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
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


        });
    </script>
@endpush
