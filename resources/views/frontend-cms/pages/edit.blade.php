@extends('layouts.master')

@section('title', $title ?? 'Edit ' . $data->title)

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Edit').' '._trans('keyword.Page'), ['#' => 'Frontend CMS', '/cms/pages' => 'Pages', _trans('keyword.Edit').' '._trans('keyword.Page')]) !!}
        <div class="app-ecommerce">
            <form method="post" id="pageUpdateForm">
                @csrf
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-tile mb-0">{{_trans('keyword.Update')}} {{ $data->title }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">{{_trans('keyword.Page').' '._trans('keyword.Title')}}
                                        <span class="text-danger">*</span></label>
                                    <input required type="text" class="form-control" value="{{ $data->title }}"
                                           name="title" id="title" aria-label="Page title" placeholder="page title"/>
                                    <input id="page_id" hidden type="text" value="{{ $data->id }}">
                                    <span class="text-danger titleError error"></span>
                                </div>

                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                           for="category-org">
                                        <span> {{_trans('keyword.Footer').' '._trans('keyword.Widget')}}</span>
                                    </label>
                                    <select id="footer_widget" name="footer_widget" class="select2 form-select"
                                            style="width: 100%;"
                                            data-placeholder="Select Category">
                                        @foreach ($footer_widget as $widget)
                                            <option value="{{ $widget->id }}"
                                                {{ $data->footer_widget_id == $widget->id ? 'selected' : '' }}>
                                                {{ $widget->title }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                {{-- Short Description --}}
                                <div>
                                    <div class="mb-3">
                                        <label class="form-label">{{_trans('keyword.Short Description')}}<span
                                                class="text-danger">*</span></label>
                                        <textarea required id="short_desc" class="form-control"
                                                  placeholder="short description" name="short_description"
                                                  cols="30" rows="5">{{ $data->short_desc }}</textarea>
                                        <span class="text-danger shortDescError error"></span>
                                    </div>
                                </div>

                                <!-- Page Content -->
                                <div class="mb-4">
                                    <label class="form-label">{{_trans('keyword.Content')}}</label>
                                    <div class="form-control p-0 pt-1">
                                        <textarea class="ckeditor form-control" id="content"
                                                  name="content"> {{ $data->content }}</textarea>
                                    </div>
                                    <span class="text-danger descriptionError error"></span>
                                </div>

                                @if(strtolower(trim($data->title)) == 'about us')
                                    <!-- Our Mission -->
                                    <div class="mb-4">
                                        <label class="form-label">{{_trans('keyword.Our Mission')}}</label>
                                        <div class="form-control p-0 pt-1">
                                            <textarea class="ckeditor form-control" id="mission"
                                                      name="mission"> {{ $data->our_mission }}</textarea>
                                        </div>
                                        <span class="text-danger descriptionError error"></span>
                                    </div>
                                @endif


                                @if ($data->slug === 'team')
                                    @include('frontend-cms.pages.team')
                                @endif

                                @if ($data->slug === 'about-us')
                                    @include('frontend-cms.pages.about-us')
                                @endif




                                <!-- Status -->
                                <div class="mb-4 ecommerce-select2-dropdown">
                                    <label
                                        class="form-label">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</label>
                                    <select id="status" name="status" class="select2 form-select"
                                            style="width: 100%"
                                            data-placeholder="Select status">
                                        <option
                                            value="1" {{ $data->active_status == 1 ? 'selected' : '' }}>{{_trans('keyword.Active')}}
                                        </option>
                                        <option
                                            value="0" {{ $data->active_status == 0 ? 'selected' : '' }}>{{_trans('keyword.Inactive')}}
                                        </option>
                                    </select>
                                    <span class="text-danger statusError error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <button type="button" id="pageUpdateButton"
                                class="btn btn-primary col-4">{{_trans('keyword.Submit')}}
                            <span class="loader"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        $(document).ready(function () {

            $('#pageUpdateButton').click(function (e) {
                e.preventDefault();

                var formData = new FormData();

                let page_id = $('#page_id').val();
                let title = $("input[name=title]").val();
                let footer_widget = $("#footer_widget option:selected").val();
                let status = $("#status option:selected").val();

                let short_desc = $("textarea[name=short_description]").val();

                let content = CKEDITOR.instances['content'].getData();
                let ourMission = CKEDITOR.instances['mission'].getData();

                formData.append('title', title);
                formData.append('footer_widget', footer_widget);
                formData.append('id', page_id);
                formData.append('short_desc', short_desc);
                formData.append('content_data', content);
                formData.append('our_mission', ourMission);
                formData.append('status', status);
                formData.append('_token', "{{ csrf_token() }}");

                let cards = $('#gallaryImageWithDetailsContainer').find('.gallaryImageWithDetailsCard');

                cards.each(function (index, card) {
                    let inputs = $(card).find('input');
                    inputs.each(function (key, input) {

                        var name = $(input).attr('name');
                        if (name == 'title') {
                            formData.append(
                                `sectionItems[${index}][title]`, $(
                                    input).val());
                        }
                        if (name == 'description') {
                            formData.append(
                                `sectionItems[${index}][description]`,
                                $(
                                    input).val());
                        }
                        if (name == 'image') {
                            formData.append(
                                `sectionItems[${index}][image]`,
                                $(
                                    input).prop('files')[0] ?? '');
                        }
                        if (name == 'id') {
                            formData.append(
                                `sectionItems[${index}][id]`,
                                $(
                                    input).val() ?? null);
                        }

                    });

                });

                loader.show();
                submitButton.prop('disabled', true);


                $('.error').text('')
                $.ajax({
                    url: '{{ route('cms.pages.update') }}',
                    type: 'POST',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        if (response.status === 403) {
                            $('.titleError').text(response.errors?.title ? response.errors
                                .title[0] : '');
                            $('.shortDescError').text(response.errors?.short_desc ? response
                                .errors.short_desc[0] : '');
                        } else if (response.status === 200) {
                            window.location.href = "{{ route('cms.pages.index') }}";
                            toastr.success(response.message);
                            $('#closeUpdateModal').click();
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
        });

    </script>
@endpush
