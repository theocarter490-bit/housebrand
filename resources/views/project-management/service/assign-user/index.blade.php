@extends('layouts.master')

@section('title', $title ??_trans('keyword.Assign User'))

@section('content')

    <div class="row">

        <div class="col-6">

            <h4 class="py-3 mb-2">{{_trans('keyword.Assigned Employee')}}</h4>

            <div class="app-ecommerce-category">
                <!-- Category List Table -->
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="data-table table border-top">
                            <thead>
                            <tr>
                                <th>{{_trans('keyword.SL')}}</th>
                                <th>{{_trans('keyword.Image')}}</th>
                                <th>{{_trans('keyword.Info')}}</th>
                                <th>{{_trans('keyword.Fee'). ' (Hourly)'}}</th>
                                <th>{{_trans('keyword.Status')}}</th>
                                <th width="100px">{{_trans('keyword.Action')}}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-6">
            <h4 class="py-3 mb-2"> {{_trans('keyword.Assign Employee For').' Project'. $service->name .' '._trans('keyword.Service')}}</h4>

            <div class="card">
                <div class="card-body row">
                    <form method="POST" class="col-12 row" id="addUser">
                        @csrf

                        <!-- Left Side: User Selection -->
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="d-flex flex-row justify-content-between">
                                    <label class="form-label"
                                           for="user_select">{{ _trans('keyword.Select').' '._trans('keyword.Employee') }}
                                        <span class="text-danger">*</span></label>
                                </div>

                                <input type="hidden" name="service_id" id="service_id" value="{{ $service->id }}"/>
                                <input type="hidden" name="service_name" id="service_name" value="{{$service->title}}">

                                <select class="form-control select2" name="user_id" id="user_select"
                                        data-placeholder="Select Employee">
                                    <option value="">{{ _trans('keyword.Select Employee') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>

                                <span class="text-danger userError error"></span>
                            </div>
                        </div>

                        <!-- Right Side: Service Fee -->
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="d-flex flex-row justify-content-between">
                                    <label class="form-label"
                                           for="service_fee">{{ _trans('keyword.Service').' '._trans('keyword.Fee') }}
                                        <span class="text-danger">*</span></label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">{{getCurrency()}}</span>
                                    <input type="number" class="form-control" name="service_fee"
                                           id="service_fee" placeholder="Amount">
                                </div>
                                <span class="text-danger serviceFeeError error"></span>
                            </div>
                        </div>

                        <!-- Full-width: Submit Button -->
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-primary" id="assignUserBtn">
                                {{ _trans('keyword.Save') }}
                            </button>
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
                ajax: '{{ route('project-management.service.assign-user.index', ['id' => $service->id]) }}',
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'info',
                        name: 'user.name',
                        searchable: true,
                    },
                    {
                        data: 'fee',
                        name: 'fee'
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
                dom: '<"card-header d-flex flex-wrap pb-2"' +
                    "<f>" +
                    '<"d-flex justify-content-center justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline"lB>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Employee",
                },
                // Button for offcanvas
                buttons: [],
            });
        });

        $(document).ready(function () {
            $('#assignUserBtn').on('click', function (e) {
                e.preventDefault();
                $('.error').text('');

                let form = $('#addUser')[0];
                let formData = new FormData(form);
                let submitButton = $(this);

                // Disable button and show loader
                submitButton.attr("disabled", true);
                $.ajax({
                    url: "{{ route('project-management.service.assign-user.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log(response)
                        if (response.status == 200) {
                            toastr.success(response.message);
                            $('.data-table').DataTable().ajax.reload(null, false);

                            $('#user_select').val('').trigger('change');
                            $('#service_fee').val('');

                            $('.serviceFeeError').text('');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.user_id) {
                                $('.userError').text(errors.user_id[0]);
                            }
                            if (errors.service_fee) {
                                $('.serviceFeeError').text(errors.service_fee[0]);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "{{ _trans('keyword.Error') }}",
                                text: "Something went wrong!"
                            });
                        }
                    },
                    complete: function () {
                        submitButton.attr("disabled", false);
                        loader.hide();
                    }
                });
            });
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
                        url: '{{route('project-management.service.assign-user.delete')}}',
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: id,
                        },
                        success: function (response) {
                            console.log(response)
                            if (response.status === 200) {
                                $('.data-table').DataTable().ajax.reload(null, false);
                                toastr.success(response.message);
                            } else {
                                Swal.fire({
                                    icon: response.icon,
                                    title: 'error!',
                                    text: response.message,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
                            }

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                }
            });
        });

        $(document).on("click", ".user_edit_button", function () {
            let id = $(this).attr("data-id");

            $.ajax({
                url: '{{ route("project-management.service.assign-user.editUser", ":id") }}'.replace(':id', id),
                method: 'GET',
                success: function (response) {
                    // Assuming response contains user details
                    $('#user_select').val(response.user_id).trigger('change'); // Set selected user
                    $('#service_fee').val(response.fee); // Set the service fee
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
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
                        url: '{{route('project-management.service.assign-user.changeStatus')}}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 200) {
                                toastr.success(response.message);
                                $('.data-table').DataTable().ajax.reload(null, false);
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
                            toastr.error(error.responseJSON.message);
                        }
                    });
                } else {
                    $('.data-table').DataTable().ajax.reload(null, false);
                }
            });
        });


    </script>
@endpush
