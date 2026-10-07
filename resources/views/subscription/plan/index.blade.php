@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Plans'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Plan') . ' ' . _trans('keyword.List'), [
            '#' => _trans('keyword.Plan') . ' ' . _trans('keyword.Subscription'),
            'plan' => _trans('keyword.Plan') . ' ' . _trans('keyword.List'),
        ]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{ _trans('keyword.SL') }}</th>
                            <th>{{ _trans('keyword.Plan For') }}</th>
                            <th>{{ _trans('keyword.Name') }}</th>
                            <th>{{ _trans('keyword.Type') }}</th>
                            <th>{{ _trans('keyword.Price') }}</th>
                            <th>{{ _trans('keyword.Setup') . ' ' . _trans('keyword.Fee') }}</th>
                            <th>{{ _trans('keyword.Description') }}</th>
                            <th>{{ _trans('keyword.Modules') }}</th>
                            <th>{{ _trans('keyword.Status') }}</th>
                            <th>{{ _trans('keyword.Is Popular') }}</th>
                            <th>{{ _trans('keyword.Action') }}</th>
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
                                {{ _trans('keyword.Add') . ' ' . _trans('keyword.Plan') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="" method="POST" id="addModalForm">
                            <div class="modal-body">
                                <div class="row mb-2">
                                    <div class="col-lg-4 col-sm-6 mb-4">
                                        <label for="name" class="form-label">{{ _trans('keyword.Name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" required id="name" name="name" class="form-control"
                                               placeholder="Enter Plan Name"/>
                                        <span class="text-danger nameError error"></span>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 mb-4">
                                        <label for="city"
                                               class="form-label">{{ _trans('keyword.One time setup fee') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" required name="setupFee" id="setupFee" class="form-control"
                                               placeholder="Enter one time setup fee"/>
                                        <span class="text-danger setupFeeError error"></span>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 mb-4">
                                        <label for="planType"
                                               class="form-label">{{ _trans('keyword.Plan Type') }}</label>
                                        <select id="planType" name="planType" class="select2 form-select"
                                                style="width: 100%" required data-placeholder="Select Plan Type">
                                            <option value="day" selected>{{ _trans('keyword.Daily') }}</option>
                                            <option value="week">{{ _trans('keyword.Weekly') }}</option>
                                            <option value="month">{{ _trans('keyword.Monthly') }}</option>
                                            <option value="year">{{ _trans('keyword.Yearly') }}</option>
                                        </select>
                                        <span class="text-danger planTypeError error"></span>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 mb-4">
                                        <label for="price" class="form-label">{{ _trans('keyword.Price') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" required name="price" id="price" class="form-control"
                                               placeholder="Enter price of plan"/>
                                        <span class="text-danger priceError error"></span>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 mb-4">
                                        <label for="status" class="form-label">{{ _trans('keyword.Status') }}</label>
                                        <select id="planStatus" required name="status" class="select2 form-select"
                                                style="width: 100%" data-placeholder="Select status">
                                            <option value="1">{{ _trans('keyword.Active') }}</option>
                                            <option value="0" selected>{{ _trans('keyword.Inactive') }}</option>
                                        </select>
                                        <span class="text-danger statusError error"></span>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 mb-4">
                                        <label for="palnFor"
                                               class="form-label">{{ _trans('keyword.Plan') . ' ' . _trans('keyword.For') }}</label>
                                        <select id="palnFor" required name="palnFor" class="select2 form-select"
                                                style="width: 100%" data-placeholder="Select Plan For">
                                            <option value="3" selected>{{ _trans('keyword.Designer') }}</option>
                                            <option value="5">{{ _trans('keyword.Manufacturer') }}</option>
                                        </select>
                                        <span class="text-danger palnForError error"></span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6 mt-4">
                                        <h5>Modules: <span class="text-danger">*</span></h5>
                                        <div class="row">
                                            @foreach ($modules as $module)
                                                <div class="col-md-12 mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <!-- Module Checkbox -->
                                                        <div class="form-check me-3">
                                                            <label class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="modules[]"
                                                                       value="{{ $module->slug }}"
                                                                       id="module_check_{{ $module->slug }}"
                                                                       class="module-checkbox form-check-input"
                                                                       data-target="limitDiv_{{ $module->slug }}">
                                                                <span>{{ $module->title }}</span>
                                                            </label>
                                                        </div>

                                                        <!-- Limit Input and Unlimited Option -->
                                                        <div class="align-items-center limitDiv"
                                                             id="limitDiv_{{ $module->slug }}"
                                                             style="display: none !important;">
                                                            <label for="limit_{{ $module->slug }}"
                                                                   class="me-2 mb-0">Limit:</label>
                                                            <input type="number"
                                                                   name="modules_limit[{{ $module->slug }}]"
                                                                   id="limit_{{ $module->slug }}"
                                                                   class="form-control form-control-sm me-2" value="0"
                                                                   min="0" placeholder="Enter limit"
                                                                   style="width: 100px;">

                                                            <div class="form-check">
                                                                <label class="checkbox checkbox-primary">
                                                                    <input type="checkbox"
                                                                           name="unlimited_check[{{ $module->slug }}]"
                                                                           class="form-check-input"
                                                                           value="{{ $module->slug }}">
                                                                    <span>Unlimited</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-6">

                                        <label>{{ _trans('keyword.Description') }} <span
                                                class="text-danger">*</span></label>
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
                                        <span class="text-danger descriptionError error"></span>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="closeAddModal" class="btn btn-label-secondary"
                                            data-bs-dismiss="modal">
                                        {{ _trans('keyword.Close') }}
                                    </button>
                                    <button type="submit" id="addModal"
                                            class="btn btn-primary">{{ _trans('keyword.Save') . ' ' . _trans('keyword.Plan') }}
                                        <span class="loader"></span>
                                    </button>
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
                                    <div class="col-4 mb-4">
                                        <input type="text" id="plan_id" name="plan_id" hidden>
                                        <label for="name" class="form-label">{{ _trans('keyword.Name') }}</label>
                                        <input type="text" required id="nameEdit" name="name" class="form-control"
                                               placeholder="Enter Plan Name"/>
                                        <span class="text-danger nameEditError error"></span>
                                    </div>

                                    <div class="col-4 mb-4 planforEditContainer">
                                        <label for="palnForEdit"
                                               class="form-label">{{ _trans('keyword.Plan For') }}</label>
                                        <select id="palnForEdit" required name="palnForEdit" class="select2 form-select"
                                                data-placeholder="Select Plan For">
                                            <option value="3">{{ _trans('keyword.Designer') }}</option>
                                            <option value="5">{{ _trans('keyword.Manufacturer') }}</option>
                                        </select>
                                        <span class="text-danger palnForEditError error"></span>
                                    </div>

                                    <div class="col-4 mb-4">
                                        <label for="status" class="form-label">{{ _trans('keyword.Status') }}</label>
                                        <select id="statusEdit" required name="status" class="select2 form-select"
                                                data-placeholder="Select status">
                                            <option value="1">{{ _trans('keyword.Active') }}</option>
                                            <option value="0" selected>{{ _trans('keyword.Inactive') }}</option>
                                        </select>
                                        <span class="text-danger statusEditError error"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mt-4">
                                        <h5>Modules: <span class="text-danger">*</span></h5>
                                        <div class="row">
                                            @foreach ($modules as $module)
                                                <div class="col-md-12 mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <!-- Module Checkbox -->
                                                        <div class="form-check me-3">
                                                            <label class="checkbox checkbox-primary">
                                                                <input type="checkbox" name="modules[]"
                                                                       value="{{ $module->slug }}"
                                                                       id="module_check_edit_{{ $module->slug }}"
                                                                       class="module-checkbox-edit form-check-input"
                                                                       data-target="limitDiv_edit_{{ $module->slug }}">
                                                                <span>{{ $module->title }}</span>
                                                            </label>
                                                        </div>

                                                        <!-- Limit Input and Unlimited Option -->
                                                        <div class="align-items-center limitDiv limitDivEdit"
                                                             id="limitDiv_edit_{{ $module->slug }}"
                                                             style="display: none !important;">
                                                            <label for="limit_{{ $module->slug }}"
                                                                   class="me-2 mb-0">Limit:</label>
                                                            <input type="number"
                                                                   name="modules_limit_edit[{{ $module->slug }}]"
                                                                   id="limit_edit_{{ $module->slug }}"
                                                                   class="form-control form-control-sm me-2 limit_input_edit"
                                                                   value="0"
                                                                   min="0" placeholder="Enter limit"
                                                                   style="width: 100px;">

                                                            <div class="form-check">
                                                                <label class="checkbox checkbox-primary">
                                                                    <input type="checkbox"
                                                                           id="unlimited_check_edit_{{ $module->slug }}"
                                                                           name="unlimited_check_edit[{{ $module->slug }}]"
                                                                           class="form-check-input unlimited_check_edit"
                                                                           value="{{ $module->slug }}">
                                                                    <span>Unlimited</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-6">

                                        <label>{{ _trans('keyword.Description') }} <span
                                                class="text-danger">*</span></label>
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
                                        <span class="text-danger descriptionEditError error"></span>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" id="closeEditModal" class="btn btn-label-secondary"
                                            data-bs-dismiss="modal">
                                        {{ _trans('keyword.Close') }}
                                    </button>
                                    <button type="submit" id="updatePlan"
                                            class="btn btn-primary">{{ _trans('keyword.Update') . ' ' . _trans('keyword.Plan') }}
                                        <span class="loader"></span>
                                    </button>
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
                    url: '{{ route('subscription.plan.index') }}',
                    data: function (d) {
                        d.status = $('#active_status').val()
                        d.plan_type = $('#plan_type').val()
                        d.plan_for = $('#plan_for').val()
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'plan_for',
                        name: 'plan_for'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'plan_type',
                        name: 'plan_type'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'setup_fee',
                        name: 'setup_fee'
                    },

                    {
                        data: 'description',
                        name: 'description',
                        render: function (data, type, row) {
                            if (data !== null) {
                                const truncated = data.length > 100 ? data.substr(0, 100) + '...' :
                                    data;
                                return '<div data-bs-toggle="tooltip" data-bs-placement="right" title="' +
                                    jQuery(data).text() +
                                    '" style="width: 220px; white-space: normal; word-wrap: break-word;">' +
                                    truncated + '</div>';
                            } else {
                                return '<div style="width: 220px; white-space: normal; word-wrap: break-word;">No description available</div>';
                            }
                        }
                    },
                    {
                        data: 'modules',
                        name: 'modules',
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'is_popular',
                        name: 'is_popular'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [0, "desc"], //set any columns order asc/desc
                "fnDrawCallback": function () {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
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
                    <div class="d-md-flex gap-3   d-block  " >
                        <div class="form-group">
                        <label><strong>{{ _trans('keyword.Status') }} :</strong></label>
                        <select id='active_status' class="form-control filter_dropdown select2 plan-list-1" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') . ' ' . _trans('keyword.Status') }}">
                            <option value="">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Status') }}</option>
                            <option value="1">{{ _trans('keyword.Active') }}</option>
                            <option value="0">{{ _trans('keyword.Inactive') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>{{ _trans('keyword.Plan') . ' ' . _trans('keyword.Type') }}:</strong></label>
                        <select id='plan_type' class="form-control filter_dropdown select2 plan-list-2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') . ' ' . _trans('keyword.Plan') . ' ' . _trans('keyword.Type') }}">
                            <option value="">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Plan') . ' ' . _trans('keyword.Type') }}</option>
                            <option value="day">{{ _trans('keyword.Daily') }}</option>
                            <option value="week">{{ _trans('keyword.Weekly') }}</option>
                            <option value="month">{{ _trans('keyword.Monthly') }}</option>
                            <option value="year">{{ _trans('keyword.Yearly') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>{{ _trans('keyword.Plan For') }}:</strong></label>
                        <select id='plan_for' class="form-control filter_dropdown plan-list-3" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') . ' ' . _trans('keyword.Plan For') }}">
                            <option value="">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Plan For') }}</option>
                            <option value="5">{{ _trans('keyword.Manufacturer') }}</option>
                            <option value="3">{{ _trans('keyword.Designer') }}</option>
                        </select>
                    </div>
                  </div>
                   `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.plan-list-1').select2({
                        allowClear: true,
                    });
                    $('.plan-list-2').select2({
                        allowClear: true,
                    });
                    $('.plan-list-3').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Plan",
                },
                // Button for offcanvas
                buttons: [

                        @if (hasPermission('plan_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>Add Plan</span>',
                        className: "add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "modal",
                            "data-bs-target": "#modalCenter",
                        },

                    },
                    @endif

                ],
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
                    let status = $("#planStatus option:selected").val();
                    let palnFor = $("#palnFor option:selected").val();

                    let modules = [];
                    $('.module-checkbox:checked').each(function () {
                        let module = $(this).val();
                        let limit = $(`input[name="modules_limit[${module}]"]`).val();
                        let unlimitedCheck = $(`input[name="unlimited_check[${module}]"]`)
                            .is(':checked');
                        if (unlimitedCheck) {
                            limit = 'unlimited';
                        }
                        modules.push({
                            slug: module,
                            limit: limit
                        });
                    });
                    console.log(modules);


                    let description = $("#description").children().first().html();
                    if (description === "<p><br></p>") {
                        $('.descriptionError').text('Description is required');
                        return;
                    }

                    formData.append('name', name);
                    formData.append('setupFee', setupFee);
                    formData.append('description', description);
                    formData.append('price', price);
                    formData.append('planType', planType);
                    formData.append('status', status);
                    formData.append('palnFor', palnFor);
                    formData.append('modules', JSON.stringify(modules));
                    formData.append('_token', "{{ csrf_token() }}");

                    loader.show();
                    submitButton.prop('disabled', true);

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
                                $('.TagifyUserListError').text(response.errors
                                    ?.modules_slug ?
                                    response
                                        .errors.modules_slug[0] : '');
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
                        },
                        complete: function () {
                            loader.hide();
                            submitButton.prop('disabled', false);
                        }
                    });
                });
            });

            $(document).on("click", ".category_edit_button", function () {
                $('.planforEditContainer').show();
                $('.module-checkbox-edit').prop('checked', false);
                $('.limitDivEdit').css("display", "none");
                $('.limit_input_edit').val(0);
                $('.unlimited_check_edit').prop('checked', false);


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
                        $(`.conditionCheckEdit`).prop('checked', false);
                        $(`.conditionEdit`).val(0);

                        if (response.data.name && response.data.name.toLowerCase() === 'free trial') {
                            $('.planforEditContainer').hide();
                        }

                        $(JSON.parse(response.data.modules)).each(function (index, data) {
                            console.log(data);
                            $(`#module_check_edit_${data.slug}`).prop('checked', true);
                            $(`#limitDiv_edit_${data.slug}`).css('display', 'flex');
                            if (data.limit != 'unlimited') {
                                $(`#limit_edit_${data.slug}`).val(data.limit);
                            } else {
                                $(`#limit_edit_${data.slug}`).val(0);
                                $(`#unlimited_check_edit_${data.slug}`).prop('checked', true);
                            }
                        });


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


                    let modules = [];
                    $('.module-checkbox-edit:checked').each(function () {
                        let module = $(this).val();
                        let limit = $(`input[name="modules_limit_edit[${module}]"]`).val();
                        let unlimitedCheck = $(`input[name="unlimited_check_edit[${module}]"]`)
                            .is(':checked');
                        if (unlimitedCheck) {
                            limit = 'unlimited';
                        }
                        modules.push({
                            slug: module,
                            limit: limit
                        });
                    });


                    if (description === "<p><br></p>") {
                        $('.descriptionEditError').text('Description is required');
                        return;
                    }

                    let condition = [];
                    let conditionsElement = $('.conditionEdit');
                    conditionsElement.each(function (index, element) {
                        let keyElement = $(element).attr('id');
                        let value = $(element).val();
                        console.log(keyElement, value);
                        condition.push({
                            [keyElement]: value,
                        })
                    });
                    formData.append('id', id);
                    formData.append('name', name);
                    formData.append('description', description);
                    formData.append('status', status);
                    formData.append('planFor', palnFor??null);
                    formData.append('modules', JSON.stringify(modules));
                    formData.append('_token', "{{ csrf_token() }}");

                    condition.forEach((item, index) => {
                        const key = Object.keys(item)[0];
                        let value = parseInt(item[key]);
                        if ($(`#${key}unlimitedCheckEdit`).is(':checked')) {
                            value = 'unlimited';
                        }

                        formData.append(`condition[${index}][${key}]`, value);
                    });

                    loader.show();
                    submitButton.prop('disabled', true);

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
                                $('.palnForEditError').text(response.errors?.planFor ?
                                    response
                                        .errors
                                        .planFor[0] : '');
                                $('.shippingPolicyEditError').text(response.errors
                                    ?.description ?
                                    response
                                        .errors.description[0] : '');
                                $('.modules_slugEditError').text(response.errors
                                    ?.modules_slug ?
                                    response
                                        .errors.modules_slug[0] : '');
                            } else if (response.status === 200) {
                                toastr.success(response.message);
                                $('#closeEditModal').click();
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
                    html: "<span style='color: red;'>To change the status of this plan. This action will affect all current subscribers of this plan</span>",
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

                    table.draw();

                });
            })

            $(document).on('change', '.popularStatus', function () {
                const planId = $(this).data('id');
                const formData = new FormData();
                formData.append('id', planId);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "To make this plan as Popular.",
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
                            url: '{{ route('subscription.plan.makePopular') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.module-checkbox');


            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const targetId = this.getAttribute('data-target');
                    const limitDiv = document.getElementById(targetId);

                    if (this.checked) {
                        limitDiv.style.display =
                            'flex'; // Show the limit input and unlimited checkbox
                    } else {
                        limitDiv.style.display =
                            'none'; // Hide the limit input and unlimited checkbox

                        // Optionally reset input fields if unchecked
                        const input = limitDiv.querySelector('input[type="number"]');
                        const unlimited = limitDiv.querySelector('input[type="checkbox"]');

                        if (input) input.value = 0;
                        if (unlimited) unlimited.checked = false;
                    }
                });

                // Trigger initial state on page load (e.g., edit mode)
                checkbox.dispatchEvent(new Event('change'));
            });

            const checkboxesEdit = document.querySelectorAll('.module-checkbox-edit');

            checkboxesEdit.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const targetId = this.getAttribute('data-target');
                    const limitDiv = document.getElementById(targetId);

                    if (this.checked) {
                        limitDiv.style.display =
                            'flex'; // Show the limit input and unlimited checkbox
                    } else {
                        limitDiv.style.display =
                            'none'; // Hide the limit input and unlimited checkbox

                        // Optionally reset input fields if unchecked
                        const input = limitDiv.querySelector('input[type="number"]');
                        const unlimited = limitDiv.querySelector('input[type="checkbox"]');

                        if (input) input.value = 0;
                        if (unlimited) unlimited.checked = false;
                    }
                });

                // Trigger initial state on page load (e.g., edit mode)
                checkbox.dispatchEvent(new Event('change'));
            });
        });
    </script>
@endpush
