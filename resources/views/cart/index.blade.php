@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Cart'))

@section('content')



    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Cart').' '. _trans('keyword.List'),['#'=>_trans('keyword.Order').' '. _trans('keyword.Management'),'##'=>_trans('keyword.Customer').' '._trans('keyword.Order'),'cart'=>_trans('keyword.Cart').' '. _trans('keyword.List')]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                            <tr>
                                <th>{{_trans('keyword.SL')}}</th>
                                <th>{{_trans('keyword.Customer Name')}}</th>
                                <th>{{_trans('keyword.No. of Item')}}</th>
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
        $(function() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('cart.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'users.name'
                    },
                    {
                        data: 'total',
                        name: 'total',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                initComplete: function () {
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                },
                order: [2, "desc"], //set any columns order asc/desc
                dom: '<"card-header d-flex flex-wrap pb-2 c-list-header"' +
                    '<f m-0><"custom-text-div">' +
                    '<" d-flex justify-content-center justify-content-md-end align-items-baseline right-side-buttons "<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline gap-xl-0 gap-3"l>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search in cart list",
                },
            });


        });
    </script>
@endpush
