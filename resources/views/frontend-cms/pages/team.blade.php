<div class="card mb-4 section" data-id="image_gallary_with_details">
    <div class="card-header d-flex flex-row justify-content-start align-items-center">
        <h5 class="card-title mb-0">{{_trans('keyword.Team image with details')}}</h5>

        <button type="button" class="btn btn-primary ms-2 image_gallary_with_description_add_button"><i
                class="ti ti-plus ti-xs"></i></button>

    </div>
    <div class="row card-body justify-content-center align-items-center">
        <div class="col-12 border-end gallaryImageWithDetailsContainer" id="gallaryImageWithDetailsContainer">

            @forelse  ($data->items as $item)
                <div class="row position-relative gallaryImageWithDetailsCard mb-2 border-bottom" style="height: 150px">
                    <div class="col-6 border-end">
                        <label for="">{{_trans('keyword.Title')}}</label>
                        <input class="form-control mb-4" value="{{ $item->name }}" name='title' id="" />
                        <label for="">{{_trans('keyword.Descripiton/Amount')}}</label>
                        <input class="form-control" value="{{ $item->description }}" name='description'
                            id="" />
                    </div>
                    <div class="col-3 border-end d-flex justify-content-center align-items-center position-relative"
                        style="height: 100%">
                        <div
                            class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                            <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                            <span style="font-size:1rem !important">{{_trans('keyword.Input image')}}</span>
                        </div>
                        <input name='image' class="opacity-0 position-absolute top-50 start-50 translate-middle p-3"
                            type="file" onchange="loadFile(event)" />
                        <input type="text" name="id" value="{{ $item->id }}" hidden>
                    </div>
                    <div class="col-3" style="height: 100%">
                        <img id='preview_img' class=" preview_img" style="height: 100%; max-Width:100%"
                            src="{{ getFilePath($item->image) }}" alt="Current profile photo" />
                    </div>
                    <button type="button" class="btn text-danger position-absolute deleteSection"
                        style="right: 0%; width:50px;"><i class="ti ti-trash"></i></button>


                </div>
            @empty
                <div class="row position-relative gallaryImageWithDetailsCard mb-1" style="height: 150px">
                    <div class="col-6 border-end">
                        <label for="">{{_trans('keyword.Name')}}</label>
                        <input class="form-control mb-4" name='title' id="" />
                        <label for="">{{_trans('keyword.Designation')}}</label>
                        <input class="form-control" name='description' id="" />
                    </div>
                    <div class="col-3 border-end d-flex justify-content-center align-items-center position-relative"
                        style="height: 100%">
                        <div
                            class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                            <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                            <span style="font-size:1rem !important">{{_trans('keyword.Input image')}}</span>
                        </div>
                        <input name='image' class="opacity-0 position-absolute top-50 start-50 translate-middle p-3"
                            type="file" onchange="loadFile(event)" />
                        <input type="text" name="id" value="" hidden>

                    </div>
                    <div class="col-3" style="height: 100%">
                        <img id='preview_img' class=" preview_img" style="height: 100%; max-Width:100%"
                            src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Current profile photo" />
                    </div>
                    <button type="button" class="btn text-danger position-absolute deleteSection"
                        style="right: 0%; width:50px;"><i class="ti ti-trash"></i></button>


                </div>
            @endforelse
        </div>

    </div>
</div>


@push('scripts')
    <script>
        var loadFile = function(event) {

            var input = event.target;
            var file = input.files[0];
            var type = file.type;
            var output = event.currentTarget.parentNode.nextElementSibling.children[0];
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }
        };

        $(document).on("click", ".image_gallary_with_description_add_button", function() {
            $(this).parent().siblings('div').eq(0).children('.gallaryImageWithDetailsContainer').append(
                imageGallaryWithDetailsContainerDesignHTML());
        });

        function imageGallaryWithDetailsContainerDesignHTML() {
            return `<div class="row mt-2 border-top position-relative gallaryImageWithDetailsCard" style="height: 150px">
                            <div class="col-6 border-end">
                                <label for="">Title</label>
                                <input class="form-control mb-4"  name='title' id=""/>
                                <label for="">Descripiton/Amount</label>
                                <input class="form-control"  name='description'  id=""/>
                            </div>
                            <div class="col-3 border-end d-flex justify-content-center align-items-center position-relative"
                                style="height: 100%">
                                <div
                                    class="branner_input_placeholder position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-plus ti-xs" style="font-size:2rem !important"></i>
                                    <span style="font-size:1rem !important">Input image</span>
                                </div>
                                <input
                                    class="opacity-0 position-absolute top-50 start-50 translate-middle p-3" name='image' type="file"
                                    onchange="loadFile(event)" />
                                    <input type="text" name="id" value="" hidden>
                            </div>
                            <div class="col-3" style="height: 100%">
                                <img id='preview_img' class=" preview_img" style="height: 100%; max-Width:100%"
                                    src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Current profile photo" />
                            </div>
                            <button type="button" class="btn text-danger position-absolute deleteSection" style="right: 0%;width:50px;"><i class="ti ti-trash"></i></button>
                        </div>`;
        }

        $(document).on("click", ".deleteSection", function() {

            let button = $(this);
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
            }).then(function(result) {
                if (result.value) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Deleted!',
                        text: 'Section Removed',
                        customClass: {
                            confirmButton: 'btn btn-success waves-effect waves-light'
                        }
                    });
                    $(button).parent().remove();
                }
            });
        });
    </script>
@endpush
