@php use App\Models\Role; @endphp
@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Products'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Products') . ' ' . _trans('keyword.List'), [
            '#' => _trans('keyword.Product') . ' ' . _trans('keyword.Management'),
            'products' => _trans('keyword.Product') . ' ' . _trans('keyword.List'),
        ]) !!}

        <div class="app-ecommerce-category">

            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Image') }}</th>
                            <th>{{ _trans('keyword.Name') }}</th>
                            <th>{{ _trans('keyword.Category') }}</th>
                            <th>{{ _trans('keyword.Brand') }}</th>
                            <th>{{ _trans('keyword.Base') }} {{ _trans('keyword.Price') }}</th>
                            <th>{{ _trans('keyword.Status') }}</th>
                            <th>{{ _trans('keyword.Review') }}</th>
                            <th>{{ _trans('keyword.Author') }}</th>
                            <th width="100px">{{ _trans('keyword.Action') }}</th>
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
                    url: "{{ route('product.index') }}",
                    data: function (d) {
                        d.status = $('#status').val(),
                            d.category = $('#category').val()
                        d.brand = $('#brand').val()
                        d.role = $('#role').val()
                        d.user = $('#user').val()
                    }
                },

                columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    searchable: false
                },
                    {
                        data: 'thumbnail_img',
                        name: 'thumbnail_img',
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category_name',
                        name: 'category.name',
                        orderable: true,
                    },
                    {
                        data: 'brand_name',
                        name: 'brand.name',
                        orderable: true,
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price',
                        orderable: true,
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'review',
                        name: 'review'
                    },
                    {
                        data: 'added_by',
                        name: 'user.shop.shop_name',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],

                order: [0, "desc"], //set any columns order asc/desc
                dom: '<"card-header d-flex   pb-2 product-list-header px-lg-4 px-0"' +
                    '<"d-flex flex-lg-row flex-column justify-content-between align-items-xl-end align-items-center gap-3 product-list-header-inner"' +
                    '<f m-0><"custom-text-div">' +
                    '>' +
                    '<"d-flex justify-content-center justify-content-md-end align-items-baseline right-side-buttons px-lg-0 px-3"' +
                    '<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline flex-md-row flex-column"' +
                    'lB>>' +
                    '>t' +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                initComplete: function () {
                    // Set the inner div to display your name
                    $('.custom-text-div').html(`
<div class="d-flex gap-2 flex-md-row flex-column px-3">

    <div class="">
        <div class="form-group">
            <label><strong>{{ _trans('keyword.Status') }} :</strong></label>
            <select id="status" class="form-control filter_dropdown select2 form-select2"
                data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
                <option value="">{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}</option>
                <option value="1">{{ _trans('keyword.Published') }}</option>
                <option value="0">{{ _trans('keyword.Unpublished') }}</option>
            </select>
        </div>
    </div>

    <div class="">
        <div class="form-group">
            <label><strong>{{ _trans('keyword.Category') }} :</strong></label>
            <select id="category" class="form-control filter_dropdown select2 form-select2"
             data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Category') }}">
                <option value="">{{ _trans('keyword.Select') }} {{ _trans('keyword.Category') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
                    </select>
        </div>
    </div>

            <div class="">
                <div class="form-group">
                    <label><strong>{{ _trans('keyword.Brands') }} :</strong></label>
            <select id="brand" class="form-control filter_dropdown select2 form-select2"
             data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Brands') }}">
                <option value="">{{ _trans('keyword.Select') }} {{ _trans('keyword.Brands') }}</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
                    </select>
                </div>
            </div>

@if(Auth::user()->role_id == Role::SUPER_ADMIN)
                    <div class="">
                        <div class="form-group">
                            <label><strong>{{ _trans('keyword.Seller') }} {{ _trans('keyword.Type') }} :</strong></label>
            <select id="role" class="form-control filter_dropdown select2 form-select2"
             data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Type') }}">
                <option value="">{{ _trans('keyword.Select') }} {{ _trans('keyword.Type') }}</option>
                <option value="{{ Role::DESIGNER }}">{{ _trans('keyword.Designers') }}</option>
                <option value="{{ Role::MANUFACTURER }}">{{ _trans('keyword.Manufacturer') }}</option>
            </select>
        </div>
    </div>
    @endif

                    <div class="" id="userSection" style="display:none;">
                        <div class="form-group">
                            <label><strong>{{ _trans('keyword.Select') }} {{ _trans('keyword.Seller') }} :</strong></label>
            <select id="user" class="form-control filter_dropdown select2 form-select2"
             data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Seller') }}">
                <option value="">{{ _trans('keyword.Select') }} {{ _trans('keyword.Seller') }}</option>
            </select>
        </div>
    </div>

</div>
`);

                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $('.select2').select2({
                        allowClear: true,
                    });

                },
                displayLength: 10,
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Product",
                },
                // Button for offcanvas
                buttons: [
                        @if (hasPermission('product_export'))
                    {
                        extend: 'collection',
                        className: 'btn btn-label-primary dropdown-toggle me-2 waves-effect waves-light',
                        text: '<i class="ti ti-file-export me-sm-1"></i> <span>Export</span>',
                        buttons: [
                            {
                                extend: 'print',
                                text: '<i class="ti ti-printer me-1" ></i>Print',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [2, 3, 4],

                                    // prevent avatar to be display
                                    format: {
                                        body: function (inner, coldex, rowdex) {
                                            if (inner.length <= 0) return inner;
                                            var el = $.parseHTML(inner);
                                            var result = '';
                                            $.each(el, function (index, item) {
                                                if (item.classList !== undefined &&
                                                    item
                                                        .classList.contains('user-name')
                                                ) {
                                                    result = result + item.lastChild
                                                        .firstChild.textContent;
                                                } else if (item.innerText ===
                                                    undefined) {
                                                    result = result + item
                                                        .textContent;
                                                } else result = result + item
                                                    .innerText;
                                            });
                                            return result;
                                        }
                                    }
                                },
                                customize: function (win) {
                                    //customize print view for dark
                                    $(win.document.body)
                                        .css('color', config.colors.headingColor)
                                        .css('border-color', config.colors.borderColor)
                                        .css('background-color', config.colors.bodyBg);
                                    $(win.document.body)
                                        .find('table')
                                        .addClass('compact')
                                        .css('color', 'inherit')
                                        .css('border-color', 'inherit')
                                        .css('background-color', 'inherit');
                                }
                            },
                            {
                                extend: 'csv',
                                text: '<i class="ti ti-file-text me-1" ></i>Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [2, 3, 4],
                                    // prevent avatar to be display
                                    format: {
                                        body: function (inner, coldex, rowdex) {
                                            if (inner.length <= 0) return inner;
                                            var el = $.parseHTML(inner);
                                            var result = '';
                                            $.each(el, function (index, item) {
                                                if (item.classList !== undefined &&
                                                    item
                                                        .classList.contains('user-name')
                                                ) {
                                                    result = result + item.lastChild
                                                        .firstChild.textContent;
                                                } else if (item.innerText ===
                                                    undefined) {
                                                    result = result + item
                                                        .textContent;
                                                } else result = result + item
                                                    .innerText;
                                            });
                                            return result;
                                        }
                                    }
                                }
                            },
                            {
                                extend: 'excel',
                                text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [2, 3, 4],
                                    // prevent avatar to be display
                                    format: {
                                        body: function (inner, coldex, rowdex) {
                                            if (inner.length <= 0) return inner;
                                            var el = $.parseHTML(inner);
                                            var result = '';
                                            $.each(el, function (index, item) {
                                                if (item.classList !== undefined &&
                                                    item
                                                        .classList.contains('user-name')
                                                ) {
                                                    result = result + item.lastChild
                                                        .firstChild.textContent;
                                                } else if (item.innerText ===
                                                    undefined) {
                                                    result = result + item
                                                        .textContent;
                                                } else result = result + item
                                                    .innerText;
                                            });
                                            return result;
                                        }
                                    }
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="ti ti-file-description me-1"></i>Pdf',
                                className: 'dropdown-item',
                                customize: function (doc) {
                                    // Align headers left
                                    doc.styles.tableHeader.alignment = 'left';
                                },
                                exportOptions: {
                                    columns: [2, 3, 4],
                                    // prevent avatar to be display
                                    format: {
                                        body: function (inner, coldex, rowdex) {
                                            if (inner.length <= 0) return inner;
                                            var el = $.parseHTML(inner);
                                            var result = '';
                                            $.each(el, function (index, item) {
                                                if (item.classList !== undefined &&
                                                    item
                                                        .classList.contains('user-name')
                                                ) {
                                                    result = result + item.lastChild
                                                        .firstChild.textContent;
                                                } else if (item.innerText ===
                                                    undefined) {
                                                    result = result + item
                                                        .textContent;
                                                } else result = result + item
                                                    .innerText;
                                            });
                                            return result;
                                        }
                                    }
                                }
                            },
                            {
                                extend: 'copy',
                                text: '<i class="ti ti-copy me-1" ></i>Copy',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [2, 3, 4],
                                    // prevent avatar to be display
                                    format: {
                                        body: function (inner, coldex, rowdex) {
                                            if (inner.length <= 0) return inner;
                                            var el = $.parseHTML(inner);
                                            var result = '';
                                            $.each(el, function (index, item) {
                                                if (item.classList !== undefined &&
                                                    item
                                                        .classList.contains('user-name')
                                                ) {
                                                    result = result + item.lastChild
                                                        .firstChild.textContent;
                                                } else if (item.innerText ===
                                                    undefined) {
                                                    result = result + item
                                                        .textContent;
                                                } else result = result + item
                                                    .innerText;
                                            });
                                            return result;
                                        }
                                    }
                                }
                            }
                        ]

                    },
                        @endif
                        @if (hasPermission('product_create') && getUserId()!=1)
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>{{ _trans('keyword.Add') }} {{ _trans('keyword.Products') }}</span>',
                        className: "create-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        action: function () {
                            window.location.href = '{{ route('product.create') }}';
                        }
                    },
                    @endif
                ],
            });


            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })
            // $(document).ready(function () {
            //     $('.dataTables_filter').parent().css('width', '100%');
            // })
            $(document).on("click", ".product_delete_button", function () {

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
                            url: '{{ route('product.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                product_id: id,
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
                            url: '{{ route('product.changeStatus') }}',
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

            $(document).ready(function () {
                $('#role').change(function () {
                    var selectedRole = $(this).val();
                    if (selectedRole) {
                        $('#userSection').show();
                        $.ajax({
                            url: '{{ route('product.user', '') }}/' + selectedRole,
                            method: 'GET',
                            success: function (data) {
                                $('#user').empty();
                                $('#user').append(
                                    '<option value="">Select User</option>');
                                $.each(data.users, function (index, user) {
                                    $('#user').append('<option value="' + user
                                        .id + '">' + user.name + '</option>'
                                    );
                                });
                            },
                            error: function (error) {
                                console.log(error.responseJSON.message);
                                // handle the error case
                            }
                        });

                    } else {
                        $('#userSection').hide();
                    }
                });
            });

        });

    </script>
@endpush
