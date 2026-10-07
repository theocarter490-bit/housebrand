<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\BlogCategory;
use App\Models\DesignerContact;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use App\Models\BackgroundSetting;
use Database\Seeders\LanguageSeeder;
use Illuminate\Support\Facades\Cache;
use Database\Seeders\FileSystemSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\CountryIconSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            GeneralSettingSeeder::class,
            TimeZoneSeeder::class,
            CurrenciesSeeder::class,
            DateFormatSeeder::class,
            EmailSettingSeeder::class,
            // BrandSeeder::class,
            // CategorySeeder::class,
            // AttributeSeeder::class,
            // AttributeValueSeeder::class,
            // UnitSeeder::class,
            ShopSettingSeeder::class,
            // ProductSeeder::class,
            FooterWidgetSeeder::class,
            // BlogCategorySeeder::class,
            // BlogPostSeeder::class,
            // FaqSeeder::class,
            // SubscriberSeeder::class,
            // ContactUsSeeder::class,
            BackgroundSettingsSeeder::class,
            LanguageSeeder::class,
            // EmailCampainSeeder::class,
            OrderClaimIssueTypeSeeder::class,
            // OrderClaimSeeder::class,
            // OrderClaimReplySeeder::class,
            GlobalSettingSeeder::class,
            // DesignerContactSeeder::class,
            PaymentMethodSeeder::class,
            FileSystemSeeder::class,
            ExpenseTypeSeeder::class,
            // ExpenseSeeder::class,
            NoticeTypeSeeder::class,
            TaskStatusSeeder::class,
            // NoticeBoardSeeder::class,
        ]);
        Cache::flush();
    }
}
