@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Notice'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Notice Board'), [
            '#' => _trans('keyword.Notice Management'),
            'emailcampaign' => _trans('keyword.Notice Board'),
        ]) !!}
        <div class="app-ecommerce-category">

            <div class="row">

                @forelse ($notices as $notice)
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-header">{{ $notice->title }}</h5>
                                <button class="btn btn-primary me-4" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseExample{{ $notice->id }}" aria-expanded="false"
                                        aria-controls="collapseExample{{ $notice->id }}">
                                    View Details
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="card-text d-flex justify-content-between align-items-center">
                                    <span>Published By: {{ $notice->createdBy->name ?? '---' }}</span>
                                    <span>Published At: {{ dateFormatwithTime($notice->published_at) }}</span>
                                </p>

                                <div class="collapse" id="collapseExample{{ $notice->id }}">
                                    <div class="d-grid d-sm-flex p-3 border">
                                            <span>
                                                {!! $notice->description !!}
                                            </span>
                                    </div>

                                    @php
                                        $filePath = getFilePath($notice->attachment);
                                        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp','webp'];
                                        $iframeExtensions = ['pdf','xlsx'];
                                    @endphp

                                    @if (Str::endsWith($filePath, '/placeholder/placeholder.png'))
                                        <p class="text-muted text-center mt-2">No Attachment Available</p>
                                    @else
                                        <div class="d-grid d-sm-flex p-3 border justify-content-center">
                                            @if (in_array($fileExtension, $imageExtensions))
                                                <img src="{{ $filePath }}" alt="Notice Attachment"
                                                     class="img-fluid img-responsive"
                                                     style="max-width:600px; height:auto;">
                                            @elseif(in_array($fileExtension, $iframeExtensions))
                                                <div class="embed-responsive embed-responsive-16by9"
                                                     style="width: 100%; height: 800px;">
                                                    <iframe src="{{ $filePath }}" title="Notice Attachment"
                                                            frameborder="0" allowfullscreen
                                                            style="width: 100%; height: 100%; border: 0;">
                                                    </iframe>
                                                </div>
                                            @else
                                                <a href="{{ $filePath }}" class="btn btn-primary" target="_blank"
                                                   rel="noopener noreferrer">
                                                    Download Attachment
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-primary text-center m-5">No Notice Available</p>
                    </div>
                @endforelse


            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script></script>
@endpush
