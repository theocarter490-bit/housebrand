@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Event') .' '._trans('keyword.Type'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Event') .' '._trans('keyword.Type'),['#'=>_trans('keyword.Event').' '._trans('keyword.Management'),'category'=>_trans('keyword.Event').' '._trans('keyword.Type')]) !!}
        <div class="app-ecommerce-category">
            <!-- Expense Type List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Name')}}</th>
                            <th>{{_trans('keyword.Color')}}</th>
                            @if(hasPermission('event_type_change_status'))
                                <th>{{_trans('keyword.Status')}}</th>
                            @endif
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- Offcanvas to add new type -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSectionCategoryList"
                 aria-labelledby="offcanvasEcommerceListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Add').' '._trans('keyword.Event').' '._trans('keyword.Type')}}</h5>
                    <button type="button" class="btn-close bg-label-secondary text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="addModal" method="POST" action="{{route('event-management.type.store')}}">
                        @csrf
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="ecommerce-category-title">{{_trans('keyword.Name')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ecommerce-category-title"
                                   placeholder="Enter Type Name" name="name" aria-label="Brand Name" required/>
                            <span class="text-danger nameError error"></span>
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{--color--}}
                        <div class="mb-3">
                            <label class="form-label"
                                   for="ecommerce-category-title">{{_trans('keyword.Color')}} </label>
                            <input type="color" class="form-control" id="ecommerce-category-title" name="color"
                                   aria-label="Brand Name"/>
                            <span class="text-danger colorError error"></span>
                            @error('color')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Type').' '._trans('keyword.Status')}}</label>
                            <select name="active_status" class="select2 form-select"
                                    data-placeholder="Select Type status">
                                <option value="1" selected>{{_trans('keyword.Active')}}</option>
                                <option value="0">{{_trans('keyword.Inactive')}}</option>
                            </select>
                            @error('status')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror                        </div>
                        <!-- Submit and reset -->
                        <div class="mb-3">
                            <button type="submit"
                                    class="btn btn-primary me-sm-3 me-1 data-submit">{{_trans('keyword.Add')}}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" id="closeAddModal" class="btn bg-label-danger"
                                    data-bs-dismiss="offcanvas">{{_trans('keyword.Discard')}}</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Offcanvas to edit type -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCategoryEditModal"
                 aria-labelledby="offcanvasEcommerceCategoryListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Edit').' '._trans('keyword.Event').' '._trans('keyword.Type')}}</h5>
                    <button type="button" class="btn-close bg-label-secondary text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="updateTypeModal" method="POST"
                          action="{{route('event-management.type.update')}}">
                        @csrf
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="edit_name">{{_trans('keyword.Name')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name"  required
                                   placeholder="Enter Type Name" name="name" aria-label="brand name"/>
                            <input type="text" hidden value="" name="event_type_id" id="event_type_id">
                            <span class="text-danger editNameError error"></span>
                        </div>

                        {{--color--}}
                        <div class="mb-3">
                            <label class="form-label" for="ecommerce-category-title">{{_trans('keyword.Color')}}</label>
                            <input type="color" class="form-control" id="edit_color" name="color"/>
                            <span class="text-danger colorError error"></span>
                        </div>

                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Type').' '._trans('keyword.Status')}}</label>
                            <select id="edit_status" name="active_status" class="select2 form-select"
                                    data-placeholder="Select Type status">
                                <option value="1">{{_trans('keyword.Active')}}</option>
                                <option value="0">{{_trans('keyword.Inactive')}}</option>
                            </select>
                            <span class="text-danger editStatusError error"></span>
                        </div>
                        <!-- Submit and reset -->
                        <div class="mb-3">
                            <button type="submit"
                                    class="btn btn-primary me-sm-3 me-1 data-submit">{{_trans('keyword.Update')}}
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

        @foreach($errors->all() as $errorMsg)
            toastr.error('{{$errorMsg}}');
        @endforeach

        $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('event-management.type.index') }}',
                    data: function (d) {
                        d.active_status = $('#status').val()
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
                        render: function (data) {
                            return `<div style="white-space: normal; word-wrap: break-word; max-width: 200px;">${data}</div>`;

                        }
                    },

                    {
                        data: 'color',
                        name: 'color',
                        render: function (data, type, full, meta) {
                            return '<div class="d-flex align-middle"><div style="background-color:' + data + '; width: 20px; height: 20px; display: inline-block; margin-right: 5px;"></div>' + data + '</div>';
                        }
                    },
                        @if(hasPermission('event_type_change_status'))
                    {
                        data: 'status',
                        name: 'status'
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
                    <div class="d-xl-flex gap-3   d-block  " >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 event-type-1" style="width: 200px" data-placeholder='{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}'>
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active') }}</option>
                            <option value="0">{{_trans('keyword.Inactive') }}</option>
                        // </select>
                    </div>
                  </div>
                  `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.event-type-1').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Type",
                },
                // Button for offcanvas
                buttons: [

                        @if(hasPermission('event_type_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>{{_trans('keyword.Add').' '._trans('keyword.Event').' '._trans('keyword.Type')}}</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "offcanvas",
                            "data-bs-target": "#offcanvasSectionCategoryList",
                        },
                    },
                    @endif
                ],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })

            $(document).on("click", ".type_edit_button", function () {

                $id = $(this).attr("data-id");
                $('#edit_status').find('option:selected').attr("selected", false);
                $('#edit_status').trigger('change');
                $.ajax({
                    url: '/event-management/types/edit/' + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#event_type_id').val(response.id);
                        $('#edit_name').val(response.name);
                        $('#edit_color').val(response.color);

                        $("#edit_status").find('option').removeAttr("selected");
                        $('#edit_status').trigger('change.select2');
                        $('#edit_status').find('option[value="' + response.active_status +
                            '"]').attr("selected", "selected");

                        $('#edit_status').trigger('change');
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });


            $(document).on("click", ".type_delete_button", function () {

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
                            url: '{{ route('event-management.type.delete') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                if (response.status == 200) {
                                    table.ajax.reload(null, false)
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.text,
                                        customClass: {
                                            confirmButton: 'btn btn-success waves-effect waves-light'
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: "Couldn't Delete this Type!",
                                        text: response.message,
                                        customClass: {
                                            confirmButton: 'btn btn-success waves-effect waves-light'
                                        }
                                    });
                                }

                            },
                            error: function (error) {
                                console.log(error.responseJSON.message);
                                // handle the error case
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
                    text: "To change the status of this type.",
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
                            url: '{{ route('event-management.type.change-status') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    table.ajax.reload(null, false);
                                }
                            },
                            error: function (error) {
                                console.error(error);
                            }
                        });
                    } else {
                        table.ajax.reload(null, false);
                    }

                })
            });

        });
    </script>
@endpush
