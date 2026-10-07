<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventStoreRequest;
use App\Http\Traits\FileUploadTrait;
use App\Jobs\SendEventReminderMail;
use App\Models\ContactUs;
use App\Models\EmailSentLog;
use App\Models\Event;
use App\Models\EventType;
use App\Models\NoticeBoard;
use App\Models\Role;
use App\Models\Subscriber;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{

    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Event::with('eventType')->where('user_id', getUserId());

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('event_type', function ($row) {
                    return $row->eventType->name;
                })
                ->editColumn('start_date', function ($row) {
                    return dateFormatwithTime($row->start_date);
                })
                ->editColumn('end_date', function ($row) {
                    return dateFormatwithTime($row->end_date);
                })
                ->editColumn('event_url', function ($row) {
                    return '<a href="' . $row->event_url . '" target="_blank">' . $row->event_url . '</a>';
                })
                ->editColumn('location', function ($row) {
                    return $row->location;
                })
                ->editColumn('file', function ($row) {
                    return getFileElement(getFilePath($row->file));
                })
                ->editColumn('description', function ($row) {
                    return $row->description;
                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('event_change_status')) {
                        $isChecked = $row->active_status == 1 ? 'checked' : '';
                        $statusHtml .= '
                        <label class="switch switch-success" style="margin-bottom: 5px;">
                            <input type="checkbox" class="switch-input changeStatus" data-id="' . $row->id . '" ' . $isChecked . ' />
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="ti ti-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="ti ti-x"></i>
                                </span>
                            </span>
                        </label>
                    ';
                    }

                    // Status badge, displayed below the toggle switch if it’s shown
                    $statusHtml .= '<div><span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span></div>';

                    $statusHtml .= '</div>'; // Closing the main container

                    return $statusHtml;
                })
                ->editColumn('email_notify', function ($row) {
                    return $row->email_notify == 1 ? '<span class="badge custom-bg-success">Yes</span>' : '<span class="badge custom-bg-danger">No</span>';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }

                    if ($request->get('type') != '') {
                        $instance->where('event_type_id', $request->get('type'));
                    }

                    if ($request->get('start_date') != '') {
                        $startDate = $request->get('start_date');
                        $instance->where('start_date', '>=', $startDate);
                    }
                    if ($request->get('end_date') != '') {
                        $endDate = $request->get('end_date');
                        $instance->where('start_date', '<=', $endDate);
                    }


                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';
                    if (hasPermission('event_update') || hasPermission('event_delete')) {
                        $btn .= '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('event_update')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item type_edit_button" data-bs-toggle="modal" data-bs-target="#editEventModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>';
                    }

                    if (hasPermission('assign_user_read')) {
                        $btn .= '<a href="' . route('event-management.event.assign-user', $row->id) . '" class="dropdown-item text-primary"><i class="ti ti-user-cog"></i>Assign Audience</a>';
                    }

                    $btn .= '<a href="' . route('event-management.event.email.log', $row->id) . '" class="dropdown-item text-primary""><i class="ti ti-settings"></i>Logs</a>';

                    if (hasPermission('event_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item type_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'event_url', 'file', 'email_notify'])
                ->make(true);
        }

        $eventTypes = EventType::where('active_status', 1)
            ->where('user_id', getUserId())->get();

        return view('event-management.event.index', compact('eventTypes'));
    }

    public function store(EventStoreRequest $request)
    {
        if (moduleConditionLimitCheck('event-management', 'App\Models\Event') == false) {
            Toastr::error('You have reached the maximum quantity for this module.');
            return redirect()->back();
        }
        try {
            $event = new Event();
            $event->name = $request->name;
            $event->event_type_id = $request->event_type;
            $event->user_id = getUserId();
            $this->createOrUpdateEvent($request, $event);

            if ($request->hasFile('file')) {
                $event->file = $this->uploadFile($request->file('file'), 'event');;
            }
            $event->save();
            Toastr::success("Event Created Successfully");
        } catch (\Exception $e) {
            Toastr::error("Something Went Wrong");
        }
        return back();
    }

    public function edit($id)
    {
        try {
            $data = Event::where('id', $id)
                ->where('user_id', getUserId())->first();
            if ($data->file) {
                $data->file = getFileElement(getFilePath($data->file));
            }
            return response()->json(['data' => $data, 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Something Went Wrong!", 'status' => 500]);
        }
    }

    public function update(EventStoreRequest $request)
    {
        try {
            $data = Event::where('id', $request->event_id)
                ->where('user_id', getUserId())->first();

            $data->name = $request->name;
            $data->event_type_id = $request->event_type;
            $this->createOrUpdateEvent($request, $data);

            if ($request->hasFile('file')) {
                $this->deleteFile($data->file);
                $data->file = $this->uploadFile($request->file('file'), 'event');;
            }
            $data->save();
            Toastr::success('Event Updated Successfully');
        } catch (\Exception $e) {
            Toastr::error('Something went wrong!');
        }
        return back();
    }

    public function delete(Request $request)
    {
        try {
            $data = Event::where('id', $request->id)->where('user_id', getUserId())->first();
            $data->delete();
            return response()->json(['message' => 'Event Deleted Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong!', 'status' => 500]);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $data = Event::where('id', $request->id)->where('user_id', getUserId())->first();
            $data->active_status = !$data->active_status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong!', 'status' => 500]);
        }
    }

    public function assignUser($event_id)
    {
        try {
            // find event
            $event = Event::where([['id', $event_id], ['user_id', getUserId()]])->first();
            if ($event) {
                $types = Role::where('active_status', 1)->byShop()->get();

                $users = [];
                if ($event->guest_user_ids) {
                    $guest_user_ids = json_decode($event->guest_user_ids);
                    $users = User::whereIn('id', $guest_user_ids)->get();
                }

                if ($event->file) {
                    $event->file = getFileElement(getFilePath($event->file));
                }

                return view('event-management.assign-user.index', compact('event', 'types', 'users'));
            }
        } catch (\Exception $e) {
            Toastr::error("Something Went Wrong!");
        }
        return back();
    }

    public function emailLog(Request $request, $id)
    {
        $search = "";
        $campaign = Event::findOrFail($id);
        $type = EmailSentLog::EVENT;
        $logs = EmailSentLog::where('source_id', $id)->where('source', EmailSentLog::EVENT);
        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $logs = $logs->where('email', 'like', '%' . strtoupper($search) . '%');
        }
        $search = $request->search ?? '';
        $logs = $logs->with('event')->orderBy('id', 'desc')->paginate(10);
        return view('campaigns.email.logs', compact('logs', 'campaign', 'search', 'type'));
    }


    public function getUsersByType(Request $request)
    {
        try {
            $type = $request->input('type');
            $users = [];

            $role = Role::find($type);
            if ($role) {
                $users = $role->users()->select('name', 'email', 'id')->get()->toArray();
            }

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(["message" => "Something Went Wrong!", 'status' => 500]);
        }
    }

    public function updateEventWithUser(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);
            $selectedUserIds = $request->input('users', []);

            $event->guest_user_ids = json_encode($selectedUserIds);
            $event->save();

            if ($event->email_notify && !empty($selectedUserIds)) {
                $users = User::whereIn('id', $selectedUserIds)->get();
                $sendTime = Carbon::parse($event->start_date)->subMinutes(30);
                $now = now();
                $sender = Auth::user();
                foreach ($users as $user) {
                    if ($sendTime->greaterThan($now)) {
                        SendEventReminderMail::dispatch($user, $event, $sender->id)->delay($sendTime);
                    } else {
                        SendEventReminderMail::dispatch($user, $event, $sender->id);
                    }
                }
            }
            return response()->json(['message' => 'Event Updated Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Something Went Wrong!", 'status' => 500]);
        }
    }


    public function calender()
    {
        $eventTypes = EventType::where('active_status', 1)->where('user_id', getUserId())->get();
        return view('event-management.calender', compact('eventTypes'));
    }

    // calenderEvents
    public function calenderEvents(Request $request)
    {

        if ($request->query('type') == 'none') {
            return response()->json(['data' => []]);
        }

        $types = $request->query('types');
        $typeArray = $types ? array_map('intval', explode(',', $types)) : [];

        $query = Event::with('eventType', 'user')->where('active_status', 1)
            ->whereHas('eventType', function ($q) {
                $q->where('active_status', 1);
            });

        $query = $query->where(function ($q) {
            $q->where('user_id', auth()->id())
                ->orWhereJsonContains('guest_user_ids', (string)auth()->id());
        });

        if (!empty($typeArray)) {
            $query->whereIn('event_type_id', $typeArray);
        }
        $events = $query->get();

        $data = [];
        foreach ($events as $event) {

            $audienceIds = json_decode($event->guest_user_ids);
            $audience = [];
            if ($audienceIds != null) {
                $audience = User::select('name', 'email')->whereIn('id', $audienceIds)->get();
            }

            $data[] = [
                'id' => $event->id,
                'event_host' => $event->user->name,
                'title' => $event->name,
                'type' => $event->eventType->name,
                'color' => $event->eventType->color,
                'start' => $event->start_date,
                'end' => $event->end_date,
                'formatedStart' => dateFormatwithTime($event->start_date),
                'formatedEnd' => dateFormatwithTime($event->end_date),
                'url' => $event->event_url,
                'file' => getFileElement(getFilePath($event->file)),
                'location' => $event->location,
                'description' => $event->description,
                'audience' => json_encode($audience)
            ];
        }

        return sendResponse('Event List', $data);
    }

    private function createOrUpdateEvent(EventStoreRequest $request, $data): void
    {
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
        $data->event_url = $request->event_url;
        $data->location = $request->location;
        $data->description = $request->description;
        $data->active_status = $request->active_status;

        if ($request->email_notify) {
            $data->email_notify = 1;
        } else {
            $data->email_notify = 0;
        }
    }

    public function getEventDetails($id)
    {
        try {
            $data = Event::where('id', $id)->with('eventType')->first();
            $data->file = getFilePath($data->file);

            return response()->json(['data' => $data, 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Something Went Wrong!", 'status' => 500]);
        }
    }

}
