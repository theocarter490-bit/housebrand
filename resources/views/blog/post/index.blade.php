@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Blog') . ' ' . _trans('keyword.Post'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Blog') . ' ' . _trans('keyword.Post'), [
            '#' => _trans('keyword.Blog'),
            'post' => _trans('keyword.Post'),
        ]) !!}
        <div class="app-ecommerce-category">
            <!-- Blog Post List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Thumbnail') }}</th>
                            <th>{{ _trans('keyword.Title') }}</th>
                            <th>{{ _trans('keyword.Description') }}</th>
                            <th>{{ _trans('keyword.Category') }}</th>
                            <th>{{ _trans('keyword.Publish Status') }}</th>
                            <th>{{ _trans('keyword.Tags') }}</th>
                            <th width="50px">{{ _trans('keyword.Author') }}</th>
                            <th width="100px">{{ _trans('keyword.Action') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Blog Post Details Modal -->
            <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content p-3 md-5">
                        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        <div class="modal-body">
                            <div class="text-center mb-2">
                                <h3 class="role-title mb-2" id="detailsTitle"></h3>
                                <hr>
                            </div>

                            <div class="mb-3" id="bannerContainer">
                                <div id="bannerThumbnail"></div>
                            </div>
                            <div class="mb-3" id="videoContainer">
                                <iframe id="videoFrame" width="100%" height="315" frameborder="0"
                                        allowfullscreen></iframe>
                            </div>
                            <div class="mb-3">
                                <p id="detailsTags"></p>
                            </div>
                            <div class="mb-3">
                                <div id="detailsDescription"></div>
                            </div>
                            <div class="mb-3 d-flex justify-content-between">
                                <p><strong>{{ _trans('keyword.Author') }}:</strong> <span id="authorName"></span></p>
                                <p><strong>{{ _trans('keyword.Post') . ' ' . _trans('keyword.Time') }}:</strong> <span
                                        id="detailsPostTime"></span></p>
                            </div>
                            <hr>
                            <div class="mb-3" id="detailsContent">
                            </div>

                            <div class="col-12 text-center mt-2">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                        aria-label="Close">{{ _trans('keyword.Close') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Blog Post Details Modal -->
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(function () {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('blog.post.index') }}',
                    data: function (d) {
                        d.status = $('#status').val()
                        d.publish_status = $('#publish_status').val()
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'thumbnail',
                        name: 'thumbnail'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'desc',
                        name: 'desc',
                        render: function (data, type, row) {
                            if (data === null || data === '') {
                                return '<div style="width: 200px; white-space: normal; word-wrap: break-word;"> --- </div>';
                            } else {
                                const truncated = data.length > 100 ? data.substr(0, 100) + '...' :
                                    data;
                                return '<div style="width: 200px; white-space: normal; word-wrap: break-word;">' +
                                    truncated + '</div>';
                            }
                        }
                    },
                    {
                        data: 'category',
                        name: 'category.name'
                    },
                    {
                        data: 'publish_status',
                        name: 'publish_status'
                    },
                    {
                        data: 'tags',
                        name: 'tags'
                    },
                    {
                        data: 'author',
                        name: 'author'
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
                    <div class="d-xl-flex gap-3 d-block">
                    <div class="form-group">
                        <label><strong>{{_trans('keyword.Publish Status')}} :</strong></label>
                        <select id='publish_status' class="form-control filter_dropdown select2 blog-post-1" style="width: 200px" data-placeholder="{{_trans('keyword.Select') }} {{_trans('keyword.Status')}}">
                            <option value="">{{_trans('keyword.Select') }} {{_trans('keyword.Publish Status')}}</option>
                            <option value="1">{{_trans('keyword.Published') }}</option>
                            <option value="0">{{_trans('keyword.Unpublished') }}</option>
                         </select>
                    </div>
                  </div>
                 `);
                    $('.data-table').wrap('<div class="overflow-auto"></div>');
                    $(document).ready(function () {
                        $('.dataTables_filter').parent().addClass('c-list-inner');
                    });
                    $('.blog-post-1').select2({
                        allowClear: true,
                    });

                },
                lengthMenu: [10, 20, 50, 70, 100], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Post",
                },

                buttons: [
                        @if (hasPermission('blog_post_create'))
                    {
                        text: '<i class="ti ti-plus ti-xs me-0 me-sm-2"></i><span>Post</span>',
                        className: "create-new btn btn-primary ms-2 waves-effect waves-light text-nowrap",
                        action: function () {
                            window.location.href = '{{ route('blog.post.create') }}';
                        }
                    },
                    @endif

                ],
            });

            $(document).on("click", ".details-button", function () {
                var postId = $(this).data("id");

                $('#detailsTitle').text('');
                $('#authorName').text('');
                $('#detailsPostTime').text('');
                $('#detailsDescription').text('');
                $('#detailsTags').html('');
                $('#detailsVideoUrl').text('');
                $('#detailsBanner').html('');
                $('#bannerThumbnail').html('');
                $('#detailsContent').html('');
                $('#videoFrame').attr('src', '');
                $('#videoContainer').hide();
                $('#bannerContainer').hide();


                $.ajax({
                    url: '/blog/post/post-details/' + postId,
                    type: 'GET',
                    success: function (response) {

                        $('#detailsTitle').text(response.title);
                        $('#authorName').text(response.created_by);
                        $('#detailsPostTime').text(response.created_at);
                        $('#detailsDescription').text(response.desc);

                        var tags = [];
                        try {
                            tags = JSON.parse(response.tags);
                        } catch (e) {
                            console.error('Error parsing tags JSON:', e);
                            tags = [];
                        }

                        var badgesHtml = '';
                        if (Array.isArray(tags) && tags.length > 0) {
                            badgesHtml = tags.map(function (tag) {
                                return `<span class="badge bg-label-dark me-2">${tag.value}</span>`;
                            }).join('');
                        } else {
                            badgesHtml =
                                '<span class="badge bg-secondary">No tags available</span>';
                        }

                        $('#detailsTags').html(badgesHtml);

                        if (response.video_url) {
                            $('#videoFrame').attr('src', response.video_url);
                            $('#videoContainer').show();
                        }

                        $('#detailsBanner').html(response.banner);
                        if (response.banner && !response.banner.endsWith(
                            'img/placeholder/placeholder.png')) {
                            var bannerHtml =
                                `<img class="" style="width:100%; overflow:hidden;" src="${response.banner}" alt="banner" />`;
                            $('#bannerThumbnail').html(bannerHtml);
                            $('#bannerContainer').show();
                        }

                        $('#itemImageBanner').html('');
                        $('#itemDescription').html('');

                        response.content_details.sort((a, b) => a.serial - b.serial);

                        response.content_details.forEach(function (detail) {
                            if (detail.section_type === "banner_image") {
                                detail.items.forEach(function (item) {
                                    $('#detailsContent').append(
                                        `<div class="mb-3">
                                            <img class="" style="width:100%; overflow:hidden;" src="${item.image}" alt="banner" />
                                        </div>`
                                    );
                                });
                            } else if (detail.section_type === 'description') {
                                detail.items.forEach(function (item) {
                                    $('#detailsContent').append(
                                        `<div class="mb-3">
                                            <p>${item.description}</p>
                                        </div>`
                                    );
                                });
                            }
                        });

                        $('#detailsModal').modal('show');
                    },
                    error: function (error) {
                        console.error('Error fetching blog post details:', error);
                        toastr.error(
                            'Failed to fetch blog post details. Please try again later.');
                    }
                });
            });


            $(document).on('change', '.filter_dropdown', function () {
                table.draw();
            });

            $(document).on("click", ".delete-post", function () {
                let id = $(this).data("id");
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
                            url: '{{ route('blog.post.delete') }}',
                            method: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                            },
                            success: function (response) {
                                table.ajax.reload(null, false);
                                toastr.success(response.message);
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    }
                });
            });

            function extractYouTubeVideoId(url) {
                var regex =
                    /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
                var match = url.match(regex);
                return match ? match[1] : null;
            }

            $(document).on('change', '.changePublishStatus', function () {
                const postId = $(this).data('id');
                const formData = new FormData();
                formData.append('id', postId);
                formData.append('_token', "{{ csrf_token() }}");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "To change the post status.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Change it',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ route('blog.post.changePublishStatus') }}',
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                if (response.status === 200) {
                                    toastr.success(response.message);
                                    table.ajax.reload(null, false);
                                } else if (response.status === 403) {
                                    // Handle the case where the post is deactivated
                                    toastr.error(response.message);
                                    table.ajax.reload(null, false);
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function (error) {
                                console.error(error);
                            }
                        });
                    } else {
                        table.ajax.reload(null, false);
                    }
                });
            });


        });
    </script>
@endpush
