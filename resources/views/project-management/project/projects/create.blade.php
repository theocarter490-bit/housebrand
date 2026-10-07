@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Project'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Project').' '. _trans('keyword.Create'),['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),'category'=> _trans('keyword.Category').' '. _trans('keyword.List')]) !!}

        <div class="app-ecommerce">
            <!-- Add Product -->
            <form method="POST" action="{{route('project-management.project.store')}}" id="product-add"
                  enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <!-- First column-->
                    <div class="col-12 col-xl-8">
                        <!-- Product Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-tile mb-0">{{_trans('keyword.Project information')}}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-12 mb-3">
                                        <label class="form-label"
                                               for="ecommerce-product-name">{{_trans('keyword.Title')}} <span
                                                style="color: red;">*</span></label>
                                        <input type="text" class="form-control" id="title"
                                               value="{{old('title')}}"
                                               placeholder="Project title" name="title" aria-label="Project title"/>
                                        @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{_trans('keyword.Description')}}</label>
                                        <div class="form-control p-0 pt-1">
                                            <div class="comment-toolbar border-0 border-bottom">
                                                <div class="d-flex justify-content-start">
                                                <span class="ql-formats me-0">
                                                    <button class="ql-bold"></button>
                                                    <button class="ql-italic"></button>
                                                    <button class="ql-underline"></button>
                                                    <button class="ql-list" value="ordered"></button>
                                                    <button class="ql-list" value="bullet"></button>
                                                    <button class="ql-link"></button>
                                                    {{-- <button class="ql-image"></button> --}}
                                                </span>
                                                </div>
                                            </div>
                                            <div class="product-editor border-0 pb-4" id="description"></div>
                                        </div>
                                        <span class="text-danger descriptionError error"></span>
                                        <input name="description" type="hidden" id="description_input">
                                    </div>

                                    <div class="row card-body justify-content-center align-items-center">
                                        <h5>Banner Image</h5>
                                        <div class="col-12">
                                            <div class="row  position-relative mb-2 p-1 card" style="height: 150px">
                                                <div
                                                    class="col-6 border-end d-flex justify-content-center align-items-center position-relative"
                                                    style="height: 100%">

                                                    <div
                                                        class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                                        <i class="ti ti-plus ti-xs"
                                                           style="font-size:2rem !important"></i>
                                                        <span
                                                            style="font-size:1rem !important">{{_trans('keyword.Input').' '. _trans('keyword.Image')}}</span>
                                                    </div>
                                                    <input name="banner"
                                                           class="opacity-0 position-absolute top-50 start-50 translate-middle p-5"
                                                           type="file" onchange="loadFile(event)"/>
                                                </div>
                                                <div class="col-6" style="height: 100%">
                                                    <img id='preview_img' class="ms-2 preview_img"
                                                         style="height: 100%; max-Width:100%"
                                                         src="{{ asset('assets/img/placeholder/placeholder.png') }}"
                                                         alt="Current profile photo"/>
                                                </div>
                                            </div>
                                            @error('banner')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-4">
                                            <label for="flatpickr-range" class="form-label">Date Range</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="YYYY-MM-DD to YYYY-MM-DD"
                                                name="date"
                                                value="{{old('date')}}"
                                                id="flatpickr-range"/>
                                            @error('date')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-12 mb-4">
                                            <label for="priority" class="form-label">Select Priority <span class="text-danger">*</span></label>
                                            <select id="priority" name="priority" class="select2 form-select"
                                                    style="width: 100%"
                                                    data-placeholder="Select Priority" >
                                                <option
                                                    value="">{{_trans('keyword.Select').' '._trans('keyword.Priority')}}</option>
                                                <option value="low" {{old('priority') == "low"?'selected':''}}>Low</option>
                                                <option value="medium" {{old('priority') == "medium"?'selected':''}}>Medium</option>
                                                <option value="high" {{old('priority') == "high"?'selected':''}}>High</option>
                                                <option value="urgent" {{old('priority') == "urgent"?'selected':''}}>Urgent</option>
                                            </select>
                                            @error('priority')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-4">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="address">{{old('date')}}</textarea>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label for="TagifyBasic" class="form-label">Tags</label>
                                            <input id="TagifyBasic" class="form-control" name="tags"
                                                   value="{{old('tags')}}"/>
                                        </div>
                                        @error('tags')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <label class="form-label">Map Location</label>
                                            <textarea class="form-control" name="map_location">{{old('map_location')}}</textarea>
                                            @error('map_location')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <!-- /Product Information -->
                    </div>
                    <!-- /Second column -->

                    <!-- Second column -->
                    <div class="col-12 col-xl-4">
                        <!-- Organize Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{_trans('keyword.Organize')}}</h5>
                            </div>
                            <div class="card-body">

                                <!-- Category -->
                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                           for="category-org">
                                            <span>{{_trans('keyword.Category')}} <span
                                                    style="color: red;">*</span></span>
                                    </label>
                                    <select id="category" name="category" class="select2 form-select"
                                            style="width: 100%"
                                            data-placeholder="Select Category" >
                                        <option
                                            value="">{{_trans('keyword.Select').' '._trans('keyword.Category')}}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{old('category') == $category->id?'selected':''}}>{{ $category->name }}</option>
                                        @endforeach

                                    </select>
                                    @error('category')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>

                                <!-- Customer -->
                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1" for="customer">{{_trans('keyword.Customer')}} <span
                                            style="color: red;">*</span></label>
                                    <select id="customer" name="customer" class="select2 form-select"
                                            style="width: 100%"
                                            data-placeholder="Select Customer">
                                        <option
                                            value="">{{_trans('keyword.Select').' '._trans('keyword.Customer')}}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" {{old('customer') == $customer->id?'selected':''}}>{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <!-- Manager -->
                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1" for="manager">{{_trans('keyword.Manager')}} <span
                                            style="color: red;">*</span></label>
                                    <select id="manager" name="manager" class="select2 form-select"
                                            style="width: 100%"
                                            data-placeholder="Select Manager">
                                        <option
                                            value="">{{_trans('keyword.Select').' '._trans('keyword.Manager')}}</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{old('manager') == $employee->id?'selected':''}}>{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('manager')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <!-- Status -->
                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1"
                                           for="status-org">{{_trans('keyword.Status')}} <span class="text-danger">*</span> </label>
                                    <select id="status" name="status" class="select2 form-select"
                                            style="width: 100%"
                                            data-placeholder="Select Status">
                                        <option
                                            value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}" {{old('status') == $status->id?'selected':''}}>{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <!-- Active Status -->
                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1"
                                           for="active_status">{{_trans('keyword.Active Status')}} <span class="text-danger">*</span> </label>
                                    <select id="active_status" name="active_status" class="select2 form-select"
                                            style="width: 100%"
                                            data-placeholder="Select Status">
                                        <option
                                            value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                                        <option
                                            value="1">Active
                                        </option>
                                        <option
                                            value="0">Inactive
                                        </option>

                                    </select>
                                    @error('active_status')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                        <!-- /Organize Card -->

                        <!-- Pricing Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{_trans('keyword.Pricing')}}
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Base Price -->
                                <div class="mb-3">
                                    <label class="form-label"
                                           for="ecommerce-product-price">{{_trans('keyword.Budget')}} <span
                                            style="color: red;">*</span></label>
                                    <input type="number" onkeyup="if(value<0) value=0;" class="form-control set_price"
                                           id="budget" placeholder="Budget"  value="{{old('budget')}}"
                                           name="budget" aria-label="Project Budget"/>
                                    @error('budget')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1 d-flex justify-content-between align-items-center"
                                           for="discout_type">
                                        <span>{{_trans('keyword.Tax Type')}}</span>
                                    </label>
                                    <select id="tax_type" name="tax_type" class="select2 form-select set_price"
                                            style="width: 100%"
                                            data-placeholder="Select Type">
                                        <option
                                            value="0">{{_trans('keyword.Select').' '._trans('keyword.Tax Type')}}</option>
                                        <option value="1" {{old('tax_type') == 1?'selected':''}}>{{_trans('keyword.Percentage')}} %</option>
                                        <option value="2" {{old('tax_type') == 2?'selected':''}}>{{_trans('keyword.Fixed')}}</option>
                                    </select>
                                </div>
                                @error('tax_type')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror


                                <!-- Tax Price -->
                                <div class="mb-3">
                                    <label class="form-label"
                                           for="tax">{{_trans('keyword.Tax')}}</label>
                                    <input type="number" class="form-control set_price" id="tax"
                                           placeholder="Tax" name="tax" value="{{old('tax')}}"
                                           aria-label="Tax Value"/>

                                    @error('tax')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <!-- Total Price -->
                                <div class="mb-3">
                                    <label class="form-label"
                                           for="total_cost">{{_trans('keyword.Total Cost')}} <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="total_cost"
                                           placeholder="Total Cost" name="total_cost"  value="{{old('total_cost')}}"
                                           aria-label="Create new scratch file from selection"/>

                                    @error('total_cost')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                            </div>
                        </div>
                        <!-- /Pricing Card -->


                    </div>

                    <!-- /Second column -->
                </div>

                @if(getUserId() !== 1)
                    <div class="row justify-content-center">
                        <button type="submit" id="addProduct" class="btn btn-primary col-4">{{_trans('keyword.Submit')}}
                            <span class="loader"></span>
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        flatpickrRange = document.querySelector('#flatpickr-range');
        // Range
        if (typeof flatpickrRange != undefined) {
            flatpickrRange.flatpickr({
                mode: 'range'
            });
        }
        const tagifyBasicEl = document.querySelector('#TagifyBasic');
        const TagifyBasic = new Tagify(tagifyBasicEl);
        var loadFile = function (event) {

            var input = event.target;
            var file = input.files[0];
            var type = file.type;

            var output = event.currentTarget.parentNode.nextElementSibling.children[0];
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function () {
                URL.revokeObjectURL(output.src) // free memory
            }
        };

        $('.set_price').on('change keyup keypress', function () {
            console.log('here');
            let budget = $('#budget').val()??0;
            let tax_amount = calculateTaxAmount(budget);
            $('#total_cost').val(parseInt(parseInt(tax_amount) + parseInt(budget)));

        });

        function calculateTaxAmount(subtotal) {
            let tax_type = $('#tax_type').find(":selected").val();
            let tax_value = $('#tax').val() === "" ? 0.00 : $('#tax').val();

            let tax_amount = 0;


            if (tax_type == 1) {
                tax_amount = (tax_value / 100) * subtotal;
            }
            if (tax_type == 2) {
                tax_amount = tax_value;
            }

            return parseFloat(tax_amount).toFixed(2);
        }


        $('#description').on('keyup', function () {
            $('#description_input').val($('#description').children().first().html());
        });

    </script>
@endpush

