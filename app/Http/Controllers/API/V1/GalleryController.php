<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', perPage());
        $itemPerData = $request->get('item_per_data', 6);

        $query = Gallery::where('user_id', getDesignerID())
            ->where('is_active', 1)
            ->latest();

        // Correct cache key
        $key = "page={$page}:perPage={$perPage}:itemPerData={$itemPerData}";

        $galleries = getDataFromRedisAPI(['gallery_list', getDesignerID()], $key,
            function () use ($query, $perPage, $itemPerData) {
                $paginator = $query->paginate($perPage);
                $collection = $paginator->getCollection();
                $collection->transform(function ($gallery) use ($itemPerData) {
                    $gallery->setRelation(
                        'details',
                        $gallery->details()->limit($itemPerData)->get()
                    );
                    return $gallery;
                });
                return GalleryResource::collection($collection)->resolve();
            }
        );

        return sendResponse('Gallery List.', $galleries);
    }

}
