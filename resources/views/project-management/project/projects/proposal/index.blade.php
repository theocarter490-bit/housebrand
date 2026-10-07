@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Proposal'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb( _trans('keyword.Proposal'),['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),'Proposal'=>   _trans('keyword.Proposal')]) !!}

        <div class="app-ecommerce-category">
            {!! projectTabMenu($project, 'proposal', $project->id) !!}
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th width="12%">{{_trans('keyword.Code')}}</th>
                            <th width="15%">{{_trans('keyword.Title')}}</th>
                            <th>{{_trans('keyword.Due Date')}}</th>
                            <th>{{_trans('keyword.Proposal Date')}}</th>
                            <th>{{_trans('keyword.Amount')}}</th>
                            <th>{{_trans('keyword.Active Status')}}</th>
                            <th>{{_trans('keyword.Published Status')}}</th>
                            <th>{{_trans('keyword.Approve Status')}}</th>
                            <th>{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Offcanvas to add new customer -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCategoryList"
             aria-labelledby="offcanvasEcommerceCategoryListLabel">
            <!-- Offcanvas Header -->
            <div class="offcanvas-header py-4">
                <h5 id="offcanvasEcommerceCategoryListLabel"
                    class="offcanvas-title">{{_trans('keyword.Add')}} {{_trans('keyword.Proposal')}}</h5>
                <button type="button" id="closeAddModal" class="btn-close bg-label-secondary text-reset"
                        data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <!-- Offcanvas Body -->
            <div class="offcanvas-body border-top">
                <form class="pt-0" id="addModal" method="POST">
                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label" for="ecommerce-category-title">{{_trans('keyword.Title')}} <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ecommerce-category-title"
                               placeholder="Enter Title" name="name" aria-label="category title"/>
                        <span class="text-danger nameError error"></span>
                    </div>

                    <div class="mb-4">
                        <label for="flatpickr-date" class="form-label">Due Date<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="flatpickr-date"/>
                        <span class="text-danger dateError error"></span>
                    </div>

                    <!-- Status -->
                    <div class="mb-4 ecommerce-select2-dropdown">
                        <label
                            class="form-label">{{_trans('keyword.Select')}}  {{_trans('keyword.Status')}}</label>
                        <select id="proposal-status" name="status" class="select2 form-select"
                                data-placeholder="Select category status">
                            <option value="1" selected>{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                        <span class="text-danger statusError error"></span>
                    </div>
                    <!-- publish -->
                    <div class="mb-4 ecommerce-select2-dropdown">
                        <label
                            class="form-label">{{_trans('keyword.Select')}}  {{_trans('keyword.Publish Status')}}</label>
                        <select id="proposal-publish" name="status" class="select2 form-select"
                                data-placeholder="Select category status">
                            <option value="1" selected>{{_trans('keyword.Publish')}}</option>
                            <option value="0">{{_trans('keyword.Unpublish')}}</option>
                        </select>
                        <span class="text-danger publishError error"></span>
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

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBrandEditModal"
             aria-labelledby="offcanvasEcommerceCategoryListLabel">
            <!-- Offcanvas Header -->
            <div class="offcanvas-header py-4">
                <h5 id="offcanvasEcommerceCategoryListLabel"
                    class="offcanvas-title">{{_trans('keyword.Edit')}} {{_trans('keyword.Proposal')}}</h5>
                <button type="button" id="closeAddModal" class="btn-close bg-label-secondary text-reset"
                        data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <!-- Offcanvas Body -->
            <div class="offcanvas-body border-top">
                <form class="pt-0" method="POST" action="{{route('project-management.project.proposal.update')}}">

                    @csrf
                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label" for="ecommerce-category-title">{{_trans('keyword.Name')}} <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name"
                               placeholder="Enter category name" name="name" aria-label="category title"/>
                        @error('name')
                        {{$message}}
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="flatpickr-date" class="form-label">Due Date<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="YYYY-MM-DD" name="date"
                               id="edit-flatpickr-date"/>
                        @error('date')
                        {{$message}}
                        @enderror
                    </div>

                    <input type="hidden" id="proposal_id" name="id">

                    <!-- Status -->
                    <div class="mb-4 ecommerce-select2-dropdown">
                        <label
                            class="form-label">{{_trans('keyword.Select')}}  {{_trans('keyword.Status')}}</label>
                        <select id="proposal-status" name="status" class="select2 form-select"
                                data-placeholder="Select category status">
                            <option value="1" selected>{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                        <span class="text-danger statusError error"></span>
                    </div>
                    <!-- publish -->
                    <div class="mb-4 ecommerce-select2-dropdown">
                        <label
                            class="form-label">{{_trans('keyword.Select')}}  {{_trans('keyword.Publish Status')}}</label>
                        <select id="proposal-publish" name="status" class="select2 form-select"
                                data-placeholder="Select category status">
                            <option value="1" selected>{{_trans('keyword.Publish')}}</option>
                            <option value="0">{{_trans('keyword.Unpublish')}}</option>
                        </select>
                        <span class="text-danger publishError error"></span>
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

@endsection

@push('scripts')
    <script>
        $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('project-management.project.proposal.index',$project->id) }}',
                    data: function (d) {
                        d.status = $('#status').val()
                    }
                },
                columns: [
                    {
                        data: 'code',
                        name: 'code',
                    },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'due_date',
                        name: 'due_date',
                    },
                    {
                        data: 'proposal_date',
                        name: 'proposal_date',
                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                    },
                    {
                        data: 'active_status',
                        name: 'active_status',
                    },
                    {
                        data: 'is_published',
                        name: 'is_published',
                    },
                    {
                        data: 'is_approved',
                        name: 'is_approved',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [0, "desc"], //set any columns order asc/desc
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
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown category-select2 select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
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
                    searchPlaceholder: "Search Proposal",
                },
                // Button for offcanvas
                buttons: [

                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>{{_trans('keyword.Add')}} {{_trans('keyword.Proposal')}}</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "offcanvas",
                            "data-bs-target": "#offcanvasEcommerceCategoryList",
                        },
                    },

                ],
            });
            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            });


            $('#addModal').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let name = $("input[name=name]").val();
                let date = $("#flatpickr-date").val();
                let status = $("#proposal-status option:selected").val();
                let publish = $("#proposal-publish option:selected").val();

                formData.append('name', name);
                formData.append('status', status);
                formData.append('publish', publish);
                formData.append('date', date);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('project-management.project.proposal.store',$project->id) }}',
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
                            $('.dateError').text(response.errors?.date ? response
                                    .errors
                                    ?.date[0] :
                                '');
                            $('.statusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                            $('.publishError').text(response.errors?.publish ? response.errors
                                    ?.publish[0] :
                                '');
                        } else if (response.status === 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false)

                            $("input[name=name]").val('');
                            $("#flatpickr-date").val('');

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

            $(document).on("click", ".brand_edit_button", function () {
                const id = $(this).attr("data-id");

                $.ajax({
                    url: '/project-management/project/proposal/edit/' + id,
                    type: 'GET',
                    success: function (response) {
                        $("#edit_name").val(response.title); // Set the Name field
                        $("#proposal_id").val(response.id); // Set the Name field
                        $("#edit-flatpickr-date").val(response.due_date); // Set the Due Date field

                        // Set Active Status dropdown
                        $("#proposal-status").val(response.active_status).trigger("change");

                        // Set Publish Status dropdown
                        $("#proposal-publish").val(response.is_published).trigger("change");


                        // Show the offcanvas modal
                        $("#offcanvasBrandEditModal").offcanvas("show");
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
                            url: '{{ route('project-management.project.proposal.changeStatus',$project->id) }}',
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
                    }
                });
            });
            $(document).on('change', '.changePublishStatus', function () {
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
                            url: '{{ route('project-management.project.proposal.changePublishStatus',$project->id) }}',
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
                    }
                });
            });


            $(document).on("click", ".proposal_delete_button", function () {

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
                            url: '{{ route('project-management.project.proposal.deleteProposal') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false)
                                Swal.fire({
                                    icon: response.icon,
                                    title: response.icon,
                                    text: response.text,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
                            },
                            error: function (error) {
                                console.log(error.responseJSON.message);
                                // handle the error case
                            }
                        });
                    }
                });
            });

            const flatpickrDate = document.querySelector('#flatpickr-date');
            if (flatpickrDate) {
                flatpickrDate.flatpickr({
                    monthSelectorType: 'static'
                });
            }

            const editFlatpickrDate = document.querySelector('#edit-flatpickr-date');
            if (editFlatpickrDate) {
                editFlatpickrDate.flatpickr({
                    monthSelectorType: 'static'
                });
            }

        });
    </script>
@endpush
