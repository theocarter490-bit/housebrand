@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Assign User'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Assign Audience') ,['#'=>_trans('keyword.Event').' '._trans('keyword.Management'),'/event-management/event'=>_trans('keyword.Events'), '##'=> _trans('keyword.Assign Audience')]) !!}
        <div class="row invoice-preview">
            <div class="col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card py-2">

                    <div class="px-3 py-1 d-flex justify-items-center">
                        <h6 class="mb-1 fw-bold">{{_trans('keyword.Title')}}:</h6>
                        <span class="ms-1">{{ $event->name }}</span>
                    </div>

                    @if($event->description)
                        <div class="px-3 py-1 d-flex justify-items-center">
                            <h6 class="mb-1 fw-bold">{{_trans('keyword.Description')}}: </h6>
                            <span class="ms-1">{!! $event->description !!}</span>
                        </div>
                    @endif

                    <div class="px-3 py-1 d-flex justify-items-center">
                        <h6 class="mb-1 fw-bold">{{_trans('keyword.Start Date')}}: </h6>
                        <span class="ms-1">{!! dateFormatwithTime($event->start_date) !!}</span>
                    </div>
                    <div class="px-3 py-1 d-flex justify-items-center">
                        <h6 class="mb-1 fw-bold">{{_trans('keyword.End Date')}}: </h6>
                        <span class="ms-1">{!! dateFormatwithTime($event->end_date) !!}</span>
                    </div>
                    @if($event->location)
                        <div class="px-3 py-1 d-flex justify-items-center">
                            <h6 class="mb-1 fw-bold">{{_trans('keyword.Location')}}: </h6>
                            <span class="ms-1">{!! $event->location !!}</span>
                        </div>
                    @endif
                    @if($event->event_url)
                        <div class="px-3 py-1 d-flex justify-items-center">
                            <h6 class="mb-1 fw-bold">{{ _trans('keyword.Event Url') }}: </h6>
                            <span class="ms-1">
                                <a href="{{ $event->event_url }}" target="_blank">{{ $event->event_url }}</a>
                            </span>
                        </div>

                    @endif

                    @if($event->file)
                        <div class="px-3 py-1 d-flex justify-items-center">
                            <h6 class="mb-1 fw-bold">{{_trans('keyword.File / Image')}}: </h6>
                            <div class="ms-1" style="min-width: 200px;"> {!!$event->file!!}</div>
                        </div>
                    @endif
                </div>

                <div class="card invoice-preview-card mt-2">
                    <div class="p-sm-3 p-0">
                        <h6 class="mb-1 fw-bold">{{_trans('keyword.Select').' '._trans('keyword.Audience')}}: </h6>
                        <p class="text-danger">Note: First select audience type and then assign new audience</p>
                    </div>
                    <hr class="m-0">

                    <div class="row p-sm-3 p-0">
                        <!-- First Dropdown -->
                        <div class="col-md-6 ecommerce-select2-dropdown">
                            <label for="typeList" class="form-label mb-1 d-flex justify-content-between align-items-center">
                                <span>{{_trans('keyword.Select').' '._trans('keyword.Type')}}</span>
                            </label>
                            <select id="typeList" name="typeList" class="select2 form-select"
                            data-placeholder="Select Type">
                            <option value="" selected disabled>{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        </div>
                        <!-- Second Dropdown -->
                        <div class="col-md-6 ecommerce-select2-dropdown">
                            <label for="emailList"
                                class="form-label mb-1 d-flex justify-content-between align-items-center">
                                <span>{{_trans('keyword.Audience').' '._trans('keyword.List')}}</span>
                            </label>
                            <select id="emailList" name="emailList" class="select2 form-select"
                                data-placeholder="Select Audience" style="display:none;">
                                <option value="" selected disabled>{{_trans('keyword.Select').' '._trans('keyword.Email')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Selected Emails -->
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title m-0">{{_trans('keyword.Selected Audience')}}</h6>
                    </div>
                    <hr class="m-0">
                    <div class="card-body" id="selectedEmails">
                        @foreach ($users as $user)
                            <div class="form-check mb-2 email-item" data-email="{{ $user->email }}">
                                <input class="form-check-input" type="checkbox" id="emailCheck_{{ $user->email }}"
                                       value="{{ $user->id }}" checked>
                                <label class="form-check-label"
                                       for="emailCheck_{{ $user->email }}">{{ $user->name . '('.$user->email.')' }}</label>
                            </div>
                        @endforeach

                        <p id="noEmailsSelectedText" class="text-muted"
                           @if (count($users) > 0) style="display:none;" @endif>
                            <span class="text-danger">{{ _trans('keyword.No Audience Selected') }}.</span>
                        </p>
                    </div>
                </div>
                <button id="sendButton" type="submit" class="btn btn-primary data-submit waves-effect waves-light">{{_trans('keyword.Save')}}</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            updateSelectedEmailsUI()
            function addOrRemoveEmail(user, action) {
                let selectedEmails = $('#selectedEmails');
                let existingEmails = selectedEmails.find('.email-item').map(function() {
                    return $(this).data('email');
                }).get();

                let displayText;
                let email;
                let userId;

                if (typeof user === 'object' && user.email && user.id) {
                    userId = user.id;
                    email = user.email;
                    displayText = user.name ? user.name + ' (' + email + ')' : email;
                } else {
                    email = user;
                    displayText = email;
                    userId = email;
                }

                if (action === 'add') {
                    if (existingEmails.includes(email)) {
                        return; // If the email already exists, do nothing
                    }
                    selectedEmails.append('<div class="form-check mb-2 email-item" data-email="' + email +
                        '">' +
                        '<input class="form-check-input" type="checkbox" id="emailCheck_' + email +
                        '" value="' + userId + '" checked>' +
                        '<label class="form-check-label" for="emailCheck_' + email + '">' + displayText + '</label>' +
                        '</div>');
                } else if (action === 'remove') {
                    selectedEmails.find('.email-item[data-email="' + email + '"]').remove();
                }

                updateSelectedEmailsUI();
            }

            function updateSelectedEmailsUI() {
                let selectedEmails = $('#selectedEmails');
                let emailItems = selectedEmails.find('.email-item');
                let noEmailsSelectedText = $('#noEmailsSelectedText');
                let sendButton = $('#sendButton');

                if (emailItems.length > 0) {
                    noEmailsSelectedText.hide();
                    sendButton.prop('disabled', false);
                } else {
                    noEmailsSelectedText.show();
                    // sendButton.prop('disabled', true);
                }
            }

            $('#typeList').on('change', function() {
                let type = $(this).val();
                if (type) {
                    $.ajax({
                        url: "{{ route('event-management.event.get-users-by-type') }}",
                        method: 'GET',
                        data: {
                            type: type
                        },
                        success: function(response) {
                            let emailList = $('#emailList');
                            emailList.empty();
                            emailList.append(
                                '<option value="" selected disabled>Select Email</option>');

                            emailList.append('<option value="all">Select All</option>');

                            response.forEach(function(user) {
                                emailList.append('<option value="' + user['id'] + '" ' +
                                    'data-name="' + user['name'] + '" ' +
                                    'data-email="' + user['email'] + '">' +
                                    user['name'] + ' (' + user['email'] + ')</option>');
                            });

                            emailList.show();
                        }
                    });
                }
            });

            $('#emailList').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                let selectedId = selectedOption.val();
                let selectedName = selectedOption.data('name');
                let selectedEmail = selectedOption.data('email');


                if (selectedId === 'all') {
                    let type = $('#typeList').val();
                    if (type) {
                        $.ajax({
                            url: "{{ route('event-management.event.get-users-by-type') }}",
                            method: 'GET',
                            data: {
                                type: type
                            },
                            success: function(response) {
                                response.forEach(function(user) {
                                    addOrRemoveEmail({
                                        id: user['id'],
                                        name: user['name'],
                                        email: user['email']
                                    }, 'add');
                                });
                            }
                        });
                    }
                } else if (selectedId) {
                    // Retrieve name and email from data attributes
                    addOrRemoveEmail({
                        id: selectedId,
                        name: selectedName,
                        email: selectedEmail
                    }, 'add');
                }
            });

            $('#selectedEmails').on('change', 'input[type="checkbox"]', function() {
                let email = $(this).closest('.email-item').data('email');
                if (!this.checked) {
                    addOrRemoveEmail(email, 'remove');
                } else {
                    addOrRemoveEmail(email, 'add');
                }
            });

            $('#sendButton').on('click', function(e) {
                e.preventDefault();
                // Collect selected user IDs
                let selectedUserIds = $('#selectedEmails').find('.email-item').map(function() {
                    return $(this).find('input[type="checkbox"]').val();
                }).get();

                $.ajax({
                    url: "{{ route('event-management.event.update-event', ['id' => $event->id]) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        users: selectedUserIds
                    },
                    success: function (response) {
                        if (response.status == 200) {
                            window.location.href = "{{ route('event-management.event.index') }}";
                            toastr.success('User Assigned Successfully');
                        }else{
                            toastr.error('Something Went Wrong!');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error ', error);
                    }
                });
            });

        });
    </script>
@endpush
