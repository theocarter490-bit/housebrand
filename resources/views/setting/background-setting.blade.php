@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Background').' '._trans('keyword.Settings'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Background').' '._trans('keyword.Settings'),['#'=>_trans('keyword.System').' '._trans('keyword.Settings'),'/setting/background-settings'=>_trans('keyword.Background').' '._trans('keyword.Settings')]) !!}
        <div class="app-ecommerce-category">
            <!-- Background Settings List Table -->
            <div class="card">
               {{-- <div class="d-flex gap-3 position-absolute ps-4 p-2 " style="z-index: 100; margin-top: 10px; margin-left: 240px">
                    <div class="form-group">
                        <label><strong>Status :</strong></label>
                        <select id='status' class="form-control filter_dropdown" style="width: 200px">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>Type :</strong></label>
                        <select id='type' class="form-control filter_dropdown" style="width: 200px">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                            <option value="image">{{_trans('keyword.Image')}}</option>
                            <option value="color">{{_trans('keyword.Color')}}</option>
                        </select>
                    </div>
                </div>--}}
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Title')}}</th>
                            <th>{{_trans('keyword.Short Description')}}</th>
                            <th>{{_trans('keyword.Purpose')}}</th>
                            <th>{{_trans('keyword.Type')}}</th>
                            <th>{{_trans('keyword.Image')}}</th>
                            <th>{{_trans('keyword.Color')}}</th>
                                <th>{{_trans('keyword.Status')}}</th>
                            <th width="50px">{{_trans('keyword.Info')}}</th>
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
                <div class="modal-body p-sm-4 p-0">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{_trans('keyword.Add').' '._trans('keyword.Background').' '._trans('keyword.Setting')}}</h3>
                    </div>
                    <form class="row g-3" action="{{route('setting.background-settings.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-12 mb-2">
                            <label class="form-label">{{_trans('keyword.Title')}} <span class="text-danger">*</span></label>
                            <input type="text" name="title" required class="form-control"
                                   placeholder="Title" tabindex="-1" />
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Short Description -->
                        <div>
                            <label class="form-label">{{_trans('keyword.Short Description')}}</label>
                            <textarea name="description" class="form-control" cols="20" rows="3" placeholder="Short Description"></textarea>
                        </div>

                        <div>
                            <label for="status" class="form-label">{{_trans('keyword.Purpose')}} <span class="text-danger">*</span></label>
                            <select id="purpose" name="purpose"  required  class="select2 form-select"
                                    data-placeholder="Select Purpose" >
                                <option value="">{{_trans('keyword.Select').' '._trans('keyword.Purpose')}}</option>
                                <option value="0">{{_trans('keyword.Login').' '._trans('keyword.Page')}}</option>
                                <option value="1">{{_trans('keyword.Signup Page')}}</option>
                                <option value="2">{{_trans('keyword.Admin').' '._trans('keyword.Login Page')}}</option>
                                <option value="3">{{_trans('keyword.Forget').' '._trans('keyword.Password') }}</option>
                                <option value="4">{{_trans('keyword.Reset').' '._trans('keyword.Password') }}</option>
                            </select>
                            @error('purpose')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="form-label">{{_trans('keyword.Type')}} <span class="text-danger">*</span></label>
                            <select id="section_type" required  name="type" class="select2 form-select"
                                    data-placeholder="Select Type">
                                <option value="">{{_trans('keyword.Select').' '._trans('keyword.Type') }}</option>
                                <option value="image">{{_trans('keyword.Image')}}</option>
                                <option value="color">{{_trans('keyword.Color')}}</option>
                            </select>
                            @error('type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div id="dynamic-field" class="col-12 mb-2"></div>

                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">{{_trans('keyword.Submit')}}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">
                                {{_trans('keyword.Cancel')}}
                            </button>
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
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{_trans('keyword.Edit').' '._trans('keyword.Background').' '._trans('keyword.Setting')}}</h3>
                    </div>
                    <form class="row g-3" action="{{route('setting.background-settings.update')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-12 mb-2">
                            <label class="form-label">{{_trans('keyword.Title')}} <span class="text-danger">*</span></label>
                            <input id="editTitle" type="text" required  name="title" class="form-control"
                                   placeholder="Title" tabindex="-1"/>
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <input type="text" name="id" id="id" hidden>
                        </div>

                        <!-- Short Description -->
                        <div>
                            <label class="form-label">{{_trans('keyword.Short Description')}}</label>
                            <textarea id="editDescription" name="description" class="form-control" cols="20" rows="3" placeholder="Short Description"></textarea>
                        </div>

                        <div>
                            <label for="status" class="form-label">{{_trans('keyword.Purpose')}} <span class="text-danger">*</span></label>
                            <select id="editPurpose" required  name="purpose" class="select2 form-select"
                                    data-placeholder="Select Purpose" required>
                                <option value="">{{_trans('keyword.Select').' '._trans('keyword.Purpose')}}</option>
                                <option value="0">{{_trans('keyword.Login Page')}}</option>
                                <option value="1">{{_trans('keyword.Signup Page')}}</option>
                                <option value="2">{{_trans('keyword.Admin').' '._trans('keyword.Login Page')}}</option>
                                <option value="3">{{_trans('keyword.Forget').' '._trans('keyword.Password')}}</option>
                                <option value="4">{{_trans('keyword.Reset').' '._trans('keyword.Password')}}</option>
                            </select>
                            @error('purpose')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="form-label">{{_trans('keyword.Type')}} <span class="text-danger">*</span></label>
                            <select id="editType"  name="type" class="select2 form-select"
                                    data-placeholder="Select Type" required>
                                <option value="">{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                                <option value="image">{{_trans('keyword.Image')}}</option>
                                <option value="color">{{_trans('keyword.Color')}}</option>
                            </select>
                            @error('type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div id="edit_dynamic-field"></div>

                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">{{_trans('keyword.Submit')}}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">
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
                ajax: {
                    url : '{{ route('setting.background-settings.index') }}',
                    data: function (d){
                        d.status = $('#status').val()
                        d.type = $('#type').val()
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'short_desc',
                        name: 'short_desc'
                    },
                    {
                        data: 'purpose',
                        name: 'purpose'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'color',
                        name: 'color',
                        render: function(data, type, full, meta) {
                            return '<div style="background-color:' + data + '; width: 20px; height: 20px; display: inline-block; margin-right: 5px;"></div>' + data;
                        }
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
                        <label><strong>Status :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 bg-setting-1" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>Type :</strong></label>
                        <select id='type' class="form-control filter_dropdown select2 bg-setting-2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Type') }}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                            <option value="image">{{_trans('keyword.Image')}}</option>
                            <option value="color">{{_trans('keyword.Color')}}</option>
                        </select>
                    </div>
                  </div>
                   `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $('.bg-setting-1').select2({
                        allowClear: true,
                    });  $('.bg-setting-2').select2({
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
                    searchPlaceholder: "Search Settings",
                },
                buttons: [
                    @if(hasPermission('background_settings_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span >Add Setting</span>',
                        className: "create-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "modal",
                            "data-bs-target": "#addSettingModal",
                        },
                    },
                    @endif
                ],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })


            $(document).on('change', '.changeStatus', function (){
                const id = $(this).data('id');
                const formData = new FormData();
                formData.append('id', id);
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
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('setting.background-settings.changeStatus') }}',
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
                            }
                        });
                    }else{
                        table.ajax.reload(null, false);
                    }

                });
            })

            $(document).on("click", ".setting_delete_button", function() {

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
                            url: '{{ route('setting.background-settings.delete') }}',
                            method: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function(response) {
                                table.ajax.reload(null, false)
                                Swal.fire({
                                    icon: response.icon,
                                    title: 'Deleted!',
                                    text: response.message,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
                            },
                            error: function(error) {
                                console.log(error.responseJSON.message);
                            }
                        });
                    }
                });
            });


            $('#section_type').on('change', function() {
                let selectedType = $(this).val();
                let inputFieldHtml = '';

                if (selectedType === 'image') {
                    inputFieldHtml = `
                <label class="form-label">Upload Image</label>
                <input type="file" name="image" class="form-control" required/>
                `;
                } else if (selectedType === 'color') {
                    inputFieldHtml = `
                <label class="form-label">Select Color</label>
                <input type="color" name="color" class="form-control" required/>
                `;
                }

                $('#dynamic-field').html(inputFieldHtml);
            });

            $(document).on("click", ".edit_button", function() {
                let id = $(this).attr("data-id");
                $('.error').text('')
                id = $(this).attr("data-id");

                $.ajax({
                    url: '/setting/background-settings/edit/' + id,
                    type: 'GET',
                    success: function (response) {
                        $('#id').val(id);
                        $("#editTitle").val(response.data.title);
                        $("#editDescription").val(response.data.short_desc);

                        const setSelectedOption = (selectId, value) => {
                            $(selectId).val(value).trigger('change.select2'); // Use .val() instead of setting "selected"
                        };

                        setSelectedOption('#editPurpose', response.data.purpose);
                        setSelectedOption('#editType', response.data.type);  // Update this to use .val()

                        let selectedType = response.data.type;
                        let inputFieldHtml = '';

                        if (selectedType === 'image') {
                            inputFieldHtml = `
                <div class="row">
                    <div class="col-8">
                        <label class="form-label">Upload Image</label>
                        <input type="file" name="image" class="form-control"/>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Current Image</label>
                        <div class="col-4">
                            <img style="height: 50px; width: 50px" src="${response.data.image}" alt=""/>
                        </div>
                    </div>
                </div>
                `;
                        } else if (selectedType === 'color') {
                            inputFieldHtml = `
                <div class="col-12">
                    <label class="form-label">Select Color</label>
                    <input type="color" name="color" class="form-control" value="${response.data.color}" required/>
                </div>
                `;
                        }

                        $('#edit_dynamic-field').html(inputFieldHtml);

                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $('#editType').on('change', function() {
                let selectedType = $(this).val();
                let inputFieldHtml = '';

                if (selectedType === 'image') {
                    inputFieldHtml = `
                <label class="form-label">Upload Image</label>
                <input type="file" name="image" class="form-control" required/>
                `;
                } else if (selectedType === 'color') {
                    inputFieldHtml = `
                <label class="form-label">Select Color</label>
                <input type="color" name="color" class="form-control" required/>
                `;
                }

                $('#edit_dynamic-field').html(inputFieldHtml);
            });


        });
    </script>
@endpush
