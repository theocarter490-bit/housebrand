@extends('layouts.master')

@section('title', $title ?? ucfirst($source).' '. _trans('keyword.Documents'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb( _trans('keyword.Documents') .' ('. (optional($data)->title ?? 'Inhouse') .')',
            ['#'=>_trans('keyword.Project').' '._trans('keyword.Management'),
            '##'=> ucfirst($source).' '. _trans('keyword.Documents')]) !!}

        <div class="app-ecommerce-category">
            @if($data != null)
                {!! projectTabMenu($data, $data->title, $data->id) !!}
            @endif

            <div class="card">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

                    <form action method="GET" class="w-100 w-md-auto">
                        <div class="row g-2"> <div class="col-12 col-md-5">
                                <input class="form-control" data-allow-clear="true" type="search" name="search" placeholder="Search" value="{{ request('search') }}">
                            </div>

                            <div class="col-12 col-md-4">
                                <select class="form-control select2 document-status" name="status" data-placeholder="Select status">
                                    <option value=""></option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <button class="btn btn-primary w-100" type="submit">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="w-100 w-md-auto text-end">
                        <button data-bs-target="#addDocumentModal" data-bs-toggle="modal" class="btn btn-primary  d-inline-flex sm-w-full ">
                            <i class="ti ti-plus"></i> {{ _trans('keyword.Add Document') }}
                        </button>
                    </div>
                </div>

                <div class="card-datatable table-responsive text-nowrap">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th scope="col" width="5%">{{ _trans('keyword.SL') }}</th>
                            <th scope="col" width="20%">{{ _trans('keyword.title') }}</th>
                            <th scope="col" width="15%">{{ _trans('keyword.file') }}</th>
                            <th scope="col" width="15%">{{ _trans('keyword.Status') }}</th>
                            <th scope="col" width="20%">{{ _trans('keyword.Info') }}</th>
                            <th scope="col" width="10%">{{ _trans('keyword.Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($document as $key => $doc)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $doc->title }}</span>
                                </td>
                                <td>
                                    @if($doc->file)
                                        {!!  getFileElement(getFilePath($doc->file)) !!}
                                    @else
                                        <span class="text-muted">{{ _trans('keyword.N/A') }}</span>
                                    @endif
                                </td>

                                <td>
                                    @if($doc->active_status == 1)
                                        <div class="custom-status-container">
                                            <label class="switch switch-success" style="margin-bottom: 5px;">
                                                <input type="checkbox" class="switch-input changeStatus" data-id={{$doc->id}} checked />
                                                <span class="switch-toggle-slider">
                                                <span class="switch-on">
                                                    <i class="ti ti-check"></i>
                                                </span>
                                                <span class="switch-off">
                                                    <i class="ti ti-x"></i>
                                                </span>
                                            </span>
                                            </label>
                                        </div>
                                        <span class="badge custom-bg-success">{{ _trans('keyword.Active') }}</span>

                                    @else
                                        <div class="custom-status-container">
                                            <label class="switch switch-success" style="margin-bottom: 5px;">
                                                <input type="checkbox" class="switch-input changeStatus" data-id={{$doc->id}} />
                                                <span class="switch-toggle-slider">
                                                <span class="switch-on">
                                                    <i class="ti ti-check"></i>
                                                </span>
                                                <span class="switch-off">
                                                    <i class="ti ti-x"></i>
                                                </span>
                                            </span>
                                            </label>
                                        </div>
                                        <span class="badge custom-bg-danger">{{ _trans('keyword.Inactive') }}</span>
                                    @endif
                                </td>

                                <td>
                                    {!! dataInfo($doc) !!}
                                </td>

                                <td>
                                    <div class="d-inline-block text-nowrap">
                                        <button class="btn dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical me-2"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            <a href="javascript:0;" class="dropdown-item docEditButton" data-bs-toggle="modal" data-bs-target="#editDocumentModal" data-id="{{ $doc->id }}" data-source="{{ $doc->source }}" data-source_id="{{ $doc->source_id }}">
                                                <i class="ti ti-edit"></i> {{ _trans('keyword.Edit') }}
                                            </a>
                                            <a href="javascript:0;" class="dropdown-item docDeleteButton text-danger" data-id="{{ $doc->id }}">
                                                <i class="ti ti-trash"></i> {{ _trans('keyword.Delete') }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ _trans('keyword.Add Document') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="documentForm" method="POST" action="{{ route('project-management.document.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="source" id="source" value="{{ old('source', $source) }}">
                            <input type="hidden" name="source_id" id="source_id" value="{{ old('source_id', $source_id) }}">

                            <div class="mb-3">
                                <label for="document-title" class="form-label">{{ _trans('keyword.Title') }} <span class="text-danger">*</span></label>
                                <input type="text" id="document-title" class="form-control @error('title') is-invalid @enderror"
                                       name="title" value="{{ old('title') }}" required placeholder="{{ _trans('keyword.Enter title') }}">
                                @error('title')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="document-file" class="form-label">{{ _trans('keyword.File') }} <span class="text-danger">*</span></label>
                                <input type="file" id="document-file" required class="form-control @error('file') is-invalid @enderror"
                                       name="file">
                                @error('file')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="document-status" class="form-label">{{ _trans('keyword.Status') }}</label>
                                <select id="document-status" class="select2 form-select @error('status') is-invalid @enderror" name="status">
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>{{ _trans('keyword.Active') }}</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>{{ _trans('keyword.Inactive') }}</option>
                                </select>
                                @error('status')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{ _trans('keyword.Save') }}
                                    <span class="loader"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editDocumentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ _trans('keyword.Edit Document') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editDocumentForm" method="POST" action="{{ route('project-management.document.update') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="source" id="source">
                            <input type="hidden" name="source_id" id="source_id">
                            <input type="hidden" name="id" id="docId">

                            <div class="mb-3">
                                <label for="document-title" class="form-label">{{ _trans('keyword.Title') }} <span class="text-danger">*</span></label>
                                <input type="text" id="document-title" required class="form-control @error('title') is-invalid @enderror" name="title" placeholder="{{ _trans('keyword.Enter title') }}">
                                @error('title')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="document-status" class="form-label">{{ _trans('keyword.Status') }}</label>
                                <select id="document-status" class="select2 form-select @error('status') is-invalid @enderror" name="status">
                                    <option value="1">{{ _trans('keyword.Active') }}</option>
                                    <option value="0">{{ _trans('keyword.Inactive') }}</option>
                                </select>
                                @error('status')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="mb-3 col-12 col-md-6">
                                    <label for="document-file" class="form-label">{{ _trans('keyword.File') }}</label>
                                    <input type="file" id="document-file" class="form-control @error('file') is-invalid @enderror" name="file">
                                    @error('file')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <div id="file-preview" class="file-preview"></div>
                                </div>

                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{ _trans('keyword.Update') }}
                                    <span class="loader"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('.document-status').select2({
                allowClear: true,
                width: '100%' // Ensures select2 fits the new responsive column width
            })

            $('.docEditButton').on('click', function() {
                var docId = $(this).data('id');
                var source = $(this).data('source');
                var sourceId = $(this).data('source_id');

                $.ajax({
                    url: '{{ route('project-management.document.edit') }}',
                    method: 'GET',
                    data: {
                        id: docId,
                        source: source,
                        source_id: sourceId
                    },
                    success: function(response) {
                        if (response) {
                            $('#editDocumentModal #document-title').val(response.title);
                            $('#editDocumentModal #document-status').val(response.active_status).trigger('change.select2');

                            $('#docId').val(response.id);

                            $('#editDocumentModal #source').val(response.source);
                            $('#editDocumentModal #source_id').val(response.source_id);

                            let filePreview = $('#editDocumentModal .file-preview');
                            filePreview.html('');
                            if (response.file) {
                                filePreview.html(response.file);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            });
        });


        $('.docDeleteButton').on('click', function() {

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
                        url: '{{ route('project-management.document.destroy') }}',
                        method: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: id,
                        },
                        success: function (response) {
                            if(response.status == 200){
                                toastr.success(response.message);
                                location.reload();
                            }else{
                                toastr.error(response.message);
                            }

                        },
                        error: function (error) {
                            console.log(error.responseJSON.message);
                            // handle the error case
                        }
                    });
                }else{
                    location.reload();
                }
            });
        });

        $(document).on('change', '.changeStatus', function() {
            const id = $(this).data('id');
            const formData = new FormData();
            formData.append('id', id);
            formData.append('_token', "{{ csrf_token() }}");

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
                    $.ajax({
                        url: '{{ route('project-management.document.changeStatus') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 200) {
                                toastr.success(response.message);
                                location.reload()
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function (error) {
                            console.error(error);
                            toastr.error(error.responseJSON.message);
                        }
                    });
                }else{
                    location.reload();
                }
            });
        });


    </script>
@endpush
