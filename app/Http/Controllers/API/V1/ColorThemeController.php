<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ColorThemeResource;
use App\Http\Resources\SliderResource;
use App\Models\ColorTheme;
use Illuminate\Http\Request;

class ColorThemeController extends Controller
{

    public function colorThemes()
    {
        $query = ColorTheme::where('type', 0)->where('user_id', getDesignerID())->where('active_status', 1);

        // Fetch from cache or execute and then return the collection
        $colorTheme = getDataFromRedisAPI(['color_theme'], getDesignerID(), function () use ($query) {
            return (new ColorThemeResource($query->first()))->resolve();
        });
        if (!$colorTheme) {
            $colorTheme = new ColorThemeResource(ColorTheme::where('type', 0)->where('active_status', 1)->first());
        }
        return sendResponse('Color Themes.', $colorTheme);
    }
}
