@extends('layouts.master')
@section('title', $title ?? __('Permission'))
@section('content')
    <div class="row">
        {!! breadcrumb('Permission', ['role/index' => 'Role', '#' => 'Permission', 'role' => 'Permissions']) !!}

        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-end gap-2 align-items-center">
                    <h5 class="mb-0 col-md-6 text-mute pl-0">{{_trans('keyword.Assign Permission to')}}
                        <strong>{{ $role->name }}</strong></h5>
                    <button type="button" class="btn btn-secondary" id="setBasicBtn">
                        Set Basic Permission
                    </button>
                    <button type="button" class="btn btn-secondary" id="setDefaultBtn">
                        Set Default Permissions
                    </button>
                </div>
                <div class="card-body">

                    @if (@$role->id == 1)
                        <div class="alert alert-danger">
                            <strong>{{_trans('keyword.Warning')}}
                                !</strong> {{_trans("role.You can't change permission of this role")}}.
                        </div>
                    @endif
                    <form action="{{ route('role.permissionUpdate') }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ @$role->id }}">
                        <div class="table-responsive">
                            <table class="table test">
                                <thead>
                                <th>{{ __('module_name') }}</th>
                                <th>{{ __('Permissions') }}</th>
                                </thead>
                                <tbody>

                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td class="text-nowrap fw-medium">{{ $permission->attribute }}</td>
                                        <td>
                                            <div class="d-flex">
                                                @foreach ($permission->keywords as $key => $keyword)
                                                    <div class="form-check me-3 me-lg-5">
                                                        <input class="common-key" type="checkbox"
                                                               name="permissions[]" value="{{ $keyword }}"
                                                               id="{{ $keyword }}"
                                                            {{ $role->permissions && in_array($keyword, @$role->permissions) ? 'checked' : '' }} />
                                                        <label class="form-check-label" for="userManagementRead">
                                                            {{ $key }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @if (@$role->id != 1)
                                <div class="modal-footer">
                                    <button class="btn btn-primary mt-3" type="submit">{{_trans('keyword.Update')}}
                                        <span class="loader"></span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.common-key', function () {
            var value = $(this).val();
            var value = value.split("_");
            if (value[1] == 'read') {
                if (!$(this).is(':checked')) {
                    $(this).closest('tr').find('.common-key').prop('checked', false);
                }
            } else {
                if ($(this).is(':checked')) {
                    $(this).closest('tr').find('.common-key').first().prop('checked', true);
                }
            }
        });


        $('#setDefaultBtn').on('click', function () {
            $('.common-key').prop('checked', false);
            let defaults = ["dashboard_read","welcome_message","statistics_read","order_status_graph_read","order_payment_graph_read","upcoming_event_read","popular_product_read","recent_notice_read","dashboard_shortcut_read","order_shortcut_read","cart_shortcut_read","product_shortcut_read","notice_board_shortcut_read","product_management_read","product_read","product_create","product_update","product_status_change","product_export","assign_visitors","white_list_product_read","white_list_product_approve","white_list_product_cancel","white_list_product_status_change","category_read","category_create","category_update","product_category_status_change","brand_read","brand_create","brand_update","brand_status_change","attribute_read","attribute_create","attribute_update","attribute_status_change","attribute_value_create","attribute_value_update","unit_read","unit_create","unit_update","unit_status_change","bulk_import_read","bulk_export_read","order_management_read","customer_order_read","customer_order_create","customer_order_update","customer_cart_list_read","customer_cart_list_details","customer_cart_list_update","customer_order_list_read","customer_order_list_edit","customer_order_list_details","customer_order_list_read_invoice","customer_order_list_print_invoice","customer_order_list_download_invoice","customer_order_list_edit","customer_order_list_add_payment","customer_order_list_send_invoice","customer_order_list_update","customer_order_list_cancel","customer_order_list_claim","customer_order_claim_read","customer_order_claim_details","customer_order_claim_status_change","customer_order_claim_reply","customer_order_product_request_read","customer_order_product_request_approve","customer_order_product_request_cancel","customer_order_product_request_add_to_cart","my_order_read","my_order_cart_list_read","my_order_cart_list_details","my_order_cart_list_update","my_order_list_read","my_order_list_details","my_order_list_update","make_payment","project_management_read","projects_read","add_project","update_projects","project_overview","tasks_read","create_task","update_task","time_billing_read","read_time_breakdown","change_bill_type","time_billing_invoice_read","time_billing_invoice_print","time_billing_invoice_download","time_billing_send_invoice","essentials_read","essentials_category_read","create_essentials_category","update_essentials_category","change_status_essentials_category","project_status_read","task_status_read","create_task_status","update_task_status","change_status_task_status","task_label_read","create_task_label","update_task_label","change_status_task_label","project_service_read","services_read","create_services","update_services","change_status_services","assign_user_service_setup","service_category_read","create_service_category","update_service_category","change_status_service_category","event_management_read","event_type_read","event_type_create","event_type_update","event_type_change_status","event_read","event_create","event_update","event_change_status","assign_user_read","event_calender_read","appointment_read","appointment_create","appointment_overview","appointment_assign","marketing_read","email_campaign_read","add_email_campaign","email_campaign_update","email_campaign_assign_user","email_campaign_status_change","notice_management_read","notice_type_read","notice_type_create","notice_type_update","notice_type_change_status","notice_read","notice_create","notice_update","notice_change_status","assign_user_read","notice_board_read","expense_management_read","expense_type_read","expense_type_create","expense_type_update","expense_type_change_status","expense_read","expense_create","expense_update","expense_change_status","document_management_read","document_management_create","document_management_update","employee_management_read","employees_read","employees_create","employees_profile","user_status_change","user_management_read","customers_read","customers_profile","user_status_change","reports_read","order_report_read","order_report_export","expense_report_read","expense_report_export","product_report_read","product_report_export","contact_request_reply","designer_contact_read","designer_contact_reply","live_chat_read","seo_content_read","seo_product_list_read","seo_product_list_content_update","blog_read","blog_category_read","blog_category_create","blog_category_update","blog_category_status_change","blog_post_read","blog_post_create","blog_post_update","blog_post_details","blog_post_status_change","blog_post_publish_status_change","reviews_read","shop_reviews_read","shop_reviews_change_status","product_reviews_read","product_reviews_change_status","frontend_cms_read","portfolio_and_inspiration_read","section_category_read","section_category_create","section_category_update","section_category_status_change","portfolio_and_inspiration_read","portfolio_and_inspiration_create","portfolio_and_inspiration_update","portfolio_inspiration_status_change","read_portfolio_inspiration_description","add_portfolio_inspiration_section","gallery_read","gallery_create","gallery_update","read_images","create_image","gallery_status_change","footer_widget_read","footer_widget_update","footer_widget_status_change","pages_read","pages_update","pages_status_change","faqs_read","faqs_create","faqs_update","faqs_status_change","slider_read","slider_create","slider_update","slider_status_change","system_settings_read","shop_settings_read","site_info_read","site_info_update","site_logo_read","site_logo_update","social_links_logo_read","social_links_update","terms_and_policies_read","terms_and_policies_update","emergency_notice_read","emergency_notice_update","emergency_notice_change_status","product_setting","product_setting_change_status","role_read","role_create","role_update","give_permission","payment_method_read","payment_method_credentials_update","payment_method_publish_status_change","color_themes_read","color_themes_create","color_themes_update","color_themes_apply"]

            defaults.forEach(p => {
                $('#' + p).prop('checked', true);
            });
        });


        $('#setBasicBtn').on('click', function () {
            $('.common-key').prop('checked', false);
            let defaults = [
                // Dashboard
                "dashboard_read", "welcome_message", "statistics_read",
                "order_status_graph_read", "order_payment_graph_read",
                "upcoming_event_read", "popular_product_read",
                "recent_notice_read", "dashboard_shortcut_read",
                "order_shortcut_read", "cart_shortcut_read",
                "product_shortcut_read", "notice_board_shortcut_read",

                // Product Management
                "product_management_read", "product_read", "product_create",
                "product_update", "product_export", "assign_visitors",
                "white_list_product_read",

                // Category / Brand / Attribute
                "category_read", "brand_read", "attribute_read",
                "attribute_value_create", "unit_read",

                // Bulk Import/Export
                "bulk_import_read", "bulk_export_read",

                // Order Management
                "order_management_read",
                "customer_order_read", "customer_order_create",
                "customer_order_update",
                "customer_cart_list_read", "customer_cart_list_details",
                "customer_cart_list_update",
                "customer_order_list_read", "customer_order_list_edit",
                "customer_order_list_details",
                "customer_order_list_read_invoice",
                "customer_order_list_print_invoice",
                "customer_order_list_download_invoice",
                "customer_order_list_send_invoice",
                "customer_order_list_update",
                "customer_order_list_claim",
                "customer_order_claim_read",
                "customer_order_claim_details",
                "customer_order_claim_status_change",
                "customer_order_claim_reply",
                "customer_order_product_request_read",
                "customer_order_product_request_approve",
                "customer_order_product_request_cancel",
                "customer_order_product_request_add_to_cart",

                // My Order
                "my_order_read",
                "my_order_cart_list_read",
                "my_order_cart_list_details",
                "my_order_cart_list_update",
                "my_order_list_read",
                "my_order_list_details",
                "my_order_list_update",

                // Project Management
                "project_management_read",
                "projects_read", "update_projects", "project_overview",
                "tasks_read", "create_task", "update_task", "delete_task",

                // Time Billing
                "time_billing_read", "read_time_breakdown",
                "time_billing_invoice_read", "time_billing_invoice_print",
                "time_billing_invoice_download", "time_billing_send_invoice",

                // Essentials
                "essentials_read", "essentials_category_read",
                "project_status_read", "task_status_read",
                "task_label_read", "project_service_read",

                // Service Category
                "services_read", "assign_user_service_setup",
                "service_category_read", "create_service_category",
                "update_service_category", "event_management_read",

                // Events
                "event_type_read", "event_type_create", "event_type_update",
                "event_type_change_status",
                "event_read", "event_create", "event_update",
                "event_change_status",
                "assign_user_read",
                "event_calender_read",

                // Appointment
                "appointment_read", "appointment_overview", "appointment_assign",

                // Marketing (Email Campaign)
                "marketing_read",
                "email_campaign_read", "add_email_campaign",
                "email_campaign_update", "email_campaign_assign_user",
                "email_campaign_status_change",

                // Notice Management
                "notice_management_read",
                "notice_type_read", "notice_type_create",
                "notice_type_update", "notice_type_change_status",
                "notice_read", "notice_create", "notice_update",
                "notice_change_status",

                // Notice Board
                "notice_board_read",

                // Expense Management
                "expense_management_read",
                "expense_type_read", "expense_type_update",
                "expense_type_change_status",
                "expense_read", "expense_create",

                // Document Management
                "document_management_read", "document_management_create",

                // Employee Management
                "employee_management_read",
                "employees_read", "employees_profile",
                "user_status_change",

                // User Management
                "user_management_read",
                "customers_read", "customers_profile",

                // Reports
                "reports_read",
                "order_report_read", "order_report_export",
                "expense_report_read", "expense_report_export",
                "product_report_read", "product_report_export",

                // Inbox/Contact
                "contact_request_reply",
                "designer_contact_read", "designer_contact_reply",
                "designer_contact_delete",

                // Live Chat
                "live_chat_read",

                // SEO
                "seo_content_read",
                "seo_product_list_read",
                "seo_product_list_content_update",

                // Blog
                "blog_read",
                "blog_category_read", "blog_category_create",
                "blog_category_update", "blog_category_delete",
                "blog_category_status_change",
                "blog_post_read", "blog_post_create",
                "blog_post_update", "blog_post_delete",
                "blog_post_details",
                "blog_post_status_change",
                "blog_post_publish_status_change",

                // Reviews
                "reviews_read",
                "shop_reviews_read", "shop_reviews_change_status",
                "product_reviews_read", "product_reviews_change_status",

                // Frontend CMS
                "frontend_cms_read",

                // Portfolio & Inspiration
                "portfolio_and_inspiration_read",
                "section_category_read", "section_category_create",
                "section_category_update", "section_category_delete",
                "section_category_status_change",
                "portfolio_and_inspiration_create",
                "portfolio_and_inspiration_update",
                "portfolio_inspiration_delete",
                "portfolio_inspiration_status_change",
                "read_portfolio_inspiration_description",
                "add_portfolio_inspiration_section",

                // Gallery
                "gallery_read", "gallery_create", "gallery_update",
                "gallery_delete",
                "read_images", "create_image",
                "gallery_status_change",

                // Footer Widget
                "footer_widget_read",
                "footer_widget_update",
                "footer_widget_delete",
                "footer_widget_status_change",

                // Pages
                "pages_read", "pages_update", "pages_status_change",

                // FAQs
                "faqs_read", "faqs_create", "faqs_update",
                "faqs_delete", "faqs_status_change",

                // Slider
                "slider_read", "slider_create",
                "slider_update", "slider_delete",
                "slider_status_change",

                // Settings
                "system_settings_read",
                "shop_settings_read",
                "site_info_read",
                "site_logo_read",
                "social_links_logo_read",
                "social_links_update",
                "emergency_notice_read",
                "emergency_notice_update",
                "emergency_notice_change_status",

                // Product Setting
                "product_setting", "product_setting_change_status",

                // Role
                "role_read",

                // Payment
                "payment_method_read",

                // Theme
                "color_themes_read",
                "color_themes_create",
                "color_themes_update",
                "color_themes_delete",
                "color_themes_apply"
            ];
            defaults.forEach(p => {
                $('#' + p).prop('checked', true);
            });
        });

    </script>
@endpush
