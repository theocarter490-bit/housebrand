<?php

namespace App\Http\Controllers;

use App\Models\CustomerAssignedDesignerRequest;
use App\Models\DesignerCustomerAssignment;
use App\Models\DesignerSharedProduct;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerAssignedDesignerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = CustomerAssignedDesignerRequest::with(['customer', 'currentDesigner', 'newDesigner']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer_details', function ($row) {
                    $customer = optional($row->customer);
                    $avatarUrl = getFilePath($customer->avatar);

                    $name = $customer->name ?? 'Guest User';
                    $phone = $customer->phone ?? 'No Phone';
                    $email = $customer->email ?? 'No Email';

                    return '
                        <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 12px; border: 1px solid #edf2f9;">
                            <div class="position-relative">
                                <div style="width: 58px; height: 58px; padding: 3px; background: linear-gradient(45deg, #7367f0, #ce9ffc); border-radius: 50%;">
                                    <img src="' . $avatarUrl . '"
                                         alt="' . $name . '"
                                         class="rounded-circle border border-2 border-white shadow-sm"
                                         style="width: 100%; height: 100%; object-fit: cover;" />
                                </div>
                                <span class="position-absolute border border-2 border-white rounded-pill bg-success"
                                      style="bottom: 4px; right: 4px; width: 14px; height: 14px;"
                                      title="Online"></span>
                            </div>

                            <div class="ms-3 flex-grow-1 position-relative">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.95rem;">' . $name . '</h6>
                                    <a href="' . route('live-chat.index', ['directChatUser' => $customer->id]) . '"
                                       class="position-absolute top-0 end-0 mt-2 me-2 text-primary hover-scale" style="top: -30px !important; right: -20px !important;"  title="Chat with Customer">
                                        <i class="ti ti-brand-hipchat fs-3"></i>
                                    </a>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    <small class="text-muted d-flex align-items-center">
                                        <i class="ti ti-mail me-1" style="font-size: 12px;"></i>
                                        <span class="text-truncate">' . $email . '</span>
                                    </small>
                                    <small class="text-secondary d-flex align-items-center fw-medium">
                                        <i class="ti ti-device-mobile me-1" style="font-size: 12px;"></i>
                                        ' . $phone . '
                                    </small>
                                </div>
                            </div>
                        </div>';
                })
                ->addColumn('current_designer_details', function ($row) {
                    $shop = optional($row->currentDesigner->shop);
                    $logoUrl = getFilePath($shop->logo);

                    $shopName = $shop->shop_name ?? 'Not Assigned';
                    $phone = $shop->phone ?? '---';
                    $email = $shop->email ?? '---';
                    $shopRoute = $shop->slug ? env('APP_FRONTEND_URL') . '/designer/' . $shop->slug : '#';

                    return '
                        <div class="position-relative d-flex align-items-center p-2" style="background: rgba(115, 103, 240, 0.05); border-radius: 12px; border: 1px solid rgba(115, 103, 240, 0.15);">
                            <!-- Chat Icon Top Right -->
                            <a href="' . route('live-chat.index', ['directChatUser' => $row->current_designer_id]) . '"
                               class="position-absolute top-0 end-0 mt-2 me-2 text-primary hover-scale" style="top: -22px !important; right: -12px !important;" title="Chat with Shop">
                                <i class="ti ti-brand-hipchat fs-3"></i>
                            </a>

                            <a href="' . $shopRoute . '" class="avatar avatar-lg me-3 flex-shrink-0" target="_blank">
                                <img src="' . $logoUrl . '" alt="Logo" class="rounded shadow-sm border border-white" style="width: 54px; height: 54px; object-fit: contain; background: white;">
                            </a>

                            <div class="flex-grow-1 overflow-hidden text-start">
                                <div class="mb-1">
                                    <a href="' . $shopRoute . '" class="badge bg-label-primary fw-bold text-truncate d-inline-block hover-opacity" style="max-width: 140px; font-size: 0.85rem; text-decoration: none;" target="_blank">
                                        ' . $shopName . '
                                    </a>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem; line-height: 1.2;">
                                    <div class="mb-1 d-flex align-items-center">
                                        <i class="ti ti-phone-calling me-1 text-primary" style="font-size: 11px;"></i> ' . $phone . '
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-mail-forward me-1 text-primary" style="font-size: 11px;"></i> ' . $email . '
                                    </div>
                                </div>
                            </div>
                        </div>';
                })
                ->addColumn('new_designer_details', function ($row) {
                    $shop = optional($row->newDesigner->shop);
                    $logoUrl = getFilePath($shop->logo);

                    $shopName = $shop->shop_name ?? 'New Applicant';
                    $phone = $shop->phone ?? '---';
                    $email = $shop->email ?? '---';
                    $shopRoute = $shop->slug ? env('APP_FRONTEND_URL') . '/designer/' . $shop->slug : '#';

                    return '
                        <div class="position-relative d-flex align-items-center p-2" style="background: rgba(255, 159, 67, 0.05); border-radius: 12px; border: 1px dashed rgba(255, 159, 67, 0.4);">
                            <!-- Chat Icon Top Right -->
                            <a href="' . route('live-chat.index', ['directChatUser' => $row->new_designer_id]) . '"  target="_blank"
                               class="position-absolute end-0 mt-2 me-2 text-warning hover-scale" style="top: -22px !important; right: -12px !important;" title="Chat with Applicant">
                                <i class="ti ti-brand-hipchat fs-3"></i>
                            </a>

                            <a href="' . $shopRoute . '" class="avatar avatar-lg me-3 flex-shrink-0">
                                <img src="' . $logoUrl . '" alt="Logo" target="_blank" class="rounded shadow-sm border border-white" style="width: 54px; height: 54px; object-fit: contain; background: white;">
                            </a>

                            <div class="flex-grow-1 overflow-hidden text-start">
                                <div class="mb-1">
                                    <a href="' . $shopRoute . '" class="badge bg-label-warning fw-bold text-truncate d-inline-block hover-opacity" style="max-width: 140px; font-size: 0.85rem; text-decoration: none;">
                                        ' . $shopName . '
                                    </a>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem; line-height: 1.2;">
                                    <div class="mb-1 d-flex align-items-center">
                                        <i class="ti ti-phone-calling me-1 text-warning" style="font-size: 11px;"></i> ' . $phone . '
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-mail-forward me-1 text-warning" style="font-size: 11px;"></i> ' . $email . '
                                    </div>
                                </div>
                            </div>
                        </div>';
                })
                ->editColumn('customer_note', function ($row) {
                    if ($row->customer_note) {
                        return $row->customer_note;
                    }
                    return 'N / A';
                })
                ->editColumn('admin_note', function ($row) {
                    if ($row->admin_note) {
                        return $row->admin_note;
                    }
                    return 'N / A';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1' || $request->get('status') == '2') {
                        $instance->where('status', $request->get('status'));
                    }
                }, true)
                ->addColumn('status', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-2">';

                    if ($row->getRawOriginal('status') == CustomerAssignedDesignerRequest::WAITING_FOR_APPROVAL) {
                        $btn .= '<span class="badge bg-label-primary p-2 c-h-32 d-flex align-items-center justify-content-center"
                    title="Waiting for Administrator Approval">
                    Pending
                 </span>';
                    }

                    if ($row->getRawOriginal('status') == CustomerAssignedDesignerRequest::APPROVED) {
                        $btn .= '<span class="badge bg-label-success p-2 c-h-32 d-flex align-items-center justify-content-center"
                    title="Approved by Administrator">
                    Approved
                 </span>';
                    }

                    if ($row->getRawOriginal('status') == CustomerAssignedDesignerRequest::DECLINED) {
                        $btn .= '<span class="badge bg-label-danger p-2 c-h-32 d-flex align-items-center justify-content-center"
                    title="Cancelled by Administrator">
                    Declined
                 </span>';
                    }
                    if ($row->getRawOriginal('status') == CustomerAssignedDesignerRequest::CANCELED) {
                        $btn .= '<span class="badge bg-label-warning p-2 c-h-32 d-flex align-items-center justify-content-center"
                    title="Cancelled by Administrator">
                    Canceled
                 </span>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-2">';

                    if ($row->getRawOriginal('status') == CustomerAssignedDesignerRequest::WAITING_FOR_APPROVAL) {

                        $btn .= '<a href="#"
                    class="btn btn-success btn-sm text-white customer_assign_approve_button c-h-32 d-flex align-items-center justify-content-center"
                    data-id="' . $row->id . '">
                    Approve
                 </a>';

                        $btn .= '<a href="#"
                    class="btn btn-danger btn-sm text-white customer_assign_cancel_button c-h-32 d-flex align-items-center justify-content-center"
                    data-id="' . $row->id . '">
                    Decline
                 </a>';
                    }

                    if ($row->getRawOriginal('status') === CustomerAssignedDesignerRequest::APPROVED) {

                        $btn .= '<a href="#"
                    class="btn btn-danger btn-sm text-white customer_assign_cancel_button c-h-32 d-flex align-items-center justify-content-center"
                    data-id="' . $row->id . '">
                    Cancel
                 </a>';
                    }

                    if ($row->getRawOriginal('status') === CustomerAssignedDesignerRequest::DECLINED || $row->getRawOriginal('status') === CustomerAssignedDesignerRequest::CANCELED) {

                        $btn .= '<a href="#"
                    class="btn btn-success btn-sm text-white customer_assign_approve_button c-h-32 d-flex align-items-center justify-content-center"
                    data-id="' . $row->id . '">
                    Approve
                 </a>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['action', 'customer_details', 'current_designer_details', 'new_designer_details', 'status', 'action'])
                ->make(true);
        }
        return view('customer-assigned-designer.index');
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:customer_assigned_designer_requests,id',
            'action' => 'required|in:approve,cancel',
            'note' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request) {
            $assignReq = CustomerAssignedDesignerRequest::findOrFail($request->request_id);
            $isApprove = $request->action === 'approve';
            $statusText = '';

            if ($isApprove) {
                $assignReq->status = CustomerAssignedDesignerRequest::APPROVED;
                $assignReq->admin_note = $request->note;
                $statusText = 'approved';

                DesignerCustomerAssignment::updateOrCreate([
                    'customer_id' => $assignReq->customer_id,
                    'designer_id' => $assignReq->new_designer_id,
                ]);
            } else {
                $customer = User::find($assignReq->customer_id);

                // Check if default designer
                if ($assignReq->new_designer_id == $customer->designer_id) {
                    return response()->json([
                        'text' => "This designer is default for the customer and cannot be canceled.",
                        'status' => false
                    ], 422);
                }

                $assignReq->admin_note = $request->note;

                $designerAssignment = DesignerCustomerAssignment::where('customer_id', $assignReq->customer_id)
                    ->where('designer_id', $assignReq->new_designer_id)
                    ->first();

                if (!$designerAssignment) {
                    // No current assignment exists, so this is a "Decline"
                    $assignReq->status = CustomerAssignedDesignerRequest::DECLINED;
                    $statusText = 'declined';
                } else {
                    // Assignment existed, so we are "Canceling" it
                    $designerAssignment->delete();
                    $assignReq->status = CustomerAssignedDesignerRequest::CANCELED;
                    $statusText = 'canceled';
                }
            }

            $assignReq->save();

            return response()->json([
                'text' => "Request " . $statusText . " successfully.",
                'icon' => 'success',
                'status' => true
            ]);
        });
    }

}
