@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Project'))

@push('styles')
    <style>
        .hover-top {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .hover-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .1) !important;
        }

        .bg-glass-primary {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Project').' '. _trans('keyword.List'),['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),'category'=> _trans('keyword.Project').' '. _trans('keyword.List')]) !!}

        <div class="flex-grow-1 container-p-y">
            <div class="app-academy">
                <div class="card p-0 mb-4">
                    <div
                        class="card-body d-flex flex-column flex-xl-row justify-content-between align-items-center p-4">

                        <div class="d-none d-xl-block" style="width: 20%;">
                            <img src="{{asset('/assets/img/illustrations/bulb-light.png')}}"
                                 class="img-fluid scaleX-n1-rtl"
                                 alt="Bulb in hand"
                                 style="max-height: 160px;"/>
                        </div>

                        <div class="d-flex flex-column align-items-center text-center w-100" style="max-width: 800px;">
                            <h3 class="card-title mb-3 lh-sm">
                                Project, Task and Business Opportunity.
                                <span class="text-primary fw-medium text-nowrap">All in one place</span>.
                            </h3>
                            <p class="mb-4 text-muted">
                                Effortlessly manage projects, tasks, and business opportunities with a platform designed
                                for clarity, efficiency, and progress.
                            </p>

                            <form action="{{ route('project-management.project.index') }}" method="GET" class="w-100">
                                <div class="row g-3 justify-content-center">

                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="ecommerce-select2-dropdown position-relative">
                                            <select id="project-status" name="status"
                                                    class="form-control select2 project-status-filter w-100"
                                                    data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
                                                <option value=""></option>
                                                @foreach($projectStatus as $status)
                                                    <option
                                                        value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                                        {{ $status->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger statusError error d-none"></span>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="ecommerce-select2-dropdown position-relative">
                                            <select id="project-category" name="category"
                                                    class="form-control select2 project-category-filter w-100"
                                                    data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Category') }}">
                                                <option value=""></option>
                                                @foreach($projectCategories as $category)
                                                    <option
                                                        value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger categoryError error d-none"></span>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <input type="search" name="search" value="{{ request('search') }}"
                                               placeholder="Find your project" class="form-control w-100"/>
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-auto d-flex gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-primary w-100 w-lg-auto">
                                            <i class="ti ti-search me-1"></i>Filter
                                        </button>

                                        @if(request()->hasAny(['status', 'category', 'search']))
                                            <a href="{{ route('project-management.project.index') }}"
                                               class="btn btn-outline-secondary w-100 w-lg-auto">
                                                <i class="ti ti-refresh me-1"></i>Reset
                                            </a>
                                        @endif
                                    </div>

                                </div>
                            </form>
                        </div>

                        <div class="d-none d-xl-flex align-items-end justify-content-end" style="width: 20%;">
                            <img src="{{asset('assets/img/illustrations/pencil-rocket.png')}}"
                                 alt="pencil rocket"
                                 class="img-fluid scaleX-n1-rtl"
                                 style="max-height: 188px;"/>
                        </div>

                    </div>
                </div>


                <div class="card mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between gap-3">
                        <div class="card-title mb-0 me-1">
                            <h5 class="mb-1">My Projects</h5>
                        </div>
                        @if(hasPermission('add_project') && (getUserId() !==1))
                            <div class="d-flex justify-content-md-end align-items-center gap-3 flex-wrap">
                                <a href="{{route('project-management.project.create')}}" class="btn btn-primary"><i
                                        class="ti ti-plus ti-xs me-0 me-sm-2"></i>Add Project</a>
                            </div>
                        @endif

                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            @forelse($projects as $project)
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm d-flex flex-column">
                                        <!-- Banner Section -->
                                        <div class="position-relative overflow-hidden" style="height: 160px;">
                                            <img
                                                class="w-100 h-100"
                                                src="{{getFilePath($project->banner)}}"
                                                alt="{{$project->title}}"
                                                style="object-fit: cover;"
                                            />
                                            <div
                                                class="position-absolute top-0 end-0 p-2 d-flex gap-2 flex-wrap justify-content-end">
                                                <span
                                                    class="badge bg-label-primary">{{@$project->category->name}}</span>
                                                <span class="badge"
                                                      style="background-color: {{@$project->status->color}}; color: white;">
                        {{@$project->status->name}}
                    </span>
                                                @if($project->active_status == 1)
                                                    <span class="badge bg-label-success">Active</span>
                                                @else
                                                    <span class="badge bg-label-danger">Inactive</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Card Body -->
                                        <div class="card-body pb-3 d-flex flex-column flex-grow-1">
                                            <!-- Title -->
                                            <h5 class="card-title mb-2">{{$project->title}}</h5>

                                            <!-- Description - Fixed height of 40px (2 lines) -->
                                            <div style="height: 40px; margin-bottom: 12px;">
                                                @if ($project->description != null)
                                                    <p class="card-text text-muted small m-0"
                                                       style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                        {!! \Illuminate\Support\Str::limit($project->description, 150) !!}
                                                    </p>
                                                @else
                                                    <p class="card-text text-muted small m-0">N/A</p>
                                                @endif
                                            </div>

                                            <!-- Manager & Client -->
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-muted d-block">Manager</small>
                                                    <img src="{{getFilePath(@$project->manager->avatar)}}"
                                                         alt="{{@$project->manager->name}}"
                                                         title="{{@$project->manager->name}}"
                                                         width="32"
                                                         height="32"
                                                         class="rounded-circle"
                                                    />
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-muted d-block">Client</small>
                                                    <img src="{{getFilePath(@$project->client->avatar)}}"
                                                         alt="{{@$project->client->name}}"
                                                         title="{{@$project->client->name}}"
                                                         width="32"
                                                         height="32"
                                                         class="rounded-circle"
                                                    />
                                                </div>
                                            </div>

                                            <!-- Timeline & Budget Side by Side -->
                                            <div class="row d-flex flex-row justify-content-evenly align-items-center mb-3">
                                                <!-- Timeline -->
                                                <div class="col-6">
                                                    <div class="d-flex align-items-start gap-2">
                                                        <div class="avatar rounded bg-label-primary">
                                                            <div class="avatar-initial">
                                                                <i class="ti ti-calendar-event"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 min-width-0">
                                                            <small class="text-muted d-block">Timeline</small>
                                                            <small class="fw-semibold d-block" style="font-size: 11px;">
                                                                {{isSet($project->start_date) ? dateFormat($project->start_date) : 'N/A'}}
                                                                @if(isSet($project->end_date))
                                                                    <br>{{dateFormat($project->end_date)}}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Budget -->
                                                <div class="col-6 d-flex justify-content-end">
                                                    <div class="d-flex align-items-start gap-2">
                                                        <div class="avatar rounded bg-label-primary">
                                                            <div class="avatar-initial">
                                                                <i class="ti ti-currency-dollar"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 min-width-0">
                                                            <small class="text-muted d-block">Budget</small>
                                                            <small
                                                                class="fw-semibold d-block">{{getPriceFormat($project->total_cost)}}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Progress -->
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <small class="text-muted">Progress</small>
                                                    <small class="fw-semibold">{{$project->progressValue}}%</small>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar" role="progressbar"
                                                         style="width: {{$project->progressValue}}%;"
                                                         aria-valuenow="{{$project->progressValue}}" aria-valuemin="0"
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Spacer to push buttons to bottom -->
                                            <div class="flex-grow-1"></div>

                                            <!-- Actions - Always at bottom -->
                                            <div class="d-flex gap-2 pt-3 border-top">
                                                @if ($project->user_id == getUserId())
                                                    @if(hasPermission('update_projects'))
                                                        <a class="btn btn-sm btn-label-secondary flex-grow-1"
                                                           href="{{route('project-management.project.edit',$project->id)}}">
                                                            <i class="ti ti-edit me-1"></i>Edit
                                                        </a>
                                                    @endif
                                                @endif

                                                @if(hasPermission('project_overview'))
                                                    <a class="btn btn-sm btn-label-primary flex-grow-1"
                                                       href="{{ route('project-management.project.overview',$project->id) }}">
                                                        <span>View</span>
                                                        <i class="ti ti-chevron-right ms-1"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body text-center py-5">
                                            <img src="{{asset('assets/img/illustrations/girl-doing-yoga-light.png')}}"
                                                 alt="No projects found"
                                                 class="img-fluid mb-4"
                                                 style="max-width: 250px; opacity: 0.7;"
                                            />
                                            <h4 class="text-muted mb-3">{{ _trans('keyword.No') }} {{ _trans('keyword.Project') }} {{ _trans('keyword.Found') }}</h4>
                                            <p class="text-muted">
                                                @if(request()->hasAny(['status', 'category', 'search']))
                                                    {{ _trans('keyword.No') }} {{ _trans('keyword.Project') }} match
                                                    your current filter criteria. Try adjusting your filters.
                                                @else
                                                    You haven't created any projects yet. Start by creating your first
                                                    project.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        @if($projects->count() > 0)
                            <nav aria-label="Page navigation" class="d-flex align-items-center justify-content-center">
                                {{ $projects->links('pagination::bootstrap-5') }}
                            </nav>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2 dropdowns following the system pattern
            $('.project-status-filter').select2({
                allowClear: true,
            });

            $('.project-category-filter').select2({
                allowClear: true,
            });

            $('form').on('submit', function () {
                $('.error').addClass('d-none').text('');
                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Filtering...');
            });

            $('.project-status-filter').on('select2:select select2:clear', function () {
                $('.statusError').addClass('d-none').text('');
            });

            $('.project-category-filter').on('select2:select select2:clear', function () {
                $('.categoryError').addClass('d-none').text('');
            });

        });
    </script>
@endpush
