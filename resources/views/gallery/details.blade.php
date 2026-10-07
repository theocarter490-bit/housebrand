@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Gallery') . ' ' . _trans('keyword.Details'))

@section('content')

    <style>
        .image-upload-container {
            position: relative;
            width: 100%;
            height: 200px;
            border: 2px dashed #d9dee3;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-upload-container:hover {
            border-color: #696cff;
            background-color: #f1f2f6;
        }

        .image-upload-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .upload-placeholder {
            text-align: center;
            color: #a1acb8;
            z-index: 0;
        }

        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .delete-btn-wrapper {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            background: white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>

    <div class="container-fluid flex-grow-1 container-p-y" id="content-wrapper">
        <form method="post" action="{{ route('gallery.details.store') }}" enctype="multipart/form-data">
            @csrf
            {!! breadcrumb('Gallery Images', ['gallery/list' => 'Gallery', 'gallery' => 'Gallery Images']) !!}
            <input type="text" name="gallery_id" hidden value="{{ $gallery->id }}">

            <div class="row g-4 mb-4" id="galley_container">
                @if($gallery->details->count() == 0)
                    <div class="col-12 col-md-6 col-lg-4 section">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body p-3">
                                <div class="delete-btn-wrapper">
                                    <button type="button" class="btn btn-icon btn-sm btn-label-danger deleteSection">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>

                                <div class="image-upload-container mb-3">
                                    <div class="upload-placeholder">
                                        <i class="ti ti-cloud-upload ti-xl mb-2"></i>
                                        <p class="mb-0 small fw-bold">Upload Image</p>
                                    </div>
                                    <img class="image-upload-preview" src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Preview">
                                    <input name="data[${count}][image]" class="file-input" type="file" onchange="previewImage(this)">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label text-muted">Title</label>
                                    <input class="form-control" name="data[${count}][title]" placeholder="Image title">
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-muted">Details</label>
                                    <textarea class="form-control" name="data[${count}][description]" rows="2" placeholder="Short description"></textarea>
                                </div>
                                <input type="text" name="id" value="" hidden>
                            </div>
                        </div>
                    </div>
                @endif

                @foreach ($gallery->details as $detail)
                    <div class="col-12 col-md-6 col-lg-4 section">
                        <input type="text" name="data[{{$loop->iteration-1}}][id]" value="{{ $detail->id }}" hidden>
                        <div class="card h-100 shadow-sm">
                            <div class="card-body p-3">
                                <div class="delete-btn-wrapper">
                                    <button type="button" class="btn btn-icon btn-sm btn-label-danger deleteSection">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>

                                <div class="image-upload-container mb-3">
                                    <div class="upload-placeholder">
                                        <i class="ti ti-cloud-upload ti-xl mb-2"></i>
                                        <p class="mb-0 small fw-bold">Change Image</p>
                                    </div>
                                    <img class="image-upload-preview" src="{{ getFilePath($detail->image) }}" alt="Preview">
                                    <input
                                        class="file-input @error('data.*.image') is-invalid @enderror"
                                        type="file"
                                        name="data[{{ $loop->iteration-1 }}][image]"
                                        onchange="previewImage(this)"
                                    >
                                </div>
                                @error('data.' . ($loop->iteration-1) . '.image')
                                <small class="text-danger d-block mb-2">{{ $message }}</small>
                                @enderror

                                <div class="form-group mb-3">
                                    <label class="form-label text-muted">{{ _trans('keyword.Title') }}</label>
                                    <input
                                        class="form-control @error('data.*.title') is-invalid @enderror"
                                        value="{{ old('data.' . ($loop->iteration-1) . '.title', $detail->title) }}"
                                        name="data[{{ $loop->iteration-1 }}][title]"
                                        placeholder="Image title"
                                    >
                                    @error('data.' . ($loop->iteration-1) . '.title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-muted">{{ _trans('keyword.Details') }}</label>
                                    <textarea
                                        class="form-control @error('data.*.description') is-invalid @enderror"
                                        name="data[{{ $loop->iteration-1 }}][description]"
                                        rows="2"
                                        placeholder="Short description"
                                    >{{ old('data.' . ($loop->iteration-1) . '.description', $detail->details) }}</textarea>
                                    @error('data.' . ($loop->iteration-1) . '.description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(hasPermission('create_image'))
                <div class="card-action-sticky-bottom">
                    <div class="row justify-content-end g-3">
                        <div class="col-12 col-sm-auto">
                            <button type="button" id="add_image" class="btn btn-label-primary w-100">
                                <i class="ti ti-plus ti-xs me-2"></i>{{_trans('keyword.Add Image')}}
                            </button>
                        </div>
                        <div class="col-12 col-sm-auto">
                            <button type="submit" id="save" class="btn btn-primary w-100">
                                {{_trans('keyword.Save Changes')}}
                                <span class="loader ms-2"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        let count = {{ count($gallery->details) }};

        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var preview = $(input).siblings('.image-upload-preview');

                reader.onload = function(e) {
                    preview.attr('src', e.target.result);
                    preview.show();
                }
                reader.readAsDataURL(input.files[0]);
            }
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
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $(button).closest('.section').remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Section Removed',
                        timer: 1000,
                        showConfirmButton: false
                    });
                }
            });
        });

        $('#add_image').on('click', function() {
            let template = `
            <div class="col-12 col-md-6 col-lg-4 section">
                <div class="card h-100 shadow-sm">
                    <div class="card-body p-3">
                        <div class="delete-btn-wrapper">
                            <button type="button" class="btn btn-icon btn-sm btn-label-danger deleteSection">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>

                        <div class="image-upload-container mb-3">
                            <div class="upload-placeholder">
                                <i class="ti ti-cloud-upload ti-xl mb-2"></i>
                                <p class="mb-0 small fw-bold">Upload Image</p>
                            </div>
                            <img class="image-upload-preview" src="{{ asset('assets/img/placeholder/placeholder.png') }}" alt="Preview">
                            <input name="data[${count}][image]" class="file-input" type="file" onchange="previewImage(this)">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label text-muted">Title</label>
                            <input class="form-control" name="data[${count}][title]" placeholder="Image title">
                        </div>

                        <div class="form-group">
                            <label class="form-label text-muted">Details</label>
                            <textarea class="form-control" name="data[${count}][description]" rows="2" placeholder="Short description"></textarea>
                        </div>
                        <input type="text" name="id" value="" hidden>
                    </div>
                </div>
            </div>
        `;

            $('#galley_container').append(template);
            count++;
        });
    </script>
@endpush
