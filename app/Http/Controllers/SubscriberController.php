<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Exports\SubscriberExport;
use App\Facades\SendMail;
use App\Http\Requests\MailRequest;
use App\Mail\SubscriberReply;
use App\Models\Subscriber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Subscriber::query();
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
                    if (hasPermission('subscribers_status_change')) {
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
                ->addColumn('action', function ($row) {

                    $btn = '';
                    if (hasPermission('subscribers_reply') || hasPermission('subscriber_delete')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }


                    if (hasPermission('subscribers_reply')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item reply_button" data-bs-toggle="modal" data-bs-target="#replyModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Reply</a>';

                    }
                    if (hasPermission('subscriber_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'description', 'status', 'replied'])
                ->make(true);
        }
        return view('subscriber.index');
    }

    public function get($id)
    {
        $data = Subscriber::find($id);
        if ($data) {
            return response()->json(['data' => $data, 'status' => 200], 200);
        }
        return response()->json(['message' => "Subscriber not found!"], 404);
    }

    public function changeStatus(Request $request)
    {
        $data = Subscriber::find($request->id);
        if ($data) {
            if ($data->active_status == 1) {
                $data->active_status = 0;
            } else {
                $data->active_status = 1;
            }
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } else {
            return response()->json(['message' => "Subscriber Not Found!"], 404);
        }
    }

    public function sendReply(MailRequest $request)
    {
        $data = [
            'subject' => $request->subject,
            'message' => $request->message,
            'username' => $request->user_name
        ];

        try {
            SendMail::sender(Auth::user()->id)->to($request->to_email)->send(new SubscriberReply($data));
            $subscriber = Subscriber::where('email', $request->to_email)->first();
            if ($subscriber) {
                $subscriber->is_replied = 1;
                $subscriber->save();
            }
            return response()->json(['message' => 'Reply Send Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $subscriber = Subscriber::find($request->id);
            $subscriber->delete();
            return response()->json(['text' => 'Subscriber has been deleted Successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function exportEmails(Request $request)
    {
        $subscribers = Subscriber::query();

        if (in_array($request->get('status'), ['0', '1'])) {
            $subscribers->where('active_status', $request->get('status'));
        }

        if (in_array($request->get('reply'), ['0', '1'])) {
            $subscribers->where('is_replied', $request->get('reply'));
        }

        $subscribers = $subscribers->get();

        return Excel::download(
            new SubscriberExport($subscribers),
            'subscriber_email_list.xlsx',
            \Maatwebsite\Excel\Excel::XLSX,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

}
