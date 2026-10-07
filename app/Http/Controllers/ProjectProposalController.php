<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Project;
use App\Models\IdeaBoard;
use App\Models\ShopSetting;
use App\Models\TimeBilling;
use App\Models\ProposalItem;

use Illuminate\Http\Request;
use App\Models\ProjectService;
use App\Models\ProjectProposal;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectProposalInvoice;
use App\Models\ProjectProposalInvoiceItem;
use App\Http\Requests\ProposalStoreRequest;
use App\Http\Requests\ProposalUpdateRequest;

class ProjectProposalController extends Controller
{
    //

    public function index(Request $request, $projectID)
    {
        if ($request->ajax()) {
            $data = ProjectProposal::select('*')
                ->where('project_id', $projectID);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', function ($row) {
                    $status = '';
                    if ($row->is_seen == 0) {
                        $status = '<span class="badge bg-label-danger">new</span>';
                    }
                    return "$row->code $status";
                })
                ->editColumn('active_status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)


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

                    // Status badge, displayed below the toggle switch if it’s shown
                    $statusHtml .= '<div><span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span></div>';

                    $statusHtml .= '</div>'; // Closing the main container

                    return $statusHtml;
                })
                ->editColumn('is_published', function ($row) {
                    $statusLabel = $row->is_published == 1 ? 'Published' : 'Unpublished';
                    $statusBadgeClass = $row->is_published == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)


                    $isChecked = $row->is_published == 1 ? 'checked' : '';
                    $statusHtml .= '
                        <label class="switch switch-success" style="margin-bottom: 5px;">
                            <input type="checkbox" class="switch-input changePublishStatus" data-id="' . $row->id . '" ' . $isChecked . ' />
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

                    // Status badge, displayed below the toggle switch if it’s shown
                    $statusHtml .= '<div><span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span></div>';

                    $statusHtml .= '</div>'; // Closing the main container

                    return $statusHtml;
                })
                ->addColumn('due_date', function ($row) {
                    return dateFormat($row->due_date);
                })
                ->addColumn('proposal_date', function ($row) {
                    return dateFormat($row->proposal_date);
                })
                ->addColumn('total_price', function ($row) {
                    return getPriceFormat($row->total_amount);
                })
                ->editColumn('is_approved', function ($row) {
                    $statusLabel = $row->is_approved == 1 ? 'Approved' : 'Pending';
                    $statusBadgeClass = $row->is_approved == 1 ? 'bg-label-success' : 'bg-label-warning';
                    return "<span class='badge {$statusBadgeClass}'>{$statusLabel}</span>";
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';


                    $btn = '<div class="d-inline-block text-nowrap">' .
                        '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                        '<div class="dropdown-menu dropdown-menu-end m-0">';
                    $btn .= '<a href="' . route('project-management.project.proposal.details', [$row->project_id, $row->id]) . '" class="dropdown-item"><i class="ti ti-list-details"></i> ' . _trans('keyword.Details') . '</a>';
                    if ($row->is_approved == 1 && $row->is_invoice_generated == 0) {
                        $btn .= '<a href="' . route('project-management.project.proposal.generateInvoice', [$row->project_id, $row->id]) . '" class="dropdown-item"><i class=" tf-icons ti ti-file-dollar"></i> ' . _trans('keyword.Generate Invoice') . '</a>';
                    }
                    if ($row->is_approved == 0) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item brand_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBrandEditModal" data-id="' . $row->id . '"><i class="ti ti-edit"></i> ' . _trans('keyword.Edit') . '</a>';
                        $btn .= '<a href="javascript:0;" class="dropdown-item proposal_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['active_status', 'is_published', 'action', 'is_approved', 'code'])
                ->make(true);
        }
        $project = Project::find($projectID);

        return view('project-management.project.projects.proposal.index', compact('project'));
    }

    public function store(ProposalStoreRequest $request, $projectID)
    {

        try {
            $proposal = new ProjectProposal();

            $proposal->title = $request->name;
            $proposal->due_date = $request->date;
            $proposal->proposal_date = Carbon::today();
            $proposal->active_status = $request->status == '1' ? 1 : 0;
            $proposal->is_published = $request->publish == '1' ? 1 : 0;
            $proposal->code = 'PL-' . time();
            $proposal->user_id = getUserId();
            $proposal->project_id = $projectID;

            $proposal->save();

            return response()->json(['message' => 'Proposal Created Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        $data = ProjectProposal::findOrFail($id);
        return response()->json($data);
    }

    public function update(ProposalUpdateRequest $request)
    {

        try {
            $proposal = ProjectProposal::findOrFail($request->id);
            $proposal->title = $request->name;
            $proposal->due_date = $request->date;
            $proposal->active_status = $request->status == '1' ? 1 : 0;
            $proposal->is_published = $request->publish == '1' ? 1 : 0;
            $proposal->save();
            Toastr::success('Proposal Updated Successfully');
        } catch (Exception $e) {
            Toastr::error('Something went wrong!');
        }
        return back();
    }

    public function changeStatus(Request $request)
    {
        try {
            $data = ProjectProposal::findOrFail($request->id);
            $data->active_status = !$data->active_status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function changePublishStatus(Request $request)
    {
        try {
            $data = ProjectProposal::findOrFail($request->id);
            $data->is_published = !$data->is_published;
            $data->save();
            return response()->json(['message' => 'Published Status Updated Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function details($projectID, $proposal_id)
    {
        $products = Product::where('is_published', 1)->isClient()->get();
        $services = ProjectService::where('active_status', 1)->where('user_id', getUserId())->get();
        $manufactureProducts = Product::where('is_published', 1)
            ->where(function ($q) {
                $q->whereIn('user_id', getSellerIds());
            })->with('shop')->get();
        $proposal = ProjectProposal::with(['items' => function ($q) {
            $q->with('product.shop', 'service');
        }])->findOrFail($proposal_id);
        $proposal->is_seen = 1;
        $proposal->save();

        return view('project-management.project.projects.proposal.details', compact('proposal', 'products', 'manufactureProducts', 'services'));
    }

    public function generateInvoice($projectID, $proposal_id)
    {
        try {
            DB::beginTransaction();
            $projectProposal = ProjectProposal::with(['items'])
                ->find($proposal_id);

            $projectProposal->is_invoice_generated = true;
            $projectProposal->save();

            $proposalInvoice = new ProjectProposalInvoice();
            $proposalInvoice->code = 'PPI-' . time();
            $proposalInvoice->project_proposal_id = $projectProposal->id;
            $proposalInvoice->title = $projectProposal->title;
            $proposalInvoice->invoice_date = today();
            $proposalInvoice->sub_total = $projectProposal->sub_total;
            $proposalInvoice->tax_type = $projectProposal->tax_type;
            $proposalInvoice->tax_value = $projectProposal->tax_value;
            $proposalInvoice->tax_amount = $projectProposal->tax_amount;
            $proposalInvoice->discount_type = $projectProposal->discount_type;
            $proposalInvoice->discount_value = $projectProposal->discount_value;
            $proposalInvoice->discount_amount = $projectProposal->discount_amount;
            $proposalInvoice->shipping_charge = $projectProposal->shipping_charge;
            $proposalInvoice->deposit_amount = $projectProposal->deposit_amount;
            $proposalInvoice->total_amount = $projectProposal->total_amount;
            $proposalInvoice->signature = $projectProposal->signature;
            $proposalInvoice->user_id = $projectProposal->user_id;
            $proposalInvoice->project_id = $projectProposal->project_id;
            $proposalInvoice->active_status = true;
            $proposalInvoice->save();

            foreach ($projectProposal->items as $item) {
                $proposalInvoiceItem = new ProjectProposalInvoiceItem();
                $proposalInvoiceItem->project_proposal_invoice_id = $proposalInvoice->id;
                $proposalInvoiceItem->product_id = $item->product_id;
                $proposalInvoiceItem->variation = $item->variation;
                $proposalInvoiceItem->unit_price = $item->unit_price;
                $proposalInvoiceItem->markup = $item->markup;
                $proposalInvoiceItem->discount_type = $item->discount_type;
                $proposalInvoiceItem->discount_amount = $item->discount_amount;
                $proposalInvoiceItem->quantity = $item->quantity;
                $proposalInvoiceItem->price = $item->price;
                $proposalInvoiceItem->type = $item->type;
                $proposalInvoiceItem->save();
            }
            Toastr::success('Proposal Invoice Generated');
            DB::commit();
            return redirect()->route('project-management.project.invoice.index', $projectID);
        } catch (Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong!');
            return redirect()->back();
        }
    }


    public function generateProposal($projectID, $ideaboard_id)
    {
        try {
            $ideaboard = IdeaBoard::with('items')->where('id', $ideaboard_id)->first();

            DB::beginTransaction();
            if ($ideaboard) {
                $proposal = new ProjectProposal();

                $proposal->title = $ideaboard->title;
                $proposal->due_date = Carbon::today();
                $proposal->proposal_date = Carbon::today();
                $proposal->active_status = 1;
                $proposal->is_published = 0;
                $proposal->code = 'PL-' . time();
                $proposal->user_id = getUserId();
                $proposal->project_id = $projectID;

                $proposal->save();
                $ideaboard->is_generate_proposal = 1;
                $ideaboard->save();

                if ($ideaboard->items) {
                    foreach ($ideaboard->items as $item) {
                        $proposalItem = new ProposalItem();

                        $proposalItem->project_proposal_id = $proposal->id;
                        $proposalItem->product_id = $item->product_id ?? $item->project_service_id;
                        $proposalItem->unit_price = $item->unit_price;
                        $proposalItem->price = $item->price;
                        $proposalItem->quantity = $item->quantity;
                        $proposalItem->type = $item->type;
                        $proposalItem->variation = $item->variation ?? [];
                        $proposalItem->save();
                    }
                }

                $proposal->sub_total = $ideaboard->items->sum('price');
                $proposal->total_amount = $ideaboard->items->sum('price');
                $proposal->is_seen = false;
                $proposal->save();

                DB::commit();
                return response()->json(['message' => 'Proposal Generate successfully.', 'status' => 200, 'redirect_route' => route('project-management.project.proposal.index', $projectID)], 200);
            } else {
                return response()->json(['message' => 'Idea Board not found.', 'status' => 404], 404);
            }


        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function deleteProposal(Request $request)
    {
        DB::beginTransaction();
        try {
            $proposal = ProjectProposal::find($request->id);
            $proposal->items()->delete();
            $proposal->delete();
            DB::commit();
            return response()->json(['text' => 'Proposal deleted successfully', 'icon' => 'success', 'title' => 'success', 'status' => 200]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }
}
