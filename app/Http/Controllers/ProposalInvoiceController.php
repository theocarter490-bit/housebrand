<?php

namespace App\Http\Controllers;

use App\Facades\SendMail;
use App\Mail\ProposalInvoiceMail;
use App\Mail\TimeBillingInvoice;
use App\Models\PaymentMethod;
use App\Models\Project;
use App\Models\ProjectProposal;
use App\Models\ProjectProposalInvoice;
use App\Models\ProposalInvoicePaymentDetails;
use App\Models\ShopSetting;
use App\Models\TimeBilling;
use App\Models\TimeBillingPaymentDetail;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PDF;

class ProposalInvoiceController extends Controller
{
    //

    public function index(Request $request, $projectID)
    {
        if ($request->ajax()) {
            $data = ProjectProposalInvoice::select('*')
                ->where('project_id', $projectID);
            return DataTables::of($data)
                ->addIndexColumn()
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
                ->addColumn('date', function ($row) {
                    return dateFormat($row->invoice_date);
                })
                ->addColumn('total_price', function ($row) {
                    return getPriceFormat($row->total_amount);
                })
                ->editColumn('payment_status', function ($row) {
                    return match ($row->payment_status) {
                        0 => '<span class="badge custom-bg-danger">Unpaid</span>',
                        1 => '<span class="badge custom-bg-success">Paid</span>',
                        2 => '<span class="badge bg-label-warning ">Partially Paid</span>',
                        default => '--',
                    };
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }
                    if ($request->get('payment_status') == '0' || $request->get('payment_status') == '1' || $request->get('payment_status') == '2') {
                        $instance->where('payment_status', $request->get('payment_status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';


                    $btn = '<div class="d-inline-block text-nowrap">' .
                        '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                        '<div class="dropdown-menu dropdown-menu-end m-0">';
                    $btn .= '<a href="' . route('project-management.project.invoice.preview', [$row->project_id, $row->id]) . '" class="dropdown-item"><i class=" tf-icons ti ti-file-dollar"></i> ' . _trans('keyword.Preview') . '</a>';
                    $btn .= '<a href="javascript:0;" class="dropdown-item time_billing_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .

                        '</div>' .
                        '</div>';


                    return $btn;
                })
                ->rawColumns(['active_status', 'action', 'payment_status'])
                ->make(true);
        }
        $project = Project::find($projectID);

        return view('project-management.project.projects.invoice.index', compact('project'));
    }

    public function preview($projectID, $invoiceID)
    {
        $invoice = ProjectProposalInvoice::with(['items' => function ($query) {
            $query->with('product', 'service');
        }, 'project.client', 'paymentDetails'])
            ->find($invoiceID);
        $shop = ShopSetting::where('user_id', $invoice->user_id)->first();
        $paymentMethos = PaymentMethod::all();

        return view('project-management.project.projects.invoice.preview', compact('invoice', 'shop', 'paymentMethos'));
    }

    public function destroy(Request $request)
    {
        try {
            $data = ProjectProposalInvoice::with('paymentDetails')->where('id', $request->id)->first();
            if ($data->paymentDetails != null && $data->paymentDetails->count() > 0) {
                return response()->json(['text' => 'Invoice has payment. Can not be deleted', 'icon' => 'error', 'title' => 'error', 'status' => 200]);
            }
            $data->delete();
            return response()->json(['text' => 'Invoice deleted successfully', 'icon' => 'success', 'title' => 'success', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }


    public function sendInvoice(Request $request)
    {

        try {
            $recipientEmail = $request->input('invoice-to');
            $subject = $request->input('invoice-subject');
            $messageContent = $request->input('message');

            $invoice = ProjectProposalInvoice::with(['items' => function ($query) {
                $query->with('product', 'service');
            }, 'project', 'paymentDetails'])
                ->find($request->proposal_invoice_id);


            $shopSetting = ShopSetting::where('user_id', $invoice->user_id)->first();

            $pdf = $this->generateInvoicePDF($request->proposal_invoice_id);

            SendMail::sender($shopSetting->user_id)->to($recipientEmail)->send(new ProposalInvoiceMail($invoice, $pdf, $messageContent, $subject, $shopSetting));

            Toastr::success('Success', "Invoice sent successfully");
        } catch (\Exception $e) {
            Toastr::error('Error', "Failed to send invoice!");
        }
        return redirect()->back();
    }

    public function generateInvoicePDF($id)
    {

        $invoice = ProjectProposalInvoice::with(['items' => function ($query) {
            $query->with('product', 'service');
        }, 'project.client', 'paymentDetails'])
            ->find($id);

        $setting = ShopSetting::where('user_id', $invoice->user_id)->first();

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('project-management.project.projects.invoice.invoice', compact('invoice', 'setting'));

        return $pdf->output();
    }

    public function changeStatus(Request $request)
    {
        try {
            $data = ProjectProposalInvoice::findOrFail($request->id);
            $data->active_status = !$data->active_status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);

        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }


    public function invoicePrint($id)
    {
        $invoice = ProjectProposalInvoice::with(['items' => function ($query) {
            $query->with('product', 'service');
        }, 'project.client', 'paymentDetails'])
            ->find($id);
        $setting = ShopSetting::where('user_id', $invoice->user_id)->first();
        return view('project-management.project.projects.invoice.invoice', compact('invoice', 'setting'));
    }

    public function invoiceDownload($project_id, $invoice_id)
    {
        $invoice = ProjectProposalInvoice::with(['items' => function ($query) {
            $query->with('product', 'service');
        }, 'project.client', 'paymentDetails'])
            ->find($invoice_id);
        $setting = ShopSetting::where('user_id', $invoice->user_id)->first();

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/'),
        ])->loadView('project-management.project.projects.invoice.invoice', compact('invoice', 'setting'));
        return $pdf->stream();
    }

    public function paymentStore(Request $request, $project_id)
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
            $payment = new ProposalInvoicePaymentDetails();
            $payment->project_proposal_invoice_id = $request->time_billing_id;
            $payment->tnx_id = 'TNX-' . date('Ymd') . '-' . rand(1000, 9999);
            $payment->amount = $request->pay;
            $payment->payment_method_id = $request->payment_method;
            $payment->payment_date = $request->payment_date;
            $payment->current_due = $request->current_due;
            $payment->save();

            $timeBill = ProjectProposalInvoice::find($request->time_billing_id);
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

    public function paymentHistry($project_id, $invoice_id, Request $request)
    {
        try {
            $query = ProposalInvoicePaymentDetails::with('invoice')->where('project_proposal_invoice_id', $invoice_id);

            if ($request->filled('search')) {
                $searchTerm = $request->search;

                $query->where(function ($query) use ($searchTerm) {
                    $query->where('amount', 'LIKE', "%{$searchTerm}%")->where('tnx_id', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('invoice', function ($q) use ($searchTerm) {
                            $q->where('code', 'LIKE', "%{$searchTerm}%");
                        });
                });
            }

            $payments = $query->latest()->paginate(perPage());
            return view('project-management.project.projects.invoice.payment_history', compact('payments', 'project_id'));
        } catch (\Throwable $th) {
            Toastr::error(__('Something went wrong!'), 'Error', ['timeOut' => 2000]);
            return redirect()->back();
        }
    }

    public function deleteHistory(Request $request, $project_id)
    {
        try {
            $paymentHistory = ProposalInvoicePaymentDetails::whereId($request->payment_id)->first();
            $invoice = $paymentHistory->invoice;
            if ($paymentHistory) {
                $paymentHistory->delete();
                $invoice->payment_status = 0;
                if ($invoice->paymentDetails()->exists()) {
                    $invoice->payment_status = 1;
                }
                $invoice->save();

                return response()->json(['message' => 'Payment history deleted successfully', 'status' => 200]);
            } else {
                return response()->json(['message' => 'Payment history not found!', 'status' => 404]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!' . $e->getMessage(), 'status' => 500]);
        }
    }
}
