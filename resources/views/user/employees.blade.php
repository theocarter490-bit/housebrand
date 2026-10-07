@extends('layouts.master')

@section('title', "Employees")

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb( $roleName .' '._trans('keyword.List'),['#'=>_trans('keyword.User').' '._trans('keyword.Management'),'user'=> $roleName .' '._trans('keyword.List')]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                {{--          <div class="d-flex gap-3 position-absolute ps-4 p-2" style="z-index: 100; margin-top: 10px; margin-left: 240px">
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
                            <th>{{_trans('keyword.Image')}}</th>
                            <th>{{_trans('keyword.Name')}}</th>
                            <th>{{_trans('keyword.Email')}}</th>
                            <th>{{_trans('keyword.Role')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{_trans('keyword.Add').' '._trans('keyword.New').' '._trans('keyword.Employee')}}</h3>
                    </div>
                    <form class="row g-3" action="{{ route('user.store') }}" method="post"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6 mb-2">
                            <label class="form-label">{{_trans('keyword.Name')}}<span
                                    class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" required value="{{old('name')}}"
                                   class="form-control"
                                   placeholder="Enter name"/>
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Role')}}<span
                                    class="text-danger">*</span></label>
                            <select id="role" name="role" required class="select2 form-select "
                                    data-placeholder="Select Role"
                                    style="width: 100%">
                                <option
                                    value="">{{_trans('keyword.Select').' '._trans('keyword.User').' '._trans('keyword.Role')}}</option>
                                @foreach ($roles as $role)
                                    <option @if($role->id == old('role'))selected
                                            @endif value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">{{_trans('keyword.Email')}}<span
                                    class="text-danger">*</span></label>
                            <input type="email" id="email" value="{{old('email')}}" required name="email"
                                   class="form-control"
                                   placeholder="Enter email"/>
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">{{_trans('keyword.Phone')}}<span
                                    class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone" required value="{{old('phone')}}"
                                   class="form-control"
                                   placeholder="Enter phone"/>
                            @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-password-toggle col-md-6 mb-2">
                            <div class="d-flex justify-content-between">
                                <label class="form-label">{{_trans('keyword.Password')}}<span
                                        class="text-danger">*</span></label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" required class="form-control" name="password"
                                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                       aria-describedby="password"/>
                                <span class="input-group-text cursor-pointer" id="c-toggle-password">
                                        <i class="ti ti-eye-off" id="toggle-icon"></i>
                                    </span>
                            </div>
                            @error('password')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-password-toggle col-md-6 mb-2">
                            <div class="d-flex justify-content-between">
                                <label class="form-label">{{_trans('keyword.Confirm').' '._trans('keyword.Password')}}
                                    <span class="text-danger">*</span></label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control" required
                                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                       aria-describedby="password"/>
                                <span class="input-group-text cursor-pointer" id="c-toggle-password">
                                        <i class="ti ti-eye-off" id="toggle-icon"></i>
                                    </span>
                            </div>
                            @error('password')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{--                        <div class="col-md-6 mb-2">--}}
                        {{--                            <label class="form-label">{{_trans('keyword.Confirm').' '._trans('keyword.Password')}}<span class="text-danger">*</span></label>--}}
                        {{--                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm Password"/>--}}
                        {{--                            @error('password_confirmation')--}}
                        {{--                            <span class="text-danger">{{ $message }}</span>--}}
                        {{--                            @enderror--}}
                        {{--                        </div>--}}

                        <div class="col-md-6 mb-2">
                            <label class="form-label">{{_trans('keyword.Image')}}</label>
                            <input type="file" id="avatar" name="avatar" class="form-control"
                                   placeholder="Employee Profile"/>
                            @error('avatar')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label mb-1" for="status-org">{{_trans('keyword.Status')}}</label>
                            <select id="status" name="status" class="select2 form-select ">
                                <option value="1" selected>{{_trans('keyword.Active')}}</option>
                                <option value="0">{{_trans('keyword.Inactive')}}</option>
                            </select>
                            @error('status')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col mb-2">
                            <label class="form-label">{{_trans('keyword.Address')}}</label>
                            <textarea id="address" name="address" class="form-control"
                                      placeholder="Employee Address">{{old('address')}}</textarea>
                            @error('address')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                    id="addFaq">{{_trans('keyword.Submit')}}
                                <span class="loader"></span>
                            </button>
                            <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">{{_trans('keyword.Cancel')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--/ Add Modal -->

    <!-- Send Reply Modal -->
    <div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" id="closeModal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{_trans('keyword.Send Credential')}}</h3>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">{{_trans('keyword.Subject')}} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="subject" name="subject" class="form-control"
                               value="Your {{shopSetting()->shop_name}} account login credentials" placeholder="subject"
                               tabindex="-1" required/>
                        <input type="hidden" id="send_id" name="send_id" class="form-control"
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
                            <span class="text-danger descriptionError error">
                                    </span>
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
            @if($errors->any())

            $('#addEmployeeModal').modal('show')
            @endif
            @foreach($errors->all() as $error)
            toastr.error('{!! $error !!}');
            @endforeach

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('employee.employeeList') }}',
                    data: function (d) {
                        d.status = $('#active_status').val()
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'avatar',
                        name: 'avatar'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role_id',
                        name: 'role_id'
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
                        <select id='active_status' class="form-control filter_dropdown select2 employee-list-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Status')}}">>
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active') }}</option>
                            <option value="0">{{_trans('keyword.Inactive') }}</option>
                         </select>
                    </div>
                  </div>
`);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.employee-list-1').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Employee",
                },
                // Button for offcanvas
                buttons: [
                        @if(hasPermission('employees_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>Add Employee</span>',
                        className: "create-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "modal",
                            "data-bs-target": "#addEmployeeModal",
                        },
                    },
                    @endif
                ],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })
            $(document).on("click", ".user_delete_button", function () {

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
                            url: '{{ route('user.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                user_id: id,
                            },
                            success: function (response) {
                                console.log('tutut');

                                table.ajax.reload(null, false);
                                toastr.success(response.message);
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                                // handle the error case
                            }
                        });
                    }
                });
            });


            $(document).on('change', '.changeStatus', function () {
                const id = $(this).data('id');
                const formData = new FormData();
                formData.append('user_id', id);
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
                            url: '{{ route('user.changeStatus') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.text);
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

            $(document).on("click", ".reply_button", function () {
                $('.error').text('')
                const id = $(this).attr("data-id");
                $.ajax({
                    url: '/user/getUser/' + id,
                    type: 'GET',
                    success: function (response) {
                        console.log(response)
                        $('#to_email').val(response.email);
                        $('#user_name').val(response.name);
                        $("#send_id").val(id);
                        $("#message").children().first().html(`<p>Below are your login credentials:</p><p><strong>Email: ${response.email}</strong></p><p><strong>Password: < user password></strong></p>`);
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });
            $(document).ready(function () {
                $('#sendReply').click(function (e) {
                    e.preventDefault();

                    var formData = new FormData();

                    let subject = $("input[name=subject]").val();
                    let to_email = $("input[name=to_email]").val();
                    let user_name = $("input[name=user_name]").val();
                    let message = $("#message").children().first().html();
                    let id = $("#send_id").val();

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
                    formData.append('id', id);
                    formData.append('_token', "{{ csrf_token() }}");


                    loader.show();
                    submitButton.prop('disabled', true);

                    $('.error').text('')
                    $.ajax({
                        url: '{{ route('user.sendCredential') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 403) {
                                $('.subjectError').text(response.errors?.subject ? response.errors
                                    .subject[0] : '');
                                $('.messageError').text(response.errors?.message ? response
                                    .errors.message[0] : '');
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


        });
    </script>
@endpush
