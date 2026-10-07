<div class="col-xl-4 col-md-6 mb-4">
    <div class="card h-100">
        <div class="card-body">
            <div class="bg-label-primary rounded-3 text-center mb-3">
                @if (@$recentEvent->file && Storage::exists(@$recentEvent->file) && isImage($recentEvent->file))
                    <img class="img-fluid" src="{{ getFilePath(@$recentEvent->file) }}" alt="Card girl image"
                         width="100%" style="height: 200px !important"/>
                @else
                    <img class="img-fluid" src="{{ asset('assets/img/illustrations/girl-with-laptop.png') }}"
                         alt="Fallback image" width="140"/>
                @endif
            </div>

            @if (isset($recentEvent) && $recentEvent)
                <h4 class="mb-2 pb-1">{{ _trans('keyword.Upcoming Event') }}: {{ @$recentEvent->name }}
                </h4>
                <p class="small">{{ Str::limit(@$recentEvent->description, 200) }}</p>

                <div class="row mb-3 g-3">
                    <div class="col-6">
                        <div class="d-flex">
                            <div class="avatar flex-shrink-0 me-2">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class="ti ti-calendar-event ti-md"></i></span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-nowrap">{{ _trans('keyword.Event Type') }}:
                                    {{ @$recentEvent->eventType->name ?? 'N/A' }}</h6>
                                <h6 class="mb-0 text-nowrap">{{ _trans('keyword.Location') }}:
                                    {{ @$recentEvent->location ?? 'N/A' }}</h6>
                            </div>
                        </div>
                        <div class="d-flex mt-2">
                            <div class="avatar flex-shrink-0 me-2">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class="ti ti-clock ti-md"></i></span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-nowrap">{{ _trans('keyword.From') }}:
                                    {{ dateFormatwithTime(@$recentEvent->start_date) }}</h6>
                                <h6 class="mb-0 text-nowrap">{{ _trans('keyword.TO') }}:
                                    {{ dateFormatwithTime(@$recentEvent->end_date) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <a href="javascript:void(0);" class="btn btn-primary w-100">{{ _trans('keyword.Visit Event') }}</a> --}}
                <button data-bs-target="#detailsModal" data-bs-toggle="modal"
                        class="btn btn-primary text-nowrap add-new-rol w-100"
                        data-id="{{ @$recentEvent->id }}">
                    {{ _trans('keyword.View Details') }}
                </button>

            @else
                <h4 class="mb-2 pb-1">{{ _trans('keyword.No upcoming events') }}</h4>
            @endif
        </div>
    </div>
</div>

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
                {{-- <x-event-detail/> --}}
               <div class="d-flex align-items-center justify-content-center w-100">
                   <img  id="event_image" src="{{getFilePath(null)}}" alt="event_image" class="w-100">
               </div>

                <div class="d-flex justify-content-between align-items-center mt-3" id="button-container">
                    <div id="event-details" class="d-flex">
                        <div class="avatar flex-shrink-0 me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-calendar-event ti-md"></i></span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-nowrap" id="event_type"></h6>
                            <h6 class="mb-0 text-nowrap" id="event_location"></h6>
                        </div>
                    </div>

                    <div id="date-details" class="d-flex">
                        <div class="avatar flex-shrink-0 me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-clock ti-md"></i></span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-nowrap" id="start_date"></h6>
                            <h6 class="mb-0 text-nowrap" id="end_date"></h6>
                        </div>
                    </div>

                    <div class="btn-group">
                                <a class="mb-0 p-2 bg-label-primary rounded text-primary cursor-pointer" id="link" href="#" target="_blank"><i class="ti ti-link"></i></a>


                        <div class="d-flex align-items-center" id="download_div">
                            <div class="avatar flex-shrink-0 me-2">
                                <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-download"></i></span>
                            </div>
                            <div >
                                <a class="mb-0" id="download_link" href="#" target="_blank" download>Download</a>
                            </div>
                        </div>
                    </div>
                </div>


                <hr>

                <div class="container">
                    <p id="description"></p>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>

    $(document).ready(function () {
        $('.add-new-rol').on('click', function () {
            const eventId = $(this).data('id');
            console.log("event id", eventId)

            $.ajax({
                url: `/event-management/event/details/${eventId}`,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    if (response.status === 200) {
                        // Update event details
                        $('#detailsTitle').text(response.data.name || 'N/A');
                        $('#event_type').text('Event Type: ' + (response.data.event_type.name || 'N/A'));
                        $('#event_location').text('Location: ' + (response.data.location || 'N/A'));
                        $('#start_date').text('From: ' + (response.data.start_date || 'N/A'));
                        $('#end_date').text('TO: ' + (response.data.end_date || 'N/A'));
                        $('#description').text(response.data.description || '');
                        // $('#link').attr('href', response.data.event_url || '#').text(response.data.event_url ? 'Visit Event' : 'N/A');
                        if (response.data.event_url) {
                            $('#link')
                                .attr('href', response.data.event_url)
                                .text('Visit Event')
                                .show();
                        } else {
                            $('#link')
                                .removeAttr('href')
                                .text('N/A')
                                .hide();
                        }


                        // Update download link
                        $('#download_link').attr('href', response.data.file || '#');

                        if (isImage(response.data.file)) {
                            $('#event_image').attr('src', response.data.file || '#');


                            // File is an image: hide the download button and show 3 buttons in a row
                            document.getElementById('download_div').setAttribute('style', 'display: none !important');
                            document.getElementById('button-container').classList.add('d-flex', 'flex-row');

                            // Optionally, you can update other content like event details if needed
                            document.getElementById('event-details').style.display = 'block';
                            document.getElementById('date-details').style.display = 'block';
                        } else {
                            // File is not an image: show all buttons and arrange in two groups (left-right)
                            document.getElementById('download_div').style.display = 'block';
                            document.getElementById('button-container').classList.remove('d-flex', 'flex-row');

                            // Ensure event details are always displayed
                            document.getElementById('event-details').style.display = 'block';
                            document.getElementById('date-details').style.display = 'block';

                            // Update the button-container with the event details, without overwriting content
                            const eventType = response.data.event_type ? response.data.event_type.name : 'N/A';
                            const eventLocation = response.data.location || 'N/A';
                            const startDate = response.data.start_date || 'N/A';
                            const endDate = response.data.end_date || 'N/A';
                            const eventUrl = response.data.event_url || '#';
                            const downloadLink = response.data.file || '#';

                            const layout = `
            <div class="d-flex justify-content-around align-items-center">
                <div>
                    <div class="d-flex mt-4">
                        <div class="avatar flex-shrink-0 me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-calendar-event ti-md"></i></span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-nowrap">${eventType}</h6>
                            <h6 class="mb-0 text-nowrap">${eventLocation}</h6>
                        </div>
                    </div>

                    <div class="d-flex mt-2">
                        <div class="avatar flex-shrink-0 me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-clock ti-md"></i></span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-nowrap">${startDate}</h6>
                            <h6 class="mb-0 text-nowrap">${endDate}</h6>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="d-flex mt-2 align-items-center">
                        <div class="avatar flex-shrink-0 me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-link"></i></span>
                        </div>
                        <div>
                            <a class="mb-0" href="${eventUrl}" target="_blank">Visit Event</a>
                        </div>
                    </div>

                    <div class="d-flex mt-2 align-items-center">
                        <div class="avatar flex-shrink-0 me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-download"></i></span>
                        </div>
                        <div>
                            <a class="mb-0" href="${downloadLink}" target="_blank" download>Download</a>
                        </div>
                    </div>
                </div>
            </div>
        `;

                            // Insert the new layout into the button-container
                            document.getElementById('button-container').innerHTML = layout;
                        }
                    } else {
                        alert(response.message || 'Failed to fetch event details.');
                    }

                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while fetching event details.');
                }
            });
        });


        function isImage(filePath) {
            const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.tiff', '.svg'];
            const fileExtension = filePath.slice(((filePath.lastIndexOf(".") - 1) >>> 0) + 2).toLowerCase();

            return imageExtensions.includes(`.${fileExtension}`);
        }

    });
</script>

