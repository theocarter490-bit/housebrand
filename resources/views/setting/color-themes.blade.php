@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Color') . ' ' . _trans('keyword.Themes'))
<style>
    .visually-hidden {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .color-palette-wrapper {
        position: relative;
        display: block;
        border-radius: 10px;
        transition: transform 0.2s ease;
        width: 100%;
    }

    .color-palette-label {
        display: block;
        border: 2px solid #bdbdbd;
        border-radius: 10px;
        padding: 2px;
        transition: border 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        width: 100%;
    }

    .color-palette-wrapper:hover .color-palette-label {
        transform: translateY(-2px);
    }

    .form-check-input:checked + .color-palette-label {
        border-color: var(--color-primary);
        /* box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25); */
    }

    .color-plate {
        display: flex;
        width: 100%;
        height: 110px;
        border-radius: 8px;
        overflow: hidden;
    }

    .color {
        flex: 1;
    }
</style>

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Color') . ' ' . _trans('keyword.Themes'), [
            '#' => _trans('keyword.System') . ' ' . _trans('keyword.Settings'),
            '/setting/color-themes' => _trans('keyword.Color') . ' ' . _trans('keyword.Themes'),
        ]) !!}
        <div class="app-ecommerce-category">

            <div class="justify-content-between d-flex gap-2">
                <div class="col-lg-10 col-md-10">
                    <form method="GET" class="mb-2" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-effect">
                                    <input
                                            class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                            type="search" placeholder="Title" name="search" value="{{ @$search }}"
                                            autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-control">
                                    <select
                                            class="w-100 bb  form-control height-50 select2 form-select2 filter_dropdown"
                                            id="active_status"
                                            style="width: 100%"
                                            data-placeholder="Select Status"
                                            name="active_status">
                                        <option
                                                value="">{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}</option>
                                        <option
                                                value="1" {{$active_status === '1'? "selected":""}}>{{ _trans('keyword.Enable') }}</option>
                                        <option
                                                value="0" {{$active_status === '0'? "selected":""}}>{{ _trans('keyword.Disable') }}</option>
                                    </select>

                                </div>

                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                                <div class="input-control">
                                    <select class="w-100 form-control height-50 select2 form-select2 filter_dropdown"
                                            style="width: 100%"
                                            id="panel_status"
                                            data-placeholder="Select Panel"
                                            name="panel_status">
                                        <option
                                                value="">{{ _trans('keyword.Select') }} {{ _trans('keyword.Panel') }}</option>
                                        <option
                                                value="0" {{$panel_status === '0'? "selected":""}}>{{ _trans('keyword.Frontend') }}</option>
                                        <option
                                                value="1" {{$panel_status === '1'? "selected":""}}>{{ _trans('keyword.Backend') }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="mb-2">
                    <button type="button" class="btn btn-primary  mt-auto text-nowrap" data-bs-toggle="modal"
                            data-bs-target="#addColorThemeModal" aria-controls="leaveTypeList">+ Theme
                    </button>
                </div>
            </div>

            <!-- Color Themes List Table -->
            <div class="row color-theme-list-wrapper">
                <div
                        class="col-12 col-lg-6 {{ $panel_status === '1' ? 'd-none' : '' }} {{ auth()->user()->role_id == App\Models\Role::MANUFACTURER ? 'd-none' : '' }}">
                    <div class="card h-100" style="background-color:var(--color-bg-primary); padding:0 16px;">
                        <h3 class="py-3 m-0">Frontend:</h3>
                        <div class="row">

                            @foreach ($data['frontend'] as $index => $card)
                                <div class="col-12 col-sm-6 col-xl-4 mb-4">
                                    <div
                                            class="custom-card-frontend d-flex flex-column justify-content-end single-color-card"
                                            style="{{ $card->active_status == 1 ? 'border: 3px solid' . $card->primary . ';' : '' }}">
                                        @if ($card->active_status == 1)
                                            <div class="card-badge" style="background-color: {{$card->primary}};">
                                                Enabled
                                            </div>
                                        @endif
                                        <div class="inner-card d-flex flex-column">
                                            <div class="card-header">
                                                <div class="window-controls">
                                                    <div class="control-dot red-dot"></div>
                                                    <div class="control-dot yellow-dot"></div>
                                                    <div class="control-dot green-dot"></div>
                                                </div>
                                            </div>
                                            <div class="ds-card ds-card-primary">
                                                <!-- Card Header -->
                                                <div class="ds-card-header"
                                                     style="background-color: {{ $card->secondary }}; border-color: {{ $card->primary }};">
                                                    <div class="ds-header-group">
                                                        <div class="ds-header-item ds-header-item-sm"></div>
                                                        <div class="ds-header-item ds-header-item-sm"></div>
                                                    </div>
                                                    <div class="ds-header-item ds-header-item-md"></div>
                                                    <div class="ds-header-group">
                                                        <div class="ds-header-item ds-header-item-sm"></div>
                                                        <div class="ds-header-item ds-header-item-sm"></div>
                                                    </div>
                                                </div>

                                                <!-- Card Body -->
                                                <div class="ds-card-body">
                                                    <div class="ds-field ds-field-full"></div>
                                                    <div class="ds-field ds-field-md"
                                                         style="background-color: {{ $card->secondary }}"></div>
                                                    <div class="ds-field ds-field-sm"
                                                         style="background-color: {{ $card->primary }}"></div>
                                                </div>

                                                <!-- Card Footer -->
                                                <div class="ds-card-footer">
                                                    <div class="ds-box"
                                                         style="border-color: {{ $card->secondary }};">
                                                        <div class="ds-box-item ds-box-item-sm"></div>
                                                        <div class="ds-box-item ds-box-item-md"
                                                             style="background-color: {{ $card->bg_primary }};"></div>
                                                    </div>
                                                    <div class="ds-box"
                                                         style="border-color: {{ $card->secondary }};">
                                                        <div class="ds-box-item ds-box-item-sm"></div>
                                                        <div class="ds-box-item ds-box-item-md"
                                                             style="background-color: {{ $card->bg_primary }};"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                                class="card-footer d-flex justify-content-center gap-2 align-items-center editButton">
                                            <h5>{{$card->name}}</h5>
                                            @if (hasPermission('color_themes_apply'))
                                                @if ($card->active_status != 1)
                                                    <a href="javascript:0;"
                                                       class="btn btn-info fs-6 theme_apply_button px-2"
                                                       data-id="{{$card->id}}"><i class="fa-solid fa-check"></i>
                                                        Apply</a>
                                                @endif
                                            @endif
                                            @if (hasPermission('color_themes_delete'))
                                                @if ($card->theme_status != 0 && $card->active_status != 1)
                                                    <a href="javascript:0;"
                                                       class="btn btn-danger fs-6 theme_delete_button px-2"
                                                       data-id="{{$card->id}}"><i class="fa-solid fa-trash"></i> Delete</a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 {{ $panel_status === '0'?'d-none':'' }}">
                    <div class="card h-100" style="background-color:var(--color-bg-primary); padding:0 16px;">
                        <h3 class="py-3 m-0">Admin Panel:</h3>
                        <div class="row">
                            @foreach ($data['backend'] as $card)
                                <div class="col-12 col-sm-6 col-xl-4 mb-4">
                                    <div class="custom-card d-flex flex-column justify-content-end single-color-card"
                                         style="{{ $card->active_status == 1 ? 'border: 3px solid' . $card->primary . ';' : '' }}">
                                        @if ($card->active_status == 1)
                                            <div class="card-badge" style="background-color: {{$card->primary}};">
                                                Enabled
                                            </div>
                                        @endif
                                        <div class="inner-card d-flex flex-column"
                                             style="background-color: {{ $card->bg_primary }}">
                                            <div class="card-header">
                                                <div class="window-controls">
                                                    <div class="control-dot red-dot"></div>
                                                    <div class="control-dot yellow-dot"></div>
                                                    <div class="control-dot green-dot"></div>
                                                </div>
                                            </div>
                                            <div class="px-1"
                                                 style="background-color: {{ $card->primary }}">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="inner-avatar"></div>
                                                        <div class="width-line-1"></div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="width-line-2"></div>
                                                        <div>x</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row h-100">
                                                <div class="col-md-3">
                                                    <div class="sidebar"
                                                         style="background-color:{{ $card->bg_secondary }}"></div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="content-area p-2">
                                                        <div class="bg-white rounded mb-2"
                                                             style="height: 8px; width: 60px;"></div>
                                                        <div class="bg-white rounded" style="height: 4px; width: 48px;">
                                                        </div>
                                                        <div class="d-flex align-items-center gap-1 mt-2">
                                                            <div class="button-1"></div>
                                                            <div class="button-2"
                                                                 style="background-color: {{ $card->bg_primary }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="right-site-sidebar"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="right-site-sidebar"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="card-footer align-items-center editButton">
                                            <h5>{{$card->name}}</h5>
                                            @if (hasPermission('color_themes_apply'))
                                                @if ($card->active_status != 1)
                                                    <a href="javascript:0;"
                                                       class="btn btn-info px-2 fs-6 theme_apply_button"
                                                       data-id="{{$card->id}}"><i class="fa-solid fa-check"></i>
                                                        Apply</a>
                                                @endif
                                            @endif
                                            @if (hasPermission('color_themes_delete'))
                                                @if ($card->theme_status != 0 && $card->active_status != 1)
                                                    <a href="javascript:0;"
                                                       class="btn btn-danger px-2 fs-6 theme_delete_button"
                                                       data-id="{{$card->id}}"><i class="fa-solid fa-trash"></i> Delete</a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Color Theme Modal -->
    <div id="addColorThemeModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h3 class="role-title mb-3">
                            {{ _trans('keyword.Add') . ' ' . _trans('keyword.Color') . ' ' . _trans('keyword.Theme') }}
                        </h3>
                    </div>
                    <form action="{{ route('setting.color-themes.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <label for="theme_name"
                                           class="form-label">{{ _trans('keyword.Theme') . ' ' . _trans('keyword.Name') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="theme_name" name="theme_name"
                                           placeholder="Theme name" required>
                                    @error('theme_name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1" for="status-org">{{ _trans('keyword.Type') }}</label>
                                    <select id="type" name="type" class="select2 form-select">
                                        @if (auth()->user()->role_id == App\Models\Role::SUPER_ADMIN || auth()->user()->role_id == App\Models\Role::DESIGNER)
                                            <option value="0" selected>{{ _trans('keyword.Frontend') }}</option>
                                        @endif
                                        <option value="1">{{ _trans('keyword.Admin Panel') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1"
                                           for="status-org">{{ _trans('keyword.Style') }}</label>
                                    <select id="style" name="style" class="select2 form-select">
                                        <option value="0" selected>{{ _trans('keyword.Color Palette') }}</option>
                                        <option value="1">{{ _trans('keyword.Custom') }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="colorPelleteDiv ">
                                <div class="d-flex row">
                                    @foreach ($colorPalettes as $index => $color)
                                        <div class="col-12 col-sm-6 col-md-3">
                                            <div
                                                    class="custom-option-basic position-relative color-palette-wrapper m-1">
                                                <input
                                                        name="color_palette"
                                                        class="form-check-input visually-hidden"
                                                        type="radio"
                                                        value="{{ $color->id }}"
                                                        id="color{{ $color->id }}"
                                                />
                                                <label class="color-palette-label" for="color{{ $color->id }}">
                                                    <div class="color-plate">
                                                        <div class="color"
                                                             style="background: {{ $color->primary }}"></div>
                                                        <div class="color"
                                                             style="background: {{ $color->secondary }}"></div>
                                                        <div class="color"
                                                             style="background: {{ $color->bg_primary }}"></div>
                                                        <div class="color"
                                                             style="background: {{ $color->bg_secondary }}"></div>
                                                        <div class="color"
                                                             style="background: {{ $color->text_primary }}"></div>
                                                        <div class="color"
                                                             style="background: {{ $color->text_secondary }}"></div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>


                            <div class="row customColorDiv">
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="primary"
                                               class="form-label">{{ _trans('keyword.Primary Color') }}</label>
                                        <input type="color" class="form-control" id="primary"
                                               name="primary">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="secondary"
                                               class="form-label">{{ _trans('keyword.Secondary Color') }}</label>
                                        <input type="color" class="form-control" id="secondary"
                                               name="secondary">
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="btn_bg_primary"
                                               class="form-label">{{ _trans('keyword.Primary Background Color') }}</label>
                                        <input type="color" class="form-control" id="bg_primary"
                                               name="bg_primary">
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="bg_secondary"
                                               class="form-label">{{ _trans('keyword.Secondary Background Color') }}</label>
                                        <input type="color" class="form-control" id="bg_secondary"
                                               name="bg_secondary">
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="text_primary"
                                               class="form-label">{{ _trans('keyword.Primary Text Color') }}</label>
                                        <input type="color" class="form-control" id="text_primary"
                                               name="text_primary">
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="text_secondary"
                                               class="form-label">{{ _trans('keyword.Secondary Text Color') }}</label>
                                        <input type="color" class="form-control" id="text_secondary"
                                               name="text_secondary">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 text-center mt-3">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Save') }}
                                    <span class="loader"></span>
                                </button>
                                <button id="reset" type="reset" class="btn btn-label-secondary"
                                        data-bs-dismiss="modal" aria-label="Close">
                                    {{ _trans('keyword.Cancel') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Color Theme Modal -->


@endsection

@push('scripts')
    <script>
        $(function () {
            @foreach($errors->all() as $error)
            toastr.error('{!! $error !!}');
            @endforeach

            $(".theme_delete_button").on("click", function () {

                let color_theme_id = $(this).attr("data-id");
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
                            url: '{{ route('setting.color-themes.delete') }}',
                            method: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: color_theme_id,
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: 'success',
                                    showConfirmButton: false,
                                    title: 'Delete Successful',
                                    text: response.text,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });

                                // reload
                                window.location.reload();
                            },
                            error: function (error) {
                                console.log(error.responseJSON.message);
                            }
                        });
                    }
                });
            });

            $(document).on("click", ".theme_apply_button", function () {
                let color_theme_id = $(this).attr("data-id");

                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Make it Default',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {

                        $.ajax({
                            url: '{{ route('setting.color-themes.apply') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: color_theme_id,
                            },
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    window.location.reload();
                                }
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        }).then(response => {
                            if (response.status === 200) {
                                return response;
                            } else {
                                throw new Error(response.message || 'Something went wrong');
                            }
                        }).catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error.responseJSON?.message || error.message}`
                            );
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            });

            $('#style').on('change', function () {
                if ($(this).val() === '0') {
                    $('.colorPelleteDiv').show();
                    $('.customColorDiv').hide();
                } else {
                    $('.colorPelleteDiv').hide();
                    $('.customColorDiv').show();
                }
            });
            $('.customColorDiv').hide();

            $('.filter_dropdown').select2({
                allowClear: true,
            });

        });
    </script>
@endpush
