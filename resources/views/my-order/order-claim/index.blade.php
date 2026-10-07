@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Order Claims'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.My Order Claims'),['#'=> _trans('keyword.Order').' '. _trans('keyword.Management'),'#'=>_trans('keyword.My').' '._trans('keyword.Order'), 'order'=>_trans('keyword.Order Claims')]) !!}
        <div class="app-ecommerce-category">
            <!-- Order Claims Table -->
            <div class="card">
                {{--    <div class="d-flex gap-3  " >
                        <div class="form-group">
                            <label><strong>{{_trans('keyword.Claim').' '. _trans('keyword.Status')}} :</strong></label>
                            <select id='claim_status' class="form-control filter_dropdown" style="width: 200px">
                                <option value="">{{_trans('keyword.Select').' '. _trans('keyword.Status')}}</option>
                                @foreach($claimIssues as $issue)
                                    <option value="{{$issue->id}}">{{$issue->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                            <select id='status' class="form-control filter_dropdown" style="width: 200px">
                                <option value="">{{_trans('keyword.Select').' '. _trans('keyword.Status')}}</option>
                                <option value="0">{{_trans('keyword.Pending')}}</option>
                                <option value="1">{{_trans('keyword.Accepted')}}</option>
                                <option value="2">{{_trans('keyword.Closed')}}</option>
                            </select>
                        </div>

                    </div>--}}
                <div class="card-datatable">
                    <table class="data-table table border-top" >
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Order ID')}}</th>
                            <th>{{_trans('keyword.Customer')}}</th>
                            <th>{{_trans('keyword.Issue Type')}}</th>
                            <th>{{_trans('keyword.Details')}}</th>
                            <th>{{_trans('keyword.File')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th>{{_trans('keyword.Date Time')}}</th>
                            <th>{{_trans('keyword.Info')}}</th>
                            @if(hasPermission("customer_order_claim_read"))
                                <th>{{_trans('keyword.Action')}}</th>
                            @endif
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
        $(function() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('myOrder.order-claim.index') }}',
                    data: function (d) {
                        d.order_id = new URLSearchParams(window.location.search).get('order_id');
                        d.claim_status = $('#claim_status').val()
                        d.status = $('#status').val()
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'order.code',
                        name: 'order.code'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'issue_type',
                        name: 'issue_type'
                    },
                    {
                        data: 'details',
                        name: 'details',
                        render: function (data, type, row) {
                            const truncated = data.length > 100 ? data.substr(0, 100) + '...' :
                                data;
                            return '<div style="width: 200px; white-space: normal; word-wrap: break-word;">' +
                                truncated + '</div>';
                        }
                    },
                    {
                        data: 'file',
                        name: 'file',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'date_time',
                        name: 'date_time',
                    },
                    {
                        data: 'info',
                        name: 'info',
                    },
                        @if(hasPermission("customer_order_claim_read"))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    @endif
                ],
                order: [2, "desc"], //set any columns order asc/desc
                dom: '<"card-header d-flex flex-wrap pb-2 c-list-header"' +
                    '<f m-0><"custom-text-div">' +
                    '<"d-flex justify-content-center justify-content-md-end align-items-baseline right-side-buttons"<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline"l>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                initComplete: function () {
                    // Set the inner div to display your name
                    $('.custom-text-div').html(`
                     <div class="d-sm-flex gap-3 d-block  " >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Claim').' '. _trans('keyword.Status')}} :</strong></label>
                        <select id='claim_status' class="form-control filter_dropdown select2 order-claims-select-2" style="width: 200px" data-placeholder='{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}'>
                            <option value="">{{_trans('keyword.Select').' '. _trans('keyword.Status')}}</option>
                            @foreach($claimIssues as $issue)
                    <option value="{{$issue->id}}">{{$issue->name}}</option>
                            @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 order-claims-select-2" style="width: 200px" data-placeholder='{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}'>
                            <option value="">{{_trans('keyword.Select').' '. _trans('keyword.Status')}}</option>
                            <option value="0">{{_trans('keyword.Pending')}}</option>
                            <option value="1">{{_trans('keyword.Accepted')}}</option>
                            <option value="2">{{_trans('keyword.Closed')}}</option>
                        </select>
                    </div>

                </div>
              `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.order-claims-select-2').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search in order list",
                },
                "oSearch": {"sSearch": "{{app('request')->input('order_id')}}"}
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })


        });
    </script>
@endpush
