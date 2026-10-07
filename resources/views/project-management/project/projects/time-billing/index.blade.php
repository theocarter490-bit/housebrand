@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Time Billing'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb( _trans('keyword.Time Billing'),['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),'Time Billing'=>   _trans('keyword.Time Billing')]) !!}

        <div class="app-ecommerce-category">
            {!! projectTabMenu($project, 'time-billing', $project->id) !!}
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Client')}}</th>
                            <th>{{_trans('keyword.Employee')}}</th>
                            <th>{{_trans('keyword.Service Type')}}</th>
                            <th>{{_trans('keyword.Rate(Hourly)')}}</th>
                            <th>{{_trans('keyword.Time')}}</th>
                            <th>{{_trans('keyword.Billed')}}</th>
                            <th>{{_trans('keyword.Payment Status')}}</th>
                            <th>{{_trans('keyword.Bill Type')}}</th>
                            <th>{{_trans('keyword.Created At')}}</th>
                            <th>{{_trans('keyword.Action')}}</th>
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
                    url: '{{ route('project-management.project.time-billing.index',$project->id) }}',
                    data: function (d) {
                        d.bill_type = $('#bill_type').val(),
                        d.payment_status = $('#payment_status').val()
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name:'id',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'client',
                        name: 'client.name',
                    },
                    {
                        data: 'employee',
                        name: 'employee.name',
                    },
                    {
                        data: 'service_type',
                        name: 'service_type',
                    },
                    {
                        data: 'rate',
                        name: 'rate',
                    },
                    {
                        data: 'time',
                        name: 'time',
                    },
                    {
                        data: 'billed',
                        name: 'billed',
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                    },

                    {
                        data: 'bill_type',
                        name: 'bill_type'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
                        <label><strong>{{_trans('Bill Type')}} :</strong></label>
                        <select id='bill_type' class="form-control filter_dropdown category-select2 select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Bill Type') }}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Bill Type')}}</option>
                            <option value="1">{{_trans('keyword.Billable') }}</option>
                            <option value="0">{{_trans('keyword.Non Billable') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>{{_trans('Payment Status')}} :</strong></label>
                        <select id='payment_status' class="form-control filter_dropdown category-select2 select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Bill Type') }}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Payment Status')}}</option>
                            <option value="1">{{_trans('keyword.Paid') }}</option>
                            <option value="2">{{_trans('keyword.Partially Paid') }}</option>
                            <option value="0">{{_trans('keyword.Unpaid') }}</option>
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
                    searchPlaceholder: "Search Time Billing",
                },
                // Button for offcanvas
                buttons: [],
            });
            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
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
                            url: '{{ route('project-management.project.time-billing.changeStatus') }}',
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


            $(document).on("click", ".time_billing_delete_button", function () {

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
                            url: '{{ route('project-management.project.time-billing.deleteTimeBilling') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false)
                                toastr.success(response.text);
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
