@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Pages'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Pages'), [
            '#' => _trans('keyword.Frontend') . ' ' . _trans('keyword.CMS'),
            'page' => _trans('keyword.Pages'),
        ]) !!}

        <div class="app-ecommerce-category">
            <!-- Widget List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                            <tr>
                                <th>{{_trans('keyword.SL')}}</th>
                                <th>{{ _trans('keyword.Title') }}</th>
                                <th>{{ _trans('keyword.Footer') . ' ' . _trans('keyword.Widget') }} </th>
                                <th>{{ _trans('keyword.Short Description') }}</th>
                                <th>{{ _trans('keyword.Content') }}</th>
                                <th>{{ _trans('keyword.Status') }}</th>
                                @if(Auth::user()->role_id == \App\Models\Role::SUPER_ADMIN)
                                <th width="50px">{{ _trans('keyword.Info') }}</th>
                                @endif
                                <th width="100px">{{ _trans('keyword.Action') }}</th>
                            </tr>
                        </thead>
                    </table>
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
                        url: '{{ route('cms.pages.index') }}',
                        data: function(d) {
                            d.status = $('#status').val()
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'footer_name',
                            name: 'footer.title',
                        },
                        {
                            data: 'short_desc',
                            name: 'short_desc',
                            searchable: false
                        },

                        {
                            data: 'content',
                            name: 'content',
                            searchable: false,
                            render: function(data, type, row) {
                                if (data !== null) {
                                    // Strip HTML tags from the data
                                    const plainText = $('<div>').html(data).text();
                                    const truncated = plainText.length > 100 ? plainText.substr(0,
                                        100) + '...' : plainText;

                                    return '<div data-bs-toggle="tooltip" data-bs-placement="right" title="' +
                                        truncated +
                                        '" style="width: 220px; white-space: normal; word-wrap: break-word;">' +
                                        truncated + '</div>';
                                } else {
                                    return '<div style="width: 220px; white-space: normal; word-wrap: break-word;">No description available</div>';
                                }
                            }
                        },
                        {
                            data: 'status',
                            name: 'active_status',
                            searchable: false
                        },
                        @if(Auth::user()->role_id == 1)
                        {
                            data: 'info',
                            name: 'info',
                            searchable: false
                        },
                        @endif
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],

                    order: [0, "desc"], //set any columns order asc/desc
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
                        <select id='status' class="form-control filter_dropdown footer-select-2 select2 form-select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active') }}</option>
                            <option value="0">{{_trans('keyword.Inactive') }}</option>
                         </select>
                    </div>
                  </div>
                   `);
                        $('.data-table').wrap('<div class="overflow-auto"></div>');
                        $('.footer-select-2').select2({
                            allowClear: true,
                        });
                        $(document).ready(function () {
                            $('.dataTables_filter').parent().addClass('c-list-inner');
                        });

                    },
                    lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                    language: {
                        sLengthMenu: "_MENU_",
                        search: "",
                        searchPlaceholder: "Search Page",
                    },
                    // Button for offcanvas
                    buttons: [],
                });

                $(document).on('change', '.filter_dropdown', function () {
                    table.draw();
                })

                $(document).on('change', '.changeStatus', function() {
                    const checkbox = $(this);
                    const isChecked = checkbox.prop('checked');
                    const statusTextElem = checkbox.closest('.form-check').find('.statusText');
                    const statusText = isChecked ? 'Active' : 'Inactive';
                    const badgeClass = isChecked ? 'badge bg-label-success' : 'badge bg-label-danger';

                    // Show SweetAlert2 confirmation dialog
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you want to set the status to ${statusText}?`,
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
                            // Update status text and badge class if confirmed
                            statusTextElem.removeClass().addClass(badgeClass).text(statusText);

                            var formData = new FormData();
                            formData.append('id', checkbox.data('id'));
                            formData.append('_token', "{{ csrf_token() }}");

                            // Proceed with the AJAX request
                            $.ajax({
                                url: '{{ route('cms.pages.changeStatus') }}',
                                type: 'POST',
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: formData,
                                success: function(response) {
                                    if (response.status === 200) {
                                        toastr.success(response.message);
                                        table.ajax.reload(null, false);
                                    } else {
                                        toastr.error(response.message);
                                    }
                                },
                                error: function(error) {
                                    console.error(error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Something went wrong!',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        } else {
                            // Revert checkbox status if the action is canceled
                            checkbox.prop('checked', !isChecked);
                        }
                    });
                });

            });
        </script>
    @endpush
