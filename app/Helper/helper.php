<?php

use App\Models\Document;
use App\Models\EmailSetting;
use App\Models\IdeaBoard;
use App\Models\ProjectProposal;
use App\Models\ProjectProposalInvoice;
use App\Models\Task;
use App\Models\TimeBilling;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\ColorPalette;
use App\Models\ColorTheme;
use App\Models\Role;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\OrderItem;
use App\Models\ShopSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\GlobalSetting;
use App\Models\ProductRequest;
use App\Models\TaskActivityLog;
use Illuminate\Support\Collection;
use App\Models\PaymentMethodStatus;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\DesignerSharedProduct;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Cache as Cache;
use Illuminate\Support\Facades\Redis;

if (!function_exists('generalSetting')) {
    function generalSetting()
    {
        return app('general_Setting');
    }
}

if (!function_exists('globalSetting')) {
    function globalSetting($key)
    {
        $globalSetting = GlobalSetting::where('key', $key)->where('active_status', 1)->first();
        if ($globalSetting) {
            return $globalSetting;
        } else {
            return '';
        }

    }
}

if (!function_exists('colorTheme')) {
    function colorTheme()
    {
        return app('color_theme');
    }
}

if (!function_exists('defaultColorTheme')) {
    function defaultColorTheme($user)
    {
        $defaultTheme = ColorTheme::where('type', 1)
            ->where('theme_status', 0)
            ->where('active_status', 1)
            ->first();

        if ($defaultTheme) {
            return $defaultTheme;
        } else {
            // If no default theme found, return the first active theme
            return ColorTheme::where('type', 1)
                ->where('active_status', 1)
                ->first();
        }
    }
}

if (!function_exists('loginBG')) {
    function loginBG()
    {
        return app('login_bg');
    }
}


if (!function_exists('shopSetting')) {
    function shopSetting()
    {
        return app('shop_Setting');
    }
}

if (!function_exists('administratorSetting')) {
    function administratorSetting()
    {
        return app('administrator_setting');
    }
}
if (!function_exists('activeLanguages')) {
    function activeLanguages()
    {
        return app('active_languages');
    }
}

// if (!function_exists('getFilePath')) {
//     function getFilePath($path)
//     {
//         if ($path && Storage::exists($path)) {
//             return asset('storage/' . $path);
//         }
//         return asset('/assets/img/placeholder/placeholder.png');
//     }
// }

if (!function_exists('isS3FileSystem')) {
    function isS3FileSystem()
    {
        static $isS3 = null;

        if ($isS3 === null) {
            $setting = globalSetting('file_system');
            $isS3 = $setting && $setting->value == 1 && !empty(config('filesystems.disks.s3.bucket'));
        }

        return $isS3;
    }
}

if (!function_exists('getFilePath')) {
    function getFilePath($path)
    {
        $placeholder = asset('/assets/img/placeholder/placeholder.png');

        if (empty($path)) {
            return $placeholder;
        }

        try {
            // Check if file exists in local storage
            if (Storage::exists($path)) {
                return asset('storage/' . $path);
            }

            // S3 objects are public, so build the URL directly. No exists() call:
            // it needs valid credentials and costs one AWS request per image.
            if (isS3FileSystem()) {
                return Storage::disk('s3')->url($path);
            }
        } catch (Exception $e) {
            Log::error('Error in getFilePath: ' . $e->getMessage());
            Toastr::error('An error occurred while fetching the file path.');
        }

        return $placeholder;
    }
}

//if (!function_exists('sendMail')) {
//    function sendMail($recipientEmail, $mailData)
//    {
//        $emailSetting = EmailSetting::isClient()->where('is_active', 1)->first();
//
//        if (!$emailSetting) {
//            $emailSetting = EmailSetting::where('user_id', 1)->first();
//        }
//
//        // --- 1. SET VENDOR CONFIG ---
//        $config = [
//            'transport' => $emailSetting->mail_driver ?? 'smtp', // Use 'transport' for Laravel 9+
//            'host' => $emailSetting->mail_host,
//            'port' => $emailSetting->mail_port,
//            'encryption' => $emailSetting->mail_encryption,
//            'username' => $emailSetting->mail_username,
//            'password' => $emailSetting->mail_password,
//            'from' => ['address' => $emailSetting->from_email, 'name' => $emailSetting->from_name],
//        ];
//
//        Config::set('mail.mailers.dynamic_smtp', array_merge(config('mail.mailers.smtp'), $config));
//        Config::set('mail.default', 'dynamic_smtp');
//
//        // Force Laravel to forget previous mailer instances
//        Mail::forgetMailers();
//
//        try {
//            Mail::to($recipientEmail)->send($mailData);
//            return true;
//        } catch (\Exception $e) {
//            Log::error("Vendor Mail Failure (User ID {$emailSetting->user_id}): " . $e->getMessage());
//
//            try {
//                $adminSetting = \App\Models\EmailSetting::where('user_id', 1)->first();
//
//                if ($adminSetting && $emailSetting->user_id != 1) {
//                    $adminConfig = [
//                        'transport' => $adminSetting->mail_driver ?? 'smtp',
//                        'host' => $adminSetting->mail_host,
//                        'port' => $adminSetting->mail_port,
//                        'encryption' => $adminSetting->mail_encryption,
//                        'username' => $adminSetting->mail_username,
//                        'password' => $adminSetting->mail_password,
//                        'from' => ['address' => $adminSetting->from_email, 'name' => 'System Alert'],
//                    ];
//
//                    Config::set('mail.mailers.dynamic_smtp', array_merge(config('mail.mailers.smtp'), $adminConfig));
//                    Config::set('mail.default', 'dynamic_smtp');
//                    Mail::forgetMailers();
//
//                    $data = [
//                        'host' => $emailSetting->mail_host,
//                        'port' => $emailSetting->mail_port,
//                        'name' => $emailSetting->from_name,
//                    ];
//
//                    Mail::to($emailSetting->from_email)->send(new \App\Mail\CredentialErrorMail($data));
//                }
//            } catch (\Exception $notifyError) {
//                Log::emergency("Failed to notify vendor via Admin SMTP: " . $notifyError->getMessage());
//            }
//
//            return false;
//        }
//    }
//}

if (!function_exists('getUserId')) {
    function getUserId()
    {
        /*
            In this case, role ID 3 is for the designer (system pre-defined) and role ID 5 is for manufacturer (system pre-defined).

            Return the user ID of the designer if the user is a designer;
            otherwise, return the user ID of the super admin, which is always 1.
        */
        if (Auth::user()->supervisor_id != null) {
            return Auth::user()->supervisor_id;
        }
        if (isSeller()) {
            return Auth::user()->id;
        }

        return 1;
    }
}


if (!function_exists('getDesignerID')) {
    function getDesignerID()
    {

        /*
            if request has designer slug than return the user id of designer
            else return the super admin(house brand) id which is 1
        */

        if (request()->has('designer') && !empty(request()->designer) && request()->designer != null) {
            $shop_setting = ShopSetting::where('slug', request()->designer)->first();
            if ($shop_setting)
                return $shop_setting->user_id;
        }
        return 1;
    }
}

if (!function_exists('getSellerIds')) {
    function getSellerIds()
    {
        if (request()->has('designer') && !empty(request()->designer) && request()->designer != null) {
            $shop_setting = ShopSetting::where('slug', request()->designer)->first();
            if ($shop_setting) return [$shop_setting->user_id]; // array return
        }
        return User::where('role_id', Role::MANUFACTURER)->where('active_status', 1)->pluck('id');
    }
}

if (!function_exists('getSharedProductsIds')) {
    function getSharedProductsIds()
    {
        if (request()->has('designer') && !empty(request()->designer) && request()->designer != null) {
            $shop_setting = ShopSetting::where('slug', request()->designer)->first();
            if ($shop_setting) {
                return DesignerSharedProduct::where('designer_id', $shop_setting->user_id)->where('is_published', 1)->where('status', 1)->select('product_id')->get()->pluck('product_id')->toArray();
            }
        }
        return [];
    }
}

if (!function_exists('getSellerIdByProductId')) {
    function getSellerIdByProductId($productId)
    {
        $product = Product::where('id', $productId)->first();
        if ($product->user_id) {
            return $product->user_id;
        }
        return null;
    }
}

if (!function_exists('hasPermissionForOperation')) {

    function hasPermissionForOperation($model, $columnName = 'seller_id')
    {
        /*
            The only person who is capable of modifying their own data is the designer.
            While a super admin can manage any data. Super admin role ID is 1.
        */

        if (!$model) {
            abort(404, 'Not found.');
        }

        if ($model instanceof \Illuminate\Database\Eloquent\Model) {
            $model = collect([$model]);
        }

        if ($model->isEmpty()) {
            abort(403);
        }

        // Normalize columnName into an array
        $columns = is_array($columnName) ? $columnName : [$columnName];

        foreach ($model as $data) {
            $hasAccess = false;

            foreach ($columns as $column) {
                if (
                    $data->{$column} === Auth::user()->id ||
                    Auth::user()->role_id === Role::SUPER_ADMIN ||
                    $data->{$column} === Auth::user()->supervisor_id
                ) {
                    $hasAccess = true;
                    break; // No need to check other columns
                }
            }

            if (!$hasAccess) {
                abort(403);
            }
        }
    }
}

if (!function_exists('isSeller')) {

    function isSeller(): bool
    {

        /*
        here only designer and manufacturer are seller
        this function exit to check if the user is seller or not.
        */
        return in_array(Auth::user()->role_id, [Role::MANUFACTURER, Role::DESIGNER]);

    }
}


if (!function_exists('createSlug')) {
    function createSlug($value)
    {
        return Illuminate\Support\Str::slug($value) . md5(uniqid(rand(), true));
    }
}
if (!function_exists('createShopSlug')) {
    function createShopSlug($value)
    {
        $shop_name_count = ShopSetting::where('shop_name', $value)->count();
        if ($shop_name_count > 0) {
            return Illuminate\Support\Str::slug($value) . ($shop_name_count + 1);
        }

        return Illuminate\Support\Str::slug($value);
    }
}

function dateFormat($date)
{
    return Carbon::parse($date)->format(generalSetting()->DateFormat->format ?? 'M d, Y');
}

function timeFormat($date)
{
    return Carbon::parse($date)->format('h:i A');
}

function dateFormatwithTime($date)
{
    return Carbon::parse($date)->format(generalSetting()->DateFormat->format . ' h:i A' ?? 'M d Y, h:i A');
}

function monthFormat($date)
{
    return Carbon::parse($date)->format('M Y');
}


function getCurrency()
{
    return generalSetting()->currency->symbol;
}

function getPriceFormat($amount)
{
    return getCurrency() . number_format($amount, 2);
}


if (!function_exists('activeMenu')) {
    function activeMenu($route, $output = "active")
    {
        if (Route::is("{$route}.*") || Route::is($route) || url()->current() == $route) {
            return $output;
        }
    }
}

if (!function_exists('openMenu')) {
    function openMenu(array $routes, $output = "open")
    {
        foreach ($routes as $route) {
            if (Route::is("{$route}.*")) {
                return $output;
            }
        }
    }
}

// Permission check
if (!function_exists('hasPermission')) {
    function hasPermission($keyword, $module_name = null)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if ($user->role_id == Role::SUPER_ADMIN) {
            return true;
        }

        $permissions = $user->permissions != null ? $user->permissions : ($user->role ? $user->role->permissions : []);

        if ($module_name) {
            $modules = shopSetting()->modules ?? [];

            if (is_string($modules)) {
                $modules = json_decode($modules, true);
            }

            if (!is_array($modules) || empty($modules)) {
                return false;
            }

            $moduleSlugs = array_column($modules, 'slug');

            if (!in_array($module_name, $moduleSlugs)) {
                return false;
            }
        }

        return is_array($permissions) && in_array($keyword, $permissions);
    }
}

if (!function_exists('hasModulePermission')) {
    function hasModulePermission($keyword)
    {
        $user = Auth::user();
        if (!$user) return false;

        // 1. Immediate access for Super Admin or users without subscription requirements
        if ($user->role_id == Role::SUPER_ADMIN || $user->subscription_required == 0) {
            return true;
        }

        // 2. Determine whose shop modules we should check (Self or Supervisor)
        $targetUser = null;

        if ($user->supervisor_id !== null && $user->supervisor) {
            if ($user->supervisor->activeSubscription || $user->supervisor->trail_mode == 1) {
                $targetUser = $user->supervisor;
            }
        } else {
            if ($user->activeSubscription || $user->trail_mode == 1) {
                $targetUser = $user;
            }
        }

        if ($targetUser && isset($targetUser->shop->modules)) {
            $modules = json_decode($targetUser->shop->modules);

            if (is_array($modules)) {
                foreach ($modules as $module) {
                    if (isset($module->slug) && $module->slug === $keyword) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}

if (!function_exists('isSubscribed')) {
    function isSubscribed($user)
    {
        if (!$user) {
            return false;
        }

        // 1. Immediate access for Super Admin or users without subscription requirements
        if ($user->role_id == Role::SUPER_ADMIN || $user->subscription_required == 0) {
            return true;
        }

        // 2. Check Supervisor subscription if applicable
        if ($user->supervisor_id !== null && $user->supervisor) {
            return ($user->supervisor->activeSubscription || $user->supervisor->trail_mode == 1);
        }

        // 3. Check User's own subscription
        return ($user->activeSubscription || $user->trail_mode == 1);
    }
}

if (!function_exists('moduleConditionLimitCheck')) {
    function moduleConditionLimitCheck($moduleSlug, $model, $columnName = 'user_id')
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->role_id == Role::SUPER_ADMIN || $user->subscription_required == 0) {
            return true;
        }

        // 2. Permission Check
        if (!hasModulePermission($moduleSlug)) {
            Toastr::error('You do not have permission to access this module.');
            return false;
        }

        // 3. Determine the "Account Owner" (Either the supervisor or the user themselves)
        $accountOwner = ($user->supervisor_id && $user->supervisor) ? $user->supervisor : $user;

        // 4. Check Subscription/Trial Status
        $hasActiveAccess = $accountOwner->activeSubscription || (isset($accountOwner->trail_mode) && $accountOwner->trail_mode == 1);

        if ($hasActiveAccess) {
            $limitCount = $model::where($columnName, getUserId())->count();
            $modules = json_decode($accountOwner->shop->modules ?? '[]', false);

            if (is_array($modules)) {
                foreach ($modules as $module) {
                    if ($module->slug === $moduleSlug) {
                        return ($module->limit === 'unlimited' || (int)$module->limit > $limitCount);
                    }
                }
            }
        }

        return false;
    }
}


if (!function_exists('checkPlanCondition')) {

    function checkPlanCondition($model): bool
    {
        $conditionValue = \shopSetting()->conditions;

        if (!$conditionValue) {
            return false;
        }
        $count = $model::isClient()->count();

        $modelName = strtolower(class_basename($model));
        $fullName = "no_of_{$modelName}";

        foreach ($conditionValue as $value) {
            if (array_key_exists($fullName, $value)) {
                if ($value[$fullName] == 'unlimited') {
                    return true;
                }
                if ((int)$value[$fullName] > (int)$count) {
                    return true;
                }
                return false;
            }
        }
        return false;
    }
}


// ENV Configurations
if (!function_exists('putEnvConfigration')) {
    function putEnvConfigration($envKey, $envValue)
    {
        $envValue = str_replace('\\', '\\' . '\\', $envValue);
        $value = '"' . $envValue . '"';
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n";
        $keyPosition = strpos($str, "{$envKey}=");


        if (is_bool($keyPosition)) {

            $str .= $envKey . '="' . $envValue . '"';
        } else {
            $endOfLinePosition = strpos($str, "\n", $keyPosition);
            $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
            $str = str_replace($oldLine, "{$envKey}={$value}", $str);

            $str = substr($str, 0, -1);
        }

        if (!file_put_contents($envFile, $str)) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('perPage')) {
    function perPage()
    {
        return request()->get('per_page', 10);
    }
}

if (!function_exists('getHourDifferences')) {
    function getHourDifferences($startTime, $endTime)
    {
        $to = Carbon::createFromFormat('Y-m-d H:s:i', $startTime);

        $from = Carbon::createFromFormat('Y-m-d H:s:i', $endTime);

        return $to->diffInHours($from);
    }
}
if (!function_exists('getDaysDifferences')) {
    function getDaysDifferences($startTime, $endTime)
    {
        $to = Carbon::createFromFormat('Y-m-d H:s:i', $startTime);

        $from = Carbon::createFromFormat('Y-m-d H:s:i', $endTime);

        return $to->diffInDays($from);
    }
}

if (!function_exists('getDaysAndHoursDifferences')) {
    function getDaysAndHoursDifferences($from, $to): string
    {
        if (!$from || !$to) {
            return '0 minutes remaining';
        }

        $from = Carbon::parse($from);
        $to = Carbon::parse($to);

        if ($to->lessThanOrEqualTo($from)) {
            return 'Expired';
        }

        $diff = $to->diff($from);

        $days = $diff->d + ($diff->m * 30) + ($diff->y * 365); // rough approx
        $hours = $diff->h;
        $minutes = $diff->i;

        $parts = [];

        if ($days > 0) {
            $parts[] = $days . ' ' . Str::plural('day', $days);
        }

        if ($hours > 0) {
            $parts[] = $hours . ' ' . Str::plural('hour', $hours);
        }

        if ($minutes > 0 || empty($parts)) {
            $parts[] = $minutes . ' ' . Str::plural('minute', $minutes);
        }

        return implode(' ', $parts) . ' remaining';
    }
}

if (!function_exists('calculateTimeProgressPercent')) {
    function calculateTimeProgressPercent($start, $end): int
    {
        if (!$start || !$end) {
            return 0;
        }

        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        $totalSeconds = $end->diffInSeconds($start);
        $passedSeconds = now()->lessThan($start)
            ? 0
            : now()->diffInSeconds($start);

        $progress = floor(($passedSeconds / $totalSeconds) * 100);
        return min(100, max(0, $progress)); // Clamp between 0 and 100
    }
}

if (!function_exists('getRemainingDaysHours')) {
    function getRemainingDaysHours($from, $to): array
    {
        if (!$from || !$to) {
            return ['days' => 0, 'hours' => 0];
        }

        $from = Carbon::parse($from);
        $to = Carbon::parse($to);

        // If time has already passed
        if ($to->lessThanOrEqualTo($from)) {
            return ['days' => 0, 'hours' => 0];
        }

        $diff = $to->diff($from);

        return [
            'days' => $diff->d + ($diff->m * 30) + ($diff->y * 365), // Approx. for months/years
            'hours' => $diff->h
        ];
    }
}


if (!function_exists('breadcrumb')) {
    function breadcrumb($title, $list)
    {
        $output = '<div class="row">
            <div class="col-12">
                <div>
                    <div class="d-flex justify-content-between align-items-center flex-md-row flex-column">
                        <div class="col-lg-6">
                            <div class="dashboard_header_title">
                                <h3>' . @$title . '</h3>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="dashboard_breadcam text-right">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="' . url('/dashboard') . '">Dashboard</a>
                                        </li>';
        if ($list != null) {
            foreach ($list as $url => $value) {
                if ($url === array_key_last($list)) {
                    $output .= '<li class="breadcrumb-item active" aria-current="page">' . $value . '</li>';
                } else {
                    $output .= '<li class="breadcrumb-item">';
                    $output .= '<a href="' . url($url) . '">' . $value . '</a>';
                    $output .= '</li>';
                }
            }
        }
        $output .= '</ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return $output;
    }
}
if (!function_exists('dataInfo')) {
    function dataInfo($data)
    {
        $output = '';

        if ($data->created_by != null) {
            $createdBy = @$data->createdBy->shop->shop_name ?? @$data->updatedBy->name;

            $output = '<p class="badge bg-label-dark data-info mb-2">'
                . 'Created By: ' . $createdBy . '<br>'
                . dateFormatwithTime(@$data->created_at)
                . '</p>';
        }

        if ($data->updated_by != null) {
            $updatedBy = @$data->updatedBy->shop->shop_name ?? @$data->updatedBy->name;

            $output .= '<p class="badge bg-label-secondary data-info mb-0">'
                . 'Updated By: ' . $updatedBy . '<br>'
                . dateFormatwithTime(@$data->updated_at)
                . '</p>';
        }

        return $output;
    }
}
if (!function_exists('getRoleName')) {
    function getRoleName($roleIds)
    {
        $role = Role::where('id', $roleIds)->first();
        $roleName = $role->name;
        return $roleName;
    }
}

if (!function_exists('isImage')) {
    /**
     * Check if the file is an image based on its extension.
     *
     * @param string $filePath
     * @return bool
     */
    function isImage($filePath)
    {
        // Get the file extension
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Check if the extension is one of the image formats
        return in_array(strtolower($extension), $imageExtensions);
    }
}


function getFileElement($filePath)
{
    // Get file extension
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // Check if file is an image
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

    // Check if file is a supported document type
    $isDocument = in_array($extension, ['pdf', 'docx', 'csv']);

    // Return appropriate HTML element based on file type
    if ($isImage) {
        return '
        <div class="thumb flex-shrink-0 mh_40 mw_40">
        <a href="' . $filePath . '" target="_blank">
        <img  height="80px;" width="80px;" class="img img-fluid" src="' . $filePath . '">
        </a>
        </div>
        ';
    } elseif ($isDocument) {
        return '
        <div class="thumb flex-shrink-0 mh_40 mw_40">
        <a href="' . $filePath . '" target="_blank">
        <img  height="80px;" width="80px;" class="img img-fluid"  src="' . asset('/assets/img/file_icons/' . $extension . '.png') . '">
        </a>
        </div>';
    } else {
        return '
        <div class="thumb flex-shrink-0 mh_40 mw_40">
        <img  height="80px;" width="80px;" class="img img-fluid"  src="' . asset('/assets/img/file_icons/file.png') . '">
        </div>';
    }
}

// capitalize
if (!function_exists('textCapitalize')) {
    function textCapitalize($text)
    {
        $sanitized = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $text);
        $sanitized = preg_replace('/\s+/', ' ', $sanitized);
        $titleCased = ucwords(strtolower($sanitized));
        return $titleCased;
    }
}


if (!function_exists('_translation')) {
    function _translation($key)
    {
        $trans = trans($key);
        try {
            $txt = $trans;
            $txt = Str::replace('_', ' ', ucfirst($txt));
            $txt = ucfirst($txt);
            return $txt;
        } catch (\Throwable $th) {
            return $key;
        }
    }
}
if (!function_exists('userLocal')) {
    function userLocal()
    {
        try {
            $user = auth()->user();
            if (isset($user->language)) {
                $user_lang = $user->language;
            } else {
                $user_lang = App::getLocale();
            }
            return $user_lang;
        } catch (\Throwable $th) {
            return 'en';
        }
    }
}

if (!function_exists('_trans')) {
    function _trans($value)
    {

        try {
            if (env('APP_ENV') == 'production') {
                return trans($value);
            } else {

                $local = userLocal() ? userLocal() : app()->getLocale();

                $langPath = base_path('lang/' . $local . '/');
                if (!file_exists($langPath)) {
                    mkdir($langPath, 0777, true);
                }
                if (str_contains($value, '.')) {
                    $new_trns = explode('.', $value);
                    $file_name = $new_trns[0];
                    // $trans_key = $new_trns[1];
                    $trans_key = str_replace($file_name . '.', '', $value);

                    $file_path = $langPath . '' . $file_name . '.php';
                    if (file_exists($file_path)) {
                        $file_content = include($file_path);

                        if (array_key_exists($trans_key, $file_content)) {
                            return _translation($value);
                        } else {
                            $file_content[$trans_key] = $trans_key;
                            $str = <<<EOT
                                            <?php
                                                return [
                                            EOT;
                            foreach ($file_content as $key => $val) {
                                if (gettype($val) == 'string') {

                                    $line = <<<EOT
                                                                    "{$key}" => "{$val}",\n
                                                                EOT;
                                }
                                if (gettype($val) == 'array') {
                                    $line = <<<EOT
                                                                            "{$key}" => [\n
                                                                        EOT;
                                    $str .= $line;
                                    foreach ($val as $lang_key => $lang_val) {

                                        $line = <<<EOT
                                                                            "{$lang_key}" => "{$lang_val}",\n
                                                                        EOT;

                                        $str .= $line;
                                    }

                                    $line = <<<EOT
                                                                        ],\n
                                                                    EOT;
                                }

                                $str .= $line;
                            }
                            $end = <<<EOT
                                                    ]
                                            ?>
                                            EOT;
                            $str .= $end;

                            file_put_contents($file_path, $str, $flags = 0, $context = null);
                        }
                    } else {

                        fopen($file_path, 'w');
                        $file_content = [];
                        $file_content[$trans_key] = $trans_key;
                        $str = <<<EOT
                                            <?php
                                                return [
                                            EOT;
                        foreach ($file_content as $key => $val) {
                            if (gettype($val) == 'string') {

                                $line = <<<EOT
                                                                    "{$key}" => "{$val}",\n
                                                                EOT;
                            }
                            if (gettype($val) == 'array') {
                                $line = <<<EOT
                                                                            "{$key}" => [\n
                                                                        EOT;
                                $str .= $line;
                                foreach ($val as $lang_key => $lang_val) {

                                    $line = <<<EOT
                                                                            "{$lang_key}" => "{$lang_val}",\n
                                                                        EOT;

                                    $str .= $line;
                                }

                                $line = <<<EOT
                                                                        ],\n
                                                                    EOT;
                            }

                            $str .= $line;
                        }
                        $end = <<<EOT
                                                    ]
                                            ?>
                                            EOT;
                        $str .= $end;

                        file_put_contents($file_path, $str, $flags = 0, $context = null);
                    }
                    return _translation($value);
                } else {

                    $trans_key = $value;
                    $file_path = base_path('lang/' . $local . '/' . $local . '.php');

                    fopen($file_path, 'w');
                    $file_content = [];
                    $file_content[$trans_key] = $trans_key;
                    $str = <<<EOT
                                            <?php
                                                return [
                                            EOT;
                    foreach ($file_content as $key => $val) {
                        if (gettype($val) == 'string') {

                            $line = <<<EOT
                                                                    "{$key}" => "{$val}",\n
                                                                EOT;
                        }
                        if (gettype($val) == 'array') {
                            $line = <<<EOT
                                                                            "{$key}" => [\n
                                                                        EOT;
                            $str .= $line;
                            foreach ($val as $lang_key => $lang_val) {

                                $line = <<<EOT
                                                                            "{$lang_key}" => "{$lang_val}",\n
                                                                        EOT;

                                $str .= $line;
                            }

                            $line = <<<EOT
                                                                        ],\n
                                                                    EOT;
                        }

                        $str .= $line;
                    }
                    $end = <<<EOT
                                                    ]
                                            ?>
                                            EOT;
                    $str .= $end;

                    file_put_contents($file_path, $str, $flags = 0, $context = null);
                    return _translation($value);
                }
                return _translation($value);
            }
        } catch (Exception $exception) {
            return $value;
        }
    }
}

if (!function_exists('getEmbedUrl')) {
    function getEmbedUrl($url)
    {
        // function for generating an embed link
        $finalUrl = '';

        if (strpos($url, 'facebook.com/') !== false) {
            // Facebook Video
            $finalUrl .= 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode($url) . '&show_text=1&width=200';
        } elseif (strpos($url, 'vimeo.com/') !== false) {
            // Vimeo video
            $videoId = isset(explode("vimeo.com/", $url)[1]) ? explode("vimeo.com/", $url)[1] : null;
            if (strpos($videoId, '&') !== false) {
                $videoId = explode("&", $videoId)[0];
            }
            $finalUrl .= 'https://player.vimeo.com/video/' . $videoId;
        } elseif (strpos($url, 'youtube.com/') !== false) {
            // Youtube video
            $videoId = isset(explode("v=", $url)[1]) ? explode("v=", $url)[1] : null;
            if (strpos($videoId, '&') !== false) {
                $videoId = explode("&", $videoId)[0];
            }
            $finalUrl .= 'https://www.youtube.com/embed/' . $videoId;
        } elseif (strpos($url, 'youtu.be/') !== false) {
            // Youtube  video
            $videoId = isset(explode("youtu.be/", $url)[1]) ? explode("youtu.be/", $url)[1] : null;
            if (strpos($videoId, '&') !== false) {
                $videoId = explode("&", $videoId)[0];
            }
            $finalUrl .= 'https://www.youtube.com/embed/' . $videoId;
        } elseif (strpos($url, 'dailymotion.com/') !== false) {
            // Dailymotion Video
            $videoId = isset(explode("dailymotion.com/", $url)[1]) ? explode("dailymotion.com/", $url)[1] : null;
            if (strpos($videoId, '&') !== false) {
                $videoId = explode("&", $videoId)[0];
            }
            $finalUrl .= 'https://www.dailymotion.com/embed/' . $videoId;
        } else {
            $finalUrl .= $url;
        }
        return $finalUrl;
    }
}

if (!function_exists('calculateOrderDue')) {
    function calculateOrderDue($order)
    {
        return @$order->grand_total_amount - $order->paymentDetails->sum('amount');
    }
}

if (!function_exists('calculateProposalInvoiceDue')) {
    function calculateProposalInvoiceDue($invoice)
    {
        return @$invoice->total_amount - $invoice->paymentDetails->sum('amount');
    }
}

if (!function_exists('calculateTimeBillingDue')) {
    function calculateTimeBillingDue($timeBilling)
    {
        return @$timeBilling->total_amount - $timeBilling->paymentDetails->sum('amount');
    }
}

if (!function_exists('numberToWords')) {
    function numberToWords($num = '')
    {
        $num = (string)((int)$num);

        if ((int)($num) && ctype_digit($num)) {
            $words = array();

            $num = str_replace(array(',', ' '), '', trim($num));

            $list1 = array(
                '',
                'one',
                'two',
                'three',
                'four',
                'five',
                'six',
                'seven',
                'eight',
                'nine',
                'ten',
                'eleven',
                'twelve',
                'thirteen',
                'fourteen',
                'fifteen',
                'sixteen',
                'seventeen',
                'eighteen',
                'nineteen'
            );

            $list2 = array(
                '',
                'ten',
                'twenty',
                'thirty',
                'forty',
                'fifty',
                'sixty',
                'seventy',
                'eighty',
                'ninety',
                'hundred'
            );

            $list3 = array(
                '',
                'thousand',
                'million',
                'billion',
                'trillion',
                'quadrillion',
                'quintillion',
                'sextillion',
                'septillion',
                'octillion',
                'nonillion',
                'decillion',
                'undecillion',
                'duodecillion',
                'tredecillion',
                'quattuordecillion',
                'quindecillion',
                'sexdecillion',
                'septendecillion',
                'octodecillion',
                'novemdecillion',
                'vigintillion'
            );

            $num_length = strlen($num);
            $levels = (int)(($num_length + 2) / 3);
            $max_length = $levels * 3;
            $num = substr('00' . $num, -$max_length);
            $num_levels = str_split($num, 3);

            foreach ($num_levels as $num_part) {
                $levels--;
                $hundreds = (int)($num_part / 100);
                $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ($hundreds == 1 ? '' : 's') . ' ' : '');
                $tens = (int)($num_part % 100);
                $singles = '';

                if ($tens < 20) {
                    $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
                } else {
                    $tens = (int)($tens / 10);
                    $tens = ' ' . $list2[$tens] . ' ';
                    $singles = (int)($num_part % 10);
                    $singles = ' ' . $list1[$singles] . ' ';
                }
                $words[] = $hundreds . $tens . $singles . (($levels && (int)($num_part)) ? ' ' . $list3[$levels] . ' ' : '');
            }
            $commas = count($words);
            if ($commas > 1) {
                $commas = $commas - 1;
            }

            $words = implode(', ', $words);

            $words = trim(str_replace(' ,', ',', ucwords($words)), ', ');
            if ($commas) {
                $words = str_replace(',', ' and', $words);
            }

            return $words;
        } else if (!((int)$num)) {
            return 'Zero';
        }
        return '';
    }
}

if (!function_exists('getDataFromRedis')) {

    function getDataFromRedis($key, $query, $duration = 3600)
    {
        Cache::forget($key);
        return Cache::remember($key, $duration, function () use ($query) {
            return $query;
        });
    }
}

if (!function_exists('getDataFromRedisAPI')) {
    function getDataFromRedisAPI($tags, $key, $callback, $duration = 86400)
    {

        $globalSetting = Cache::tags(['globalSetting'])->remember('cache_system', $duration, function () use ($key) {
            return GlobalSetting::where('key', 'cache_system')->where('active_status', 1)->first()->toArray();
        });
        if ($globalSetting['value']) {
            return Cache::tags($tags)->remember($key, $duration, $callback);
        }
        return $callback();
    }
}
function removeDataFromRedisAPI($tags, $key = null)
{
    if ($key) {
        Cache::tags($tags)->forget($key); // delete specific key
    } else {
        Cache::tags($tags)->flush(); // delete everything under this tag
    }
}


if (!function_exists('isProductInUse')) {
    function isProductInUse($product)
    {
        return OrderItem::whereProductId($product->id)->exists() ||
            DesignerSharedProduct::whereProductId($product->id)->exists() ||
            ProductRequest::whereProductId($product->id)->exists() ||
            Wishlist::whereProductId($product->id)->exists() ||
            Cart::whereProductId($product->id)->exists();
    }
}
if (!function_exists('checkIfStripeIsSetup')) {
    function checkIfStripeIsSetup($id)
    {
        $status = PaymentMethodStatus::where('user_id', $id)->where('payment_method_id', 1)->where('active_status', 1)->where('setup_status', 1)->first();
        if ($status) {
            return true;
        }
        return false;
    }
}
if (!function_exists('checkIfPaypalIsSetup')) {
    function checkIfPaypalIsSetup($id)
    {
        $status = PaymentMethodStatus::where('user_id', $id)->where('payment_method_id', 2)->where('active_status', 1)->where('setup_status', 1)->first();
        if ($status) {
            return true;
        }
        return false;
    }
}

if (!function_exists('renderStarRating')) {
    function renderStarRating($rating, $maxRating = 5)
    {
        $fullStar = "<i class = 'fas fa-star active'></i>";
        $halfStar = "<i class = 'fas fa-star half'></i>";
        $emptyStar = "<i class = 'fas fa-star'></i>";
        $rating = $rating <= $maxRating ? $rating : $maxRating;

        $fullStarCount = (int)$rating;
        $halfStarCount = ceil($rating) - $fullStarCount;
        $emptyStarCount = $maxRating - $fullStarCount - $halfStarCount;

        $html = str_repeat($fullStar, $fullStarCount);
        $html .= str_repeat($halfStar, $halfStarCount);
        $html .= str_repeat($emptyStar, $emptyStarCount);
        echo $html;
    }
}

if (!function_exists('checkIfUserIsClient')) {
    function checkIfUserIsClient($user)
    {
        return in_array($user->role_id, [Role::DESIGNER, Role::MANUFACTURER]);
    }
}

if (!function_exists('userInfo')) {
    function userInfo($user)
    {
        if (checkIfUserIsClient($user)) {
            $user = $user->load('shop');
            $collect = (object)collect([
                'avatar' => optional($user->shop)->logo,
                'name' => optional($user->shop)->shop_name,
                'created_at' => optional($user->shop)->created_at,
                'email' => optional($user->shop)->email,
                'phone' => optional($user->shop)->phone,
            ])->all();
            return $collect;
        }
        return $user;
    }
}

if (!function_exists('getAverageRating')) {
    function getAverageRating($user)
    {
        $ratings = $user->avgRating ?? collect(); // ensure it's always a collection
        $totalRating = $ratings->sum('rating');
        $totalReview = $ratings->count();
        $avgRating = $totalReview > 0 ? $totalRating / $totalReview : 0;
        return round($avgRating * 2) / 2;
    }
}

if (!function_exists('getProductAverageRating')) {
    function getProductAverageRating($product)
    {
        $totalRating = $product->reviews->sum('rating');
        $totalReview = $product->reviews->count();
        $avgRating = $totalReview > 0 ? $totalRating / $totalReview : 0;
        $avgRating = round($avgRating * 2) / 2;

        return $avgRating ?? 0;
    }
}

if (!function_exists('getProductTotalReviews')) {
    function getProductTotalReviews($product)
    {
        return $product->reviews->count();
    }
}
if (!function_exists('subscriptionStatus')) {
    function subscriptionStatus($user)
    {
        $status = '';
        if ($user->subscription_required == 0) {
            $status .= ' <span class="badge bg-label-warning">Subscription Not Required</span>';

        } else {
            if ($user->is_subscribed == 1) {
                $status .= ' <span class="badge bg-label-success" >Subscribed</span >';

            } else {
                $status .= '<span class="badge bg-label-danger" >Not Subscribed</span > ';
                if ($user->trail_mode == 1) {
                    $status .= '<span class="badge bg-label-warning" >Free Trial</span > ';
                }
            }
        }
        return $status;
    }
}

if (!function_exists('projectTabMenu')) {
    function projectTabMenu($project, $type, $sourceId)
    {
        $menu = '<ul class="nav nav-pills flex-nowrap flex-md-wrap overflow-auto text-nowrap mb-3 pb-1">';

        if (hasPermission('project_overview')) {
            $menu .= '<li class="nav-item">
                        <a class="nav-link ' . ($type == 'project' ? 'active' : '') . ' py-2"
                           href="' . route('project-management.project.overview', $project->id) . '">
                            <i class="ti ti-chart-donut me-1"></i>Overview
                        </a>
                    </li>';
            if (hasPermission('tasks_read')) {


                $menu .= '<li class="nav-item">
                        <a class="nav-link ' . ($type == 'task' ? 'active' : '') . ' py-2"
                           href="' . route('project-management.project.task', $project->id) . '">
                            <i class="ti ti-checklist"></i>Task
                        </a>
                    </li>';
            }

            if (hasPermission('time_billing_read')) {
                $menu .= '<li class="nav-item">
                        <a class="nav-link ' . ($type == 'time-billing' ? 'active' : '') . ' py-2" href="' . route('project-management.project.time-billing.index', $project->id) . '">
                            <i class="ti ti-calendar-time me-1"></i>Time Billing
                        </a>
                    </li>';
            }


            $menu .= '<li class="nav-item">
                        <a class="nav-link ' . ($type == 'idea-board' ? 'active' : '') . ' py-2" href="' . route('project-management.project.idea-board.index', $project->id) . '">
                            <i class="ti ti-bulb me-1"></i>Idea Board
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ' . ($type == 'proposal' ? 'active' : '') . ' py-2" href="' . route('project-management.project.proposal.index', $project->id) . '">
                            <i class="ti ti-address-book me-1"></i>Proposal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ' . ($type == 'invoice' ? 'active' : '') . ' py-2" href="' . route('project-management.project.invoice.index', $project->id) . '">
                            <i class="ti ti-address-book me-1"></i>Invoice
                        </a>
                    </li>';
        }

        if (hasPermission('document_management_read')) {
            $menu .= '<li class="nav-item">
                    <a class="nav-link py-2" href="' . route('project-management.document.index', ['source' => 'project', 'source_id' => $sourceId]) . '">
                        <i class="ti ti-file me-1"></i>Documents
                    </a>
                  </li>';
        }


        $menu .= '</ul>';

        return $menu;
    }
}


if (!function_exists('projectProgressBar')) {
    function getProjectProgressValue($project)
    {
        $projectId = $project->id;

        // ---------- Tasks ----------
        $tasks = Task::where('project_id', $projectId)->count();

        $completedTasks = Task::where('project_id', $projectId)
            ->whereHas('status', function ($q) {
                $q->where('system_default', 1)
                    ->where('name', 'Complete');
            })
            ->count();

        $taskProgress = $tasks > 0
            ? round(($completedTasks / $tasks) * 20)
            : 0;

        // ---------- Time Billing ----------
        $timeBillingCounts = TimeBilling::where('project_id', $projectId)
            ->where('bill_type', TimeBilling::BILLABLE)
            ->selectRaw('
            COUNT(*) as total,
            SUM(payment_status = 1) as paid
        ')
            ->first();

        $timeBillingProgress = ($timeBillingCounts->total ?? 0) > 0
            ? round(($timeBillingCounts->paid / $timeBillingCounts->total) * 20)
            : 0;

        // ---------- Idea Board ----------
        $ideaProgress = IdeaBoard::where('project_id', $projectId)->exists() ? 20 : 0;

        // ---------- Proposal ----------
        $proposalProgress = ProjectProposal::where('project_id', $projectId)->exists() ? 20 : 0;

        // ---------- Invoices ----------
        $invoiceCounts = ProjectProposalInvoice::where('project_id', $projectId)
            ->selectRaw('
            COUNT(*) as total,
            SUM(payment_status = 1) as paid
        ')
            ->first();

        $invoiceProgress = ($invoiceCounts->total ?? 0) > 0
            ? round(($invoiceCounts->paid / $invoiceCounts->total) * 20)
            : 0;

        // ---------- Documents ----------
        $documentProgress = Document::where('source', 'project')
            ->where('source_id', $projectId)
            ->exists() ? 20 : 0;

        // ---------- Total ----------
        $totalProgress =
            $taskProgress +
            $timeBillingProgress +
            $ideaProgress +
            $proposalProgress +
            $invoiceProgress +
            $documentProgress;

        return min($totalProgress, 100);
    }

}


if (!function_exists('createTaskActivityLog')) {
    function createTaskActivityLog($taskId, $description)
    {
        $log = new TaskActivityLog();
        $log->task_id = $taskId;
        $log->user_id = Auth::id();
        $log->description = $description;
        $log->save();
    }
}

if (!function_exists('secondsToHMS')) {
    function secondsToHMS($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}

if (!function_exists('getUserTimeBill')) {
    function getUserTimeBill($timeBill)
    {
        if ($timeBill->user->supervisor_id != null) {
            $userID = $timeBill->user->supervisor_id;
        } else {
            $userID = $timeBill->user_id;
        }
        return $userID;
    }
}
if (!function_exists('sectionContent')) {
    function sectionContent()
    {
        $data = [
            "hero" => [
                "label" => null,
                "description" => null,
                "display_control" => 1,
            ],
            "category" => [
                "label" => "Browse The Category",
                "description" => null,
                "display_control" => 1,
            ],
            "product" => [
                "label" => "Our Products",
                "description" => null,
                "display_control" => 1,
            ],
            "inspiration" => [
                "label" => "Beautiful rooms inspiration",
                "description" => "Our designer already made a lot of beautiful prototype of rooms that inspire you",
                "display_control" => 1,
            ],
            "portfolio" => [
                "label" => "Portfolio",
                "description" => "Innovative Designs for Stylish Living Spaces",
                "display_control" => 1,
            ],
            "gallery" => [
                "label" => "Gallery",
                "description" => null,
                "display_control" => 1,
            ],

        ];

        return $data;
    }
}

if (!function_exists('setDefaultAdminColorTheme')) {
    function setDefaultAdminColorTheme($userId)
    {
        try {
            $palette = ColorPalette::where('active_status', 1)->where('id', 1)->first();
            $colorTheme = new ColorTheme();
            $colorTheme->name = 'Default Theme';
            $colorTheme->type = 1;
            $colorTheme->primary = $palette->primary;
            $colorTheme->secondary = $palette->secondary;
            $colorTheme->bg_primary = $palette->bg_primary;
            $colorTheme->bg_secondary = $palette->bg_secondary;
            $colorTheme->text_primary = $palette->text_primary;
            $colorTheme->text_secondary = $palette->text_secondary;
            $colorTheme->theme_status = 1;
            $colorTheme->active_status = 1;
            $colorTheme->user_id = $userId;
            $colorTheme->created_by = $userId;
            $colorTheme->save();
            // try {
            //     Process::run('npm run build');
            // } catch (\Throwable $th) {
            //     Log::error($th);
            // }
        } catch (\Throwable $th) {
            Toastr::error('Default color theme not set! Please contact with admin.');
        }
    }
}

if (!function_exists('setDefaultShopColorTheme')) {
    function setDefaultShopColorTheme($userId)
    {
        try {
            $id = $userId == 1 ? 2 : 3;
            $palette = ColorPalette::where('active_status', 1)->where('id', $id)->first();
            $colorTheme = new ColorTheme();
            $colorTheme->name = 'Default Theme';
            $colorTheme->type = 0;
            $colorTheme->primary = $palette->primary;
            $colorTheme->secondary = $palette->secondary;
            $colorTheme->bg_primary = $palette->bg_primary;
            $colorTheme->bg_secondary = $palette->bg_secondary;
            $colorTheme->text_primary = $palette->text_primary;
            $colorTheme->text_secondary = $palette->text_secondary;
            $colorTheme->theme_status = 1;
            $colorTheme->active_status = 1;
            $colorTheme->user_id = $userId;
            $colorTheme->created_by = $userId;
            $colorTheme->save();
        } catch (\Throwable $th) {
            Toastr::error('Default color theme not set! Please contact with admin.');
        }
    }
}
