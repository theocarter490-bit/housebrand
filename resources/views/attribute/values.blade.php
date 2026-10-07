@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Attribute').' '. _trans('keyword.Value'))

@section('content')

    <div class=" flex-grow-1 container-p-y">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">{{_trans('keyword.Attribute')}} /</span> {{ $attribute->name }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">{{_trans('keyword.Dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="#">{{_trans('keyword.Attributes')}}</a></li>
                    <li class="breadcrumb-item active">{{ $attribute->name }}</li>
                </ol>
            </nav>
        </div>

        <div class="row g-4">
            <div class="col-lg-8 col-md-7 order-1 order-md-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $attribute->name }} {{_trans('keyword.Values List')}}</h5>
                        <small class="text-muted">{{_trans('keyword.Manage values')}}</small>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="data-table table table-hover border-top">
                            <thead class="table-light">
                            <tr>
                                <th>{{_trans('keyword.SL')}}</th>
                                <th>{{_trans('keyword.Name')}}</th>
                                <th>{{_trans('keyword.Value')}}</th>
                                <th width="100px">{{_trans('keyword.Action')}}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAttributeValueEditModal" aria-labelledby="editModalLabel">
                    <div class="offcanvas-header border-bottom">
                        <h5 id="editModalLabel" class="offcanvas-title">{{_trans('keyword.Edit Value')}}</h5>
                        <button type="button" class="btn-close text-reset closeButton" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body p-4">
                        <form class="pt-0" id="updateAttributeValueModal" method="POST">
                            <div class="mb-4 bg-lighter p-3 rounded-3 d-flex justify-content-between align-items-center border">
                                <div>
                                    <label class="form-label mb-0 fw-bold">{{_trans('keyword.Is this a color?')}}</label>
                                    <div class="small text-muted">{{_trans('keyword.Enable to pick a hex code')}}</div>
                                </div>
                                <label class="switch switch-primary switch-lg" style="left: -36px;">
                                    <input type="checkbox" class="switch-input is_color_edit" name="check_color_edit" id="is_color_edit" />
                                    <span class="switch-toggle-slider">
                                    <span class="switch-on"><i class="ti ti-check"></i></span>
                                    <span class="switch-off"><i class="ti ti-x"></i></span>
                                </span>
                                </label>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="text" class="form-control" id="edit_name" placeholder="Enter Value Name" name="edit_name" />
                                <label for="edit_name">{{_trans('keyword.Name')}}</label>
                                <input type="hidden" name="value_id" id="value_id">
                                <span class="text-danger editNameError error small mt-1 d-block"></span>
                            </div>

                            <div class="mb-4 d-none" id="color_edit_div">
                                <label class="form-label mb-2">{{_trans('keyword.Select Color')}}</label>
                                <div class="input-group p-1 border rounded">
                                    <input type="color" name="edit_aditional_value" class="form-control form-control-color border-0 w-100" id="edit_aditional_value" title="Choose your color">
                                </div>
                                <span class="text-danger editAttributeValueError error small mt-1 d-block"></span>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-primary btn-lg data-submit">{{_trans('keyword.Save Changes')}}
                                    <span class="loader"></span>
                                </button>
                                <button type="reset" class="btn btn-label-secondary btn-lg" data-bs-dismiss="offcanvas">{{_trans('keyword.Cancel')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-5 order-0 order-md-1">
                <div class="card border-0 shadow-sm position-sticky" style="top: 2rem; z-index: 1;">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 text-white"><i class="ti ti-plus me-2"></i>{{_trans('keyword.Add New Value')}}</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" id="addModal">
                            <input type="hidden" name="attribute_id" id="attribute_id" value="{{ $attribute->id }}" />

                            <div class="mb-4 bg-lighter p-3 rounded-3 d-flex justify-content-between align-items-center border">
                                <label class="form-label mb-0 fw-bold text-dark">{{_trans('keyword.Is this a color?')}}</label>
                                <label class="switch switch-primary switch-lg " style="left: -36px">
                                    <input type="checkbox" class="switch-input is_color" name="check_color" id="is_color" />
                                    <span class="switch-toggle-slider">
                                    <span class="switch-on"><i class="ti ti-check"></i></span>
                                    <span class="switch-off"><i class="ti ti-x"></i></span>
                                </span>
                                </label>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Red, XL, Cotton" />
                                <label for="name">{{_trans('keyword.Value Name')}}</label>
                                <span class="text-danger nameError error small mt-1 d-block"></span>
                            </div>

                            <div class="mb-4 d-none" id="aditional_value_div">
                                <label class="form-label mb-2">{{_trans('keyword.Pick Color')}}</label>
                                <div class="input-group p-1 border rounded">
                                    <input type="color" name="aditional_value" id="aditional_value" class="form-control form-control-color border-0 w-100" value="#563d7c" />
                                </div>
                                <span class="text-danger aditional_valueError error small mt-1 d-block"></span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm">{{_trans('keyword.Add Value')}}
                                <span class="loader"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(function() {
            // Initialize DataTable
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('attribute.value.index', request()->route()->parameters) }}',
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'value', name: 'value' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [2, "desc"],
                dom: '<"card-header d-flex flex-wrap pb-0 pt-0"' +
                    "<f>" +
                    '<"d-flex justify-content-center justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline"lB>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                lengthMenu: [10, 20, 50, 70, 100],
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search...",
                    paginate: {
                        next: '<i class="ti ti-chevron-right"></i>',
                        previous: '<i class="ti ti-chevron-left"></i>'
                    }
                },
                buttons: [],
            });

            // Add New Value
            $('#addModal').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData();
                let name = $("input[name=name]").val();
                let aditional_value = $("#aditional_value").val();

                formData.append('name', name);
                formData.append('aditional_value', aditional_value);
                formData.append('check_color', $('#is_color').is(":checked"));
                formData.append('attribute_id', $("#attribute_id").val());
                formData.append('_token', "{{ csrf_token() }}");

                $('.loader').show();
                $('button[type=submit]').prop('disabled', true);
                $('.error').text('');

                $.ajax({
                    url: '{{ route('attribute.value.store') }}',
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.status == 403) {
                            $('.nameError').text(response.errors?.name?.[0] || '');
                            $('.aditional_valueError').text(response.errors?.description?.[0] || '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false);
                            $("input[name=name]").val('');
                            $("#aditional_value").val('#563d7c');
                        }
                    },
                    error: function(error) {
                        toastr.error(error.message || 'Something went wrong');
                    },
                    complete: function() {
                        $('.loader').hide();
                        $('button[type=submit]').prop('disabled', false);
                    }
                });
            });

            // Open Edit Modal
            $(document).on("click", ".attribute_value_edit_button", function() {
                let id = $(this).attr("data-id");
                $.ajax({
                    url: '/attribute/value/' + id,
                    type: 'GET',
                    success: function(response) {
                        $('#value_id').val(response.data.id);
                        $('#edit_name').val(response.data.name);

                        // Handle Color Toggle logic for Edit
                        if (response.data.value) {
                            $('#is_color_edit').prop('checked', true);
                            $('#color_edit_div').removeClass('d-none');
                            $('#edit_aditional_value').val(response.data.value);
                        } else {
                            $('#is_color_edit').prop('checked', false);
                            $('#color_edit_div').addClass('d-none');
                            $('#edit_aditional_value').val('#000000');
                        }
                    },
                    error: function(error) {
                        toastr.error("Error fetching data");
                    }
                });
            });

            // Update Value
            $('#updateAttributeValueModal').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData();
                let value_id = $('#value_id').val();
                let name = $("input[name=edit_name]").val();
                let aditional_value = $("#edit_aditional_value").val();

                formData.append('name', name);
                formData.append('aditional_value', aditional_value);
                formData.append('check_color', $('#is_color_edit').is(":checked"));
                formData.append('value_id', value_id);
                formData.append('_token', "{{ csrf_token() }}");

                $('.loader').show();
                $('button[type=submit]').prop('disabled', true);
                $('.error').text('');

                $.ajax({
                    url: '{{ route('attribute.value.update') }}',
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.status == 403) {
                            $('.editNameError').text(response.errors?.name?.[0] || '');
                            $('.editAttributeValueError').text(response.errors?.aditional_value?.[0] || '');
                        } else if (response.status == 200) {
                            $('.closeButton').click();
                            toastr.success(response.message);
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(error) {
                        toastr.error(error.responseJSON?.message || 'Error updating');
                    },
                    complete: function() {
                        $('.loader').hide();
                        $('button[type=submit]').prop('disabled', false);
                    }
                });
            });

            // Delete Value
            $(document).on("click", ".attribute_value_delete_button", function() {
                let id = $(this).attr("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('attribute.value.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                value_id: id,
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);
                                toastr.success(response.text);
                            },
                            error: function(error) {
                                toastr.error('Failed to delete');
                            }
                        });
                    }
                });
            });

            // Toggle Logic for Add Form
            $('.is_color').on('change', function() {
                $('#aditional_value').val('#563d7c');
                if ($(this).is(":checked")) {
                    $('#aditional_value_div').removeClass('d-none'); // Show if checked
                } else {
                    $('#aditional_value_div').addClass('d-none'); // Hide if unchecked
                }
            });

            // Toggle Logic for Edit Form
            $('.is_color_edit').on('change', function() {
                $('#edit_aditional_value').val('#000000');
                if ($(this).is(":checked")) {
                    $('#color_edit_div').removeClass('d-none'); // Show if checked
                } else {
                    $('#color_edit_div').addClass('d-none'); // Hide if unchecked
                }
            });

        });
    </script>
@endpush
