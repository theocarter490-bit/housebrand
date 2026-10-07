<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailStoreRequest;
use App\Http\Requests\MailRequest;
use App\Mail\TestMail;
use App\Models\EmailSetting;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Facades\SendMail;


class EmailSettingController extends Controller
{

    public function index()
    {
        $email_setting = EmailSetting::where('user_id', getUserId())->first();
        return view('setting.email-setting', compact('email_setting'));
    }

    public function store(EmailStoreRequest $request)
    {
        try {

            EmailSetting::updateOrCreate(
                ['user_id' => getUserId()],
                $request->validated()
            );

            Toastr::success('Email Setting Updated Successfully');
            return redirect()->back();

        } catch (Exception $e) {
            Log::error("Email Settings Save Error: " . $e->getMessage());
            Toastr::error('Something went wrong. Please check your inputs.');
            return redirect()->back();
        }
    }

    public function sendTestEmail(MailRequest $request)
    {
        $data = [
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        try {
            if (SendMail::sender(Auth::user()->id)->to($request->to_email)->send(new TestMail($data))) {
                Toastr::success('Email Send Successfully');
            } else {
                Toastr::error('Mail send failed. Please check your credentials.');
            }
        } catch (Exception $e) {
            Toastr::error('Something went wrong');
        }
        return back();
    }
}
