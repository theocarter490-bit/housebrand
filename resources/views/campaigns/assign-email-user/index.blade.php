@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Assign User Emails'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Assign User Emails'), ['#' => _trans('keyword.Marketing'), 'Assign User Emails' => _trans('keyword.Assign User Emails')]) !!}
        <div class="row invoice-preview">
            <!-- Invoice -->
            <div class="col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">

                    <div class="p-sm-3 p-0">
                        <h6 class="mb-1 fw-bold">{{_trans('keyword.Title')}}: </h6>
                        {{ $campaign->title }}
                    </div>

                    <div class="p-sm-3 p-0">
                        <h6 class="mb-1 fw-bold">{{_trans('keyword.Message')}}: </h6>
                        {!! $campaign->message !!}
                    </div>

                    <div class="p-sm-3 p-0 mb-3">
                        <h6 class="mb-1 fw-bold">{{_trans('keyword.File/Attachment')}}: </h6>

                        {!! getFileElement(getFilePath($campaign->attachment)) !!}

                    </div>
                </div>
                {{--  --}}
                <div class="card invoice-preview-card mt-2">
                    <div class="p-sm-3 p-0">
                        <h6 class="mb-1 fw-bold">{{_trans('keyword.Select').' '._trans('keyword.Email')}}: </h6>
                        <p class="text-danger">Note: First select email type and then assign new email</p>
                    </div>
                    <hr class="m-0">

                    <div class="row p-sm-3 p-0">
                        <!-- First Dropdown -->
                        <div class="col-md-6 ecommerce-select2-dropdown">
                            <label for="typeList"
                                   class="form-label mb-1 d-flex justify-content-between align-items-center">
                                <span>{{_trans('keyword.Select').' '._trans('keyword.Type')}}</span>
                            </label>
                            <select id="typeList" name="typeList" class="select2 form-select"
                                    data-placeholder="Select Category">
                                <option value="" selected
                                        disabled>{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Second Dropdown -->
                        <div class="col-md-6 ecommerce-select2-dropdown">
                            <label for="emailList"
                                   class="form-label mb-1 d-flex justify-content-between align-items-center">
                                <span>{{_trans('keyword.Email').' '._trans('keyword.List')}}</span>
                            </label>
                            <select id="emailList" name="emailList" class="select2 form-select"
                                    data-placeholder="Select Email" style="display:none;">
                                <option value="" selected
                                        disabled>{{_trans('keyword.Select').' '._trans('keyword.Email')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                {{--  --}}
            </div>
            <!-- Selected Emails -->
            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header d-flex flex-row justify-content-between">
                        <h6 class="card-title m-0">{{_trans('keyword.Selected Emails')}}</h6>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalToggle"><i
                                class="ti ti-plus ti-xs me-0 me-sm-2"></i>Add Emails
                        </button>
                    </div>
                    <hr class="m-0">
                    <div class="card-body" style="max-height: 500px; overflow: scroll;" id="selectedEmails">
                        @foreach (json_decode($campaign->emails ?? '[]', true) as $email)
                            <div class="form-check mb-2 email-item" data-email="{{ $email }}">
                                <input class="form-check-input" type="checkbox" id="emailCheck_{{ $email }}"
                                       value="{{ $email }}" checked>
                                <label class="form-check-label"
                                       for="emailCheck_{{ $email }}">{{ $email }}</label>
                            </div>
                        @endforeach
                        <p id="noEmailsSelectedText" class="text-muted"
                           @if (!empty(json_decode($campaign->emails ?? '[]', true))) style="display:none;" @endif>
                            <span class="text-danger">{{_trans('keyword.No emails are selected')}}.</span>
                        </p>
                    </div>
                </div>
                <button id="sendButton" type="submit" class="btn btn-primary data-submit waves-effect waves-light"
                        @if (empty(json_decode($campaign->emails ?? '[]', true))) disabled @endif>{{_trans('keyword.Save')}}
                    <span class="loader"></span>
                </button>
            </div>
        </div>
        <div class="modal fade" id="modalToggle" aria-labelledby="modalToggleLabel" tabindex="-1" style="display: none;"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalToggleLabel">Add Email List</h5>
                        <button type="button" id="emailListModalClosebutton" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <!-- Add Single Email -->
                        <label class="form-label text-primary mb-2">For single email</label>
                        <div class="row g-2 border border-2 border-gray-300 rounded p-3 mb-3 align-items-center">
                            <div class="col-8">

                                <input type="email" class="form-control" placeholder="example@gmail.com"
                                       id="single_email">
                            </div>
                            <div class="col-4 d-grid">
                                <button id="add_single_email" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i> Add
                                </button>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <label class="form-label text-primary mb-1">For bulk upload</label>
                        <div class="border border-2 border-gray-300 rounded p-3">

                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">

                                    <a href="{{ route('marketing.downloadDemoFile') }}" class="btn btn-primary w-100">
                                        <i class="ti ti-download me-1"></i> Demo format
                                    </a>
                                </div>

                                <div class="col-md-7">
                                    <label class="form-label text-primary mb-1">Upload CSV/XLS file here</label>
                                    <input type="file" class="form-control" id="email_list" name="email_list"
                                           accept=".xls,.xlsx,.csv">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col d-grid">
                                    <button class="btn btn-success" id="submitEmailList">Submit</button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        $(function () {

            function addOrRemoveEmail(email, action) {
                let selectedEmails = $('#selectedEmails');
                let existingEmails = selectedEmails.find('.email-item').map(function () {
                    return $(this).data('email');
                }).get();

                if (action === 'add') {
                    if (existingEmails.includes(email)) {
                        return;
                    }
                    selectedEmails.append('<div class="form-check mb-2 email-item" data-email="' + email +
                        '">' +
                        '<input class="form-check-input" type="checkbox" id="emailCheck_' + email +
                        '" value="' + email + '" checked>' +
                        '<label class="form-check-label" for="emailCheck_' + email + '">' + email + '</label>' +
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
                    sendButton.prop('disabled', true);
                }
            }

            $('#typeList').on('change', function () {
                let type = $(this).val();
                if (type) {
                    $.ajax({
                        url: "{{ route('marketing.get-users-by-type') }}",
                        method: 'GET',
                        data: {
                            type: type
                        },
                        success: function (response) {
                            let emailList = $('#emailList');
                            emailList.empty();
                            emailList.append(
                                '<option value="" selected disabled>Select Email</option>');
                            if (type === 'subscribers' || type === 'contact_requests' || type) {
                                emailList.append('<option value="all">Select All</option>');
                            }
                            response.forEach(function (email) {
                                emailList.append('<option value="' + email + '">' +
                                    email + '</option>');
                            });
                            emailList.show();
                        }
                    });
                }
            });

            $('#emailList').on('change', function () {
                let selectedEmail = $(this).val();
                if (selectedEmail === 'all') {
                    let type = $('#typeList').val();
                    if (type) {
                        $.ajax({
                            url: "{{ route('marketing.get-users-by-type') }}",
                            method: 'GET',
                            data: {
                                type: type
                            },
                            success: function (response) {
                                response.forEach(function (email) {
                                    addOrRemoveEmail(email, 'add');
                                });
                            }
                        });
                    }
                } else if (selectedEmail) {
                    addOrRemoveEmail(selectedEmail, 'add');
                }
            });

            $('#selectedEmails').on('change', 'input[type="checkbox"]', function () {
                let email = $(this).val();
                if (!this.checked) {
                    addOrRemoveEmail(email, 'remove');
                } else {
                    addOrRemoveEmail(email, 'add');
                }
            });

            $('#sendButton').on('click', function (e) {
                e.preventDefault();
                // Collect selected emails
                let selectedEmails = $('#selectedEmails').find('.email-item').map(function () {
                    return $(this).data('email');
                }).get();
                // update selected emails for campaign


                loader.show();
                submitButton.prop('disabled', true);

                $.ajax({
                    url: "{{ route('marketing.updateEmailcampaignEmails', ['id' => $campaign->id]) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        emails: selectedEmails
                    },
                    success: function (response) {
                        toastr.success(response.message);

                        window.location.href = "{{ route('marketing.campaign.index') }}";
                    },
                    error: function (xhr, status, error) {
                        console.error('Error launching campaign:', error);
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });

            $(document).ready(function () {
                $("#submitEmailList").on("click", function (e) {
                    e.preventDefault();

                    let list = $('#email_list')[0];
                    let file = list.files[0];
                    if (!file) {
                        toastr.warning('Add a list of email file');
                        return;
                    }

                    let reader = new FileReader();

                    reader.onload = function (e) {
                        let data = new Uint8Array(e.target.result);
                        let workbook = XLSX.read(data, {type: "array"});

                        // Get first sheet
                        let sheetName = workbook.SheetNames[0];
                        let sheet = workbook.Sheets[sheetName];

                        let rows = XLSX.utils.sheet_to_json(sheet, {header: 1});

                        // Loop each row
                        rows.forEach((row, index) => {
                            if (index === 0) {
                                // skip header row
                                return;
                            }
                            console.log("Row " + index, row[0]);
                            if (isValidEmail(row[0])) {
                                addOrRemoveEmail(row[0], 'add');
                            }
                        });
                    };

                    reader.readAsArrayBuffer(file);
                    $('#emailListModalClosebutton').click();
                });

                function isValidEmail(email) {
                    // Basic email regex
                    let re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return re.test(String(email).toLowerCase());
                }

                $('#add_single_email').on('click', function (e) {
                    let singleEmail = $('#single_email').val();
                    if (isValidEmail(singleEmail)) {
                        addOrRemoveEmail(singleEmail, 'add');
                        $('#single_email').val('');
                    }else {
                        toastr.error('Add valid email');
                    }
                });

            });


        });
    </script>
@endpush
