@extends('layouts.master')

@section('title', $title ?? _trans('keyword.My Order'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.My Order').' ' ._trans('keyword.List'),['#'=>_trans('keyword.Order').' '. _trans('keyword.Management'),'##'=>_trans('keyword.My').' '._trans('keyword.Order'), 'order'=>_trans('keyword.My Order').' '._trans('keyword.List')]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                {{--  <div class="d-flex gap-3  " >
                      <div class="form-group">
                          <label><strong>{{_trans('keyword.Order Status')}} :</strong></label>
                          <select id='status' class="form-control filter_dropdown" style="width: 200px">
                              <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                              @foreach($orderStatus as $status)
                                  <option value="{{$status->id}}">{{$status->name}}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>--}}

                <div class="card-datatable table-responsive">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Order ID')}}</th>
                            <th>{{_trans('keyword.Seller')}}</th>
                            <th>{{_trans('keyword.No. of Item')}}</th>
                            <th>{{_trans('keyword.Date')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th>{{_trans('keyword.Payment Status')}}</th>
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
                    url: '{{ route('myOrder.order.index') }}',
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
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'designer.name'
                    },
                    {
                        data: 'items_count',
                        name: 'items_count',
                        searchable: false
                    },
                    {
                        data: 'order_date',
                        name: 'order_date',
                    },
                    {
                        data: 'status',
                        name: 'orderStatus.name',
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                dom: '<"card-header d-flex flex-wrap pb-2"' +
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
                     <div class="d-flex gap-3  " >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Order Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 my-order-list-select-1" style="width: 200px" data-placeholder='{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}'>
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            @foreach($orderStatus as $status)
                    <option value="{{$status->id}}">{{$status->name}}</option>
                            @endforeach
                    </select>
                </div>
            </div>
             `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });

                    $('.my-order-list-select-1').select2({
                        allowClear: true,
                    });

                },
                order: [0, "desc"], //set any columns order asc/desc
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
