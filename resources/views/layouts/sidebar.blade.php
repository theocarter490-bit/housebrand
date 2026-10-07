@php use App\Models\Role; @endphp
    <!-- Menu -->

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo d-flex justify-content-between align-items-center" style="height: 85px;">
        <a href="{{ route('dashboard') }}" class="app-brand-link w-100">
        <span class="app-brand-logo demo w-100 h-100 text-center">
            <img src="{{ @getFilePath(shopSetting()->logo) }}"
                 alt="Brand Logo"
                 class="img-fluid"
                 style="max-height: 70px; max-width: 100%; width: auto; object-fit: contain;">
        </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti ti-menu-2 d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    {{--    <div class="menu-inner-shadow"></div> --}}

    <ul class="menu-inner py-1 mt-2">
        <!-- Dashboards -->
        @if (hasPermission('dashboard_read'))
            <li class="menu-item {{ activeMenu('dashboard') }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-smart-home"></i>
                    <div data-i18n="{{ _trans('keyword.Dashboard') }}">{{ _trans('keyword.Dashboard') }}</div>
                </a>
            </li>
        @endif

        @if (hasPermission('product_management_read'))
            <li
                class="menu-item {{ openMenu(['category', 'brand', 'attribute', 'product', 'sharedProduct', 'vendor', 'unit', 'bulkImport', 'bulkExport', 'productClipper']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-color-swatch"></i>
                    <div data-i18n="{{ _trans('keyword.Product') }} {{ _trans('keyword.Management') }}">
                        {{ _trans('keyword.Product') }} {{ _trans('keyword.Management') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (hasPermission('product_read'))
                        <li class="menu-item {{ activeMenu('product.index') }}">
                            <a href="{{ route('product.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Products') }}">{{ _trans('keyword.Products') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('white_list_product_read'))
                        <li class="menu-item {{ activeMenu('sharedProduct') }}">
                            <a href="{{ route('sharedProduct.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.White Label Product') }}">
                                    {{ _trans('keyword.White Label Product') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('product_read'))
                        <li class="menu-item {{ activeMenu('productClipper.index') }}">
                            <a href="{{ route('productClipper.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Clipper Products') }}">{{ _trans('keyword.Clipper Products') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('category_read'))
                        <li class="menu-item {{ activeMenu('category') }}">
                            <a href="{{ route('category.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Categories') }}">{{ _trans('keyword.Categories') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('brand_read'))
                        <li class="menu-item {{ activeMenu('brand') }}">
                            <a href="{{ route('brand.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Brands') }}">{{ _trans('keyword.Brands') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('attribute_read'))
                        <li class="menu-item {{ activeMenu('attribute') }}">
                            <a href="{{ route('attribute.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Attribute') }}">{{ _trans('keyword.Attribute') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('unit_read'))
                        <li class="menu-item {{ activeMenu('unit') }}">
                            <a href="{{ route('unit.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Unit') }}">{{ _trans('keyword.Unit') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('bulk_import_read'))
                        <li class="menu-item {{ activeMenu('bulkImport') }}">
                            <a href="{{ route('bulkImport.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Bulk') }} {{ _trans('keyword.Import') }}">
                                    {{ _trans('keyword.Bulk') }} {{ _trans('keyword.Import') }}</div>
                            </a>
                        </li>
                    @endif


                    @if (hasPermission('bulk_export_read'))
                        <li class="menu-item {{ activeMenu('bulkExport.index') }}">
                            <a href="{{ route('bulkExport.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Bulk') }} {{ _trans('keyword.Export') }}">
                                    {{ _trans('keyword.Bulk') }} {{ _trans('keyword.Export') }}</div>
                            </a>
                        </li>
                    @endif


                </ul>
            </li>
        @endif

        @if (hasPermission('order_management_read') && hasModulePermission('ecommerce-support'))
            <li class="menu-item {{ openMenu(['order', 'myOrder', 'order-claim', 'cart']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-truck"></i>
                    <div data-i18n="{{ _trans('keyword.Order') }} {{ _trans('keyword.Management') }}">
                        {{ _trans('keyword.Order') }} {{ _trans('keyword.Management') }}</div>
                </a>

                <ul class="menu-sub">

                    <li class="menu-item {{ openMenu(['order', 'order-claim', 'cart']) }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">

                            <div data-i18n="{{ _trans('keyword.Customer') . ' ' . _trans('keyword.Order') }}">
                                {{ _trans('keyword.Customer') . ' ' . _trans('keyword.Order') }}</div>
                        </a>
                        <ul class="menu-sub">
                            @if (hasPermission('customer_cart_list_read'))
                                <li class="menu-item  {{ activeMenu('cart.*') }}">
                                    <a href="{{ route('cart.index') }}" class="menu-link">
                                        <div
                                            data-i18n="{{ _trans('keyword.Cart') . ' ' . _trans('keyword.List') }}">
                                            {{ _trans('keyword.Cart') . ' ' . _trans('keyword.List') }}</div>
                                    </a>
                                </li>
                            @endif
                            @if (hasPermission('customer_order_list_details'))
                                <li class="menu-item {{ activeMenu('order') }}">
                                    <a href="{{ route('order.index') }}" class="menu-link">
                                        <div
                                            data-i18n="{{ _trans('keyword.Order') . ' ' . _trans('keyword.List') }}">
                                            {{ _trans('keyword.Order') . ' ' . _trans('keyword.List') }}</div>
                                    </a>
                                </li>
                            @endif
                            @if (hasPermission('customer_order_claim_read'))
                                <li class="menu-item {{ activeMenu('order-claim') }}">
                                    <a href="{{ route('order-claim.index') }}" class="menu-link">
                                        <div
                                            data-i18n="{{ _trans('keyword.Order') . ' ' . _trans('keyword.Claim') }}">
                                            {{ _trans('keyword.Order') . ' ' . _trans('keyword.Claim') }}</div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    @if (hasPermission('my_order_read') && (getUserId() != Role::SUPER_ADMIN))
                        <li
                            class="menu-item {{ openMenu(['myOrder', 'myOrder.cart', 'myOrder.order', 'myOrder.order-claim']) }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <div data-i18n="{{ _trans('keyword.My') . ' ' . _trans('keyword.Order') }}">
                                    {{ _trans('keyword.My') . ' ' . _trans('keyword.Order') }}</div>
                            </a>
                            <ul class="menu-sub">
                                @if (hasPermission('my_order_cart_list_read'))
                                    <li class="menu-item {{ activeMenu('myOrder.cart.*') }}">
                                        <a href="{{ route('myOrder.cart.index') }}" class="menu-link">
                                            <div
                                                data-i18n="{{ _trans('keyword.Cart') . ' ' . _trans('keyword.List') }}">
                                                {{ _trans('keyword.Cart') . ' ' . _trans('keyword.List') }}</div>
                                        </a>
                                    </li>
                                @endif
                                @if (hasPermission('my_order_list_read'))
                                    <li class="menu-item {{ activeMenu('myOrder.order.*') }}">
                                        <a href="{{ route('myOrder.order.index') }}" class="menu-link">
                                            <div
                                                data-i18n="{{ _trans('keyword.Order') . ' ' . _trans('keyword.List') }}">
                                                {{ _trans('keyword.Order') . ' ' . _trans('keyword.List') }}</div>
                                        </a>
                                    </li>
                                @endif
                                <li class="menu-item {{ activeMenu('myOrder.order-claim.*') }}">
                                    <a href="{{ route('myOrder.order-claim.index') }}" class="menu-link">
                                        <div data-i18n="{{ _trans('keyword.Order Claim') }}">
                                            {{ _trans('keyword.Order Claim') }}</div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    @endif
                </ul>
            </li>

        @endif

        @if (hasModulePermission('project-management') && hasPermission('project_management_read'))
            <li class="menu-item {{ openMenu(['project-management']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon ti ti-building-skyscraper"></i>
                    <div data-i18n="{{ _trans('keyword.Project') }} {{ _trans('keyword.Management') }}">
                        {{ _trans('keyword.Project') }} {{ _trans('keyword.Management') }}
                    </div>
                </a>

                <ul class="menu-sub">
                    @if (hasPermission('projects_read'))
                        <li class="menu-item {{ activeMenu('project-management.project') }}">
                            <a href="{{ route('project-management.project.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Projects') }}">
                                    {{ _trans('keyword.Projects') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('tasks_read'))
                        <li class="menu-item {{ activeMenu('project-management.project.task.index') }}">
                            <a href="{{ route('project-management.project.task.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Task') }}">
                                    {{ _trans('keyword.Task') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('essentials_read'))
                        <li
                            class="menu-item {{ openMenu(['project-management.project.category', 'project-management.project.status.index', 'project-management.project.task.status.index', 'project-management.project.task.label.index', 'project-management.project.service.index', 'project-management.essentials.*']) }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <div data-i18n="{{ _trans('keyword.Essentials') }}">
                                    {{ _trans('keyword.Essentials') }}
                                </div>
                            </a>
                            <ul class="menu-sub">
                                @if (hasPermission('essentials_category_read'))
                                    <li class="menu-item {{ activeMenu('project-management.project.category') }}">
                                        <a href="{{ route('project-management.project.category.index') }}"
                                           class="menu-link">
                                            <div data-i18n="{{ _trans('keyword.Category') }}">
                                                {{ _trans('keyword.Category') }}
                                            </div>
                                        </a>
                                    </li>
                                @endif


                                @if (hasPermission('project_status_read'))
                                    <li
                                        class="menu-item {{ activeMenu('project-management.essentials.status.index') }}">
                                        <a href="{{ route('project-management.essentials.status.index') }}"
                                           class="menu-link">
                                            <div data-i18n="{{ _trans('keyword.Project Status') }}">
                                                {{ _trans('keyword.Project Status') }}
                                            </div>
                                        </a>
                                    </li>
                                @endif


                                @if (hasPermission('task_status_read'))
                                    <li
                                        class="menu-item {{ activeMenu('project-management.essentials.task.status.index') }}">
                                        <a href="{{ route('project-management.essentials.task.status.index') }}"
                                           class="menu-link">
                                            <div data-i18n="{{ _trans('keyword.Task Stage') }}">
                                                {{ _trans('keyword.Task Stage') }}
                                            </div>
                                        </a>
                                    </li>
                                @endif


                                @if (hasPermission('task_label_read'))
                                    <li
                                        class="menu-item {{ activeMenu('project-management.essentials.task.label.index') }}">
                                        <a href="{{ route('project-management.essentials.task.label.index') }}"
                                           class="menu-link">
                                            <div data-i18n="{{ _trans('keyword.Tags') }}">
                                                {{ _trans('keyword.Tags') }}
                                            </div>
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    @endif

                    @if (hasPermission('project_service_read'))
                        <li class="menu-item {{ openMenu(['service', 'project-management.service']) }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <div data-i18n="{{ _trans('keyword.Service') }}">{{ _trans('keyword.Service') }}
                                </div>
                            </a>
                            <ul class="menu-sub">
                                @if (hasPermission('services_read'))
                                    <li class="menu-item {{ activeMenu('project-management.service.index') }}">
                                        <a href="{{ route('project-management.service.index') }}" class="menu-link">
                                            <div data-i18n="{{ _trans('keyword.Services') }}">
                                                {{ _trans('keyword.Services') }}</div>
                                        </a>
                                    </li>
                                @endif
                                @if (hasPermission('service_category_read'))
                                    <li
                                        class="menu-item {{ activeMenu('project-management.service.category.index') }}">
                                        <a href="{{ route('project-management.service.category.index') }}"
                                           class="menu-link">
                                            <div data-i18n="{{ _trans('keyword.Service Category') }}">
                                                {{ _trans('keyword.Service Category') }}
                                            </div>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif


                </ul>
            </li>

        @endif

        @if (hasModulePermission('event-management') && hasPermission('event_management_read'))
            <li class="menu-item {{ openMenu(['event-management']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon ti ti-calendar-event"></i>
                    <div data-i18n="{{ _trans('keyword.Event') }} {{ _trans('keyword.Management') }}">
                        {{ _trans('keyword.Event') }} {{ _trans('keyword.Management') }}</div>
                </a>
                <ul class="menu-sub">

                    @if (hasPermission('event_type_read'))
                        <li class="menu-item {{ activeMenu('event-management.type.index') }}">
                            <a href="{{ route('event-management.type.index') }}" class="menu-link">
                                <i class="menu-icon ti ti-calendar-event"></i>
                                <div data-i18n="{{ _trans('keyword.Event') . ' ' . _trans('keyword.Type') }}">
                                    {{ _trans('keyword.Event') . ' ' . _trans('keyword.Type') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('event_read'))
                        <li class="menu-item {{ activeMenu('event-management.event.index') }}">
                            <a href="{{ route('event-management.event.index') }}" class="menu-link">
                                <i class="menu-icon ti ti-calendar-event"></i>
                                <div data-i18n="{{ _trans('keyword.Events') }}">{{ _trans('keyword.Events') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('event_calender_read'))
                        <li class="menu-item {{ activeMenu('event-management.event.calender') }}">
                            <a href="{{ route('event-management.event.calender') }}" class="menu-link">
                                <i class="menu-icon ti ti-calendar-event"></i>
                                <div data-i18n="{{ _trans('keyword.Calendar') }}">{{ _trans('keyword.Calendar') }}
                                </div>
                            </a>
                        </li>
                    @endif

                </ul>
            </li>
        @endif

        @if (hasPermission('appointment_read'))
            <li class="menu-item {{ openMenu(['appointment-scheduler']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon ti ti-calendar-event"></i>
                    <div data-i18n="{{ _trans('keyword.Appointment') }} {{ _trans('keyword.Scheduler') }}">
                        {{ _trans('keyword.Appointment') }} {{ _trans('keyword.Scheduler') }}</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ activeMenu('appointment-scheduler.appointments') }}">
                        <a href="{{ route('appointment-scheduler.appointments') }}" class="menu-link">
                            <i class="menu-icon ti ti-calendar-event"></i>
                            <div data-i18n="{{ _trans('keyword.Appointments') }}">
                                {{ _trans('keyword.Appointments') }}
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        @if (hasModulePermission('marketing') && hasPermission('marketing_read'))
            <li class="menu-item {{ openMenu(['marketing']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-ad-2"></i>
                    <div data-i18n="{{ _trans('keyword.Marketing') }}">{{ _trans('keyword.Marketing') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (hasPermission('email_campaign_read'))
                        <li class="menu-item {{ activeMenu('marketing.campaign.index') }}">
                            <a href="{{ route('marketing.campaign.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Email') }} {{ _trans('keyword.Campaign') }}">
                                    {{ _trans('keyword.Email') }} {{ _trans('keyword.Campaign') }}</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

        @endif

        @if (hasModulePermission('notice-management') && hasPermission('notice_management_read'))
            <li class="menu-item {{ openMenu(['notice-board']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-bell"></i>
                    <div data-i18n="{{ _trans('keyword.Notice') . ' ' . _trans('keyword.Management') }}">
                        {{ _trans('keyword.Notice') . ' ' . _trans('keyword.Management') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (hasPermission('notice_type_read'))
                        <li class="menu-item {{ activeMenu('notice-board.type.index') }}">
                            <a href="{{ route('notice-board.type.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Notice') }} {{ _trans('keyword.Type') }}">
                                    {{ _trans('keyword.Notice') }} {{ _trans('keyword.Type') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('notice_read'))
                        <li class="menu-item {{ activeMenu('notice-board.notice.index') }}">
                            <a href="{{ route('notice-board.notice.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Notices') }}">{{ _trans('keyword.Notices') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('notice_board_read'))
                        <li class="menu-item {{ activeMenu('notice-board.notice.noticeBoard') }}">
                            <a href="{{ route('notice-board.notice.noticeBoard') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Notice Board') }}">
                                    {{ _trans('keyword.Notice Board') }}
                                </div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if (hasModulePermission('expense-management') && hasPermission('expense_management_read'))
            <li class="menu-item {{ openMenu(['expense-management']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                    <div data-i18n="{{ _trans('keyword.Expense') . ' ' . _trans('keyword.Tracker') }}">
                        {{ _trans('keyword.Expense') . ' ' . _trans('keyword.Tracker') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (hasPermission('expense_type_read'))
                        <li class="menu-item {{ activeMenu('expense-management.type.index') }}">
                            <a href="{{ route('expense-management.type.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Expense') }} {{ _trans('keyword.Type') }}">
                                    {{ _trans('keyword.Expense') }} {{ _trans('keyword.Type') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('expense_read'))
                        <li class="menu-item {{ activeMenu('expense-management.expenses.index') }}">
                            <a href="{{ route('expense-management.expenses.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Expenses') }}">{{ _trans('keyword.Expenses') }}
                                </div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if (hasPermission('document_management_read'))
            <li
                class="menu-item {{ activeMenu(route('project-management.document.index', ['source' => 'inhouse', 'source_id' => $sourceId ?? null])) }}">
                <a href="{{ route('project-management.document.index', ['source' => 'inhouse', 'source_id' => $sourceId ?? null]) }}"
                   class="menu-link">
                    <i class="menu-icon ti ti-file-check"></i>
                    <div data-i18n="{{ _trans('keyword.Document Management') }}">
                        {{ _trans('keyword.Document Management') }}</div>
                </a>
            </li>
        @endif

        @if (hasModulePermission('employee-management') && hasPermission('employee_management_read'))
            <li class="menu-item {{ openMenu(['employee']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon ti ti-users"></i>
                    <div data-i18n="{{ _trans('keyword.Employee') }} {{ _trans('keyword.Management') }}">
                        {{ _trans('keyword.Employee') }} {{ _trans('keyword.Management') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (hasPermission('employees_read'))
                        <li class="menu-item {{ activeMenu(route('employee.employeeList')) }}">
                            <a href="{{ route('employee.employeeList') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.employee') }}">{{ _trans('keyword.Employees') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    {{--                    @if (hasPermission('service_setup_read'))--}}
                    {{--                        <li class="menu-item {{ activeMenu('employee.service.index') }}">--}}
                    {{--                            <a href="{{ route('employee.service.index') }}" class="menu-link">--}}
                    {{--                                <div data-i18n="{{ _trans('keyword.Service Setup') }}">--}}
                    {{--                                    {{ _trans('keyword.Service Setup') }}--}}
                    {{--                                </div>--}}
                    {{--                            </a>--}}
                    {{--                        </li>--}}
                    {{--                    @endif--}}

                </ul>
            </li>
        @endif

        @if (hasPermission('user_management_read'))
            <li class="menu-item {{ openMenu(['user','assigned-designer']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon ti ti-users"></i>
                    <div data-i18n="{{ _trans('keyword.Customer') }} {{ _trans('keyword.Management') }}">
                        {{ _trans('keyword.Customer') }} {{ _trans('keyword.Management') }}</div>
                </a>
                <ul class="menu-sub">

                    @if (hasPermission('customers_read'))
                        <li class="menu-item {{ activeMenu(route('user.index', Role::CUSTOMER)) }}">
                            <a href="{{ route('user.index', Role::CUSTOMER) }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Customers') }}">
                                    {{ _trans('keyword.Customers') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('customer_assign_designer_read'))
                        <li class="menu-item {{ activeMenu(route('assignedDesigner.index')) }}">
                            <a href="{{ route('assignedDesigner.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Customers Join Request') }}">
                                    {{ _trans('keyword.Customers Join Request') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('manufacturer_read'))
                        <li class="menu-item {{ activeMenu(route('user.index', Role::MANUFACTURER)) }}">
                            <a href="{{ route('user.index', Role::MANUFACTURER) }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Manufacturers') }}">
                                    {{ _trans('keyword.Manufacturers') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('designer_read'))
                        <li class="menu-item {{ activeMenu(route('user.index', Role::DESIGNER)) }}">
                            <a href="{{ route('user.index', Role::DESIGNER) }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Designers') }}">
                                    {{ _trans('keyword.Designers') }}
                                </div>
                            </a>
                        </li>
                    @endif

                </ul>
            </li>

        @endif

        @if (hasPermission('plan_subscription_read'))
            <li class="menu-item {{ openMenu(['subscription','coupon']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-calendar-dollar"></i>
                    <div data-i18n="{{ _trans('keyword.Plan') }} & {{ _trans('keyword.Subscription') }}">
                        {{ _trans('keyword.Plan') }} & {{ _trans('keyword.Subscription') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (hasPermission('plan_read'))
                        <li class="menu-item {{ activeMenu('subscription.plan') }}">
                            <a href="{{ route('subscription.plan.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Plan') }}">
                                    {{ _trans('keyword.Plan') }}</div>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('subscription_read'))
                        <li class="menu-item {{ activeMenu('subscription.customer') }}">
                            <a href="{{ route('subscription.customer.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Subscription') }}">
                                    {{ _trans('keyword.Subscribers') }}</div>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('subscription_read'))
                        <li class="menu-item {{ activeMenu('subscription.free-trail') }}">
                            <a href="{{ route('subscription.free-trail') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Subscription') }}">
                                    {{ _trans('keyword.Invitation User List') }}</div>
                            </a>
                        </li>
                    @endif
                    @if (hasPermission('subscription_calcel_request_read'))
                        <li class="menu-item {{ activeMenu('subscription.cancelRequest') }}">
                            <a href="{{ route('subscription.cancelRequest.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Cancel Request') }}">
                                    {{ _trans('keyword.Cancel Request') }}</div>
                            </a>
                        </li>
                    @endif
                    {{--                        <li class="menu-item {{ activeMenu('coupon') }}">--}}
                    {{--                            <a href="{{ route('coupon.index') }}" class="menu-link">--}}
                    {{--                                <div data-i18n="{{ _trans('keyword.Coupon') }}">--}}
                    {{--                                    {{ _trans('keyword.Coupon') }}</div>--}}
                    {{--                            </a>--}}
                    {{--                        </li>--}}
                </ul>
            </li>

        @endif

        @if (hasPermission('reports_read'))
            <li class="menu-item {{ openMenu(['report']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-checkup-list"></i>
                    <div data-i18n="{{ _trans('keyword.Reports') }}">
                        {{ _trans('keyword.Reports') }}</div>
                </a>
                <ul class="menu-sub">

                    @if (hasPermission('order_report_read'))
                        <li class="menu-item {{ activeMenu('report.orderReport') }}">
                            <a href="{{ route('report.orderReport') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Order Report') }}">
                                    {{ _trans('keyword.Order Report') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('expense_report_read'))
                        <li class="menu-item {{ activeMenu('report.expenseReport') }}">
                            <a href="{{ route('report.expenseReport') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Expense Report') }}">
                                    {{ _trans('keyword.Expense Report') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('product_report_read'))
                        <li class="menu-item {{ activeMenu('report.productReport') }}">
                            <a href="{{ route('report.productReport') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Product Report') }}">
                                    {{ _trans('keyword.Product Report') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('user_report_read'))
                        <li class="menu-item {{ activeMenu('report.userReport') }}">
                            <a href="{{ route('report.userReport') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.User Report') }}">
                                    {{ _trans('keyword.User Report') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('subscription_plan_report_read'))
                        <li class="menu-item {{ activeMenu('report.subscriptionReport') }}">
                            <a href="{{ route('report.subscriptionReport') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Subscription Plan Report') }}">
                                    {{ _trans('keyword.Subscription Plan Report') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('shared_product_report_read'))
                        <li class="menu-item {{ activeMenu('report.sharedProductReport') }}">
                            <a href="{{ route('report.sharedProductReport') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.White Label Report') }}">
                                    {{ _trans('keyword.White Label Report') }}</div>
                            </a>
                        </li>
                    @endif


                    @if (hasPermission('search_keyword_report_read'))
                        <li class="menu-item {{ activeMenu('report.searchKeywordReport') }}">
                            <a href="{{ route('report.searchKeywordReport') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Search Keyword Report') }}">
                                    {{ _trans('keyword.Search Keyword Report') }}</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if (hasPermission('contact_request_read'))
            <li class="menu-item {{ activeMenu(route('contact-request.index')) }}">
                <a href="{{ route('contact-request.index') }}" class="menu-link">
                    <i class="menu-icon ti ti-user-question"></i>
                    <div data-i18n="{{ _trans('keyword.Contact') }} {{ _trans('keyword.Request') }}">
                        {{ _trans('keyword.Contact') }} {{ _trans('keyword.Request') }}</div>
                </a>
            </li>
        @endif

        @if (hasPermission('designer_contact_read'))
            <li class="menu-item {{ activeMenu(route('designer-contact.index')) }}">
                <a href="{{ route('designer-contact.index') }}" class="menu-link">
                    <i class="menu-icon ti ti-user-question"></i>
                    <div data-i18n="{{ _trans('keyword.Customer') }} {{ _trans('keyword.Leads') }} ">
                        {{ _trans('keyword.Customer') }} {{ _trans('keyword.Leads') }}</div>
                </a>
            </li>
        @endif

        @if (hasPermission('newsletter_read'))
            <li class="menu-item {{ activeMenu(route('subscriber.index')) }}">
                <a href="{{ route('subscriber.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-share"></i>
                    <div data-i18n="{{ _trans('keyword.Newsletter') }}">{{ _trans('keyword.Newsletter') }}
                    </div>
                </a>
            </li>
        @endif

        @if (hasPermission('live_chat_read'))
            <li class="menu-item {{ activeMenu(route('live-chat.index')) }}">
                <a href="{{ route('live-chat.index') }}" class="menu-link">
                    <i class="menu-icon ti ti-brand-hipchat"></i>
                    <div data-i18n="{{ _trans('keyword.Live Chat') }}">{{ _trans('keyword.Live Chat') }}</div>
                </a>
            </li>
        @endif

        @if (hasPermission('seo_content_read') && hasModulePermission('ecommerce-support'))
            <li class="menu-item {{ openMenu(['seoContent']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-seo"></i>
                    <div data-i18n="{{ _trans('keyword.SEO Content') }}">{{ _trans('keyword.SEO Content') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (hasPermission('seo_product_list_read'))
                        <li class="menu-item {{ activeMenu('seoContent.product.index') }}">
                            <a href="{{ route('seoContent.product.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Product List') }}">
                                    {{ _trans('keyword.Product List') }}</div>
                            </a>
                        </li>
                    @endif
                    @if (Role::SUPER_ADMIN == \Illuminate\Support\Facades\Auth::user()->role_id)
                        <li class="menu-item {{ activeMenu('sitemap.index') }}">
                            <a href="{{ route('sitemap.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Sitemap') }}">
                                    {{ _trans('keyword.Sitemap') }}</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

        @endif

        @if (hasModulePermission('blog') && hasPermission('blog_read') && hasModulePermission('ecommerce-support'))
            <li class="menu-item {{ openMenu(['blog']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon ti ti-presentation"></i>
                    <div data-i18n="{{ _trans('keyword.Blog') }}">{{ _trans('keyword.Blog') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (hasPermission('blog_category_read'))
                        <li class="menu-item {{ activeMenu(route('blog.category.index')) }}">
                            <a href="{{ route('blog.category.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Category') }}">
                                    {{ _trans('keyword.Category') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('blog_post_read'))
                        <li class="menu-item {{ activeMenu(route('blog.post.index')) }}">
                            <a href="{{ route('blog.post.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Post') }}">{{ _trans('keyword.Post') }}</div>
                            </a>
                        </li>
                    @endif

                </ul>
            </li>
        @endif

        @if (hasPermission('reviews_read') && hasModulePermission('ecommerce-support'))
            <li class="menu-item {{ openMenu(['review', 'product.reviews']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon ti ti-star"></i>
                    <div data-i18n="{{ _trans('keyword.Reviews') }}">
                        {{ _trans('keyword.Reviews') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (hasPermission('review_type_read'))
                        <li class="menu-item {{ activeMenu('review.type.index') }}">
                            <a href="{{ route('review.type.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Review Type') }}">
                                    {{ _trans('keyword.Review Type') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('shop_reviews_read'))
                        <li class="menu-item {{ activeMenu('review.index') }}">
                            <a href="{{ route('review.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Shop') }}  {{ _trans('keyword.Reviews') }}">
                                    {{ _trans('keyword.Shop') }} {{ _trans('keyword.Reviews') }}
                                </div>
                            </a>
                        </li>
                    @endif
                    @if(hasPermission('product_reviews_read'))

                        <li class="menu-item {{ activeMenu('product.reviews.index') }}">
                            <a href="{{ route('product.reviews.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Product') }} {{ _trans('keyword.Reviews') }}">
                                    {{ _trans('keyword.Product') }} {{ _trans('keyword.Reviews') }}</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if (hasPermission('frontend_cms_read') && hasModulePermission('ecommerce-support'))
            <li class="menu-item {{ openMenu(['cms','section','gallery']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-adjustments-horizontal"></i>
                    <div data-i18n="{{ _trans('keyword.Frontend') }} {{ _trans('keyword.CMS') }}">
                        {{ _trans('keyword.Frontend') }} {{ _trans('keyword.CMS') }}</div>
                </a>
                <ul class="menu-sub">

                    @if (hasPermission('portfolio_and_inspiration_read'))
                        <li class="menu-item {{ openMenu(['section']) }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons ti ti-map"></i>
                                <div
                                    data-i18n="{{ _trans('keyword.Portfolio') }} & {{ _trans('keyword.Inspiration') }}">
                                    {{ _trans('keyword.Portfolio') }} & {{ _trans('keyword.Inspiration') }}</div>
                            </a>
                            <ul class="menu-sub">
                                @if (hasPermission('section_category_read'))
                                    <li class="menu-item {{ activeMenu('section.category') }}">
                                        <a href="{{ route('section.category.index') }}" class="menu-link">
                                            <div
                                                data-i18n="{{ _trans('keyword.Category') }} {{ _trans('keyword.List') }}">
                                                {{ _trans('keyword.Category') }}
                                                {{ _trans('keyword.List') }}</div>
                                        </a>
                                    </li>
                                @endif

                                @if (hasPermission('portfolio_and_inspiration_read'))
                                    <li
                                        class="menu-item {{ activeMenu(route('section.portfolioAndInspiration.index', 1)) }}">
                                        <a href="{{ route('section.portfolioAndInspiration.index', 1) }}"
                                           class="menu-link">
                                            <div data-i18n="{{ _trans('keyword.Portfolio') }}">
                                                {{ _trans('keyword.Portfolio') }}</div>
                                        </a>
                                    </li>
                                @endif
                                @if (hasPermission('portfolio_and_inspiration_read'))
                                    <li
                                        class="menu-item {{ activeMenu(route('section.portfolioAndInspiration.index', 2)) }}">
                                        <a href="{{ route('section.portfolioAndInspiration.index', 2) }}"
                                           class="menu-link">
                                            <div data-i18n="{{ _trans('keyword.Inspiration') }}">
                                                {{ _trans('keyword.Inspiration') }}</div>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (hasPermission('gallery_read'))
                        <li class="menu-item {{ activeMenu('gallery') }}">
                            <a href="{{ route('gallery.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons ti ti-photo-star"></i>
                                <div data-i18n="{{ _trans('keyword.Gallery') }}">{{ _trans('keyword.Gallery') }}
                                </div>
                            </a>
                        </li>
                    @endif


                    @if (hasPermission('footer_widget_read'))
                        <li class="menu-item {{ activeMenu(route('cms.footer-widget.index')) }}">
                            <a href="{{ route('cms.footer-widget.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Footer') }} {{ _trans('keyword.Widget') }}">
                                    {{ _trans('keyword.Footer') }} {{ _trans('keyword.Widget') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('pages_read'))
                        <li class="menu-item {{ activeMenu('cms.pages') }}">
                            <a href="{{ route('cms.pages.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Pages') }}">
                                    {{ _trans('keyword.Pages') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('faqs_read'))
                        <li class="menu-item {{ activeMenu(route('cms.faq.index')) }}">
                            <a href="{{ route('cms.faq.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.FAQs') }}">{{ _trans('keyword.FAQs') }}
                                </div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('slider_read'))
                        <li class="menu-item {{ activeMenu(route('cms.slider.index')) }}">
                            <a href="{{ route('cms.slider.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Slider') }}">
                                    {{ _trans('keyword.Slider') }}</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if (hasPermission('system_settings_read'))
            <li class="menu-item {{ openMenu(['setting', 'role']) }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-settings"></i>
                    <div data-i18n="{{ _trans('keyword.System') }} {{ _trans('keyword.Settings') }}">
                        {{ _trans('keyword.System') }} {{ _trans('keyword.Settings') }}</div>
                </a>
                <ul class="menu-sub">
                    @if (Auth::user()->role_id == 1)
                        <li class="menu-item {{ activeMenu('setting.general-setting') }}">
                            <a href="{{ route('setting.general-setting.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.General') }} {{ _trans('keyword.Settings') }}">
                                    {{ _trans('keyword.General') }} {{ _trans('keyword.Settings') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->role_id == 1)
                        <li class="menu-item {{ activeMenu('setting.file-system') }}">
                            <a href="{{ route('setting.file-system.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.File') }} {{ _trans('keyword.System') }}">
                                    {{ _trans('keyword.File') }} {{ _trans('keyword.System') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('shop_settings_read'))
                        <li class="menu-item {{ activeMenu('setting.shop-setting') }}">
                            <a href="{{ route('setting.shop-setting.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Shop') }} {{ _trans('keyword.Setting') }}">
                                    {{ _trans('keyword.Shop') }} {{ _trans('keyword.Setting') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->role_id == 1)
                        <li class="menu-item {{ activeMenu('setting.global-setting') }}">
                            <a href="{{ route('setting.global-setting.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Global') }} {{ _trans('keyword.Setting') }}">
                                    {{ _trans('keyword.Global') }} {{ _trans('keyword.Setting') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('role_read'))
                        <li class="menu-item {{ activeMenu('role') }}">
                            <a href="{{ route('role.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Role') }}">{{ _trans('keyword.Role') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('payment_method_read'))
                        <li class="menu-item {{ activeMenu('setting.payment-method') }}">
                            <a href="{{ route('setting.payment-method.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Payment') }} {{ _trans('keyword.Method') }}">
                                    {{ _trans('keyword.Payment') }} {{ _trans('keyword.Method') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('email_settings_read'))
                        <li class="menu-item {{ activeMenu('setting.email-setting') }}">
                            <a href="{{ route('setting.email-setting.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Email') }} {{ _trans('keyword.Settings') }}">
                                    {{ _trans('keyword.Email') }} {{ _trans('keyword.Settings') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('background_settings_read'))
                        <li class="menu-item {{ activeMenu('setting.background-settings') }}">
                            <a href="{{ route('setting.background-settings.index') }}" class="menu-link">
                                <div
                                    data-i18n="{{ _trans('keyword.Background') }} {{ _trans('keyword.Settings') }}">
                                    {{ _trans('keyword.Background') }} {{ _trans('keyword.Settings') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('color_themes_read'))
                        <li class="menu-item {{ activeMenu('setting.color-themes') }}">
                            <a href="{{ route('setting.color-themes.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Color') }} {{ _trans('keyword.Themes') }}">
                                    {{ _trans('keyword.Color') }} {{ _trans('keyword.Themes') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('language_settings_read'))
                        <li class="menu-item {{ activeMenu('setting.language') }}">
                            <a href="{{ route('setting.language.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Language') }} {{ _trans('keyword.Settings') }}">
                                    {{ _trans('keyword.Language') }} {{ _trans('keyword.Settings') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->role_id == 1)
                        <li class="menu-item {{ activeMenu('setting.API Setting') }}">
                            <a href="{{ route('setting.api-setting.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.API') }} {{ _trans('keyword.Settings') }}">
                                    {{ _trans('keyword.API') }} {{ _trans('keyword.Settings') }}</div>
                            </a>
                        </li>
                    @endif

                    @if (hasPermission('activity_log_read'))
                        <li class="menu-item {{ activeMenu('setting.activity-log') }}">
                            <a href="{{ route('setting.activity-log.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Activity Log') }}">
                                    {{ _trans('keyword.Activity Log') }}</div>
                            </a>
                        </li>
                    @endif
                    @if(hasPermission('free_signup_read'))
                        <li class="menu-item {{ activeMenu('setting.free-signup') }}">
                            <a href="{{ route('setting.free-signup.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Invitation Code') }}">
                                    {{ _trans('keyword.Invitation Code') }}</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->role_id == 1)
                        <li class="menu-item {{ activeMenu('setting.API Setting') }}">
                            <a href="{{ route('setting.cacheSetup.index') }}" class="menu-link">
                                <div data-i18n="{{ _trans('keyword.Cache') }} {{ _trans('keyword.Settings') }}">
                                    {{ _trans('keyword.Cache') }} {{ _trans('keyword.Settings') }}</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
    </ul>
</aside>
<!-- / Menu -->
