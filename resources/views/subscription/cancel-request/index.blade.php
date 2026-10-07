@extends('layouts.master')

@section('title', $title ?? __('Cancel Request'))

@section('content')



    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('Cancel Request List', [
            '#' => 'Order Management',
            '##' => _trans('keyword.Customer') . ' ' . _trans('keyword.Order'),
            'cart' => 'Cancel Request List',
        ]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                            <tr>
                                <th>{{_trans('keyword.SL')}}</th>
                                <th>{{ _trans('keyword.Designer/Manufacturer') . ' ' . _trans('keyword.Info') }}</th>
                                <th>{{ _trans('keyword.Plan Name') }}</th>
                                <th>{{ _trans('keyword.subject') }}</th>
                                <th>{{ _trans('keyword.Description') }}</th>
                                <th>{{ _trans('keyword.File') }}</th>
                                <th>{{ _trans('keyword.Comment') }}</th>
                                <th>{{ _trans('keyword.Details') }}</th>
                                <th>{{ _trans('keyword.Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="enableOTP" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-3">
                            <h3 class="mb-2">Customer Order Info</h3>
                        </div>
                        <div id="customer_order_count">

                        </div>
                        <div class="text-center mb-3">
                            <h3 class="mb-2">Own Order Info</h3>
                        </div>
                        <div id="own_order_count">

                        </div>


                    </div>
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
                ajax: '{{ route('subscription.cancelRequest.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'user_info',
                        name: 'user.name',
                        searchable: true
                    },
                    {
                        data: 'plan_name',
                        name: 'plan.name',
                    },
                    {
                        data: 'subject',
                        name: 'subject',
                        render: function(data, type, row) {
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
                        data: 'description',
                        name: 'description',
                        render: function(data, type, row) {
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
                        data: 'file',
                        name: 'file',
                    },
                    {
                        data: 'comments',
                        name: 'comments',
                        render: function(data, type, row) {
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
                        data: 'details_button',
                        name: 'details_button',
                        orderable: false,
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
                order: [0, "desc"], //set any columns order asc/desc
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
                    searchPlaceholder: "Search here",
                },
            });

            $(document).on("click", ".product_request_approve_button", function() {

                let id = $(this).attr("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Approve it!',
                    customClass: {
                        confirmButton: 'btn btn-success me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {

                        $.ajax({
                            url: '{{ route('subscription.cancelRequest.approve') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                request_id: id,
                            },
                            success: function(response) {
                                table.ajax.reload(null, false)
                                Swal.fire({
                                    icon: response.icon,
                                    title: 'Success!',
                                    text: response.text,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
                            },
                            error: function(error) {
                                console.log(error.responseJSON.message);
                                // handle the error case
                            }
                        });
                    }
                });
            });
            $(document).on("click", ".product_request_cancel_button", function() {

                let id = $(this).attr("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Cancel it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.value) {

                        $.ajax({
                            url: '{{ route('subscription.cancelRequest.cancel') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                request_id: id,
                            },
                            success: function(response) {
                                table.ajax.reload(null, false)
                                Swal.fire({
                                    icon: response.icon,
                                    title: 'Canceled!',
                                    text: response.text,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
                            },
                            error: function(error) {
                                console.log(error.responseJSON.message);
                                // handle the error case
                            }
                        });
                    }
                });
            });
            $(document).on("click", ".details", function() {
                $('#own_order_count').html('');
                $('#customer_order_count').html('');

                let id = $(this).attr("data-id");
                $.ajax({
                    url: '{{ route('subscription.cancelRequest.orderCount') }}',
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        request_id: id,
                    },
                    success: function(response) {

                        let str = '';

                        $(response.customerOrderCount).each(function(index, value) {
                            str += `<p>${value.name}: <span class="text-dark badge  bg-opacity-25" style="background: rgb(from ${value.color} r g b / 50%);">${value.total}</span> </p>`
                        });
                        $('#customer_order_count').append(str);
                        str = '';
                        $(response.ownOrderCount).each(function(index, value) {
                            str += `<p>${value.name}: <span class="text-dark badge  bg-opacity-25" style="background: rgb(from ${value.color} r g b / 50%);">${value.total}</span></p>`
                        });
                        $('#own_order_count').append(str);
                    },
                    error: function(error) {
                        console.log(error.responseJSON.message);
                        // handle the error case
                    }
                });
            });



        });
    </script>
@endpush
