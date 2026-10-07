@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Appointments Overview'))

@section('content')

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Appointment Overview'), [
            '#' => _trans('keyword.Appointment'),
            'Appointment ' => _trans('keyword.Appointment Overview'),
        ]) !!}

        <div class="app-ecommerce-category">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Unassigned Jobs</h3>
                </div>
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                        <tr>
                            <th scope="col">SL.</th>
                            <th scope="col">Title</th>
                            <th scope="col">Type</th>
                            <th scope="col">Name</th>
                            <th scope="col">Date Time</th>
                            <th scope="col">Location</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($unassignedJobs as $key => $job)
                            <tr>
                                <th>{{ $unassignedJobs->firstItem() + $key }}</th>
                                <td>{{ $job->title }}</td>
                                <td>
                                    <span class="badge custom-bg-success">{{ $job->type }}</span>
                                </td>
                                <td>{{ $job->name }}</td>
                                <td>
                                    <p>Start: {{ dateFormatwithTime($job->start_time) }}</p>
                                    <p>End: {{ dateFormatwithTime($job->end_time) }}</p>
                                </td>
                                <td> {{ $job->street . ' ' . $job->city . ' ' . $job->state . ' ' . $job->zip }} </td>
                                <td>
                                    <a href="javascript();"
                                       class="btn btn-primary d-flex align-items-center gap-2 px-2 py-2"
                                       data-bs-toggle="modal" data-bs-target="#addItem"
                                       onclick="assignEmployee({{ $job->id }},'{{ $job->title }}','{{ $job->type }}')"><i
                                            class="ti ti-users-plus"></i> {{_trans('keyword.Assign')}}</a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center">No Data Available</td>
                            </tr>
                        @endforelse
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $unassignedJobs->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Locations</h3>
                </div>
                <div class="card-body">
                    <div>
                        <div id="layerControl" style="height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- @dd($mapLocations) --}}

        <div class="modal fade" id="assignEmployeeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3 class="mb-2">{{ _trans('keyword.Assign Employee') }} for (<strong
                                    id="job_title"></strong>)
                            </h3>

                            <span id="job_type" class="badge custom-bg-success"></span>
                        </div>
                        <form class="row g-3" action="{{ route('appointment-scheduler.assignEmployee') }}"
                              method="POST">
                            @csrf
                            <input type="hidden" name="appointment_id" id="appointment_id">
                            <div class="col-12">
                                <label class="form-label"
                                       for="employee_id">{{ _trans('keyword.Select Employee') }}</label>
                                <select name="employee_id" class="select2 form-select"
                                        data-allow-clear="true">
                                    <option value="">{{ _trans('keyword.Select Employee') }}</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit"
                                        class="btn btn-primary me-sm-3 me-1">{{ _trans('keyword.Submit') }}
                                    <span class="loader"></span>
                                </button>
                                <button id="modalClose" type="reset" class="btn btn-label-secondary"
                                        data-bs-dismiss="modal" aria-label="Close">
                                    {{ _trans('keyword.Clear') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endsection

        @push('scripts')
            <script>
                const mapLocations = @json($mapLocations);
            </script>
            <script src="{{ asset(mix('assets/vendor/libs/leaflet/leaflet.js')) }}"></script>
            <script>

                function assignEmployee(id, title, type) {
                    $('#assignEmployeeModal').modal('show');
                    $('#appointment_id').val(id);
                    $('#job_title').text(title);
                    $('#job_type').text(type);
                }

                const myTimeout = setTimeout(function () {
                    const layerControlVar = document.getElementById('layerControl');
                    if (layerControlVar) {

                        // Define base tile layers
                        const street = L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
                                maxZoom: 18
                            }),
                            watercolor = L.tileLayer('http://tile.stamen.com/watercolor/{z}/{x}/{y}.jpg', {
                                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
                                maxZoom: 18
                            });

                        // Initialize the map
                        const defaultLatLng = mapLocations.length > 0
                            ? [parseFloat(mapLocations[0].lat), parseFloat(mapLocations[0].lng)]
                            : [39.73, -104.99];

                        const map = L.map('layerControl', {
                            center: defaultLatLng,
                            zoom: 12,
                            layers: [street]
                        });

                        // Add dynamic markers
                        const markers = mapLocations.map(loc => {
                            return L.marker([parseFloat(loc.lat), parseFloat(loc.lng)])
                                .bindPopup(loc.address)
                                .bindTooltip(loc.address, {permanent: true, direction: 'top', offset: [0, -10]});
                        });

                        const markerGroup = L.layerGroup(markers).addTo(map);

                        const groupBounds = L.featureGroup(markers).getBounds();
                        map.fitBounds(groupBounds);

                        // Base and overlay map layers
                        const baseMaps = {
                            Street: street,
                            Watercolor: watercolor
                        };

                        const overlayMaps = {
                            Locations: markerGroup
                        };

                        // Add layer control to map
                        L.control.layers(baseMaps, overlayMaps).addTo(map);
                    }
                }, 1000);
            </script>

    @endpush
