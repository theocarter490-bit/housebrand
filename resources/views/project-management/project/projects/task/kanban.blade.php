@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Project'))
@push('styles')
    <style>
        .ps__rail-x {
            top: 0 !important;
            bottom: auto !important;
        }

    </style>
@endpush
@section('content')



    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Kanban') .' ('. $project->title .')', [
            '#' => _trans('keyword.Project') . ' ' . _trans('keyword.Management'),
            'project' => _trans('keyword.Project').' ' . _trans('keyword.Overview'),
        ]) !!}
        <div class="row">

            <!-- Customer Content -->
            <div class="col-12 order-0 order-md-1">
                {!! projectTabMenu($project, 'task', $project->id) !!}
                <div class="row">
                    <div class="app-kanban">
                        <!-- Add new board -->
                        <div class="row">
                            <div class="col-12">
                                <form class="kanban-add-new-board" method="POST"
                                      action="{{route('project-management.project.task.taskStatusStore',$project->id)}}">
                                    @csrf
                                    <label class="kanban-add-board-btn" for="kanban-add-board-input">
                                        <i class="ti ti-plus ti-xs"></i>
                                        <span class="align-middle">Add Stage</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control w-px-250 kanban-add-board-input mb-2 d-none"
                                        name="name"
                                        placeholder="Stage Title"
                                        id="kanban-add-board-input"
                                        required/>
                                    <input
                                        type="color"
                                        class="form-control w-px-250 kanban-add-board-input mb-2 d-none"
                                        name="color"
                                        placeholder="Add Stage Title"
                                        required/>
                                    <div class="mb-3 kanban-add-board-input d-none">
                                        <button class="btn btn-primary btn-sm me-2">Add</button>
                                        <button type="button"
                                                class="btn btn-label-secondary btn-sm kanban-add-board-cancel-btn">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Kanban Wrapper -->
                        <div class="kanban-wrapper"></div>

                        <!-- Edit Task & Activities -->
                        <div class="offcanvas offcanvas-end kanban-update-item-sidebar">
                            <div class="offcanvas-header border-bottom">
                                <h5 class="offcanvas-title">Edit Task</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <div class="px-0 pb-0">
                                    <!-- Update item/tasks -->
                                    <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
                                        <form method="post"
                                              action="{{route('project-management.project.task.update',$project->id)}}"
                                              id="update_task"
                                              enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label" for="title">Title <span
                                                        class="text-danger">*</span></label>
                                                <input name="task_id" id="task_id" required class="d-none"/>
                                                <input name="title" type="text" id="edit_title" required
                                                       class="form-control"
                                                       placeholder="Enter Title"/>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="due-date">Due Date <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="edit_due-date" name="due_date" required
                                                       class="form-control"
                                                       placeholder="Enter Due Date"/>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="label"> Label <span
                                                        class="text-danger">*</span></label>
                                                <select class="select2 select2-label form-select" id="edit_label"
                                                        required name="label">
                                                    @foreach($taskLabels as $label)
                                                        <option data-color="{{$label->color}}"
                                                                value="{{$label->id}}">{{$label->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="priority" class="form-label">Select Priority <span
                                                        class="text-danger">*</span></label>
                                                <select id="edit_priority" name="priority" class=" form-select"
                                                        data-placeholder="Select Priority" required>
                                                    <option
                                                        value="">{{_trans('keyword.Select').' '._trans('keyword.Priority')}}</option>
                                                    <option value="low" {{old('priority') == "low"?'selected':''}}>Low
                                                    </option>
                                                    <option
                                                        value="medium" {{old('priority') == "medium"?'selected':''}}>
                                                        Medium
                                                    </option>
                                                    <option value="high" {{old('priority') == "high"?'selected':''}}>
                                                        High
                                                    </option>
                                                    <option
                                                        value="urgent" {{old('priority') == "urgent"?'selected':''}}>
                                                        Urgent
                                                    </option>
                                                </select>
                                                @error('priority')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="attachments">Attachments</label>
                                                <input type="file" class="form-control" name="attachment"
                                                       id="attachments"/>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label">Description</label>
                                                <div class="form-control p-0 pt-1">
                                                    <div class="commonEditor1-toolbar border-0 border-bottom">
                                                        <div class="d-flex justify-content-start">
                                                <span class="ql-formats me-0">
                                                    <button class="ql-bold"></button>
                                                    <button class="ql-italic"></button>
                                                    <button class="ql-underline"></button>
                                                    <button class="ql-list" value="ordered"></button>
                                                    <button class="ql-list" value="bullet"></button>
                                                    <button class="ql-link"></button>
                                                </span>
                                                        </div>
                                                    </div>
                                                    <div class="commonEditor1 border-0 pb-4"
                                                         id="edit_description"></div>
                                                    <input id="update_description" class="d-none" name="description"
                                                           type="text">
                                                </div>
                                                <span class="text-danger descriptionError error"></span>
                                            </div>
                                            <div class="d-flex flex-wrap">
                                                <button type="submit" class="btn btn-primary me-3"
                                                        data-bs-dismiss="offcanvas">
                                                    Update
                                                </button>
                                                <button type="button" class="btn btn-label-danger"
                                                        data-bs-dismiss="offcanvas">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- add Task & Activities -->
                        <div class="offcanvas offcanvas-end kanban-create-item-sidebar">
                            <div class="offcanvas-header border-bottom">
                                <h5 class="offcanvas-title">Create Task</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">

                                <!-- Update item/tasks -->
                                <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
                                    <form method="post"
                                          action="{{route('project-management.project.task.store',$project->id)}}"
                                          id="create_task"
                                          enctype="multipart/form-data">
                                        @csrf
                                        <input id="create_task_status_id" class="d-none" required
                                               name="create_task_status_id"
                                               type="text">
                                        <div class="mb-3">
                                            <label class="form-label" for="title">Title<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="title" name="title" required class="form-control"
                                                   placeholder="Enter Title"/>
                                            @error('title')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="due-date">Due Date<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" value="{{\Carbon\Carbon::today()}}" name="due_date"
                                                   required id="due-date-create"
                                                   class="form-control"
                                                   placeholder="Enter Due Date"/>
                                            @error('due_date')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="label">Label<span
                                                    class="text-danger">*</span></label>
                                            <select class="select2 select2-label form-select" required name="label"
                                                    id="label">
                                                @foreach($taskLabels as $label)
                                                    <option data-color="{{$label->color}}"
                                                            value="{{$label->id}}">{{$label->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('label')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="priority" class="form-label">Select Priority<span
                                                    class="text-danger">*</span></label>
                                            <select id="priority" name="priority" required class="form-select"
                                                    data-placeholder="Select Priority">
                                                <option
                                                    value="">{{_trans('keyword.Select').' '._trans('keyword.Priority')}}</option>
                                                <option value="low" {{old('priority') == "low"?'selected':''}}>Low
                                                </option>
                                                <option value="medium" {{old('priority') == "medium"?'selected':''}}>
                                                    Medium
                                                </option>
                                                <option value="high" {{old('priority') == "high"?'selected':''}}>High
                                                </option>
                                                <option value="urgent" {{old('priority') == "urgent"?'selected':''}}>
                                                    Urgent
                                                </option>
                                            </select>
                                            @error('priority')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="attachments">Attachments</label>
                                            <input type="file" name="attachment" class="form-control" id="attachments"/>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Description</label>
                                            <div class="form-control p-0 pt-1">
                                                <div class="commonEditor-toolbar border-0 border-bottom">
                                                    <div class="d-flex justify-content-start">
                                                <span class="ql-formats me-0">
                                                    <button class="ql-bold"></button>
                                                    <button class="ql-italic"></button>
                                                    <button class="ql-underline"></button>
                                                    <button class="ql-list" value="ordered"></button>
                                                    <button class="ql-list" value="bullet"></button>
                                                    <button class="ql-link"></button>
                                                    {{-- <button class="ql-image"></button> --}}
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="commonEditor border-0 pb-4" id="descriptionCreate"></div>
                                                <input id="create_description" class="d-none" name="description"
                                                       type="text">
                                            </div>
                                            <span class="text-danger descriptionError error"></span>
                                        </div>
                                        <div class="d-flex flex-wrap">
                                            <button type="submit" class="btn btn-primary me-3">
                                                Create
                                            </button>
                                            <button type="button" class="btn btn-label-danger"
                                                    data-bs-dismiss="offcanvas">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>


                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
        @endsection

        @push('scripts')

            <script>
                (async function () {
                    let boards;
                    const kanbanSidebar = document.querySelector('.kanban-update-item-sidebar'),
                        kanbanCreateSidebar = document.querySelector('.kanban-create-item-sidebar'),
                        kanbanWrapper = document.querySelector('.kanban-wrapper'),
                        kanbanAddNewBoard = document.querySelector('.kanban-add-new-board'),
                        kanbanAddNewInput = [].slice.call(document.querySelectorAll('.kanban-add-board-input')),
                        kanbanAddBoardBtn = document.querySelector('.kanban-add-board-btn'),
                        datePicker = document.querySelector('#due-date'),
                        duedatePicker = document.querySelector('#edit_due-date'),
                        datePickerCreate = document.querySelector('#due-date-create'),
                        select2 = $('.select2'), // ! Using jquery vars due to select2 jQuery dependency
                        assetsPath = document.querySelector('html').getAttribute('data-assets-path');

                    // Init kanban Offcanvas
                    const kanbanOffcanvas = new bootstrap.Offcanvas(kanbanSidebar);
                    const kanbanCreateOffcanvas = new bootstrap.Offcanvas(kanbanCreateSidebar);

                    // Get kanban data
                    const kanbanResponse = await fetch('{{route('project-management.project.task.kanban',$project->id)}}');

                    if (!kanbanResponse.ok) {
                        console.error('error', kanbanResponse);
                    }
                    boards = await kanbanResponse.json();
                    boards = boards.data;


                    if (datePickerCreate) {
                        datePickerCreate.flatpickr({
                            monthSelectorType: 'static',
                            altInput: true,
                            altFormat: 'j F, Y',
                            dateFormat: 'Y-m-d'
                        });
                    }


                    if (select2.length) {

                        initSelect2Design();

                        function renderLabels(option) {
                            if (!option.id) {
                                return option.text;
                            }
                            var badge = `<div class='badge rounded-pill' style="color: ${$(option.element).data('color')}; background-color:${$(option.element).data('color')}20" > ` + option.text + '</div>';
                            return badge;
                        }

                        function initSelect2Design() {
                            select2.each(function () {
                                var $this = $(this);
                                $this.wrap("<div class='position-relative'></div>").select2({
                                    placeholder: 'Select Label',
                                    dropdownParent: $this.parent(),
                                    templateResult: renderLabels,
                                    templateSelection: renderLabels,
                                    escapeMarkup: function (es) {
                                        return es;
                                    }
                                });
                            });
                        }

                    }

                    // Render board dropdown
                    function renderBoardDropdown() {
                        return (
                            "<div class='dropdown'>" +
                            "<i class='dropdown-toggle ti ti-dots-vertical cursor-pointer' id='board-dropdown' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'></i>" +
                            "<div class='dropdown-menu dropdown-menu-end' aria-labelledby='board-dropdown'>" +
                            "<a class='dropdown-item delete-board' href='javascript:void(0)'> <i class='ti ti-trash ti-xs me-1' ></i> <span class='align-middle'>Delete</span></a>" +
                            '</div>' +
                            '</div>'
                        );
                    }

                    // Render item dropdown
                    function renderDropdown(task_id) {
                        return (
                            "<div class='dropdown kanban-tasks-item-dropdown'>" +
                            "<i class='dropdown-toggle ti ti-dots-vertical' id='kanban-tasks-item-dropdown' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'></i>" +
                            "<div class='dropdown-menu dropdown-menu-end' aria-labelledby='kanban-tasks-item-dropdown'>" +
                            @if(hasPermission('update_task'))
                                "<a class='dropdown-item text-primary task_edit_id' data-id='" + task_id + "' href='javascript:void(0)'>Edit</a>" +
                            @endif
                                @if(hasPermission('delete_task'))
                                "<a class='dropdown-item delete-task text-danger' href='javascript:void(0)'>Delete</a>" +
                            @endif
                                '</div>' +
                            '</div>'
                        );
                    }

                    // Render header
                    function renderHeader(color, text, task_id) {
                        return (
                            `<div class='d-flex justify-content-between flex-wrap align-items-center mb-2 pb-1'>
                                <div class='item-badges'>
                                <div class='badge rounded-pill' style='color:${color};background-color: ${color}20' >${text}</div>
                                </div>${renderDropdown(task_id)}</div>`
                        );
                    }

                    // Render avatar
                    function renderAvatar(images, pullUp, size, margin, members) {
                        var $transition = pullUp ? ' pull-up' : '',
                            $size = size ? 'avatar-' + size + '' : '',
                            member = members == undefined ? ' ' : members.split(',');

                        return images == undefined
                            ? ' '
                            : images
                                .split(',')
                                .map(function (img, index, arr) {
                                    if (img !== '') {
                                        var $margin = margin && index !== arr.length - 1 ? ' me-' + margin + '' : '';

                                        return (
                                            "<div class='avatar " +
                                            $size +
                                            $margin +
                                            "'" +
                                            "data-bs-toggle='tooltip' data-bs-placement='top'" +
                                            "title='" +
                                            member[index] +
                                            "'" +
                                            '>' +
                                            "<img src='" + img +
                                            "' alt='Avatar' class='rounded-circle " +
                                            $transition +
                                            "'>" +
                                            '</div>'
                                        );
                                    }
                                })
                                .join(' ');
                    }

                    // Render footer
                    function renderFooter(attachments, comments, assigned, members) {
                        return (
                            "<div class='d-flex justify-content-between align-items-center flex-wrap mt-2 pt-1'>" +
                            "<div class='d-flex'> <span class='d-flex align-items-center me-2'><i class='ti ti-paperclip ti-xs me-1'></i>" +
                            "<span class='attachments'>" +
                            attachments +
                            '</span>' +
                            "</span> <span class='d-flex align-items-center ms-1'><i class='ti ti-message-dots ti-xs me-1'></i>" +
                            '<span> ' +
                            comments +
                            ' </span>' +
                            '</span></div>' +
                            "<div class='avatar-group d-flex align-items-center assigned-avatar'>" +
                            renderAvatar(assigned, true, 'xs', null, members) +
                            '</div>' +
                            '</div>'
                        );
                    }

                    // Init kanban
                    const kanban = new jKanban({
                        element: '.kanban-wrapper',
                        gutter: '15px',
                        widthBoard: '250px',
                        dragItems: true,
                        boards: boards,
                        dragBoards: false,
                        addItemButton: true,
                        buttonContent: '+ Add Item',
                        itemAddOptions: {
                            enabled: @if(hasPermission('create_task')) true @else false @endif, // add a button to board for easy item creation
                            content: '+ New Item', // text or html content of the board button
                            class: 'kanban-title-button btn', // default class of the button
                            footer: false // position the button on footer
                        },
                        click: function (el) {
                            let taskID = $(el).data('eid');
                            window.location.replace('/project-management/project/task/' + taskID + '/details');
                        },

                        dropEl: function (el, target, source, sibling) {

                            let task_id = $(el).data('eid');
                            let status_id = $(target).parent().data('id');
                            changeStatus(task_id, status_id);
                        },

                        buttonClick: function (el, boardId) {
                            $('#create_task_status_id').val(boardId);
                            kanbanCreateOffcanvas.show();
                        }
                    });

                    // Kanban Wrapper scrollbar
                    if (kanbanWrapper) {
                        new PerfectScrollbar(kanbanWrapper);
                    }

                    const kanbanContainer = document.querySelector('.kanban-container'),
                        kanbanTitleBoard = [].slice.call(document.querySelectorAll('.kanban-title-board')),
                        kanbanItem = [].slice.call(document.querySelectorAll('.kanban-item'));

                    // Render custom items
                    if (kanbanItem) {
                        kanbanItem.forEach(function (el) {

                            let taskID = $(el).data('eid');
                            const element = "<span class='kanban-text'>" + el.textContent + '</span>';
                            let img = '';
                            if (el.getAttribute('data-image') !== null) {
                                img =
                                    "<img class='img-fluid rounded mb-2' src='" +
                                    el.getAttribute('data-image') +
                                    "'>";
                            }
                            el.textContent = '';
                            if (el.getAttribute('data-badge') !== undefined && el.getAttribute('data-badge-text') !== undefined) {
                                el.insertAdjacentHTML(
                                    'afterbegin',
                                    renderHeader(el.getAttribute('data-badge'), el.getAttribute('data-badge-text'), taskID) + img + element
                                );
                            }
                            if (
                                el.getAttribute('data-comments') !== undefined ||
                                el.getAttribute('data-due-date') !== undefined ||
                                el.getAttribute('data-assigned') !== undefined
                            ) {
                                el.insertAdjacentHTML(
                                    'beforeend',
                                    renderFooter(
                                        el.getAttribute('data-attachments'),
                                        el.getAttribute('data-comments'),
                                        el.getAttribute('data-assigned'),
                                        el.getAttribute('data-members')
                                    )
                                );
                            }
                        });
                    }

                    // To initialize tooltips for rendered items
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    // prevent sidebar to open onclick dropdown buttons of tasks
                    const tasksItemDropdown = [].slice.call(document.querySelectorAll('.kanban-tasks-item-dropdown'));
                    if (tasksItemDropdown) {
                        tasksItemDropdown.forEach(function (e) {
                            e.addEventListener('click', function (el) {
                                el.stopPropagation();
                            });
                        });
                    }

                    // Toggle add new input and actions add-new-btn
                    if (kanbanAddBoardBtn) {
                        kanbanAddBoardBtn.addEventListener('click', () => {
                            kanbanAddNewInput.forEach(el => {
                                el.value = '';
                                el.classList.toggle('d-none');
                            });
                        });
                    }

                    // Render add new inline with boards
                    if (kanbanContainer) {
                        kanbanContainer.appendChild(kanbanAddNewBoard);
                    }

                    // Makes kanban title editable for rendered boards
                    if (kanbanTitleBoard) {
                        kanbanTitleBoard.forEach(function (elem) {

                            elem.addEventListener('mouseenter', function () {
                                this.contentEditable = 'true';
                            });

                            // Appends delete icon with title
                            elem.insertAdjacentHTML('afterend', renderBoardDropdown());
                        });
                    }

                    // To delete Board for rendered boards
                    const deleteBoards = [].slice.call(document.querySelectorAll('.delete-board'));
                    if (deleteBoards) {
                        deleteBoards.forEach(function (elem) {
                            elem.addEventListener('click', async function () {
                                const id = this.closest('.kanban-board').getAttribute('data-id');
                                let status = await deleteTaskStatus(id);
                                if (status) kanban.removeBoard(id);
                            });
                        });
                    }

                    // Delete task for rendered boards
                    const deleteTask = [].slice.call(document.querySelectorAll('.delete-task'));
                    if (deleteTask) {
                        deleteTask.forEach(function (e) {
                            e.addEventListener('click', function () {
                                const id = this.closest('.kanban-item').getAttribute('data-eid');
                                var formData = new FormData();
                                formData.append('task_id', id);
                                formData.append('_token', "{{ csrf_token() }}");
                                $.ajax({
                                    url: '{{route('project-management.project.task.destroy',$project->id)}}',
                                    type: 'POST',
                                    data: formData,
                                    contentType: 'multipart/form-data',
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (response) {
                                        kanban.removeElement(id);
                                        toastr.success(response.message);
                                    },
                                    error: function (error) {
                                        toastr.error(error.responseJSON.message);
                                    }
                                });

                            });
                        });
                    }

                    // Cancel btn add new input
                    const cancelAddNew = document.querySelector('.kanban-add-board-cancel-btn');
                    if (cancelAddNew) {
                        cancelAddNew.addEventListener('click', function () {
                            kanbanAddNewInput.forEach(el => {
                                el.classList.toggle('d-none');
                            });
                        });
                    }

                    // Clear comment editor on close
                    kanbanSidebar.addEventListener('hidden.bs.offcanvas', function () {
                        kanbanSidebar.querySelector('.ql-editor').firstElementChild.innerHTML = '';
                    });

                    // Re-init tooltip when offcanvas opens(Bootstrap bug)
                    if (kanbanSidebar) {
                        kanbanSidebar.addEventListener('shown.bs.offcanvas', function () {
                            const tooltipTriggerList = [].slice.call(kanbanSidebar.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                        });
                    }


                    $('#create_task').on('submit', function (e) {
                        e.preventDefault();
                        $('#create_description').val($('#descriptionCreate').children(':first-child').html());
                        e.currentTarget.submit();
                    });

                    $('.task_edit_id').on('click', function (e) {
                        let task_id = $(this).data('id');
                        getTask(task_id);
                    });

                    function getTask(task_id) {

                        kanbanOffcanvas.show();
                        $.ajax({
                            url: '/project-management/project/task/' + task_id,
                            type: 'GET',
                            success: function (response) {
                                $('#task_id').val(response.id);
                                $('#edit_title').val(response.title);
                                const dateTime = response.due_date;
                                const formattedDate = dateTime.split(" ")[0];
                                $('#edit_due-date').val(formattedDate);
                                // datepicker init
                                if (duedatePicker) {
                                    duedatePicker.flatpickr({
                                        monthSelectorType: 'static',
                                        altInput: true,
                                        altFormat: 'j F, Y',
                                        dateFormat: 'Y-m-d'
                                    });
                                }
                                $('#edit_label').find('option:selected').attr("selected", false);
                                $('#edit_label').find('option[value="' + response.task_label_id +
                                    '"]').attr("selected", "selected");
                                initSelect2Design();

                                $('#edit_priority').find('option:selected').attr("selected", false);
                                $('#edit_priority').find('option[value="' + response.priority +
                                    '"]').attr("selected", "selected");

                                $('#edit_description').children(':first-child').html(response.description);


                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    }

                    $('#update_task').on('submit', function (e) {
                        e.preventDefault();
                        $('#update_description').val($('#edit_description').children(':first-child').html());
                        e.currentTarget.submit();
                    });

                    async function changeStatus(task_id, status_id) {

                        var formData = new FormData();
                        formData.append('task_id', task_id);
                        formData.append('status_id', status_id);
                        formData.append('_token', "{{ csrf_token() }}");
                        $.ajax({
                            url: '{{route('project-management.project.task.changeStatus',$project->id)}}',
                            type: 'POST',
                            data: formData,
                            contentType: 'multipart/form-data',
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (response) {

                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    }

                    async function changeStatusSerialNumber(id, order) {

                        var formData = new FormData();
                        formData.append('status_id', id);
                        formData.append('order', order);
                        formData.append('_token', "{{ csrf_token() }}");
                        $.ajax({
                            url: '{{route('project-management.essentials.task.status.updateSerial')}}',
                            type: 'POST',
                            data: formData,
                            contentType: 'multipart/form-data',
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                toastr.success(response.message);
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                                location.reload();
                            }
                        });
                    }

                    async function deleteTaskStatus(id) {
                        let status = true;
                        var formData = new FormData();
                        formData.append('status_id', id);
                        formData.append('_token', "{{ csrf_token() }}");

                        await $.ajax({
                            url: '{{route('project-management.essentials.task.status.destroy')}}',
                            type: 'POST',
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                if (response.title === 'error') {
                                    toastr.error(response.text);
                                    status = false;
                                } else {
                                    toastr.success(response.text);
                                }
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                                status = false;
                            }
                        });
                        return status;
                    }

                })();
            </script>

    @endpush
