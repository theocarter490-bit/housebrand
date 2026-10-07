<?php

namespace App\Http\Controllers;

use App\Models\OrderClaim;
use App\Models\OrderClaimIssueType;
use App\Models\OrderClaimReply;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MyOrderClaimController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = OrderClaim::with(['user', 'order', 'orderClaimIssueType', 'createdBy', 'updatedBy'])
                ->whereHas('order', function ($q) {
                    $q->isClient('user_id')->latest();
                });

            if ($request->has('status') && in_array($request->get('status'), ['0', '1', '2'])) {
                $query->where('status', $request->get('status'));
            }

            if ($request->get('claim_status') !== null) {
                $query->where('order_claim_issue_type_id', $request->get('claim_status'));
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('file', function ($row) {
                    return getFileElement(getFilePath($row->file));
                })
                ->addColumn('issue_type', function ($row) {
                    return '<span class="badge bg-label-info">' . $row->orderClaimIssueType->name . '</span>';
                })
                ->editColumn('date_time', function ($row) {
                    return dateFormat($row->date_time);
                })
                ->editColumn('status', function ($row) {
                    match ($row->getAttributes()['status']) {
                        1 => $color_class = "bg-label-success",
                        2 => $color_class = "bg-label-danger",
                        default => $color_class = "bg-label-warning",
                    };
                    return '<span class="badge ' . $color_class . ' ">' . $row->status . '</span>';
                })
                ->addColumn('info', function ($row) {
                    return dataInfo($row);
                })
                ->addColumn('action', function ($row) {
                    if (hasPermission("customer_order_claim_read")) {
                        return '<a href="' . route('myOrder.order-claim.details', $row) . '" class="btn btn-primary text-white me-1">Details</a>';
                    }
                    return "";
                })
                ->rawColumns(['action', 'status', 'issue_type', 'info', 'file'])
                ->make(true);
        }
        $claimIssues = OrderClaimIssueType::where('active_status', 1)->get();
        return view('my-order.order-claim.index', compact('claimIssues'));
    }

    public function details($id)
    {
        try {
            $orderClaim = OrderClaim::with([
                'order.shop',
                'orderClaimIssueType',
                'createdBy',
                'updatedBy',
                'replies.user'
            ])->where('id', $id)->firstOrFail();

            $replies = OrderClaimReply::with('user')
                ->where('order_claim_id', $id)
                ->orderBy('created_at','asc')
                ->get();

            return view('my-order.order-claim.details', compact('orderClaim', 'replies'));

        } catch (Exception $e) {
            dd($e->getMessage());
            Toastr::error('Something Went Wrong!', 'Error!');
        }

        return back();
    }
}
