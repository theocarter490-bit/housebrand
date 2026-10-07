<?php

namespace App\Http\Controllers;

use App\Http\Requests\ColorThemeStoreRequest;
use App\Models\ColorPalette;
use App\Models\ColorTheme;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ColorThemeController extends Controller
{

    public function index(Request $request)
    {
        $search = "";
        $active_status = "";

        $data['frontend'] = ColorTheme::where('type', 0)->where('user_id', Auth::user()->id);
        $data['backend'] = ColorTheme::where('type', 1)->where('user_id', Auth::user()->id);
        if ($request->has('search')) {
            $search = $request->search;
            $data['frontend'] = $data['frontend']->where('name', 'like', '%' . $search . '%');
            $data['backend'] = $data['backend']->where('name', 'like', '%' . $search . '%');
        }
        if ($request->has('active_status') && $request->get('active_status') != "") {
            $active_status = $request->active_status;
            $data['frontend'] = $data['frontend']->where('active_status', $active_status);
            $data['backend'] = $data['backend']->where('active_status', $active_status);
        }

        $data['frontend'] = $data['frontend']->orderBy('id', 'desc')->get();
        $data['backend'] = $data['backend']->orderBy('id', 'desc')->get();

        $colorPalettes = ColorPalette::where('active_status', 1)->get();
        $panel_status = $request->panel_status;

        return view('setting.color-themes', compact('data', 'search', 'active_status', 'colorPalettes', 'panel_status'));
    }

    public function store(ColorThemeStoreRequest $request)
    {
        if (moduleConditionLimitCheck('custom-theme', 'App\Models\ColorTheme') == false) {
            Toastr::error('You have reached the maximum quantity for this module.');
            return redirect()->back();
        }
        try {

            if ($request->style == 0) {
                $palette = ColorPalette::where('id', $request->color_palette)->first();
                if (!$palette) {
                    Toastr::error('Color Palette Not Found!');
                    return back();
                }
                $request->merge([
                    'primary' => $palette->primary,
                    'secondary' => $palette->secondary,
                    'bg_primary' => $palette->bg_primary,
                    'bg_secondary' => $palette->bg_secondary,
                    'text_primary' => $palette->text_primary,
                    'text_secondary' => $palette->text_secondary,
                ]);
            }

            $colorTheme = new ColorTheme();
            $colorTheme->name = $request->theme_name;
            $colorTheme->type = $request->type;
            $colorTheme->primary = $request->primary;
            $colorTheme->secondary = $request->secondary;
            $colorTheme->bg_primary = $request->bg_primary;
            $colorTheme->bg_secondary = $request->bg_secondary;
            $colorTheme->text_primary = $request->text_primary;
            $colorTheme->text_secondary = $request->text_secondary;
            $colorTheme->theme_status = 1;
            $colorTheme->active_status = 0;
            $colorTheme->user_id = auth()->id();
            $colorTheme->created_by = auth()->id();
            $colorTheme->save();
            Cache::forget('color_theme');
            removeDataFromRedisAPI(['color_theme'], getUserId());
            Toastr::success('Color Theme Created Successfully!');
        } catch (Exception $e) {
            Toastr::error('Something Went Wrong!');
        }
        return back();
    }

    private function setOrUpdateColorTheme(ColorThemeStoreRequest $request, $colorTheme): void
    {
        $colorTheme->name = $request->theme_name;
        if ($request->filled('type')) {
            $colorTheme->type = $request->type;
        }
        $colorTheme->primary_color = $request->primary_color;
        $colorTheme->secondary_color = $request->secondary_color;
        $colorTheme->background_color = $request->background_color;
        $colorTheme->button_bg_color = $request->btn_background_color;
        $colorTheme->button_text_color = $request->btn_text_color;
        $colorTheme->hover_color = $request->hover_color;
        $colorTheme->border_color = $request->border_color;
        $colorTheme->text_color = $request->text_color;
        $colorTheme->secondary_text_color = $request->secondary_text_color;
        $colorTheme->shadow_color = $request->shadow_color;
        $colorTheme->sidebar_bg = $request->sidebar_background;
        $colorTheme->sidebar_hover = $request->sidebar_hover;
    }


    public function delete(Request $request)
    {
        try {
            $colorTheme = ColorTheme::find($request->id);
            if ($colorTheme) {
                $colorTheme->delete();

                removeDataFromRedisAPI(['color_theme'], getUserId());
            }
        } catch (Exception $e) {
            Toastr::error('Something Went Wrong!');
        }
    }

    public function applyTheme(Request $request)
    {
        $colorTheme = ColorTheme::find($request->id);
        try {
            if ($colorTheme) {
                $colorThemesCollection = ColorTheme::where('type', $colorTheme->type)->where('user_id', Auth::user()->id)->get();
                foreach ($colorThemesCollection as $theme) {
                    $theme->active_status = 0;
                    $theme->save();
                }

                $colorTheme->active_status = 1;
                $colorTheme->save();

                removeDataFromRedisAPI(['color_theme'], getUserId());
                try {
                    Process::run('npm run build');
                } catch (\Throwable $th) {
                    Log::error($th);
                }

                return response()->json(['message' => 'Default Applied Successfully', 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'Color Theme Not Found!', 'status' => 404], 200);
            }
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }
}
