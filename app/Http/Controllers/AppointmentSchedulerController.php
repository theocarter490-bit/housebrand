<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AppointmentSchedulerController extends Controller
{
    public function appointments()
    {
        $employees = User::where('supervisor_id', '=', getUserId())->get();
        return view('appointment-scheduler.appointment', compact('employees'));
    }

    public function storeAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eventTitle' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'eventStartDate' => 'required|date',
            'eventEndDate' => 'required|date|after_or_equal:eventStartDate',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'development' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'eventGuests' => 'nullable|integer|exists:users,id', // Assuming `employees` table
            'note' => 'nullable|string',
            'reference' => 'nullable',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Toastr::error($error, 'Error');
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput(array_merge($request->all(), ['action' => 'store']));
        }


        $appointment = new Appointment();
        $appointment->user_id = getUserId();
        $appointment->title = $request->eventTitle;
        $appointment->type = $request->type;
        $appointment->name = $request->name;
        $appointment->start_time = $request->eventStartDate;
        $appointment->end_time = $request->eventEndDate;
        $appointment->street = $request->street;
        $appointment->city = $request->city;
        $appointment->development = $request->development;
        $appointment->state = $request->state;
        $appointment->zip = $request->zip;
        $appointment->phone = $request->phone;
        $appointment->phone2 = $request->phone_2;
        $appointment->email = $request->email;
        $appointment->employee_id = $request->eventGuests;
        $appointment->note = $request->note;
        $appointment->ref_by = $request->reference;

        $address = $request->street . ', ' . $request->city . ', ' . $request->state . ', ' . $request->zip;
        $data = $this->CoordinatesFromAddress($address);
        if ($data != null) {
            $appointment->lat = $data['lat'];
            $appointment->lng = $data['lng'];
        } else {
            Toastr::warning('', '<span class="text-danger">Warning:</span> Invalid Address');
        }
        $appointment->save();
        Toastr::success('Appointment scheduled added successfully');
        return back();
    }

    function CoordinatesFromAddress($address)
    {

        $response = Http::withHeaders([
            'User-Agent' => 'Housebrands (rayhanmahmud157@gmail.com)'
        ])->timeout(60)->get('https://nominatim.openstreetmap.org/search', [
            'q' => $address,
            'format' => 'json',
        ]);

        if ($response->successful() && isset($response[0])) {
            return [
                'lat' => $response[0]['lat'],
                'lng' => $response[0]['lon'],
            ];
        }

        return null;
    }

    public function calenderEvents(Request $request)
    {

        $query = Appointment::query();
        $eventType = $request->eventTypes ?? [];
        $employee = $request->employee ?? [];

        $query->whereIn('type', $eventType)->whereIn('employee_id', $employee);


        $events = $query->where('user_id', getUserId())->get();

        $data = [];
        foreach ($events as $event) {

            $audienceIds = json_decode($event->guest_user_ids);
            $audience = [];
            if ($audienceIds != null) {
                $audience = User::select('name', 'email')->whereIn('id', $audienceIds)->get();
            }

            $data[] = [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_time,
                'end' => $event->end_time,
                'note' => @$event->note,
                'url' => '',
                'allDay' => false,
                'extendedProps' => [
                    'calendar' => $event->type,
                ],
            ];
        }

        return sendResponse('Calender List', $data);
    }

    public function editAppointment($id)
    {
        $appointment = Appointment::find($id);
        return sendResponse('Appointment', $appointment);
    }

    public function updateAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'editEventID' => 'required|integer|exists:appointments,id',
            'editEventTitle' => 'required|string|max:255',
            'editType' => 'required|string|max:100',
            'editName' => 'required|string|max:255',
            'editEventStartDate' => 'required|date',
            'editEventEndDate' => 'required|date|after_or_equal:editEventStartDate',
            'editEventStreet' => 'nullable|string|max:255',
            'editEventCity' => 'nullable|string|max:100',
            'eventDevelopment' => 'nullable|string|max:100',
            'editState' => 'nullable|string|max:100',
            'editEventZIP' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'eventGuests' => 'nullable|integer|exists:users,id',
            'note' => 'nullable|string',
            'reference' => 'nullable',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Toastr::error($error, 'Error');
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput(array_merge($request->all(), ['action' => 'update']));
        }

        $appointment = Appointment::find($request->editEventID);
        $appointment->user_id = getUserId();
        $appointment->title = $request->editEventTitle;
        $appointment->type = $request->editType;
        $appointment->name = $request->editName;
        $appointment->start_time = $request->editEventStartDate;
        $appointment->end_time = $request->editEventEndDate;
        $appointment->street = $request->editEventStreet;
        $appointment->city = $request->editEventCity;
        $appointment->development = $request->eventDevelopment;
        $appointment->state = $request->editState;
        $appointment->zip = $request->editEventZIP;
        $appointment->phone = $request->phone;
        $appointment->phone2 = $request->phone_2;
        $appointment->email = $request->email;
        $appointment->employee_id = $request->eventGuests;
        $appointment->note = $request->note;
        $appointment->ref_by = $request->reference;

        $address = $request->editEventStreet . ', ' . $request->editEventCity . ', ' . $request->editState . ', ' . $request->editEventZIP;
        $data = $this->CoordinatesFromAddress($address);
        if ($data != null) {
            $appointment->lat = $data['lat'];
            $appointment->lng = $data['lng'];
        } else {
            Toastr::warning('', '<span class="text-danger">Warning:</span> Invalid Address');
        }

        $appointment->save();
        Toastr::success('Appointment scheduled Updated successfully');
        return back();
    }

    public function dateTimeUpdate(Request $request)
    {
        $event = Appointment::find($request->id);
        $start_time = null;
        $end_time = null;
        if ($request->start_time) {
            $dateString = $request->start_time;
            $date = new \DateTime($dateString);
            $start_time = $date->format('Y-m-d H:i:s');
        }
        if ($request->end_time) {

            $dateString = $request->end_time;
            $date = new \DateTime($dateString);
            $end_time = $date->format('Y-m-d H:i:s');
        }
        if ($start_time != null) {
            $event->start_time = $start_time;
        }
        if ($end_time != null) {
            $event->end_time = $end_time;
        }

        $event->save();
        return sendResponse('Time Updated successfully', $event);
    }

    public function overview()
    {
        $unassignedJobs = Appointment::where('user_id', getUserId())->where('employee_id', null)->paginate(10);
        $mapLocations = Appointment::where('user_id', getUserId())->whereNotNull('lat')->whereNotNull('lng')->get();
        $mapLocations = $mapLocations->map(function ($location) {
            return [
                'id' => $location->id,
                'title' => $location->title,
                'address' => $location->street . ', ' . $location->city . ', ' . $location->state . ', ' . $location->zip,
                'lat' => $location->lat,
                'lng' => $location->lng,
            ];
        });
        $mapLocations = json_encode($mapLocations);
        $mapLocations = json_decode($mapLocations, true);
        $employees = User::where('supervisor_id', '=', getUserId())->get();
        return view('appointment-scheduler.appointment_overview', compact('unassignedJobs', 'mapLocations', 'employees'));
    }

    public function assignEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:users,id',
            'appointment_id' => 'required|integer|exists:appointments,id',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Toastr::error($error, 'Error');
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $appointment = Appointment::find($request->appointment_id);
        $appointment->employee_id = $request->employee_id;
        $appointment->save();
        Toastr::success('Employee assigned successfully!');
        return redirect()->route('appointment-scheduler.overview');
    }
}
