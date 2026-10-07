<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUsRequest;
use App\Models\ContactUs;
use Exception;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function contactUs(ContactUsRequest $request)
    {
        try {
            $contactUs = new ContactUs();
            $contactUs->name            = $request->name;
            $contactUs->email           = $request->email;
            $contactUs->phone           = $request->phone;
            $contactUs->message         = $request->message;
            $contactUs->designer_id     = getDesignerID();
            if (\Auth::guard('sanctum')->check()) {
                $contactUs->user_id     = \Auth::guard('sanctum')->id();
            }
            $contactUs->save();
            return sendResponse('Contact Request Send Successfully', null, 200);
        }catch (Exception $e){
            return sendError($e->getMessage(), 'Something Went Wrong!', 500);
        }
    }
}
