<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Models\Audit;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {

        $search = "";
        $start_date = "";
        $end_date = "";
        $user_id = "";
        $event = "";

        $users = User::whereNotIN('role_id',[Role::CUSTOMER])->get();

        $audits = Audit::latest();

        if ($request->filled('user_id')) {
            $audits = $audits->where('user_id', $request->user_id);
            $user_id = $request->user_id;
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $audits = $audits->where('user_type', 'like', '%' . strtoupper($search) . '%');
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $audits = $audits->whereBetween('created_at', [$startDate, $endDate]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        if ($request->filled('event')) {
            $audits = $audits->where('event', strtolower(trim($request->event)));
            $event = $request->event;
        }

        $audits = $audits->paginate(perPage());

        return view('setting.activity-log.index', compact('users', 'audits', 'search', 'start_date', 'end_date', 'user_id', 'event'));
    }

    public function activityLogReportPDF(Request $request)
    {
        $start_date = "";
        $end_date = "";
        $user_id = "";
        $event = "";

        $audits = Audit::latest()->limit(10);
        if ($request->filled('user_id')) {
            $audits = $audits->where('user_id', $request->user_id);
            $user_id = $request->user_id;
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $audits = $audits->where('user_type', 'like', '%' . strtoupper($search) . '%');
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $audits = $audits->whereBetween('created_at', [$startDate, $endDate]);
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }

        if ($request->filled('event')) {
            $audits = $audits->where('event', strtolower(trim($request->event)));
            $event = $request->event;
        }

        $user = "";
        if ($user_id){
            $user = User::find($user_id);
        }

        $audits = $audits->get();

        $pdf = \PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('reports.activity-log.report_export', compact('audits',  'event', 'start_date', 'end_date', 'user'));

        return $pdf->stream();
    }

}
