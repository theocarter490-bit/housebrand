@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Cache Setting'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('', [
            '#' => _trans('keyword.General') . ' ' . _trans('keyword.Settings'),
            'setting/cache-setup' => _trans('keyword.Cache') . ' ' . _trans('keyword.Setting'),
        ]) !!}

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div
                        class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="ti ti-database-zap me-2"></i>Cache Setting
                        </h5>
                        <button class="btn btn-label-danger cache-clear-btn" data-name="all">
                            <i class="ti ti-trash me-1"></i> Clear All Cache
                        </button>
                    </div>
                    <div class="card-body mt-4">
                        <div class="row g-4">
                            @php
                                $caches = [
                                    ['name' => 'product_list', 'label' => 'Products', 'icon' => 'ti-package', 'color' => 'primary'],
                                    ['name' => 'slider_list', 'label' => 'Sliders', 'icon' => 'ti-slideshow', 'color' => 'info'],
                                    ['name' => 'special_section_list', 'label' => 'Inspiration', 'icon' => 'ti-bulb', 'color' => 'warning'],
                                    ['name' => 'special_section_list', 'label' => 'Portfolio', 'icon' => 'ti-briefcase', 'color' => 'success'],
                                    ['name' => 'gallery_list', 'label' => 'Gallery', 'icon' => 'ti-photo', 'color' => 'secondary'],
                                    ['name' => 'blog_post_list', 'label' => 'Blog', 'icon' => 'ti-article', 'color' => 'danger'],
                                ];
                            @endphp

                            @foreach($caches as $cache)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card h-100 border border-light-subtle hover-shadow-sm transition-all">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar me-3">
                                                    <span class="avatar-initial rounded bg-label-{{ $cache['color'] }}">
                                                        <i class="ti {{ $cache['icon'] }} fs-4"></i>
                                                    </span>
                                                </div>
                                                <h6 class="mb-0">{{ $cache['label'] }}</h6>
                                            </div>
                                            <p class="text-muted small">Clear cached data specifically for
                                                the {{ strtolower($cache['label']) }} module.</p>
                                            <button class="btn btn-outline-{{ $cache['color'] }} w-100 cache-clear-btn"
                                                    data-name="{{ $cache['name'] }}">
                                                <i class="ti ti-refresh me-1"></i> Clear Cache
                                            </button>
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

    <style>
        .transition-all {
            transition: all 0.3s ease;
        }

        .hover-shadow-sm:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .bg-label-primary {
            background-color: #e7e7ff !important;
            color: #696cff !important;
        }

        /* Add labels for other colors as needed if your theme doesn't have them */
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.cache-clear-btn').on('click', function () {
                let $btn = $(this);
                let cacheType = $btn.data('name');
                let originalHtml = $btn.html();

                // UI Feedback
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

                $.ajax({
                    url: "{{route('setting.cacheSetup.cacheClear')}}", // Update with your actual route
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        type: cacheType
                    },
                    success: function (response) {
                        toastr.success(response.success);
                    },
                    error: function (xhr) {
                        toastr.error('Something went wrong. Please try again.');
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });
        });
    </script>
@endpush
