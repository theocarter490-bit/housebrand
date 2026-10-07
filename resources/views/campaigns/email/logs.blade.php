@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Email Send Log'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Email Send Log'), [
            '#' => _trans('keyword.Reports'),
            'Email Send Log' => _trans('keyword.Email Send Log'),
        ]) !!}

        <div class="app-ecommerce-category">
            <div class="col-lg-12 col-md-12">
                <div class="col-lg-12 col-md-12 mb-4">
                    <form method="GET" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-lg-0 mb-4">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                        type="search" placeholder="email" name="search"
                                        value="{{ @$search }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 ">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                        <tr>
                            <th class="text-nowrap" scope="col">{{ __('SL') }}</th>
                            <th class="text-nowrap" scope="col">{{ __('Title') }}</th>
                            <th class="text-nowrap" scope="col">{{ __('Email') }}</th>
                            <th class="text-nowrap" scope="col">{{ __('Sent At') }}</th>
                            <th class="text-nowrap" scope="col">{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($logs as $key => $log)
                            <tr>
                                <th>{{ $logs->firstItem() + $key }}</th>
                                <td style="width: 40%">
                                    @if($type == 'notice')
                                        {{ optional($log->notice)->title }}
                                    @elseif($type=='campaign')
                                        {{ optional($log->emailCampaign)->title }}
                                    @elseif($type == 'event')
                                        {{ optional($log->event)->name }}
                                    @endif

                                </td>
                                <td>{{ $log->email }}</td>
                                <td>{{ dateFormatwithTime($log->sent_at) }}</td>
                                <td>
                                    @if ($log->status == 1)
                                        <span class="custom-bg-success">Send</span>
                                    @else
                                        <span class="custom-bg-danger">Not Send</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center"><p>No logs data found</p></td>
                            </tr>

                        @endforelse
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $logs->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection
