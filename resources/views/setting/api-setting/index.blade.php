@extends('layouts.master')

@section('title', $title ?? _trans('keyword.API') . ' ' . _trans('keyword.Setting'))

@section('content')



    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.API') . ' ' . _trans('keyword.Setting'), [
            '#' => _trans('keyword.General') . ' ' . _trans('keyword.Settings'),
            'setting/api-setting' => _trans('keyword.API') . ' ' . _trans('keyword.Setting'),
        ]) !!}
        <div class="app-ecommerce-category">

            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-8 mb-3">
                                <label class="form-label" for="api_key">{{ _trans('keyword.Api Key') }}</label>
                                <input type="text" class="form-control" id="api_key" placeholder="key"
                                    value="{{ $setting->api_key }}"
                                    name="api_key" aria-label="api Key" />
                                <span class="text-danger api_keyError error"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

@endsection
