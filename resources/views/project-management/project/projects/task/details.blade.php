@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Project'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Task Details'), [
            '#' => _trans('keyword.Project') . ' ' . _trans('keyword.Management'),
            'project' => _trans('keyword.Project') . ' ' . _trans('keyword.Overview'),
        ]) !!}
        <div class="row">
            <!-- Customer Content -->
            <div class="col-12 order-0 order-md-1">
                {!! projectTabMenu($project, 'task', $task->id) !!}

                <div class="row">

                    <div class="col-xl-4 col-lg-4 col-md-4 order-1 order-md-0">
                        <!--/ Customer Pills -->
                        <div class="order-1 order-md-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card mb-4">
                                        <div class="card-header">Project Details</div>
                                        <div class="card-body">
                                            <div class="customer-avatar-section">
                                                <div class="d-flex align-items-center flex-column">
                                                    <img class="rounded my-3" src="{{ getFilePath($project->banner) }}"
                                                         height="auto" width="100%" alt="Project banner"/>
                                                    <div class="customer-info text-center">
                                                        <h4 class="mb-1">{{ $project->name }}</h4>
                                                        <small>Project Code: #{{ $project->project_code }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-around flex-wrap my-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar">
                                                        <div class="avatar-initial rounded bg-label-primary">
                                                            <i class="ti ti-calendar ti-md"></i>
                                                        </div>
                                                    </div>
                                                    <div class="gap-0 d-flex flex-column">
                                                        <p class="mb-0 fw-medium">
                                                            Start: {{ dateFormat($project->start_date) }}</p>
                                                        <small>End: {{ dateFormat($project->end_date) }}</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar">
                                                        <div class="avatar-initial rounded bg-label-primary">
                                                            <i class='ti ti-currency-dollar ti-md'></i>
                                                        </div>
                                                    </div>
                                                    <div class="gap-0 d-flex flex-column">
                                                        <p class="mb-0 fw-medium">{{ getPriceFormat(0) }}
                                                            /{{ getPriceFormat($project->total_cost) }}</p>
                                                        <small>Budget</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress mb-4">
                                                <div class="progress-bar" role="progressbar" style="width: 75%;"
                                                     aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8 col-lg-8 col-md-8 order-1 order-md-0">
                        <div class="row">
                            <div class="col-12 order-1 order-md-0">
                                <div class="card">
                                    <div class="card-header">Task Details</div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <h2>{{$task->title}}</h2>
                                                <h6 class="text-muted">{!! $task->description!!}</h6>
                                            </div>
                                        </div>

                                        {{-- Details Section --}}
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="row align-items-center">
                                                    <div class="col-5 col-sm-4 col-md-3 text-nowrap">
                                                        <p class="mb-0"><i class="ti ti-progress-check me-1"></i> Priority</p>
                                                    </div>
                                                    <div class="col-7 col-sm-8 col-md-9">
                                                        <span class="badge bg-label-primary rounded-pill">{{ucfirst($task->priority)}}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="row align-items-center">
                                                    <div class="col-5 col-sm-4 col-md-3 text-nowrap">
                                                        <p class="mb-0"><i class="ti ti-calendar-time me-1"></i> Due Date</p>
                                                    </div>
                                                    <div class="col-7 col-sm-8 col-md-9">
                                                        <p class="mb-0">{{dateFormat($task->due_date)}}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="row align-items-center">
                                                    <div class="col-5 col-sm-4 col-md-3 text-nowrap">
                                                        <p class="mb-0"><i class="ti ti-tag me-1"></i> Tag</p>
                                                    </div>
                                                    <div class="col-7 col-sm-8 col-md-9">
                    <span class="badge badge-pill" style='color:{{$task->taskLabel->color}};background-color: {{$task->taskLabel->color}}20'>
                        {{ucfirst($task->taskLabel->name)}}
                    </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="row align-items-center">
                                                    <div class="col-5 col-sm-4 col-md-3 text-nowrap">
                                                        <p class="mb-0"><i class="ti ti-users me-1"></i> Assigned</p>
                                                    </div>
                                                    <div class="col-7 col-sm-8 col-md-9">
                                                        <ul class="list-unstyled m-0 d-flex align-items-center avatar-group flex-wrap">
                                                            @if($assignedUsers != null)
                                                                @foreach($assignedUsers as $user)
                                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{$user->name}}" class="avatar pull-up">
                                                                        <a href="{{ route('user.profile', $user->id) }}">
                                                                            <img class="rounded-circle" src="{{ getFilePath($user->avatar) }}" alt="Avatar"/>
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                            <li data-mode="modal" data-bs-toggle="modal" data-bs-target="#assignUser" class="ti ti-plus ti-xs ms-1 cursor-pointer" style="font-size:1.5rem !important"></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="row align-items-center">
                                                    <div class="col-5 col-sm-4 col-md-3 text-nowrap">
                                                        <p class="mb-0"><i class="ti ti-user me-1"></i> Manager</p>
                                                    </div>
                                                    <div class="col-7 col-sm-8 col-md-9">
                                                        <ul class="list-unstyled m-0 d-flex align-items-center avatar-group">
                                                            <li class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $project->manager->name }}">
                                                                <a href="{{ route('user.profile', $project->manager->id) }}">
                                                                    <img class="rounded-circle" src="{{ getFilePath($project->manager->avatar) }}" alt="Avatar"/>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="row align-items-center">
                                                    <div class="col-5 col-sm-4 col-md-3 text-nowrap">
                                                        <p class="mb-0"><i class="ti ti-brand-redux me-1"></i> Stage</p>
                                                    </div>
                                                    <div class="col-7 col-sm-8 col-md-9">
                                                        <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="badge badge-pill" style='color:{{$task->status->color}};background-color: {{$task->status->color}}20'>
                            {{ucfirst($task->status->name)}}
                        </span>

                                                            @if($project->user_id == Auth::user()->id || $project->project_manager_id == Auth::user()->id || in_array(Auth::user()->id, $task->assigned_users))
                                                                <div class="ecommerce-select2-dropdown" style="min-width: 150px;">
                                                                    <select class="select2 form-select form-select-sm" name="status_id" id="taskStatusDropdown">
                                                                        @foreach ($taskStatus as $status)
                                                                            <option value="{{ $status->id }}" style="color: {{ $status->color }}" {{ $task->status->id == $status->id ? 'selected' : '' }}>
                                                                                {{ ucfirst($status->name) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Attachments Section --}}
                                        <div class="row mt-4 mb-4">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <p class="mb-0 fw-bold"><i class="ti ti-paperclip me-1"></i> Attachment</p>
                                                <i data-mode="modal" data-bs-toggle="modal" data-bs-target="#addAttachment" class="ti ti-plus ti-xs cursor-pointer" style="font-size:1.5rem !important"></i>
                                            </div>

                                            <div class="row g-2">
                                                @foreach ($task->taskFiles as $item)
                                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 position-relative">
                                                        <div class="p-2 border border-dashed rounded h-100 d-flex align-items-center justify-content-center bg-light">
                                                            <i class="ti ti-x position-absolute bg-white shadow-sm"
                                                               style="right: -5px; top: -5px; cursor: pointer; border-radius: 50%; font-size: 14px; padding: 2px;"
                                                               onclick="deleteFile({{ $item->id }})"></i>
                                                            {!! getFileElement(getFilePath($item->file)) !!}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Tabs Section --}}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="nav-align-top">
                                                    <ul class="nav nav-tabs nav-fill" role="tablist">
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="true">
                                                                <i class="ti ti-list-check me-1"></i> <span class="d-none d-sm-inline">Checklist</span>
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false">
                                                                <i class="ti ti-message me-1"></i> <span class="d-none d-sm-inline">Comment</span>
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-messages" aria-controls="navs-top-messages" aria-selected="false">
                                                                <i class="ti ti-trending-up me-1"></i> <span class="d-none d-sm-inline">Activity</span>
                                                            </button>
                                                        </li>
                                                    </ul>

                                                    <div class="tab-content p-2">
                                                        {{-- Checklist Tab --}}
                                                        <div class="tab-pane fade show active" id="navs-top-home" role="tabpanel">
                                                            @if(count($taskChecklist) > 0)
                                                                <small class="text-muted">Progress</small>
                                                                <div class="progress mb-3 mt-1" style="height: 10px;">
                                                                    <div class="progress-bar" role="progressbar" style="width: {{ $completionPercentage }}%;" aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            @endif

                                                            <div class="demo-inline-spacing mt-1 checklist-container">
                                                                <div class="d-flex justify-content-end mb-2">
                                                                    <button data-bs-target="#addChecklist" data-bs-toggle="modal" class="btn btn-sm btn-icon bg-primary text-white">
                                                                        <i class="ti ti-plus ti-md"></i>
                                                                    </button>
                                                                </div>

                                                                @foreach($taskChecklist as $checklist)
                                                                    <div class="list-group-item d-flex align-items-center justify-content-between p-2 mb-1 border rounded">
                                                                        <div class="d-flex align-items-center text-break me-2">
                                                                            <input class="form-check-input me-2 mt-0" type="checkbox" value="{{ $checklist->id }}" onclick="handleChecklistClick({{ $checklist->id }})" {{ $checklist->active_status ? 'checked' : '' }} />
                                                                            <span class="{{$checklist->active_status == 1 ? 'text-decoration-line-through text-muted':''}}">{{ $checklist->title }}</span>
                                                                        </div>
                                                                        <div class="d-flex align-items-center flex-shrink-0">
                                                                            <small class="text-muted me-2 d-none d-sm-block">{{ dateFormatwithTime($checklist->created_at) }}</small>
                                                                            <a href="javascript:void(0);" onclick="deleteChecklist({{ $checklist->id }})" class="text-danger">
                                                                                <i class="ti ti-trash"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        {{-- Comments Tab --}}
                                                        <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                                                            <div class="chat-container" style="max-height: 400px; overflow-y: auto;">
                                                                @foreach($taskComments as $comment)
                                                                    <div class="card p-3 mb-3 border bg-light">
                                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="avatar avatar-sm me-2">
                                                                                    <a href="{{route('user.profile', $comment->user->id)}}">
                                                                                        <img src="{{ getFilePath($comment->user->avatar) }}" alt="Avatar" class="rounded-circle">
                                                                                    </a>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="m-0 fw-bold">{{ $comment->user->name }}</h6>
                                                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ dateFormatwithTime($comment->created_at) }}</small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="text-break">
                                                                            {{ $comment->comment }}
                                                                        </div>
                                                                        @if($comment->file)
                                                                            <div class="mt-2">
                                                                                {!! getFileElement(getFilePath($comment->file)) !!}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <div class="mt-3">
                                                                <div class="chat-history-footer shadow-sm p-2 border rounded">
                                                                    <form class="form-send-message d-flex align-items-center gap-2 flex-wrap flex-sm-nowrap">
                                                                        <input id="taskId" name="taskId" value="{{ $task->id }}" hidden>

                                                                        <input id="comment" class="form-control border-0 shadow-none flex-grow-1" placeholder="Type your comment here" name="comment">

                                                                        <div class="message-actions d-flex align-items-center gap-2">
                                                                            <label for="attach-doc" class="form-label mb-0 cursor-pointer text-secondary">
                                                                                <i class="ti ti-photo ti-sm"></i>
                                                                                <input type="file" name="file" id="attach-doc" hidden="">
                                                                            </label>
                                                                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                                                                                <i class="ti ti-send me-1"></i>
                                                                                <span class="d-none d-md-inline">Send</span>
                                                                                <span class="loader" style="display: none;"></span>
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <span class="text-danger small mt-1 d-block" id="commentError"></span>
                                                            </div>
                                                        </div>

                                                        {{-- Activity Tab --}}
                                                        <div class="tab-pane fade" id="navs-top-messages" role="tabpanel">
                                                            @foreach ($task->activityLogs as $log)
                                                                <div class="d-flex align-items-start mb-4">
                                                                    <div class="avatar me-2 flex-shrink-0 mt-1">
                                                                        <img class="avatar-initial bg-label-success rounded-circle" src="{{getFilePath($log->user->avatar)}}" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <p class="mb-0 text-break">
                                                                            <strong>{{$log->user->name}}</strong> {{$log->description}}
                                                                        </p>
                                                                        <small class="text-muted">{{dateFormatwithTime($log->created_at)}}</small>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal fade" id="addChecklist" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ _trans('keyword.Add Checklist') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addChecklistForm" class="row g-3"
                                  action="{{ route('project-management.project.task.checklist.store') }}"
                                  method="POST">
                                @csrf
                                <input type="hidden" value="{{ $task->id }}" name="taskID">

                                <div class="col-12">
                                    <label class="form-label" for="title">{{ _trans('keyword.Checklist Title') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="title" name="title"
                                           class="form-control" placeholder="Enter checklist title"/>
                                    <span class="text-danger error" id="titleError"></span>
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" id="submitChecklistForm"
                                            class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Add') }}
                                        <span class="loader"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addAttachment" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ _trans('keyword.Add File') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addAttachmentForm" class="row g-3"
                                  action="{{ route('project-management.project.task.addAttachment') }}"
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $task->id }}" name="taskID">
                                <input type="hidden" value="{{ $task->project_id }}" name="projectId">

                                <div class="row">
                                    <div class="col-6">
                                        <label for="file" class="form-label">{{ _trans('keyword.Choose File') }} <span
                                                class="text-danger">*</span></label>
                                        <input required type="file" id="file" name="file"
                                               class="form-control" placeholder="Choose a file"
                                               onchange="previewFile()"/>
                                        @error('file')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Preview Container -->
                                    <div class="col-6" id="filePreviewContainer" style="display: none;">
                                        <div class="text-center">
                                            <h6 style="margin: 0 !important;">File Preview:</h6>
                                            <img id="filePreview" src="#" alt="File Preview"
                                                 style="max-width: 80px; max-height: 80px;"/>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12 text-center">
                                    <button type="submit"
                                            class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Submit') }}
                                        <span class="loader"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="assignUser" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ _trans('keyword.Assign User') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="assignUserForm" class="row g-3"
                                  action="{{ route('project-management.project.task.assignUser') }}"
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $task->id }}" name="taskID">
                                <input type="hidden" value="{{ $task->project_id }}" name="projectId">

                                <div class="col-12">
                                    <label for="TagifyUserList" class="form-label">{{ _trans('keyword.Select Users') }}
                                        <span class="text-danger">*</span></label>
                                    <input
                                        id="TagifyUserList"
                                        name="TagifyUserList"
                                        class="TagifyUserList form-control"
                                        value='{{$assignedUsersJson}}'
                                    />
                                    @error('TagifyUserList')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit"
                                            class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Submit') }}
                                        <span class="loader"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
@push('scripts')

    <script>

        @error('file')
        toastr.error('{{ $message }}');
        @enderror

        function deleteFile(fileId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "you want to delete this file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'No, cancel',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('project-management.project.task.deleteAttachment') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: fileId
                        },
                        success: function (response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                location.reload();
                            } else {
                                toastr.error(response.message)
                            }
                        },
                        error: function (error) {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong!',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        }

        $(document).ready(function () {
            $(".form-send-message").submit(function (e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('project-management.project.task.storeComment') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (response) {
                        if (response.status == 200) {
                            // Append the new comment to the UI
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        if (xhr.status === 422) {
                            // Validation errors
                            let errors = xhr.responseJSON.errors;
                            if (errors.comment) {
                                $("#commentError").text(errors.comment[0]);
                            }
                        } else {
                            toastr.error('Something went wrong!', error)
                        }
                    },
                    complete: function () {
                        // Hide loader and enable the submit button
                        loader.hide();
                        submitButton.prop("disabled", false);
                    }
                });
            });
        });

        $(document).ready(function () {
            $('#taskStatusDropdown').change(function () {
                var statusId = $(this).val();
                var taskId = {{ $task->id }};
                var projectId = {{$project->id}}
                $.ajax({
                    url: "/project-management/project/task/" + projectId + "/status/update",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        task_id: taskId,
                        status_id: statusId
                    },
                    success: function (response) {
                        console.log(response)
                        if (response.success) {
                            toastr.success("Status updated successfully!");
                            location.reload();
                        } else {
                            toastr.error("Failed to update status!");
                        }
                    },
                    error: function (xhr) {
                        toastr.error("An error occurred while updating status.");
                    }
                });
            });
        });

        function previewFile() {
            const fileInput = document.getElementById('file');
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const filePreview = document.getElementById('filePreview');

            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    filePreview.src = e.target.result;
                    filePreviewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                filePreviewContainer.style.display = 'none';
            }
        }

        function handleChecklistClick(checklistId) {

            $.ajax({
                url: "{{ route('project-management.project.task.checklist.changeStatus') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: checklistId,
                },
                success: function (response) {
                    if (response.status == 200) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message)
                    }
                },
                error: function (xhr) {
                    console.error("Error updating status", xhr.responseText);
                }
            });
        }

        function deleteChecklist(checklistId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this checklist item?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'No, cancel',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('project-management.project.task.checklist.delete') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: checklistId
                        },
                        success: function (response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                location.reload();
                            } else {
                                toastr.error(response.message)
                            }
                        },
                        error: function (error) {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong!',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        }

        $(document).ready(function () {
            $('#assignUser').on('shown.bs.modal', function () {
                $.ajax({
                    url: "{{ route('project-management.project.task.getUsers') }}",
                    type: "GET",
                    success: function (response) {
                        let userList = [];

                        $(response).each(function (index, value) {
                            userList.push({
                                value: value.id,
                                name: value.name,
                                avatar: `${value.avatar}`,
                            });
                        });

                        tagifyuserList(userList);
                    },
                    error: function () {
                        console.error("Error fetching user list");
                    }
                });
            });
        });

        let TagifyUserList;

        function tagifyuserList(dataList) {
            const TagifyUserListEl = document.querySelector('#TagifyUserList');
            let selectedTags = TagifyUserList ? TagifyUserList.value.map(tag => ({
                value: tag.value,
                name: tag.name,
                avatar: tag.avatar
            })) : [];
            // Destroy the existing Tagify instance if it exists
            if (TagifyUserList) {
                console.log('here to destroy');
                TagifyUserList.destroy();
            }

            function tagTemplate(tagData) {
                return `
    <tag title="${tagData.name}"
      contenteditable='false'
      spellcheck='false'
      tabIndex="-1"
      class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ''}"
      ${this.getAttributes(tagData)}
    >
      <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
      <div>
        <div class='tagify__tag__avatar-wrap'>
          <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
        </div>
        <span class='tagify__tag-text'>${tagData.name}</span>
      </div>
    </tag>
  `;
            }

            function suggestionItemTemplate(tagData) {
                return `
    <div ${this.getAttributes(tagData)}
      class='tagify__dropdown__item align-items-center ${tagData.class ? tagData.class : ''}'
      tabindex="0"
      role="option"
    >
      ${
                    tagData.avatar
                        ? `<div class='tagify__dropdown__item__avatar-wrap'>
          <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
        </div>`
                        : ''
                }
      <div class="fw-medium">${tagData.name}</div>
    </div>
  `;
            }

            function dropdownHeaderTemplate(suggestions) {
                return `
        <div class="${this.settings.classNames.dropdownItem} ${this.settings.classNames.dropdownItem}__addAll">
            <strong>${this.value.length ? `Select remaining` : 'Select All'}</strong>
            <span>${suggestions.length} users</span>
        </div>
    `;
            }

            // initialize Tagify on the above input node reference
            TagifyUserList = new Tagify(TagifyUserListEl, {
                tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
                enforceWhitelist: true,
                skipInvalid: true, // do not remporarily add invalid tags
                dropdown: {
                    closeOnSelect: false,
                    enabled: 0,
                    classname: 'users-list',
                    searchKeys: ['name'] // very important to set by which keys to search for suggesttions when typing
                },
                templates: {
                    tag: tagTemplate,
                    dropdownItem: suggestionItemTemplate,
                    dropdownHeader: dropdownHeaderTemplate
                },
                whitelist: dataList
            });

            // Step 4: Reapply the selected tags
            TagifyUserList.addTags(selectedTags);

            // attach events listeners
            TagifyUserList.on('dropdown:select', onSelectSuggestion) // allows selecting all the suggested (whitelist) items
                .on('edit:start', onEditStart); // show custom text in the tag while in edit-mode

            function onSelectSuggestion(e) {
                // custom class from "dropdownHeaderTemplate"
                if (e.detail.elm.classList.contains(`${TagifyUserList.settings.classNames.dropdownItem}__addAll`))
                    TagifyUserList.dropdown.selectAll();
            }

            function onEditStart({detail: {tag, data}}) {
                TagifyUserList.setTagTextNode(tag, `${data.name}`);
            }
        }

    </script>

@endpush



