@extends('layouts.master')

@section('title', $title ?? _trans('keyword.General').' '._trans('keyword.Settings'))

@section('content')

    {{-- @include('assets.layouts.breadcrumb' , ['title' => @$title], ['breadcrumb'=>'dashboard']) --}}

    <div class="col-12 mb-4">
        {!! breadcrumb( _trans('keyword.General').' '._trans('keyword.Settings') ,['#'=>_trans('keyword.System').' '._trans('keyword.Settings') ,'setting'=>_trans('keyword.General').' '._trans('keyword.Settings')]) !!}
        <div class="bs-stepper vertical wizard-vertical-icons-example mt-2">
            <div class="bs-stepper-header">
                <div class="step active" data-target="#system-info-setting">
                    <button type="button" class="step-trigger" role="tab" aria-controls="system-info-setting" id="system-info-setting-trigger" aria-selected="true">
                        <span class="bs-stepper-circle">
                            <i class="ti ti-settings"></i>
                        </span>
                        <span class="bs-stepper-label text-wrap" style="word-break: break-word;">
                            <span class="bs-stepper-title">{{_trans('keyword.System Info')}}</span>
                            <span class="bs-stepper-subtitle">{{_trans('keyword.Setup').' '._trans('keyword.System Info')}}</span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="bs-stepper-content">
                <!-- System Info -->
                <div id="system-info-setting" class="content active dstepper-block" role="tabpanel" aria-labelledby="system-info-setting-trigger">
                    <form method="POST" id="system-info-setting-form">
                        @csrf
                        <div class="content-header mb-3">
                            <h6 class="mb-0">{{_trans('keyword.System').' '._trans('keyword.Info')}}</h6>
                            <small>{{_trans('keyword.Enter').' '._trans('keyword.Your').' '._trans('keyword.System').' '._trans('keyword.Info')}}</small>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="siteName">{{_trans('keyword.Site').' '._trans('keyword.Name')}}</label>
                                <input type="text" id="siteName" name="siteName" class="form-control"
                                    placeholder="House Brand" value="{{ $setting->site_name }}" />

                                <span class="text-danger siteNameError error"></span>

                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="siteTitle">{{_trans('keyword.Site').' '._trans('keyword.Title')}}</label>
                                <input type="text" id="siteTitle" name="siteTitle" class="form-control"
                                    value="{{ $setting->site_title }}"
                                    placeholder="House Brand" aria-label="" />
                                <span class="text-danger siteTitleError error"></span>

                            </div>

                            <div class="col-sm-6">
                                <label class="form-label" for="timeZone">{{_trans('keyword.Time Zone')}}</label>
                                <select class="select2" id="timeZone" name="timeZone" style="width: 100%">
                                    @foreach ($time_zones as $time_zone)
                                        <option value="{{ $time_zone->id }}"
                                            @if ($setting->time_zone_id == $time_zone->id) selected @endif>{{ $time_zone->time_zone }}
                                        </option>
                                    @endforeach
                                </select>

                                <span class="text-danger timeZoneError error"></span>

                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="currence">{{_trans('keyword.Currency')}}</label>
                                <select class="select2" id="currence" name="currence" style="width: 100%">
                                    @foreach ($currences as $currence)
                                        <option value="{{ $currence->id }}"
                                            @if ($setting->currency_id == $currence->id) selected @endif>{{ $currence->symbol }}
                                            {{ $currence->name }}</option>
                                    @endforeach
                                </select>

                                <span class="text-danger currenceError error"></span>

                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="dateFormat">{{_trans('keyword.Date Format')}}</label>
                                <select class="select2" id="dateFormat" name="dateFormat" style="width: 100%">
                                    @foreach ($date_formats as $date_format)
                                        <option value="{{ $date_format->id }}"
                                            @if ($setting->date_format_id == $date_format->id) selected @endif>{{ $date_format->format }}
                                        </option>
                                    @endforeach
                                </select>

                                <span class="text-danger dateFormatError error"></span>

                            </div>

                            <div class="col-sm-12">
                                <label class="form-label" for="copyRightText">{{_trans('keyword.Copy Right Text')}}</label>
                                <div class="input-group input-group-merge">
                                    <textarea id="copy_right" name="copy_right" class="form-control" placeholder="2025 copyright reserved"
                                        rows="3">{{ $setting->copyright_text }}</textarea>
                                </div>

                                <span class="text-danger copy_rightError error"></span>

                            </div>
                            @if(hasPermission('general_settings_update'))
                                <div class="col-12 d-flex justify-content-end">
                                    <button class="btn btn-primary" type="submit">
                                        <span class="align-middle  me-sm-1">{{_trans('keyword.Submit')}}</span>
                                        <span class="loader"></span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection


@push('scripts')
    {{-- form submit ajax --}}

    <script>
        $('#system-info-setting-form').on('submit', function(e) {
            e.preventDefault();

            loader.show();
            submitButton.prop('disabled', true);

            $('.error').text('');
            $.ajax({
                url: '{{ route('setting.general-setting.system_info.store') }}',
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    // element = $('input[name="element_name"]');
                    siteName: $('#siteName').val(),
                    siteTitle: $('#siteTitle').val(),
                    // address: $('#address').val(),
                    // phone: $('#phone').val(),
                    // email: $('#email').val(),
                    timeZone: $('#timeZone').val(),
                    currence: $('#currence').val(),
                    dateFormat: $('#dateFormat').val(),
                    // map_location: $('#map_location').val(),
                    copy_right: $('#copy_right').val(),
                },
                success: function(response) {
                    if (response.status == 403) {
                        $('.siteNameError').text(response.errors?.siteName ? response.errors?.siteName[
                            0] : '');
                        $('.siteTitleError').text(response.errors?.siteTitle ? response.errors
                            ?.siteTitle[0] : '');
                        // $('.addressError').text(response.errors?.address ? response.errors?.address[0] :
                        //     '');
                        // $('.phoneError').text(response.errors?.phone ? response.errors?.phone[0] : '');
                        // $('.emailError').text(response.errors?.email ? response.errors?.email[0] : '');
                        $('.timeZoneError').text(response.errors?.timeZone ? response.errors?.timeZone[
                            0] : '');
                        $('.currenceError').text(response.errors?.currence ? response.errors?.currence[
                            0] : '');
                        $('.dateFormatError').text(response.errors?.dateFormat ? response.errors
                            ?.dateFormat[0] : '');
                        // $('.map_locationError').text(response.errors?.map_location ? response.errors
                        //     ?.map_location[0] : '');
                        $('.copy_rightError').text(response.errors?.copy_right ? response.errors
                            ?.copy_right[0] : '');
                    } else if (response.status == 200) {
                        toastr.success(response.message);
                    }
                },
                error: function(error) {
                    toastr.error(error.message);
                },
                complete: function() {
                    loader.hide();
                    submitButton.prop('disabled', false);
                }


            });
        });


    </script>




@endpush

