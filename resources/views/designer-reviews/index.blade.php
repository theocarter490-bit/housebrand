@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Shop Reviews'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Shop Reviews'), [
            '#' => _trans('keyword.Shop Reviews'),
            'review type' =>_trans('keyword.Reviews'),
        ]) !!}

        <div class="col-lg-12 col-md-12 mb-4">
            <form method="GET" class="mb-2" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-2 mb-lg-0 mb-3 ">
                        <div class="input-effect">
                            <input
                                class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                type="text" placeholder="Search here" name="search" value="{{ request('search') }}"
                                autocomplete="off">
                        </div>
                    </div>

{{--                    <div class="col-lg-2 col-md-3 col-sm-6 mb-lg-0 mb-3 ">--}}
{{--                        <div class="input-control">--}}
{{--                            <select class="w-100 bb form-control height-50 select2" style="width: 100%"--}}
{{--                                    aria-label="Select Type"--}}
{{--                                    name="category_type">--}}
{{--                                <option value=""--}}
{{--                                        selected>{{ _trans('keyword.Select') }} {{ _trans('keyword.Category') }}</option>--}}
{{--                                @foreach ($reviews as $category)--}}
{{--                                    <option--}}
{{--                                        value="{{ $category->id }}" {{ request('category_type') == $category->id ? 'selected' : '' }}>--}}
{{--                                        {{ $category->name }}--}}
{{--                                    </option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="col-lg-2 col-md-3 col-sm-6 mb-lg-0 mb-3 ">
                        <div class="input-control">
                            <select class="w-100 bb form-control height-50 select2" style="width: 100%"
                                    aria-label="Select Type"
                                    name="status_type" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">>
                                <option selected></option>
                                <option
                                    value="1" {{ request('status_type') != '' && (int)request('status_type') == 1 ? 'selected' : '' }}>{{ _trans('keyword.Published') }}</option>
                                <option
                                    value="0" {{ request('status_type') != '' && (int)request('status_type') == 0 ? 'selected' : '' }}>{{ _trans('keyword.Unpublished') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-6 mb-lg-0 mb-3">
                        <div class="input-control">
                            <select class="form-control filter_dropdown  type-select-1 select2"
                                    style="width: 100%"
                                    aria-label="Select Type"
                                    name="type"
                                    data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Type') }}">
                                <option selected></option>

                                @foreach($reviewTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ request('type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 mb-lg-0 ">
                        <button class="btn btn-primary" type="submit">{{ _trans('keyword.Submit') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="app-ecommerce-category">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{ _trans('keyword.SL') }}</th>
                            <th>{{ _trans('keyword.Shop Info.') }}</th>
                            <th>{{ _trans('keyword.Reviewer') }}</th>
                            <th>{{ _trans('keyword.Review') }}</th>
                            <th>{{ _trans('keyword.Rating') }}</th>
                            <th>{{ _trans('keyword.Type') }}</th>
                            <th>{{ _trans('keyword.Date') }}</th>
                            <th>{{ _trans('keyword.Status') }}</th>
{{--                            <th width="100px">{{ _trans('keyword.Action') }}</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($reviews as $key => $review)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if ($review->designer && $review->designer->shop)
                                        <div class="d-flex align-items-center gap-2">
                                            <div>
                                                <img src="{{ getFilePath($review->designer->shop->logo) }}"
                                                     alt="{{ $review->designer->shop->shop_name }}"
                                                     style="width: 50px; height: 50px;">
                                            </div>
                                            <div>
                                                <a target="_blank" href="{{env('APP_FRONTEND_URL'). '/designer/'. $review->designer->shop->slug}}"><strong>{{ $review->designer->shop->shop_name }}</strong><br></a>
                                                <p style="margin: 0">{{ $review->designer->shop->email }}</p>
                                                <p>{{ $review->designer->shop->phone }}</p>
                                            </div>
                                        </div>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if ($review->customer)
                                        <div class="d-flex align-items-center gap-2">
                                            <div>
                                                <img src="{{ getFilePath($review->customer->avatar) }}"
                                                     alt="{{ $review->customer->name }}"
                                                     style="width: 50px; height: 50px; border-radius: 100%;">
                                            </div>
                                            <div>
                                                <a target="_blank" href="{{route('user.profile', $review->customer->id)}}">
                                                    <strong>{{ $review->customer->name }}</strong><br>
                                                </a>
                                                <p style="margin: 0">{{ $review->customer->email }}</p>
                                                <p style="margin: 0">{{ $review->customer->phone }}</p>
                                            </div>
                                        </div>
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <p data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $review->review }}">
                                        {{ \Illuminate\Support\Str::limit($review->review, 100, '...') }}
                                    </p>
                                </td>
                                <td>
                                    <div class="rating rating-sm mt-1">
                                        {!! renderStarRating($review->rating) !!}
                                    </div>
                                    <p style="margin: 0">
                                        {!! _trans('keyword.Rating') . ': <span class="fw-bold">' . $review->rating . '</span>' !!}
                                    </p>
                                </td>
                                <td>{{$review->reviewType->name}}</td>
                                <td>{{ dateFormatwithTime($review->created_at) }}</td>
                                <td>

                                    <div class="d-flex flex-column">
                                       @if(hasPermission('shop_reviews_change_status'))
                                            <label class="switch switch-success" style="margin-bottom: 5px;">
                                                <input type="checkbox" class="switch-input changeStatus"
                                                       data-id="{{$review->id}}"
                                                       @if($review->active_status) checked @endif>
                                                <span class="switch-toggle-slider">
                                            <span class="switch-on">
                                                <i class="ti ti-check"></i>
                                                </span>
                                                <span class="switch-off">
                                                    <i class="ti ti-x"></i>
                                                </span>
                                            </span>
                                            </label>
                                       @endif
                                        <span style="width: fit-content" class="d-inline-block badge {{ $review->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger' }}">
                                        {{ $review->active_status == 1 ? 'Published' : 'Unpublished' }}
                                    </span>
                                    </div>
                                </td>
{{--                                <td>--}}
{{--                                    <div class="d-inline-block text-nowrap">--}}
{{--                                        <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="box-shadow: none">--}}
{{--                                            <i class="ti ti-dots-vertical"></i>--}}
{{--                                        </button>--}}
{{--                                        <div class="dropdown-menu dropdown-menu-end">--}}
{{--                                            @if (hasPermission('notice_type_update'))--}}
{{--                                                <a href="#" class="dropdown-item type_edit_button" data-id="{{ $review->id }}">--}}
{{--                                                    <i class="ti ti-edit"></i> Edit--}}
{{--                                                </a>--}}
{{--                                            @endif--}}
{{--                                            @if (hasPermission('notice_type_delete'))--}}
{{--                                                <a href="#" class="dropdown-item text-danger type_delete_button" data-id="{{ $review->id }}">--}}
{{--                                                    <i class="ti ti-trash"></i> Delete--}}
{{--                                                </a>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center">No Review Data</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $reviews->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>


@endsection

@push('scripts')
    <script>
        $(function() {

            $('.select2').select2({
                allowClear: true,
            });

            $(document).on('change', '.changeStatus', function() {
                const id = $(this).data('id');
                const formData = new FormData();
                formData.append('id', id);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to change the status of this?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it',
                    cancelButtonText: 'No, cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('review.changeStatus') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    location.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
