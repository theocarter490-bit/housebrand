@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Email').' '._trans('keyword.Settings'))

@push('styles')
    <style>
        .emailLoader {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #fff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
            display: none; /* Initially hidden */
            margin-left: 5px;
        }
    </style>
@endpush

@section('content')

    <div class="row g-4 justify-content-between align-items-end mt-0">
        {!! breadcrumb(_trans('keyword.Email').' '._trans('keyword.Settings'),['#'=>_trans('keyword.System').' '._trans('keyword.Settings'),'/setting/email-setting'=>_trans('keyword.Email').' '._trans('keyword.Settings')]) !!}
    </div>

    <div class="row g-4">
        <!-- Email Settings Form - Left Side -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{_trans('keyword.Email').' '._trans('keyword.Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('setting.email-setting.store') }}" method="POST" id="emailSettingsForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="email-engine-type">
                                    {{_trans('keyword.Email Engine Type')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="email-engine-type" name="email_engine_type"
                                       class="form-control" placeholder="ex:smtp,mailgun,ses,postmark,sendmail"
                                       value="{{ optional($email_setting)->email_engine_type }}"/>
                                @error('email_engine_type')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="from-name">
                                    {{_trans('keyword.From').' '._trans('keyword.Name')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="from-name" class="form-control"
                                       placeholder="HouseBrands Support" name="from_name"
                                       value="{{optional($email_setting)->from_name}}"
                                       aria-label="Janifer House"/>
                                @error('from_name')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="from-email">
                                    {{_trans('keyword.From').' '._trans('keyword.Email')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="from-email" class="form-control"
                                       placeholder="noreply@housebrands.com" name="from_email"
                                       value="{{optional($email_setting)->from_email}}"/>
                                @error('from_email')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail-driver">
                                    {{_trans('keyword.Mail Driver')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="mail-driver" class="form-control"
                                       placeholder="ex: smtp,sendmail,mail" name="mail_driver"
                                       value="{{optional($email_setting)->mail_driver}}"/>
                                @error('mail_driver')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail-host">
                                    {{_trans('keyword.Mail Host')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="mail-host" class="form-control"
                                       placeholder="smtp.gmail.com" name="mail_host"
                                       value="{{optional($email_setting)->mail_host}}"/>
                                @error('mail_host')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail-port">
                                    {{_trans('keyword.Mail Port')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="mail-port" class="form-control"
                                       placeholder="ex: 25, 465, 587, or 2525" name="mail_port"
                                       value="{{optional($email_setting)->mail_port}}"/>
                                @error('mail_port')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail-username">
                                    {{_trans('keyword.Mail').' '._trans('keyword.User').' '._trans('keyword.Name')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="mail-username" name="mail_username"
                                       class="form-control" placeholder="noreplay@mail.com"
                                       value="{{optional($email_setting)->mail_username}}"/>
                                @error('mail_username')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail-password">
                                    {{_trans('keyword.Mail').' '._trans('keyword.Password')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="mail-password" name="mail_password"
                                       class="form-control" placeholder="••••••••"
                                       value="{{optional($email_setting)->mail_password}}"/>
                                @error('mail_password')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail-encryption">
                                    {{_trans('keyword.Mail Encryption')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="mail-encryption" name="mail_encryption"
                                       class="form-control" placeholder="ex:tls,ssl,none"
                                       value="{{optional($email_setting)->mail_encryption}}"/>
                                @error('mail_encryption')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            @if(hasPermission('email_settings_update'))
                                <div class="col-12">
                                    <button type="submit" id="emailSettingsSubmitBtn"
                                            class="btn btn-primary w-100">
                                        {{_trans('keyword.Submit')}}
                                        <span class="emailLoader" style="display: none;"></span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Test Email Card - Right Side -->
        <div class="col-lg-5">
            <div class="card h-100">

                <div class="card-body">
                    @if(hasPermission('send_test_email'))
                        <form class="row g-3" action="{{route('setting.email-setting.sendTestEmail')}}" method="POST"
                              enctype="multipart/form-data" id="testEmailForm">
                            @csrf
                            <div class="text-center d-flex flex-column justify-content-center">
                                <i class="fa fa-envelope-open-text fs-1 text-primary"></i>
                                <h5>{{ _trans('keyword.Test Email') }}</h5>
                                <p class="text-muted small">
                                    Send a test email to verify your configuration is working correctly.
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="test-subject">
                                    {{_trans('keyword.Subject')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="test-subject" name="subject" class="form-control"
                                       placeholder="Enter subject" required/>
                                @error('subject')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="test-email">
                                    {{_trans('keyword.To Email')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="test-email" name="to_email" class="form-control"
                                       placeholder="Enter email address" required/>
                                @error('to_email')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Message Body -->
                            <div class="col-12">
                                <label class="form-label" for="test-message">
                                    {{_trans('keyword.Message')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea id="test-message" name="message" class="form-control" rows="6"
                                          placeholder="Enter your test email message here..." required></textarea>
                                @error('message')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" id="testEmailSubmitBtn" class="btn btn-primary w-100">
                                    {{_trans('keyword.Send Test Email')}}
                                    <span class="emailLoader" style="display: none;"></span>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            You don't have permission to send test emails.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Password Confirmation Modal -->
    <div class="modal fade" id="passwordConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-body p-4 ">
                    <div class="mb-3">
                        <div
                            class=" text-center avatar avatar-lg bg-light-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fa fa-lock text-primary fs-4"></i>
                        </div>
                        <h5 class="modal-title mb-1 fw-bold text-center">Security Verification</h5>
                        <p class="text-muted small text-center">
                            <strong>Sensitive Action Required:</strong> You are about to update system-wide email
                            configurations.
                            To prevent unauthorized changes to your communication delivery, please verify your identity
                            by entering your account password below.
                        </p>
                    </div>

                    <div class="text-start mb-3">
                        <label class="form-label small fw-semibold text-secondary">Password</label>
                        <input type="password" id="confirmation_password" class="form-control"
                               placeholder="············">
                        <div id="password_error" class="text-danger small mt-1"></div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" id="confirm_password_btn" class="btn btn-primary">
                            <span class="btn-text">Confirm Action</span>
                            <div class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>
                        <button type="button"
                                class="btn btn-outline-light text-dark border-0 btn-sm passwordConfrimationCancel"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        $(document).ready(function () {
            const $emailForm = $('#emailSettingsForm');
            const $testEmailForm = $('#testEmailForm');
            const $emailSettingsSubmitBtn = $('#emailSettingsSubmitBtn');
            const $testEmailSubmitBtn = $('#testEmailSubmitBtn');
            const $confirmModal = new bootstrap.Modal(document.getElementById('passwordConfirmModal'));
            let isPasswordVerified = false;

            // Email Settings Submit Button Click
            $emailSettingsSubmitBtn.on('click', function (e) {
                if (!isPasswordVerified) {
                    e.preventDefault();
                    // Show loader immediately when clicked
                    $emailSettingsSubmitBtn.find('.emailLoader').css('display', 'inline-block');
                    $confirmModal.show();
                }
            });

            // Test Email Submit Button Click - Show emailLoader immediately
            $testEmailSubmitBtn.on('click', function (e) {
                $testEmailSubmitBtn.find('.emailLoader').css('display', 'inline-block');
            });

            // Handle Modal Confirmation Button
            $('#confirm_password_btn').on('click', function () {
                const password = $('#confirmation_password').val();
                const $btn = $(this);
                const $errorSpan = $('#password_error');

                if (!password) {
                    $errorSpan.text('Password is required');
                    return;
                }

                // Show loading state
                $btn.prop('disabled', true);
                $btn.find('.btn-text').addClass('d-none');
                $btn.find('.spinner-border').removeClass('d-none');
                $errorSpan.text('');

                // AJAX Request
                $.ajax({
                    url: '{{ route("setting.payment-method.verifyPassword") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        password: password
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            isPasswordVerified = true;
                            $confirmModal.hide();
                            // Trigger the form submit again
                            $emailForm.submit();
                        } else {
                            $errorSpan.text(response.message || 'Invalid password');
                            resetBtn($btn);
                            // Hide loader on failed password verification
                            $emailSettingsSubmitBtn.find('.emailLoader').css('display', 'none');
                        }
                    },
                    error: function (xhr) {
                        $errorSpan.text(xhr.responseJSON?.message || 'Verification failed');
                        resetBtn($btn);
                        // Hide loader on error
                        $emailSettingsSubmitBtn.find('.emailLoader').css('display', 'none');
                    }
                });
            });

            // Reset button helper
            function resetBtn($btn) {
                $btn.prop('disabled', false);
                $btn.find('.btn-text').removeClass('d-none');
                $btn.find('.spinner-border').addClass('d-none');
            }

            // Clear modal on close
            $('#passwordConfirmModal').on('hidden.bs.modal', function () {
                $('#confirmation_password').val('');
                $('#password_error').text('');
                isPasswordVerified = false;
            });

            // Hide emailLoader on modal cancel
            $('.passwordConfrimationCancel').on('click', function () {
                isPasswordVerified = false;
                $emailSettingsSubmitBtn.find('.emailLoader').css('display', 'none');
            });

        });
    </script>
@endpush
