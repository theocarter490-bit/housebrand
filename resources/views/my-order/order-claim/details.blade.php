@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Order Claims').' '. _trans('keyword.Details'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Order Claims').' '. _trans('keyword.Details'), ['#' => _trans('keyword.Order').' '. _trans('keyword.Management'),'#'=>_trans('keyword.Customer').' '._trans('keyword.Order'), '/order-claim'=>_trans('keyword.Order Claims'), 'claim_details' => _trans('keyword.Order Claims').' '. _trans('keyword.Details')]) !!}
        <div class="row invoice-preview">
            <!-- Invoice -->
            <div class="col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        <div class="row p-sm-3 p-0">
                            <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-4 mb-sm-0 mb-4">
                                <h6 class="mb-3 fw-bold">{{_trans('keyword.Claim').' '. _trans('keyword.Details')}}</h6>
                                <table>
                                    <tbody>

                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Status')}}:</td>
                                        <td>
                                            {{$orderClaim->status}}
                                        </td>

                                    </tr>

                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Issue Type')}}:</td>
                                        <td><span
                                                class="badge bg-label-info">{{$orderClaim->orderClaimIssueType->name}}</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Date Time')}}:</td>
                                        <td> {{dateFormat($orderClaim->date_time)}}</td>
                                    </tr>

                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Current').' '. _trans('keyword.Status')}}:
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-warning">{{$orderClaim->status}} </span>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xl-6 col-md-12 col-sm-7 col-12">
                                <h6 class="mb fw-bold">{{_trans('keyword.Order Info')}}</h6>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Order Code')}}:</td>
                                        <td class="fw-medium">{{@$orderClaim->order->code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Order Date')}}:</td>
                                        <td>{{ dateFormat($orderClaim->order->order_date) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Order Status')}}:</td>
                                        <td><span
                                                class="badge bg-label-success">{{ $orderClaim->order->orderStatus->name }}</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Payment status')}}:</td>
                                        <td class="fw-medium">
                                            @if ($orderClaim->order->payment_status == 'paid')
                                                <span class="badge bg-label-success">{{_trans('keyword.Paid')}}</span>
                                            @elseif ($orderClaim->order->payment_status == 'partial')
                                                <span
                                                    class="badge bg-label-info">{{_trans('keyword.Parlially Paid')}}</span>
                                            @else
                                                <span class="badge bg-label-danger">{{_trans('keyword.Unpaid')}}</span>
                                            @endif</td>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="pe-4">{{_trans('keyword.Total Order Amount')}}:</td>
                                        <td>{{ getPriceFormat($orderClaim->order->grand_total_amount) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr class="m-0">

                    <div class="p-3 mt-3">
                        <h4 class="mb-3 fw-bold">{{_trans('keyword.Subject')}}: {{$orderClaim->subject}}</h4>
                        <h6 class="mb-3 fw-bold">{{_trans('keyword.Message')}}: </h6>

                        {{$orderClaim->details}}

                    </div>

                    <div class="p-3 pb-0  mt-3">
                        <h6 class="mb-3 fw-bold">{{_trans('keyword.File/Attachment')}}: </h6>
                        @if($orderClaim->file)
                            {!!getFileElement(getFilePath($orderClaim->file)) !!}
                        @endif
                    </div>

                    <hr class="m-0">

                    <!-- Chat History -->
                    <div class="container-xxl flex-grow-1 container-p-y ">
                        <div class="app-chat card overflow-hidden">
                            <div class="row g-0">
                                <div class="col app-chat-history bg-body ">
                                    <div class="chat-history-wrapper">
                                        <div class="chat-history-header border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex overflow-hidden align-items-center">
                                                    <i
                                                        class="ti ti-menu-2 ti-sm cursor-pointer d-lg-none d-block me-2"
                                                        data-bs-toggle="sidebar"
                                                        data-overlay
                                                        data-target="#app-chat-contacts"></i>
                                                    <div class="flex-shrink-0 avatar">
                                                        <img
                                                            src="{{getFilePath($orderClaim->order->shop->logo)}}"
                                                            alt="Avatar"
                                                            class="rounded-circle"
                                                            data-bs-toggle="sidebar"
                                                            data-overlay
                                                            data-target="#app-chat-sidebar-right"/>
                                                    </div>
                                                    <div class="chat-contact-info flex-grow-1 ms-2">
                                                        <h6 class="m-0">{{$orderClaim->order->shop->shop_name}}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="chat-history-body bg-body" id="chat_history">
                                            <ul class="list-unstyled chat-history">
                                                @foreach($replies as $message)
                                                    <li class="chat-message {{ $message->user_id == auth()->id() ? 'chat-message-right' : '' }}">
                                                        <div class="d-flex overflow-hidden">
                                                            @if($message->user_id != auth()->id())
                                                                <div class="user-avatar flex-shrink-0 me-3">
                                                                    <div class="avatar avatar-sm">
                                                                        <img
                                                                            src="{{ getFilePath($message->user->avatar) }}"
                                                                            alt="Avatar" class="rounded-circle"/>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="chat-message-wrapper flex-grow-1">
                                                                <div class="chat-message-text">
                                                                    <p class="mb-0">{{ $message->details }}</p>
                                                                </div>
                                                                @if($message->file)
                                                                    <div class="file-attachment mt-2">
                                                                        <a href="{{ getFilePath($message->file) }}"
                                                                           download>
                                                                            {!! getFileElement(getFilePath($message->file)) !!}
                                                                        </a>
                                                                    </div>
                                                                @endif

                                                                <div
                                                                    class="text-{{ $message->user_id == auth()->id() ? 'end' : 'start' }} text-muted mt-1">
                                                                    <i class="ti ti-checks ti-xs me-1 {{ $message->user_id == auth()->id() ? 'text-success' : '' }}"></i>
                                                                    <small>{{ dateFormatwithTime($message->created_at) }}</small>
                                                                </div>
                                                            </div>

                                                            @if($message->user_id == auth()->id())
                                                                <div class="user-avatar flex-shrink-0 ms-3">
                                                                    <div class="avatar avatar-sm">
                                                                        <img
                                                                            src="{{ getFilePath(auth()->user()->avatar) }}"
                                                                            alt="Avatar" class="rounded-circle"/>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <!-- Chat message form -->
                                        <div class="chat-history-footer shadow-sm">
                                            <form
                                                class="form-send-message d-flex justify-content-between align-items-center">
                                                <input id="orderClaimId" name="order_claim_id"
                                                       value="{{ $orderClaim->id }}" type="text" hidden>

                                                <input id="messageInput"
                                                       class="form-control border-0 me-3 shadow-none"
                                                       placeholder="Type your message here" name="message"/>

                                                <div class="message-actions d-flex align-items-center">
                                                    <label for="attach-doc" class="form-label mb-0">
                                                        <i class="ti ti-photo ti-sm cursor-pointer mx-3"></i>
                                                        <input type="file" name="file" id="attach-doc" hidden/>
                                                    </label>

                                                    <!-- File Preview -->
                                                    <div id="filePreview" class="ms-2"></div>

                                                    <button type="submit" class="btn btn-primary d-flex send-msg-btn">
                                                        <i class="ti ti-send me-md-1 me-0"></i>
                                                        <span class="align-middle d-md-inline-block d-none">Send</span>
                                                        <span class="loader"></span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Chat History -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /Invoice -->

            <div class="col-12 col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title m-0">{{_trans('keyword.Seller details')}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-4">
                            <div class="avatar me-2">
                                <img src="{{ getFilePath(optional($orderClaim->order->shop)->logo) }}" alt="Avatar"
                                     class="rounded-circle"/>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-body text-nowrap">
                                    <h6 class="mb-0">{{ optional($orderClaim->order->shop)->shop_name }}</h6>
                                </a>
                                <small class="text-muted">{{_trans('keyword.Member since')}}
                                    : {{monthFormat(optional($orderClaim->order->shop)->created_at)}}</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <h6>{{_trans('keyword.Contact info')}}</h6>
                        </div>
                        <p class="mb-1">{{_trans('keyword.Email')}}: {{ optional($orderClaim->order->shop)->email }}</p>
                        <p class="mb-0">{{_trans('keyword.Mobile')}}
                            : {{ optional($orderClaim->order->shop)->phone }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')

    <script src="{{ asset(mix('assets/js/app-chat.js')) }}"></script>
    <script>

        $(function () {

            @if(hasPermission('customer_order_claim_status_change'))
            $(document).on('change', '#claim_status', function () {
                const status = $(this).val();
                const orderClaimId = {{$orderClaim->id}};

                const formData = new FormData();
                formData.append('claimIssueStatus', status)
                formData.append('orderClaimId', orderClaimId)
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "To change the status of this.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Update it',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('order-claim.statusChange') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    window.location.reload();
                                }
                            },
                            error: function (error) {
                                toastr.error(error);
                            }
                        })
                    } else {
                        window.location.reload();
                    }
                });


            });
            @endif

        });


        $(document).ready(function () {
            $('.form-send-message').on('submit', function (event) {
                event.preventDefault(); // Prevent form submission

                // Collect values by ID
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token
                formData.append('order_claim_id', $('#orderClaimId').val());
                formData.append('message', $('#messageInput').val());

                // Check if file is selected
                if ($('#attach-doc')[0].files[0]) {
                    formData.append('file', $('#attach-doc')[0].files[0]);
                }

                // Show a loader (if you have a loader element in the button)
                $('.send-msg-btn .loader').show();

                $.ajax({
                    url: '{{ route('order-claim-reply.store') }}', // Use the route directly as a string
                    type: 'POST',
                    data: formData,
                    processData: false, // Required for FormData
                    contentType: false, // Required for FormData
                    success: function (response) {
                        // Hide loader
                        $('.send-msg-btn .loader').hide();

                        if (response.status === 200) {
                            $('#messageInput').val(''); // Clear message input
                            $('#attach-doc').val(''); // Clear file input
                            toastr.success(response.message); // Show success message

                            // Reload chat history and then scroll to the bottom
                            $('#chat_history').load(location.href + ' #chat_history > *', function () {
                                // Scroll to the bottom of the chat history
                                $('#chat_history').scrollTop($('#chat_history')[0].scrollHeight);
                            });
                            $('#attach-doc').val(''); // Clear file input
                            $('#filePreview').empty(); // Clear preview
                        } else {
                            toastr.error(response.message); // Show error message
                        }
                    },
                    error: function (response) {
                        // Hide loader and show error
                        if (response.status === 422) {
                            $.each(response.responseJSON.errors, function (field, messages) {
                                toastr.error(messages[0]); // show the first error for each field
                            });
                        }
                        if (response.status === 403) {
                            toastr.error(response.responseJSON.message)
                        } else {
                            toastr.error('Failed to send message');
                        }

                        $('.send-msg-btn .loader').hide();
                    }
                });
            });
        });
        $(document).ready(function () {
            // File preview handler
            $('#attach-doc').on('change', function (event) {
                let file = event.target.files[0];
                let previewContainer = $('#filePreview');
                previewContainer.empty(); // Clear previous preview

                if (file) {
                    let fileType = file.type;

                    if (fileType.startsWith('image/')) {
                        // Image preview
                        let reader = new FileReader();
                        reader.onload = function (e) {
                            previewContainer.html(`
                        <div class="position-relative d-inline-block me-2">
                            <img src="${e.target.result}" alt="Preview"
                                 class="rounded"
                                 style="width: 50px; height: 50px; object-fit: cover;" />
                            <button type="button"
                                    class="btn btn-sm btn-danger rounded-circle position-absolute top-0 start-100 translate-middle remove-preview"
                                    style="width: 20px; height: 20px; padding: 0; line-height: 1;">
                                ×
                            </button>
                        </div>
                    `);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // Non-image (PDF, DOC, etc.)
                        previewContainer.html(`
                    <div class="position-relative d-inline-block me-2 p-2 border rounded bg-light">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-file me-2 fs-4"></i>
                            <span class="small text-truncate" style="max-width: 100px;">${file.name}</span>
                        </div>
                        <button type="button"
                                class="btn btn-sm btn-danger rounded-circle position-absolute top-0 start-100 translate-middle remove-preview"
                                style="width: 20px; height: 20px; padding: 0; line-height: 1;">
                            ×
                        </button>
                    </div>
                `);
                    }
                }
            });

            // Remove preview and clear input
            $(document).on('click', '.remove-preview', function () {
                $('#attach-doc').val(''); // Clear file input
                $('#filePreview').empty(); // Clear preview
            });
        });
    </script>
@endpush
