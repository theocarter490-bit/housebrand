@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Task Stage'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb( _trans('keyword.Task Stage'),['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),'essentials'=> _trans('keyword.Essentials'), 'status'=>   _trans('keyword.Task Stage')]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Name')}}</th>
                            <th>{{_trans('keyword.Color')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th>{{_trans('keyword.Project')}}</th>
                            <th>{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- add sidebar -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCategoryList"
                 aria-labelledby="offcanvasEcommerceCategoryListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Add')}} {{_trans('keyword.Stage')}}</h5>
                    <button type="button" id="closeAddModal" class="btn-close bg-label-secondary text-reset"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="addModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="ecommerce-category-title">{{_trans('keyword.Name')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ecommerce-category-title"
                                   placeholder="Enter name" name="name" aria-label="category title"/>
                            <span class="text-danger nameError error"></span>
                        </div>


                        {{--                        project--}}
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label class="form-label">{{_trans('keyword.Select')}} {{_trans('keyword.Project')}}<span
                                    class="text-danger">*</span></label>
                            <select id="project_id" name="project_id" class="select2 form-select"
                                    data-placeholder="Select a project">
                                <option value="">Select a Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger projectError error"></span>
                        </div>


                        <!-- Color -->
                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image">{{_trans('keyword.Status')}} {{_trans('keyword.Color')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="color" name="color" id="color"/>
                            <span class="text-danger colorError error"></span>
                        </div>


                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label class="form-label">{{_trans('keyword.Select')}} {{_trans('keyword.Status')}}</label>
                            <select id="category-status" name="status" class="select2 form-select"
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

            <!-- edit sidebar -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCategoryEditModal"
                 aria-labelledby="offcanvasEcommerceCategoryListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Edit')}} {{_trans('keyword.Stage')}}</h5>
                    <button type="button" id="closeEditModal" class="btn-close bg-label-secondary text-reset"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="updateCategoryModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="edit_name">{{_trans('keyword.Name')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" placeholder="Enter name"
                                   name="edit_name" aria-label="category title"/>
                            <input type="text" hidden value="" name="status_id" id="status_id">
                            <span class="text-danger editNameError error"></span>
                        </div>

                        {{-- project--}}
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label class="form-label">{{_trans('keyword.Select')}} {{_trans('keyword.Project')}}<span
                                    class="text-danger">*</span></label>
                            <select id="edit_project_id" name="edit_project_id" class="select2 form-select"
                                    data-placeholder="Select a project">
                                <option value="">Select a Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger editProjectError error"></span>
                        </div>

                        <!-- Color -->
                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image">{{_trans('keyword.Status')}} {{_trans('keyword.Color')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="color" name="edit_color" id="edit_color"/>
                            <span class="text-danger colorError error"></span>
                        </div>

                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label class="form-label">{{_trans('keyword.Select')}} {{_trans('keyword.Status')}}</label>

                            <select id="edit_status" name="edit_status" class="select2 form-select"
                                    data-placeholder="Select status">
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
                            <button type="reset" class="btn bg-label-danger"
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
        $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('project-management.essentials.task.status.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()
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
                            return '<div style="background-color:' + data + '; width: 20px; height: 20px; display: inline-block; margin-right: 5px;"></div>' + data;
                        }
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'project',
                        name: 'project',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [2, "desc"], //set any columns order asc/desc
                dom: '<"card-header d-flex flex-xl-row flex-column  pb-3 gap-3 align-items-xl-end align-items-start category-list-header"' +
                    '<f m-0><"custom-text-div">' +
                    '<"d-flex justify-content-center justify-content-md-end align-items-baseline right-side-buttons"<"dt-action-buttons d-flex xl-gap-0 gap-3 justify-content-center flex-md-row flex-column mb-3 mb-md-0 ps-1 ms-1 align-items-baseline"lB>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                initComplete: function () {
                    // Set the inner div to display your name
                    $('.custom-text-div').html(`
                  <div class="d-xl-flex gap-3   d-block" >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Stage')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown category-select2 select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Stage') }}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Stage')}}</option>
                            <option value="1">{{_trans('keyword.Active') }}</option>
                            <option value="0">{{_trans('keyword.Inactive') }}</option>
                        </select>
                    </div>
                </div>
`);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('category-list-inner');
                    });
                    $('.category-select2').select2({
                        allowClear: true,
                    });
                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Stage",
                },
                // Button for offcanvas
                buttons: [
                        @if(hasPermission('create_task_status'))

                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>{{_trans('keyword.Add')}} {{_trans('keyword.Stage')}}</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "offcanvas",
                            "data-bs-target": "#offcanvasEcommerceCategoryList",
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
                let status = $("#category-status option:selected").val();
                let projectId = $("#project_id option:selected").val();
                const color = $('#color').val();

                formData.append('name', name);
                formData.append('status', status);
                formData.append('project_id', projectId);
                formData.append('color', color);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('project-management.essentials.task.status.store') }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        if (response.status == 403) {
                            $('.nameError').text(response.errors?.name ? response.errors
                                ?.name[0] : '');

                            $('.statusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false)

                            $("input[name=name]").val('');
                            $('#color').val('');
                            $('#closeAddModal').click();
                            $('#project_id').val('').trigger('change.select2');

                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            // Display errors if they exist
                            if (errors.name) {
                                $('.nameError').text(errors.name[0]);
                            }
                            if (errors.status) {
                                $('.statusError').text(errors.status[0]);
                            }
                            if (errors.project_id) {
                                $('.projectError').text(errors.project_id[0]);
                            }
                        }
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });


            $(document).on("click", ".category_edit_button", function () {
                $id = $(this).attr("data-id");
                $('#edit_status').find('option:selected').attr("selected", false);
                $('#edit_status').trigger('change.select2');
                $.ajax({
                    url: '/project-management/essentials/task/status/edit/' + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#status_id').val(response.data.id);
                        $('#edit_name').val(response.data.name);
                        $('#edit_color').val(response.data.color);

                        // Preselect status dropdown
                        $('#edit_status').val(response.data.active_status).trigger('change.select2');

                        // Preselect project dropdown
                        $('#edit_project_id').val(response.data.project_id).trigger('change.select2');
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
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
                            url: '{{ route('project-management.essentials.task.status.changeStatus') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    table.ajax.reload(null, false);
                                    toastr.success(response.message);
                                } else {
                                    table.ajax.reload(null, false);
                                    toastr.error(response.message);
                                }
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                                table.ajax.reload(null, false);
                            }
                        });
                    }

                });
            });


            $('#updateCategoryModal').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let name = $("input[name=edit_name]").val();
                let status_id = $('#status_id').val();
                let status = $("#edit_status option:selected").val();

                let project_id = $("#edit_project_id option:selected").val();

                let color = $('#edit_color').val();

                formData.append('name', name);
                formData.append('color', color)
                formData.append('status', status);
                formData.append('status_id', status_id);
                formData.append('project_id', project_id);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('project-management.essentials.task.status.update') }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        if (response.status == 403) {
                            table.ajax.reload(null, false)
                            $('.editNameError').text(response.errors?.name ? response.errors
                                ?.name[0] : '');

                            $('.editImageError').text(response.errors?.image ? response.errors
                                ?.image[0] : '');
                            $('.editDescriptionError').text(response.errors?.description ?
                                response
                                    .errors
                                    ?.description[0] :
                                '');
                            $('.editStatusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false)
                            $('#closeEditModal').click();
                            $('#edit_image').val('');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            // Display errors if they exist
                            if (errors.name) {
                                $('.editNameError').text(errors.name[0]);
                            }
                            if (errors.status) {
                                $('.statusError').text(errors.status[0]);
                            }
                            if (errors.project_id) {
                                $('.editProjectError').text(errors.project_id[0]);
                            }
                        }
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });


            $(document).on("click", ".category_delete_button", function () {

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
                            url: '{{ route('project-management.essentials.task.status.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                status_id: id,
                            },
                            success: function (response) {
                                if (response.status === 200) {
                                    table.ajax.reload(null, false)
                                    toastr.success(response.text);
                                } else {
                                    toastr.error(response.message);
                                }
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
