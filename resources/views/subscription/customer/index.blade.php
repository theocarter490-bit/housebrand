@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Subscription') . ' ' . _trans('keyword.Customer'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Subscriber') . ' ' . _trans('keyword.List'), [
            '#' => 'Plan & Subscriptions',
            'plan' =>_trans('keyword.Subscriber') . ' ' . _trans('keyword.List'),
        ]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Name') }}</th>
                            <th>{{ _trans('keyword.Shop Info') }}</th>
                            <th>{{ _trans('keyword.Email') }}</th>
                            <th>{{ _trans('keyword.Plan') }}</th>
                            <th>{{ _trans('keyword.Expire Date') }}</th>
                            <th>{{ _trans('keyword.Status') }}</th>
                            <th width="100px">{{ _trans('keyword.Action') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            {{-- add plan modal --}}
            <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCenterTitle">
                                {{ _trans('keyword.Add') . ' ' . _trans('keyword.Plan') }} </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="" method="POST" id="addModalForm">
                            <div class="modal-body">
                                <div class="row mb-2">
                                    <div class="col mb-4">
                                        <label for="name" class="form-label">{{ _trans('keyword.Name') }}</label>
                                        <input type="text" id="name" name="name" class="form-control"
                                               placeholder="Enter Plan Name"/>
                                        <span class="text-danger nameError error"></span>
                                    </div>
                                    <div class="col mb-4">
                                        <label for="city"
                                               class="form-label">{{ _trans('keyword.One time setup fee') }}</label>
                                        <input type="text" name="setupFee" id="setupFee" class="form-control"
                                               placeholder="Enter one time setup fee"/>
                                        <span class="text-danger setupFeeError error"></span>
                                    </div>
                                    <div class="col mb-4">
                                        <label for="planType"
                                               class="form-label">{{ _trans('keyword.Plan') . ' ' . _trans('keyword.Type') }}</label>
                                        <select id="planType" name="planType" class="select2 form-select"
                                                data-placeholder="Select Plan Type">
                                            <option value="day" selected>{{ _trans('keyword.Daily') }}</option>
                                            <option value="week">{{ _trans('keyword.Weekly') }}</option>
                                            <option value="month">{{ _trans('keyword.Monthly') }}</option>
                                            <option value="year">{{ _trans('keyword.Yearly') }}</option>
                                        </select>
                                        <span class="text-danger planTypeError error"></span>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col mb-4">
                                        <label for="price" class="form-label">{{ _trans('keyword.Price') }}</label>
                                        <input type="text" name="price" id="price" class="form-control"
                                               placeholder="Enter price of plan"/>
                                        <span class="text-danger priceError error"></span>
                                    </div>
                                    <div class="col mb-4">
                                        <label for="status" class="form-label">{{ _trans('keyword.Status') }}</label>
                                        <select id="status" name="status" class="select2 form-select"
                                                data-placeholder="Select status">
                                            <option value="1">{{ _trans('keyword.Active') }}</option>
                                            <option value="0" selected>{{ _trans('keyword.Deactive') }}</option>
                                        </select>
                                        <span class="text-danger statusError error"></span>
                                    </div>
                                    <div class="col mb-4">
                                        <label for="palnFor"
                                               class="form-label">{{ _trans('keyword.Plan') . ' ' . _trans('keyword.For') }}</label>
                                        <select id="palnFor" name="palnFor" class="select2 form-select"
                                                data-placeholder="Select Plan">
                                            <option value="3" selected>{{ _trans('keyword.Designer') }}</option>
                                            <option value="5">{{ _trans('keyword.Manufacturer') }}</option>
                                        </select>
                                        <span class="text-danger palnForError error"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-12">

                                        <label>{{ _trans('keyword.Description') }}</label>
                                        <div class="form-control p-0 pt-1">
                                            <div class="commonEditor-toolbar border-0 border-bottom">
                                                <div class="d-flex justify-content-start">
                                                    <span class="ql-formats me-0">
                                                        <button class="ql-bold"></button>
                                                        <button class="ql-italic"></button>
                                                        <button class="ql-underline"></button>
                                                        <button class="ql-list" value="ordered"></button>
                                                        <button class="ql-list" value="bullet"></button>
                                                        <button class="ql-link"></button>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="commonEditor border-0 pb-4" id="description">
                                            </div>
                                        </div>
                                        <span class="text-danger shippingPolicyError error"></span>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="closeAddModal" class="btn btn-label-secondary"
                                            data-bs-dismiss="modal">
                                        {{ _trans('keyword.Close') }}
                                    </button>
                                    <button type="submit" id="addModal"
                                            class="btn btn-primary">{{ _trans('keyword.Save') . ' ' . _trans('keyword.Plan') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- edit plan modal --}}
            <div class="modal fade" id="modalCenterEdit" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCenterTitle">
                                {{ _trans('keyword.Edit') . ' ' . _trans('keyword.Plan') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <form action="" method="POST" id="editModalForm">
                            <div class="modal-body">
                                <div class="row mb-2">
                                    <div class="col mb-4">
                                        <input type="text" id="plan_id" name="plan_id" hidden>
                                        <label for="name" class="form-label">{{ _trans('keyword.Name') }}</label>
                                        <input type="text" id="nameEdit" name="name" class="form-control"
                                               placeholder="Enter Plan Name"/>
                                        <span class="text-danger nameEditError error"></span>
                                    </div>
                                    <div class="col mb-4">
                                        <label for="status" class="form-label">{{ _trans('keyword.Status') }}</label>
                                        <select id="statusEdit" name="status" class="select2 form-select"
                                                data-placeholder="Select status">
                                            <option value="1">{{ _trans('keyword.Active') }}</option>
                                            <option value="0" selected>{{ _trans('keyword.Deactive') }}</option>
                                        </select>
                                        <span class="text-danger statusEditError error"></span>
                                    </div>

                                    <div class="col mb-4">
                                        <label for="palnForEdit"
                                               class="form-label">{{ _trans('keyword.Plan') . ' ' . _trans('keyword.For') }}</label>
                                        <select id="palnForEdit" name="palnForEdit" class="select2 form-select"
                                                data-placeholder="Select Plan">
                                            <option value="3">{{ _trans('keyword.Designer') }}</option>
                                            <option value="5">{{ _trans('keyword.Manufacturer') }}</option>
                                        </select>
                                        <span class="text-danger palnForEditError error"></span>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col mb-12">

                                        <label>{{ _trans('keyword.Description') }}</label>
                                        <div class="form-control p-0 pt-1">
                                            <div class="commonEditor-toolbar1 border-0 border-bottom">
                                                <div class="d-flex justify-content-start">
                                                    <span class="ql-formats me-0">
                                                        <button class="ql-bold"></button>
                                                        <button class="ql-italic"></button>
                                                        <button class="ql-underline"></button>
                                                        <button class="ql-list" value="ordered"></button>
                                                        <button class="ql-list" value="bullet"></button>
                                                        <button class="ql-link"></button>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="commonEditor1 border-0 pb-4" id="descriptionEdit">
                                            </div>
                                        </div>
                                        <span class="text-danger shippingPolicyEditError error"></span>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" id="closeEditModal" class="btn btn-label-secondary"
                                            data-bs-dismiss="modal">
                                        {{ _trans('keyword.Close') }}
                                    </button>
                                    <button type="submit" id="updatePlan" class="btn btn-primary">
                                        {{ _trans('keyword.Update') . ' ' . _trans('keyword.Plan') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                    url: '{{ route('subscription.customer.index')  }}',
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
                        name: 'name'
                    },
                    {
                        data: 'shop_info',
                        name: 'shop_info'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'plan',
                        name: 'plan'
                    },
                    {
                        data: 'expire_date',
                        name: 'expire_date'
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
                order: [1, "desc"], //set any columns order asc/desc

                dom: '<"card-header d-flex flex-wrap pb-2 c-list-header"' +
                    '<f m-0><"custom-text-div">' +
                    '<" d-flex justify-content-center justify-content-md-end align-items-baseline right-side-buttons "<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline gap-xl-0 gap-3"l>>' +
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
                        <select id='status' class="form-control filter_dropdown select2 customer-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Status')}}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="active">{{_trans('keyword.Active')}}</option>
                            <option value="canceled">{{_trans('keyword.Canceled')}}</option>
                        </select>
                    </div>
                  </div>
                    `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.customer-1').select2({
                        allowClear: true,
                    });
                },

                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Subscribers",
                },

            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })


            $(document).ready(function () {
                $('#addModal').click(function (e) {
                    e.preventDefault();
                    $('.error').text('')

                    var formData = new FormData();

                    let name = $("#name").val();
                    let setupFee = $("#setupFee").val();
                    let price = $("#price").val();
                    let planType = $("#planType option:selected").val();
                    let status = $("#status option:selected").val();
                    let palnFor = $("#palnFor option:selected").val();
                    let description = $("#description").children().first().html();

                    formData.append('name', name);
                    formData.append('setupFee', setupFee);
                    formData.append('description', description);
                    formData.append('price', price);
                    formData.append('planType', planType);
                    formData.append('status', status);
                    formData.append('palnFor', palnFor);
                    formData.append('_token', "{{ csrf_token() }}");

                    $.ajax({
                        url: '{{ route('subscription.plan.store') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 403) {
                                $('.nameError').text(response.errors?.name ? response
                                    .errors
                                    .name[0] : '');
                                $('.setupFeeError').text(response.errors?.setupFee ?
                                    response
                                        .errors
                                        .setupFee[0] : '');
                                $('.planTypeError').text(response.errors?.planType ?
                                    response
                                        .errors
                                        .planType[0] : '');
                                $('.priceError').text(response.errors?.price ? response
                                    .errors
                                    .price[0] : '');
                                $('.statusError').text(response.errors?.status ?
                                    response
                                        .errors
                                        .status[0] : '');
                                $('.palnForError').text(response.errors?.palnFor ?
                                    response
                                        .errors
                                        .palnFor[0] : '');
                                $('.shippingPolicyError').text(response.errors
                                    ?.description ?
                                    response
                                        .errors.description[0] : '');
                            } else if (response.status === 200) {
                                // Clear form fields
                                $("#status").val('0').trigger(
                                    'change'); // Reset status to "Active"
                                $("#description").children().first().html('');
                                $("#name").val('');
                                $("#setupFee").val('');
                                $("#price").val('');


                                toastr.success(response.message);
                                $('#closeAddModal').click();
                                table.ajax.reload(null, false);
                            }
                        },
                        error: function (error) {
                            toastr.error(error.responseJSON.message);
                        }
                    });
                });
            });


            $(document).on("click", ".category_edit_button", function () {
                $('.error').text('')
                $id = $(this).attr("data-id");
                $('#editStatus').val(null).trigger('change');
                $.ajax({
                    url: '/subscription/plan/edit/' + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#plan_id').val(response.data.id);
                        $('#nameEdit').val(response.data.name);
                        $('#statusEdit').val(response.data.is_active).trigger('change');
                        $('#palnForEdit').val(response.data.role_id).trigger(
                            'change');
                        $("#descriptionEdit").html(response.data.description);
                        reInitQuillEditor();

                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $(document).ready(function () {
                $('#updatePlan').click(function (e) {
                    e.preventDefault();

                    var formData = new FormData();

                    let id = $('#plan_id').val();
                    let name = $("#nameEdit").val();
                    let status = $("#statusEdit option:selected").val();
                    let palnFor = $("#palnForEdit option:selected").val();
                    let description = $("#descriptionEdit").children().first().html();

                    formData.append('id', id);
                    formData.append('name', name);
                    formData.append('description', description);
                    formData.append('status', status);
                    formData.append('palnFor', palnFor);
                    formData.append('_token', "{{ csrf_token() }}");

                    $('.error').text('')
                    $.ajax({
                        url: '{{ route('subscription.plan.update') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 403) {
                                $('.nameEditError').text(response.errors?.name ?
                                    response
                                        .errors
                                        .name[0] : '');
                                $('.statusEditError').text(response.errors?.status ?
                                    response
                                        .errors
                                        .status[0] : '');
                                $('.palnForEditError').text(response.errors?.palnFor ?
                                    response
                                        .errors
                                        .palnFor[0] : '');
                                $('.shippingPolicyEditError').text(response.errors
                                    ?.description ?
                                    response
                                        .errors.description[0] : '');
                            } else if (response.status === 200) {
                                toastr.success(response.message);
                                $('#closeEditModal').click();
                                table.ajax.reload(null, false);
                            }
                        },
                        error: function (error) {
                            toastr.error(error.responseJSON.message);
                        }
                    });
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
                            url: '{{ route('subscription.plan.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false)
                                Swal.fire({
                                    icon: response.icon,
                                    title: 'Deleted!',
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

            $(document).on('change', '.changeStatus', function () {
                const planId = $(this).data('id');
                const formData = new FormData();
                formData.append('id', planId);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "To change the status of this plan.",
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
                            url: '{{ route('subscription.plan.changeStatus') }}',
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
                    } else {
                        table.ajax.reload(null, false);
                    }

                });
            })


            function reInitQuillEditor() {

                const commonEditor1 = document.querySelector('.commonEditor1');
                if (commonEditor1) {
                    new Quill(commonEditor1, {
                        modules: {
                            toolbar: '.commonEditor-toolbar1'
                        },
                        placeholder: 'Description',
                        theme: 'snow'
                    });
                }
            }

        });
    </script>
@endpush
