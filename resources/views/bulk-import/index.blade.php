@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Bulk') .' '._trans('keyword.Import'))

@section('content')



    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('Bulk Import',['#'=>_trans('keyword.Product').' '._trans('keyword.Management'),'bulk-import'=>_trans('keyword.Bulk') .' '._trans('keyword.Import')]) !!}

        <div class="card mb-3">
            <div class="card-header">
                <h5>{{_trans('keyword.Product Bulk Upload')}}</h5>
            </div>
            <div class="card-body">
                <h6 class="text-primary">{{_trans('keyword.Step:1')}}</h6>
                <ol>
                    <li>{{_trans('keyword.Download the skeleton file and fill it with proper data.')}}</li>
                    <li>{{_trans('keyword.You can download the example file to understand how the data must be filled.')}}</li>
                    <li>{{_trans('keyword.Once you have downloaded and filled the skeleton file, upload it in the form below and submit.')}}</li>
                    <li>{{_trans("keyword.After uploading products you need to edit them and set product's images and choices.")}}</li>
                </ol>
               <div class="row">
                   <div class="col-md-3 col-sm-6 mb-sm-0  text-nowrap">
                       <a href="{{ route('bulkImport.productExport') }}" class="btn btn-primary w-sm-auto w-100">{{_trans('keyword.Download Demo xlsx')}}</a>
                   </div>
               </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h6 class="text-primary">{{_trans('keyword.Step:2')}}</h6>
                <ol>
                    <li>{{_trans('keyword.Category and Brand should be in numerical id.')}}</li>
                    <li>{{_trans('keyword.You can download the pdf to get Category and Brand id.')}}</li>
                </ol>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-sm-0 mb-3 text-nowrap">
                        <a href="{{ route('bulkImport.categoryExport') }}" class="btn btn-primary w-sm-auto w-100">{{_trans('keyword.Download Category')}}</a>
                    </div>
                    <div class="col-md-3 col-sm-6 text-nowrap">
                        <a href="{{ route('bulkImport.brandExport') }}" class="btn btn-primary w-sm-auto w-100">{{_trans('keyword.Download Brand')}}</a>
                    </div>
                </div>


            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('bulkImport.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h6 class="text-primary">{{_trans('keyword.Upload Product List File')}}</h6>
                    <h6 class="text-primary">{{_trans('keyword.Note')}}:</h6>
                    <ol>
                        <li>{{_trans("keyword.'discount_type' is takes 0, 1 or 2. 1 refer to percentage and 2 refer to fixed. Other wise keep value 0.")}}</li>
                        <li>{{_trans("keyword.'discount' field take absolute numerical value.")}}</li>
                        <li>{{_trans("keyword.Thumbnail image takes the links of online reference of image link. Must have public access otherwise image will be empty. ex: google drive")}}</li>
                        <li>{{_trans("keyword.'meta_keywords' field takes coma separate value. example format: keyword1,keyword2,keyword3.")}}</li>
                        <li>{{_trans("keyword.Shipping Policy, Return Policy, Disclaimer text will get from shop setting global term and condition. recommended: Set Term and Condition from shop setting before bulk import.")}}</li>
                    </ol>
                    <div class="col-md-8 mb-2">
                        <input type="file" class="form-control" name="productListFile">
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>{{_trans('keyword.Whoops!')}}</strong> {{_trans('keyword.There were some problems with your input.')}}<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                  <div class="row">
                      <div class="col-md-3 col-sm-6 mb-sm-0  text-nowrap mt-sm-0 mt-3">
                          <button class="btn btn-primary me-sm-auto w-100" type="submit">{{_trans('keyword.Upload')}}</button>
                      </div>
                  </div>
                </form>
            </div>
        </div>


    </div>


@endsection
