<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableDesignerListResource;
use App\Http\Resources\CustomerAssignedDesignerResource;
use App\Http\Resources\UserResource;
use App\Models\CustomerAssignedDesignerRequest;
use App\Models\DesignerCustomerAssignment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerAssignedDesignerController extends Controller
{
    public function list()
    {
        $designerList = DesignerCustomerAssignment::where('customer_id', Auth::user()->id)->with('designer.shop')->whereNot('designer_id', Auth::user()->designer_id)->get();
        return sendResponse('Assigned Designer List', CustomerAssignedDesignerResource::collection($designerList));
    }

    public function sendJoinRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designer_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        $customerAssignedDesigner = CustomerAssignedDesignerRequest::where('customer_id', Auth::user()->id)->where('new_designer_id', $request->designer_id)->first();

        if ($customerAssignedDesigner) {
            if ($customerAssignedDesigner->status == CustomerAssignedDesignerRequest::APPROVED) {
                return sendError('Designer has already been assigned');
            }
            if ($customerAssignedDesigner->status == CustomerAssignedDesignerRequest::DECLINED) {
                return sendError('Request Declined: You do not have permission to add this designer. Please contact an Administrator to proceed.');
            }
            if ($customerAssignedDesigner->status == CustomerAssignedDesignerRequest::WAITING_FOR_APPROVAL) {
                return sendError('A request for approval has already been sent and is currently pending review.');
            }
        }

        $customerAssignedDesigner = new CustomerAssignedDesignerRequest();
        $customerAssignedDesigner->new_designer_id = $request->designer_id;
        $customerAssignedDesigner->current_designer_id = Auth::user()->designer_id;
        $customerAssignedDesigner->customer_id = Auth::user()->id;
        $customerAssignedDesigner->customer_note = $request->customer_note;
        $customerAssignedDesigner->save();

        return sendResponse('Request sent successfully. Wait for administrator approval');

    }

    public function getAvailableDesignerList()
    {
        $designers = User::where('role_id', Role::DESIGNER)
            ->where('active_status', 1)
            ->checkSubscription()
            ->with('shop', 'designerJoinRequest')
            ->doesntHave('assignedDesigners');
        if (request()->has('no_pagination') && request()->get('no_pagination') == 1) {
            $designers = $designers->get(); // Get all records
        } else {
            $designers = $designers->paginate(perPage());
        }
        return sendResponse('Designer list.', AvailableDesignerListResource::collection($designers)->resource);
    }

    public function setDefaultDesigner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designer_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }
        $customerAssignedDesigner = DesignerCustomerAssignment::where('customer_id', Auth::user()->id)
            ->where('designer_id', $request->designer_id)
            ->first();

        if (!$customerAssignedDesigner) {
            return sendError('Designer not assigned');
        }

        $user = Auth::user();
        $user->designer_id = $request->designer_id;
        $user->save();
        return sendResponse('Designer set successfully');
    }

    public function leaveDesigner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designer_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }
        $customerAssignedDesigner = DesignerCustomerAssignment::where('customer_id', Auth::user()->id)
            ->where('designer_id', $request->designer_id)
            ->first();

        if (!$customerAssignedDesigner) {
            return sendError('Designer not assigned');
        }
        if ($customerAssignedDesigner->designer_id == Auth::user()->designer_id) {
            return sendError("This designer is marked as your default. Assign another designer as default to proceed with removal.");
        }

        $customerAssignedDesigner->delete();
        return sendResponse('Designer removed successfully');
    }

    public function deleteRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required',
        ]);
        $list = CustomerAssignedDesignerRequest::find($request->request_id);
        if ($list->status == CustomerAssignedDesignerRequest::APPROVED) {
            return sendError('Request already approved. You can not delete this request.');
        }
        $list->delete();
        return sendResponse('Request deleted successfully');
    }
}
