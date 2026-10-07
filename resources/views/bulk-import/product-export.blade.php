@extends('layouts.master')

@section('title', $title ??  _trans('keyword.Bulk') .' '._trans('keyword.Export'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Bulk') .' '._trans('keyword.Export'), ['#' => _trans('keyword.Product').' '._trans('keyword.Management'), 'bulk-export' => _trans('keyword.Bulk') .' '._trans('keyword.Export')]) !!}

        <div class="card mb-3">
            <div class="card-body">
                <h6 class="text-primary">{{_trans('keyword.Export your Product List')}}</h6>
                <form action="{{ route('bulkExport.export') }}" method="POST">
                    @csrf
                    <div class="row py-sm-3 py-0">
                        <!-- Status Dropdown -->
                        <div class="col-xl-2 col-md-4 mb-xl-0 mb-2 ecommerce-select2-dropdown">
                            <label for="isPublishedList"
                                   class="form-label mb-1 d-flex justify-content-between align-items-center">
                                <span>{{_trans('keyword.Select').' '._trans('keyword.Status')}}</span>
                            </label>
                            <select id="isPublishedList" name="isPublishedList"
                                    class="select2 form-select status-select" style="width: 100%"
                                    data-placeholder="Select Status">
                                <option value="" selected
                                        disabled>{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                                <option value="0">{{_trans('keyword.Unpublished')}}</option>
                                <option value="1">{{_trans('keyword.Published')}}</option>
                            </select>
                        </div>
                        <!-- Category Dropdown -->
                        <div class="col-xl-2 col-md-4 mb-xl-0 mb-2 ecommerce-select2-dropdown">
                            <label for="categoryList"
                                   class="form-label mb-1 d-flex justify-content-between align-items-center">
                                <span>{{_trans('keyword.Category')}}</span>
                            </label>
                            <select id="categoryList" name="categoryList" class="select2 form-select category-select"
                                    style="width: 100%"
                                    data-placeholder="Select Category">
                                <option value="" selected
                                        disabled>{{_trans('keyword.Select') .''._trans('keyword.Category')}}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Brand Dropdown -->
                        <div class="col-xl-2 col-md-4 mb-xl-0 mb-2 ecommerce-select2-dropdown">
                            <label for="brandList"
                                   class="form-label mb-1 d-flex justify-content-between align-items-center">
                                <span>{{_trans('keyword.Brand')}}</span>
                            </label>
                            <select id="brandList" name="brandList" class="select2 form-select brand-select"
                                    style="width: 100%"
                                    data-placeholder="Select Brand">
                                <option value="" selected
                                        disabled>{{_trans('keyword.Select').' '. _trans('keyword.Brands')}}</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(getUserId() == App\Models\Role::SUPER_ADMIN)
                            <!-- Select Type Dropdown -->
                            <div class="col-xl-3 mb-xl-0 mb-2 col-md-12 ecommerce-select2-dropdown">
                                <label for="typeSelect"
                                       class="form-label mb-1 d-flex justify-content-between align-items-center">
                                    <span>{{_trans('keyword.Select').' '._trans('keyword.Type')}}</span>
                                </label>
                                <select id="typeSelect" name="typeSelect" class="select2 form-select type-select"
                                        style="width: 100%"
                                        data-placeholder="Select Type">
                                    <option value="" selected
                                            disabled>{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                                    <option value="manufacturer">{{_trans('keyword.Manufacturer')}}</option>
                                    <option value="designer">{{_trans('keyword.Designers')}}</option>
                                </select>
                            </div>

                            <!-- Manufacturer Dropdown -->
                            <div class="col-xl-3 mb-xl-0 mb-2 col-md-12 ecommerce-select2-dropdown"
                                 id="manufacturerDropdown"
                                 style="display:none;">
                                <label for="manufacturerList"
                                       class="form-label mb-1 d-flex justify-content-between align-items-center">
                                    <span>{{_trans('keyword.Select').' '. _trans('keyword.Manufacturer')}}</span>
                                </label>
                                <select id="manufacturerList" name="manufacturerList"
                                        class="select2 form-select user-select" style="width: 100%"
                                        data-placeholder="Select Manufacturer">
                                    <option value="" selected
                                            disabled>{{_trans('keyword.Select').' '. _trans('keyword.Manufacturer')}}</option>
                                    @foreach ($manufacturers as $manufacturer)
                                        <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Designer Dropdown -->
                            <div class="col-xl-3 mb-xl-0 mb-2 col-md-12 ecommerce-select2-dropdown"
                                 id="designerDropdown"
                                 style="display:none;">
                                <label for="designerList"
                                       class="form-label mb-1 d-flex justify-content-between align-items-center">
                                    <span>{{_trans('keyword.Select').' '. _trans('keyword.Designers')}}</span>
                                </label>
                                <select id="designerList" name="designerList"
                                        class="select2 form-select designer-select" style="width: 100%"
                                        data-placeholder="Select Designer">
                                    <option value="" selected
                                            disabled>{{_trans('keyword.Select').' '. _trans('keyword.Designers')}}</option>
                                    @foreach ($designers as $designer)
                                        <option value="{{ $designer->id }}">{{ $designer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                    </div>
                    <div class="row py-0">
                        <!-- Users List -->
                        <div class="col-md-12 col-lg-8  ">
                            <label for="TagifyUserList" class="form-label">Product List</label>
                            <input
                                id="TagifyUserList"
                                name="TagifyProductList"
                                class="form-control"
                                placeholder="Select Product"
                                value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <button class="btn btn-primary mt-md-4 mt-4 w-100"
                                    type="submit">{{_trans('keyword.Export')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function () {

            $('.status-select').select2({
                allowClear: true,
            });

            $('.category-select').select2({
                allowClear: true,
            });
            $('.designer-select').select2({
                allowClear: true,
            });

            $('.brand-select').select2({
                allowClear: true,
            });

            $('.type-select').select2({
                allowClear: true,
            });

            $('.user-select').select2({
                allowClear: true,
            });

            $('#typeSelect').change(function () {
                var selectedType = $(this).val();
                $('.designer-select').val('').change();
                $('.user-select').val('').change();
                if (selectedType === 'manufacturer') {
                    $('#manufacturerDropdown').show();
                    $('#designerDropdown').hide();
                } else if (selectedType === 'designer') {
                    $('#manufacturerDropdown').hide();
                    $('#designerDropdown').show();
                } else {
                    $('#manufacturerDropdown').hide();
                    $('#designerDropdown').hide();
                }
            });

            $('#isPublishedList, #categoryList, #brandList, #typeSelect, #manufacturerList, #designerList').on('change', function () {
                getProduct();
            });
            getProduct();

            function getProduct() {

                let published = $('#isPublishedList').val();
                let category = $('#categoryList').val();
                let brand = $('#brandList').val();
                let typeSelect = $('#typeSelect').val();
                let manufacturer = $('#manufacturerList').val();
                let designer = $('#designerList').val();
                console.log(published, category, brand, typeSelect, manufacturer, designer);

                $.ajax({
                    url: '{{ route('bulkExport.productList') }}',
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'is_published': published,
                        'category': category,
                        'brand': brand,
                        'typeSelect': typeSelect,
                        'manufacturer': manufacturer,
                        'designer': designer,
                    },
                    success: function (response) {
                        console.log(response);
                        let productList = [];
                        $(response[0]).each(function (index, value) {
                            productList.push({
                                value: value.id,
                                name: value.name,
                                avatar: `${value.thumbnail_img}`,
                            });
                        });

                        tagifyProductList(productList);
                    },
                    error: function (error) {
                        console.log(error.responseJSON.message);
                        // handle the error case
                    }
                });


            }

            let TagifyUserList;

            function tagifyProductList(dataList) {
                const TagifyUserListEl = document.querySelector('#TagifyUserList');
                let selectedTags = TagifyUserList ? TagifyUserList.value.map(tag => ({
                    value: tag.value,
                    name: tag.name,
                    avatar: tag.avatar
                })) : [];
                // Destroy the existing Tagify instance if it exists
                if (TagifyUserList) {
                    TagifyUserList.removeAllTags();
                    TagifyUserList.destroy();
                }

                function tagTemplate(tagData) {
                    return `
    <tag title="${tagData.name}"
      contenteditable='false'
      spellcheck='false'
      tabIndex="-1"
      class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ''}"
      ${this.getAttributes(tagData)}
    >
      <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
      <div>
        <div class='tagify__tag__avatar-wrap'>
          <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
        </div>
        <span class='tagify__tag-text'>${tagData.name}</span>
      </div>
    </tag>
  `;
                }

                function suggestionItemTemplate(tagData) {
                    return `
    <div ${this.getAttributes(tagData)}
      class='tagify__dropdown__item align-items-center ${tagData.class ? tagData.class : ''}'
      tabindex="0"
      role="option"
    >
      ${
                        tagData.avatar
                            ? `<div class='tagify__dropdown__item__avatar-wrap'>
          <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
        </div>`
                            : ''
                    }
      <div class="fw-medium">${tagData.name}</div>
    </div>
  `;
                }

                function dropdownHeaderTemplate(suggestions) {
                    return `
        <div class="${this.settings.classNames.dropdownItem} ${this.settings.classNames.dropdownItem}__addAll">
            <strong>${this.value.length ? `Select remaining` : 'Select All'}</strong>
            <span>${suggestions.length} products</span>
        </div>
    `;
                }

                // initialize Tagify on the above input node reference
                TagifyUserList = new Tagify(TagifyUserListEl, {
                    tagTextProp: 'name', // very important since a custom template is used with this property as text. allows typing a "value" or a "name" to match input with whitelist
                    enforceWhitelist: true,
                    skipInvalid: true, // do not remporarily add invalid tags
                    dropdown: {
                        closeOnSelect: false,
                        enabled: 0,
                        classname: 'users-list',
                        searchKeys: ['name'] // very important to set by which keys to search for suggesttions when typing
                    },
                    templates: {
                        tag: tagTemplate,
                        dropdownItem: suggestionItemTemplate,
                        dropdownHeader: dropdownHeaderTemplate
                    },
                    whitelist: dataList
                });

                // Step 4: Reapply the selected tags
                TagifyUserList.removeAllTags();
                TagifyUserList.addTags(selectedTags);

                // attach events listeners
                TagifyUserList.on('dropdown:select', onSelectSuggestion) // allows selecting all the suggested (whitelist) items
                    .on('edit:start', onEditStart); // show custom text in the tag while in edit-mode

                function onSelectSuggestion(e) {
                    // custom class from "dropdownHeaderTemplate"
                    if (e.detail.elm.classList.contains(`${TagifyUserList.settings.classNames.dropdownItem}__addAll`))
                        TagifyUserList.dropdown.selectAll();
                }

                function onEditStart({detail: {tag, data}}) {
                    TagifyUserList.setTagTextNode(tag, `${data.name}`);
                }
            }


        });
    </script>
@endpush
