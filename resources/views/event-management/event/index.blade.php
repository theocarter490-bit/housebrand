@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Event').' '._trans('keyword.Management'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Events') ,['#'=>_trans('keyword.Event').' '._trans('keyword.Management'),'category'=>_trans('keyword.Events')]) !!}
        <div class="app-ecommerce-category">
            <div class="card">
                {{--  <div class="d-flex gap-3 " >
                      <div class="form-group">
                          <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                          <select id='status' class="form-control filter_dropdown" style="width: 200px">
                              <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                              <option value="1">{{_trans('keyword.Active')}}</option>
                              <option value="0">{{_trans('keyword.Deactive')}}</option>
                          </select>
                      </div>

                      <div class="form-group">
                          <label><strong>{{_trans('keyword.Event Type')}} :</strong></label>
                          <select id='type' class="form-control filter_dropdown" style="width: 200px">
                              <option value="">{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                              @foreach($eventTypes as $type)
                                  <option value="{{ $type->id }}">{{ $type->name }}</option>
                              @endforeach
                          </select>
                      </div>

                      <!-- Date Range Filter -->
                      <div class="form-group">
                          <label><strong>{{ _trans('keyword.Date') }} :</strong></label>
                          <div class="d-flex">
                              <input type="date" id="start_date" class="form-control filter_date" style="width: 150px; margin-right: 10px;" placeholder="{{ _trans('keyword.Start Date') }}">
                              <input type="date" id="end_date" class="form-control filter_date" style="width: 150px;" placeholder="{{ _trans('keyword.End Date') }}">
                          </div>
                      </div>

                  </div>--}}
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Title')}}</th>
                            <th>{{_trans('keyword.Event'). ' '. _trans('keyword.Type')}}</th>
                            <th>{{_trans('keyword.Start Date')}}</th>
                            <th>{{_trans('keyword.End Date')}}</th>
                            <th>{{_trans('keyword.Event Url')}}</th>
                            <th>{{_trans('keyword.Location')}}</th>
                            <th>{{_trans('keyword.File')}}</th>
                            <th>{{_trans('keyword.Description')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th>{{_trans('keyword.Email Notify')}}</th>
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{ _trans('keyword.Add') . ' ' . _trans('keyword.New') . ' ' . _trans('keyword.Event') }}</h3>
                    </div>

                    <!-- Start Form -->
                    <form action="{{ route('event-management.event.store') }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <!-- Name -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Title') }}<span
                                    style="color: red;">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter title"
                                   value="{{ old('name') }}" required/>
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Event Type -->
                        <div class="col ecommerce-select2-dropdown mb-3">
                            <label class="form-label mb-1">{{ _trans('keyword.Event') . ' ' . _trans('keyword.Type') }}
                                <span style="color: red;">*</span></label>
                            <select id="event_type" name="event_type" class="select2 form-select" style="width: 100%;"
                                    required>
                                <option value="" disabled {{ old('event_type') ? '' : 'selected' }}>Select Type</option>
                                @foreach($eventTypes as $type)
                                    <option
                                        value="{{ $type->id }}" {{ old('event_type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Start Date') }}<span
                                    style="color: red;">*</span></label>
                            <input type="datetime-local" name="start_date" class="form-control"
                                   placeholder="Select Start Date" value="{{ old('start_date') }}" required/>
                            @error('start_date')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.End Date') }}<span style="color: red;">*</span></label>
                            <input type="datetime-local" name="end_date" class="form-control"
                                   placeholder="Select End Date" value="{{ old('end_date') }}" required/>
                            @error('end_date')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Event URL -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Event Url') }}</label>
                            <input type="text" name="event_url" class="form-control" placeholder="Event Url"
                                   value="{{ old('event_url') }}"/>
                            @error('event_url')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Location') }}</label>
                            <input type="text" name="location" class="form-control" placeholder="Event location"
                                   value="{{ old('location') }}"/>
                            @error('location')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Upload File')}}</label>
                            <input type="file" name="file" class="form-control">
                            @error('file')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Description') }}</label>
                            <textarea name="description" class="form-control" cols="20" rows="3"
                                      placeholder="Description">{{ old('description') }}</textarea>
                            @error('description')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3 col ecommerce-select2-dropdown">
                            <label class="form-label mb-1">{{ _trans('keyword.Status') }}<span
                                    style="color: red;">*</span></label>
                            <select name="active_status" class="select2 form-select" required>
                                <option value="1">{{ _trans('keyword.Active') }}</option>
                                <option value="0">{{ _trans('keyword.Inactive') }}</option>
                            </select>
                            @error('active_status')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-check mb-3 col">
                            <input id="email_notify" name="email_notify" class="form-check-input" type="checkbox"
                                   @if(old('email_notify')==1) checked @endif value="1">
                            <label class="form-check-label" for="defaultCheck3"> Email Notify </label>
                        </div>

                        <div id="alert_info" style="display: none" class="alert alert-warning" role="alert">An Email
                            will be sent 30 minutes before the event.
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                    id="addFaq">{{ _trans('keyword.Submit') }}
                                <span class="loader"></span>
                            </button>
                            <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                {{ _trans('keyword.Cancel') }}
                            </button>
                        </div>
                    </form>
                    <!-- End Form -->
                </div>
            </div>
        </div>
    </div>
    <!--/ Add Modal -->


    <!-- Edit Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{ _trans('keyword.Edit') . ' ' . _trans('keyword.Event') }}</h3>
                    </div>

                    <!-- Start Form -->
                    <form action="{{ route('event-management.event.update') }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" id="event_id" name="event_id" value="">

                        <!-- Name -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Title') }}<span
                                    style="color: red;">*</span></label>
                            <input type="text" id="edit_name" name="name" class="form-control" placeholder="Enter title"
                                   required/>
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Event Type -->
                        <div class="col ecommerce-select2-dropdown mb-3">
                            <label class="form-label mb-1">{{ _trans('keyword.Event') . ' ' . _trans('keyword.Type') }}
                                <span style="color: red;">*</span></label>
                            <select id="edit_event_type" name="event_type" class="select2 form-select"
                                    style="width: 100%" required>
                                <option value="" selected disabled>Select Type</option>
                                @foreach($eventTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('event_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Start Date') }}<span
                                    style="color: red;">*</span></label>
                            <input type="datetime-local" id="edit_start_date" name="start_date" class="form-control"
                                   placeholder="Select Start Date" required/>
                            @error('start_date')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.End Date') }}<span style="color: red;">*</span></label>
                            <input type="datetime-local" id="edit_end_date" name="end_date" class="form-control"
                                   placeholder="Select End Date" required/>
                            @error('end_date')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Event URL -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Event Url') }}</label>
                            <input type="text" id="edit_event_url" name="event_url" class="form-control"
                                   placeholder="Event Url"/>
                            @error('event_url')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Location') }}</label>
                            <input type="text" id="edit_location" name="location" class="form-control"
                                   placeholder="Event location"/>
                            @error('location')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 mb-3" id="fileArea" style="display: none;">
                            <label class="form-label">{{ _trans('keyword.Current File')}}</label>
                            <div id="currentImageContainer" class="col-12 mb-3">
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Upload File')}}</label>
                            <input type="file" name="file" class="form-control">
                            @error('file')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ _trans('keyword.Description') }}</label>
                            <textarea id="edit_description" name="description" class="form-control" cols="20" rows="3"
                                      placeholder="Description"></textarea>
                            @error('description')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3 col ecommerce-select2-dropdown">
                            <label class="form-label mb-1">{{ _trans('keyword.Status') }}<span
                                    style="color: red;">*</span></label>
                            <select id="edit_active_status" name="active_status" class="select2 form-select" required>
                                <option value="1" selected>{{ _trans('keyword.Active') }}</option>
                                <option value="0">{{ _trans('keyword.Inactive') }}</option>
                            </select>
                            @error('active_status')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-check mb-3 col">
                            <input name="email_notify" class="form-check-input" type="checkbox" value="1"
                                   id="edit_email_notify">
                            <label class="form-check-label" for="defaultCheck3"> Email Notify </label>
                        </div>

                        <div id="edit_alert_info" style="display: none" class="alert alert-warning" role="alert">An
                            Email will be sent 30 minutes before the event.
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                    id="addFaq">{{ _trans('keyword.Submit') }}
                                <span class="loader"></span>
                            </button>
                            <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                {{ _trans('keyword.Cancel') }}
                            </button>
                        </div>
                    </form>
                    <!-- End Form -->
                </div>
            </div>
        </div>
    </div>
    <!--/ Edit Modal -->



    <!-- Hidden Button for Triggering Modal -->
    <div style="display: none">
        <button id="openAddEventModal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
            {{ _trans('keyword.Add').' '._trans('keyword.Event') }}
        </button>
    </div>

@endsection

@push('scripts')
    <script>
        $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('event-management.event.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()
                        d.type = $('#type').val()
                        d.start_date = $('#start_date').val()
                        d.end_date = $('#end_date').val()
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function (data) {
                            return `<div style="white-space: normal; word-wrap: break-word; max-width: 200px;">${data}</div>`;
                        }
                    },
                    {
                        data: 'event_type',
                        name: 'event_type'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'event_url',
                        name: 'event_url',
                        render: function (data) {
                            return `<div style="white-space: normal; word-wrap: break-word; max-width: 200px;">${data}</div>`;
                        }
                    },
                    {
                        data: 'location',
                        name: 'location',
                        render: function (data) {
                            if (data != null) {
                                return `<div style="white-space: normal; word-wrap: break-word; max-width: 200px;">${data}</div>`;
                            }
                            return '';
                        }
                    },
                    {
                        data: 'file',
                        name: 'file',
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function (data, type, row) {
                            if (!data) return '';
                            const truncated = data.length > 100 ? data.substr(0, 100) + '...' :
                                data;
                            return '<div data-bs-toggle="tooltip" data-bs-placement="right" title="' +
                                data +
                                '" style="width: 220px; white-space: normal; word-wrap: break-word;">' +
                                truncated + '</div>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'email_notify',
                        name: 'email_notify'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],

                order: [0, "desc"], //set any columns order asc/desc
                "fnDrawCallback": function () {
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
                    <div class="d-flex gap-3 flex-md-row flex-column xl-mb-0 mb-3" >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown select2 events-management-event-1" style="width: 200px" data-placeholder='{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}'>
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active')}}</option>
                            <option value="0">{{_trans('keyword.Inactive')}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Event Type')}} :</strong></label>
                        <select id='type' class="form-control filter_dropdown select2 events-management-event-2" style="width: 200px" data-placeholder='{{ _trans('keyword.Select') }} {{ _trans('keyword.Type') }}'>
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                            @foreach($eventTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="form-group">
                    <label><strong>{{ _trans('keyword.Date') }} :</strong></label>
                        <div class="d-sm-flex d-block w-100 gap-3">
                            <input type="date" id="start_date" class="form-control filter_date w-sm-auto w-100 mb-sm-0 mb-3" style="width: 150px; " placeholder="{{ _trans('keyword.Start Date') }}">
                            <input type="date" id="end_date" class="form-control filter_date w-sm-auto w-100" style="width: 150px;" placeholder="{{ _trans('keyword.End Date') }}">
                        </div>
                    </div>

                </div>
                 `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.events-management-event-1').select2({
                        allowClear: true,
                    });
                    $('.events-management-event-2').select2({
                        allowClear: true,
                    });
                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Event",
                },
                // Button for offcanvas
                buttons: [
                        @if(hasPermission('event_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span >{{_trans('keyword.Add').' '._trans('keyword.Event')}}</span>',
                        className: "create-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        attr: {
                            "data-bs-toggle": "modal",
                            "data-bs-target": "#addEventModal",
                        },
                    },
                    @endif
                ],
            });

            // Trigger table redraw on filter change (dropdowns and date pickers)
            /*  $('.filter_dropdown, #start_date, #end_date').change(function () {
                  table.draw();
              });*/
            $(document).on('change', ['.filter_dropdown', '#start_date', '#end_date'], function () {
                table.draw();
            });


            $(document).ready(function () {
                $('#reset').click(function (e) {
                    e.preventDefault();
                    $("input[name=title]").val('');
                    $("#status option:selected").prop('selected', false);
                    $("#description").children().first().html('');
                });
            })

            $(document).ready(function () {
                @if ($errors->any())
                $('#openAddEventModal').click();
                @endif
            });

            $(document).ready(function () {
                function handleCheckboxToggle(checkboxSelector, alertSelector) {
                    // Check the initial state when the page or modal is loaded
                    if ($(checkboxSelector).prop('checked')) {
                        $(alertSelector).show();
                    } else {
                        $(alertSelector).hide();
                    }

                    // Toggle the alert div visibility based on the checkbox change event
                    $(checkboxSelector).on('change', function () {
                        if ($(this).prop('checked')) {
                            $(alertSelector).show();
                        } else {
                            $(alertSelector).hide();
                        }
                    });
                }

                // Call the function for both the initial and edit checkboxes
                handleCheckboxToggle('#email_notify', '#alert_info');
                handleCheckboxToggle('#edit_email_notify', '#edit_alert_info');
            });


            $(document).on("click", ".type_edit_button", function () {
                $('.error').text('')
                $id = $(this).attr("data-id");
                $('#edit_active_status').val(null).trigger('change');
                $('#edit_event_type').val(null).trigger('change');
                $.ajax({
                    url: '/event-management/event/edit/' + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#event_id').val(response.data.id);
                        $("#edit_name").val(response.data.name);
                        $("#edit_event_type").val(response.data.event_type_id).trigger('change');
                        $("#edit_start_date").val(response.data.start_date);
                        $("#edit_end_date").val(response.data.end_date);
                        $("#edit_event_url").val(response.data.event_url);
                        $("#edit_location").val(response.data.location);
                        $("#edit_description").val(response.data.description);
                        $('#edit_active_status').val(response.data.active_status).trigger('change');

                        // Set the checkbox checked state and show the alert info if necessary
                        $('#edit_email_notify').prop('checked', response.data.email_notify === 1);
                        if (response.data.email_notify === 1) {
                            $('#edit_alert_info').show();
                        } else {
                            $('#edit_alert_info').hide();
                        }

                        // $('#currentFile').parent().empty()
                        if (response.data.file != null) {
                            $('#fileArea').show();
                            $('#currentImageContainer').html(response.data.file);
                        } else {
                            // Hide the file area div if no file is present
                            $('#fileArea').hide();
                        }


                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $(document).on("click", ".type_delete_button", function () {

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
                            url: '{{ route('event-management.event.delete') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false)
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
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

            $(document).on('change', '.changeStatus', function () {
                const id = $(this).data('id');
                const formData = new FormData();
                formData.append('id', id);
                formData.append('_token', "{{ csrf_token() }}");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "To change the status of this Event.",
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
                            url: '{{ route('event-management.event.change-status') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    table.ajax.reload(null, false);
                                }
                            },
                            error: function (error) {
                                console.error(error);
                            }
                        });
                    } else {
                        table.ajax.reload(null, false);
                    }

                })
            })
        })

    </script>
@endpush

