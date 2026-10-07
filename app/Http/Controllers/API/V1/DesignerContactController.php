<?php

namespace App\Http\Controllers\API\V1;

use Exception;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use App\Models\DesignerContact;
use App\Http\Controllers\Controller;
use App\Http\Requests\DesignerConntactRequest;

class DesignerContactController extends Controller
{

    public function store(DesignerConntactRequest $request)
    {
        $designerShop = ShopSetting::select('user_id')
                ->where('slug', $request->designer)
                ->first();
        if (!$designerShop) {
            return sendError('Designer not found.');
        }
        $existingContact = DesignerContact::where('email', $request->email)
            ->where('message', $request->message)
            ->where('designer_id', $designerShop->user_id)
            ->first();

        if ($existingContact) {
            return sendResponse('Designer contact already sent', null, 409);
        }
        try {
            $designerContact = new DesignerContact();
            $designerContact->name       = $request->name;
            $designerContact->designer_id = $designerShop->user_id;
            $designerContact->email      = $request->email;
            $designerContact->phone      = $request->phone;
            $designerContact->message    = $request->message;
            $designerContact->active_status = 0;
            $designerContact->save();

            return sendResponse('Designer Contact Request Send Successfully', null, 200);
        } catch (Exception $e) {
            return sendError($e->getMessage(), 'Something Went Wrong!', 500);
        }
    }

}
