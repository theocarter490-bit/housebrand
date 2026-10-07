<?php

namespace App\Http\Controllers;

use App\Http\Traits\FileUploadTrait;
use App\Models\Order;
use App\Models\OrderClaimReply;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderClaimReplyController extends Controller
{
    use FileUploadTrait;

    public function store(Request $request)
    {
        $request->validate([
            'message' => Rule::requiredIf($request->hasFile('file') == false),
            'order_claim_id' => 'required',
            'file' => 'sometimes|file|mimes:jpg,jpeg,png,gif,pdf,docx,csv,xlsx,webp|max:2048', // Optional file validation
        ]);

        try {
            $data = new OrderClaimReply();
            $data->user_id = Auth::user()->id;
            $data->order_claim_id = $request->order_claim_id;
            $data->details = $request->message ?? null;
            $data->created_by = Auth::user()->id;

            if ($request->hasFile('file')) {
                $path = $this->uploadFile($request->file('file'), 'order-claim-reply');
                $data->file = $path;
            }
            $data->save();
            return response()->json(['message' => 'Reply send successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }
}
