<?php

namespace App\Http\Controllers;

use App\Facades\SendMail;
use App\Http\Requests\MailRequest;
use App\Mail\ContactReply;
use App\Models\ContactUs;
use App\Models\DesignerContact;
use App\Models\EmailSentLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class ContactRequestController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ContactUs::with('user')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('replied', function ($row) {
                    if ($row->is_replied == 1) {
                        return '<span class="badge bg-label-success">Yes</span>';
                    } else {
                        return '<span class="badge bg-label-danger">No</span>';
                    }
                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('contact_request_status_change')) {
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
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }

                    if ($request->get('reply') == '0' || $request->get('reply') == '1') {
                        $instance->where('is_replied', $request->get('reply'));
                    }
                }, true)
                ->addColumn('user', function ($row) {
                    if ($row->user) {
                        return @$row->user->name;
                    }
                    return '-------';
                })
                ->addColumn('action', function ($row) {

                    $btn = '';
                    if (hasPermission('contact_request_reply') || hasPermission('contact_request_delete')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('contact_request_reply')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item reply_button" data-bs-toggle="modal" data-bs-target="#replyModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Reply</a>';
                        $btn .= '<a href="' . route('contact-request.emailLog', $row->id) . '" class="dropdown-item text-primary"><i class="ti ti-settings"></i>Logs</a>';
                    }
                    if (hasPermission('contact_request_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'description', 'status', 'replied'])
                ->make(true);
        }
        return view('contact-request.index');
    }

    public function changeStatus(Request $request)
    {
        $data = ContactUs::find($request->id);
        if ($data) {
            if ($data->active_status == 1) {
                $data->active_status = 0;
            } else {
                $data->active_status = 1;
            }
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } else {
            return response()->json(['message' => "Data Not Found!"], 404);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = ContactUs::find($request->id);
            if ($data) {
                $data->delete();
                return response()->json(['message' => 'Contact Request Deleted Successfully', 'status' => 200], 200);
            } else {
                return response()->json(['message' => "Data not found!"], 404);
            }
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function getContactRequest($id)
    {
        $data = ContactUs::find($id);
        if ($data) {
            return response()->json(['data' => $data, 'status' => 200], 200);
        }
        return response()->json(['message' => "Data not found!"], 404);
    }


    public function sendReply(MailRequest $request)
    {
        $contact = ContactUs::where('id', $request->contact_us_id)->first();
        $data = [
            'subject' => $request->subject,
            'message' => $request->message,
            'username' => $contact->name
        ];

        try {

            SendMail::sender(Auth::user()->id)->to($request->to_email)->send(new ContactReply($data));
            if ($contact) {
                $contact->is_replied = 1;
                $contact->reply_count = $contact->reply_count + 1;
                $contact->save();
            }

            EmailSentLog::create([
                'source_id' => $contact->id,
                'source' => EmailSentLog::CONTACT_US,
                'email' => $request->to_email,
                'subject' => $request->subject,
                'text' => $request->message,
                'sent_at' => now(),
                'status' => 1,
                'created_by' => getUserId(),
                'updated_by' => getUserId(),
            ]);

            return response()->json(['message' => 'Reply Send Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function emailLog(Request $request, $id)
    {
        $campaign = ContactUs::findOrFail($id);
        $logs = EmailSentLog::where('source_id', $id)->where('source', EmailSentLog::CONTACT_US)->orderBy('id', 'desc')->paginate(10);
        return view('designer-contact.logs', compact('logs', 'campaign'));
    }

}
