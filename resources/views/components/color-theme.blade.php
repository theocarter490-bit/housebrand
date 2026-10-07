@push('styles')
    <style>
        /* Hide default radio button */
        .visually-hidden {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .color-palette-wrapper {
            position: relative;
            display: inline-block;
            border-radius: 10px;
            transition: transform 0.2s ease;
        }

        .color-palette-label {
            display: block;
            border: 2px solid #bdbdbd;
            border-radius: 10px;
            padding: 2px;
            transition: border 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .color-palette-wrapper:hover .color-palette-label {
            /* border-color: var(--color-primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); */
            transform: translateY(-2px);
        }

        .form-check-input:checked + .color-palette-label {
            border-color: var(--color-primary);
            /* box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25); */
        }

        /* Color plate layout */
        .color-plate {
            display: flex;
            width: 135px;
            height: 110px;
            border-radius: 8px;
            overflow: hidden;
        }

        .color {
            flex: 1;
        }
    </style>
@endpush
<div>
    <div class="flex-grow-1 container-p-y">
        <div class="app-ecommerce-category">
            <!-- Color Themes List Table -->
            <div class="row color-theme-list-wrapper">
                <div class="col-12 {{ auth()->user()->role_id == App\Models\Role::MANUFACTURER ? 'd-none' : '' }}">
                    <div class="card" style="background-color:var(--color-bg-primary); padding:0px 16px;">
                        <h3>Frontend:</h3>
                        <div class="row">

                            @foreach ($data['frontend'] as $index => $card)
                                <div class="col-md-4 mb-4">
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
            </div>
        </div>
    </div>


</div>

@push('scripts')
    <script>
        $(function () {
            @foreach($errors->all() as $error)
            toastr.error('{!! $error !!}');
            @endforeach
            // $(document).on("click", ".theme_edit_button", function () {
            //     console.log('edit button clicked');

            //     $('.error').text('')
            //     $id = $(this).attr("data-id");
            //     $.ajax({
            //         url: '/setting/color-themes/edit/' + $id,
            //         type: 'GET',
            //         success: function (response) {
            //             console.log(response)

            //             let editType = $('#editType');
            //             editType.empty(); // Clear existing options

            //             if (response.data.type == 0) {
            //                 editType.append('<option value="0" selected>{{ _trans('keyword.Frontend') }}</option>');
            //             } else if (response.data.type == 1) {
            //                 editType.append('<option value="1" selected>{{ _trans('keyword.Admin Panel') }}</option>');
            //             }

            //             if (response.data.active_status == 1) {
            //                 editType.prop('disabled', true);
            //             } else {
            //                 editType.prop('disabled', false);
            //             }

            //             $('#themeId').val($id);

            //             $("#editThemeName").val(response.data.name);

            //             $("#edit_primary_color").val(response.data.primary_color);
            //             $("#edit_secondary_color").val(response.data.secondary_color);
            //             $("#edit_background_color").val(response.data.background_color);
            //             $("#edit_btn_background_color").val(response.data.button_bg_color);
            //             $("#edit_btn_text_color").val(response.data.button_text_color);
            //             $("#edit_hover_color").val(response.data.hover_color);
            //             $("#edit_border_color").val(response.data.border_color);
            //             $("#edit_text_color").val(response.data.text_color);
            //             $("#edit_secondary_text_color").val(response.data.secondary_text_color);

            //             $("#edit_shadow_color").val(response.data.shadow_color);
            //             $("#edit_side_background").val(response.data.sidebar_bg);
            //             $("#edit_sidebar_hover").val(response.data.sidebar_hover);

            //             $('#edit_active_status').val(response.data.active_status).trigger('change');
            //             $('#editType').val(response.data.type).trigger('change');

            //         },
            //         error: function (error) {
            //             toastr.error(error.responseJSON.message);
            //         }
            //     });
            // });


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


                                toastr.success(response.text);

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
                if ($(this).val() == '0') {
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
