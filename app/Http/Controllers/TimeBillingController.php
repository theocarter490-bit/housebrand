<?php

namespace App\Http\Controllers;

use App\Facades\SendMail;
use App\Mail\TimeBillingInvoice;
use Illuminate\Support\Facades\Mail;
use PDF;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Project;
use App\Models\ShopSetting;
use App\Models\TimeBilling;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\TimeBillingLog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\AssignProjectService;
use App\Models\TimeBillingPaymentDetail;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TimeBillingController extends Controller
{
    public function index(Request $request, $projectID)
    {
        if ($request->ajax()) {

            $data = TimeBilling::select('*')
                ->where('project_id', $projectID)
                ->where('active_status', 0)
                ->with('client', 'employee', 'serviceType.projectService')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('bill_type', function ($row) {
                    $statusLabel = $row->bill_type == 1 ? 'Billable' : 'Non Billable';
                    $statusBadgeClass = $row->bill_type == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if ($row->payment_status == 0) {
                        if (hasPermission('change_bill_type')) {
                            $isChecked = $row->bill_type == 1 ? 'checked' : '';
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
                    }

                    // Status badge, displayed below the toggle switch if it’s shown
                    $statusHtml .= '<div><span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span></div>';

                    $statusHtml .= '</div>'; // Closing the main container

                    return $statusHtml;
                })
                ->editColumn('payment_status', function ($row) {

                    return match ($row->payment_status) {
                        0 => '<span class="badge custom-bg-danger">Unpaid</span>',
                        1 => '<span class="badge custom-bg-success">Paid</span>',
                        2 => '<span class="badge bg-label-warning ">Partially Paid</span>',
                        default => '--',
                    };
                })
                ->addColumn('client', function ($row) {
                    return $row->client->name;
                })
                ->addColumn('employee', function ($row) {
                    return $row->employee->name;
                })
                ->addColumn('service_type', function ($row) {
                    return @$row->serviceType->projectService->title;
                })
                ->addColumn('time', function ($row) {
                    return secondsToHMS($row->duration);
                })
                ->addColumn('billed', function ($row) {
                    return getPriceFormat($row->total_amount);
                })
                ->addColumn('rate', function ($row) {
                    return getPriceFormat($row->rate);
                })
                ->addColumn('created_at', function ($row) {
                    return dateFormatwithTime($row->created_at);
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('bill_type') == '0' || $request->get('bill_type') == '1') {
                        $instance->where('bill_type', $request->get('bill_type'));
                    }
                    if ($request->get('payment_status') == '0' || $request->get('payment_status') == '1' || $request->get('payment_status') == '2') {
                        $instance->where('payment_status', $request->get('payment_status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';

                    if (hasPermission('read_time_breakdown') || hasPermission('time_billing_invoice_read') || hasPermission('time_billing_delete')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('read_time_breakdown')) {
                        $btn .= '<a href="' . route('project-management.project.time-billing.timeBreakdown', $row->id) . '" class="dropdown-item"><i class="ti ti-eye"></i> ' . _trans('keyword.Time Breakdown') . '</a>';
                    }

                    if (hasPermission('time_billing_invoice_read')) {
                        $btn .= '<a href="' . route('project-management.project.time-billing.invoicePreview', $row->id) . '" class="dropdown-item"><i class=" tf-icons ti ti-file-dollar"></i> ' . _trans('keyword.Invoice') . '</a>';
                    }

                    if (hasPermission('time_billing_delete') && $row->payment_status == 0) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item time_billing_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }
                    return $btn;
                })
                ->rawColumns(['client', 'employee', 'service_type', 'time', 'billed', 'bill_type', 'info', 'action', 'payment_status'])
                ->make(true);
        }
        $project = Project::find($projectID);

        return view('project-management.project.projects.time-billing.index', compact('project'));
    }

    public function customers(Request $request)
    {
        $customers = User::where('designer_id', getUserId())->where('active_status', 1)->get();

        return response()->json(['customers' => $customers]);
    }

    public function timeBreakdown($billID)
    {
        $timeBreakdowns = TimeBillingLog::where('time_billing_id', $billID)->paginate(perPage());
        return view('project-management.project.projects.time-billing.time_breakdown', compact('timeBreakdowns'));
    }

    public function projects(Request $request)
    {

        $project = Project::where('client_id', $request->user_id)->where('active_status', 1)->get();
        return response()->json(['projects' => $project]);
    }

    public function employees()
    {
        $employee = User::where('supervisor_id', getUserId())->orWhere('id', getUserId())->where('active_status', 1)->get();
        return response()->json(['employees' => $employee]);
    }

    public function services($employee_id)
    {
        $services = AssignProjectService::with('projectService')->whereHas('projectService', function ($q) {
            $q->where('active_status', 1);
        })->where('user_id', $employee_id)->where('active_status', 1)->get();
        return response()->json(['services' => $services]);
    }

    public function getRunningTime(Request $request)
    {
        $timeBilling = TimeBilling::with('currentActiveLog', 'project', 'client', 'employee', 'serviceType.projectService')
            ->withSum('logs', 'duration')
            ->where('user_id', Auth::user()->id)
            ->where('active_status', 1)
            ->first();

        return response()->json($timeBilling);
    }

    public function startTracking(Request $request)
    {
        try {
            $currentActiveBilling = TimeBilling::where('user_id', Auth::user()->id)
                ->where('active_status', 1)->first();
            if (!$currentActiveBilling) {
                $currentActiveBilling = TimeBilling::create([
                    'code' => 'TB-' . time(),
                    'project_id' => $request->project_id,
                    'client_id' => $request->client_id,
                    'employee_id' => $request->employee_id,
                    'assign_project_service_id' => $request->assign_project_service_id,
                    'rate' => $request->rate,
                    'description' => $request->description,
                    'bill_type' => $request->bill_type,
                    'user_id' => Auth::user()->id,
                ]);
            }

            $currentActiveBilling->logs()->create([
                'start_time' => Carbon::now(),
            ]);

            return response()->json(['message' => 'Tracking started', 'entry' => $currentActiveBilling]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }

    }

    public function pauseTracking(Request $request)
    {
        try {
            $entry = TimeBilling::with('currentActiveLog')->where('id', $request->time_tracker_id)->first();
            if ($entry->currentActiveLog) {
                $startTime = Carbon::parse($entry->currentActiveLog->start_time); // Given time
                $endTime = Carbon::now(); // Current time

                $diffInSeconds = $endTime->diffInSeconds($startTime);

                $entry->currentActiveLog->update([
                    'end_time' => $endTime,
                    'duration' => $diffInSeconds,
                ]);
            }

            return response()->json(['message' => 'Tracking Paused', 'entry' => $entry]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function restartTracking(Request $request)
    {
        try {
            $entry = TimeBilling::where('id', $request->time_tracker_id)->first();
            if ($entry) {
                $entry->logs()->create([
                    'start_time' => Carbon::now(),
                ]);
            }

            return response()->json(['message' => 'Tracking Start', 'entry' => $entry]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function saveTracking(Request $request)
    {
        try {
            $timeBilling = TimeBilling::with('currentActiveLog')->where('id', $request->time_tracker_id)->first();

            if ($timeBilling->currentActiveLog && $timeBilling->currentActiveLog->end_time == null) {
                $startTime = Carbon::parse($timeBilling->currentActiveLog->start_time); // Given time
                $endTime = Carbon::now(); // Current time

                $diffInSeconds = $endTime->diffInSeconds($startTime);

                $timeBilling->currentActiveLog->update([
                    'end_time' => $endTime,
                    'duration' => $diffInSeconds,
                ]);
            }
            $timeBilling->loadSum('logs', 'duration');
            $timeBilling->active_status = 0;
            $timeBilling->duration = $timeBilling->logs_sum_duration;

            $hourly_rate = $timeBilling->rate; // Cost per hour
            $seconds_in_hour = 3600; // 60 minutes * 60 seconds

            $cost_per_second = $hourly_rate / $seconds_in_hour;

            $timeBilling->total_amount = $timeBilling->logs_sum_duration * $cost_per_second;

            $timeBilling->save();
            return response()->json(['message' => 'Time Saved']);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }

    }

    public function invoicePreview($id)
    {
        $timeBilling = TimeBilling::with(['logs', 'client', 'serviceType.projectService'])
            ->find($id);
        $shop = ShopSetting::where('user_id', $timeBilling->user_id)->first();
        $paymentMethos = PaymentMethod::all();
        return view('project-management.project.projects.time-billing.invoice-preview', compact('timeBilling', 'paymentMethos', 'shop'));
    }


    public function invoice($id)
    {
        $timeBilling = TimeBilling::with(['logs', 'client', 'serviceType.projectService'])
            ->find($id);

        $setting = shopSetting();

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('project-management.project.projects.time-billing.invoice', compact('timeBilling', 'setting'));
        // return $pdf->loadview($order->code . '.pdf');
        return $pdf->stream();
    }

    public function paymentStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'time_billing_id' => 'required',
            'payment_method' => 'required',
            'payment_date' => 'required',
            'current_due' => 'required',
            'pay' => 'required',
        ]);

        $messages = $validator->messages();
        foreach ($messages->all() as $message) {
            Toastr::error($message, 'Failed', ['timeOut' => 2000]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $payment = new TimeBillingPaymentDetail();
            $payment->time_billing_id = $request->time_billing_id;
            $payment->tnx_id = 'TNX-' . date('Ymd') . '-' . rand(1000, 9999);
            $payment->amount = $request->pay;
            $payment->payment_method_id = $request->payment_method;
            $payment->payment_date = $request->payment_date;
            $payment->current_due = $request->current_due;
            $payment->save();

            $timeBill = TimeBilling::find($request->time_billing_id);
            if ($request->current_due <= 0) {
                $timeBill->payment_status = 1;
            } else {
                $timeBill->payment_status = 2;
            }
            $timeBill->save();
            DB::commit();
            Toastr::success(__('Payment Added Successfully'), 'Success', ['timeOut' => 2000]);
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error(__('Something went wrong!'), 'Error', ['timeOut' => 2000]);
            return redirect()->back();
        }
    }

    // paymentHistory
    public function paymentHistry($id, Request $request)
    {
        try {
            $query = TimeBillingPaymentDetail::with('timeBilling')->where('time_billing_id', $id);

            if ($request->filled('search')) {
                $searchTerm = $request->search;

                $query->where(function ($query) use ($searchTerm) {
                    $query->where('amount', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('timeBilling', function ($q) use ($searchTerm) {
                            $q->where('code', 'LIKE', "%{$searchTerm}%");
                        });
                });
            }

            $payments = $query->latest()->paginate(perPage());
            return view('project-management.project.projects.time-billing.payment_history', compact('payments'));
        } catch (\Throwable $th) {
            Toastr::error(__('Something went wrong!'), 'Error', ['timeOut' => 2000]);
            return redirect()->back();
        }
    }

    public function deleteHistory(Request $request)
    {
        try {
            $paymentHistory = TimeBillingPaymentDetail::whereId($request->payment_id)->first();
            $timeBill = $paymentHistory->timeBilling;
            if ($paymentHistory) {
                $paymentHistory->delete();
                $timeBill->payment_status = 0;
                if ($timeBill->paymentDetails()->exists()) {
                    $timeBill->payment_status = 1;
                }
                $timeBill->save();

                return response()->json(['message' => 'Payment history deleted successfully', 'status' => 200]);
            } else {
                return response()->json(['message' => 'Payment history not found!', 'status' => 404]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!' . $e->getMessage(), 'status' => 500]);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $data = TimeBilling::findOrFail($request->id);
            $data->bill_type = !$data->bill_type;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function sendInvoice(Request $request)
    {

        try {
            $recipientEmail = $request->input('invoice-to');
            $subject = $request->input('invoice-subject');
            $messageContent = $request->input('message');

            $timeBilling = TimeBilling::with(['logs', 'client', 'serviceType.projectService'])
                ->find($request->time_billing_id);

            $shopSetting = ShopSetting::where('user_id', $timeBilling->user_id)->first();

            $pdf = $this->generateInvoicePDF($request->time_billing_id);
            SendMail::sender($shopSetting->user_id)->to($recipientEmail)->send(new TimeBillingInvoice($timeBilling, $pdf, $messageContent, $subject, $shopSetting));

            Toastr::success('Success', "Invoice sent successfully");
        } catch (\Exception $e) {
            Toastr::error('Error', "Failed to send invoice!");
        }
        return redirect()->back();
    }

    public function generateInvoicePDF($id)
    {

        $timeBilling = TimeBilling::with(['logs', 'client', 'serviceType.projectService'])
            ->find($id);

        $setting = ShopSetting::where('user_id', $timeBilling->user_id)->first();

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('project-management.project.projects.time-billing.invoice', compact('timeBilling', 'setting'));

        return $pdf->output();
    }

    public function invoicePrint($id)
    {
        $timeBilling = TimeBilling::with(['logs', 'client', 'serviceType.projectService'])
            ->find($id);
        $setting = ShopSetting::where('user_id', $timeBilling->user_id)->first();
        return view('project-management.project.projects.time-billing.invoice', compact('timeBilling', 'setting'));
    }

    public function invoiceDownload($id)
    {

        $timeBilling = TimeBilling::with(['logs', 'client', 'serviceType.projectService'])
            ->find($id);
        $setting = ShopSetting::where('user_id', $timeBilling->user_id)->first();

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('project-management.project.projects.time-billing.invoice', compact('timeBilling', 'setting'));
        return $pdf->stream();
    }

    public function deleteTimeBilling(Request $request)
    {
        try {
            $data = TimeBilling::findOrFail($request->id);
            $data->logs()->delete();
            $data->paymentDetails()->delete();
            $data->delete();
            return response()->json(['text' => 'Time billing deleted successfully', 'icon' => 'success', 'title' => 'success', 'status' => 200]);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }


}
