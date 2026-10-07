<?php

namespace App\Http\Controllers;

use App\Models\DesignerCustomerAssignment;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Dompdf\Css\Color;
use App\Models\Product;
use App\Models\Project;
use App\Models\IdeaBoard;
use App\Models\TaskLabel;
use App\Models\TaskStatus;
use App\Models\TimeBilling;
use App\Models\ColorPalette;
use App\Models\ColorTheme;
use App\Models\Moodboard;
use Illuminate\Http\Request;
use App\Models\TimeBillingLog;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectProposalInvoice;
use App\Models\ShopSetting;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Artisan;
use App\Models\TimeBillingPaymentDetail;

class DevController extends Controller
{
    // productAdminToManufac
    public function productAdminToManufac()
    {
        $product = Product::where('user_id', 1)->update(['user_id' => Role::MANUFACTURER]);
        return response()->json($product);
    }

    public function modulePerSet()
    {
        $users = User::with('activeSubscription.plan')->where('is_subscribed', 1)->get();

        foreach ($users as $user) {
            $shop = $user->shop;
            $shop->modules = @$user->activeSubscription->plan->modules ?? [];
            $shop->save();
        }
        return response()->json($users);
    }

    public function colorPaletteSetup()
    {
        ColorPalette::where('active_status', 1)->delete();

        $colorPalettes = [
            [
                'primary' => '#0d3b66',
                'secondary' => '#006d77',
                'bg_primary' => '#e7ecef',
                'bg_secondary' => '#f8f9fa',
                'text_primary' => '#293241',
                'text_secondary' => '#FFFFFF',
            ],
            [
                'primary' => '#1b2839',
                'secondary' => '#D6C3B1',
                'bg_primary' => '#EFEAE0',
                'bg_secondary' => '#edf6f9',
                'text_primary' => '#023047',
                'text_secondary' => '#FFFFFF',
            ],
            [
                'primary' => '#1C395F',
                'secondary' => '#bac9b1',
                'bg_primary' => '#EBEFE0',
                'bg_secondary' => '#fdfdfd',
                'text_primary' => '#1C395F',
                'text_secondary' => '#FFFFFF',
            ],
        ];
        ColorPalette::truncate();
        foreach ($colorPalettes as $palette) {
            $color = new ColorPalette();
            $color->primary = $palette['primary'];
            $color->secondary = $palette['secondary'];
            $color->bg_primary = $palette['bg_primary'];
            $color->bg_secondary = $palette['bg_secondary'];
            $color->text_primary = $palette['text_primary'];
            $color->text_secondary = $palette['text_secondary'];
            $color->active_status = 1;
            $color->save();
        }

        ColorTheme::truncate();

        $superadmin = User::where('role_id', Role::SUPER_ADMIN)->first();
        if ($superadmin) {
            setDefaultAdminColorTheme($superadmin->id);
            setDefaultShopColorTheme($superadmin->id);
        }

        $designers = User::where('role_id', Role::DESIGNER)->get();
        foreach ($designers as $designer) {
            setDefaultShopColorTheme($designer->id);
            setDefaultAdminColorTheme($designer->id);
        }

        $manufacturers = User::whereIn('role_id', [Role::MANUFACTURER])->get();
        foreach ($manufacturers as $manufacturer) {
            setDefaultAdminColorTheme($manufacturer->id);
        }

        return response()->json(['message' => 'Color palettes created successfully']);

    }

    // rolePermissionUpdate
    public function rolePermissionUpdate()
    {
        // run PermissionSeeder
        Artisan::call('db:seed', ['--class' => 'PermissionSeeder']);
        return response()->json(['message' => 'Role permissions updated successfully']);
    }

    // projectEarse
    public function projectEarse()
    {

        IdeaBoard::truncate();
        TimeBillingPaymentDetail::truncate();
        TimeBillingLog::truncate();
        // TimeBilling::truncate();
        TaskComment::truncate();
        Task::truncate();
        TaskLabel::truncate();
        TaskStatus::truncate();
        ProjectProposalInvoice::truncate();
        Project::truncate();
    }

    // signUpSourceUpdate
    public function signUpSourceUpdate()
    {
        $users = User::whereNull('user_source')->get();
        foreach ($users as $user) {
            $user->user_source = "HB";
            $user->save();
        }
        return response()->json(['message' => 'User signup source updated successfully', 'updated_users' => $users->count()]);
    }

    public function setColorTheme($userId)
    {
        setDefaultAdminColorTheme($userId);
        return response()->json(['message' => 'Theme generate successfully']);
    }

    public function setSectionData()
    {
        $shops = ShopSetting::whereNull('section_content')
            ->whereHas('seller', function ($query) {
                $query->where('role_id', Role::DESIGNER);
            })
            ->get(['id', 'section_content']);

        if ($shops->isEmpty()) {
            return response()->json(['message' => 'No shops found requiring section content update']);
        }

        foreach ($shops as $shop) {
            $shop->update(['section_content' => sectionContent()]);
        }

        return response()->json(['message' => 'Section content data set successfully for ' . $shops->count() . ' shops']);
    }

    public function setDesignerCustomerAssignment()
    {
        $users = User::where('role_id', Role::CUSTOMER)->get();
        foreach ($users as $user) {
            $assign = new DesignerCustomerAssignment();
            $assign->customer_id = $user->id;
            $assign->designer_id = $user->designer_id;
            $assign->save();
        }
        return 'Designer migration completed';
    }

    // moodBoard
    public function moodBoard()
    {
        $products = Product::take(10)->select('id','name','thumbnail_img')->latest()->get();
        $products = $products->map(function ($product) {
            $product->image_url = asset(getFilePath($product->thumbnail_img));
            return $product;
        });
        $moodboard = Moodboard::where('user_id', auth()->id())->first();
        return view('mood-board.index', compact('products', 'moodboard'));
    }

    public function save(Request $request)
    {
        Moodboard::updateOrCreate(
            ['user_id' => auth()->id()],
            ['canvas_json' => $request->canvas_json]
        );

        return response()->json(['success' => true]);
    }

    public function sliderStyle()
    {
        $shops = ShopSetting::all();

        foreach ($shops as $shop) {
            $shop->home_slider_style = 'image';
            $shop->save();
        }

        return response()->json(['message' => 'Shop slider style updated successfully']);
    }
}
