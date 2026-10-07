<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialSectionListResource;
use App\Http\Resources\SpecialSectionResource;
use App\Models\ShopSetting;
use App\Models\SpecialSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecialSectionController extends Controller
{

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2|integer',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', perPage());

        $query = SpecialSection::with('category')
            ->where('type', $request->type)
            ->where('is_active', 1)
            ->where('user_id', getDesignerID())
            ->whereHas('category', function ($query) {
                $query->where('is_active', 1);
            })
            ->latest();

        // 🔑 Page & per_page aware Redis key
        $key = sprintf(
            'type_%s_page_%s_per_%s',
            $request->type,
            $page,
            $perPage
        );

        $specialSections = getDataFromRedisAPI(['special_section_list', getDesignerID()], $key, function () use ($query, $perPage) {
            return SpecialSectionListResource::collection(
                $query->paginate($perPage)
            )->response()->getData(true);
        });

        return sendResponse('Special Section List.', $specialSections);
    }


    public function details($section_id)
    {
        $specialSection = SpecialSection::with(['category', 'details.items.products' => function ($query) {
            $query->where('is_published', 1)->with('category');
        }])
            ->where('is_active', 1)
            ->where('id', $section_id)
            ->where('user_id', getDesignerID())
            ->first();
        if (!$specialSection) {
            return sendError('Special Section not found');
        }

        return sendResponse('Special Section List.', new SpecialSectionResource($specialSection));
    }

}
