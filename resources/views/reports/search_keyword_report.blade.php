@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Search Keyword Report'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Search Keyword Report'),['#'=>_trans('keyword.Reports'),'Expense Report'=> _trans('keyword.Search Keyword Report')]) !!}

        <div class="col-lg-12 col-md-12 mb-4">
            <form method="GET" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-lg-0 mb-4">
                        <div class="input-effect">
                            <input
                                class="primary-input form-control{{ $errors->has('search') ? ' is-invalid' : '' }}"
                                type="search" placeholder="Keyword" name="search"
                                value="{{ @$search }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-lg-0 mb-4">
                        <div class="input-control">
                            <select class="w-100 bb  form-control height-50 select2" style="width: 100%"
                                    aria-label="Select Type" data-placeholder="Sort By"
                                    name="sort_by">
                                <option value="" ></option>
                                <option value="0" {{ request('sort_by') == '0' ? 'selected' : '' }}>High to Low</option>
                                <option value="1" {{ request('sort_by') == '1' ? 'selected' : '' }}>Low to High</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 ">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>


        <div class="app-ecommerce-category">
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Keyword')}}</th>
                            <th>{{_trans('keyword.Count')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key=>$expense)
                            <tr>
                                <th>{{ $data->firstItem() + $key }}</th>
                                <td>{{ $expense->keyword }}</td>
                                <td>{{ $expense->count }}</td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(function () {
            $('.select2').select2({
                allowClear: true,
            });
        })
    </script>
@endpush
