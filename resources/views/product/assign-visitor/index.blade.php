@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Assign User'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Assign Visitors'), [
                    '#' => _trans('keyword.Product') . ' ' . _trans('keyword.Management'),
                    'product/index' => 'Products',
                    'products' => _trans('keyword.Assign Visitors'),
                ]) !!}

        <div class="row g-4">
            <div class="col-12 col-lg-8">

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <div class="rounded bg-lighter d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                <img src="{{ getFilePath($product->thumbnail_img) }}" alt="product-image" class="d-block rounded" style="width: 100%; height: 100%; object-fit: contain;">
                            </div>
                            <div class="button-wrapper">
                                <h5 class="mb-2 fw-bold text-heading">{{ $product->name }}</h5>
                                <div class="mb-2 ">
                                    <span class="badge bg-label-secondary me-2">{{ $product->category->name }}</span>
                                    <span class="badge mt-sm-0 mt-1 bg-label-{{ $product->is_published == 1 ? 'success' : 'danger' }}">
                                        {{ $product->is_published == 1 ? 'Published' : 'Unpublished' }}
                                    </span>
                                </div>
                                <h6 class="mb-0 text-primary">{{ getPriceFormat($product->unit_price) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-bottom">
                        <h6 class="card-title m-0 fw-bold">{{_trans('keyword.Select Visitors')}}</h6>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 ecommerce-select2-dropdown">
                                <label for="typeList" class="form-label mb-1">{{_trans('keyword.Select Type')}}</label>
                                <select id="typeList" name="typeList" class="select2 form-select" data-placeholder="{{_trans('keyword.Select Type')}}">
                                    <option value="" selected disabled>{{_trans('keyword.Select Type')}}</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 ecommerce-select2-dropdown">
                                <label for="emailList" class="form-label mb-1">{{_trans('keyword.Audience List')}}</label>
                                <select id="emailList" name="emailList" class="select2 form-select" data-placeholder="{{_trans('keyword.Select Audience')}}">
                                    <option value="" selected disabled>{{_trans('keyword.Select Email')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card mb-4 h-100">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                        <h6 class="card-title m-0 fw-bold">{{_trans('keyword.Selected Visitors')}}</h6>
                        <span class="badge bg-label-primary" id="countBadge">0</span>
                    </div>
                    <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                        <div id="selectedEmails" class="p-3">
                            @foreach ($visitors as $user)
                                <div class="form-check d-flex align-items-center mb-3 gap-2  email-item bg-lighter p-2 rounded" data-email="{{ $user->email }}">
                                    <input class="form-check-input mt-0 float-none m-0 " type="checkbox" id="emailCheck_{{ $user->email }}" value="{{ $user->id }}" checked style="width: 1.2em; height: 1.2em;">
                                    <label class="form-check-label w-100 cursor-pointer" for="emailCheck_{{ $user->email }}">
                                        <span class="d-block fw-semibold text-heading">{{ $user->name }}</span>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </label>
                                </div>
                            @endforeach

                            <div id="noEmailsSelectedText" class="text-center py-4 @if(count($visitors) > 0) d-none @endif">
                                <div class="text-muted mb-2"><i class="ti ti-users fs-2"></i></div>
                                <span class="text-muted">{{ _trans('keyword.No Visitors Selected') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top bg-body pt-3">
                        <button id="sendButton" type="submit" class="btn btn-primary w-100 waves-effect waves-light">{{_trans('keyword.Save Changes')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            // Initialize UI on load
            updateSelectedEmailsUI()

            // Function to add or remove email from the DOM
            function addOrRemoveEmail(user, action) {
                let selectedEmails = $('#selectedEmails');
                let existingEmails = selectedEmails.find('.email-item').map(function() {
                    return $(this).data('email');
                }).get();

                let displayTextName;
                let displayTextEmail;
                let email;
                let userId;

                // Handle both object input and direct email string input (legacy support)
                if (typeof user === 'object' && user.email && user.id) {
                    userId = user.id;
                    email = user.email;
                    displayTextName = user.name ? user.name : 'Unknown';
                    displayTextEmail = email;
                } else {
                    email = user;
                    displayTextName = email;
                    displayTextEmail = '';
                    userId = email;
                }

                if (action === 'add') {
                    if (existingEmails.includes(email)) {
                        return; // Prevent duplicates
                    }
                    let newItem = `
                        <div class="form-check d-flex align-items-center mb-3 email-item bg-lighter p-2 gap-2 rounded" data-email="${email}">
                            <input class="form-check-input mt-0 float-none m-0" type="checkbox" id="emailCheck_${email}" value="${userId}" checked style="width: 1.2em; height: 1.2em;">
                            <label class="form-check-label w-100 cursor-pointer" for="emailCheck_${email}">
                                <span class="d-block fw-semibold text-heading">${displayTextName}</span>
                                <small class="text-muted">${displayTextEmail}</small>
                            </label>
                        </div>
                    `;
                    selectedEmails.append(newItem);
                } else if (action === 'remove') {
                    // Fade out animation before removing
                    selectedEmails.find('.email-item[data-email="' + email + '"]').fadeOut(300, function() {
                        $(this).remove();
                        updateSelectedEmailsUI();
                    });
                }

                updateSelectedEmailsUI();
            }

            // Update Badge Count and Empty State
            function updateSelectedEmailsUI() {
                let selectedEmails = $('#selectedEmails');
                let emailItems = selectedEmails.find('.email-item');
                let noEmailsSelectedText = $('#noEmailsSelectedText');
                let sendButton = $('#sendButton');
                let countBadge = $('#countBadge');

                // Update count
                countBadge.text(emailItems.length);

                // Show/Hide empty state
                if (emailItems.length > 0) {
                    noEmailsSelectedText.addClass('d-none');
                    sendButton.prop('disabled', false);
                } else {
                    noEmailsSelectedText.removeClass('d-none');
                    // sendButton.prop('disabled', true); // Optional: keep enabled if you want to allow clearing
                }
            }

            // 1. Handle User Type Change
            $('#typeList').on('change', function() {
                let type = $(this).val();
                let designerId = `{{$product->user_id}}`
                if (type) {
                    $.ajax({
                        url: "{{ route('product.get-users-by-type') }}",
                        method: 'GET',
                        data: {
                            type: type,
                            designer_id:designerId
                        },
                        success: function(response) {
                            let emailList = $('#emailList');
                            emailList.empty();
                            emailList.append(
                                '<option value="" selected disabled>{{_trans("keyword.Select Email")}}</option>');

                            emailList.append('<option value="all">{{_trans("keyword.Select All")}}</option>');

                            response.forEach(function(user) {
                                emailList.append('<option value="' + user['id'] + '" ' +
                                    'data-name="' + user['name'] + '" ' +
                                    'data-email="' + user['email'] + '">' +
                                    user['name'] + ' (' + user['email'] + ')</option>');
                            });
                        }
                    });
                }
            });


            // 2. Handle Audience/Email Selection
            $('#emailList').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                let selectedId = selectedOption.val();
                let selectedName = selectedOption.data('name');
                let selectedEmail = selectedOption.data('email');

                if (selectedId === 'all') {
                    let type = $('#typeList').val();
                    if (type) {
                        $.ajax({
                            url: "{{ route('product.get-users-by-type') }}",
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
                    addOrRemoveEmail({
                        id: selectedId,
                        name: selectedName,
                        email: selectedEmail
                    }, 'add');
                }

                // Reset dropdown to allow re-selecting the same person if needed (UX preference)
                $(this).val(null).trigger('change.select2');
            });

            // 3. Handle Checkbox Uncheck (Remove)
            $('#selectedEmails').on('change', 'input[type="checkbox"]', function() {
                let email = $(this).closest('.email-item').data('email');
                if (!this.checked) {
                    addOrRemoveEmail(email, 'remove');
                } else {
                    // Re-adding via checkbox is rare but logic exists
                    addOrRemoveEmail(email, 'add');
                }
            });

            // 4. Submit Data
            $('#sendButton').on('click', function(e) {
                e.preventDefault();
                let selectedUserIds = $('#selectedEmails').find('.email-item').map(function() {
                    return $(this).find('input[type="checkbox"]').val();
                }).get();

                $.ajax({
                    url: "{{ route('product.storeVisitor', ['id' => $product->id]) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        users: selectedUserIds
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            window.location.href = "{{ route('product.index') }}";
                        }
                        toastr.success(response.message);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error ', error);
                        toastr.error('Something went wrong');
                    }
                });
            });

        });
    </script>
@endpush
