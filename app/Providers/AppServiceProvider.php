<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use App\Services\MailService;
use Faker\Core\Color;
use App\Models\Language;
use App\Models\ColorTheme;
use App\Models\ShopSetting;
use App\Models\GeneralSetting;
use App\Models\BackgroundSetting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('mail-service', function ($app) {
            return new MailService();
        });
        $this->app->singleton('general_Setting', function () {
            return getDataFromRedis('general_Setting', GeneralSetting::with('currency', 'timezone', 'DateFormat')->first());
        });

        $this->app->singleton('color_theme', function () {
            $theme = ColorTheme::where('type', 1)->where('user_id', Auth::user()->id)->where('active_status', 1)->first();
            if ($theme) {
                return $theme;
            }
            return getDataFromRedis('color_theme', ColorTheme::where('type', 1)->where('active_status', 1)->first());
        });

        $this->app->singleton('active_languages', function () {
            return getDataFromRedis('active_languages', Language::where('active_status', 1)->get());
        });

        $this->app->singleton('login_bg', function () {
            return getDataFromRedis('login_bg', BackgroundSetting::where('purpose', 2)->where('is_active', 1)->first());
        });

        $this->app->singleton('shop_Setting', function () {
            $id = Role::SUPER_ADMIN;
            if (Auth::user() && isSeller()) {
                $id = getUserId();
            }
            return getDataFromRedis("shop_Setting_$id", ShopSetting::where('user_id', $id)->with('seller')->first());
        });

        $this->app->singleton('administrator_setting', function () {
            $id = Role::SUPER_ADMIN;
            return getDataFromRedis("shop_Setting_$id", ShopSetting::where('user_id', $id)->first());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return env('APP_FRONTEND_URL') . '/auth/reset-password?token=' . $token . '&email=' . $user->email;
        });

        if (env('APP_HTTPS') == true) {
            URL::forceScheme('https');
        }

    }
}
