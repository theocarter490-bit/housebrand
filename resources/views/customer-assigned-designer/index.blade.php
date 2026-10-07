@extends('layouts.master')

@section('title', $title ?? __('Join Customer'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('Join Customer', [
            '#' => 'User',
            'cart' => 'Join Customer',
        ]) !!}


        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Customer') }}</th>
                            <th>{{ _trans('keyword.Current Designer') }}</th>
                            <th>{{ _trans('keyword.New Designer') }}</th>
                            <th>{{ _trans('keyword.Customer Note')}}</th>
                            <th>{{ _trans('keyword.Admin Note') }}</th>
                            <th>{{ _trans('keyword.Approve Status') }}</th>
                            <th>{{ _trans('keyword.Actions') }}</th>
                        </tr>
                        </thead>
                    </table>
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
                    url: '{{ route('assignedDesigner.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                    {
                        data: 'customer_details',
                        name: 'customer.name',
                    },
                    {
                        data: 'current_designer_details',
                        name: 'currentDesigner.name',
                    },
                    {
                        data: 'new_designer_details',
                        name: 'newDesigner.name',
                    },
                    {
                        data: 'customer_note',
                        name: 'customer_note',
                        searchable: false,
                    },
                    {
                        data: 'admin_note',
                        name: 'admin_note',
                        searchable: false,
                    },

                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: false
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
                        <select id='status' class="form-control filter_dropdown select2 form-select2" style="width: 200px"
                        data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}" >
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Approved')}}</option>
                            <option value="0">{{_trans('keyword.Pending')}}</option>
                            <option value="2">{{_trans('keyword.Cancelled')}}</option>
                        </select>
                    </div>
                  </div>
`);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.select2').select2({
                        allowClear: true,
                    });

                },

                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search in list",
                },
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })

            function processDesignerRequest(id, action) {
                const isApprove = action === 'approve';

                Swal.fire({
                    title: isApprove ? 'Approve Request' : 'Cancel Request',
                    html: isApprove ? 'Add a message for the customer regarding the approval.' : '<div class="text-start mt-2"><h5 class="fw-bold mb-1">Reason for Cancellation</h5><p class="text-muted small">Please provide a detailed explanation for why this request is being cancelled. This note will be visible to the customer and recorded in the history.</p></div>',
                    input: 'textarea',
                    inputPlaceholder: 'Write a note...',
                    inputValue: isApprove ? 'Your request has been approved.' : 'The specifications do not meet requirements.',
                    confirmButtonText: isApprove ? 'Approve' : 'Yes, Cancel Request',
                    customClass: {
                        confirmButton: isApprove ? 'btn btn-success me-3' : 'btn btn-danger me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false,
                    inputValidator: (value) => (!isApprove && !value) ? 'A reason is required!' : null
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.post('{{ route('assignedDesigner.updateStatus') }}', {
                            _token: "{{ csrf_token() }}",
                            request_id: id,
                            action: action,
                            note: result.value
                        })
                            .done(response => {
                                toastr.success(response.text);
                                table.ajax.reload(null, false);
                            })
                            .fail(xhr => {
                                toastr.error(xhr.responseJSON?.text || 'Something went wrong');
                            });
                    }
                });
            }

// Click Listeners
            $(document).on("click", ".customer_assign_approve_button", function () {
                processDesignerRequest($(this).data("id"), 'approve');
            });

            $(document).on("click", ".customer_assign_cancel_button", function () {
                processDesignerRequest($(this).data("id"), 'cancel');
            });
        });
    </script>
@endpush
