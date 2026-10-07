@extends('layouts.master')

@section('title', $title ?? __('White Label'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('White Label List', [
            '#' => 'Product Management',
            'cart' => 'White Label List',
        ]) !!}
        <div id="tableLoader" style="
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);">

            <div style="
        width: 50px;
        height: 50px;
        border: 3px solid rgba(0,0,0,0.1);
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;">
            </div>
        </div>

        <style>
            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Product Image') }}</th>
                            <th>{{ _trans('keyword.Product') . ' ' . _trans('keyword.Name') }}</th>
                            <th>{{ _trans('keyword.Product Owner')}}</th>
                            <th>{{ _trans('keyword.Shop Info') }}</th>
                            <th>{{ _trans('keyword.Approve Status') }}</th>
                            @if(isSeller())
                                <th>{{ _trans('keyword.Actions') }}</th>
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
        $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('sharedProduct.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()
                        d.publish_status = $('#publish_status').val()
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                    {
                        data: 'product_image',
                        name: 'product_image',
                    },
                    {
                        data: 'product_name',
                        name: 'product.name',
                        searchable: true,
                    },
                    {
                        data: 'product_owner_info',
                        name: 'seller.shop.shop_name',
                        orderable: false,
                    },
                    {
                        data: 'designer_info',
                        name: 'user.shop.shop_name',
                        searchable: true,
                        orderable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: false
                    },
                        @if(isSeller())
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    @endif
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
                    <div class="d-md-flex gap-3   d-block  " >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Publish Status')}} :</strong></label>
                        <select id='publish_status' class="form-control select2 form-select2 filter_dropdown" style="width: 200px"
                        data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Published')}}</option>
                            <option value="0">{{_trans('keyword.Unpublished')}}</option>
                        </select>
                    </div>

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
                    
                    // Modern fix for Select2 dropdown positioning in responsive layouts
                    $('.select2').each(function () {
                        var $this = $(this);
                        $this.wrap('<div class="position-relative"></div>').select2({
                            allowClear: true,
                            dropdownParent: $this.parent()
                        });
                    });

                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
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

            $(document).on("click", ".product_request_approve_button", function () {

                let id = $(this).attr("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Approve it!',
                    customClass: {
                        confirmButton: 'btn btn-success me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {

                        $.ajax({
                            url: '{{ route('sharedProduct.approve') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                product_request_id: id,
                            },
                            success: function (response) {
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
                            error: function (error) {
                                console.log(error.responseJSON.message);
                                // handle the error case
                            }
                        });
                    }
                });
            });
            $(document).on("click", ".product_request_cancel_button", function () {

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
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('sharedProduct.cancel') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                product_request_id: id,
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

            $(document).on('change', '.changeStatus', function () {
                const productId = $(this).data('id');
                const formData = new FormData();
                formData.append('id', productId);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "To change the status of this product.",
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
                            url: '{{ route('sharedProduct.changeStatus') }}',
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
            });

            $(document).on('click', '.delete_shared_product', function () {
                const requestID = $(this).data('id');
                const formData = new FormData();
                formData.append('id', requestID);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete the White Label item.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete it',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('sharedProduct.destroy') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    table.ajax.reload(null, false);
                                    toastr.success(response.text);
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
            });

            function showLoader() {
                $('#tableLoader').css('display', 'flex');   // flex
            }

            function hideLoader() {
                $('#tableLoader').css('display', 'none');   // hide
            }


            $(document).on('click', '.clone_product', function () {
                const productId = Number($(this).data('id'));
                const sharedId = Number($(this).data('shared-id'));

                const duplicateUrl = "{{ route('product.duplicate', ['productId' => ':productId', 'sharedID' => ':sharedID']) }}"
                    .replace(':productId', productId)
                    .replace(':sharedID', sharedId);


                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to CLONE White Label product.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, CLONE',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        showLoader();
                        $.ajax({
                            url: duplicateUrl,
                            type: 'GET',
                            cache: false,
                            contentType: false,
                            success: function (response) {
                                if (response.status === 200) {
                                    table.ajax.reload(null, false);
                                    hideLoader();
                                    toastr.success(response.text);
                                    window.location.href = '{{route('product.index')}}';
                                } else {
                                    toastr.error(response.text);
                                }
                                hideLoader()
                            },
                            error: function (error) {
                                console.error(error);
                            }
                        });
                    } else {
                        table.ajax.reload(null, false);
                    }

                });
            });


        });
    </script>
@endpush
