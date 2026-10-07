@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Invitation code'))

@section('content')

    <style>
        /* --- Modern Neo-Minimal Card --- */
        .signup-mini-card {
            background: var(--color-bg-primary);
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .signup-mini-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        /* --- Buttons --- */
        .btn-light {
            background-color: #f9fafb;
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }

        .btn-light:hover {
            background-color: #eef1f4;
        }

        /* --- Soft Badges --- */
        .bg-success-soft {
            background-color: #e8f7ee !important;
        }

        .bg-danger-soft {
            background-color: #fdebec !important;
        }

        /* --- Switch --- */
        .form-check-input {
            width: 2.2em;
            height: 1.1em;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
    </style>

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Generate Invitation') . ' ' . _trans('keyword.Code'), [
            '#' => _trans('keyword.General') . ' ' . _trans('keyword.Settings'),
            'setting/free-signup' => _trans('keyword.Invitation') . ' ' . _trans('keyword.Code'),
        ]) !!}
        <div class="app-ecommerce-category">

            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8 mb-4">
                                    <form method="GET" class="mb-2" action="" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                                <div class="input-effect">
                                                    <input
                                                        class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                                        type="search" placeholder="code" name="search"
                                                        value="{{ @$search }}"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                                <div class="input-control">
                                                    <select class="w-100   form-control height-50 select2"
                                                            style="width: 100%"
                                                            aria-label="Active Status"
                                                            data-placeholder="Active Status"
                                                            name="active_status">
                                                        <option value="">Active Status</option>
                                                        <option value="1" {{ @$active_status == 1 ? 'selected' : '' }}>
                                                            Active
                                                        </option>
                                                        <option value="2" {{ @$active_status == 2 ? 'selected' : '' }}>
                                                            Inactive
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-6">
                                                <button class="btn btn-primary" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                @if (hasPermission('free_signup_create'))
                                    <div class="col-4 text-right">

                                        <button class="btn btn-primary mb-2" type="button" data-bs-toggle="modal"
                                                data-bs-target="#addEmployeeModal"><i class="ti ti-plus ti-xs me-0"></i>Generate
                                            Code
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                @forelse($signUpKeys as $data)
                                    <div class="col-12 col-md-4 col-lg-3 mb-3">
                                        <div class="signup-mini-card d-flex flex-column justify-content-between">
                                            <!-- Code + Copy -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0">{{ $data->code }}</h6>
                                                    <small class="text-muted">Invitation Code</small>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-light border-0 copyCode"
                                                        data-code="{{ $data->code }}" title="Copy Code">
                                                    <i class="ti ti-copy text-primary fs-6"></i>
                                                </button>
                                            </div>

                                            <!-- Info Row -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <div>
                                                        <small class="text-muted">Max Use</small>
                                                        <button class="fw-semibold text-warning border border-0">
                                                            {{ $data->no_of_use }}
                                                        </button>

                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Validity (in days)</small>
                                                        <button class="fw-semibold text-primary border border-0">
                                                            {{ $data->validity }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Used</small>
                                                    <button class="fw-semibold text-success border border-0 viewUsers"
                                                            data-id="{{ $data->id }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewDetailsModal">
                                                        {{ $data->uses_count }}
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Status + Actions -->
                                            <div
                                                class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if (hasPermission('free_signup_status_change'))
                                                        <div class="form-check form-switch m-0">
                                                            <input class="form-check-input shadow-none changeStatus"
                                                                   type="checkbox" data-id="{{ $data->id }}"
                                                                {{ $data->active_status == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    @endif

                                                    <span
                                                        class="badge rounded-pill {{ $data->active_status ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                                        {{ $data->active_status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>

                                                @if (hasPermission('free_signup_delete'))
                                                    <button class="btn btn-sm btn-outline-danger border-0 delete_code"
                                                            data-id="{{ $data->id }}" title="Delete Code">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5">
                                        <i class="ti ti-alert-octagon fs-1 text-danger mb-2"></i>
                                        <p class="text-muted fw-semibold mb-0">No Sign-up Keys Found</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Add Modal -->
            <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
                    <div class="modal-content p-3 p-md-5">
                        <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        <div class="modal-body">
                            <div class="text-center mb-2">
                                <h3 class="role-title mb-2">
                                    {{ _trans('keyword.Generate') . ' ' . _trans('keyword.New') . ' ' . _trans('keyword.Code') }}
                                </h3>
                            </div>
                            <form class="row g-3" action="{{ route('setting.free-signup.store') }}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="col-10 mb-2">
                                    <label class="form-label">{{ _trans('keyword.code') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="code" name="code" required
                                           value="{{ old('code') }}" class="form-control"
                                           placeholder="Ex: AB58-8JKA-L5FT-N85Q"/>
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-2 mb-2">
                                    <button type="button" id="generate_code" class="btn btn-primary mt-4"><i
                                            class="ti ti-brand-react-native"></i></button>
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="form-label">{{ _trans('keyword.No of Use') }}<span
                                            class="text-danger">*</span></label>
                                    <input id="no_of_use" type="number" name="no_of_use" required class="form-control"
                                           placeholder="Ex: 5" value="{{ old('no_of_use') }}"/>
                                    @error('no_of_use')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-4 mb-2">
                                    <label class="form-label">{{ _trans('keyword.Validity (in days)') }}<span
                                            class="text-danger">*</span></label>
                                    <input id="validity" type="number" name="validity" required class="form-control"
                                           placeholder="Ex: 5" value="{{ old('validity') }}"/>
                                    @error('validity')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="col-md-4 mb-2">
                                    <label class="form-label mb-1"
                                           for="status-org">{{ _trans('keyword.Status') }}</label>
                                    <select id="status" name="status" class="select2 form-select ">
                                        <option value="1" selected>{{ _trans('keyword.Active') }}</option>
                                        <option value="0">{{ _trans('keyword.Inactive') }}</option>
                                    </select>
                                    @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                            id="addFaq">{{ _trans('keyword.Submit') }}
                                        <span class="loader"></span>
                                    </button>
                                    <button id="reset" type="reset" class="btn btn-label-secondary"
                                            data-bs-dismiss="modal"
                                            aria-label="Close">{{ _trans('keyword.Cancel') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Add Modal -->

            <!-- View Details Modal -->
            <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content p-3 p-md-4">
                        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <h4 class="fw-semibold mb-1" id="codeContainer">Code</h4>
                                <p class="text-muted mb-0">List of users who used this free sign-up code</p>
                            </div>

                            <div id="userListContainer" class="mt-3">
                                <div class="text-center text-muted py-4" id="loadingUsers">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2 mb-0">Loading users...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /View Details Modal -->
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $('#generate_code').on('click', function () {
            $.ajax({
                url: '{{ route('setting.free-signup.generateKey') }}',
                type: 'get',
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log(response);
                    $('#code').val(response.key);
                },
                error: function (error) {
                    location.reload();
                    toastr.error(error.responseJSON.message);
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
                text: "Do you want to change the status of this?",
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
                    $.ajax({
                        url: '{{ route('setting.free-signup.changeStatus') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 200) {
                                location.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                                location.reload();
                            }
                        },
                        error: function (error) {
                            location.reload();
                            toastr.error(error.responseJSON.message);
                        }
                    });
                } else {
                    location.reload();
                }

            });
        });
        $(document).on('click', '.delete_code', function () {
            const id = $(this).data('id');
            const formData = new FormData();
            formData.append('id', id);
            formData.append('_token', "{{ csrf_token() }}");

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to Delete the code?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it',
                cancelButtonText: 'No, cancel',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('setting.free-signup.destroy') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 200) {
                                location.reload();
                            } else {
                                location.reload();
                            }
                        },
                        error: function (error) {
                            location.reload();
                            toastr.error(error.responseJSON.text);
                        }
                    });
                }
                table.ajax.reload(null, false);
            });
        });
        $(document).on('click', '.copyCode', function () {
            const code = $(this).data('code');
            const btn = $(this);

            if (navigator.clipboard && navigator.clipboard.writeText) {
                // Modern browsers (HTTPS)
                navigator.clipboard.writeText(code).then(() => {
                    btn.text('Copied!');
                    setTimeout(() => btn.html('<i class="ti ti-copy me-1"></i>'), 1500);
                });
            } else {
                // Fallback for HTTP or older browsers
                const tempInput = $('<input>');
                $('body').append(tempInput);
                tempInput.val(code).select();
                document.execCommand('copy');
                tempInput.remove();

                btn.text('Copied!');
                setTimeout(() => btn.html('<i class="ti ti-copy me-1"></i>'), 1500);
            }
        });

        $(document).on('click', '.viewUsers', function () {
            const id = $(this).data('id');
            const modal = $('#viewDetailsModal');
            const container = $('#userListContainer');

            container.html(`
        <div class="text-center text-muted py-4" id="loadingUsers">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 mb-0">Loading users...</p>
        </div>
    `);

            $.ajax({
                url: '/setting/free-signup/user-list/' + id,
                type: 'GET',
                success: function (response) {
                    if (response.status === 200 && response.code && response.code.uses && response.code.uses.length > 0) {
                        let usersHtml = '';
                        $('#codeContainer').text(response.code.code);
                        response.code.uses.forEach((use, index) => {
                            const user = use.user;
                            usersHtml += `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div class="d-flex flex-row gap-1">
                                <div>
                                    <img src="/storage/${user?.shop?.logo}" width="30px" height="50px" alt="shop_logo" onerror="this.src='/assets/img/placeholder/placeholder.png';"/>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark">${user?.shop?.shop_name ?? 'Unknown Shop'}</h6>
                                    <small class="text-muted">${user?.shop?.email ?? 'No email available'}</small>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-1">
                            <span class="badge bg-light text-dark">Start at: ${use.start_at ? new Date(use.start_at).toLocaleDateString() : '-'}</span>
                            <span class="badge bg-light text-dark">Expired at: ${use.expire_at ? new Date(use.expire_at).toLocaleDateString() : '-'}</span>

                            </div>
                        </div>
                    `;
                        });
                        container.html(usersHtml);
                    } else {
                        container.html(`
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-alert-octagon fs-1 text-danger mb-2"></i>
                        <p class="mb-0">No users have used this code yet.</p>
                    </div>
                `);
                    }
                },
                error: function () {
                    container.html(`
                <div class="text-center text-danger py-4">
                    <i class="ti ti-alert-circle fs-1 mb-2"></i>
                    <p class="mb-0">Failed to load users. Please try again later.</p>
                </div>
            `);
                }
            });
        });
            $(function () {
                $('.select2').select2({
                    allowClear: true,
                });
            })


    </script>
@endpush
