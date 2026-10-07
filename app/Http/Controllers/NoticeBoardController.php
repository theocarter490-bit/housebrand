<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaignEmailsJob;
use App\Jobs\SendNoticeEmailsJob;
use App\Models\EmailCampain;
use App\Models\EmailSentLog;
use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\NoticeType;
use App\Models\NoticeBoard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\NoticeCreateRequest;
use App\Http\Requests\NoticeUpdateRequest;

class NoticeBoardController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $authUserId = auth()->id();
            $data = NoticeBoard::select('*')
                ->with('createdBy', 'updatedBy', 'noticeType')
                ->byShop('created_by')
                ->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('notice_change_status')) {
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
                        '<a href="javascript:void(0);" class="dropdown-item edit-notice" data-id="' . $row->id . '"><i class="ti ti-pencil"></i> Edit</a>' .
                        '<a href="javascript:void(0);" class="dropdown-item delete-notice text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                        '<a href="' . route('notice-board.notice.assign', $row->id) . '" class="dropdown-item assign-user-email-campaign text-primary"><i class="ti ti-users"></i>Assign Receivers</a>' .
                        '<a href="' . route('notice-board.notice.email.log', $row->id) . '" class="dropdown-item text-primary""><i class="ti ti-settings"></i>Logs</a>';
                    if (is_null($row->receivers) || $row->active_status == 0) {
                        $btn .= '<span class="dropdown-item disabled"><i class="ti ti-mail"></i> Send Emails</span>';
                    } elseif ($row->is_sent == 1) {
                        $btn .= '<a href="javascript:void(0);" class="dropdown-item send-email-notice text-danger" data-id="' . $row->id . '"><i class="ti ti-mail"></i> Resend Emails</a>';
                    } else {
                        $btn .= '<a href="javascript:void(0);" class="dropdown-item send-email-notice text-danger" data-id="' . $row->id . '"><i class="ti ti-mail"></i> Send Emails</a>';
                    }


                    $btn .= '</div></div>';

                    return $btn;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
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
                ->addColumn('published_at', function ($row) {
                    return $row->published_at ? dateFormatwithTime($row->published_at) : '---';
                })
                ->addColumn('type', function ($row) {
                    return $row->noticeType ? $row->noticeType->name : 'N/A';
                })
                ->rawColumns(['status', 'action', 'created_by', 'attachment', 'is_sent'])
                ->make(true);
        }

        $types = NoticeType::byShop('user_id')->where('is_active', 1)->get();

        return view('notice-board.notice.index', compact('types'));
    }

    public function store(NoticeCreateRequest $request)
    {

        if (moduleConditionLimitCheck('notice-management', 'App\Models\NoticeBoard', 'created_by') == false) {
            return response()->json(['message' => 'You have reached the maximum quantity for this module.', 'status' => 403]);
        }

        try {
            $notice = new NoticeBoard();
            $notice->title = $request->title;
            $notice->type_id = $request->notice_type;
            $notice->active_status = 0;
            $notice->published_at = null;
            $notice->description = $request->description;
            $notice->created_by = Auth::id();
            if ($request->hasFile('attachment')) {
                $path = $this->uploadFile($request->file('attachment'), 'notices');
                $notice->attachment = $path;
            }
            $notice->save();
            return response()->json(['message' => 'Notice Created Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }

    public function edit($id)
    {
        $notice = NoticeBoard::findOrFail($id);
        $notice->attachment_element = getFileElement(getFilePath($notice->attachment));
        return response()->json([
            'id' => $notice->id,
            'title' => $notice->title,
            'attachment' => $notice->attachment_element,
            'published_at' => $notice->published_at,
            'description' => $notice->description,
            'notice_type' => $notice->type_id,
            'active_status' => $notice->active_status,
        ]);
    }

    public function update(NoticeUpdateRequest $request)
    {
        try {
            $notice = NoticeBoard::findOrFail($request->id);
            $notice->title = $request->title;
            $notice->description = $request->description;
            $notice->type_id = $request->notice_type;

            if ($request->hasFile('attachment')) {
                $path = $this->uploadFile($request->file('attachment'), 'notices');
                if ($notice->attachment) {
                    $this->deleteFile($notice->attachment);
                }
                $notice->attachment = $path;
            }
            $notice->updated_by = Auth::id();
            $notice->save();
            return response()->json(['message' => 'Notice updated successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = NoticeBoard::find($request->id);

        if ($data) {
            if (is_null($data->receivers)) {
                return response()->json([
                    'message' => 'Failed! No receivers were assigned.',
                    'status' => 400
                ]);
            }

            if ($data->active_status == 1) {
                $data->active_status = 0;
                $data->published_at = null;
            } else {
                $data->active_status = 1;
                $data->published_at = now();
            }

            $data->save();

            return response()->json([
                'message' => 'Status updated successfully',
                'status' => 200,
                'published_at' => $data->published_at ? dateFormatwithTime($data->published_at) : '---'
            ]);
        } else {
            return response()->json([
                'message' => 'Something went wrong!',
                'status' => 404
            ]);
        }
    }


    public function destroy(Request $request)
    {
        try {
            $data = NoticeBoard::find($request->id);
            if ($data) {
                $data->delete();
                return response()->json(['message' => 'Notice Deleted Successfully', 'status' => 200]);
            } else {
                return response()->json(['message' => 'Data not found!', 'status' => 404]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }


    public function assign($id)
    {
        $notice = NoticeBoard::findOrFail($id);
        $types = Role::where('active_status', 1)->byShop()->get();
        $receiversIds = json_decode($notice->receivers ?? '[]', true);
        $selectedUsers = User::whereIn('id', $receiversIds)
            ->select('id', 'name', 'email')
            ->get();
        $formattedUsers = $selectedUsers->map(function ($user) {
            return [
                'id' => $user->id,
                'label' => $user->name . ' (' . $user->email . ')',
                'email' => $user->email
            ];
        });

        return view('notice-board.assign-receiver.index', compact('notice', 'types', 'formattedUsers'));
    }

    public function emailLog(Request $request, $id)
    {
        $search = "";
        $campaign = NoticeBoard::findOrFail($id);
        $type = EmailSentLog::NOTICE;
        $logs = EmailSentLog::where('source_id', $id)->where('source', EmailSentLog::NOTICE);

        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $logs = $logs->where('email', 'like', '%' . strtoupper($search) . '%');
        }
        $search = $request->search ?? '';
        $logs = $logs->with('notice')->orderBy('id', 'desc')->paginate(10);
        return view('campaigns.email.logs', compact('logs', 'campaign', 'search', 'type'));
    }


    public function getUsersByType(Request $request)
    {
        $users = [];
        $users = User::where('role_id', $request->type)
            ->where('active_status', 1)
            ->select('id', 'name', 'email')
            ->get();

        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'label' => $user->name . ' (' . $user->email . ')',
                'email' => $user->email
            ];
        });

        return response()->json($formattedUsers);
    }


    public function updateNoticeBoardReceivers(Request $request, $id)
    {
        try {
            $notice = NoticeBoard::findOrFail($id);
            $selectedReceivers = $request->receivers ?? [];
            $notice->receivers = json_encode($selectedReceivers);
            $notice->updated_by = Auth::id();
            $notice->save();

            return response()->json(['message' => 'User Assigned Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }


    public function noticeBoard()
    {
        $notices = NoticeBoard::with('createdBy')
            ->where('active_status', 1)->byShop('created_by');
        if (auth()->user()->role_id != Role::SUPER_ADMIN) {
            $notices = $notices->where(function ($query) {
                $query->whereJsonContains('receivers', (string)auth()->id())
                    ->orWhere('created_by', auth()->id());
            });
        }
        $notices = $notices->latest()->get();

        return view('notice-board.board.index', compact('notices'));
    }

    public function sendEmail(Request $request)
    {
        try {
            $notice = NoticeBoard::findOrFail($request->id);

            if (empty($notice->receivers) || $notice->active_status != 1) {
                return response()->json([
                    'message' => "Notice email not found in system or Notice is disabled.",
                    'status' => 400
                ]);
            }

            $receiversIds = json_decode($notice->receivers ?? '[]', true);
            $userEmails = User::whereIn('id', $receiversIds)
                ->pluck('email')
                ->toArray();


            DB::beginTransaction();

            SendNoticeEmailsJob::dispatch($userEmails, [
                'subject' => $notice->title,
                'message' => $notice->description,
                'attachment' => $notice->attachment ? getFilePath($notice->attachment) : null,
                'notice_id' => $notice->id,
                'created_by' => getUserId(),
                'shopSetting' => shopSetting(),
            ],Auth::user()->id);
            $notice->is_sent = 1;
            $notice->save();
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
}
