@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Time Breakdown'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb( _trans('keyword.Time Breakdown'),['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),'Time Billing'=>   _trans('keyword.Time Breakdown')]) !!}

        <div class="app-ecommerce-category">
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                            <tr>
                                <th>{{_trans('keyword.SL')}}</th>
                                <th>{{_trans('keyword.Start Time')}}</th>
                                <th>{{_trans('keyword.End Time')}}</th>
                                <th>{{_trans('keyword.Duration')}}</th>
                                {{-- <th>{{_trans('keyword.Action')}}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timeBreakdowns as $key => $item)
                                <tr>
                                    <th>{{ $timeBreakdowns->firstItem() + $key }}</th>
                                    <td>{{ dateFormatwithTime($item->start_time) }}</td>
                                    <td>{{ dateFormatwithTime($item->end_time) }}</td>
                                    <td>{{ secondsToHMS($item->duration) }}</td>
                                </tr>
                            @endforeach
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $timeBreakdowns->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
