@php
    use App\Models\Role;
    use Illuminate\Support\Facades\Auth;
@endphp
    <!-- Navbar -->
<div class="row container-fluid" id="timer-app">
    <x-timer-modal/>
    <nav class="layout-navbar navbar navbar-expand-xl navbar-detached align-items-center p-0 bg-transparent shadow-none"
         id="layout-navbar">
        <div class="row w-100 mx-0 align-items-center h-100">
            <div class="{{Auth::user()->role_id !== Role::MANUFACTURER?'col-12 col-xl-9':'col-12'}} d-flex align-items-center p-0 h-100">
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="ti ti-menu-2 ti-sm"></i>
                    </a>
                </div>

                <div
                    class="navbar-nav-right d-flex align-items-center justify-content-between rounded shadow-sm bg-navbar-theme flex-grow-1 px-1 h-100"
                    id="navbar-collapse">

                    <ul class="navbar-nav align-items-center flex-row">
                        @if (hasPermission('dashboard_shortcut_read'))
                            <!-- Quick links  -->
                            <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                   data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <i class="ti ti-layout-grid-add ti-md"></i>
                                </a>
                                <div class="dropdown-menu py-0">
                                    <div class="dropdown-menu-header border-bottom">
                                        <div class="dropdown-header d-flex align-items-center py-3">
                                            <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                                        </div>
                                    </div>
                                    <div class="dropdown-shortcuts-list scrollable-container">
                                        <div class="row row-bordered overflow-visible g-0">

                                            {{--                                Orders --}}
                                            @if (hasPermission('order_shortcut_read'))
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                        <i class="ti ti-truck fs-4"></i>
                                                    </span>
                                                    <a href="{{ route('order.index') }}" class="stretched-link">Orders</a>
                                                    <small class="text-muted mb-0">Order List</small>
                                                </div>
                                            @endif

                                            {{--                                Carts --}}
                                            @if (hasPermission('cart_shortcut_read'))
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                        <i class="ti ti-shopping-cart fs-4"></i>
                                                    </span>
                                                    <a href="{{ route('cart.index') }}" class="stretched-link">Cart</a>
                                                    <small class="text-muted mb-0">Cart list</small>
                                                </div>
                                            @endif


                                        </div>
                                        <div class="row row-bordered overflow-visible g-0">

                                            {{--                                Users --}}
                                            @if (hasPermission('user_shortcut_read'))
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                        <i class="ti ti-users fs-4"></i>
                                                    </span>
                                                    <a href="{{ route('user.index', Role::CUSTOMER) }}"
                                                       class="stretched-link">Users</a>
                                                    <small class="text-muted mb-0">User Management</small>
                                                </div>
                                            @endif

                                            {{--                                Products --}}
                                            @if (hasPermission('product_shortcut_read'))
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                        <i class="ti ti-color-swatch fs-4"></i>
                                                    </span>
                                                    <a href="{{ route('product.index') }}"
                                                       class="stretched-link">Products</a>
                                                    <small class="text-muted mb-0">Product List</small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row row-bordered overflow-visible g-0">

                                            {{--                                Dashboard Statistics --}}
                                            @if (hasPermission('dashboard_statistics_read'))
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                        <i class="ti ti-chart-bar fs-4"></i>
                                                    </span>
                                                    <a href="{{ route('dashboard') }}" class="stretched-link">Dashboard</a>
                                                    <small class="text-muted mb-0">Dashboard Statistics</small>
                                                </div>
                                            @endif

                                            {{--                                Setting --}}
                                            @if (hasPermission('settings_shortcut_read'))
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                        <i class="ti ti-settings fs-4"></i>
                                                    </span>
                                                    <a href="{{ route('setting.shop-setting.index') }}"
                                                       class="stretched-link">Setting</a>
                                                    <small class="text-muted mb-0">Shop Settings</small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row row-bordered overflow-visible g-0">
                                            {{--                                FAQs --}}
                                            @if (hasPermission('faqs_shortcut_read'))
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                        <i class="ti ti-help fs-4"></i>
                                                    </span>
                                                    <a href="{{ route('cms.faq.index') }}" class="stretched-link">FAQs</a>
                                                    <small class="text-muted mb-0">All FAQ</small>
                                                </div>
                                            @endif

                                            {{--                                Notices --}}
                                            @if (hasPermission('notice_board_shortcut_read'))
                                                <div class="dropdown-shortcuts-item col">
                                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                                        <i class="ti ti-square fs-4"></i>
                                                    </span>
                                                    <a href="{{ route('notice-board.notice.noticeBoard') }}"
                                                       class="stretched-link">Notices</a>
                                                    <small class="text-muted mb-0">Notice Board</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <!-- Quick links -->
                        @endif

                        <!-- Search -->
                        <li class="nav-item align-items-center d-flex">
                            @if (Auth::user()->role_id == Role::DESIGNER && Auth::user()->shop)
                                <a href="{{ env('APP_FRONTEND_URL') . '/designer/' . Auth::user()->shop->slug }}"
                                   target="_blank">
                                    <i class="ti ti-world ti-md me-2 text-white"></i>
                                </a>
                            @else
                                <a href="{{ env('APP_FRONTEND_URL') }}" target="_blank">
                                    <i class="ti ti-world ti-md me-2 text-white"></i>
                                </a>
                            @endif
                        </li>
                        {{-- User Guide Link --}}
                        <li class="nav-item align-items-center d-flex">
                            <a href="https://guide.housebrands.com"
                                target="_blank"
                                title="User Guide">
                                <i class="ti ti-book ti-md text-white"></i>
                            </a>
                        </li>
                    </ul>
                    <div id="date-time-container" class="d-md-block d-none">
                        <div id="date-view"></div>
                        <div id="date-time"></div>
                    </div>
                    <!-- /Search -->
                    <ul class="navbar-nav flex-row align-items-center gap-2 ">
                        @if (hasModulePermission('project-management') && hasPermission('project_management_read') && Auth::user()->role_id != Role::SUPER_ADMIN)
                            <div class="timer-avatar-wrapper shadow" :class="{'timer-avatar-active':showPreview}"
                                 data-bs-toggle="modal"
                                 data-bs-target="#timeTrackerModal" v-cloak>
                                <svg v-show="!showPreview" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24"
                                     fill="none">
                                    <g clip-path="url(#clip0_4491_23901)">
                                        <path
                                            d="M5 13C5 13.9193 5.18106 14.8295 5.53284 15.6788C5.88463 16.5281 6.40024 17.2997 7.05025 17.9497C7.70026 18.5998 8.47194 19.1154 9.32122 19.4672C10.1705 19.8189 11.0807 20 12 20C12.9193 20 13.8295 19.8189 14.6788 19.4672C15.5281 19.1154 16.2997 18.5998 16.9497 17.9497C17.5998 17.2997 18.1154 16.5281 18.4672 15.6788C18.8189 14.8295 19 13.9193 19 13C19 11.1435 18.2625 9.36301 16.9497 8.05025C15.637 6.7375 13.8565 6 12 6C10.1435 6 8.36301 6.7375 7.05025 8.05025C5.7375 9.36301 5 11.1435 5 13Z"
                                            stroke="#7367F0" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"/>
                                        <path d="M14.5 10.5L12 13" stroke="#7367F0" stroke-width="2"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17 8L18 7" stroke="#7367F0" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                        <path d="M14 3H10" stroke="#7367F0" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_4491_23901">
                                            <rect width="24" height="24" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <p v-show="showPreview" class="text-center mb-0">@{{ formattedTotalTime }}</p>
                            </div>
                        @endif
                        {{-- language  --}}
                        @if (Auth::user() && Auth::user()->role_id == Role::SUPER_ADMIN)
                            <div class="navbar-nav align-items-center">
                                <div class="nav-item navbar-search-wrapper mb-0">
                                    <div class="form-group">
                                        <select name="active_lang" id="active_lang" class="select2 form-select2"
                                                style="width: 100%" data-placeholder="Select Language"
                                                onchange="changeLanguage()">
                                            @foreach (activeLanguages() as $language)
                                                <option value="{{ $language->code }}"
                                                    {{ $language->code == \Cache::get('locale') ? 'selected' : '' }}>
                                                    {{ $language->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- language end --}}

                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                               data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <img src="{{ getFilePath(Auth::user()->avatar) }}" alt class="rounded-circle"/>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-online">
                                                    <img src="{{ getFilePath(Auth::user()->avatar) }}" alt
                                                         class="rounded-circle"/>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fw-medium d-block">{{ Auth::user()->name }}</span>
                                                <small class="text-muted"> {{ Auth::user()->role->name }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="ti ti-user-check me-2 ti-sm"></i>
                                        <span class="align-middle">My Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('setting.shop-setting.index') }}">
                                        <i class="ti ti-settings me-2 ti-sm"></i>
                                        <span class="align-middle">Settings</span>
                                    </a>
                                </li>

                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('accessHouseBrandsAI') }}" target="_blank">
                                        <span class="d-flex align-items-center align-middle">
                                            <i class="flex-shrink-0 ti ti-brand-openai me-2 ti-sm text-danger"></i>
                                            <span class="flex-grow-1 align-middle">House Brands AI</span>
                                        </span>
                                    </a>
                                </li>
                                @if (Auth::user()->role_id == Role::SUPER_ADMIN)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('subscription.plan.index') }}">
                                            <span class="d-flex align-items-center align-middle">
                                                <i class="flex-shrink-0 ti ti-credit-card me-2 ti-sm"></i>
                                                <span class="flex-grow-1 align-middle">Plans</span>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('subscription.customer.index') }}">
                                            <i class="ti ti-currency-dollar me-2 ti-sm"></i>
                                            <span class="align-middle">Subscribers</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                @endif
                                <li>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                    <i class="ti ti-logout me-2 ti-sm"></i>
                                    <span class="align-middle">Log Out</span>
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      class="d-none">
                                    @csrf
                                </form>
                                </li>
                            </ul>
                        </li>
                            <li class="nav-item navbar-dropdown  dropdown d-xl-none d-block">
                                @if(Auth::user()->role_id !== Role::MANUFACTURER)

                                            <a href="{{ route('setting.quickShopSetting.index') }}"
                                               class="p-10 btn btn-sm btn-white text-primary d-flex align-items-center px-sm-3 px-1 ">
                                                <i class="ti ti-manual-gearbox me-1 "></i> <span class="d-md-block d-none">Quick Setup </span><span class="ms-md-1 d-sm-block d-none ">10%</span>
                                            </a>

                                @endif
                            </li>
                        <!--/ User -->
                    </ul>

                    <!-- Search Small Screens -->
                    <div class="navbar-search-wrapper search-input-wrapper d-none">
                        <input type="text" class="form-control search-input container-xxl border-0"
                               placeholder="Search..." aria-label="Search..."/>
                        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
                    </div>
                </div>
            </div>
        @if(Auth::user()->role_id !== Role::MANUFACTURER)
            <div class="col-12 col-xl-3 mt-3 mt-xl-0 d-xl-block d-none h-100 pe-0">
                <div class="d-flex align-items-center ms-0 ms-xl-3 p-2 rounded shadow-sm bg-navbar-theme h-100 ">
                    <div class="d-flex flex-column me-3 flex-grow-1">
                        <small class="text-white mb-1">Shop Setup Status</small>
                        <div class="d-flex align-items-center">
                            <div class="progress w-100" >
                                <div class="progress-bar " id="quick-setting-progress-bar" role="progressbar"
                                     style="width: 0%;" aria-valuenow="0"
                                     aria-valuemin="0" aria-valuemax="100">0%
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('setting.quickShopSetting.index') }}"
                       class="btn btn-sm btn-white text-primary d-flex align-items-center">
                        <i class="ti ti-manual-gearbox me-1 d-md-block d-none"></i> Quick Setup
                    </a>
                </div>
            </div>
        @endif
        </div>
    </nav>
</div>

<script src="{{asset(mix('/js/timer-modal/index.js'))}}"></script>
<!-- / Navbar -->
@push('scripts')
    <script>
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('setting.quickShopSetting.shopSettingPercentage') }}",
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        let percent = Math.round(response.percent ?? 0);
                        let $progressBar = $('#quick-setting-progress-bar');

                        // Update width and text
                        $progressBar.css('width', percent + '%');
                        $progressBar.attr('aria-valuenow', percent);
                        $progressBar.text(percent + '%');
                    }
                },
                error: function (error) {
                    console.error(error);
                    toastr.error(error.responseJSON?.message || 'Something went wrong');
                }
            });
        });
    </script>
@endpush

