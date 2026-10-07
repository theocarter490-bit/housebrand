<?php

namespace App\Http\Controllers;

use App\Exports\EmailCampaignExport;
use App\Exports\ProductExport;
use App\Models\EmailSentLog;
use App\Models\User;
use Exception;
use App\Models\Role;
use App\Models\ContactUs;
use App\Models\Subscriber;
use App\Models\EmailCampain;
use Illuminate\Http\Request;
use App\Models\CampaignEmailLog;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendCampaignEmailsJob;
use App\Http\Traits\FileUploadTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreEmailCampaignRequest;
use App\Http\Requests\UpdateEmailCampaignRequest;

class EmailCampaignController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = EmailCampain::select('*')->with('createdBy', 'updatedBy')->byShop('created_by')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('email_campaign_status_change')) {
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
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-inline-block text-nowrap">' .
                        '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                        '<div class="dropdown-menu dropdown-menu-end m-0">' .
                        '<a href="javascript:void(0);" class="dropdown-item edit-campaign" data-id="' . $row->id . '"><i class="ti ti-pencil"></i> Edit</a>' .
                        '<a href="javascript:void(0);" class="dropdown-item delete-campaign text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                        '<a href="' . route('marketing.launch.index', $row->id) . '" class="dropdown-item assign-user-email-campaign text-primary"><i class="ti ti-settings"></i>Assign User Emails</a>' .
                        '<a href="' . route('marketing.email.log', $row->id) . '" class="dropdown-item text-primary""><i class="ti ti-settings"></i>Logs</a>';

                    if (is_null($row->emails) || $row->active_status == 0) {
                        $btn .= '<span class="dropdown-item disabled"><i class="ti ti-mail"></i> Send Emails</span>';
                    } elseif ($row->is_sent == 1) {
                        $btn .= '<a href="javascript:void(0);" class="dropdown-item send-campaign text-danger" data-id="' . $row->id . '"><i class="ti ti-mail"></i> Resend Emails</a>';
                    } else {
                        $btn .= '<a href="javascript:void(0);" class="dropdown-item send-campaign text-danger" data-id="' . $row->id . '"><i class="ti ti-mail"></i> Send Emails</a>';
                    }

                    $btn .= '</div></div>';

                    return $btn;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }

                    if ($request->get('sending_status') == '0' || $request->get('sending_status') == '1') {
                        $instance->where('is_sent', $request->get('sending_status'));
                    }
                }, true)
                ->addColumn('is_sent', function ($row) {
                    if ($row->is_sent == 1) {
                        return '<span class="badge bg-label-success">Sent</span>';
                    } else {
                        return '<span class="badge bg-label-danger">Not Yet</span>';
                    }
                })
                ->addColumn('created_by', function ($row) {
                    return dataInfo($row);
                })
                ->editColumn('attachment', function ($row) {
                    return getFileElement(getFilePath($row->attachment));
                })
                ->rawColumns(['status', 'action', 'is_sent', 'created_by', 'attachment'])
                ->make(true);
        }

        return view('campaigns.email.index');
    }


    public function store(StoreEmailCampaignRequest $request)
    {
        if (moduleConditionLimitCheck('marketing', 'App\Models\EmailCampain', 'created_by') == false) {
            return response()->json(['message' => 'You have reached the maximum quantity for this module.', 'status' => 403]);
        }
        try {

            $campaign = new EmailCampain();
            $campaign->title = $request->title;
            $campaign->message = $request->message;
            $campaign->created_by = getUserId();
            if ($request->hasFile('attachment')) {
                $path = $this->uploadFile($request->file('attachment'), 'email_attachments');
                $campaign->attachment = $path;
            }
            $campaign->save();

            return response()->json(['message' => 'Email campaign created successfully.', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong.'], 500);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = EmailCampain::find($request->id);
        if ($data) {
            if ($data->active_status == 1) {
                $data->active_status = 0;
            } else {
                $data->active_status = 1;
            }
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Something Went Wrong!', 'status' => 404]);
        }
    }

    public function edit($id)
    {
        $campaign = EmailCampain::findOrFail($id);
        $campaign->attachment_element = getFileElement(getFilePath($campaign->attachment));
        return response()->json($campaign);
    }

    public function update(UpdateEmailCampaignRequest $request)
    {

        try {

            $campaign = EmailCampain::findOrFail($request->id);
            $campaign->title = $request->title;
            $campaign->message = $request->message;
            if ($request->hasFile('attachment')) {
                $path = $this->uploadFile($request->file('attachment'), 'email_attachments');

                if ($campaign->attachment) {
                    $this->deleteFile($campaign->attachment);
                }
                $campaign->attachment = $path;
            }
            $campaign->updated_by = getUserId();
            $campaign->save();

            return response()->json(['message' => 'Campaign updated successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went Wrong', 'status' => 500]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = EmailCampain::find($request->id);
            if ($data) {
                $data->delete();
                return response()->json(['message' => 'Email campaign Deleted Successfully', 'status' => 200]);
            } else {
                return response()->json(['message' => 'Data not found!', 'status' => 404]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }

    public function launch($id)
    {
        $campaign = EmailCampain::findOrFail($id);

        $types = Role::where('active_status', 1)->byShop();

        if (Auth::user()->role_id == Role::DESIGNER) {
            $types = $types->orWhere('id', Role::CUSTOMER)->get();
        } else if (Auth::user()->role_id == Role::MANUFACTURER) {
            $types = $types->orWhere('id', Role::DESIGNER)->get();
        } else {
            $types = $types->get();
        }
        $subscribers = Subscriber::where('active_status', 1)->get();
        $contact_requests = ContactUs::where('active_status', 1)->get();

        return view('campaigns.assign-email-user.index', compact('campaign', 'types', 'subscribers', 'contact_requests'));
    }

    public function emailLog(Request $request, $id)
    {
        $search = "";
        $campaign = EmailCampain::findOrFail($id);
        $type = EmailSentLog::CAMPAIGN;
        $logs = EmailSentLog::where('source_id', $id)->where('source', EmailSentLog::CAMPAIGN);
        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $logs = $logs->where('email', 'like', '%' . strtoupper($search) . '%');
        }
        $search = $request->search ?? '';
        $logs = $logs->with('emailCampaign')->orderBy('id', 'desc')->paginate(10);
        return view('campaigns.email.logs', compact('logs', 'campaign', 'search', 'type'));
    }


    public function getUsersByType(Request $request)
    {
        $users = [];

        if ($request->type === 'subscribers') {
            $users = Subscriber::where('active_status', 1)->pluck('email')->toArray();
        } elseif ($request->type === 'contact_requests') {
            $users = ContactUs::where('active_status', 1)->pluck('email')->toArray();
        } elseif ($request->type === Role::CUSTOMER) {
            $users = User::where('active_status', 1)->isClient('designer_id')->pluck('email')->toArray();
        } else {
            $role = Role::find($request->type);
            if ($role) {
                $users = $role->users()->where('active_status', 1)->pluck('email')->toArray();
            }
        }

        return response()->json($users);
    }


    public function updateEmailcampaignEmails(Request $request, $id)
    {
        try {
            $campaign = EmailCampain::findOrFail($id);
            $selectedEmails = $request->emails ?? [];


            $campaign->emails = json_encode($selectedEmails);
            $campaign->updated_by = getUserId();
            $campaign->save();
            return response()->json(['message' => 'Email Assigned Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }


    public function sendEmail(Request $request)
    {

        $campaign = EmailCampain::findOrFail($request->id);

        if (empty($campaign->emails) || $campaign->active_status != 1) {
            return response()->json([
                'message' => 'Something Went Wrong.',
                'status' => 400
            ]);
        }

        $emails = json_decode($campaign->emails);
        DB::beginTransaction();
        try {

            SendCampaignEmailsJob::dispatch($emails, [
                'subject' => $campaign->title,
                'campaign_name' => $campaign->name,
                'message' => $campaign->message,
                'attachment' => $campaign->attachment ? getFilePath($campaign->attachment) : null,
                'campaign_id' => $campaign->id,
                'created_by' => getUserId(),
            ], Auth::user()->id);

            $campaign->update(['is_sent' => 1]);
            DB::commit();

            return response()->json([
                'message' => 'Emails queued for sending successfully',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while queuing the emails: ',
                'status' => 500
            ]);
        }
    }

    public function downloadDemoFile()
    {
        return Excel::download(new EmailCampaignExport(), 'email_campaign.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'text/xlsx',
        ]);
    }
}
