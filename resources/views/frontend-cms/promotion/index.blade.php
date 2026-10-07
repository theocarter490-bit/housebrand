@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Promotional').' '._trans('keyword.Settings'))

@section('content')

    {{-- @include('assets.layouts.breadcrumb' , ['title' => @$title], ['breadcrumb'=>'dashboard']) --}}

    <div class="col-12 mb-4">
        {!! breadcrumb( _trans('keyword.Promotion Data').' '._trans('keyword.Settings') ,['#'=>_trans('keyword.Promotion Data').' '._trans('keyword.Settings') ]) !!}
        <div class="bs-stepper vertical wizard-vertical-icons-example mt-2">
            <div class="bs-stepper-content">
                <!-- System Info -->
                <div id="system-info-setting" class="content active">
                    <form method="POST" id="system-info-setting-form">
                        @csrf
                        <div class="content-header mb-3">
                            <h6 class="mb-0">{{_trans('keyword.Promotional').' '._trans('keyword.Info')}}</h6>
                            <small>{{_trans('keyword.Enter').' '._trans('keyword.Your').' '._trans('keyword.Promotional').' '._trans('keyword.Info')}}</small>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="siteName">{{_trans('keyword.Title')}}</label>
                                <input type="text" id="title" name="siteName" class="form-control"
                                       placeholder="House Brand" value="{{ $setting->promotional_title }}" />

                                <span class="text-danger titleError error"></span>

                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="siteTitle">{{_trans('keyword.Short Description')}}</label>
                                <input type="text" id="description" name="siteTitle" class="form-control"
                                       value="{{ $setting->promotional_description }}"
                                       placeholder="House Brand" aria-label="" />
                                <span class="text-danger descriptionError error"></span>

                            </div>

                            <div class="col-sm-12">
                                <label class="form-label" for="copyRightText">{{_trans('keyword.Video link')}}</label>
                                <div class="input-group input-group-merge">
                                    <textarea id="link" name="copy_right" class="form-control" placeholder="https://"
                                              rows="3">{{ $setting->promotional_video_link }}</textarea>
                                </div>

                                <span class="text-danger linkError error"></span>

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
                url: '{{ route('cms.promotion.store') }}',
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    title: $('#title').val(),
                    description: $('#description').val(),
                    link: $('#link').val(),
                },
                success: function(response) {
                    if (response.status == 403) {
                        $('.titleError').text(response.errors?.title ? response.errors?.title[
                            0] : '');
                        $('.descriptionError').text(response.errors?.description ? response.errors
                            ?.description[0] : '');

                        $('.linkError').text(response.errors?.link ? response.errors
                            ?.link[0] : '');
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

