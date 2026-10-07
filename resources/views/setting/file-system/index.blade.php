@extends('layouts.master')

@section('title', $title ?? _trans('keyword.File').' '._trans('keyword.System'))

@section('content')



    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.File').' '._trans('keyword.System'), ['#' => _trans('keyword.System').' '._trans('keyword.Settings'), 'setting/file-system' => _trans('keyword.File').' '._trans('keyword.System')]) !!}

        <div class="row justify-content-center">
            @foreach ($settings as $setting)
                <div class="col-md-6">
                    <div class="card mb-4 ">
                        <h5 class="card-header">{{ $setting->description }}</h5>
                        <div class="card-body">
                            <div class="individual-section{{ $setting->id }}">
                                <div class="row">
                                    @foreach (json_decode($setting->options, true) as $optionValue => $optionLabel)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check custom-option custom-option-basic">
                                                <label class="form-check-label custom-option-content"
                                                    for="customRadioTemp{{ $optionValue }}_{{ $setting->id }}">
                                                    <input name="customRadioTemp_{{ $setting->id }}"
                                                        class="form-check-input"
                                                        onchange="changeStatus('{{ $setting->id }}')" type="radio"
                                                        value="{{ $optionValue }}"
                                                        id="customRadioTemp{{ $optionValue }}_{{ $setting->id }}"
                                                        data-setting-id="{{ $setting->id }}"
                                                        {{ $setting->value == $optionValue ? 'checked' : '' }} />
                                                    <span class="custom-option-header">
                                                        <span class="h6 mb-0">{{ textCapitalize($optionLabel) }}</span>
                                                    </span>
                                                    <span class="custom-option-body">
                                                        <small>Note: {{ textCapitalize($setting->key) }}
                                                            {{ textCapitalize($optionLabel) }}</small>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

                <div class="col-md-6">
                <div class="card">
                    <h5 class="card-header">AWS S3 Credentials</h5>
                    <div class="card-body">
                        <form method="POST" id="system-info-setting-form"
                            action="{{ route('setting.file-system.storeCredentials') }}">
                            @csrf
                            @foreach ($credentials as $key => $value)
                                <div class="mb-3">
                                    <label class="form-label required"
                                        for="{{ $key }}">{{ $key }}</label>
                                    <input type="text" id="AWS_ACCESS_KEY_ID" name="{{ $key }}"
                                        class="form-control" placeholder="{{ $key }}"
                                        value="{{ $value }}" />
                                    @error($key)
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach

                            @if (hasPermission('file_system_update'))
                                <div class="col-12 d-flex justify-content-end">
                                    <button class="btn btn-primary" type="submit">
                                        <span class="align-middle  me-sm-1">{{_trans('keyword.Submit')}}</span>
                                        <span class="loader"></span>
                                    </button>
                                </div>
                            @endif
                        </form>

                    </div>
                </div>
            </div>
        </div>
        @endsection

        @push('scripts')
            <script src="{{ asset('backend/js/backend_custom.js') }}"></script>
        @endpush

