@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Project Service'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Project Service'),['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),'category'=> _trans('keyword.Project Service')]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Title')}}</th>
                            <th>{{_trans('keyword.Image')}}</th>
                            <th>{{_trans('keyword.Cost')}}</th>
                            <th>{{_trans('keyword.Description')}}</th>
                            <th>{{_trans('keyword.Tax type')}}</th>
                            <th>{{_trans('keyword.Tax')}}</th>
                            <th>{{_trans('keyword.Category')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th>{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>


            <!-- Add  Modal -->
            <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">Add Product Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <form action="{{route('project-management.service.store')}}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                <!-- Title Field -->
                                <div class="col-12 p-0">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="Enter Title"
                                           required/>
                                    @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row mt-3 p-0">
                                    <div class="col-6">
                                        <!-- Image Field -->
                                        <div class="col-12">
                                            <label class="form-label">Image</label>
                                            <input type="file" name="image" class="form-control" accept="image/*"/>
                                            @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <!-- Cost Field -->
                                        <div class="col-12">
                                            <label class="form-label">Cost <span class="text-danger">*</span></label>
                                            <input type="number" name="cost" class="form-control"
                                                   placeholder="Enter Cost" step="0.01" required/>
                                            @error('cost')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-6">
                                        <!-- Tax Type Field -->
                                        <div class="col-12">
                                            <label class="form-label">Tax Type</label>
                                            <select name="tax_type" class="form-select select2"
                                                    data-placeholder="Select tax type">
                                                <option value=""></option>
                                                <option value="1">Percentage</option>
                                                <option value="2">Fixed Amount</option>
                                            </select>
                                            @error('tax_type')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <!-- Tax Field -->
                                        <div class="col-12">
                                            <label class="form-label">Tax Amount</label>
                                            <input type="number" name="tax" class="form-control"
                                                   placeholder="Enter Tax Amount" step="0.01"/>
                                            @error('tax')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-3">
                                    <div class="col-6">
                                        <!-- Service Category Field -->
                                        <div>
                                            <label class="form-label">Service Category <span
                                                    class="text-danger">*</span></label>
                                            <select name="service_category_id" required class="form-select select2"
                                                    data-placeholder="Select Service Category">
                                                <option value="">Select Category</option>
                                                @foreach($serviceCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('service_category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <!-- Active Status Field -->
                                        <div>
                                            <label class="form-label">Status</label>
                                            <select name="active_status" class="form-select select2">
                                                <option value="1">Active</option>
                                                <option value="2">Inactive</option>
                                            </select>
                                            @error('active_status')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <!-- Description Field -->
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"
                                              placeholder="Enter Description"></textarea>
                                    @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                                <!-- Submit and Cancel Buttons -->
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Add
                                        <span class="loader"></span>
                                    </button>
                                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                            aria-label="Close">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add Setting Modal -->

            <div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Product Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <form action="{{route('project-management.service.update')}}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                <!-- Title Field -->
                                <div class="col-12">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control"
                                           placeholder="Enter Title" required/>
                                    @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <input type="hidden" name="service_id" id="service_id">

                                <div class="row mt-3">
                                    <!-- Image Field -->
                                    <div class="col-6">
                                        <label class="form-label">Image</label>
                                        <input type="file" name="image" id="image" class="form-control"
                                               accept="image/*"/>
                                        @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Cost Field -->
                                    <div class="col-6">
                                        <label class="form-label">Cost <span class="text-danger">*</span></label>
                                        <input type="number" name="cost" id="cost" class="form-control"
                                               placeholder="Enter Cost" step="0.01" required/>
                                        @error('cost')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>


                                <div class="image-area">
                                    {{--                                    preview the current image here--}}
                                </div>

                                <div class="row mt-3">
                                    <!-- Tax Type Field -->
                                    <div class="col-6">
                                        <label class="form-label">Tax Type</label>
                                        <select name="tax_type" id="tax_type" class="form-select select2"
                                                data-placeholder="Select tax type">
                                            <option value=""></option>
                                            <option value="1">Percentage</option>
                                            <option value="2">Fixed Amount</option>
                                        </select>
                                        @error('tax_type')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Tax Field -->
                                    <div class="col-6">
                                        <label class="form-label">Tax Amount</label>
                                        <input type="number" name="tax" id="tax" class="form-control"
                                               placeholder="Enter Tax Amount" step="0.01"/>
                                        @error('tax')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-3">

                                    <!-- Service Category Field -->
                                    <div class="col-6">
                                        <label class="form-label">Service Category</label>
                                        <select name="service_category_id" id="service_category_id"
                                                class="form-select select2" data-placeholder="Select Service Category">
                                            <option value="">Select Category</option>
                                            @foreach($serviceCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('service_category_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Active Status Field -->
                                    <div class="col-6">
                                        <label class="form-label">Status</label>
                                        <select name="active_status" id="active_status" class="form-select select2">
                                            <option value="1">Active</option>
                                            <option value="2">Inactive</option>
                                        </select>
                                        @error('active_status')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Description Field -->
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="3"
                                              placeholder="Enter Description"></textarea>
                                    @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Submit and Cancel Buttons -->
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Update
                                        <span class="loader"></span>
                                    </button>
                                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                            aria-label="Close">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(function () {

            @foreach($errors->all() as $error)
            toastr.error('{!! $error !!}');
            @endforeach

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('project-management.service.index') }}',
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
                        data: 'title',
                        name: 'title',
                        render: function (data) {
                            return `<div style="white-space: normal; word-wrap: break-word; max-width: 200px;">${data}</div>`;
                        }
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'cost',
                        name: 'cost'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function (data) {
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
                        data: 'tax_type',
                        name: 'tax_type'
                    },
                    {
                        data: 'tax',
                        name: 'tax'
                    },
                    {
                        data: 'category',
                        name: 'category'
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
                    searchPlaceholder: "Search service",
                },
                // Button for offcanvas
                buttons: [
                        @if(hasPermission('create_services'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>{{_trans('keyword.Add')}} {{_trans('keyword.Service')}}</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "modal",
                            "data-bs-target": "#addServiceModal",
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
                let description = $("#category-descripton").val();
                let status = $("#category-status option:selected").val();
                var image = $('#category-image').prop('files')[0] ?? '';

                formData.append('name', name);
                formData.append('description', description);
                formData.append('status', status);
                formData.append('image', image);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('project-management.service.store') }}',
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
                            $('.imageError').text(response.errors?.image ? response.errors
                                ?.image[0] : '');
                            $('.descriptionError').text(response.errors?.description ? response
                                    .errors
                                    ?.description[0] :
                                '');
                            $('.statusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false)

                            $("input[name=name]").val('');
                            $("#category-descripton").val('');
                            $('#category-image').val('');

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


            $(document).on("click", ".editServiceModal", function () {
                $id = $(this).attr("data-id");

                $.ajax({
                    url: '/project-management/service/edit/' + $id,
                    type: 'GET',
                    success: function (response) {
                        console.log(response)

                        // Fill modal fields with received data
                        $("#service_id").val(response.id);
                        $("#title").val(response.title);
                        $("#cost").val(response.cost);
                        $("#description").val(response.description);
                        $("#tax").val(response.tax);
                        $("#tax_type").val(response.tax_type).trigger('change'); // Trigger change for select2
                        $("#service_category_id").val(response.service_category_id).trigger('change');
                        $("#active_status").val(response.active_status).trigger('change');

                        // Display current image preview
                        if (response.image) {
                            $(".image-area").html(
                                `<img src="${response.image}" class="mt-2" width="100">`
                            );
                        }

                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $('#updateCategoryModal').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let category_id = $('#category_id').val();
                let name = $("input[name=edit_name]").val();
                let description = $("#edit_description").val();
                let status = $("#edit_status option:selected").val();
                var image = $('#edit_image').prop('files')[0] ?? '';

                formData.append('name', name);
                formData.append('description', description);
                formData.append('status', status);
                formData.append('image', image);
                formData.append('category_id', category_id);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('project-management.service.update') }}',
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
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
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
                            url: '{{ route('project-management.service.changeStatus') }}',
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
                            url: '{{ route('project-management.service.destroy') }}',
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


        });
    </script>
@endpush
