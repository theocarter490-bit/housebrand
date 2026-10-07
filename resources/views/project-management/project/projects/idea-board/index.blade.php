@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Idea Board'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb( _trans('keyword.Idea Board'),['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),'Idea Board'=>   _trans('keyword.Idea Board')]) !!}

        <div class="app-ecommerce-category">
            {!! projectTabMenu($project, 'idea-board', $project->id) !!}
            <!-- Category List Table -->
            <div class="d-flex flex-row justify-content-end mb-2">
                <a class="add-new btn btn-primary ms-2 waves-effect waves-light text-nowrap text-white" data-bs-toggle="offcanvas"
                   data-bs-target="#offcanvasEcommerceCategoryList">
                    <i class="ti ti-plus ti-xs me-0 me-sm-2"></i>
                    Add Idea Board
                </a>
            </div>

            <div class="row row-cols-1 row-cols-md-4 g-4">
                @foreach($ideaBoard as $board)
                    <div class="col d-flex"> <!-- make column a flex container -->
                        <div class="card flex-fill h-100 d-flex flex-column"> <!-- stretch card to full height -->
                            <img src="{{ getFilePath($board->image) }}"
                                 style="max-height: 190px; overflow: hidden"
                                 class="card-img-top"
                                 alt="Hollywood Sign on The Hill"/>

                            <div class="card-body d-flex flex-column flex-grow-1">
                                <h5 class="card-title">{{ $board->title }}</h5>
                                <p class="card-text flex-grow-1">
                                    {{ substr($board->description, 0, 130) }}
                                    @if(strlen($board->description) > 130)
                                        ....
                                    @endif
                                </p>

                                <div class="d-flex align-items-center gap-2 mt-auto">
                                    <div class="avatar">
                                        <div class="avatar-initial rounded bg-label-primary">
                                            <i class='ti ti-currency-dollar ti-md'></i>
                                        </div>
                                    </div>
                                    <div class="gap-0 d-flex flex-column mb-2">
                                        <p class="mb-0 fw-medium">
                                            {{ getPriceFormat($board->items_sum_price) }}/{{ getPriceFormat($board->budget) }}
                                        </p>
                                        <small>Budget</small>
                                    </div>
                                </div>

                                <div class="progress mt-2">
                                    <div class="progress-bar"
                                         role="progressbar"
                                         style="width: {{ round(($board->items_sum_price / $board->budget) * 100) }}%;
                                    @if(round(($board->items_sum_price / $board->budget) * 100) > 90) background-color: darkred; @endif"
                                         aria-valuenow="{{ round(($board->items_sum_price / $board->budget) * 100) }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ round(($board->items_sum_price / $board->budget) * 100) }}%
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer mt-auto">
                                <div class="d-flex flex-row align-items-center justify-content-between">
                                    <div>
                                        <button class="rounded btn-label-warning category_edit_button"
                                                data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvasCategoryEditModal"
                                                data-id="{{ $board->id }}">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button class="rounded btn-label-danger category_delete_button"
                                                data-id="{{ $board->id }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <a href="{{ route('project-management.project.idea-board.details', [$project->id, $board->id]) }}"
                                           class="rounded btn btn-label-primary">
                                            <i class="ti ti-home-edit me-1"></i>Preview
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{ $ideaBoard->links() }}
            </div>

            <!-- Offcanvas to add new customer -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCategoryList"
                 aria-labelledby="offcanvasEcommerceCategoryListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Add')}} {{_trans('keyword.Idea Board')}}</h5>
                    <button type="button" id="closeAddModal" class="btn-close bg-label-secondary text-reset"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="addModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="ecommerce-category-title">{{_trans('keyword.Area Name')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ecommerce-category-title"
                                   placeholder="Enter name" name="name" aria-label="category title"/>
                            <span class="text-danger nameError error"></span>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image"> {{_trans('keyword.Image')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" name="image" id="category-image"/>
                            <span class="text-danger imageError error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"
                                   for="ecommerce-category-title">{{_trans('keyword.Budget')}} {{getCurrency()}} <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="ecommerce-category-title"
                                   placeholder="Enter Value" name="budget" aria-label="category title"/>
                            <span class="text-danger budgetError error"></span>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">{{_trans('keyword.Description')}}</label>
                            <textarea name="description" class="form-control" id="category-descripton" cols="30"
                                      rows="10"></textarea>
                            <span class="text-danger descriptionError error"></span>
                        </div>
                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select')}} {{_trans('keyword.Category')}} {{_trans('keyword.Status')}}</label>
                            <select id="category-status" name="status" class="select2 form-select"
                                    data-placeholder="Select category status">
                                <option value="1" selected>{{_trans('keyword.Active')}}</option>
                                <option value="0">{{_trans('keyword.Inactive')}}</option>
                            </select>
                            <span class="text-danger statusError error"></span>
                        </div>
                        <!-- Submit and reset -->
                        <div class="mb-3">
                            <button type="submit"
                                    class="btn btn-primary me-sm-3 me-1 data-submit">{{_trans('keyword.Add')}}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" class="btn bg-label-danger"
                                    data-bs-dismiss="offcanvas">{{_trans('keyword.Discard')}}</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Offcanvas to edit category -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCategoryEditModal"
                 aria-labelledby="offcanvasEcommerceCategoryListLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-4">
                    <h5 id="offcanvasEcommerceCategoryListLabel"
                        class="offcanvas-title">{{_trans('keyword.Edit')}} {{_trans('keyword.Idea Board')}}</h5>
                    <button type="button" id="closeEditModal" class="btn-close bg-label-secondary text-reset"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <form class="pt-0" id="updateCategoryModal" method="POST">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="edit_name">{{_trans('keyword.Area Name')}} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" placeholder="Enter category name"
                                   name="edit_name" aria-label="category title"/>
                            <input type="text" hidden value="" name="category_id" id="category_id">
                            <span class="text-danger editNameError error"></span>
                        </div>

                        <!-- Image -->
                        <div class="mb-3" id="currentImageSection">
                            <img src="" id="currentImage" alt="" width="60px" height="60px">
                        </div>
                        <!-- Image -->
                        <div class="mb-3">
                            <label class="form-label"
                                   for="category-image"> {{_trans('keyword.Image')}}
                                <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" name="edit_image" id="edit_image"/>
                            <span class="text-danger editImageError error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"
                                   for="edit_budget">{{_trans('keyword.Budget')}} {{getCurrency()}} <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_budget"
                                   placeholder="Enter Value" name="edit_budget" aria-label="category title"/>
                            <span class="text-danger editbudgetError error"></span>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">{{_trans('keyword.Description')}}</label>
                            <textarea name="edit_description" class="form-control" id="edit_description" cols="30"
                                      rows="10"></textarea>
                            <span class="text-danger editDescriptionError error"></span>
                        </div>
                        <!-- Status -->
                        <div class="mb-4 ecommerce-select2-dropdown">
                            <label
                                class="form-label">{{_trans('keyword.Select')}} {{_trans('keyword.Category')}} {{_trans('keyword.Status')}}</label>
                            <select id="edit_status" name="edit_status" class="select2 form-select"
                                    data-placeholder="Select category status">
                                <option value="1">{{_trans('keyword.Active')}}</option>
                                <option value="0">{{_trans('keyword.Inactive')}}</option>
                            </select>
                            <span class="text-danger editStatusError error"></span>
                        </div>
                        <!-- Submit and reset -->
                        <div class="mb-3">
                            <button type="submit"
                                    class="btn btn-primary me-sm-3 me-1 data-submit">{{_trans('keyword.Update')}}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" class="btn bg-label-danger"
                                    data-bs-dismiss="offcanvas">{{_trans('keyword.Discard')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(function () {


            $('#addModal').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let name = $("input[name=name]").val();
                let budget = $("input[name=budget]").val();
                let description = $("#category-descripton").val();
                let status = $("#category-status option:selected").val();
                var image = $('#category-image').prop('files')[0] ?? '';

                formData.append('title', name);
                formData.append('description', description);
                formData.append('status', status);
                formData.append('image', image);
                formData.append('budget', budget);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('project-management.project.idea-board.store',$project->id) }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        if (response.status == 403) {
                            $('.nameError').text(response.errors?.title ? response.errors
                                ?.title[0] : '');
                            $('.imageError').text(response.errors?.image ? response.errors
                                ?.image[0] : '');
                            $('.descriptionError').text(response.errors?.description ? response
                                    .errors
                                    ?.description[0] :
                                '');
                            $('.statusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                            $('.budgetError').text(response.errors?.budget ? response.errors
                                    ?.budget[0] :
                                '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                            location.reload();

                            $("input[name=name]").val('');
                            $("input[name=budget]").val('');
                            $("#category-descripton").val('');
                            $('#category-image').val('');

                            $('#closeAddModal').click();

                        }
                    },
                    error: function (errors) {
                        $('.nameError').text(errors.responseJSON.errors?.title ? errors.responseJSON
                            .errors?.title[0] : '');
                        $('.imageError').text(errors.responseJSON.errors?.image ? errors.responseJSON
                            .errors?.image[0] : '');
                        $('.descriptionError').text(errors.responseJSON.errors?.description ? errors
                            .responseJSON.errors
                            ?.description[0] : '');
                        $('.statusError').text(errors.responseJSON.errors?.status ? errors.responseJSON
                                .errors?.status[0] :
                            '');
                        $('.budgetError').text(errors.responseJSON.errors?.budget ? errors.responseJSON
                                .errors?.budget[0] :
                            '');
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });

            $(document).on("click", ".category_edit_button", function () {
                $id = $(this).attr("data-id");
                $('#edit_status').find('option:selected').attr("selected", false);
                $('#edit_status').trigger('change.select2');
                $.ajax({
                    url: '/project-management/project/idea-board/' + {{$project->id}} + "/edit/" + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#category_id').val(response.data.id);
                        $('#edit_name').val(response.data.name);
                        $('#edit_budget').val(response.data.budget);
                        $('#edit_description').val(response.data.description);
                        $('#edit_status').find('option[value="' + response.data.active_status +
                            '"]').attr("selected", "selected");

                        $("#currentImage").attr("src", ``);

                        if (response.data.image != null) {
                            $("#currentImage").attr("src", `${response.data.image}`);
                        }

                        $('#edit_status').trigger('change.select2');

                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $('#updateCategoryModal').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let category_id = $('#category_id').val();
                let name = $("input[name=edit_name]").val();
                let budget = $("input[name=edit_budget]").val();
                let description = $("#edit_description").val();
                let status = $("#edit_status option:selected").val();
                var image = $('#edit_image').prop('files')[0] ?? '';

                formData.append('title', name);
                formData.append('budget', budget);
                formData.append('description', description);
                formData.append('status', status);
                formData.append('image', image);
                formData.append('idea_board_id', category_id);
                formData.append('_token', "{{ csrf_token() }}");

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('project-management.project.idea-board.update',$project->id) }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        if (response.status == 403) {
                            $('.editNameError').text(response.errors?.title ? response.errors
                                ?.title[0] : '');
                            $('.editbudgetError').text(response.errors?.budget ? response.errors
                                ?.budget[0] : '');

                            $('.editImageError').text(response.errors?.image ? response.errors
                                ?.image[0] : '');
                            $('.editDescriptionError').text(response.errors?.description ?
                                response
                                    .errors
                                    ?.description[0] :
                                '');
                            $('.editStatusError').text(response.errors?.status ? response.errors
                                    ?.status[0] :
                                '');
                        } else if (response.status == 200) {
                            location.reload();
                            toastr.success(response.message);
                            $('#closeEditModal').click();
                            $('#edit_image').val('');
                        }
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });
            });


            $(document).on("click", ".category_delete_button", function () {

                let id = $(this).attr("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {

                        $.ajax({
                            url: '{{ route('project-management.project.idea-board.destroy',$project->id) }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                idea_board_id: id,
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: response.icon,
                                    title: response.icon,
                                    text: response.text,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
                                location.reload();
                            },
                            error: function (error) {
                                console.log(error.responseJSON.message);
                                // handle the error case
                            }
                        });
                    }
                });
            });


        });
    </script>
@endpush
