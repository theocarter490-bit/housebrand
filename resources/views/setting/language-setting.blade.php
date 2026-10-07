@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Language Setting'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Language Setting'),['#'=>_trans('keyword.Language Setting')]) !!}
        <div class="app-ecommerce-category">
            <!-- Language Setting List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Name')}} </th>
                            <th>{{_trans('keyword.Code')}}</th>
                            <th>{{_trans('keyword.Native')}}</th>
                            <th>RTL</th>
                            <th>{{_trans('keyword.Default')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Setting Modal -->
    <div id="addSettingModal" class="modal fade" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{_trans('keyword.Add').' '._trans('keyword.New').' '._trans('keyword.Language')}}</h3>
                    </div>
                    <form class="row g-3" action="{{route('setting.language.store')}}" method="POST">
                        @csrf

                        <!-- Left Column (6 columns) -->
                        <div class="col-12 col-lg-6">

                            <div class="mb-3">
                                <label class="form-label">{{ _trans('keyword.Name')}} <span class="text-danger">*</span></label>
                                <input required type="text" class="form-control" name="name" id="name"
                                       aria-label="Language Name" placeholder="Language Name"/>
                                <span class="text-danger nameError error"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{_trans('keyword.Code')}} <span class="text-danger">*</span></label>
                                <input required type="text" class="form-control" name="code" id="code"
                                       aria-label="Language Code" placeholder="Language Code"/>
                                <span class="text-danger codeError error"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{_trans('keyword.Native')}} <span
                                        class="text-danger">*</span></label>
                                <input required type="text" class="form-control" name="native" id="native"
                                       aria-label="Language Native" placeholder="Language Native"/>
                                <span class="text-danger titleError error"></span>
                            </div>
                        </div>

                        <!-- Right Column (6 columns) -->
                        <div class="col-12 col-lg-6">
                            <div class="mb-3 col ecommerce-select2-dropdown">
                                <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                       for="rtl_support">
                                    <span>RTL {{_trans('keyword.Support')}}</span>
                                </label>
                                <select id="rtl" name="rtl_support" class="select2 form-select" style="width: 100%"
                                        data-placeholder="RTL Supported?">
                                    <option value="0">N/A</option>
                                    <option value="1">{{_trans('keyword.Supported')}}</option>
                                </select>
                            </div>

                            <div class="mb-3 col ecommerce-select2-dropdown">
                                <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                       for="is_default">
                                    <span>{{_trans('keyword.Default')}}</span>
                                </label>
                                <select id="is_default" name="is_default" class="select2 form-select"
                                        style="width: 100%"
                                        data-placeholder="Default?">
                                    <option value="0">N/A</option>
                                    <option value="1">{{_trans('keyword.Default')}}</option>
                                </select>
                            </div>

                            <div class="mb-4 ecommerce-select2-dropdown">
                                <label
                                    class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</label>
                                <select id="status" name="status" class="select2 form-select" style="width: 100%"
                                        data-placeholder="Select status">
                                    <option value="1">{{_trans('keyword.Active')}}</option>
                                    <option value="0">{{_trans('keyword.Deactive')}}</option>
                                </select>
                                <span class="text-danger statusError error"></span>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="row justify-content-center">
                                <div class="col-sm-3">
                                    <button type="submit" id="submit"
                                            class="btn btn-primary w-100 px-sm-4 px-5">{{_trans('keyword.Save')}}
                                        <span class="loader"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Setting Modal -->



    <!-- Edit Setting Modal -->
    <div id="editSettingModal" class="modal fade" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content  p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body p-sm-4 p-0">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{_trans('keyword.Edit').' '._trans('keyword.Language').' '. _trans('keyword.Settings')}}</h3>
                    </div>
                    <form class="row g-3" action="{{route('setting.language.update')}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <!-- Left Column (6 columns) -->
                        <div class="col-12 col-lg-6">
                            <div>
                                <input type="text" name="id" id="id" hidden>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{_trans('keyword.Name')}}</label>
                                <input required type="text" class="form-control"
                                       name="name" id="edit_name" aria-label="Language Name"
                                       placeholder="Language Name"/>
                                <span class="text-danger nameError error"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{_trans('keyword.Native')}}</label>
                                <input required type="text" class="form-control" name="native" id="edit_native"
                                       aria-label="Language Native" placeholder="Language Native"/>
                                <span class="text-danger titleError error"></span>
                            </div>

                        </div>

                        <!-- Right Column (6 columns) -->
                        <div class="col-12 col-lg-6">

                            <div class="mb-3">
                                <label class="form-label">{{_trans('keyword.Code')}}</label>
                                <input required type="text" class="form-control" name="code" id="edit_code"
                                       aria-label="Language Code" placeholder="Language Code"/>
                                <span class="text-danger codeError error"></span>
                            </div>


                            <div class="mb-3 col ecommerce-select2-dropdown">
                                <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                       for="rtl_support">
                                    <span>RTL {{_trans('keyword.Support')}}</span>
                                </label>
                                <select id="edit_rtl" name="rtl_support" class="select2 form-select"
                                        data-placeholder="RTL Supported?">
                                    <option value="0">N/A</option>
                                    <option value="1">{{_trans('keyword.Supported')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">{{_trans('keyword.Update')}}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                {{_trans('keyword.Cancel')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Setting Modal -->

@endsection

@push('scripts')
    <script>
        $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('setting.language.index') }}',
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
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'native',
                        name: 'native'
                    },

                    {
                        data: 'rtl',
                        name: 'rtl'
                    },
                    {
                        data: 'is_default',
                        name: 'is_default'
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
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search language",
                },
                // Button for offcanvas
                buttons: [{
                    text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>{{_trans('keyword.Add').' '._trans('keyword.Language')}}</span>',
                    className: "create-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                    attr: {
                        "data-bs-toggle": "modal",
                        "data-bs-target": "#addSettingModal",
                    }
                },],
            });

            //change any status form datatable
            $(document).on('change', '.changeStatus', function () {
                const id = $(this).data('id');
                const type = $(this).data('type');
                const formData = new FormData();
                formData.append('id', id);
                formData.append('type', type);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "To change the status of this setting.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Change it',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('setting.language.changeStatus') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    location.reload();
                                } else {
                                    toastr.error(response.message);
                                }
                                table.ajax.reload(null, false);
                            },
                            error: function (error) {
                                console.error(error);
                            }
                        });
                    } else {
                        table.ajax.reload(null, false);
                    }

                });
            })

            $(document).on("click", ".edit_button", function () {
                let id = $(this).attr("data-id");
                $('.error').text('')
                id = $(this).attr("data-id");
                $.ajax({
                    url: '/setting/language/edit/' + id,
                    type: 'GET',
                    success: function (response) {
                        $('#id').val(id);
                        $("#edit_name").val(response.data.name);
                        $("#edit_code").val(response.data.code);
                        $("#edit_native").val(response.data.native);

                        $('#edit_language_code').find('option[value="' + response.data.country_icon.id + '_' + response.data.country_icon.title + '"]').attr("selected", "selected");
                        $('#edit_rtl').find('option[value="' + response.data.rtl + '"]').attr("selected", "selected");
                        $('#edit_is_default').find('option[value="' + response.data.is_default + '"]').attr("selected", "selected");
                        $('#edit_status').find('option[value="' + response.data.active_status + '"]').attr("selected", "selected");


                        $('#edit_language_code').trigger('change.select2');
                        $('#edit_rtl').trigger('change.select2');
                        $('#edit_is_default').trigger('change.select2');
                        $('#edit_status').trigger('change.select2');

                        let selectedType = response.data.type;
                        let inputFieldHtml = '';
                        $('#edit_dynamic-field').html(inputFieldHtml);

                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });


            $(document).on("click", ".setting_delete_button", function () {
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
                            url: '{{ route('setting.language.delete') }}',
                            method: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false)
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                } else if (response.status === 409) {
                                    toastr.error(response.message);
                                }

                            },
                            error: function (error) {
                                console.log(error.responseJSON.message);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
