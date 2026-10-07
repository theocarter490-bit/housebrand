@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Calender'))
@push('styles')
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/fullcalendar/fullcalendar.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/app-calendar.css')) }}">
@endpush
@section('content')

    <div class="card app-calendar-wrapper">
        <div class="row g-0">
            <!-- Calendar Sidebar -->
            <div class="col app-calendar-sidebar" id="app-calendar-sidebar">
                <div class="border-bottom p-4 my-sm-0 mb-3">
                    @if(hasPermission('event_create'))
                        <div class="d-grid">
                            <a href="{{ route('event-management.event.index') }}"
                               class="btn btn-primary btn-toggle-sidebar" aria-controls="addEventSidebar">
                                <i class="ti ti-plus me-1"></i>
                                <span
                                    class="align-middle">{{ _trans('keyword.Add').' '._trans('keyword.Event') }}</span>
                            </a>
                        </div>
                    @endif
                </div>
                <div class="p-3">
                    <!-- inline calendar (flatpicker) -->
                    <div class="inline-calendar"></div>
                    <hr class="container-m-nx mb-4 mt-3">

                    <!-- Filter -->
                    <div class="mb-3 ms-3">
                        <small class="text-small text-muted text-uppercase align-middle">Filter</small>
                    </div>

                    <div class="form-check mb-2 ms-3">
                        <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all"
                               checked>
                        <label class="form-check-label" for="selectAll">View All</label>
                    </div>

                    <div class="app-calendar-events-filter ms-3">
                        @foreach($eventTypes as $type)
                            <div class="form-check form-check mb-2">
                                <input class="form-check-input input-filter" type="checkbox" id="type_{{$type->id}}"
                                       data-value="{{$type->name}}" data-id="{{$type->id}}"
                                       data-color="{{$type->color}}" checked
                                       style="background-color: {{ $type->color }}; border-color: {{$type->color}}">
                                <label class="form-check-label" for="type_{{$type->id}}">{{$type->name}}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- /Calendar Sidebar -->

            <!-- Calendar & Modal -->
            <div class="col app-calendar-content">
                <div class="card shadow-none border-0">
                    <div class="card-body pb-0">
                        <!-- FullCalendar -->
                        <div id="calendar"></div>
                    </div>
                </div>
                <div class="app-overlay"></div>
                <!-- FullCalendar Offcanvas -->
            </div>
            <!-- /Calendar & Modal -->
        </div>
    </div>



    <!-- Event Details Modal -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                    <button type="button" class="btn-close btn-pinned" id="closeUpdateModal" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column: Event Details -->
                        <div class="col-md-6">
                            <p><strong>Title:</strong> <span id="modalEventTitle"></span></p>
                            <p><strong>Type:</strong> <span id="modalEventType"></span></p>
                            <p><strong>Start:</strong> <span id="modalEventStart"></span></p>
                            <p><strong>End:</strong> <span id="modalEventEnd"></span></p>
                            <p><strong>Event Host:</strong> <span id="modalEventHost"></span></p>
                            <p><strong>Description:</strong> <span id="modalEventDescription"></span></p>
                            <div id="modalEventFile"></div>
                        </div>

                        <!-- Right Column: Additional Content -->
                        <div class="col-md-6" style="height: 400px ; overflow-y : auto">
                            <p><strong>Audience:</strong></p>
                            <div id="modalEventAudience"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="reset" type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                            aria-label="Close">
                        {{ _trans('keyword.Close') }}
                    </button>
                    <a href="#" id="modalEventUrl" target="_blank" class="btn btn-primary">Go to Event</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset(mix('assets/vendor/libs/fullcalendar/fullcalendar.js')) }}"></script>
    <script src="{{ asset(mix('assets/vendor/libs/flatpickr/flatpickr.js')) }}"></script>
    <script>
        $(document).ready(function () {
            // Function to fetch events based on selected types
            function fetchEvents(types = [], type) {
                let queryString = types.length > 0 ? '?types=' + types.join(',') : '';
                if (type === 'none') {
                    queryString += '?type=none';
                }
                $.ajax({
                    url: '/event-management/event/calender-events' + queryString,
                    type: 'GET',
                    data: {eventTypes: types},
                    success: function (response) {
                        let allEvents = response.data;
                        let events = [];

                        allEvents.forEach(event => {
                            events.push({

                                id: event.id,
                                url: event.url || '',
                                title: event.title || 'No Title',
                                description: event.description || '',
                                start: event.start,
                                end: event.end,
                                formatedStart: event.formatedStart,
                                formatedEnd: event.formatedEnd,
                                eventHost: event.event_host,
                                backgroundColor: event.color || '#3788d8',
                                color: '#000000',
                                extendedProps: {
                                    calendar: event.type || 'General',
                                    file: event.file || '',
                                    audience: event.audience || []
                                }

                            });
                        });
                        console.log(events);

                        let calendarEl = document.getElementById('calendar');

                        let calendar = new Calendar(calendarEl, {
                            plugins: [dayGridPlugin, interactionPlugin, listPlugin, timegridPlugin],
                            editable: true,
                            dragScroll: true,
                            dayMaxEvents: 2,
                            eventResizableFromStart: true,
                            headerToolbar: {
                                start: 'prev,next, title',
                                end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                            },

                            events: events,
                            eventClick: function (info) {
                                document.getElementById('modalEventTitle').innerText = info.event.title;
                                document.getElementById('modalEventType').innerText = info.event.extendedProps.calendar;
                                if (info.event.extendedProps.file) {
                                    document.getElementById('modalEventFile').html = info.event.extendedProps.file;
                                }
                                document.getElementById('modalEventDescription').innerText = info.event.extendedProps.description;
                                document.getElementById('modalEventStart').innerText = info.event.extendedProps.formatedStart;
                                document.getElementById('modalEventEnd').innerText = info.event.extendedProps.formatedEnd;
                                document.getElementById('modalEventHost').innerText = info.event.extendedProps.eventHost;
                                const eventUrl = document.getElementById('modalEventUrl');

                                if (info.event.url) {
                                    eventUrl.classList.remove('d-none')
                                    eventUrl.href = info.event.url;
                                } else {
                                    eventUrl.classList.add('d-none')
                                }

                                // Parse audience data if it's a string
                                let audience = info.event.extendedProps.audience;
                                if (typeof audience === 'string') {
                                    try {
                                        audience = JSON.parse(audience);
                                    } catch (e) {
                                        console.error('Error parsing audience data:', e);
                                        audience = [];
                                    }
                                }

                                // audience info
                                const audienceDiv = document.getElementById('modalEventAudience');
                                audienceDiv.innerHTML = '';
                                if (Array.isArray(audience) && audience.length > 0) {
                                    let counter = 1;
                                    audience.forEach(user => {
                                        audienceDiv.innerHTML += `<p>${counter}. ${user.name}, ${user.email}</p> `
                                        counter++;
                                    });
                                } else {
                                    audienceDiv.innerHTML = '<p>No audience data available.</p>';
                                }

                                const fileDiv = document.getElementById('modalEventFile');
                                fileDiv.innerHTML = info.event.extendedProps.file || '';

                                $('#eventDetailsModal').modal('show');
                                info.jsEvent.preventDefault();
                            }
                        });
                        calendar.render();
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            }

            // Fetch all events when the page loads
            fetchEvents();

            // Function to handle the filter checkboxes and update events
            function handleFilters() {
                let selectedTypes = [];

                // Collect the IDs of all selected checkboxes
                $('.input-filter:checked').each(function () {
                    selectedTypes.push($(this).data('id'));
                });

                // Fetch events based on selected filters
                if (selectedTypes.length === 0) {
                    fetchEvents([], 'none');
                } else {
                    fetchEvents(selectedTypes);
                }
            }

            // Event listener for individual filter checkbox changes
            $('.input-filter').change(function () {
                let checkbox = $(this);

                // Change the background color based on whether the checkbox is checked or unchecked
                if (checkbox.is(':checked')) {
                    checkbox.css('background-color', checkbox.data('color'));
                    checkbox.css('border-color', checkbox.data('color'));
                } else {
                    checkbox.css('background-color', '#ffffff');
                }

                handleFilters();
            });

            // Event listener for "View All" checkbox
            $('#selectAll').change(function () {
                if ($(this).is(':checked')) {
                    // Check all filter checkboxes and update their appearance
                    $('.input-filter').prop('checked', true).trigger('change');
                } else {
                    // Uncheck all filter checkboxes and update their appearance
                    $('.input-filter').prop('checked', false).trigger('change');
                }
            });

            // Initial trigger to set correct checkbox background colors
            $('.input-filter').each(function () {
                $(this).css('background-color', $(this).data('color'));
                $(this).css('border-color', $(this).data('color'));
            });

            // Ensure "View All" checkbox correctly reflects all filters being checked or unchecked
            $('.input-filter').change(function () {
                if ($('.input-filter:checked').length === $('.input-filter').length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
            });
        });
        $('#app-calendar-sidebar-btn').on('click', function () {
            $('#app-calendar-sidebar').toggleClass('c-left-0');
        });
    </script>

    <script src="{{ asset(mix('assets/js/app-calendar-events.js')) }}"></script>
    <script src="{{ asset(mix('assets/js/app-calendar.js')) }}"></script>

@endpush
