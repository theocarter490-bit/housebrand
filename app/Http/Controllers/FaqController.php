<?php

namespace App\Http\Controllers;

use App\Http\Requests\FaqStoreRequest;
use App\Http\Requests\FaqUpdateRequest;
use App\Models\Faq;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{
    public function index(Request $request){

        if ($request->ajax()) {
            $data = Faq::with('user');
            if (getUserId() != Role::SUPER_ADMIN) {
                $data->where('user_id', getUserId());
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('faqs_status_change')) {
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

                ->addColumn('user',function ($row)
                {
                    return dataInfo($row);
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('filter_status') == '0' || $request->get('filter_status') == '1') {
                        $instance->where('active_status', $request->get('filter_status'));
                    }
                }, true)

                ->addColumn('action', function ($row) {

                    $btn = '';
                    if (hasPermission('faqs_update') || hasPermission('faqs_delete')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('faqs_update')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_edit_button" data-bs-toggle="modal" data-bs-target="#editFaqModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>';
                    }
                    if (hasPermission('faqs_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'description', 'status', 'user'])
                ->make(true);
        }
        return view('frontend-cms.faq.index');
    }

    public function store(FaqStoreRequest $request){
        try {
            $faq                = new Faq();
            $faq->title         = $request->title;
            $faq->description   = $request->description;
            $faq->active_status = $request->status;
            $faq->user_id       = getUserId();
            $faq->created_by    = getUserId();

            $faq->save();
            return response()->json(['message' => 'Faq Created Successfully', 'status' => 200], 200);
        }catch (Exception $e){
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function edit($id){
        $faq = Faq::find($id);
        if ($faq){
            return response()->json(['data' => $faq, 'status' => 200], 200);
        }
        return response()->json(['message' => "Faq not found"], 404);
    }

    public function update(FaqUpdateRequest $request){
        try {
            $faq                = Faq::find($request->id);
            $faq->title         = $request->title;
            $faq->description   = $request->description;
            $faq->active_status = $request->status;
            $faq->updated_by    = getUserId();
            $faq->update();
            return response()->json(['message' => 'Faq Update Successfully', 'status' => 200], 200);
        }catch (Exception $e){
            return response()->json(['message' => "Something went wrong"], 500);
        }

    }

    public function destroy(Request $request){
        try {
            $faq = Faq::find($request->id);
            $faq->delete();
            return response()->json(['message' => 'Faq Deleted Successfully', 'status' => 200], 200);
        }catch (Exception $e){
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function changeStatus(Request $request){
        try {
            $faq = Faq::find($request->id);
            $faq->active_status = !$faq->active_status;
            $faq->update();
            return response()->json(['message' => 'Status Change Successfully', 'status' => 200], 200);
        }catch (Exception $e){
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

}
