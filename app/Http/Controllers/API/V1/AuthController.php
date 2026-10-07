<?php

namespace App\Http\Controllers\API\V1;

use App\Facades\SendMail;
use App\Jobs\GenerateDemoDataJob;
use App\Mail\VerifyMailLink;
use App\Mail\VerifyMailOtp;
use App\Models\DesignerCustomerAssignment;
use App\Services\DemoDataService;
use Exception;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use App\Models\ShopSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\RegistrationInfoMail;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Notifications\VerifyEmail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validation_rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $messages = [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'password.required' => 'Password is required',
        ];

        $validator = validateRules($validation_rules, $messages);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors());
        }

        $user = User::with('role', 'designer', 'lastSubscription')->where('email', $request->email)->first();

        if ($user) {
            if (!in_array($user->role_id, [Role::CUSTOMER, Role::DESIGNER, Role::MANUFACTURER])) {
                return sendForbidden("You don't have access to this page");
            }
            if ($user->active_status == 0) {
                return sendForbidden('Your account is not active');
            }
        }

        if ($user && Hash::check($request->password, $user->password)) {
            $success['id'] = $user->id;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            $success['phone'] = $user->phone;
            $success['image'] = getFilePath($user->avatar);
            $success['token_type'] = 'Bearer';
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['role'] = $user->role->name;
            $success['role_id'] = $user->role_id;
            if ($user->role_id == Role::CUSTOMER) {
                $success['designer_id'] = $user->designer_id;
                $success['shop_slug'] = $user->designer->shop->slug;
            } elseif ($user->role_id == Role::DESIGNER || $user->role_id == Role::MANUFACTURER) {
                $success['subscription_required'] = $user->subscription_required;
                $success['is_subscribed'] = $user->is_subscribed;
                $success['plan_id'] = $user->lastSubscription->plan_id ?? null;
            }
            $success['active_status'] = $user->active_status == 1 ? true : false;
            $success['is_email_verified'] = $user->hasVerifiedEmail();

            return sendResponse('User login successfully.', $success);
        } else {
            return sendError("Invalid login credentials. Please check your details and try again.", ['error' => 'Unauthorised']);
        }
    }

    public function registration(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'type' => 'required|integer|in:3,4,5',
            'shop_name' => 'bail|required_if:type,3|unique:shop_settings,shop_name',
            'designer_id' => 'required_if:type,4|exists:users,id',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error.', $validator->errors());
        }

        try {
            DB::beginTransaction();
            $response = [];
            $success = [];
            $user = null;
            if ($request->type == Role::DESIGNER) {
                $response = $this->designerRegister($request);
                $user = $response[1];
                $success['is_subscribed'] = $user->is_subscribed;
            } else if ($request->type == Role::CUSTOMER) {
                $response = $this->userRegister($request);
                $user = $response[1];
                $success['designer_id'] = $user->designer_id;
                $success['shop_slug'] = $user->designer->shop->slug;
            } else if ($request->type == Role::MANUFACTURER) {
                $response = $this->manufacturerRegister($request);
                $user = $response[1];
                $success['is_subscribed'] = $user->is_subscribed;
            }

            $messages = $response[0];
            $success['id'] = $user->id;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            $success['phone'] = $user->phone;
            $success['image'] = getFilePath($user->avatar);
            $success['token_type'] = 'Bearer';
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['role'] = $user->role->name;
            $success['role_id'] = $user->role_id;
            $success['is_email_verified'] = $user->hasVerifiedEmail();
            if ($user) {
                try {
                    $this->sendMailOtp($user);
                } catch (\Throwable $th) {
                    return sendError('Email not send');
                }
            }

            DB::commit();
            return sendResponse($messages, $success);

        } catch (Exception $e) {
            DB::rollBack();
            return sendError('Something went wrong', $e);
        }
    }

    public function designerRegister($request)
    {

        $user = new User();
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role_id = Role::DESIGNER;
        $user->active_status = 1;
        $user->is_subscribed = 0;
        $user->user_source = "HB";
        $user->save();

        $dataService = new DemoDataService();
        $shop_setting = new ShopSetting();
        $shop_setting->user_id = $user->id;
        $shop_setting->shop_name = $request->shop_name;
        $shop_setting->logo = $dataService->uploadDemoImage("default/shop/logo.png", 'designer/' . $user->id . '/icon');
        $shop_setting->banner = $dataService->uploadDemoImage("default/shop/banner.png", 'designer/' . $user->id . '/icon');
        $shop_setting->slug = createShopSlug($request->shop_name);
        $shop_setting->section_content = json_encode(sectionContent());
        $shop_setting->shop_status = 1;
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

        setDefaultShopColorTheme($user->id);
        setDefaultAdminColorTheme($user->id);

        GenerateDemoDataJob::dispatch($user);


        return ['Designer registered successfully.', $user];
    }

    public function userRegister($request)
    {


        $user = new User();
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role_id = Role::CUSTOMER;
        $user->designer_id = $request->designer_id;
        $user->active_status = 1;
        $user->user_source = "HB";

        $user->save();

        $customerAssignDesigner = new DesignerCustomerAssignment();
        $customerAssignDesigner->customer_id = $user->id;
        $customerAssignDesigner->designer_id = $request->designer_id;
        $customerAssignDesigner->save();


        return ['User registered successfully.', $user];
    }

    public function manufacturerRegister($request)
    {

        $user = new User();
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role_id = Role::MANUFACTURER;
        $user->active_status = 1;
        $user->is_subscribed = 0;
        $user->user_source = "HB";
        $user->save();

        $shop_setting = new ShopSetting();
        $shop_setting->user_id = $user->id;
        $shop_setting->shop_name = $request->name;
        $shop_setting->slug = createShopSlug($request->name);
        $shop_setting->save();
        setDefaultAdminColorTheme($user->id);
        return ['Manufacturer registered successfully.', $user];
    }


    public function sendPasswordResetToken(Request $request)
    {
        $validation_rules = [
            'email' => 'required|email',
        ];
        $messages = [
            'email.required' => 'Email is required',
        ];

        $validator = validateRules($validation_rules, $messages);

        if ($validator->fails()) {
            return sendError('Validation Error.', $validator->errors());
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return [
                'status' => __($status)
            ];
        } elseif ($status == Password::RESET_THROTTLED) {
            return sendError('Password reset link already been sent. Check your email.');
        } else {
            return sendError('User Not Found.', ['error' => 'Unauthorised']);
        }
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                $user->tokens()->delete();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return sendResponse($status, 'Password changed successfully.');
        } else {
            return sendError('Credential did not matched');
        }
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return sendResponse('User logout successfully.');
    }

    public function sendMailOtp($user): bool
    {
        $validation_code = random_int(100000, 999999);
        $user->verification_code = $validation_code;
        $user->code_expire_at = Carbon::now()->addMinutes(30);
        $user->save();
        SendMail::to($user->email)->send(new VerifyMailOtp($user, $validation_code));
        return true;
    }

    public function sendVerificationMailOtp(Request $request)
    {
        try {
            $this->sendMailOtp($request->user());
            return sendResponse('Verification code sent on your email address.');
        } catch (\Exception $e) {
            return sendError("Something went wrong.");
        }

    }


    public function completeVerifyWithOTP(Request $request)
    {
        if (!$request->filled('code')) {
            return sendError('Verification code is required.');
        }

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return sendResponse(__('Email address already verified.'));
        }

        if (Carbon::now()->greaterThan($user->code_expire_at)) {
            return sendError('Your verification code has expired. Please click "Resend Code" to receive a new one.');
        }

        if ($request->code != $user->verification_code) {
            return sendError('Invalid verification code.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            // Send registration info email
            SendMail::to($user->email)->send(new RegistrationInfoMail($user));

            return sendResponse(__('Email address verified successfully.'));
        }

        return sendError('Email address not verified.');
    }


    public function sendMailLink($user): bool
    {
        SendMail::to($user->email)->send(new VerifyMailLink($user));
        return true;
    }

    public function sendVerificationMailLink(Request $request)
    {
        try {
            $this->sendMailLink($request->user());
            return true;
        } catch (\Exception $e) {
            return sendError("Something went wrong.");
        }

    }

    public function completeVerifyWithLink(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return sendResponse(__('Email address already verified.'));
        }

        if ($request->token != sha1($request->user()->email)) {
            return sendError('Invalid Validation Link.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            SendMail::to($request->user()->email)->send(new RegistrationInfoMail($request->user()));


            return sendResponse(__('Email address successfully verified.'));
        }
        return sendError('Email address not verified.');
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);
        $user = User::find(Auth::user()->id);
        if (Hash::check($request->password, $user->password)) {
            return sendResponse('Password verified successfully.');
        } else {
            return sendError('The password you entered is incorrect.');
        }

    }

}
