<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use App\Models\Role;
use App\Models\ShopSetting;
use Illuminate\Http\Request;

class FaqController extends Controller
{

    public function list(Request $request)
    {
        $userId = Role::SUPER_ADMIN;

        if ($slug = $request->query('designer')) {
            $designerShop = ShopSetting::select('user_id')
                ->where('slug', $slug)
                ->first();

            if (!$designerShop) {
                return sendError('Designer not found.');
            }

            $userId = $designerShop->user_id;
        }

        $data = Faq::where('active_status', 1)
            ->where('user_id', $userId)->get();

        return sendResponse('Faq List.', FaqResource::collection($data)->resource);
    }
}
