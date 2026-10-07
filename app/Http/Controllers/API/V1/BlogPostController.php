<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryResource;
use App\Http\Resources\BlogPostDetailsResource;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BlogPostController extends Controller
{

    public function index(Request $request)
    {
        try {

            $page = $request->get('page', 1);
            $perPage = perPage();
            $key = 'blog_post_list:' . md5(json_encode([
                    'page' => $page,
                    'per_page' => $perPage,
                    'category' => $request->get('category'),
                    'sortBy' => $request->get('sortBy'),
                    'search' => $request->get('search'),
                    'user_id' => getDesignerID(),
                ]));

            $data = getDataFromRedisAPI(
                ['blog_post_list', getDesignerID()],
                $key,
                function () use ($request) {

                    $query = BlogPost::query()
                        ->with(['createdBy', 'category'])
                        ->where('user_id', getDesignerID())
                        ->whereHas('category', function ($q) {
                            $q->where('is_active', 1);
                        });

                    if ($request->has('category')) {
                        $query->where('category_id', $request->get('category'));
                    }

                    if ($request->has('sortBy')) {
                        match ($request->get('sortBy')) {
                            'asc' => $query->orderBy('title'),
                            'desc' => $query->orderByDesc('title'),
                            default => $query->orderByDesc('id'),
                        };
                    } else {
                        $query->orderByDesc('id');
                    }

                    if ($request->has('search')) {
                        $query->where('title', 'like', '%' . $request->get('search') . '%');
                    }

                    $data = $query
                        ->where('publish_status', 1)
                        ->paginate(perPage());
                    return BlogPostResource::collection($data)->response()->getData(true);
                }
            );


            return sendResponse(
                'Blog Post.',
                $data
            );

        } catch (\Exception $e) {
            return sendError('Something went wrong.');
        }
    }

    public function details($slug)
    {
        try {
            $data = BlogPost::with(["createdBy", "contentDetails.items"])->where('slug', $slug)->first();
            if (!$data) {
                return sendError("Blog Post Not Found");
            }
            return sendResponse("Blog Post Details.", new BlogPostDetailsResource($data));
        } catch (\Exception $e) {
            return sendError("Something went wrong");
        }
    }

    public function categories()
    {
        try {
            $categories = BlogCategory::where('is_active', 1)->get();
            return sendResponse("Blog Categories.", BlogCategoryResource::collection($categories));
        } catch (\Exception $e) {
            return sendError("Something went wrong");
        }
    }
}
