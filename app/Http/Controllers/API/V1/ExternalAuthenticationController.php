<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Page;
use App\Models\Role;
use App\Models\ShopSetting;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExternalAuthenticationController extends Controller
{
    //

    public function verifyAccessToken(Request $request)
    {
        $setting = GeneralSetting::first();
        $token = $setting->api_key;

        try {
            if ($request->api_key != $token) {
                return sendError('Token Invalid');
            }

            $temp_token = [
                'token' => bin2hex(random_bytes(16)), // Generate random token
                'expires_at' => Carbon::now()->addMinutes(5)->timestamp // Expire in 2 minutes
            ];
            $setting->api_key_verify = $temp_token;
            $setting->save();

            return sendResponse('Temporary API key', Crypt::encryptString(json_encode($temp_token)));

        } catch (\Exception $e) {

            return sendError('Something Went Wrong');
        }

    }

    public function getHBAccessTokens(Request $request)
    {
        try {
            DB::beginTransaction();
            $setting = GeneralSetting::first();

            $tokenData = json_decode(Crypt::decryptString($request->api_key), true);
            $temp_token = json_decode($setting->api_key_verify);


            if (!$tokenData['expires_at'] >= Carbon::now()->timestamp && $tokenData['token'] != $temp_token['token']) {
                return sendError("Token Invalid");
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {

                $user = new User();
                $user->name = $request->full_name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->password = Hash::make('12345678');
                $user->role_id = Role::DESIGNER;
                $user->active_status = 1;
                $user->is_subscribed = 0;
                $user->subscription_required = 0;
                $user->user_source = 'WWP';
                $user->email_verified_at = Carbon::now();
                $user->save();

                $shop_setting = new ShopSetting();
                $shop_setting->user_id = $user->id;
                $shop_setting->shop_name = $request->full_name;
                $shop_setting->slug = createShopSlug($request->full_name);
                $shop_setting->shop_status = 0;

                $modules = ['Project Management', 'Employee Management', 'Event Management', 'Marketing', 'Expense Management', 'Notice Management', 'Blog'];
                $moduleJson = [];
                foreach ($modules as $module) {
                    $moduleJson[] = [
                        'slug' => Str::slug($module),
                        'limit' => 'unlimited',
                    ];
                }

                $shop_setting->modules = json_encode($moduleJson);
                $shop_setting->save();

                foreach (Page::pages as $page) {
                    $pg = new \App\Models\Page();
                    $pg->title = $page;
                    if ($page == "About Us") {
                        $slug = Str::slug('About');
                    } elseif ($page == "Contact Us") {
                        $slug = Str::slug('Contact');
                    } else {
                        $slug = Str::slug($page);
                    }
                    $pg->slug = $slug;
                    $pg->short_desc = 'This is ' . $page . ' page';
                    $pg->content = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>';
                    $pg->meta_title = $page;
                    $pg->meta_description = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>';
                    if ($page == 'About Us' || $page == 'Our Story' || $page == 'Contact Us') {
                        $pg->footer_widget_id = 1;
                    } elseif ($page == 'Team' || $page == 'Blog') {
                        $pg->footer_widget_id = 2;
                    } else {
                        $pg->footer_widget_id = 3;
                    }
                    $pg->user_id = $user->id;
                    $pg->save();
                }
            }
            $loginToken = $user->createToken('MyApp')->plainTextToken;
            DB::commit();
            return sendResponse('User Login token', $loginToken);

        } catch
        (\Exception $e) {
            DB::rollBack();
            return sendError('Something went wrong');
        }
    }
}
