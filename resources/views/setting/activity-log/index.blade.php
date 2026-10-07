@php use Illuminate\Support\Str; @endphp
@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Activity Log'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Activity Log'), [
            '#' => _trans('keyword.System Settings'),
            'User Report' => _trans('keyword.Activity Log'),
        ]) !!}

        <div class="app-ecommerce-category">
                <div class="col-lg-12 col-md-12">
                    <form method="GET"  action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 mb-4 col-sm-6 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                        type="text" placeholder="Search something" name="search" value="{{ @$search }}"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 mb-4 col-sm-6 mb-20">
                                <div class="input-control">
                                    <select name="user_id" class="select2 form-select" style="width: 100%">
                                        <option value="">{{ _trans('keyword.Select User') }}</option>
                                        @foreach ($users as $user)
                                            <option
                                                value="{{ $user->id }}" {{$user_id == $user->id ? "selected":"" }}> {{ $user->name . ($user->shop ? ' (' . $user->shop->shop_name . ')' : '') }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 mb-4 col-sm-6 mb-20">
                                <div class="input-control">
                                    <select name="event" class="select2 form-select" style="width: 100%">
                                        <option value="">{{ _trans('keyword.Event Type') }}</option>
                                        <option value="created" {{ $event == 'created' ? 'selected' : '' }}>Created</option>
                                        <option value="updated" {{ $event == 'updated' ? 'selected' : '' }}>Updated</option>
                                        <option value="deleted" {{ $event == 'deleted' ? 'selected' : '' }}>Deleted</option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 mb-4 col-sm-6 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}"
                                        type="date" placeholder="Start Date *" name="start_date"
                                        value="{{ @$start_date }}" autocomplete="off">
                                    <span class="focus-border"></span>
                                    @if ($errors->has('start_date'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('start_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 mb-4 col-sm-6 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('end_date') ? ' is-invalid' : '' }}"
                                        type="date" name="end_date" value="{{ @$end_date }}" placeholder="End Date *"
                                        autocomplete="off">
                                    <span class="focus-border"></span>
                                    @if ($errors->has('end_date'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-2 mb-4">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>

            @if(hasPermission('activity_log_export'))
                <div class="mb-2">
                    <a href="{{ url('setting/activity-log/report/pdf') . '?' . http_build_query(request()->all()) }}"
                       target="_blank" class="btn btn-primary">{{_trans('keyword.Export PDF')}}</a>
                </div>
            @endif

            <div class="card">

                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                        <tr>
                            <th width="5%">{{_trans('keyword.SL')}}</th>
                            <th width="10%">{{_trans('keyword.Model')}}</th>
                            <th width="5%">{{_trans('keyword.Event')}}</th>
                            <th width="20%">{{_trans('keyword.Old Data')}}</th>
                            <th width="20%">{{_trans('keyword.New Data')}}</th>
                            <th width="10%">{{_trans('keyword.IP')}}</th>
                            <th width="20%">{{_trans('keyword.Agent')}}</th>
                            <th width="10%">{{_trans('keyword.Time')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($audits as $key => $audit)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ Str::afterLast($audit['auditable_type'], '\\') }}</td>
                                <td>{{ $audit['event']}}</td>
                                <td>
                                    @foreach ($audit['old_values'] as $oldValKey=> $item)
                                        <p>{{$oldValKey}} : <span class="text-danger">{{ $item }}</span></p>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($audit['new_values'] as $newValKey=> $item)
                                        <p>{{$newValKey}} : <span
                                                class="text-success"> {{ $item == false? "0":$item }}</span></p>
                                    @endforeach
                                </td>
                                <td>{{ $audit['ip_address']}}</td>
                                <td>{{ $audit['user_agent']}}</td>
                                <td>{{ dateFormatwithTime($audit['created_at'])}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $audits->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
