@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Payment') . ' ' . _trans('keyword.Method'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Payment') . ' ' . _trans('keyword.Method'), [
            '#' => _trans('keyword.Payment') . ' ' . _trans('keyword.Settings'),
            'setting/payment-method' => _trans('keyword.Payment') . ' ' . _trans('keyword.Method'),
        ]) !!}
        <div class="app-ecommerce-category">

            <div class="row">
                @if(hasPermission('payment_method_create'))
                    <div class="mb-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addPaymentMethod"><i class="ti ti-plus ti-xs me-0 me-sm-2"></i>
                            {{_trans('keyword.Add Payment Method')}}
                        </button>
                    </div>
                @endif

                @foreach ($methods as $method)
                    @if ($method->id == 1 && globalSetting('stripe_payment')->value == 1)
                        <div class="col-md-6 mb-4">
                            <form action="" id="stripe_form">
                                <div class="card ">
                                    <div
                                        class="card-header d-flex flex-xl-row flex-column gap-2 justify-content-between align-items-center">
                                        <h4>{{ $method->name }}</h4>
                                        <img src="{{ asset($method->logo) }}" alt="" width="100px">
                                        <label class="switch switch-square switch-success">
                                            <input type="checkbox" onchange="changePublishStatus({{ $method->id }})"
                                                   class="switch-input "
                                                {{ optional(Auth()->user()->paymentMethodStatus->where('payment_method_id', $method->id)->first())->active_status == 1? 'checked': '' }} />
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on">
                                                    <i class="ti ti-check"></i>
                                                </span>
                                                <span class="switch-off">
                                                    <i class="ti ti-x"></i>
                                                </span>
                                            </span>
                                            <span class="switch-label">{{ _trans('keyword.Enable') }}</span>
                                        </label>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12 mb-3">
                                            <label class="form-label"
                                                   for="public_key">{{ _trans('keyword.Public Key') }}</label>
                                            <input type="text" class="form-control" id="public_key" placeholder="key"
                                                   value="{{ optional(Auth()->user()->gatewayCredentials->where('payment_method_id', $method->id)->where('key', 'public_key')->first())->value }}"
                                                   name="public_key" aria-label="Public Key"/>
                                            <span class="text-danger public_keyError error"></span>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label"
                                                   for="secret_key">{{ _trans('keyword.Secret Key') }}</label>
                                            <input type="text" class="form-control" id="secret_key"
                                                   placeholder="Secret Key"
                                                   value="{{ optional(Auth()->user()->gatewayCredentials->where('payment_method_id', $method->id)->where('key', 'secret_key')->first())->value }}"
                                                   name="secret_key" aria-label="Secret Key"/>
                                            <span class="text-danger secret_keyError error"></span>
                                        </div>
                                        @if (getUserId() == 1)
                                            <div class="col-12 mb-3">
                                                <label class="form-label"
                                                       for="secret_key">{{ _trans('keyword.Webhook Key') }}</label>
                                                <input type="text" class="form-control" id="webhook_key"
                                                       placeholder="Webhook Key"
                                                       value="{{ optional(Auth()->user()->gatewayCredentials->where('payment_method_id', $method->id)->where('key', 'webhook_key')->first())->value }}"
                                                       name="webhook_key" aria-label="WebhookKey"/>
                                                <span class="text-danger webhook_keyError error"></span>
                                            </div>
                                        @endif
                                    </div>
                                    @if(hasPermission('payment_method_credentials_update'))
                                        <div class="card-footer d-flex justify-content-between align-items-center">
                                            <button class="btn btn-primary"
                                                    id="paypal_submit">{{ _trans('keyword.Save') }}</button>
                                        </div>
                                    @endif

                                </div>
                            </form>
                        </div>
                    @elseif ($method->id == 2 && @globalSetting('paypal_payment')->value == 1)
                        <div class="col-md-6 mb-4">
                            <form action="" id="paypal_form" class="h-100">
                                <div class="card  h-100">
                                    <div
                                        class="card-header d-flex justify-content-between gap-2 align-items-center flex-xl-row flex-column">

                                        <h4>{{ $method->name }}</h4>
                                        <img src="{{ asset('assets/img/payment-method/paypal.png') }}" alt=""
                                             width="100px" height="30px">
                                        <label class="switch switch-square switch-success">
                                            <input type="checkbox" class="switch-input"
                                                   onchange="changePublishStatus({{ $method->id }})"
                                                {{ optional(Auth()->user()->paymentMethodStatus->where('payment_method_id', $method->id)->first())->active_status == 1? 'checked': '' }} />
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on">
                                                    <i class="ti ti-check"></i>
                                                </span>
                                                <span class="switch-off">
                                                    <i class="ti ti-x"></i>
                                                </span>
                                            </span>
                                            <span class="switch-label">{{ _trans('keyword.Enable') }}</span>
                                        </label>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="form-label"
                                                           for="client_id">{{ _trans('keyword.Client ID') }}</label>
                                                    <input type="text" class="form-control" id="client_id"
                                                           value="{{ optional(Auth()->user()->gatewayCredentials->where('payment_method_id', $method->id)->where('key', 'client_id')->first())->value }}"
                                                           placeholder="Client ID" name="client_id"
                                                           aria-label="Client ID"/>
                                                    <span class="text-danger client_idError error"></span>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="form-label"
                                                           for="client_secret">{{ _trans('keyword.Client Secret') }}</label>
                                                    <input type="text" class="form-control" id="client_secret"
                                                           value="{{ optional(Auth()->user()->gatewayCredentials->where('payment_method_id', $method->id)->where('key', 'client_secret')->first())->value }}"
                                                           placeholder="{{ _trans('keyword.Client Secret') }}"
                                                           name="client_secret"
                                                           aria-label="{{ _trans('keyword.Client Secret') }}"/>
                                                    <span class="text-danger client_secretError error"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="demo-vertical-spacing">
                                                    <label class="switch">
                                                        <input type="checkbox" id="paypal_mode_input"
                                                               {{ optional(Auth()->user()->gatewayCredentials->where('payment_method_id', $method->id)->where('key', 'mode')->first())->value == 1? 'checked': '' }}
                                                               class="switch-input is-valid"/>
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"></span>
                                                            <span class="switch-off"></span>
                                                        </span>
                                                        <span
                                                            class="switch-label">{{ _trans('keyword.Live Mode') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(hasPermission('payment_method_credentials_update'))
                                        <div class="card-footer d-flex justify-content-between align-items-center">
                                            <button class="btn btn-primary"
                                                    id="paypal_submit">{{ _trans('keyword.Save') }}</button>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @elseif (@globalSetting('cash_payment')->value == 1)
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between">
                                    <h4>{{ $method->name }}</h4>
                                    <img
                                        src="{{ $method->type == 1 ? getFilePath($method->logo) : asset('assets/img/payment-method/cash.png') }}"
                                        alt="" width="100px" height="30px">
                                    <label class="switch switch-square switch-success">
                                        <input type="checkbox" class="switch-input"
                                               onchange="changePublishStatus({{ $method->id }})"
                                            {{ optional(Auth()->user()->paymentMethodStatus->where('payment_method_id', $method->id)->first())->active_status == 1? 'checked': '' }} />

                                        <span class="switch-toggle-slider">
                                            <span class="switch-on">
                                                <i class="ti ti-check"></i>
                                            </span>
                                            <span class="switch-off">
                                                <i class="ti ti-x"></i>
                                            </span>
                                        </span>
                                        <span class="switch-label">{{ _trans('keyword.Enable') }}</span>
                                        @if ($method->type == 1)
                                            <div class="d-flex justify-content-end mt-2">

                                                @if(hasPermission('payment_method_update'))
                                                    <a href="#" class="edit_method text-primary me-3"
                                                       data-id="{{ $method->id }}" title="{{ _trans('keyword.Edit') }}">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                @endif

                                                @if(hasPermission('payment_method_delete'))
                                                    <a id="delete-method" href="#" data-id="{{ $method->id }}"
                                                       class="text-danger" title="{{ _trans('keyword.Delete') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </label>

                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
    </div>

    <!-- Add Modal -->
    <div id="addPaymentMethod" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">Add New Payment Method</h3>
                    </div>
                    <form class="row g-3" action="{{route('setting.payment-method.store')}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="col-12 mb-2">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Title" tabindex="-1" required/>
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control"
                                   placeholder="Title" tabindex="-1" required/>
                            @error('logo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit
                                <span class="loader"></span>
                            </button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Modal -->

    <!-- Edit Modal -->
    <div id="editPaymentMethod" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">Edit Payment Method</h3>
                    </div>
                    <form class="row g-3" action="{{route('setting.payment-method.update')}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="col-12 mb-2">
                            <label class="form-label">Name</label>
                            <input type="text" id="edit_name" name="name" class="form-control"
                                   placeholder="Title" tabindex="-1" required/>
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <input type="hidden" value="" id="payment_method_id" name="payment_method_id">
                        <div class="col-12 mb-2">
                            <label class="form-label">Logo</label>
                            <input type="file" id="logo" name="logo" class="form-control"
                                   placeholder="Title" tabindex="-1"/>
                            @error('logo')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Current Logo</label>
                            <img class="d-block mt-2" style="max-height: 50px; max-width: 50px" id="current_logo" src=""
                                 alt="Logo">
                        </div>
                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit
                                <span class="loader"></span>
                            </button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->

    <!-- Password Confirmation Modal -->
    <div class="modal fade" id="passwordConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div
                            class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-primary mb-3"
                            style="width: 64px; height: 64px; background-color: rgba(13, 110, 253, 0.1);">
                            <i class="fa fa-shield-halved text-primary fs-2">🔒</i>
                        </div>
                        <h5 class="fw-bold">Identity Verification</h5>
                        <p class="text-muted small px-2">
                            <strong>Security Note:</strong> You are modifying critical system settings of Payments Gateway.
                            To prevent unauthorized changes to your communication gateway, please re-enter your password
                            to authorize this update.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Account Password</label>
                        <input type="password" id="confirmation_password"
                               class="form-control form-control-lg border-primary-subtle"
                               placeholder="Enter your password">
                        <div id="password_error" class="text-danger small mt-2 fw-medium"></div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" id="confirm_password_btn" class="btn btn-primary btn-lg py-2">
                            <span class="btn-text">Confirm & Save Changes</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                        <button type="button" class="btn btn-link text-decoration-none text-muted btn-sm mt-1"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Password Confirmation Modal -->

@endsection

@push('scripts')
    <script>
        // Global variables to store pending action details
        let pendingAction = null;
        let pendingActionData = null;

        // Function to verify password
        function verifyPassword(password, callback) {
            const formData = new FormData();
            formData.append('password', password);
            formData.append('_token', "{{ csrf_token() }}");

            // Show loading spinner
            $('#confirm_password_btn .btn-text').addClass('d-none');
            $('#confirm_password_btn .spinner-border').removeClass('d-none');
            $('#confirm_password_btn').prop('disabled', true);

            $.ajax({
                url: '{{ route("setting.payment-method.verifyPassword") }}', // You need to create this route
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // Hide loading spinner
                    $('#confirm_password_btn .btn-text').removeClass('d-none');
                    $('#confirm_password_btn .spinner-border').addClass('d-none');
                    $('#confirm_password_btn').prop('disabled', false);

                    if (response.status === 200) {
                        $('#password_error').text('');
                        $('#confirmation_password').val('');
                        $('#passwordConfirmModal').modal('hide');
                        callback(true);
                    } else {
                        $('#password_error').text(response.message || 'Invalid password');
                        callback(false);
                    }
                },
                error: function (error) {
                    // Hide loading spinner
                    $('#confirm_password_btn .btn-text').removeClass('d-none');
                    $('#confirm_password_btn .spinner-border').addClass('d-none');
                    $('#confirm_password_btn').prop('disabled', false);

                    $('#password_error').text(error.responseJSON?.message || 'Invalid password');
                    callback(false);
                }
            });
        }

        // Handle password confirmation button click
        $(document).on('click', '#confirm_password_btn', function () {
            const password = $('#confirmation_password').val();

            if (!password) {
                $('#password_error').text('Password is required');
                return;
            }

            verifyPassword(password, function (isVerified) {
                if (isVerified && pendingAction) {
                    pendingAction(pendingActionData);
                    pendingAction = null;
                    pendingActionData = null;
                }
            });
        });

        // Clear error when typing
        $(document).on('input', '#confirmation_password', function () {
            $('#password_error').text('');
        });

        // Clear password field when modal is closed
        $('#passwordConfirmModal').on('hidden.bs.modal', function () {
            $('#confirmation_password').val('');
            $('#password_error').text('');
            pendingAction = null;
            pendingActionData = null;
        });

        // Stripe form submission with password confirmation
        $('#stripe_form').on('submit', function (e) {
            e.preventDefault();

            // Store the action and data
            pendingAction = executeStripeSubmit;
            pendingActionData = null;

            // Show password modal
            $('#passwordConfirmModal').modal('show');
        });

        // Execute Stripe submission after password verification
        function executeStripeSubmit() {
            var formData = new FormData();
            let public_key = $("#public_key").val();
            let secret_key = $("#secret_key").val();
            let webhook_key = $("#webhook_key").val();

            formData.append('public_key', public_key);
            formData.append('secret_key', secret_key);
            formData.append('webhook_key', webhook_key);
            formData.append('_token', "{{ csrf_token() }}");

            $('.error').text('');
            $.ajax({
                url: '{{ route('setting.payment-method.stripeCredentialStore') }}',
                type: 'POST',
                contentType: 'multipart/form-data',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response.status == 403) {
                        $('.public_keyError').text(response.errors?.public_key ? response.errors
                            ?.public_key[0] : '');
                        $('.secret_keyError').text(response.errors?.secret_key ? response.errors
                            ?.secret_key[0] : '');
                        $('.webhook_keyError').text(response.errors?.webhook_key ? response.errors
                            ?.webhook_key[0] : '');
                    } else if (response.status == 200) {
                        toastr.success(response.message);
                    }
                },
                error: function (error) {
                    toastr.error(error.responseJSON.message);
                }
            });
        }

        // PayPal form submission with password confirmation
        $('#paypal_form').on('submit', function (e) {
            e.preventDefault();

            // Store the action and data
            pendingAction = executePaypalSubmit;
            pendingActionData = null;

            // Show password modal
            $('#passwordConfirmModal').modal('show');
        });

        // Execute PayPal submission after password verification
        function executePaypalSubmit() {
            var formData = new FormData();
            let client_id = $("#client_id").val();
            let client_secret = $("#client_secret").val();
            let paypal_mode_input = $("#paypal_mode_input").is(":checked");

            formData.append('client_id', client_id);
            formData.append('client_secret', client_secret);
            formData.append('paypal_mode_input', paypal_mode_input);
            formData.append('_token', "{{ csrf_token() }}");

            $('.error').text('');
            $.ajax({
                url: '{{ route('setting.payment-method.paypalCredentialStore') }}',
                type: 'POST',
                contentType: 'multipart/form-data',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response.status == 403) {
                        $('.client_secretError').text(response.errors?.client_secret ? response.errors
                            ?.client_secret[0] : '');
                        $('.client_idError').text(response.errors?.client_id ? response.errors
                            ?.client_id[0] : '');
                    } else if (response.status == 200) {
                        toastr.success(response.message);
                    }
                },
                error: function (error) {
                    toastr.error(error.responseJSON.message);
                }
            });
        }

        // Modified changePublishStatus to require password
        function changePublishStatus(id) {
            // Store the action and data
            pendingAction = executeStatusChange;
            pendingActionData = id;

            // Show password modal
            $('#passwordConfirmModal').modal('show');
        }

        // Execute status change after password verification
        function executeStatusChange(id) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('_token', "{{ csrf_token() }}");

            Swal.fire({
                title: 'Are you sure?',
                text: "To change the status of this payment gateway.",
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
                        url: '{{ route('setting.payment-method.changePublishStatus') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 200) {
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function (error) {
                            console.error(error);
                            toastr.error(error.responseJSON.message);
                        }
                    });
                } else {
                    location.reload();
                }
            });
        }

        $(document).ready(function () {
            // Modified delete to require password
            $(document).on('click', '#delete-method', function (e) {
                e.preventDefault();

                var paymentMethodId = $(this).data('id');

                // Store the action and data
                pendingAction = executeDelete;
                pendingActionData = paymentMethodId;

                // Show password modal
                $('#passwordConfirmModal').modal('show');
            });

            // Execute delete after password verification
            window.executeDelete = function (paymentMethodId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "To delete this payment method.",
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
                            url: '/setting/payment-method/delete/' + paymentMethodId,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    location.reload();
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function (xhr) {
                                alert("Error: " + xhr.responseJSON.message);
                            }
                        });
                    }
                });
            };
        });

        $(document).on('click', '.edit_method', function (e) {
            e.preventDefault();

            var paymentMethodId = $(this).data('id');

            $.ajax({
                url: '/setting/payment-method/edit/' + paymentMethodId,
                type: 'GET',
                success: function (response) {
                    $('#edit_name').val(response.name);
                    $('#current_logo').attr('src', (response.logo));
                    $('#payment_method_id').val(response.id);
                    var editPaymentMethodModal = new bootstrap.Modal(document.getElementById(
                        'editPaymentMethod'));
                    editPaymentMethodModal.show();
                },
                error: function (xhr) {
                    alert("Error: " + xhr.responseJSON.message);
                }
            });
        });
    </script>
@endpush
