<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\SliderResource;
use App\Models\Page;
use App\Models\FooterWidget;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageDetailResource;
use App\Http\Resources\FooterWidgetResource;

class FooterWidgetController extends Controller
{
    public function widgets()
    {
        $data = FooterWidget::with(['pages' => function ($query) {
            $query->where('user_id', getDesignerID())->where('active_status', 1);
        }])->where('active_status', 1)->orderBy('serial', 'ASC');

        $widgets = getDataFromRedisAPI(['footer_widget'], getDesignerID(), function () use ($data) {
            return FooterWidgetResource::collection($data->get())->resolve();
        });

        return sendResponse('Footer Widget list', $widgets);
    }

    // pageDetails
    public function pageDetails($slug)
    {
        $page = Page::where('slug', $slug)->where('user_id', getDesignerID())->where('active_status', 1)->first();


        if ($page) {
            return sendResponse('Page Details', new PageDetailResource($page));
        } else {
            return sendError('Page not found');
        }

    }
}
