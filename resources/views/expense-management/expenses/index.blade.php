@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Expenses'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Expenses'), [
            '#' => _trans('keyword.Expense') . ' ' . _trans('keyword.Management'),
            'emailcampaign' => _trans('keyword.Expenses'),
        ]) !!}
        <div class="app-ecommerce-category">
            <!-- Email Campaign List Table -->
            <div class="card">
                {{--   <div class="d-flex gap-3 "
                   >
                       <div class="form-group">
                           <label><strong>{{ _trans('keyword.Status') }} :</strong></label>
                           <select id='status' class="form-control filter_dropdown" style="width: 200px">
                               <option value="">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Status') }}</option>
                               <option value="1">{{ _trans('keyword.Active') }}</option>
                               <option value="0">{{ _trans('keyword.Inactive') }}</option>
                           </select>
                       </div>
                       <div class="form-group">
                           <label><strong>{{ _trans('keyword.Type') }} :</strong></label>
                           <select id='type' class="form-control filter_dropdown" style="width: 200px">
                               <option value="">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Type') }}</option>
                               @foreach ($expenseTypes as $type)
                                   <option value="{{ $type->id }}">{{ $type->name }}</option>
                               @endforeach
                           </select>
                       </div>
                   </div>--}}

                <div class="card-datatable table-responsive">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Title') }}</th>
                            <th>{{ _trans('keyword.Type') }}</th>
                            <th>{{ _trans('keyword.Expense Date') }}</th>
                            <th>{{ _trans('keyword.Amount') }}</th>
                            <th>{{ _trans('keyword.Payment Method') }}</th>
                            <th>{{ _trans('keyword.Voucher') }}</th>
                            <th>{{ _trans('keyword.Details') }}</th>
                            <th>{{ _trans('keyword.Status') }}</th>
                            <th width="50px">{{ _trans('keyword.Author') }}</th>
                            <th width="100px">{{ _trans('keyword.Action') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Expense Modal -->
    <div class="modal fade p-sm-3 p-0" id="createExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" id="closeModal"
                        aria-label="Close"></button>
                <div class="modal-body p-sm-4 p-0">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{ _trans('keyword.Create') . ' ' . _trans('keyword.Expense') }}</h3>
                    </div>
                    <form id="createForm">

                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Title') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control"
                                   placeholder="Enter title"/>
                            <span class="text-danger titleError error"></span>
                        </div>

                        {{-- Expense Type --}}
                        <div class="mb-2 ecommerce-select2-dropdown">
                            <label class="form-label">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Type') }}
                                <span
                                    class="text-danger">*</span></label>
                            <select id="expense_type" name="expense_type" class="select2 form-select "
                                    style="width: 100%"
                                    data-placeholder="Select type">
                                <option value="">{{ _trans('keyword.Select Type') }}</option>
                                @foreach ($expenseTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger typeError error"></span>
                        </div>
                        {{-- Expense Type --}}


                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Amount') }} <span
                                    class="text-danger">*</span></label>
                            <input type="number" id="amount" name="amount" class="form-control"
                                   placeholder="Enter amount" step="0.01" min="0"/>
                            <span class="text-danger amountError error"></span>
                        </div>

                        <div class="mb-2 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{ _trans('keyword.Payment Method') }} <span
                                    class="text-danger">*</span></label>
                            <select id="payment_method" name="payment_method" class="select2 form-select"
                                    data-placeholder="Select Payment method" style="width: 100%">
                                @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger paymentError error"></span>
                        </div>

                        <div>
                            <label for="formFileLg" class="form-label">{{ _trans('keyword.Voucher') }}</label>
                            <input class="form-control form-control mb-2" id="formFileLg" name="voucher" type="file">
                            <span class="text-danger attachmentError error"></span>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Expense Date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" id="expense_date" name="expense_date" class="form-control"
                                   max="{{ date('Y-m-d') }}"
                                   placeholder="Select expense date"/>
                            <span class="text-danger expenseDateError error"></span>
                        </div>


                        <div class="row card-body">
                            <div class="col-12 mb-2">
                                <label class="form-label">{{ _trans('keyword.Details') }}</label>
                                <div class="form-control p-0 pt-1">
                                    <div class="commonEditor1-toolbar border-0 border-bottom">
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
                                    <div class="commonEditor1 border-0 pb-4" id="createMessageEditor"></div>
                                </div>
                                <span class="text-danger messageError error"></span>
                            </div>
                        </div>

                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Status') }}</label>
                            <select id="active_status" name="active_status" class="select2 form-select"
                                    data-placeholder="Select status">
                                <option value="1">{{ _trans('keyword.Active') }}</option>
                                <option value="0">{{ _trans('keyword.Inactive') }}</option>
                            </select>
                            <span class="text-danger statusError error"></span>
                        </div>


                        <div class="col-12 text-center mt-2">
                            <button type="submit"
                                    class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Save') }}
                                <span class="loader"></span>
                            </button>
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">{{ _trans('keyword.Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Create Expense Modal -->



    <!-- Edit Expense Modal -->
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{ _trans('keyword.Edit') . ' ' . _trans('keyword.Expense') }}</h3>
                    </div>
                    <form id="editForm">
                        <input type="hidden" id="editId" name="id">

                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Title') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="editTitle" name="title" class="form-control"
                                   placeholder="Enter title" required/>
                            <span class="text-danger editTitleError error"></span>
                        </div>

                        {{-- Expense Type --}}
                        <div class="mb-2 ecommerce-select2-dropdown">
                            <label class="form-label">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Type') }}
                                <span
                                    class="text-danger">*</span></label>
                            <select id="editExpenseType" name="expense_type" class="select2 form-select"
                                    style="width: 100%"
                                    data-placeholder="Select type" required>
                                <option value="">{{ _trans('keyword.Select Type') }}</option>
                                @foreach ($expenseTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger editTypeError error"></span>
                        </div>
                        {{-- Expense Type --}}

                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Amount') }} <span
                                    class="text-danger">*</span></label>
                            <input type="number" id="editAmount" name="amount" class="form-control"
                                   placeholder="Enter amount" step="0.01" min="0" required/>
                            <span class="text-danger editAmountError error"></span>
                        </div>

                        <div class="mb-2 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{ _trans('keyword.Payment Method') }} <span
                                    class="text-danger">*</span></label>
                            <select id="edit_payment_method" name="payment_method" class="select2 form-select"
                                    style="width: 100%">
                                @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger editPaymentError error"></span>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-8">
                                <label for="editFormFileLg" class="form-label">{{ _trans('keyword.Voucher') }}</label>
                                <input class="form-control form-control mb-2" id="editFormFileLg" name="voucher"
                                       type="file">
                                <span class="text-danger editAttachmentError error"></span>

                            </div>
                            <div class="col-md-4">
                                <div id="currentAttachmentSection"></div>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Expense Date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="date" id="editExpenseDate" name="expense_date" class="form-control"
                                   placeholder="Select expense date" max="{{ date('Y-m-d') }}" required/>
                            <span class="text-danger editExpenseDateError error"></span>
                        </div>

                        <div class="row card-body">
                            <div class="col-12 mb-2">
                                <label class="form-label">{{ _trans('keyword.Details') }}</label>
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
                                    <div class="commonEditor border-0 pb-4" id="editMessageEditor"></div>
                                </div>
                                <span class="text-danger editMessageError error"></span>
                            </div>
                        </div>

                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Status') }}</label>
                            <select id="editActiveStatus" name="active_status" class="select2 form-select"
                                    data-placeholder="Select status">
                                <option value="1">{{ _trans('keyword.Active') }}</option>
                                <option value="0">{{ _trans('keyword.Inactive') }}</option>
                            </select>
                            <span class="text-danger editStatusError error"></span>
                        </div>

                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                    id="expenseUpdate">{{ _trans('keyword.Update') }}
                                <span class="loader"></span>
                            </button>
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">{{ _trans('keyword.Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Expense Modal -->

@endsection

@push('scripts')
    <script>
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('expense-management.expenses.index') }}',
                    data: function (d) {
                        d.status = $('#status').val();
                        d.type = $('#type').val();
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'expense_date',
                        name: 'expense_date'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method'
                    },
                    {
                        data: 'voucher',
                        name: 'voucher'
                    },
                    {
                        data: 'details',
                        name: 'details',
                        render: function(data, type, row) {
                            if (!data) return '';
                            const truncated = data.length  > 100 ? data.substr(0, 100) + '...' :
                                data;
                            return '<div data-bs-toggle="tooltip" data-bs-placement="right" title="' +
                                data +
                                '" style="width: 220px; white-space: normal; word-wrap: break-word;">' +
                                truncated + '</div>';
                        }
                    },

                    {
                        data: 'status',
                        name: 'active_status'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false

                    },
                ],

                order: [
                    [0, 'desc']
                ],
                "fnDrawCallback": function() {
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
                    <div class="d-xl-flex gap-3   d-block  " >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 expenses-management-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Status')}}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active') }}</option>
                            <option value="0">{{_trans('keyword.Inactive') }}</option>
                         </select>
                    </div>
                  </div>
                  `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.expenses-management-1').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100],
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Expenses",
                },
                buttons: [{
                    text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>Add Expense</span>',
                    className: "create-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                    action: function () {
                        $('#createExpenseModal').modal('show');
                    }
                }],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })

            function resetCreateModal() {
                $('#createForm').trigger('reset');
                $('.titleError').text('');
                $('.typeError').text('');
                $('.amountError').text('');
                $('.expenseDateError').text('');
                $('.paymentError').text('');
                $('.messageError').text('');
                $('.attachmentError').text('');
                $('#formFileLg').val('');
                $('#expense_type').val('').trigger('change');
                $('#payment_method').val('').trigger('change');
            }

            // Open Create Expense modal
            $('.create-new').click(function () {
                resetCreateModal();
                $('#createExpenseModal').modal('show');
            });

            // Submit form to create new campaign
            $('#createForm').submit(function (event) {
                event.preventDefault();
                $('.error').text('');
                let details = $("#createMessageEditor").children().first().html();
                var formData = new FormData();

                let title = $('#title').val();
                if (title && /^\d+$/.test(title)) {
                    $('.titleError').text('Title must not be numeric.');
                    loader.hide();
                    submitButton.prop('disabled', false);
                    return;
                }

                formData.append('title', title);
                formData.append('expense_type', $('#expense_type').val());
                formData.append('active_status', $('#active_status').val());
                formData.append('amount', $('#amount').val());
                formData.append('payment_method', $('#payment_method').val());
                if ($('#formFileLg')[0].files.length > 0) {
                    formData.append('voucher', $('#formFileLg')[0].files[0]);
                }
                formData.append('expense_date', $('#expense_date').val());
                formData.append('details', details);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                loader.show();
                submitButton.prop('disabled', true);

                $.ajax({
                    url: '{{ route('expense-management.expenses.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response.errors)
                        if (response.status === 403) {
                            if (response.message) {
                                toastr.error(response.message);
                            }
                            $('.titleError').text(response.errors?.title ? response.errors
                                .title[0] : '');
                            $('.typeError').text(response.errors?.expense_type ? response.errors
                                .expense_type[0] : '');

                            $('.amountError').text(response.errors?.amount ? response.errors
                                .amount[0] : '');
                            $('.expenseDateError').text(response.errors?.expense_date ? response.errors.expense_date[0] : '');
                            $('.paymentError').text(response.errors?.payment_method ? response.errors.payment_method[0] : '');

                            $('.messageError').text(response.errors?.message ? response.errors
                                .message[0] : '');
                            $('.attachmentError').text(response.errors?.voucher ? response
                                .errors
                                .voucher[0] : '');

                        } else if (response.status === 200) {
                            $('#createExpenseModal').modal('hide');
                            table.ajax.reload(null, false);
                            toastr.success(response.message);
                        } else if (response.status === 403) {
                            table.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    },
                    error: function (error) {
                        console.error('Error creating Expense:', error);
                        toastr.error('Failed to create Expense. Please try again later.');
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Edit expense modal handler
            $(document).on('click', '.edit-expense', function () {
                var id = $(this).data('id');
                $('#editFormFileLg').val('');

                $('#currentAttachmentSection').html('');

                $.ajax({
                    url: '/expense-management/expenses/' + id + '/edit',
                    type: 'GET',
                    success: function (response) {
                        $('#editId').val(response.id);
                        $('#editTitle').val(response.title);
                        $('#editAmount').val(response.amount);
                        $('#edit_payment_method').val(response.payment_method).trigger('change');
                        if (response.voucher) {
                            $('#currentAttachmentSection').html(
                                `<label class="form-label">Current Voucher</label>
                            <div class="mb-2">
                                ${response.voucher}
                            </div>`
                            );
                        } else {
                            $('#currentAttachmentSection').html('');
                        }
                        $('#editExpenseDate').val(response.expense_date);
                        $('#editMessageEditor').html(response
                            .details);
                        $('#editExpenseType').val(response.expense_type).trigger(
                            'change');
                        $('#editActiveStatus').val(response.active_status).trigger('change');
                        reInitQuillEditor();
                        $('#editExpenseModal').modal('show');

                    },
                    error: function (error) {
                        console.error('Error fetching expense data:', error);
                        toastr.error('Failed to fetch expense data.');
                    }
                });
            });


            $(document).ready(function () {
                // Submit form to edit expense
                $('#expenseUpdate').click(function (e) {
                    e.preventDefault();

                    var formData = new FormData();

                    let expense_id = $('#editId').val();
                    let title = $('#editTitle').val();
                    let voucher = $('#editFormFileLg')[0].files[0];
                    let expense_type = $('#editExpenseType').val();
                    let amount = $('#editAmount').val();
                    let expense_date = $('#editExpenseDate').val();
                    let details = $('#editMessageEditor').children().first().html();
                    let active_status = $('#editActiveStatus').val();

                    formData.append('id', expense_id);
                    formData.append('title', title);
                    formData.append('expense_type', expense_type);
                    formData.append('amount', amount);
                    if (voucher) {
                        formData.append('voucher', voucher);
                    }
                    formData.append('expense_date', expense_date);
                    formData.append('details', details);
                    formData.append('active_status', active_status);
                    formData.append('payment_method', $('#edit_payment_method').val());
                    formData.append('_token', "{{ csrf_token() }}");

                    loader.show();
                    submitButton.prop('disabled', true);

                    $.ajax({
                        url: '/expense-management/expenses',
                        type: 'post',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.status === 403) {
                                $('.editTitleError').text(response.errors?.title ?
                                    response.errors
                                        .title[0] : '');
                                $('.editAttachmentError').text(response.errors
                                    ?.voucher ? response.errors
                                    .voucher[0] : '');
                            } else if (response.status === 200) {
                                $('#editExpenseModal').modal('hide');
                                table.ajax.reload(null, false);
                                toastr.success(response.message);
                            }
                        },
                        error: function (error) {
                            console.error('Error updating Expense', error);
                            toastr.error(
                                'Failed to update Expense. Please try again later.'
                            );
                        },
                        complete: function () {
                            loader.hide();
                            submitButton.prop('disabled', false);
                        }
                    });
                });
            });

            // delete expenses
            $(document).on("click", ".delete-expense", function () {
                let id = $(this).data("id");
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
                            url: '{{ route('expense-management.expenses.destroy') }}',
                            method: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.text,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
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
                            url: '{{ route('expense-management.expenses.changeStatus') }}',
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

        });

        function reInitQuillEditor() {

            const commonEditor = document.querySelector('.commonEditor');
            if (commonEditor) {
                new Quill(commonEditor, {
                    modules: {
                        toolbar: '.commonEditor-toolbar'
                    },
                    placeholder: 'Description',
                    theme: 'snow'
                });
            }
        }
    </script>
@endpush
