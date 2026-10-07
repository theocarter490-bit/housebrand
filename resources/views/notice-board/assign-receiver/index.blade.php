@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Assign Recievers'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Assign Recievers'), [
            '#' => _trans('keyword.Notice Management'),
            'Assign User Emails' => _trans('keyword.Assign Recievers'),
        ]) !!}
        <div class="row invoice-preview">
            <!-- Invoice -->
            <div class="col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">

                    <div class="p-3 ">
                        <h6 class="mb-1 fw-bold">{{ _trans('keyword.Title') }}: </h6>
                        {{ $notice->title }}
                    </div>

                    <div class="p-3">
                        <h6 class="mb-1 fw-bold">{{ _trans('keyword.Description') }}: </h6>
                        {!! $notice->description !!}
                    </div>

                    <div class="p-3 mb-3">
                        <h6 class="mb-1 fw-bold">{{ _trans('keyword.File/Attachment') }}: </h6>

                        {!! getFileElement(getFilePath($notice->attachment)) !!}

                    </div>
                </div>
                {{--  --}}
                <div class="card invoice-preview-card mt-2">
                    <div class="p-3 ">
                        <h6 class="mb-1 fw-bold">{{ _trans('keyword.Select') . ' ' . _trans('keyword.Receiver') }}: </h6>
                    </div>
                    <hr class="m-0">

                    <div class="row p-3">
                        <!-- First Dropdown -->
                        <div class="col-md-6 ecommerce-select2-dropdown">
                            <label for="typeList" class="form-label mb-1 d-flex justify-content-between align-items-center">
                                <span>Select Type</span>
                            </label>
                            <select id="typeList" name="typeList" class="select2 form-select"
                                data-placeholder="Select Type">
                                <option value="" selected disabled>Select Type</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 ecommerce-select2-dropdown">
                            <label for="emailList"
                                class="form-label mb-1 d-flex justify-content-between align-items-center">
                                <span>Select Receiver</span>
                            </label>
                            <select id="emailList" name="emailList" class="select2 form-select"
                                data-placeholder="Select receiver" style="display:none;">
                                <option value="" selected disabled>Select Receiver</option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="d-flex justify-content-center mt-3">
                    <button id="sendButton" type="submit" class="btn btn-primary data-submit waves-effect waves-light"
                            @if (count($formattedUsers) === 0) disabled @endif>
                        {{ _trans('keyword.Save') }}
                        <span class="loader"></span>
                    </button>
                </div>
            </div>
            <!-- Selected users -->
            <!-- Selected users -->
            <div class="col-12 col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title m-0">{{ _trans('keyword.Selected Receivers') }}</h6>
                    </div>
                    <hr class="m-0">
                    <div class="card-body" id="selectedEmails">
                        @foreach ($formattedUsers as $user)
                            <div class="form-check mb-2 email-item" data-id="{{ $user['id'] }}">
                                <input class="form-check-input" type="checkbox" id="emailCheck_{{ $user['id'] }}"
                                    value="{{ $user['id'] }}" checked>
                                <label class="form-check-label" for="emailCheck_{{ $user['id'] }}">
                                    {{ $user['label'] }}
                                </label>
                            </div>
                        @endforeach
                        <p id="noEmailsSelectedText" class="text-muted"
                            @if (count($formattedUsers) > 0) style="display:none;" @endif>
                            <span class="text-danger">{{ _trans('keyword.No Receivers are selected') }}.</span>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            function addOrRemoveEmail(user, action) {
                let selectedEmails = $('#selectedEmails');
                let existingEmails = selectedEmails.find('.email-item').map(function () {
                    return String($(this).data('id'));
                }).get();

                if (action === 'add') {
                    if (existingEmails.includes(String(user.id))) {
                        return;
                    }
                    selectedEmails.append('<div class="form-check mb-2 email-item" data-id="' + user.id + '">' +
                        '<input class="form-check-input" type="checkbox" id="emailCheck_' + user.id +
                        '" value="' + user.id + '" checked>' +
                        '<label class="form-check-label" for="emailCheck_' + user.id + '">' + user.name + ' (' +
                        user.email + ')</label>' +
                        '</div>');
                } else if (action === 'remove') {
                    selectedEmails.find('.email-item[data-id="' + user.id + '"]').remove();
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
                    sendButton.prop('disabled', true);
                }
            }

            $('#typeList').on('change', function() {
                let type = $(this).val();
                if (type) {
                    $.ajax({
                        url: "{{ route('notice-board.notice.get-users-by-type') }}",
                        method: 'GET',
                        data: {
                            type: type
                        },
                        success: function(response) {
                            let emailList = $('#emailList');
                            emailList.empty().append(
                                '<option value="" selected disabled>Select Email</option>');
                            if (response.length > 0) {
                                emailList.append('<option value="all">Select All</option>');
                            }
                            response.forEach(function(user) {
                                emailList.append('<option value="' + user.id + '">' +
                                    user.label + '</option>');
                            });
                            emailList.show();
                        }
                    });
                }
            });

            $('#emailList').on('change', function() {
                let selectedEmail = $(this).val();
                let selectedEmailText = $('#emailList option:selected').text();

                if (selectedEmail === 'all') {
                    let type = $('#typeList').val();
                    if (type) {
                        $.ajax({
                            url: "{{ route('notice-board.notice.get-users-by-type') }}",
                            method: 'GET',
                            data: {
                                type: type
                            },
                            success: function(response) {
                                response.forEach(function(user) {
                                    let userData = {
                                        id: user.id,
                                        name: user.label.split(' (')[0].trim(),
                                        email: user.email
                                    };
                                    addOrRemoveEmail(userData, 'add');
                                });
                            }
                        });
                    }
                } else if (selectedEmail) {
                    console.log("i am here")

                    let user = {
                        id: selectedEmail,
                        name: selectedEmailText.split('(')[0].trim(),
                        email: selectedEmailText.match(/\(([^)]+)\)/)[1]
                    };
                    addOrRemoveEmail(user, 'add');
                }
            });


            $('#selectedEmails').on('change', 'input[type="checkbox"]', function() {
                let email = $(this).val();
                let selectedEmailText = $(this).next('label').text();
                let emailData = {
                    id: email,
                    name: selectedEmailText.split('(')[0].trim(),
                    email: selectedEmailText.match(/\(([^)]+)\)/)[1]
                };

                if (!this.checked) {
                    addOrRemoveEmail(emailData, 'remove');
                } else {
                    addOrRemoveEmail(emailData, 'add');
                }
            });

            $('#sendButton').on('click', function(e) {
                loader.show();
                submitButton.prop('disabled', true);
                e.preventDefault();
                let selectedUsers = $('#selectedEmails').find('.email-item').map(function() {
                    return $(this).data('id');
                }).get();

                $.ajax({
                    url: "{{ route('notice-board.notice.updateReceivers', ['id' => $notice->id]) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        receivers: selectedUsers
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        window.location.href = "{{ route('notice-board.notice.index') }}";
                    },
                    error: function(xhr, status, error) {
                        console.error('Error Assigning Users:', error);
                    },complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });

            updateSelectedEmailsUI();

        });
    </script>
@endpush
