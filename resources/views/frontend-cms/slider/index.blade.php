@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Slider'))
@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Slider'), ['#'=> 'Frontend CMS', '/cms/slider' => _trans('keyword.Slider')]) !!}
        <div class="app-ecommerce-category">
            <!-- Slider List Table -->
            <div class="card">
                {{--  <div class="d-flex gap-3 position-absolute ps-4 p-2 " style="z-index: 100; margin-top: 10px; margin-left: 240px">
                      <div class="form-group">
                          <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                          <select id='status' class="form-control filter_dropdown" style="width: 200px">
                              <option value="">{{_trans('keyword.Select').' '._trans('keyword.Status')}}</option>
                              <option value="1">{{_trans('keyword.Active')}}</option>
                              <option value="0">{{_trans('keyword.Inactive')}}</option>
                          </select>
                      </div>

                      <div class="form-group">
                          <label><strong>{{_trans('keyword.Type')}} :</strong></label>
                          <select id='type' class="form-control filter_dropdown" style="width: 200px">
                              <option value="">{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                              <option value="0">{{_trans('keyword.Home Page')}}</option>
                              <option value="1">{{_trans('keyword.About Us')}}</option>
                          </select>
                      </div>
                  </div>--}}
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{_trans('keyword.Title')}}</th>
                            <th>{{_trans('keyword.Description')}}</th>
                            <th>{{_trans('keyword.File')}}</th>
                            <th>{{_trans('keyword.Type')}}</th>
                            <th>{{_trans('keyword.File Type')}}</th>
                            <th>{{_trans('keyword.Status')}}</th>
                            <th width="100px">{{_trans('keyword.Action')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addSliderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{ _trans('keyword.Add').' '._trans('keyword.New').' '._trans('keyword.Slider') }}</h3>
                    </div>
                    <!-- Form Start -->
                    <form id="addSliderForm" action="{{ route('cms.slider.store') }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Title') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" required class="form-control"
                                   placeholder="Enter title"
                                   value="{{ old('title') }}"/>
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-12 mb-2">
                            <div class="form-group position-relative">
                                <label><strong>Description :</strong></label>

                                <textarea class="ckeditor form-control" name="description"></textarea>
                            </div>
                        </div>

                        <div class="mt-3 col ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">{{ _trans('keyword.Type') }}<span
                                    class="text-danger">*</span></label>
                            <select name="slider_type" class="select2 form-select sliderType" style="width: 100%"
                                    required>
                                <option disabled>{{ _trans('keyword.Select Type') }}</option>
                                <option
                                    value="0" {{ old('slider_type') == '0' ? 'selected' : '' }}>{{ _trans('keyword.Hero Section (Landing Page)') }}</option>
                                <option
                                    value="1" {{ old('slider_type') == '1' ? 'selected' : '' }}>{{ _trans('keyword.About Us') }}</option>
                                @if(!isSeller())
                                    <option
                                        value="2" {{ old('slider_type') == '2' ? 'selected' : '' }}>{{ _trans('keyword.Slider with product (Landing Page)') }}</option>
                                @endif
                            </select>
                            @error('slider_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mt-3 col ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">{{ _trans('keyword.Status') }}<span
                                    class="text-danger">*</span></label>
                            <select name="active_status" class="select2 form-select" style="width: 100%" required>
                                <option
                                    value="1" {{ old('active_status') == '1' ? 'selected' : '' }}>{{ _trans('keyword.Active') }}</option>
                                <option
                                    value="0" {{ old('active_status') == '0' ? 'selected' : '' }}>{{ _trans('keyword.Inactive') }}</option>
                            </select>
                            @error('active_status')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- File Type -->
                        <div class="mt-3 col ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">{{ _trans('keyword.File Type') }}<span
                                    class="text-danger">*</span></label>
                            <select name="file_type" class="select2 form-select" style="width: 100%" required>
                                <option class="fileType0"
                                        value="0" {{ old('file_type') == '0' ? 'selected' : '' }}>{{ _trans('keyword.Image') }}</option>
                                <option class="fileType1"
                                        value="1" {{ old('file_type') == '1' ? 'selected' : '' }}>{{ _trans('keyword.Video') }}</option>
                            </select>
                            @error('file_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 mt-3">
                            <label class="form-label">{{ _trans('keyword.File') }} ({{ _trans('keyword.Image/Video') }}
                                )<span class="text-danger">*</span></label>
                            <input type="file" id="newImage" name="image" class="form-control" required/>
                            @error('image')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Current Image Preview -->
                        <div class="col-12 mt-3" id="preview_image">
                            <label class="form-label">{{ _trans('keyword.Current Image') }}</label>
                            <div class="mb-2">
                                <img id="newImagePreview" src="#" alt="Current Image" class="img-fluid rounded"
                                     style="width: 100%;"/>
                            </div>
                        </div>


                        <div class="col-md-12 sliderProductList d-none">
                            <label for="TagifyUserList" class="form-label">Product List</label>
                            <input
                                id="TagifyUserList"
                                name="TagifyProductList"
                                class="form-control"
                                placeholder="Select Product"
                                value=""/>
                        </div>


                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                    id="addFaq">{{ _trans('keyword.Submit') }}
                                <span class="loader"></span>
                            </button>
                            <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                {{ _trans('keyword.Cancel') }}
                            </button>
                        </div>
                    </form>
                    <!-- Form End -->

                </div>
            </div>
        </div>
    </div>
    <!--/ Add Modal -->


    <!-- Edit Modal -->
    <div class="modal fade" id="editSliderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" id="closeModal"
                        aria-label="Close"></button>
                <form id="editSliderForm" action="{{route('cms.slider.update')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="text-center mb-2">
                            <h3 class="role-title mb-2">{{ _trans('keyword.Edit') . ' ' . _trans('keyword.Slider') }}</h3>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label">{{ _trans('keyword.Title') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="editTitle" required name="editTitle" class="form-control"
                                   placeholder="Enter title"/>
                            <span class="text-danger titleError error"></span>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label><strong>Description :</strong></label>

                                <textarea class="ckeditor form-control" id="editDescription"
                                          name="description"></textarea>
                            </div>
                        </div>

                        <input type="hidden" id="slider_id" name="slider_id">

                        <!-- Status -->
                        <div class="mt-3 col ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">{{ _trans('keyword.Status') }}<span
                                    class="text-danger">*</span></label>
                            <select id="editStatus" name="editStatus" class="select2 form-select" required>
                                <option value="1">{{ _trans('keyword.Active') }}</option>
                                <option value="0">{{ _trans('keyword.Inactive') }}</option>
                            </select>
                        </div>

                        <div class="mt-3 col ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">{{ _trans('keyword.Type') }}<span
                                    class="text-danger">*</span></label>
                            <select id="edit_slider_type" name="edit_slider_type" class="select2 form-select sliderType"
                                    required>
                                <option value="0">{{ _trans('keyword.Hero Section (Landing Page)') }}</option>
                                <option value="1">{{ _trans('keyword.About Us') }}</option>
                                @if(!isSeller())
                                    <option
                                        value="2">{{ _trans('keyword.Slider with product (Landing Page)') }}</option>
                                @endif
                            </select>
                        </div>

                        <!-- File Type -->
                        <div class="mt-3 col ecommerce-select2-dropdown">
                            <label class="form-label mb-1" for="status-org">{{ _trans('keyword.File Type') }}<span
                                    class="text-danger">*</span></label>
                            <select id="edit_file_type" name="edit_file_type" required class="select2 form-select">
                                <option value="0">{{ _trans('keyword.Image') }}</option>
                                <option value="1">{{ _trans('keyword.Video') }}</option>
                            </select>
                            @error('file_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Current Image Preview -->
                        <div class="col-12 mt-3" id="preview_image">
                            <label class="form-label">{{ _trans('keyword.Current Image') }}</label>
                            <div class="mb-2">
                                <img id="currentImage" src="#" alt="Current Image" class="img-fluid rounded"
                                     style="width: 100%;"/>
                            </div>
                        </div>

                        <!-- New Image Upload -->
                        <div class="col-12 mt-3">
                            <label class="form-label">{{ _trans('keyword.File') }} ({{ _trans('keyword.Image/Video') }}
                                )</label>
                            <input type="file" id="image" name="image" class="form-control"/>
                            <span class="text-danger imageError error"></span>
                        </div>

                        <div class="col-md-12 sliderProductList d-none">
                            <label for="TagifyUserList" class="form-label">Product List</label>
                            <input
                                id="TagifyUserListEdit"
                                name="TagifyProductList"
                                class="form-control"
                                placeholder="Select Product"
                                value=""/>
                        </div>

                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                    id="updateFaq">{{ _trans('keyword.Submit') }}
                                <span class="loader"></span>
                            </button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">{{ _trans('keyword.Cancel') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--/ Edit Modal -->

    <!-- Add Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{ _trans('keyword.Select').' '._trans('keyword.Product') }}</h3>
                    </div>
                    <div class="col-md-12 sliderProductList d-none">
                        <label for="TagifyUserList" class="form-label">Product List<span
                                class="text-danger">*</span></label>
                        <input
                            id="TagifyProductList"
                            name="TagifyProductList"
                            class="form-control"
                            placeholder="Select Product"
                            value=""/>
                    </div>

                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                id="addProductSubmit">{{ _trans('keyword.Submit') }}
                            <span class="loader"></span>
                        </button>
                        <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">
                            {{ _trans('keyword.Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Add Modal -->
    <!-- Edit Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="role-title mb-2">{{ _trans('keyword.Select').' '._trans('keyword.Product') }}</h3>
                    </div>
                    <div class="col-md-12 sliderProductList d-none">
                        <label for="TagifyUserList" class="form-label">Product List<span
                                class="text-danger">*</span></label>
                        <input
                            id="TagifyProductListEdit"
                            name="TagifyProductListEdit"
                            class="form-control"
                            placeholder="Select Product"
                            value=""/>
                    </div>

                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1"
                                id="editProductSubmit">{{ _trans('keyword.Submit') }}
                            <span class="loader"></span>
                        </button>
                        <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">
                            {{ _trans('keyword.Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Add Modal -->

@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        $(function () {


            @foreach($errors->all() as $error)
            toastr.error('{!! $error !!}');
            @endforeach

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('cms.slider.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()
                        d.type = $('#type').val()
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },

                    {
                        data: 'file_type',
                        name: 'file_type'
                    },


                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [0, "desc"], //set any columns order asc/desc
                dom: '<"card-header d-flex flex-wrap pb-2 c-list-header"' +
                    '<f m-0><"custom-text-div">' +
                    '<" d-flex justify-content-center justify-content-md-end align-items-baseline right-side-buttons "<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline gap-xl-0 gap-3"lB>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                initComplete: function () {
                    // Set the inner div to display your name
                    $('.custom-text-div').html(`
                    <div class="d-flex gap-3   d-block  " >
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Status')}} :</strong></label>
                        <select id='status' class="form-control filter_dropdown slider-select-1 select2 form-select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Status') }}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}</option>
                            <option value="1">{{_trans('keyword.Active') }}</option>
                            <option value="0">{{_trans('keyword.Inactive') }}</option>
                         </select>
                    </div>
                     <div class="form-group">
                        <label><strong>{{_trans('keyword.Type')}} :</strong></label>
                        <select id='type' class="form-control filter_dropdown slider-select-2 select2 form-select2" style="width: 200px" data-placeholder="{{ _trans('keyword.Select') }} {{ _trans('keyword.Type') }}">
                            <option value="">{{_trans('keyword.Select').' '._trans('keyword.Type')}}</option>
                            <option value="0">{{_trans('keyword.Home Section')}}</option>
                            <option value="1">{{_trans('keyword.About Us')}}</option>
                            @if(!isSeller())
                    <option value="2">{{_trans('keyword.Banner With Product')}}</option>
                    @endif
                    </select>
                </div>
              </div>
`);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $('.slider-select-1').select2({
                        allowClear: true,
                    });
                    $('.slider-select-2').select2({
                        allowClear: true,
                    });
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Slider",
                },
                // Button for offcanvas
             buttons: [
                    @if(hasPermission('slider_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Add Slider</span>',
                        className: "create-new btn btn-primary ms-2 waves-effect waves-light d-flex align-items-center justify-content-center text-nowrap",
                        attr: {
                            "data-bs-toggle": "modal",
                            "data-bs-target": "#addSliderModal",
                        },
                    },
                    @endif
                ],
            });

            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            });

            $(document).on('change', '.changeStatus', function () {
                const id = $(this).data('id');
                const formData = new FormData();
                formData.append('id', id);
                formData.append('_token', "{{ csrf_token() }}");

                // Show SweetAlert2 confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to change the status of this?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it',
                    cancelButtonText: 'No, cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed with the AJAX request if confirmed
                        $.ajax({
                            url: '{{ route('cms.slider.changeStatus') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    table.ajax.reload(null, false);
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function (error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });

            $(document).on("click", ".slider_edit_button", function () {
                $('.error').text('')
                $id = $(this).attr("data-id");
                $('#editStatus').val(null).trigger('change');
                $('#edit_slider_type').val(null).trigger('change');
                $('#edit_file_type').val(null).trigger('change');

                $.ajax({
                    url: '/cms/slider/edit/' + $id,
                    type: 'GET',
                    success: function (response) {
                        $('#slider_id').val(response.data.id);
                        $("#editTitle").val(response.data.title);
                        if (CKEDITOR.instances['editDescription']) {
                            CKEDITOR.instances['editDescription'].setData(response.data.description);
                        }

                        if (response.data.file_type === 0) {
                            $('#currentImage').attr('src', response.data.image);
                        } else if (response.data.file_type === 1) {
                            $('#preview_image').hide();
                        }

                        $('#editStatus').val(response.data.active_status).trigger('change');
                        $('#edit_slider_type').val(response.data.type).trigger('change');
                        $('#edit_file_type').val(response.data.file_type).trigger('change');

                        let selectedProductList = [];

                        $(response.data.products).each(function (index, value) {
                            selectedProductList.push({
                                value: value.id,
                                name: value.name,
                                avatar: `/storage/${value.thumbnail_img}`,
                                coordinate: {
                                    x: value.pivot.x,
                                    y: value.pivot.y,
                                },
                            });
                        });

                        tagifyProductListEdit(selectedProductList);


                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $(document).on("click", ".slider_delete_button", function () {

                let id = $(this).attr("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('cms.slider.destroy') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false)
                                toastr.success(response.text);
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                                // handle the error case
                            }
                        });
                    }
                });
            });

            $('.sliderType').on('change', function () {
                let type = $(this).val();
                if (type == 2) {
                    $('.sliderProductList').removeClass('d-none');
                    $('.fileType1').attr('disabled', 'disabled');
                } else {
                    $('.sliderProductList').addClass('d-none');
                    $('.fileType1').removeAttr('disabled');
                    $('.fileType1').removeAttr('selected');
                    $('.fileType0').attr('selected', 'selected');
                }
            });

            // Declare Tagify instances globally at the top
            let TagifyUserListInstance; // For #TagifyUserList
            let TagifyUserListEditInstance; // For #TagifyUserListEdit
            let TagifyProductListInstance; // For #TagifyProductList
            let TagifyProductListEditInstance; // For #TagifyProductList

            let productList = [];
            getProduct();

            function getProduct() {
                $.ajax({
                    url: '{{ route('bulkExport.productList') }}',
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "is_published": 1,
                    },
                    success: function (response) {
                        productList = [];
                        $(response[0]).each(function (index, value) {
                            productList.push({
                                value: value.id,
                                name: value.name,
                                avatar: `${value.thumbnail_img}`,
                            });
                        });

                        tagifyProductList();
                        tagifyProductListEdit();
                        tagifyProductListSelection();
                        tagifyProductListEditSelection();
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            }

            function tagifyProductList() {
                const TagifyUserListEl = document.querySelector('#TagifyUserList');

                if (!TagifyUserListEl) return;

                let selectedTags = [];

                // Destroy the existing Tagify instance if it exists
                if (TagifyUserListInstance) {
                    TagifyUserListInstance.removeAllTags();
                    TagifyUserListInstance.destroy();
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
            <strong>${this.value.length ? 'Select remaining' : 'Select All'}</strong>
            <span>${suggestions.length} products</span>
        </div>
    `;
                }

                // Initialize Tagify - ASSIGN TO GLOBAL VARIABLE
                TagifyUserListInstance = new Tagify(TagifyUserListEl, {
                    tagTextProp: 'name',
                    enforceWhitelist: true,
                    skipInvalid: true,
                    dropdown: {
                        closeOnSelect: false,
                        enabled: 0,
                        classname: 'users-list',
                        searchKeys: ['name']
                    },
                    templates: {
                        tag: tagTemplate,
                        dropdownItem: suggestionItemTemplate,
                        dropdownHeader: dropdownHeaderTemplate
                    },
                    whitelist: selectedTags,
                });

                // Reapply the selected tags
                TagifyUserListInstance.removeAllTags();
                TagifyUserListInstance.addTags(selectedTags);

                // Attach events listeners
                TagifyUserListInstance.on('dropdown:select', onSelectSuggestion)
                    .on('edit:start', onEditStart);

                function onSelectSuggestion(e) {
                    if (e.detail.elm.classList.contains(`${TagifyUserListInstance.settings.classNames.dropdownItem}__addAll`))
                        TagifyUserListInstance.dropdown.selectAll();
                }

                function onEditStart({detail: {tag, data}}) {
                    TagifyUserListInstance.setTagTextNode(tag, `${data.name}`);
                }
            }

            function tagifyProductListEdit(selectedData = []) {
                const TagifyUserListElEdit = document.querySelector('#TagifyUserListEdit');

                if (!TagifyUserListElEdit) return;

                let selectedTags = selectedData;

                // Destroy the existing Tagify instance if it exists
                if (TagifyUserListEditInstance) {
                    TagifyUserListEditInstance.removeAllTags();
                    TagifyUserListEditInstance.destroy();
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
            <strong>${this.value.length ? 'Select remaining' : 'Select All'}</strong>
            <span>${suggestions.length} products</span>
        </div>
    `;
                }

                // Initialize Tagify - ASSIGN TO GLOBAL VARIABLE
                TagifyUserListEditInstance = new Tagify(TagifyUserListElEdit, {
                    tagTextProp: 'name',
                    enforceWhitelist: true,
                    skipInvalid: true,
                    dropdown: {
                        closeOnSelect: false,
                        enabled: 0,
                        classname: 'users-list',
                        searchKeys: ['name']
                    },
                    templates: {
                        tag: tagTemplate,
                        dropdownItem: suggestionItemTemplate,
                        dropdownHeader: dropdownHeaderTemplate
                    },
                    whitelist: selectedTags,
                });

                // Reapply the selected tags
                TagifyUserListEditInstance.removeAllTags();
                TagifyUserListEditInstance.addTags(selectedTags);

                // Attach events listeners
                TagifyUserListEditInstance.on('dropdown:select', onSelectSuggestion)
                    .on('edit:start', onEditStart);

                function onSelectSuggestion(e) {
                    if (e.detail.elm.classList.contains(`${TagifyUserListEditInstance.settings.classNames.dropdownItem}__addAll`))
                        TagifyUserListEditInstance.dropdown.selectAll();
                }

                function onEditStart({detail: {tag, data}}) {
                    TagifyUserListEditInstance.setTagTextNode(tag, `${data.name}`);
                }
            }

            function tagifyProductListSelection(selectedData = []) {
                const TagifyProductEl = document.querySelector('#TagifyProductList');

                if (!TagifyProductEl) return;

                let selectedTags = selectedData;

                // Destroy the existing Tagify instance if it exists
                if (TagifyProductListInstance) {
                    TagifyProductListInstance.removeAllTags();
                    TagifyProductListInstance.destroy();
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

                // Initialize Tagify - ASSIGN TO GLOBAL VARIABLE
                TagifyProductListInstance = new Tagify(TagifyProductEl, {
                    tagTextProp: 'name',
                    enforceWhitelist: true,
                    skipInvalid: true,
                    maxTags: 1,
                    dropdown: {
                        closeOnSelect: true,
                        enabled: 0,
                        classname: 'users-list',
                        searchKeys: ['name']
                    },
                    templates: {
                        tag: tagTemplate,
                        dropdownItem: suggestionItemTemplate
                    },
                    whitelist: productList
                });

                // Reapply the selected tags
                TagifyProductListInstance.removeAllTags();
                if (selectedTags.length > 0) {
                    TagifyProductListInstance.addTags([selectedTags[0]]);
                }

                // Attach event listener
                TagifyProductListInstance.on('edit:start', onEditStart);
                TagifyProductListInstance.on('add', function (e) {
                    TagifyProductListInstance.dropdown.hide();
                });

                function onEditStart({detail: {tag, data}}) {
                    TagifyProductListInstance.setTagTextNode(tag, `${data.name}`);
                }
            }

            function tagifyProductListEditSelection(selectedData = []) {
                const TagifyProductEditEl = document.querySelector('#TagifyProductListEdit');

                if (!TagifyProductEditEl) return;

                let selectedTags = selectedData;

                // Destroy the existing Tagify instance if it exists
                if (TagifyProductListEditInstance) {
                    TagifyProductListEditInstance.removeAllTags();
                    TagifyProductListEditInstance.destroy();
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

                // Initialize Tagify - ASSIGN TO GLOBAL VARIABLE
                TagifyProductListEditInstance = new Tagify(TagifyProductEditEl, {
                    tagTextProp: 'name',
                    enforceWhitelist: true,
                    skipInvalid: true,
                    maxTags: 1,
                    dropdown: {
                        closeOnSelect: true,
                        enabled: 0,
                        classname: 'users-list',
                        searchKeys: ['name']
                    },
                    templates: {
                        tag: tagTemplate,
                        dropdownItem: suggestionItemTemplate
                    },
                    whitelist: productList
                });

                // Reapply the selected tags
                TagifyProductListEditInstance.removeAllTags();
                if (selectedTags.length > 0) {
                    TagifyProductListEditInstance.addTags([selectedTags[0]]);
                }

                // Attach event listener
                TagifyProductListEditInstance.on('edit:start', onEditStart);
                TagifyProductListEditInstance.on('add', function (e) {
                    TagifyProductListEditInstance.dropdown.hide();
                });

                function onEditStart({detail: {tag, data}}) {
                    TagifyProductListEditInstance.setTagTextNode(tag, `${data.name}`);
                }
            }

            $('#newImage').on('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#newImagePreview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#preview').hide();
                }
            });

            let x = 0;
            let y = 0;
            $('#newImagePreview').click(function (e) {
                const offset = $(this).offset();
                x = ((e.pageX - offset.left) / $(this).width()) * 100;
                y = ((e.pageY - offset.top) / $(this).height()) * 100;
                console.log(`X: ${x}%, Y: ${y}%`);
                let type = $('.sliderType').val();
                if (type == 2) {
                    $('#addProductModal').modal('show')
                }
                // open modal to select product and save
            });
            $('#currentImage').click(function (e) {
                const offset = $(this).offset();
                x = ((e.pageX - offset.left) / $(this).width()) * 100;
                y = ((e.pageY - offset.top) / $(this).height()) * 100;
                console.log(`X: ${x}%, Y: ${y}%`);
                let type = $('#edit_slider_type').val();
                console.log(type);
                if (type == 2) {
                    $('#editProductModal').modal('show')
                }
                // open modal to select product and save
            });

            $('#editProductSubmit').on('click', function () {

                if (TagifyProductListEditInstance.value && TagifyProductListEditInstance.value.length > 0) {
                    let selectedProduct = TagifyProductListEditInstance.value[0];

                    let data = {
                        'value': selectedProduct.value,
                        'name': selectedProduct.name,
                        'avatar': selectedProduct.avatar,
                        'coordinate': {
                            'x': x,
                            'y': y,
                        },
                    };

                    // Add to the main list
                    TagifyUserListEditInstance.settings.whitelist.push(data);
                    TagifyUserListEditInstance.addTags([data]);

                    // Close modal
                    $('#editProductModal').modal('hide');

                    // Clear the product selection modal
                    TagifyProductListEditInstance.removeAllTags();
                } else {
                    toastr.warning('Please select a product first');
                }
            });
        });
    </script>

@endpush
