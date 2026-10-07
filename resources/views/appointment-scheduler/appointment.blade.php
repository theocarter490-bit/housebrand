@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Calender'))
@push('styles')
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/fullcalendar/fullcalendar.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/app-calendar.css')) }}">
    <style>
        .app-calendar-wrapper .app-calendar-sidebar {
            border-left: 1px solid #dbdade;
        }
    </style>
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/leaflet/leaflet.css')) }}">
@endpush
@section('content')

    <div class="card app-calendar-wrapper">
        <div class="row g-0">


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
                <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
                     aria-labelledby="addEventSidebarLabel">
                    <div class="offcanvas-header my-1">
                        <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Appointment</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                    </div>

                    <div class="offcanvas-body pt-0">
                        <form class="event-form pt-0" id="eventAddForm" method="POST"
                              action="{{ route('appointment-scheduler.storeAppointment') }}">
                            @csrf

                            {{-- Title --}}
                            <div class="mb-3">
                                <label class="form-label" for="eventTitle">Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="eventTitle" name="eventTitle"
                                       value="{{ old('eventTitle') }}" required placeholder="Title">
                                @error('eventTitle') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Type --}}
                            <div class="mb-3">
                                <label class="form-label" for="eventLabel">Type <span
                                        class="text-danger">*</span></label>
                                <select class="select2 select-event-label form-select" id="eventLabel" name="type"
                                        required>
                                    <option value="">Choose</option>
                                    @foreach(['Personal','Sales','Install','Service','Followup','Measure','virtual_estimate','Delivery'] as $type)
                                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_',' ', $type)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Name --}}
                            <div class="mb-3">
                                <label class="form-label" for="eventName">Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="eventName" name="name"
                                       value="{{ old('name') }}" placeholder="John Doe" required>
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Start Date --}}
                            <div class="mb-3">
                                <label class="form-label" for="eventStartDate">Start Date <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="eventStartDate" name="eventStartDate"
                                       value="{{ old('eventStartDate') }}" placeholder="Start Date" required>
                                @error('eventStartDate') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- End Date --}}
                            <div class="mb-3">
                                <label class="form-label" for="eventEndDate">End Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="eventEndDate" name="eventEndDate"
                                       value="{{ old('eventEndDate') }}" placeholder="End Date" required>
                                @error('eventEndDate') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Street --}}
                            <div class="mb-3">
                                <label class="form-label" for="street">Street</label>
                                <input type="text" class="form-control" id="street" name="street"
                                       value="{{ old('street') }}">
                                @error('street') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- City --}}
                            <div class="mb-3">
                                <label class="form-label" for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city"
                                       value="{{ old('city') }}">
                                @error('city') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Development --}}
                            <div class="mb-3">
                                <label class="form-label" for="development">Development</label>
                                <input type="text" class="form-control" id="development" name="development"
                                       value="{{ old('development') }}">
                                @error('development') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- State --}}
                            <div class="mb-3">
                                <label class="form-label" for="state">State <span class="text-danger">*</span></label>
                                <select class="select2 form-select" id="state" name="state" required>
                                    <option value="">Choose</option>
                                    @foreach([
                                        'Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut',
                                        'Delaware','District of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois',
                                        'Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts',
                                        'Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada',
                                        'New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota',
                                        'Ohio','Oklahoma','Oregon','Other','Pennsylvania','Rhode Island','South Carolina',
                                        'South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington',
                                        'West Virginia','Wisconsin','Wyoming'
                                    ] as $st)
                                        <option value="{{ $st }}" {{ old('state') == $st ? 'selected' : '' }}>
                                            {{ $st }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('state') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- ZIP --}}
                            <div class="mb-3">
                                <label class="form-label" for="zip">ZIP</label>
                                <input type="text" class="form-control" id="zip" name="zip"
                                       value="{{ old('zip') }}">
                                @error('zip') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="mb-3">
                                <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="{{ old('phone') }}" required>
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Phone 2 --}}
                            <div class="mb-3">
                                <label class="form-label" for="phone_2">Phone 2</label>
                                <input type="tel" class="form-control" id="phone_2" name="phone_2"
                                       value="{{ old('phone_2') }}">
                                @error('phone_2') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email') }}" required>
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Employee --}}
                            <div class="mb-3 select2-primary">
                                <label class="form-label" for="eventGuests">Employee</label>
                                <select class="select2 select-event-guests form-select" id="eventGuests"
                                        name="eventGuests">
                                    <option value="" {{ old('eventGuests') == '' ? 'selected' : '' }}>Unassigned
                                    </option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ old('eventGuests') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('eventGuests') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Note --}}
                            <div class="mb-3">
                                <label class="form-label" for="eventDescription">Note</label>
                                <textarea class="form-control" name="note"
                                          id="eventDescription">{{ old('note') }}</textarea>
                                @error('note') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Reference --}}
                            <div class="mb-3 select2-primary">
                                <label class="form-label" for="reference">Ref. By</label>
                                <select class="select2 select-event-guests form-select" id="reference" name="reference">
                                    <option value="">Choose</option>
                                    @foreach(['Angies List','Email','Flyer','Friend','Google','Home Mag','Lutron','Postcard','Repeat','Road Sign','Somfy'] as $ref)
                                        <option value="{{ $ref }}" {{ old('reference') == $ref ? 'selected' : '' }}>
                                            {{ $ref }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('reference') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Buttons --}}
                            <div class="mb-3 d-flex justify-content-sm-between justify-content-start my-4">
                                <div>
                                    <button type="submit" class="btn btn-primary btn-add-event me-sm-3 me-1">Add
                                    </button>
                                    <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                            data-bs-dismiss="offcanvas">Cancel
                                    </button>
                                </div>
                                <div>
                                    <button class="btn btn-label-danger btn-delete-event d-none">Delete</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="editEventSidebar"
                     aria-labelledby="editEventSidebarLabel">
                    <div class="offcanvas-header my-1">
                        <h5 class="offcanvas-title" id="addEventSidebarLabel">Edit Appointment</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body pt-0">
                        <form class="event-form pt-0" id="eventAddForm" method="POST"
                              action="{{route('appointment-scheduler.updateAppointment')}}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="eventTitle">Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('editEventTitle') is-invalid @enderror"
                                       id="editEventTitle" name="editEventTitle"
                                       value="{{ old('editEventTitle') }}"
                                       placeholder="Event Title"/>
                                @error('editEventTitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <input type="text" class="form-control d-none" id="editEventID" name="editEventID"
                                       value="{{ old('editEventID') }}"
                                       placeholder="Event Title" required/>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventLabel">Type <span
                                        class="text-danger">*</span></label>
                                <select class="select2 select-event-label form-select @error('editType') is-invalid @enderror"
                                        id="EditEventLabel"
                                        name="editType" required>
                                    <option data-label="Personal" value="Personal" {{ old('editType') == 'Personal' ? 'selected' : '' }}>Personal</option>
                                    <option data-label="Sales" value="Sales" {{ old('editType') == 'Sales' ? 'selected' : '' }}>Sales</option>
                                    <option data-label="Install" value="Install" {{ old('editType') == 'Install' ? 'selected' : '' }}>Install</option>
                                    <option data-label="Service" value="Service" {{ old('editType') == 'Service' ? 'selected' : '' }}>Service</option>
                                    <option data-label="Followup" value="Followup" {{ old('editType') == 'Followup' ? 'selected' : '' }}>Followup</option>
                                    <option data-label="Measure" value="Measure" {{ old('editType') == 'Measure' ? 'selected' : '' }}>Measure</option>
                                    <option data-label="virtual_estimate" value="virtual_estimate" {{ old('editType') == 'virtual_estimate' ? 'selected' : '' }}>Virtual Estimate</option>
                                    <option data-label="Delivery" value="Delivery" {{ old('editType') == 'Delivery' ? 'selected' : '' }}>Delivery</option>
                                </select>
                                @error('editType')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventname">Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('editName') is-invalid @enderror"
                                       id="editEventname" name="editName"
                                       value="{{ old('editName') }}"
                                       placeholder="John Doe" required/>
                                @error('editName')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventStartDate">Start Date <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('editEventStartDate') is-invalid @enderror"
                                       id="editEventStartDate"
                                       name="editEventStartDate"
                                       value="{{ old('editEventStartDate') }}"
                                       placeholder="Start Date" required/>
                                @error('editEventStartDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventEndDate">End Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('editEventEndDate') is-invalid @enderror"
                                       id="editEventEndDate" name="editEventEndDate"
                                       value="{{ old('editEventEndDate') }}"
                                       placeholder="End Date" required/>
                                @error('editEventEndDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventTitle">Street </label>
                                <input type="text" class="form-control @error('editEventStreet') is-invalid @enderror"
                                       id="editEventStreet" name="editEventStreet"
                                       value="{{ old('editEventStreet') }}"
                                       placeholder=""/>
                                @error('editEventStreet')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventTitle">City</label>
                                <input type="text" class="form-control @error('editEventCity') is-invalid @enderror"
                                       id="editEventCity" name="editEventCity"
                                       value="{{ old('editEventCity') }}"
                                       placeholder=""/>
                                @error('editEventCity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventTitle">Development</label>
                                <input type="text" class="form-control @error('eventDevelopment') is-invalid @enderror"
                                       id="eventDevelopment" name="eventDevelopment"
                                       value="{{ old('eventDevelopment') }}"
                                       placeholder=""/>
                                @error('eventDevelopment')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="editState">State <span
                                        class="text-danger">*</span></label>
                                <select class="select2 select-event-label form-select @error('editState') is-invalid @enderror"
                                        id="editState" name="editState"
                                        required>
                                    <option>Choose</option>
                                    <option value='Alabama' {{ old('editState') == 'Alabama' ? 'selected' : '' }}>Alabama</option>
                                    <option value='Alaska' {{ old('editState') == 'Alaska' ? 'selected' : '' }}>Alaska</option>
                                    <option value='Arizona' {{ old('editState') == 'Arizona' ? 'selected' : '' }}>Arizona</option>
                                    <option value='Arkansas' {{ old('editState') == 'Arkansas' ? 'selected' : '' }}>Arkansas</option>
                                    <option value='California' {{ old('editState') == 'California' ? 'selected' : '' }}>California</option>
                                    <option value='Colorado' {{ old('editState') == 'Colorado' ? 'selected' : '' }}>Colorado</option>
                                    <option value='Connecticut' {{ old('editState') == 'Connecticut' ? 'selected' : '' }}>Connecticut</option>
                                    <option value='Delaware' {{ old('editState') == 'Delaware' ? 'selected' : '' }}>Delaware</option>
                                    <option value='District of Columbia' {{ old('editState') == 'District of Columbia' ? 'selected' : '' }}>District of Columbia</option>
                                    <option value='Florida' {{ old('editState', 'Florida') == 'Florida' ? 'selected' : '' }}>Florida</option>
                                    <option value='Georgia' {{ old('editState') == 'Georgia' ? 'selected' : '' }}>Georgia</option>
                                    <option value='Hawaii' {{ old('editState') == 'Hawaii' ? 'selected' : '' }}>Hawaii</option>
                                    <option value='Idaho' {{ old('editState') == 'Idaho' ? 'selected' : '' }}>Idaho</option>
                                    <option value='Illinois' {{ old('editState') == 'Illinois' ? 'selected' : '' }}>Illinois</option>
                                    <option value='Indiana' {{ old('editState') == 'Indiana' ? 'selected' : '' }}>Indiana</option>
                                    <option value='Iowa' {{ old('editState') == 'Iowa' ? 'selected' : '' }}>Iowa</option>
                                    <option value='Kansas' {{ old('editState') == 'Kansas' ? 'selected' : '' }}>Kansas</option>
                                    <option value='Kentucky' {{ old('editState') == 'Kentucky' ? 'selected' : '' }}>Kentucky</option>
                                    <option value='Louisiana' {{ old('editState') == 'Louisiana' ? 'selected' : '' }}>Louisiana</option>
                                    <option value='Maine' {{ old('editState') == 'Maine' ? 'selected' : '' }}>Maine</option>
                                    <option value='Maryland' {{ old('editState') == 'Maryland' ? 'selected' : '' }}>Maryland</option>
                                    <option value='Massachusetts' {{ old('editState') == 'Massachusetts' ? 'selected' : '' }}>Massachusetts</option>
                                    <option value='Michigan' {{ old('editState') == 'Michigan' ? 'selected' : '' }}>Michigan</option>
                                    <option value='Minnesota' {{ old('editState') == 'Minnesota' ? 'selected' : '' }}>Minnesota</option>
                                    <option value='Mississippi' {{ old('editState') == 'Mississippi' ? 'selected' : '' }}>Mississippi</option>
                                    <option value='Missouri' {{ old('editState') == 'Missouri' ? 'selected' : '' }}>Missouri</option>
                                    <option value='Montana' {{ old('editState') == 'Montana' ? 'selected' : '' }}>Montana</option>
                                    <option value='Nebraska' {{ old('editState') == 'Nebraska' ? 'selected' : '' }}>Nebraska</option>
                                    <option value='Nevada' {{ old('editState') == 'Nevada' ? 'selected' : '' }}>Nevada</option>
                                    <option value='New Hampshire' {{ old('editState') == 'New Hampshire' ? 'selected' : '' }}>New Hampshire</option>
                                    <option value='New Jersey' {{ old('editState') == 'New Jersey' ? 'selected' : '' }}>New Jersey</option>
                                    <option value='New Mexico' {{ old('editState') == 'New Mexico' ? 'selected' : '' }}>New Mexico</option>
                                    <option value='New York' {{ old('editState') == 'New York' ? 'selected' : '' }}>New York</option>
                                    <option value='North Carolina' {{ old('editState') == 'North Carolina' ? 'selected' : '' }}>North Carolina</option>
                                    <option value='North Dakota' {{ old('editState') == 'North Dakota' ? 'selected' : '' }}>North Dakota</option>
                                    <option value='Ohio' {{ old('editState') == 'Ohio' ? 'selected' : '' }}>Ohio</option>
                                    <option value='Oklahoma' {{ old('editState') == 'Oklahoma' ? 'selected' : '' }}>Oklahoma</option>
                                    <option value='Oregon' {{ old('editState') == 'Oregon' ? 'selected' : '' }}>Oregon</option>
                                    <option value='Other' {{ old('editState') == 'Other' ? 'selected' : '' }}>Other</option>
                                    <option value='Pennsylvania' {{ old('editState') == 'Pennsylvania' ? 'selected' : '' }}>Pennsylvania</option>
                                    <option value='Rhode Island' {{ old('editState') == 'Rhode Island' ? 'selected' : '' }}>Rhode Island</option>
                                    <option value='South Carolina' {{ old('editState') == 'South Carolina' ? 'selected' : '' }}>South Carolina</option>
                                    <option value='South Dakota' {{ old('editState') == 'South Dakota' ? 'selected' : '' }}>South Dakota</option>
                                    <option value='Tennessee' {{ old('editState') == 'Tennessee' ? 'selected' : '' }}>Tennessee</option>
                                    <option value='Texas' {{ old('editState') == 'Texas' ? 'selected' : '' }}>Texas</option>
                                    <option value='Utah' {{ old('editState') == 'Utah' ? 'selected' : '' }}>Utah</option>
                                    <option value='Vermont' {{ old('editState') == 'Vermont' ? 'selected' : '' }}>Vermont</option>
                                    <option value='Virginia' {{ old('editState') == 'Virginia' ? 'selected' : '' }}>Virginia</option>
                                    <option value='Washington' {{ old('editState') == 'Washington' ? 'selected' : '' }}>Washington</option>
                                    <option value='West Virginia' {{ old('editState') == 'West Virginia' ? 'selected' : '' }}>West Virginia</option>
                                    <option value='Wisconsin' {{ old('editState') == 'Wisconsin' ? 'selected' : '' }}>Wisconsin</option>
                                    <option value='Wyoming' {{ old('editState') == 'Wyoming' ? 'selected' : '' }}>Wyoming</option>
                                </select>
                                @error('editState')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventURL">ZIP</label>
                                <input type="text" class="form-control @error('editEventZIP') is-invalid @enderror"
                                       id="editEventZIP" name="editEventZIP"
                                       value="{{ old('editEventZIP') }}"
                                       placeholder=""/>
                                @error('editEventZIP')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventPhone">Phone <span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="eventPhone" name="phone"
                                       value="{{ old('phone') }}"
                                       placeholder="" required/>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventPhone2">Phone 2</label>
                                <input type="tel" class="form-control @error('phone_2') is-invalid @enderror"
                                       id="eventPhone2" name="phone_2"
                                       value="{{ old('phone_2') }}"
                                       placeholder=""/>
                                @error('phone_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventEmail">Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="eventEmail" name="email"
                                       value="{{ old('email') }}"
                                       placeholder="" required/>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 select2-primary">
                                <label class="form-label" for="eventGuests">Employee</label>
                                <select class="select2 select-event-guests form-select @error('eventGuests') is-invalid @enderror"
                                        id="editEventGuests"
                                        name="eventGuests">
                                    <option data-avatar="" value="" {{ old('eventGuests') == '' ? 'selected' : '' }}>Unassigned</option>
                                    @foreach($employees as $employee)
                                        <option data-avatar="{{getFilePath($employee->image)}}"
                                                value="{{$employee->id}}" {{ old('eventGuests') == $employee->id ? 'selected' : '' }}>{{$employee->name}}</option>
                                    @endforeach
                                </select>
                                @error('eventGuests')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="eventDescription">Note</label>
                                <textarea class="form-control @error('note') is-invalid @enderror"
                                          name="note" id="EditeventDescription">{{ old('note') }}</textarea>
                                @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 select2-primary">
                                <label class="form-label" for="eventGuests">Ref. By</label>
                                <select class="select2 select-event-guests form-select @error('reference') is-invalid @enderror"
                                        id="editReference"
                                        name="reference">
                                    <option value='' {{ old('reference') == '' ? 'selected' : '' }}>Choose</option>
                                    <option value='Angies List' {{ old('reference') == 'Angies List' ? 'selected' : '' }}>Angies List</option>
                                    <option value='Email' {{ old('reference') == 'Email' ? 'selected' : '' }}>Email</option>
                                    <option value='Flyer' {{ old('reference') == 'Flyer' ? 'selected' : '' }}>Flyer</option>
                                    <option value='Friend' {{ old('reference') == 'Friend' ? 'selected' : '' }}>Friend</option>
                                    <option value='Google' {{ old('reference') == 'Google' ? 'selected' : '' }}>Google</option>
                                    <option value='Home Mag' {{ old('reference') == 'Home Mag' ? 'selected' : '' }}>Home Mag</option>
                                    <option value='Lutron' {{ old('reference') == 'Lutron' ? 'selected' : '' }}>Lutron</option>
                                    <option value='Postcard' {{ old('reference') == 'Postcard' ? 'selected' : '' }}>Postcard</option>
                                    <option value='Repeat' {{ old('reference') == 'Repeat' ? 'selected' : '' }}>Repeat</option>
                                    <option value='Road Sign' {{ old('reference') == 'Road Sign' ? 'selected' : '' }}>Road Sign</option>
                                    <option value='Somfy' {{ old('reference') == 'Somfy' ? 'selected' : '' }}>Somfy</option>
                                </select>
                                @error('reference')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 d-flex justify-content-sm-between justify-content-start my-4">
                                <div>
                                    <button type="submit" class="btn btn-primary btn-add-event me-sm-3 me-1">Update
                                    </button>
                                    <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                            data-bs-dismiss="offcanvas">Cancel
                                    </button>
                                </div>
                                <div>
                                    <button class="btn btn-label-danger btn-delete-event d-none">Delete</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <!-- /Calendar & Modal -->

            <!-- Calendar Sidebar -->
            <div class="col app-calendar-sidebar" id="app-calendar-sidebar">
                <div class="border-bottom p-4 my-sm-0 mb-3">
                    <div class="d-grid">
                        @if(hasPermission('appointment_create'))
                            <button class="btn btn-primary btn-toggle-sidebar" data-bs-toggle="offcanvas"
                                    data-bs-target="#addEventSidebar" id="addEventSidebarButton"
                                    aria-controls="addEventSidebar">
                                <i class="ti ti-plus me-1"></i>
                                <span class="align-middle">Add Appointment</span>
                            </button>
                        @endif
                        @if(hasPermission('appointment_overview'))
                            <a class="btn btn-primary btn-toggle-sidebar mt-2"
                               href="{{route('appointment-scheduler.overview')}}">
                                <i class="ti ti-arrow-badge-left me-1"></i>
                                <span class="align-middle">Overview</span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="p-3">

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
                        <div class="form-check form-check-danger mb-2">
                            <input class="form-check-input input-filter input-filter-checkbox" type="checkbox"
                                   id="select-personal"
                                   data-value="Personal" checked>
                            <label class="form-check-label" for="select-personal">Personal</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input input-filter input-filter-checkbox" type="checkbox"
                                   id="select-business"
                                   data-value="Sales" checked>
                            <label class="form-check-label" for="select-business">Sales</label>
                        </div>
                        <div class="form-check form-check-warning mb-2">
                            <input class="form-check-input input-filter input-filter-checkbox" type="checkbox"
                                   id="select-family"
                                   data-value="Install" checked>
                            <label class="form-check-label" for="select-family">Install</label>
                        </div>
                        <div class="form-check form-check-success mb-2">
                            <input class="form-check-input input-filter input-filter-checkbox" type="checkbox"
                                   id="select-holiday"
                                   data-value="Service" checked>
                            <label class="form-check-label" for="select-holiday">Service</label>
                        </div>
                        <div class="form-check form-check-info">
                            <input class="form-check-input input-filter input-filter-checkbox" type="checkbox"
                                   id="select-etc"
                                   data-value="Followup" checked>
                            <label class="form-check-label" for="select-etc">Followup</label>
                        </div>
                        <div class="form-check form-check-dark">
                            <input class="form-check-input input-filter input-filter-checkbox" type="checkbox"
                                   id="select-etc"
                                   data-value="Measure" checked>
                            <label class="form-check-label" for="select-etc">Measure</label>
                        </div>
                        <div class="form-check form-check-secondary">
                            <input class="form-check-input input-filter input-filter-checkbox" type="checkbox"
                                   id="select-etc"
                                   data-value="virtual_estimate" checked>
                            <label class="form-check-label" for="select-etc">Visual Estimate</label>
                        </div>
                        <div class="form-check form-check-danger">
                            <input class="form-check-input input-filter input-filter-checkbox" type="checkbox"
                                   id="select-etc"
                                   data-value="Delivery" checked>
                            <label class="form-check-label" for="select-etc">Delivery</label>
                        </div>
                    </div>

                    <hr class="container-m-nx mb-4 mt-3">

                    <!-- Filter -->
                    <div class="mb-3 ms-3">
                        <small class="text-small text-muted text-uppercase align-middle">Employee Filter</small>
                    </div>

                    <div class="form-check mb-2 ms-3">
                        <input class="form-check-input select-all" type="checkbox" id="selectAllEmployee"
                               data-value="all"
                               checked>
                        <label class="form-check-label" for="selectAllEmployee">View All</label>
                    </div>

                    <div class="app-calendar-events-filter ms-3">

                        @foreach($employees as $employee)
                            <div class="form-check form-check-danger mb-2">
                                <input class="form-check-input input-filter-employee input-filter-checkbox"
                                       type="checkbox"
                                       id="select-personal-employee"
                                       data-value="{{$employee->id}}" checked>
                                <label class="form-check-label"
                                       for="select-personal-employee">{{$employee->name}}</label>
                            </div>
                        @endforeach

                    </div>

                </div>
            </div>
            <!-- /Calendar Sidebar -->
        </div>

        @endsection

        @push('scripts')
            <script src="{{ asset(mix('assets/vendor/libs/fullcalendar/fullcalendar.js')) }}"></script>
            <script src="{{ asset(mix('assets/vendor/libs/flatpickr/flatpickr.js')) }}"></script>



            <script>

                $(document).ready(function () {
                    @if ($errors->any() && old('action') === 'store')
                    $('#addEventSidebarButton').click();
                    @endif
                });

                $(document).ready(function () {
                    @if ($errors->any() && old('action') === 'update')
                    var myOffcanvas = new bootstrap.Offcanvas(document.getElementById('editEventSidebar'))
                    myOffcanvas.show()
                    if (editEventStartDate) {
                        var start = editEventStartDate.flatpickr({
                            enableTime: true,
                            altFormat: 'Y-m-dTH:i:S',
                            onReady: function (selectedDates, dateStr, instance) {
                                if (instance.isMobile) {
                                    instance.mobileInput.setAttribute('step', null);
                                }
                            }
                        });
                    }

                    // Event end (flatpicker)
                    if (editEventEndDate) {
                        var end = editEventEndDate.flatpickr({
                            enableTime: true,
                            altFormat: 'Y-m-dTH:i:S',
                            onReady: function (selectedDates, dateStr, instance) {
                                if (instance.isMobile) {
                                    instance.mobileInput.setAttribute('step', null);
                                }
                            }
                        });
                    }
                    @endif
                });
                $(document).ready(function () {

                    let appointmentCalendarsColor = {
                        Personal: 'danger',
                        Sales: 'primary',
                        Install: 'warning',
                        Service: 'success',
                        Followup: 'info',
                        Measure: 'dark',
                        virtual_estimate: 'secondary',
                        Delivery: 'danger',
                    };

                    // Function to fetch events based on selected types
                    function fetchEvents(types = [], employee = []) {

                        $.ajax({
                            url: '/appointment-scheduler/calender-events',
                            type: 'GET',
                            data: {
                                eventTypes: types,
                                employee: employee,
                            },
                            success: function (response) {
                                let allEvents = response.data;
                                let events = [];

                                allEvents.forEach(event => {
                                    events.push({
                                        id: event.id,
                                        title: event.title || 'No Title',
                                        description: event.note || '',

                                        start: event.start,
                                        end: event.end,
                                        extendedProps: {
                                            calendar: event.extendedProps.calendar || 'General',
                                        }
                                    });
                                });

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
                                    eventClassNames: function ({event: calendarEvent}) {
                                        const colorName = appointmentCalendarsColor[calendarEvent._def.extendedProps.calendar];
                                        // Background Color
                                        return ['fc-event-' + colorName];
                                    },
                                    dateClick: function (info) {

                                        var myOffcanvas = new bootstrap.Offcanvas(document.getElementById('addEventSidebar'))
                                        myOffcanvas.show();


                                        $('#eventStartDate').val(formatDate(new Date(info.dateStr)));
                                        $('#eventEndDate').val(formatDate(new Date(info.dateStr), 30));
                                    },
                                    eventClick: function (info) {
                                        var myOffcanvas = new bootstrap.Offcanvas(document.getElementById('editEventSidebar'))
                                        myOffcanvas.show()


                                        $.ajax({
                                            url: '/appointment-scheduler/appointments/edit/' + info.event.id,
                                            type: 'GET',
                                            data: {eventTypes: types},
                                            success: function (response) {

                                                $('#editEventID').val(response.data.id);
                                                $('#editEventTitle').val(response.data.title);
                                                $('#editEventname').val(response.data.name);
                                                $('#editEventStartDate').val(response.data.start_time);
                                                $('#editEventEndDate').val(response.data.end_time);
                                                $('#editEventStreet').val(response.data.street);
                                                $('#editEventCity').val(response.data.city);
                                                $('#eventDevelopment').val(response.data.development);
                                                $('#editEventZIP').val(response.data.zip);
                                                $('#eventPhone').val(response.data.phone);
                                                $('#eventPhone2').val(response.data.phone2);
                                                $('#eventEmail').val(response.data.email);
                                                $('#EditeventDescription').val(response.data.note);


                                                $('#EditEventLabel').find('option[value="' + response.data.type +
                                                    '"]').attr("selected", "selected");
                                                $('#editState').find('option[value="' + response.data.state +
                                                    '"]').attr("selected", "selected");
                                                $('#editEventGuests').find('option[value="' + response.data.employee_id +
                                                    '"]').attr("selected", "selected");
                                                $('#editReference').find('option[value="' + response.data.ref_by +
                                                    '"]').attr("selected", "selected");

                                                $('#EditEventLabel').trigger('change.select2');
                                                $('#editState').trigger('change.select2');
                                                $('#editEventGuests').trigger('change.select2');
                                                $('#editReference').trigger('change.select2');


                                                if (editEventStartDate) {
                                                    var start = editEventStartDate.flatpickr({
                                                        enableTime: true,
                                                        altFormat: 'Y-m-dTH:i:S',
                                                        onReady: function (selectedDates, dateStr, instance) {
                                                            if (instance.isMobile) {
                                                                instance.mobileInput.setAttribute('step', null);
                                                            }
                                                        }
                                                    });
                                                }

                                                // Event end (flatpicker)
                                                if (editEventEndDate) {
                                                    var end = editEventEndDate.flatpickr({
                                                        enableTime: true,
                                                        altFormat: 'Y-m-dTH:i:S',
                                                        onReady: function (selectedDates, dateStr, instance) {
                                                            if (instance.isMobile) {
                                                                instance.mobileInput.setAttribute('step', null);
                                                            }
                                                        }
                                                    });
                                                }

                                            },
                                            error: function (error) {
                                            }

                                        });


                                    },

                                    eventDrop: function (info) {

                                        updateEventAfterDrag(info.event);
                                    },
                                    eventResize: function (info) {
                                        updateEventAfterDrag(info.event);
                                    }

                                });
                                calendar.render();
                            },
                            error: function (error) {
                                toastr.error(error.responseJSON.message);
                            }
                        });
                    }

                    function formatDate(date, addMinutes = 0) {

                        const adjustedDate = new Date(date);
                        adjustedDate.setMinutes(adjustedDate.getMinutes() + addMinutes);

                        const year = adjustedDate.getFullYear();
                        const month = String(adjustedDate.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                        const day = String(adjustedDate.getDate()).padStart(2, '0');
                        const hours = String(adjustedDate.getHours()).padStart(2, '0');
                        const minutes = String(adjustedDate.getMinutes()).padStart(2, '0');
                        const seconds = String(adjustedDate.getSeconds()).padStart(2, '0');

                        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    }


                    function updateEventAfterDrag(event) {

                        const formData = new FormData();
                        formData.append('id', event.id);
                        formData.append('_token', "{{ csrf_token() }}");
                        formData.append('start_time', event.start ? formatDate(new Date(event.start)) : '');
                        formData.append('end_time', event.end ? formatDate(new Date(event.end)) : '');


                        $.ajax({
                            url: '{{route('appointment-scheduler.dateTimeUpdate')}}',
                            type: 'POST',
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (response) {

                            },
                            error: function (e) {

                            }
                        });
                    }

                    // Fetch all events when the page loads
                    handleFilters();

                    // Function to handle the filter checkboxes and update events
                    function handleFilters() {
                        let selectedTypes = [];
                        let selectedEmployee = [];

                        // Collect the IDs of all selected checkboxes
                        $('.input-filter:checked').each(function () {
                            selectedTypes.push($(this).data('value'));
                        });
                        // Collect the IDs of all selected Employee
                        $('.input-filter-employee:checked').each(function () {
                            selectedEmployee.push($(this).data('value'));
                        });

                        fetchEvents(selectedTypes, selectedEmployee);

                    }

                    // Event listener for individual filter checkbox changes
                    $(".input-filter-checkbox").change(function () {
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
                        handleFilters();
                    });


                    // Ensure "View All" checkbox correctly reflects all filters being checked or unchecked
                    $('.input-filter').change(function () {
                        if ($('.input-filter:checked').length === $('.input-filter').length) {
                            $('#selectAll').prop('checked', true);
                        } else {
                            $('#selectAll').prop('checked', false);
                        }
                    });
                    // Event listener for "Employee View All" checkbox
                    $('#selectAllEmployee').change(function () {
                        if ($(this).is(':checked')) {
                            // Check all filter checkboxes and update their appearance
                            $('.input-filter-employee').prop('checked', true).trigger('change');
                        } else {
                            // Uncheck all filter checkboxes and update their appearance
                            $('.input-filter-employee').prop('checked', false).trigger('change');
                        }
                    });


                    // Ensure "View All" checkbox correctly reflects all filters being checked or unchecked
                    $('.input-filter-employee').change(function () {
                        if ($('.input-filter-employee:checked').length === $('.input-filter-employee').length) {
                            $('#selectAllEmployee').prop('checked', true);
                        } else {
                            $('#selectAllEmployee').prop('checked', false);
                        }
                    });
                });

                $('#app-calendar-sidebar-btn').on('click', function () {
                    $('#app-calendar-sidebar').toggleClass('c-left-0');
                });
                let editEventStartDate = document.querySelector('#editEventStartDate'),
                    editEventEndDate = document.querySelector('#editEventEndDate');

            </script>

            <script src="{{ asset(mix('assets/js/app-calendar-events.js')) }}"></script>
            <script src="{{ asset(mix('assets/js/app-calendar.js')) }}"></script>
    @endpush
