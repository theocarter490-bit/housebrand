@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Subscription') . ' ' . _trans('keyword.Invitation'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Invited Users List'), [
            '#' => 'Plan & Subscriptions',
            'plan' =>_trans('keyword.Invited Users') . ' ' . _trans('keyword.List'),
        ]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Name') }}</th>
                            <th>{{ _trans('keyword.Email') }}</th>
                            <th>{{ _trans('keyword.Invitation Code') }}</th>
                            {{-- <th>{{ _trans('keyword.Status') }}</th> --}}
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
                    url: '{{ route('subscription.free-trail')  }}',
                    data: function (d) {
                        d.status = $('#status').val()
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'trail_code',
                        name: 'trail_code'
                    },
                    // {
                    //     data: 'status',
                    //     name: 'status'
                    // },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [1, "desc"], //set any columns order asc/desc

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
                        <select id='status' class="form-control filter_dropdown select2 customer-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select').' '._trans('keyword.Status')}}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>
                  </div>
                    `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.customer-1').select2({
                        allowClear: true,
                    });
                },

                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Code/User",
                },

            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            })


            function reInitQuillEditor() {

                const commonEditor1 = document.querySelector('.commonEditor1');
                if (commonEditor1) {
                    new Quill(commonEditor1, {
                        modules: {
                            toolbar: '.commonEditor-toolbar1'
                        },
                        placeholder: 'Description',
                        theme: 'snow'
                    });
                }
            }

            $(document).on("click", ".terminate", function () {

                let id = $(this).attr("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Once terminated, this user must use subscription plan to sign up again.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Terminate it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {

                        $.ajax({
                            url: '{{ route('subscription.free-trail.terminate') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                user_id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false)
                                toastr.success(response.text);
                            },
                            error: function (error) {
                                console.log(error.responseJSON.message);
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
