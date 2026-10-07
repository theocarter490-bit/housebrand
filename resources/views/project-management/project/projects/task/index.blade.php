@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Task'))

@push('styles')
    <style>
        .card {
            height: 100%;
        }
        .col-md-4 {
            display: flex;
        }
    </style>
@endpush

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Task') . ' ' . _trans('keyword.List'), [
            '#' => _trans('keyword.Project') . ' ' . _trans('keyword.Management'),
            'category' => _trans('keyword.Task') . ' ' . _trans('keyword.List'),
        ]) !!}

        <div class="flex-grow-1 container-p-y">
            <div class="app-academy">
                <div class="card p-0 mb-4">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-4">
                        <div class="app-academy-md-25 py-0" style="padding: 5px">
                            <img src="{{ asset('/assets/img/illustrations/bulb-light.png') }}"
                                 class="img-fluid app-academy-img-height scaleX-n1-rtl" alt="Bulb in hand"
                                 style="height:160px;"/>
                        </div>
                        <div
                            class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center">
                            <h3 class="card-title mb-4 lh-sm px-md-5 lh-lg">
                                Pick a Project to Manage Tasks
                            </h3>
                            <p class="mb-3">
                                Select a project to explore its tasks, deadlines, and progress updates. Stay organized
                                and
                                manage your workflow efficiently.
                            </p>
                            <form action="{{ route('project-management.project.task.index') }}" method="GET">
                                <div class="d-flex align-items-center justify-content-between app-academy-md-80 gap-3">
                                    <div>
                                        <input type="search" name="search" value="{{ request('search') }}"
                                               placeholder="Find your project" class="form-control me-2"/>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        Filter
                                    </button>

                                </div>
                            </form>

                        </div>
                        <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
                            <img src="{{ asset('assets/img/illustrations/pencil-rocket.png') }}" alt="pencil rocket"
                                 height="188" class="scaleX-n1-rtl"/>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="row gy-4 mb-4">
                        @if(count($projects) == 0)
                            <h1 class="text-center">No projects available</h1>
                        @endif
                            <div class="row d-flex flex-wrap">
                                @foreach ($projects as $project)
                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4 d-flex mt-4">
                                        <a href="{{ route('project-management.project.task', $project->id) }}" class="w-100">
                                            <div class="card mb-3 h-100 w-100">
                                                <div class="row g-0 hover-effect h-100">
                                                    <div class="col-md-4">
                                                        <img class="card-img card-img-left h-100"
                                                             src="{{ getFilePath($project->banner) }}" alt="Project banner"/>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                                                            <div>
                                                                <div class="d-flex justify-content-between">
                                                                    <h5 class="card-title">{{ $project->title }}</h5>
                                                                    <p class="badge h-100"
                                                                       style="background-color: {{ @$project->status->color }}">
                                                                        {{ @$project->status->name }}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center">
                                                                        <label class="me-1">Client:</label>
                                                                        <img src="{{ getFilePath(@$project->client->avatar) }}"
                                                                             alt="Avatar" class="rounded-circle m-2" width="30" height="30">
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <label class="me-1">Manager:</label>
                                                                        <img src="{{ getFilePath(@$project->manager->avatar) }}"
                                                                             alt="Avatar" class="rounded-circle m-2" width="30" height="30">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex justify-content-between mt-2">
                                                                <p class="mb-0 fw-medium">
                                                                    Budget: <span class="text-primary">{{ getPriceFormat($project->total_cost) }}</span>
                                                                </p>
                                                                <p class="mb-0 fw-medium">
                                                                    Date: <small class="text-muted">{{ dateFormat($project->start_date) }}</small>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                    </div>
                    <nav aria-label="Page navigation" class="d-flex align-items-center justify-content-center">
                        {{ $projects->links('pagination::bootstrap-5') }}
                    </nav>
                </div>

            </div>
        </div>
    </div>

@endsection
