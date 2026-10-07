@php use App\Models\Role; @endphp
@extends('layouts.master')

@section('title', $title ?? $roleName)

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb( $roleName .' '._trans('keyword.List'),['#'=>_trans('keyword.User').' '._trans('keyword.Management'),'user'=> $roleName .' '._trans('keyword.List')]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                {{--  <div class="d-flex gap-3" >
                      <div class="form-group">
                          <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                          <select id='status' class="form-control filter_dropdown" style="width: 200px">
                              <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                              <option value="1">{{_trans('keyword.Active')}}</option>
                              <option value="0">{{_trans('keyword.Deactive')}}</option>
                          </select>
                      </div>

                      @if($roleId == Role::CUSTOMER)
                          <div class="form-group">
                              <label><strong>{{_trans('keyword.Designer')}} :</strong></label>
                              <select id='designer_id' class="form-control filter_dropdown" style="width: 200px">
                                  <option value="">{{_trans('keyword.Select').' '._trans('keyword.Designer')}}</option>
                                  @foreach($designers as $designer)
                                      <option value="{{$designer->id}}">{{$designer->name}}</option>
                                  @endforeach
                              </select>
                          </div>
                      @endif
                  </div>--}}
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            @if($roleId == Role::DESIGNER)
                                <th>{{_trans('keyword.Designer Info')}}</th>
                                <th>{{_trans('keyword.Shop Info')}}</th>
                            @else
                                <th>{{_trans('keyword.Customer Info')}}</th>
                            @endif
                            <th>{{_trans('keyword.Image')}}</th>
                            <th>{{_trans('keyword.Role')}}</th>
                            @if($roleId == Role::CUSTOMER)
                                <th>{{_trans('keyword.Designer Info') }}</th>
                            @endif
                            <th>{{_trans('keyword.Status')}}</th>
                            @if($roleId == Role::DESIGNER || $roleId == Role::MANUFACTURER)
                                <th>{{_trans('keyword.Subscription')}}</th>
                            @endif
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Offcanvas to add new customer -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCustomerList"
                 aria-labelledby="offcanvasEcommerceCustomerListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCustomerListLabel"
                        class="offcanvas-title">{{_trans('keyword.Add')}} {{_trans('keyword.Customer')}}</h5>
                    <button type="button" id="closeAddModal" class="btn-close bg-label-secondary text-reset"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="addModal" method="POST">
                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label" for="customer-name">{{_trans('keyword.Name')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer-name"
                                   placeholder="Enter Name" name="name" aria-label="customer name"/>
                            <span class="text-danger nameError error"></span>
                        </div>
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label" for="customer-email">{{_trans('keyword.Email')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer-email"
                                   placeholder="Enter Email" name="email" aria-label="customer email"/>
                            <span class="text-danger emailError error"></span>
                        </div>
                        <!-- Phone -->
                        <div class="mb-3">
                            <label class="form-label" for="customer-phone">{{_trans('keyword.Phone')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer-phone"
                                   placeholder="Enter Phone" name="phone" aria-label="customer phone"/>
                            <span class="text-danger phoneError error"></span>
                        </div>
                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label" for="password">{{_trans('keyword.Password')}} <span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password"
                                   placeholder="Enter Password" name="password" aria-label="password"/>
                            <span class="text-danger passwordError error"></span>
                        </div>

                        <!--Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label" for="ConfirmPassword">{{_trans('keyword.Confirm Password')}} <span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="ConfirmPassword"
                                   placeholder="Enter Confirm Password" name="confirm_password"
                                   aria-label="Confirm password"/>
                            <span class="text-danger confirmPasswordError error"></span>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label class="form-label"
                                   for="customer-image">{{_trans('keyword.Image')}}
                            </label>
                            <input class="form-control" type="file" name="image" id="customer-image"/>
                            <span class="text-danger imageError error"></span>
                        </div>

                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select')}} {{_trans('keyword.Status')}}</label>
                            <select id="acive-status" name="status" class="select2 form-select"
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
                                       value="Your {{shopSetting()->shop_name}} account login credentials"
                                       placeholder="subject"
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
                    url: '{{ route('user.index',$roleId) }}',
                    data: function (d) {
                        d.status = $('#status').val()
                        d.designer_id = $('#designer_id').val()
                        d.subscription_status = $('#subscription_status').val()
                        d.user_source = $('#user_source').val();
                        d.dateRange = $('#flatpickr-range').val();
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'name',
                        name: 'name',
                        searchable: false
                    },
                        @if($roleId == Role::DESIGNER)
                    {
                        data: 'shop_info',
                        name: 'shop_info',
                        searchable: false
                    },
                        @endif
                    {
                        data: 'avatar',
                        name: 'avatar',
                        searchable: false
                    },
                    {
                        data: 'role_id',
                        name: 'role_id',
                        searchable: false
                    },
                        @if($roleId == Role::CUSTOMER)
                    {
                        data: 'designer',
                        name: 'designer',
                        searchable: false
                    },
                        @endif
                    {
                        data: 'status',
                        name: 'status',
                        searchable: false
                    },
                        @if($roleId == Role::DESIGNER || $roleId == Role::MANUFACTURER)
                    {
                        data: 'is_subscribed',
                        name: 'is_subscribed',
                        searchable: false
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
                    <div class="d-flex gap-3" >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Active Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 customer-list-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Status')}}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>
                    @if($roleId == Role::CUSTOMER && Auth::user()->role_id == Role::SUPER_ADMIN)
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Designer')}} :</strong></label>
                            <select id='designer_id' class="form-control filter_dropdown select2 customer-list-2" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Designer')}}">
                                <option value="">{{_trans('keyword.Select').' '._trans('keyword.Designer')}}</option>
                                @foreach($designers as $designer)
                    <option value="{{$designer->id}}">{{$designer->name}}</option>
                                @endforeach
                    </select>
                </div>
@endif
                    @if(in_array($roleId,[Role::DESIGNER,Role::MANUFACTURER]))
                    <div class="form-group">
                   <label><strong>{{_trans('keyword.Subscription Status')}} :</strong></label>
                        <select id='subscription_status' class="form-control filter_dropdown select2 customer-list-1" style="width: 200px" data-placeholder="{{_trans('keyword.Subscription Status')}}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Subscription Status')}}</option>
                            <option value="1">{{_trans('keyword.Subscribed')}}</option>
                            <option value="0">{{_trans('keyword.Not Subscribed')}}</option>
                            <option value="3">{{_trans('keyword.Subscription Not Required')}}</option>
                            <option value="4">{{_trans('keyword.Free Trial')}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.User Source')}} :</strong></label>
                        <select id='user_source' class="form-control filter_dropdown select2 customer-list-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Source')}}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Source')}}</option>
                            <option value="HB">{{_trans('keyword.HB')}}</option>
                            <option value="WWP">{{_trans('keyword.WWP')}}</option>
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                            <label for="flatpickr-range" class="form-label">Date Range </label>
                            <input type="text" class="form-control"
                            placeholder="YYYY-MM-DD to YYYY-MM-DD"
                            name="date"
                            value=""
                            id="flatpickr-range"/>
                    </div>

                </div>
                    </div>
                `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.customer-list-1').select2({
                        allowClear: true,
                    });
                    $('.customer-list-2').select2({
                        allowClear: true,
                    });
                    flatpickrRange = document.querySelector('#flatpickr-range');
                    // Range
                    if (typeof flatpickrRange != undefined) {
                        flatpickrRange.flatpickr({
                            mode: 'range',
                            allowClear: true,
                            onChange: function () {
                                table.draw();
                            },
                        });
                    }
                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search User",
                },

                buttons: [
                        @if(hasPermission('customers_read') && Role::CUSTOMER == $roleId)
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>{{_trans('keyword.Customer')}}</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "offcanvas",
                            "data-bs-target": "#offcanvasEcommerceCustomerList",
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
                let email = $("input[name=email]").val();
                let phone = $("input[name=phone]").val();
                let password = $("input[name=password]").val();
                let confirm_password = $("input[name=confirm_password]").val();
                let status = $("#acive-status option:selected").val();
                var image = $('#customer-image').prop('files')[0] ?? '';

                formData.append('name', name);
                formData.append('email', email);
                formData.append('phone', phone);
                formData.append('password', password);
                formData.append('password_confirmation', confirm_password);
                formData.append('status', status);
                formData.append('image', image);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('customer.store') }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        console.log(response);
                        if (response.status == 403) {
                            $('.nameError').text(response.errors?.name ? response.errors
                                ?.name[0] : '');
                            $('.emailError').text(response.errors?.email ? response.errors
                                ?.email[0] : '');
                            $('.phoneError').text(response.errors?.phone ? response.errors
                                ?.phone[0] : '');
                            $('.passwordError').text(response.errors?.password ? response.errors
                                ?.password[0] : '');
                            $('.confirmPasswordError').text(response.errors?.confirm_password ? response.errors
                                ?.confirm_password[0] : '');
                            $('.imageError').text(response.errors?.image ? response.errors
                                ?.image[0] : '');
                            $('.statusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false)

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


            $(document).ready(function () {
                $('#reset').click(function (e) {
                    e.preventDefault();
                    $("input[name=subject]").val('');
                    $("#message").children().first().html('');
                });
            })
        });
    </script>
@endpush
