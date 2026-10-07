<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            'dashboard' => ['read' => 'dashboard_read', 'welcome message' => 'welcome_message', 'best seller' => 'best_seller_read', 'statistics' => 'statistics_read', 'order status graph' => 'order_status_graph_read', 'order payment graph' => 'order_payment_graph_read',
                'upcoming event' => 'upcoming_event_read', 'earning reports' => 'earning_report_read', 'popular products' => 'popular_product_read', 'recent notice' => 'recent_notice_read', 'order claim graph' => 'order_claim_graph_read',
                'expense graph' => 'expense_graph_read'
            ],

            'dashboard shortcut' => ['read' => 'dashboard_shortcut_read', 'orders' => 'order_shortcut_read', 'cart' => 'cart_shortcut_read', 'users' => 'user_shortcut_read', 'products' => 'product_shortcut_read', 'dashboard statistics' => 'dashboard_statistics_read',
                'settings' => 'settings_shortcut_read', 'faqs' => 'faqs_shortcut_read', 'notice board' => 'notice_board_shortcut_read'],

            'product management' => ['read' => 'product_management_read'],
            'product' => ['read' => 'product_read', 'create' => 'product_create', 'update' => 'product_update', 'delete' => 'product_delete', 'change status' => 'product_status_change', 'export' => 'product_export', 'assign visitors' => 'assign_visitors'],
            'white list product' => ['read' => 'white_list_product_read', 'approve' => 'white_list_product_approve', 'cancel' => 'white_list_product_cancel', 'delete' => 'white_list_product_delete', 'change status' => 'white_list_product_status_change'],
            'customer_assign_designer' => ['read' => 'customer_assign_designer_read', 'approve' => 'customer_assign_designer_approve', 'cancel' => 'customer_assign_designer_cancel'],

            'category' => ['read' => 'category_read', 'create' => 'category_create', 'update' => 'category_update', 'delete' => 'category_delete', 'change status' => 'product_category_status_change'],
            'brands' => ['read' => 'brand_read', 'create' => 'brand_create', 'update' => 'brand_update', 'delete' => 'brand_delete', 'change status' => 'brand_status_change'],
            'attribute' => ['read' => 'attribute_read', 'create' => 'attribute_create', 'update' => 'attribute_update', 'delete' => 'attribute_delete', 'change status' => 'attribute_status_change', 'add value' => 'attribute_value_create',
                'update value' => 'attribute_value_update', 'delete value' => 'attribute_value_delete'],
            'unit' => ['read' => 'unit_read', 'create' => 'unit_create', 'update' => 'unit_update', 'delete' => 'unit_delete', 'change status' => 'unit_status_change'],
            'bulk import' => ['read' => 'bulk_import_read'],
            'bulk export' => ['read' => 'bulk_export_read'],
            'order management' => ['read' => 'order_management_read'],
            'customer order' => ['read' => 'customer_order_read'],
            'order create' => ['create' => 'customer_order_create', 'update' => 'customer_order_update'],
            'customer cart list' => ['read' => 'customer_cart_list_read', 'details' => 'customer_cart_list_details', 'update' => 'customer_cart_list_update', 'delete' => 'customer_cart_list_delete'],
            'customer order list' => ['read' => 'customer_order_list_read', 'edit' => 'customer_order_list_edit', 'details' => 'customer_order_list_details', 'read invoice' => 'customer_order_list_read_invoice', 'print invoice' => 'customer_order_list_print_invoice',
                'download invoice' => 'customer_order_list_download_invoice', 'edit invoice' => 'customer_order_list_edit', 'add payment' => 'customer_order_list_add_payment', 'send invoice' => 'customer_order_list_send_invoice',
                'update' => 'customer_order_list_update', 'delete' => 'customer_order_list_delete', 'cancel' => 'customer_order_list_cancel', 'claim order' => 'customer_order_list_claim',],
            'customer order claim' => ['read' => 'customer_order_claim_read', 'claim details' => 'customer_order_claim_details', 'change status' => 'customer_order_claim_status_change', 'claim replay' => 'customer_order_claim_reply'],
            'customer product request' => ['read' => 'customer_order_product_request_read', 'approve' => 'customer_order_product_request_approve', 'cancel' => 'customer_order_product_request_cancel', 'add to cart' => 'customer_order_product_request_add_to_cart',],
            'my order' => ['read' => 'my_order_read'],
            'my cart list' => ['read' => 'my_order_cart_list_read', 'details' => 'my_order_cart_list_details', 'update' => 'my_order_cart_list_update', 'delete' => 'my_order_cart_list_delete'],
            'my order list' => ['read' => 'my_order_list_read', 'details' => 'my_order_list_details', 'update' => 'my_order_list_update', 'delete' => 'my_order_list_delete'],
            'payment' => ['payment' => 'make_payment'],

            'project management' => ['read' => 'project_management_read'],
            'projects' => ['read' => 'projects_read', 'add project' => 'add_project', 'update' => 'update_projects', 'delete' => 'delete_projects', 'overview' => 'project_overview'],
            'tasks' => ['read' => 'tasks_read', 'create task' => 'create_task', 'update task' => 'update_task', 'delete task' => 'delete_task'],
            'time billing' => ['read' => 'time_billing_read', 'delete' => 'time_billing_delete', 'time breakdown' => 'read_time_breakdown', 'bill type change' => 'change_bill_type', 'read invoice' => 'time_billing_invoice_read', 'print invoice' => 'time_billing_invoice_print', 'download invoice' => 'time_billing_invoice_download', 'send invoice' => 'time_billing_send_invoice'],
            'essentials' => ['read' => 'essentials_read'],
            'essentials_category' => ['read' => 'essentials_category_read', 'create' => 'create_essentials_category', 'update' => 'update_essentials_category', 'delete' => 'delete_essentials_category', 'change status' => 'change_status_essentials_category'],
            'project status' => ['read' => 'project_status_read', 'create' => 'create_project_status', 'update' => 'update_project_status', 'delete' => 'delete_project_status', 'change status' => 'change_status_project_status'],
            'task Stage' => ['read' => 'task_status_read', 'create' => 'create_task_status', 'update' => 'update_task_status', 'delete' => 'delete_task_status', 'change status' => 'change_status_task_status'],
            'task label' => ['read' => 'task_label_read', 'create' => 'create_task_label', 'update' => 'update_task_label', 'delete' => 'delete_task_label', 'change status' => 'change_status_task_label'],
            'service' => ['read' => 'project_service_read'],
            'services' => ['read' => 'services_read', 'create' => 'create_services', 'update' => 'update_services', 'delete' => 'delete_services', 'change status' => 'change_status_services', 'assign employee' => 'assign_user_service_setup'],
            'service category' => ['read' => 'service_category_read', 'create' => 'create_service_category', 'update' => 'update_service_category', 'delete' => 'delete_service_category', 'change status' => 'change_status_service_category'],

            'event management' => ['read' => 'event_management_read'],
            'event type' => ['read' => 'event_type_read', 'create' => 'event_type_create', 'update' => 'event_type_update', 'delete' => 'event_type_delete', 'change status' => 'event_type_change_status'],
            'events' => ['read' => 'event_read', 'create' => 'event_create', 'update' => 'event_update', 'delete' => 'event_delete', 'change status' => 'event_change_status', 'assign audience' => 'assign_user_read'],
            'calender' => ['read' => 'event_calender_read'],

            'appointment scheduler' => ['read' => 'appointment_read', 'create' => 'appointment_create', 'overview' => 'appointment_overview', 'assign' => 'appointment_assign'],
            'marketing' => ['read' => 'marketing_read'],
            'email campaign' => ['read' => 'email_campaign_read', 'add campaign' => 'add_email_campaign', 'update' => 'email_campaign_update', 'delete' => 'email_campaign_delete', 'assign user' => 'email_campaign_assign_user', 'change status' => 'email_campaign_status_change'],

            'notice management' => ['read' => 'notice_management_read'],
            'notice type' => ['read' => 'notice_type_read', 'create' => 'notice_type_create', 'update' => 'notice_type_update', 'delete' => 'notice_type_delete', 'change status' => 'notice_type_change_status'],
            'notice' => ['read' => 'notice_read', 'create' => 'notice_create', 'update' => 'notice_update', 'delete' => 'notice_delete', 'change status' => 'notice_change_status', 'assign receiver' => 'assign_user_read'],
            'notice board' => ['read' => 'notice_board_read'],

            'expense management' => ['read' => 'expense_management_read'],
            'expense type' => ['read' => 'expense_type_read', 'create' => 'expense_type_create', 'update' => 'expense_type_update', 'delete' => 'expense_type_delete', 'change status' => 'expense_type_change_status'],
            'expense' => ['read' => 'expense_read', 'create' => 'expense_create', 'update' => 'expense_update', 'delete' => 'expense_delete', 'change status' => 'expense_change_status'],

            'document management' => ['read' => 'document_management_read', 'create' => 'document_management_create', 'update' => 'document_management_update', 'delete' => 'document_management_delete'],
            'employee management' => ['read' => 'employee_management_read'],
            'employees' => ['read' => 'employees_read', 'create' => 'employees_create', 'delete' => 'employees_delete', 'profile' => 'employees_profile', 'change status' => 'user_status_change'],

            'user management' => ['read' => 'user_management_read'],
            'customers management' => ['read' => 'customers_read', 'delete' => 'customers_delete', 'profile' => 'customers_profile', 'change status' => 'user_status_change'],
            'manufacturer management' => ['read' => 'manufacturer_read', 'delete' => 'manufacturer_delete', 'profile' => 'manufacturer_profile', 'change status' => 'user_status_change'],
            'designer management' => ['read' => 'designer_read', 'delete' => 'designer_delete', 'profile' => 'designer_profile', 'change status' => 'user_status_change'],

            'plan & subscription' => ['read' => 'plan_subscription_read'],
            'plan' => ['read' => 'plan_read', 'create' => 'plan_create', 'update' => 'plan_update', 'change status' => 'plan_status_change', 'make popular' => 'plan_make_popular'],
            'subscription' => ['read' => 'subscription_read', 'details' => 'subscription_details'],
            'Cancel Request' => ['read' => 'subscription_calcel_request_read'],
            'Cancel List' => ['read' => 'subscription_calcel_list_read', 'show details' => 'subscription_calcel_list_details', 'approve' => 'subscription_calcel_list_approve', 'cancel' => 'subscription_calcel_list_cancel'],


            'reports' => ['read' => 'reports_read'],
            'order report' => ['read' => 'order_report_read', 'export' => 'order_report_export'],
            'expense report' => ['read' => 'expense_report_read', 'export' => 'expense_report_export'],
            'product report' => ['read' => 'product_report_read', 'export' => 'product_report_export'],
            'user report' => ['read' => 'user_report_read', 'export' => 'user_report_export'],
            'subscription plan report' => ['read' => 'subscription_plan_report_read', 'export' => 'subscription_plan_report_export'],
            'white list product report' => ['read' => 'white_list_product_report_read', 'export' => 'white_list_product_report_export'],
            'search keyword report' => ['read' => 'search_keyword_report_read'],
            'contact request' => ['read' => 'contact_request_read', 'change status' => 'contact_request_status_change', 'reply' => 'contact_request_reply', 'delete' => 'contact_request_delete'],
            'designer contact' => ['read' => 'designer_contact_read', 'reply' => 'designer_contact_reply', 'delete' => 'designer_contact_delete'],
            'newsletter' => ['read' => 'newsletter_read', 'change status' => 'newsletter_status_change', 'reply' => 'newsletter_reply', 'delete' => 'newsletter_delete'],
            'live chat' => ['read' => 'live_chat_read'],
            'seo content' => ['read' => 'seo_content_read'],
            'seo product list' => ['read' => 'seo_product_list_read', 'update' => 'seo_product_list_content_update'],
            'blog' => ['read' => 'blog_read'],
            'blog category' => ['read' => 'blog_category_read', 'create' => 'blog_category_create', 'update' => 'blog_category_update', 'delete' => 'blog_category_delete', 'change status' => 'blog_category_status_change'],
            'blog post' => ['read' => 'blog_post_read', 'create' => 'blog_post_create', 'update' => 'blog_post_update', 'delete' => 'blog_post_delete', 'details' => 'blog_post_details', 'change status' => 'blog_post_status_change', 'change publish status' => 'blog_post_publish_status_change'],

            'reviews' => ['read' => 'reviews_read'],
            'review type' => ['read' => 'review_type_read', 'create' => 'review_type_create', 'update' => 'review_type_update', 'delete' => 'review_type_delete', 'change status' => 'review_type_change_status'],
            'shop reviews' => ['read' => 'shop_reviews_read', 'change status' => 'shop_reviews_change_status'],
            'product reviews' => ['read' => 'product_reviews_read', 'change status' => 'product_reviews_change_status'],
            'frontend cms' => ['read' => 'frontend_cms_read'],
            'portfolio & inspiration' => ['read' => 'portfolio_and_inspiration_read'],
            'section category list' => ['read' => 'section_category_read', 'create' => 'section_category_create', 'update' => 'section_category_update', 'delete' => 'section_category_delete', 'change status' => 'section_category_status_change'],
            'portfolio inspiration' => ['read' => 'portfolio_and_inspiration_read', 'create' => 'portfolio_and_inspiration_create', 'update' => 'portfolio_and_inspiration_update', 'delete' => 'portfolio_inspiration_delete', 'change status' => 'portfolio_inspiration_status_change',
                'description' => 'read_portfolio_inspiration_description', 'create_section' => 'add_portfolio_inspiration_section'],
            'gallery' => ['read' => 'gallery_read', 'create' => 'gallery_create', 'update' => 'gallery_update', 'delete' => 'gallery_delete', 'images' => 'read_images', 'add image' => 'create_image', 'change status' => 'gallery_status_change'],
            'footer widget' => ['read' => 'footer_widget_read', 'update' => 'footer_widget_update', 'delete' => 'footer_widget_delete', 'change status' => 'footer_widget_status_change'],
            'pages' => ['read' => 'pages_read', 'update' => 'pages_update', 'change status' => 'pages_status_change'],
            'faqs' => ['read' => 'faqs_read', 'create' => 'faqs_create', 'update' => 'faqs_update', 'delete' => 'faqs_delete', 'change status' => 'faqs_status_change'],
            'slider' => ['read' => 'slider_read', 'create' => 'slider_create', 'update' => 'slider_update', 'delete' => 'slider_delete', 'change status' => 'slider_status_change'],
            'quick info' => ['read' => 'quick_info_read', 'update' => 'quick_info_update', 'change status' => 'quick_info_status_change'],

            'system settings' => ['read' => 'system_settings_read'],
            'shop settings' => ['read' => 'shop_settings_read'],
            'site info' => ['read' => 'site_info_read', 'update' => 'site_info_update'],
            'site logo' => ['read' => 'site_logo_read', 'update' => 'site_logo_update'],
            'social links' => ['read' => 'social_links_logo_read', 'update' => 'social_links_update'],
            'terms & policies' => ['read' => 'terms_and_policies_read', 'update' => 'terms_and_policies_update'],
            'emergency notice' => ['read' => 'emergency_notice_read', 'update' => 'emergency_notice_update', 'change status' => 'emergency_notice_change_status'],
            'product setting' => ['read' => 'product_setting', 'change status' => 'product_setting_change_status'],
            'global settings' => ['read' => 'global_settings_read', 'update' => 'global_settings_update'],
            'roles' => ['read' => 'role_read', 'create' => 'role_create', 'update' => 'role_update', 'delete' => 'role_delete', 'permission' => 'give_permission'],
            'payment method' => ['read' => 'payment_method_read', 'create' => 'payment_method_create', 'update' => 'payment_method_update', 'credentials update' => 'payment_method_credentials_update', 'delete' => 'payment_method_delete', 'change publish status' => 'payment_method_publish_status_change'],
            'email settings' => ['read' => 'email_settings_read', 'update' => 'email_settings_update', 'test_email' => 'send_test_email'],
            'background settings' => ['read' => 'background_settings_read', 'change status' => 'background_settings_status_change', 'create' => 'background_settings_create', 'update' => 'background_settings_update', 'delete' => 'background_settings_delete'],
            'color themes' => ['read' => 'color_themes_read', 'create' => 'color_themes_create', 'update' => 'color_themes_update', 'delete' => 'color_themes_delete', 'apply' => 'color_themes_apply'],
            'language settings' => ['read' => 'language_settings_read', 'create' => 'language_settings_create', 'update' => 'language_settings_update', 'update terms' => 'language_settings_update_terms', 'delete' => 'language_settings_delete', 'change status' => 'language_settings_status_change'],
            'activity log' => ['read' => 'activity_log_read', 'export' => 'activity_log_export'],
            'free_signup' => ['read' => 'free_signup_read', 'create' => 'free_signup_create', 'change status' => 'free_signup_status_change', 'delete' => 'free_signup_delete'],

        ];

        Permission::query()->truncate();

        foreach ($attributes as $key => $attribute) {
            $permission = new Permission();
            $permission->attribute = $key;
            $permission->keywords = $attribute;
            $permission->save();
        }

        $roles = Role::whereIn('id', [2, 3, 5])->get();
        $roles->each(function ($role) {
            $role->permissions = [];
            $role->save();
        });

        foreach ($roles as $key => $role) {
            if ($role->id == 2) {
                $role->permissions = ["dashboard_read", "product_management_read", "product_read", "product_create", "product_update", "product_delete", "product_status_change", "product_export", "category_read", "category_create", "category_update", "category_delete", "brand_read", "brand_create", "brand_update", "brand_delete", "attribute_read", "attribute_create", "attribute_update", "attribute_delete", "attribute_value_create", "attribute_value_update", "attribute_value_delete", "manufacturer_read", "manufacturer_create", "manufacturer_update", "manufacturer_delete", "unit_read", "unit_create", "unit_update", "unit_delete", "bulk_import_read", "bulk_export_read", "order_management_read", "customer_order_read", "customer_cart_list_read", "customer_cart_list_details", "customer_cart_list_update", "customer_cart_list_delete", "customer_order_list_read", "customer_order_list_edit", "customer_order_list_details", "customer_order_list_read_invoice", "customer_order_list_update", "customer_order_list_delete", "customer_order_list_cancel", "customer_order_list_claim", "customer_order_claim_read", "customer_order_claim_details", "customer_order_claim_status_change", "customer_order_claim_reply", "customer_order_product_request_read", "customer_order_product_request_approve", "customer_order_product_request_cancel", "my_order_read", "my_order_cart_list_read", "my_order_cart_list_details", "my_order_cart_list_update", "my_order_cart_list_delete", "my_order_list_read", "my_order_list_details", "my_order_list_update", "my_order_list_delete", "make_payment", "portfolio_and_inspiration_read", "section_category_read", "section_category_create", "section_category_update", "section_category_delete", "portfolio_and_inspiration_read", "portfolio_and_inspiration_create", "portfolio_and_inspiration_update", "portfolio_inspiration_delete", "read_portfolio_inspiration_description", "add_portfolio_inspiration_section", "gallery_read", "gallery_create", "gallery_update", "gallery_delete", "read_images", "create_image", "blog_read", "blog_category_read", "blog_category_create", "blog_category_update", "blog_category_delete", "blog_category_status_change", "blog_post_read", "blog_post_create", "blog_post_update", "blog_post_delete", "blog_post_details", "blog_post_status_change", "user_read", "user_create", "user_update", "user_delete", "user_management_read", "customers_read", "customers_delete", "customers_profile", "manufacturer_read", "manufacturer_delete", "manufacturer_profile", "designer_read", "designer_delete", "designer_profile", "employees_read", "employees_create", "employees_delete", "employees_profile", "subscribers_read", "subscribers_status_change", "subscribers_reply", "subscriber_delete", "designer_contact_read", "designer_contact_reply", "designer_contact_delete", "marketing_read", "email_campaign_read", "add_email_campaign", "email_campaign_update", "email_campaign_delete", "email_campaign_assign_user", "email_campaign_status_change", "contact_request_read", "contact_request_status_change", "contact_request_reply", "contact_request_delete", "frontend_cms_read", "footer_widget_read", "footer_widget_update", "footer_widget_delete", "pages_read", "pages_update", "faqs_read", "faqs_create", "faqs_update", "faqs_delete", "system_settings_read", "general_settings_read", "general_settings_update", "file_system_read", "file_system_update", "shop_settings_read", "site_info_read", "site_info_update", "site_logo_read", "site_logo_read", "social_links_logo_read", "social_links_update", "terms_and_policies_read", "terms_and_policies_update", "global_settings_read", "global_settings_update", "payment_method_read", "payment_method_create", "payment_method_update", "payment_method_delete", "payment_method_status_change", "email_settings_read", "email_settings_update", "send_test_email", "email_settings_create", "background_settings_read", "background_settings_status_change", "background_settings_create", "background_settings_update", "background_settings_delete", "color_themes_read", "color_themes_create", "color_themes_update", "color_themes_delete", "color_themes_apply", "language_settings_read", "language_settings_create", "language_settings_update", "language_settings_update_terms", "language_settings_delete", "language_settings_status_change", "storage_settings_read", "storage_settings_update", "slider_read", "slider_update", "slider_delete", "slider_create", "slider_status_change", "notice_management_read", "notice_board_read", "customer_order_product_request_add_to_cart"];
            } elseif ($role->id == 3) {
                $role->permissions = ["dashboard_read", "welcome_message", "statistics_read", "order_status_graph_read", "order_payment_graph_read", "upcoming_event_read", "popular_product_read", "recent_notice_read", "dashboard_shortcut_read", "order_shortcut_read", "cart_shortcut_read", "product_shortcut_read", "notice_board_shortcut_read", "product_management_read", "product_read", "product_create", "product_update", "product_delete", "product_status_change", "product_export", "assign_visitors", "white_list_product_read", "white_list_product_approve", "white_list_product_cancel", "white_list_product_delete", "white_list_product_status_change", "category_read", "category_create", "category_update", "category_delete", "product_category_status_change", "brand_read", "brand_create", "brand_update", "brand_delete", "brand_status_change", "attribute_read", "attribute_create", "attribute_update", "attribute_delete", "attribute_status_change", "attribute_value_create", "attribute_value_update", "attribute_value_delete", "unit_read", "unit_create", "unit_update", "unit_delete", "unit_status_change", "bulk_import_read", "bulk_export_read", "order_management_read", "customer_order_read", "customer_order_create", "customer_order_update", "customer_cart_list_read", "customer_cart_list_details", "customer_cart_list_update", "customer_cart_list_delete", "customer_order_list_read", "customer_order_list_edit", "customer_order_list_details", "customer_order_list_read_invoice", "customer_order_list_print_invoice", "customer_order_list_download_invoice", "customer_order_list_edit", "customer_order_list_add_payment", "customer_order_list_send_invoice", "customer_order_list_update", "customer_order_list_delete", "customer_order_list_cancel", "customer_order_list_claim", "customer_order_claim_read", "customer_order_claim_details", "customer_order_claim_status_change", "customer_order_claim_reply", "customer_order_product_request_read", "customer_order_product_request_approve", "customer_order_product_request_cancel", "customer_order_product_request_add_to_cart", "my_order_read", "my_order_cart_list_read", "my_order_cart_list_details", "my_order_cart_list_update", "my_order_cart_list_delete", "my_order_list_read", "my_order_list_details", "my_order_list_update", "my_order_list_delete", "make_payment", "project_management_read", "projects_read", "add_project", "update_projects", "delete_projects", "project_overview", "tasks_read", "create_task", "update_task", "delete_task", "time_billing_read", "time_billing_delete", "read_time_breakdown", "change_bill_type", "time_billing_invoice_read", "time_billing_invoice_print", "time_billing_invoice_download", "time_billing_send_invoice", "essentials_read", "essentials_category_read", "create_essentials_category", "update_essentials_category", "delete_essentials_category", "change_status_essentials_category", "project_status_read", "task_status_read", "create_task_status", "update_task_status", "delete_task_status", "change_status_task_status", "task_label_read", "create_task_label", "update_task_label", "delete_task_label", "change_status_task_label", "project_service_read", "services_read", "create_services", "update_services", "delete_services", "change_status_services", "assign_user_service_setup", "service_category_read", "create_service_category", "update_service_category", "delete_service_category", "change_status_service_category", "event_management_read", "event_type_read", "event_type_create", "event_type_update", "event_type_delete", "event_type_change_status", "event_read", "event_create", "event_update", "event_delete", "event_change_status", "assign_user_read", "event_calender_read", "appointment_read", "appointment_create", "appointment_overview", "appointment_assign", "marketing_read", "email_campaign_read", "add_email_campaign", "email_campaign_update", "email_campaign_delete", "email_campaign_assign_user", "email_campaign_status_change", "notice_management_read", "notice_type_read", "notice_type_create", "notice_type_update", "notice_type_delete", "notice_type_change_status", "notice_read", "notice_create", "notice_update", "notice_delete", "notice_change_status", "assign_user_read", "notice_board_read", "expense_management_read", "expense_type_read", "expense_type_create", "expense_type_update", "expense_type_delete", "expense_type_change_status", "expense_read", "expense_create", "expense_update", "expense_delete", "expense_change_status", "document_management_read", "document_management_create", "document_management_update", "document_management_delete", "employee_management_read", "employees_read", "employees_create", "employees_delete", "employees_profile", "user_status_change", "user_management_read", "customers_read", "customers_delete", "customers_profile", "user_status_change", "user_status_change", "user_status_change", "reports_read", "order_report_read", "order_report_export", "expense_report_read", "expense_report_export", "product_report_read", "product_report_export", "contact_request_reply", "designer_contact_read", "designer_contact_reply", "designer_contact_delete", "live_chat_read", "seo_content_read", "seo_product_list_read", "seo_product_list_content_update", "blog_read", "blog_category_read", "blog_category_create", "blog_category_update", "blog_category_delete", "blog_category_status_change", "blog_post_read", "blog_post_create", "blog_post_update", "blog_post_delete", "blog_post_details", "blog_post_status_change", "blog_post_publish_status_change", "reviews_read", "shop_reviews_read", "shop_reviews_change_status", "product_reviews_read", "product_reviews_change_status", "frontend_cms_read", "portfolio_and_inspiration_read", "section_category_read", "section_category_create", "section_category_update", "section_category_delete", "section_category_status_change", "portfolio_and_inspiration_read", "portfolio_and_inspiration_create", "portfolio_and_inspiration_update", "portfolio_inspiration_delete", "portfolio_inspiration_status_change", "read_portfolio_inspiration_description", "add_portfolio_inspiration_section", "gallery_read", "gallery_create", "gallery_update", "gallery_delete", "read_images", "create_image", "gallery_status_change", "footer_widget_read", "footer_widget_update", "footer_widget_delete", "footer_widget_status_change", "pages_read", "pages_update", "pages_status_change", "faqs_read", "faqs_create", "faqs_update", "faqs_delete", "faqs_status_change", "slider_read", "slider_create", "slider_update", "slider_delete", "slider_status_change", "system_settings_read", "shop_settings_read", "site_info_read", "site_info_update", "site_logo_read", "site_logo_update", "social_links_logo_read", "social_links_update", "terms_and_policies_read", "terms_and_policies_update", "emergency_notice_read", "emergency_notice_update", "emergency_notice_change_status", "product_setting", "product_setting_change_status", "role_read", "role_create", "role_update", "role_delete", "give_permission", "payment_method_read", "payment_method_credentials_update", "payment_method_publish_status_change", "color_themes_read", "color_themes_create", "color_themes_update", "color_themes_delete", "color_themes_apply"];
            } elseif ($role->id == 5) {
                $role->permissions = ["dashboard_read", "welcome_message", "order_status_graph_read", "order_payment_graph_read", "upcoming_event_read", "popular_product_read", "recent_notice_read", "dashboard_shortcut_read", "order_shortcut_read", "cart_shortcut_read", "product_shortcut_read", "settings_shortcut_read", "notice_board_shortcut_read", "product_management_read", "product_read", "product_create", "product_update", "product_delete", "product_status_change", "product_export", "white_list_product_read", "white_list_product_approve", "white_list_product_cancel", "white_list_product_delete", "white_list_product_status_change", "essentials_category_read", "create_essentials_category", "update_essentials_category", "delete_essentials_category", "change_status_essentials_category", "brand_read", "brand_create", "brand_update", "brand_delete", "brand_status_change", "attribute_read", "attribute_create", "attribute_update", "attribute_delete", "attribute_status_change", "attribute_value_create", "attribute_value_update", "attribute_value_delete", "unit_read", "unit_create", "unit_update", "unit_delete", "unit_status_change", "bulk_import_read", "bulk_export_read", "order_management_read", "customer_order_read", "customer_order_create", "customer_order_update", "customer_cart_list_read", "customer_cart_list_details", "customer_cart_list_update", "customer_cart_list_delete", "customer_order_list_read", "customer_order_list_edit", "customer_order_list_details", "customer_order_list_read_invoice", "customer_order_list_print_invoice", "customer_order_list_download_invoice", "customer_order_list_edit", "customer_order_list_add_payment", "customer_order_list_send_invoice", "customer_order_list_update", "customer_order_list_delete", "customer_order_list_cancel", "customer_order_list_claim", "customer_order_claim_read", "customer_order_claim_details", "customer_order_claim_status_change", "event_management_read", "event_type_read", "event_type_create", "event_type_update", "event_type_delete", "event_type_change_status", "event_read", "event_create", "event_update", "event_delete", "event_change_status", "assign_user_read", "event_calender_read", "marketing_read", "email_campaign_read", "add_email_campaign", "email_campaign_update", "email_campaign_delete", "email_campaign_assign_user", "email_campaign_status_change", "notice_management_read", "notice_type_read", "notice_type_create", "notice_type_update", "notice_type_delete", "notice_type_change_status", "notice_read", "notice_create", "notice_update", "notice_delete", "notice_change_status", "assign_user_read", "notice_board_read", "expense_management_read", "expense_type_read", "expense_read", "expense_create", "expense_update", "expense_delete", "expense_change_status", "document_management_read", "document_management_create", "document_management_update", "document_management_delete", "employee_management_read", "employees_read", "employees_create", "employees_delete", "employees_profile", "user_status_change", "user_status_change", "user_status_change", "user_status_change", "reports_read", "order_report_read", "order_report_export", "expense_report_read", "expense_report_export", "product_report_read", "product_report_export", "live_chat_read", "seo_content_read", "seo_product_list_read", "seo_product_list_content_update", "blog_read", "blog_category_read", "blog_category_create", "blog_category_update", "blog_category_delete", "blog_category_status_change", "blog_post_read", "blog_post_create", "blog_post_update", "blog_post_delete", "blog_post_details", "blog_post_status_change", "blog_post_publish_status_change", "reviews_read", "product_reviews_read", "product_reviews_change_status", "system_settings_read", "shop_settings_read", "site_info_read", "site_info_update", "site_logo_read", "social_links_logo_read", "social_links_update", "terms_and_policies_read", "terms_and_policies_update", "role_read", "role_create", "role_update", "role_delete", "give_permission", "payment_method_read", "payment_method_create", "payment_method_update", "payment_method_credentials_update", "payment_method_delete", "payment_method_publish_status_change"];
            }
            $role->save();
        }
    }
}
