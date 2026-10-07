@php
    use App\Models\Role;
    use Carbon\Carbon;
@endphp
@extends('layouts.master')

@section('title', $title ?? __('Profile'))
<style>
    .current-plan-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--bs-primary);
        color: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 10;
        display: flex;
        align-items: center;
    }
</style>
@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('Profile', ['profile' => 'Profile']) !!}
        <div class="row">
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <img class="img-fluid rounded mb-3 pt-1 mt-4" src="{{ asset(getFilePath($user->avatar)) }}"
                                    height="100" width="100" alt="User avatar" />
                                <div class="user-info text-center">
                                    <h4 class="mb-2">{{ $user->name }}</h4>
                                    <span class="badge bg-info">{{ $user->role->name }}</span>

                                    @if ($user->role_id == \App\Models\Role::DESIGNER)
                                        <div class="rating rating-sm mt-1">
                                            {!! renderStarRating(getAverageRating($user)) !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around flex-wrap mt-3 pt-3 pb-4 border-bottom">
                            <div class="d-flex align-items-start me-4 mt-3 gap-2">
                                <span class="badge bg-label-primary p-2 rounded"><i class="ti ti-checkbox ti-sm"></i></span>
                                <div>
                                    <p class="mb-0 fw-medium">{{ $dataCount['product_count'] }}</p>
                                    <small>Total Product</small>
                                </div>
                            </div>
                            @if (hasModulePermission('ecommerce-support'))
                                <div class="d-flex align-items-start mt-3 gap-2">
                                    <span class="badge bg-label-primary p-2 rounded"><i
                                            class="ti ti-briefcase ti-sm"></i></span>
                                    <div>
                                        <p class="mb-0 fw-medium">{{ $dataCount['order_count'] }}</p>
                                        <small>Total Order</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <p class="mt-4 small text-uppercase text-muted">Details</p>
                        <div class="info-container">
                            <ul class="list-unstyled">

                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">Email:</span>
                                    <span>{{ $user->email }}</span>
                                </li>
                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">Status:</span>
                                    @if ($user->active_status == 1)
                                        <span class="badge bg-label-success">Active</span>
                                    @else
                                        <span class="badge bg-label-danger">Inactivate</span>
                                    @endif

                                </li>
                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">Subscription Status:</span>
                                    {!! subscriptionStatus($user) !!}
                                </li>
                                @if (checkIfUserIsClient($user) && isset($user->freeTrailCode))
                                    <li class="mb-2 pt-1">
                                        <span class="fw-medium me-1">{{ _trans('keyword.Free Trail Code') }}:</span>
                                        {{ $user->freeTrailCode->code }}
                                    </li>
                                @endif

                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">Contact:</span>
                                    <span>{{ $user->phone }}</span>
                                </li>
                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">Address:</span>
                                    <span>{{ $user->address }}</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:;" class="btn btn-primary me-3" data-bs-target="#editUser"
                                    data-bs-toggle="modal">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!--/ User Sidebar -->

            <!-- User Content -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                        @if (isSeller())
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-overview" aria-controls="navs-overview" aria-selected="false">
                                    <i class="ti ti-user me-1"></i> Overview
                                </button>
                            </li>
                        @endif
                        <li class="nav-item">
                            <button type="button" class="nav-link  @if (!isSeller()) active @endif"
                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-profile"
                                aria-controls="navs-pills-justified-profile" aria-selected="false">
                                <i class="tf-icons ti ti-lock ti-xs me-1"></i> Security
                            </button>
                        </li>
                        @if (isSeller() || in_array(Auth::user()->role_id, [Role::SUPER_ADMIN, Role::ADMIN]))
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-social-links" aria-controls="navs-social-links"
                                    aria-selected="false">
                                    <i class="tf-icons ti ti-message-dots ti-xs me-1"></i> Social Links
                                </button>
                            </li>
                        @endif
                        @if (isSeller() && $user->subscription_required == 1)
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-subscription-details" aria-controls="navs-social-links"
                                    id="navs-subscription-details-button" aria-selected="false">
                                    <i class="tf-icons ti ti-calendar-dollar ti-xs me-1"></i> Subscription
                                </button>
                            </li>
                        @endif

                    </ul>
                    <div class="tab-content p-0 bg-transparent shadow-none">
                        <div class="tab-pane fade   @if (isSeller()) show active @endif" id="navs-overview"
                            role="tabpanel">
                            <!-- / Customer cards -->
                            <div class="row text-nowrap">
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="card-icon mb-3">
                                                <div class="avatar">
                                                    <div class="avatar-initial rounded bg-label-primary">
                                                        <i class="ti ti-shopping-bag"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h4 class="card-title mb-3">Products</h4>
                                                <div class="d-flex align-items-baseline mb-1 gap-1">
                                                    <h4 class="text-primary mb-0">{{ $dataCount['product_count'] }}</h4>
                                                    <p class="mb-0"> Products </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if (hasModulePermission('ecommerce-support'))

                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="card-icon mb-3">
                                                    <div class="avatar">
                                                        <div class="avatar-initial rounded bg-label-success">
                                                            <i class='ti ti-gift ti-md'></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-info">
                                                    <h4 class="card-title mb-3">Total Order</h4>
                                                    <div class="d-flex align-items-baseline mb-1 gap-1">
                                                        <h4 class="text-success mb-0">{{ $dataCount['order_count'] }}</h4>
                                                        <p class="mb-0"> Order </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if (Auth::user()->role_id !== Role::MANUFACTURER)
                                        <div class="col-md-6 mb-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="card-icon mb-3">
                                                        <div class="avatar">
                                                            <div class="avatar-initial rounded bg-label-warning">
                                                                <i class="ti ti-list-numbers"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-info">
                                                        <h4 class="card-title mb-3">Total My Order</h4>
                                                        <div class="d-flex align-items-baseline mb-1 gap-1">
                                                            <h4 class="text-warning mb-0">
                                                                {{ $dataCount['my_order_count'] }}</h4>
                                                            <p class="mb-0">Order</p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="card-icon mb-3">
                                                        <div class="avatar">
                                                            <div class="avatar-initial rounded bg-label-info">
                                                                <i class="ti ti-shopping-cart"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-info">
                                                        <h4 class="card-title mb-3">Cart List</h4>
                                                        <div class="d-flex align-items-baseline mb-1 gap-1">
                                                            <h4 class="text-info mb-0">{{ $dataCount['cart_count'] }}</h4>
                                                            <p class="mb-0">Items in cart</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif


                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-icon mb-3">
                                                <div class="avatar">
                                                    <div class="avatar-initial rounded bg-label-danger">
                                                        <i class='ti ti-heart ti-md'></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-info">
                                                <h4 class="card-title mb-3">Wishlist</h4>
                                                <div class="d-flex align-items-baseline mb-1 gap-1">
                                                    <h4 class="text-danger mb-0">{{ $dataCount['wishlist_count'] }}</h4>
                                                    <p class="mb-0">Items in wishlist</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (Auth::user()->role_id !== Role::MANUFACTURER)
                                    <div class="col-md-6 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-icon mb-3">
                                                    <div class="avatar">
                                                        <div class="avatar-initial rounded bg-label-primary">
                                                            <i class="ti ti-list-details"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-info">
                                                    <h4 class="card-title mb-3">Total Portfolio & Inspiration</h4>
                                                    <div class="d-flex align-items-baseline mb-1 gap-1">
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-baseline mb-1 gap-1">
                                                                <h4 class="text-primary mb-0">
                                                                    {{ $dataCount['portfolio_count'] }}</h4>
                                                                <p class="mb-0">Portfolio</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-baseline mb-1 gap-1">
                                                                <h4 class="text-primary mb-0">
                                                                    {{ $dataCount['inspiration_count'] }}</h4>
                                                                <p class="mb-0">Inspiration</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>

                        </div>
                        <div class="tab-pane fade @if (!isSeller()) show active @endif"
                            id="navs-pills-justified-profile" role="tabpanel">
                            <div class="card">
                                <h5 class="card-header">Change Password</h5>
                                <div class="card-body">
                                    <form id="passwordFrom" class="row g-3">
                                        <input type="text" id="userID" hidden value="{{ $user->id }}">

                                        <div class="col-md-4">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="current_password">Current
                                                    Password<span class="text-danger">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input class="form-control" type="password" id="current_password"
                                                        name="current_password"
                                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                        aria-describedby="multicol-password2" />
                                                    <span class="input-group-text cursor-pointer"
                                                        id="multicol-password2"><i class="ti ti-eye-off"></i></span>
                                                </div>
                                                <span class="text-danger currentPasswordError error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="formValidationPass">New Password<span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input class="form-control" type="password" id="formValidationPass"
                                                        name="formValidationPass"
                                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                        aria-describedby="multicol-password2" />
                                                    <span class="input-group-text cursor-pointer"
                                                        id="multicol-password2"><i class="ti ti-eye-off"></i></span>
                                                </div>
                                                <span class="text-danger passwordError error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="formValidationConfirmPass">Confirm
                                                    Password<span class="text-danger">*</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input class="form-control" type="password"
                                                        id="formValidationConfirmPass" name="formValidationConfirmPass"
                                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                        aria-describedby="multicol-confirm-password2" />
                                                    <span class="input-group-text cursor-pointer"
                                                        id="multicol-confirm-password2"><i
                                                            class="ti ti-eye-off"></i></span>
                                                </div>
                                                <span class="text-danger confirmPasswordError error"></span>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="navs-social-links" role="tabpanel">
                            <div class="card">
                                <h5 class="card-header">Social Links</h5>
                                <div class="card-body">
                                    <form method="POST" id="social-links-vertical-form">
                                        @csrf
                                        <div class="content-header mb-3">
                                            <small>Enter Your Social Links.</small>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label class="form-label" for="twitter1">Twitter</label>
                                                <input type="text" id="twitter1" name="twitter" class="form-control"
                                                    value="{{ optional($user->shop)->twitter_url }}"
                                                    placeholder="https://twitter.com/abc" />
                                                <span class="text-danger twitterError error"></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="facebook1">Facebook</label>
                                                <input type="text" id="facebook1" name="facebook"
                                                    class="form-control"
                                                    value="{{ optional($user->shop)->facebook_url }}"
                                                    placeholder="https://facebook.com/abc" />
                                                <span class="text-danger facebookError error"></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="facebook1">Instagram</label>
                                                <input type="text" id="instagram" name="instagram"
                                                    class="form-control"
                                                    value="{{ optional($user->shop)->instagram_url }}"
                                                    placeholder="https://instagram.com/abc" />
                                                <span class="text-danger instagramError error"></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="linkedin1">Linkedin</label>
                                                <input type="text" id="linkedin1" name="linkedin"
                                                    class="form-control" value="{{ optional($user->shop)->linkedin }}"
                                                    placeholder="https://linkedin.com/abc" />
                                                <span class="text-danger linkedinError error"></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="linkedin1">Youtube</label>
                                                <input type="text" id="youtube" name="youtube" class="form-control"
                                                    value="{{ optional($user->shop)->youtube_url }}"
                                                    placeholder="https://youtube.com/abc" />
                                                <span class="text-danger youtubeError error"></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="linkedin1">Tiktok</label>
                                                <input type="text" id="tiktok" name="tiktok" class="form-control"
                                                    value="{{ optional($user->shop)->tiktok_url }}"
                                                    placeholder="https://tiktok.com/abc" />
                                                <span class="text-danger tiktokError error"></span>
                                            </div>
                                            <div class="col-12 d-flex justify-content-end">
                                                <button class="btn btn-primary" type="submit">
                                                    <span
                                                        class="align-middle d-sm-inline-block d-none me-sm-1">Submit</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="navs-subscription-details" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-12">
                                    @if (isSeller() && @$user->lastSubscription->latestItem->expire_at && $user->trail_mode != 1)
                                        <!-- Plan Card -->
                                        <div class="card border-0 shadow-sm overflow-hidden mb-4">
                                            <div class="card-header bg-label-primary py-3 border-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="m-0 fw-bold text-primary">
                                                        Current
                                                        Plan: {{ optional(optional($user->lastSubscription)->plan)->name }}
                                                    </h5>
                                                    <span class="badge bg-primary text-white rounded-pill px-3">Active
                                                        Plan</span>
                                                </div>
                                            </div>

                                            <div class="card-body p-4">
                                                @if (optional($user->lastSubscription)->cancel_at_period_end === 1 && isSeller())
                                                    <div class="alert alert-danger d-flex align-items-center mb-4 border-0 bg-light-danger"
                                                        role="alert">
                                                        <i class="ti ti-alert-circle me-2"></i>
                                                        <div>
                                                            @if (optional($latestCancelRequest)->status == 0)
                                                                Cancellation request sent and pending approval.
                                                            @elseif(optional($latestCancelRequest)->status == 1)
                                                                Approved. Subscription ends on
                                                                <strong>{{ dateFormatwithTime(optional($user->lastSubscription)->latestItem->expire_at) }}</strong>
                                                            @elseif(optional($latestCancelRequest)->status == 2)
                                                                Cancellation request declined. Please contact support.
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row g-4">
                                                    <div class="col-lg-5">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-3"
                                                            style="font-size: 0.75rem; letter-spacing: 1px;">Plan
                                                            Overview</h6>
                                                        <div class="d-flex align-items-baseline mb-3">
                                                            <h2 class="mb-0 fw-bold text-primary">
                                                                {{ getCurrency() }}{{ optional(optional($user->lastSubscription)->plan)->price }}
                                                            </h2>
                                                            <sub
                                                                class="text-muted ms-1">/{{ ucfirst(optional(optional($user->lastSubscription)->plan)->plan_type) }}</sub>
                                                        </div>
                                                        <div class="text-dark opacity-75 lh-base">
                                                            {!! ucfirst(optional(optional($user->lastSubscription)->plan)->description) !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-7 border-start-lg">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-3 ps-lg-3"
                                                            style="font-size: 0.75rem; letter-spacing: 1px;">Included
                                                            Modules</h6>
                                                        <div class="row row-cols-1 row-cols-md-2 g-2 ps-lg-3">
                                                            @foreach (json_decode(shopSetting()->modules) as $module)
                                                                <div class="col">
                                                                    <div
                                                                        class="d-flex align-items-center p-2 rounded-2 bg-light-subtle border">
                                                                        <i
                                                                            class="ti ti-square-check-filled text-success me-2 fs-5"></i>
                                                                        <div class="flex-grow-1">
                                                                            <small
                                                                                class="d-block fw-medium text-dark">{{ ucfirst(str_replace('-', ' ', $module->slug)) }}</small>
                                                                            <small class="text-muted">Limit: <span
                                                                                    class="fw-bold text-primary">{{ ucfirst($module->limit) }}</span></small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                @php
                                                    $percent = calculateTimeProgressPercent(
                                                        optional(optional($user->lastSubscription)->latestItem)
                                                            ->started_at,
                                                        optional(optional($user->lastSubscription)->latestItem)
                                                            ->expire_at,
                                                    );
                                                @endphp
                                                <div class="mt-4 pt-4 border-top">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-heading fw-semibold">Subscription Progress</span>
                                                        <span
                                                            class="text-muted small">{{ getDaysAndHoursDifferences(Carbon::now(), optional($user->lastSubscription)->latestItem->expire_at) }}</span>
                                                    </div>
                                                    <div class="progress rounded-pill shadow-none mb-1"
                                                        style="height: 8px; background-color: #f1f1f2;">
                                                        <div class="progress-bar bg-primary" role="progressbar"
                                                            style="width: {{ $percent }}%"
                                                            aria-valuenow="{{ $percent }}" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $percent }}% of billing cycle
                                                        completed</small>
                                                </div>

                                                <div
                                                    class="mt-4 pt-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center">
                                                    <p class="text-muted small mb-3 mb-md-0">Need a more flexible
                                                        solution? Explore our high-tier plans.</p>
                                                    <button
                                                        class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center"
                                                        data-bs-target="#upgradePlanModal" data-bs-toggle="modal">
                                                        <i class="ti ti-arrow-up-right me-2"></i> Upgrade Your Plan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Plan Card -->
                                    @elseif(@$user->trail_mode == 1)
                                        <div class="card border-0 shadow-sm overflow-hidden">
                                            <div class="card-header bg-label-warning py-3 border-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="m-0 fw-bold text-warning-emphasis">Current
                                                        Plan: {{ @$freeTrailPlan->name }}</h5>
                                                    <span class="badge bg-warning text-white rounded-pill px-3">Free
                                                        Trial</span>
                                                </div>
                                            </div>

                                            <div class="card-body p-4">
                                                <div class="row g-4">
                                                    <div class="col-lg-5">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-3"
                                                            style="font-size: 0.75rem; letter-spacing: 1px;">Plan
                                                            Overview</h6>
                                                        <div class="text-dark opacity-75 lh-base">
                                                            {!! ucfirst(@$freeTrailPlan->description) !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-7 border-start-lg">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-3 ps-lg-3"
                                                            style="font-size: 0.75rem; letter-spacing: 1px;">Included
                                                            Modules</h6>
                                                        <div class="row row-cols-1 row-cols-md-2 g-2 ps-lg-3">
                                                            @foreach (json_decode(shopSetting()->modules) as $module)
                                                                <div class="col">
                                                                    <div
                                                                        class="d-flex align-items-center p-2 rounded-2 bg-light-subtle border">
                                                                        <i
                                                                            class="ti ti-circle-check-filled text-success me-2 fs-5"></i>
                                                                        <div class="flex-grow-1">
                                                                            <small
                                                                                class="d-block fw-medium text-dark">{{ ucfirst(str_replace('-', ' ', $module->slug)) }}</small>
                                                                            <small class="text-muted">Limit: <span
                                                                                    class="fw-bold">{{ ucfirst($module->limit) }}</span></small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="mt-4 pt-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center">
                                                    <p class="text-muted small mb-3 mb-md-0">Need more power? Unlock
                                                        advanced features and higher limits.</p>
                                                    <button
                                                        class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center"
                                                        data-bs-target="#upgradePlanModal" data-bs-toggle="modal">
                                                        <i class="ti ti-arrow-up-right me-2"></i> Upgrade Your Plan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (@$user->trail_mode != 1)
                                        <div class="card mb-4">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="card-title m-0"><strong>Cancel Request List</strong></h5>
                                                <button class="btn btn-primary" data-bs-toggle="offcanvas"
                                                    data-bs-target="#offcanvasEBrandList"><i
                                                        class="ti ti-plus ti-xs me-0 me-sm-2"></i>Add Request
                                                </button>
                                            </div>

                                            <div class="card-datatable table-responsive">
                                                <table class="data-table table border-top">
                                                    <thead>
                                                        <tr>
                                                            <th>Plan Name</th>
                                                            <th>Title</th>
                                                            <th>Description</th>
                                                            <th>File</th>
                                                            <th>Comment</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cancelRequests as $list)
                                                            <tr>
                                                                <td>{{ optional($list->plan)->name }}</td>
                                                                <td>{{ $list->subject }}</td>
                                                                <td>{{ $list->description }}</td>
                                                                <td>{!! getFileElement(getFilePath($list->file)) !!}</td>
                                                                <td>{{ $list->comments }}</td>
                                                                <td>
                                                                    @if ($list->status == 1)
                                                                        <span
                                                                            class="badge bg-label-success">Accepted</span>
                                                                    @elseif ($list->status == 2)
                                                                        <span
                                                                            class="badge bg-label-danger">Cancelled</span>
                                                                    @else
                                                                        <span class="badge bg-label-primary">Pending</span>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-felx justify-content-center">
                                                {{ $cancelRequests->links('pagination::bootstrap-5') }}
                                            </div>


                                        </div>
                                    @endif

                                </div>
                            </div>

                            <!-- /Current Plan -->
                            <div class="card">
                                <div class="card-header">
                                    <h5>Billing History</h5>
                                </div>

                                <div class="card-datatable table-responsive">
                                    <table class="data-table table border-top">
                                        <thead>
                                            <tr>
                                                <th>Plan Name</th>
                                                <th>User Details</th>
                                                <th>Dounload Invoice</th>
                                                <th>Price</th>
                                                <th>Created Date</th>
                                                <th>Expire Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($subscriptionPaymentList as $list)
                                                <tr>
                                                    <td>{{ optional($list->plan)->name }}</td>
                                                    <td>
                                                        <pre>Name: {{ $list->customer_details ? json_decode($list->customer_details)->name : 'N/A' }}<br>Email: {{ $list->customer_details ? json_decode($list->customer_details)->email : 'N/A' }}<br>Sub ID: {{ optional($list->subscription)->stripe_subscription_id }}</pre>
                                                    </td>

                                                    <td>
                                                        <a href="{{ route('subscription.customer.invoicePreview', $list->stripe_invoice_no) }}"
                                                            class="badge bg-label-success mb-1">HB Invoice</a>
                                                        <a href="{{ route('subscription.customer.stripeGenerateInvoice', $list->stripe_invoice_no) }}"
                                                            class="badge bg-label-info">Stripe Invoice</a>
                                                    </td>
                                                    <td>{{ getPriceFormat($list->price / 100) }}</td>
                                                    <td>{{ dateFormat($list->started_at) }}</td>
                                                    <td>{{ dateFormat($list->expire_at) }}</td>
                                                    <td>

                                                        @if ($list->payment_status == 'paid')
                                                            <span
                                                                class="badge bg-label-success">{{ $list->payment_status }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-label-danger">{{ $list->payment_status }}</span>
                                                        @endif

                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-felx justify-content-center py-1">
                                    {{ $subscriptionPaymentList->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--/ User Content -->
        </div>

        <!-- Modal -->
        <!-- Edit User Modal -->
        <div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3 class="mb-2">Edit User Information</h3>
                            <p class="text-muted">Updating user details will receive a privacy audit.</p>
                        </div>
                        <form id="editUserForm" class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserFirstName">Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" id="modalEditUserName" name="modalEditUserName"
                                    class="form-control" placeholder="John" value="{{ $user->name }}" />
                                <span class="text-danger usernameError error"></span>

                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserPhone">Phone</label>
                                <input type="text" id="modalEditPhone" name="modalEditPhone" class="form-control"
                                    placeholder="+87554442" value="{{ $user->phone }}" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserEmail">Email</label>
                                <input type="text" value="{{ $user->email }}" id="modalEditUserEmail"
                                    name="modalEditUserEmail" class="form-control" placeholder="example@domain.com"
                                    readonly />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditTaxID">Address</label>
                                <input type="text" value="{{ $user->address }}" id="modalEditAddress"
                                    name="modalEditTaxID" class="form-control"
                                    placeholder="Richfield Springs, NY 13439" />
                            </div>

                            <div class="col-6">
                                <!-- Image -->
                                <div class="mb-3">
                                    <label class="form-label" for="user-image">Image</label>
                                    <input class="form-control" type="file" name="image" id="modalUserImage" />
                                    <span class="text-danger imageError error"></span>
                                </div>
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit
                                    <span class="loader"></span>
                                </button>
                                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Edit User Modal -->


        <!-- Upgrade plan Modal -->
        <div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-simple modal-edit-user">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3 class="mb-2">Upgrade plan Information</h3>
                            <p class="text-muted">Updating user details will receive a privacy audit.</p>
                        </div>
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {!! session('error') !!}
                            </div>
                        @endif
                        <div class="card px-3">
                            <div class="row">
                                <div class="col-lg-7 card-body border-end">

                                    <h4 class="mt-2 mb-1">Plan List</h4>
                                    <p class="mb-2"> Choose the best plan to fit your needs. </p>
                                    @foreach ($plans as $index => $plan)
                                        <div class="plan form-check custom-option custom-option-basic position-relative {{ optional($user->lastSubscription)->plan_id == $plan->id ? 'checked' : '' }}"
                                            role="button" data-object="{{ $plan }}">

                                            {{-- Modern Top-Right Badge --}}
                                            @if (optional($user->lastSubscription)->plan_id == $plan->id)
                                                <div class="current-plan-badge">
                                                    <i class="ti ti-star-filled me-1"></i> Current Plan
                                                </div>
                                            @endif

                                            <label
                                                class="form-check-label custom-option-content form-check-input-payment d-flex gap-3 align-items-center"
                                                for="plan{{ $plan->id }}">
                                                <input name="planRadio" class="form-check-input" type="radio"
                                                    {{ optional($user->lastSubscription)->plan_id == $plan->id ? 'checked' : '' }}
                                                    value="{{ $plan->id }}" id="plan{{ $plan->id }}" required />
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-2 p-2 w-100">
                                                    <div class="text-start">
                                                        <h5 class="mb-0">{{ $plan->name }}</h5>
                                                        <p class="mb-0">{!! $plan->description !!}</p>
                                                        <ul class="m-0 p-0">
                                                            @if (json_decode($plan->modules))
                                                                @foreach (json_decode($plan->modules) as $module)
                                                                    <li style="list-style: none;">
                                                                        <i class="ti ti-check text-success"></i>
                                                                        {{ ucfirst(str_replace('-', ' ', $module->slug)) }}
                                                                        :
                                                                        {{ $module->limit }}
                                                                    </li>
                                                                @endforeach
                                                            @endif

                                                        </ul>
                                                    </div>
                                                    <div class="ms-2">${{ $plan->price }}/{{ $plan->plan_type }}</div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach


                                </div>
                                <div class="col-lg-5 card-body">
                                    <h4 class="mb-2">Order Summary</h4>
                                    <p class="pb-2 mb-0">
                                        It can help you manage and service orders before,<br />
                                        during and after fulfilment.
                                    </p>
                                    <div class="bg-lighter p-4 rounded mt-4">
                                        <p class="mb-1" id="plan_name">Select A plan</p>
                                        <div class="d-flex align-items-center">
                                            <h1 class="text-heading display-5 mb-1"><span id="plan_price"></span></h1>
                                        </div>

                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <p class="mb-0">Subtotal</p>
                                            <h6 class="mb-0"><span id="subtotal_plan_price"></span></h6>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <p class="mb-0">Setup Fee</p>
                                            <h6 class="mb-0"><span id="one_time_fee"></span></h6>
                                        </div>
                                        <hr />
                                        <div class="d-flex justify-content-between align-items-center mt-3 pb-1">
                                            <p class="mb-0">Total</p>
                                            <h6 class="mb-0"><span id="total_price"></span></h6>
                                        </div>
                                        <div class="row py-4 my-2">
                                            @foreach ($paymentMethods as $index => $paymentMethod)
                                                <div class="col-md mb-md-0 mb-2">
                                                    <div
                                                        class="form-check custom-option custom-option-basic {{ $index == 0 ? 'checked' : '' }}">
                                                        <label
                                                            class="form-check-label custom-option-content form-check-input-payment d-flex gap-3 align-items-center"
                                                            for="{{ $paymentMethod['name'] }}">
                                                            <input name="customRadioTemp" class="form-check-input"
                                                                type="radio" value="{{ $paymentMethod['id'] }}"
                                                                id="{{ $paymentMethod['name'] }}" required
                                                                {{ $index == 0 ? 'checked' : '' }} />
                                                            <span class="custom-option-body">
                                                                <img src="{{ asset($paymentMethod['logo']) }}"
                                                                    alt="{{ $paymentMethod['name'] }}" width="58" />
                                                                <span class="ms-3">{{ $paymentMethod['name'] }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                        <form action="{{ route('subscription.plan.make-payment') }}" method="POST">
                                            @csrf
                                            <div class="d-grid mt-3">
                                                <input type="number" name="plan_id" id="plan_id" hidden required>
                                                <button class="btn btn-primary" disabled="disabled" type="submit"
                                                    id="buy_plan">
                                                    <span class="me-2">Proceed with Payment</span>
                                                    <i class="ti ti-arrow-right scaleX-n1-rtl"></i>
                                                </button>
                                            </div>
                                        </form>

                                        <p class="mt-4 pt-2">
                                            By continuing, you accept to our Terms of Services and Privacy Policy.
                                            Please
                                            note that
                                            payments are
                                            non-refundable.
                                        </p>
                                        <div class="d-flex justify-content-end align-items-center">
                                            <a class="btn btn-danger mb-2 " href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                                <i class="ti ti-logout me-2 ti-sm"></i>
                                                <span class="align-middle">Log Out</span>
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">
                                                @csrf
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Upgrade plan Modal -->

        <!-- Offcanvas to add new customer -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEBrandList"
            aria-labelledby="offcanvasEcommerceListLabel">
            <!-- Offcanvas Header -->
            <div class="offcanvas-header py-4">
                <h5 id="offcanvasEcommerceCategoryListLabel" class="offcanvas-title">
                    {{ _trans('keyword.Add') . ' ' . _trans('keyword.Request') }}</h5>
                <button type="button" class="btn-close bg-label-secondary text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <!-- Offcanvas Body -->
            <div class="offcanvas-body border-top">
                <form class="pt-0" action="{{ route('subscription.cancelRequest.store') }}" id="addModal"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label" for="ecommerce-category-title">{{ _trans('keyword.Title') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ecommerce-category-title" required
                            placeholder="Enter Title" name="title" aria-label="Title Name" />
                        <span class="text-danger nameError error"></span>
                    </div>
                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">{{ _trans('keyword.Description') }}<span
                                class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" required id="brand-descripton" cols="30" rows="10"></textarea>
                        <span class="text-danger descriptionError error"></span>
                    </div>
                    <!-- Image -->
                    <div class="mb-3">
                        <label class="form-label" for="brand-image">{{ _trans('keyword.File') }}</label>
                        <input class="form-control" type="file" name="file" id="brand-image" />
                        <span class="text-danger imageError error"></span>
                    </div>

                    <!-- Comment -->
                    <div class="mb-3">
                        <label class="form-label" for="ecommerce-category-title">{{ _trans('keyword.Comment') }}</label>
                        <textarea class="form-control" id="ecommerce-category-title" placeholder="Enter Comment" name="comment"
                            aria-label="Title Comment" cols="30" rows="10"></textarea>
                        <span class="text-danger commentError error"></span>
                    </div>

                    <!-- Submit and reset -->
                    <div class="mb-3">
                        <button type="submit"
                            class="btn btn-primary me-sm-3 me-1 data-submit">{{ _trans('keyword.Submit') }}
                            <span class="loader"></span>
                        </button>
                        <button type="reset" class="btn bg-label-danger"
                            data-bs-dismiss="offcanvas">{{ _trans('keyword.Cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>


        <!-- /Modal -->
    </div>

@endsection

@push('scripts')
    <script>
        $(function() {

            $('#passwordFrom').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData();

                let user_id = $('#userID').val();
                let currentPassword = $("#current_password").val();
                let password = $("#formValidationPass").val();
                let confirmPassword = $("#formValidationConfirmPass").val();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('user_id', {{ $user->id }});
                formData.append('current_password', currentPassword);
                formData.append('password', password);
                formData.append('password_confirmation', confirmPassword);


                $('.error').text('');
                $.ajax({
                    url: '{{ route('user.passwordReset') }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.status == 403) {
                            $('.currentPasswordError').text(response.errors?.current_password ?
                                response.errors
                                ?.current_password[0] : '');
                            $('.passwordError').text(response.errors?.password ? response.errors
                                ?.password[0] : '');
                            $('.confirmPasswordError').text(response.errors
                                ?.password_confirmation ? response.errors
                                ?.password_confirmation[0] : '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                        }
                        loader.hide();
                    },
                    error: function(error) {
                        toastr.error(error.responseJSON.message);
                        loader.hide();
                    }
                });

            })

            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData();

                let user_id = $('#userID').val();
                let name = $("#modalEditUserName").val();
                let phone = $("#modalEditPhone").val();
                let address = $("#modalEditAddress").val();
                var image = $("#modalUserImage").prop('files')[0] ?? '';
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('user_id', {{ $user->id }});
                formData.append('name', name);
                formData.append('phone', phone);
                formData.append('address', address);
                formData.append('avatar', image);
                loader.show();
                submitButton.prop('disabled', true);


                $('.error').text('');
                $.ajax({
                    url: '{{ route('user.update') }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        if (response.status === 403) {
                            $('.usernameError').text(response.errors?.name ? response.errors
                                .name[0] : '');
                            $('.imageError').text(response.errors?.avatar ? response
                                .errors.avatar[0] : '');
                        } else if (response.status === 200) {
                            location.reload(); // Refresh the page
                            toastr.success(response.message);
                            $('.btn-close').click();
                        }
                        loader.hide();
                    },
                    error: function(error) {
                        toastr.error(error.responseJSON.message);
                        loader.hide();
                    },
                    complete: function() {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });

            })
        });
    </script>

    <script>
        $('#logo-setting-form').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData();

            let name = $("input[name=name]").val();

            var lightLogo = $('#lightLogoInput').prop('files')[0] ?? '';
            var darkLogo = $('#darkLogoInput').prop('files')[0] ?? '';
            var fevicon = $('#feviconLogoInput').prop('files')[0] ?? '';
            var user_id = {{ $user->id }};

            formData.append('light_logo', lightLogo);
            formData.append('banner', darkLogo);
            formData.append('favicon', fevicon);
            formData.append('user_id', user_id);
            formData.append('_token', "{{ csrf_token() }}");


            $('.error').text('');
            $.ajax({
                url: '{{ route('user.shoplogoUpdate') }}',
                type: 'POST',
                contentType: 'multipart/form-data',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    if (response.status == 403) {
                        $('.light_logoError').text(response.errors?.light_logo ? response.errors
                            ?.light_logo[0] : '');
                        $('.dark_logoError').text(response.errors?.dark_logo ? response.errors
                            ?.dark_logo[0] : '');
                        $('.faviconError').text(response.errors?.favicon ? response.errors?.favicon[0] :
                            '');
                    } else if (response.status == 200) {
                        toastr.success(response.message);
                    }
                    loader.hide();
                },
                error: function(error) {
                    toastr.error(error.responseJSON.message);
                    loader.hide();
                }
            });
        });


        $('#social-links-vertical-form').on('submit', function(e) {
            e.preventDefault();

            $('.error').text('');
            $.ajax({
                url: '{{ route('user.shoplinkUpdate') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    twitter: $('#twitter1').val(),
                    facebook: $('#facebook1').val(),
                    instagram: $('#instagram').val(),
                    linkedin: $('#linkedin1').val(),
                    youtube: $('#youtube').val(),
                    tiktok: $('#tiktok').val(),
                    user_id: {{ $user->id }},

                },
                success: function(response) {
                    if (response.status == 403) {
                        $('.twitterError').text(response.errors?.twitter ? response.errors
                            ?.twitter[0] : '');
                        $('.facebookError').text(response.errors?.facebook ? response.errors
                            ?.facebook[0] : '');
                        $('.instagramError').text(response.errors?.instagram ? response.errors
                            ?.instagram[0] :
                            '');
                        $('.linkedinError').text(response.errors?.linkedin ? response.errors?.linkedin[
                                0] :
                            '');
                        $('.youtubeError').text(response.errors?.youtube ? response.errors?.youtube[0] :
                            '');
                        $('.tiktokError').text(response.errors?.tiktok ? response.errors?.tiktok[0] :
                            '');
                    } else if (response.status == 200) {
                        toastr.success(response.message);
                    }
                    loader.hide();
                },
                error: function(error) {

                    toastr.error(error.responseJSON.message);
                }
            });
        });
    </script>


    <script>
        let lightLogoImage = document.getElementById('lightLogo');
        const lightLogofileInput = document.querySelector('.lightLogo-account-file-input'),
            lightLogoresetFileInput = document.querySelector('.lightLogo-account-image-reset');

        if (lightLogoImage) {
            const resetImage = lightLogoImage.src;
            lightLogofileInput.onchange = () => {
                if (lightLogofileInput.files[0]) {
                    lightLogoImage.src = window.URL.createObjectURL(lightLogofileInput.files[0]);
                }
            };
            lightLogoresetFileInput.onclick = () => {
                lightLogofileInput.value = '';
                lightLogoImage.src = resetImage;
            };
        }

        let darkLogoImage = document.getElementById('darkLogo');
        const darkLogofileInput = document.querySelector('.darkLogo-account-file-input'),
            darkLogoresetFileInput = document.querySelector('.darkLogo-account-image-reset');

        if (darkLogoImage) {
            const resetImage = darkLogoImage.src;
            darkLogofileInput.onchange = () => {
                if (darkLogofileInput.files[0]) {
                    darkLogoImage.src = window.URL.createObjectURL(darkLogofileInput.files[0]);
                }
            };
            darkLogoresetFileInput.onclick = () => {
                darkLogofileInput.value = '';
                darkLogoImage.src = resetImage;
            };
        }


        let feviconImage = document.getElementById('feviconLogo');
        const feviconFileInput = document.querySelector('.fevicon-account-file-input'),
            resetFeviconFileInput = document.querySelector('.fevicon-account-image-reset');

        if (feviconImage) {
            const resetImage = feviconImage.src;
            feviconFileInput.onchange = () => {
                if (feviconFileInput.files[0]) {
                    feviconImage.src = window.URL.createObjectURL(feviconFileInput.files[0]);
                }
            };
            resetFeviconFileInput.onclick = () => {
                feviconFileInput.value = '';
                feviconImage.src = resetImage;
            };
        }
    </script>

    <script>
        @if (isSeller())
            const currentActivePlan = @json($user->lastSubscription->plan_id ?? null);
        @endif
        $(document).on('click', '.plan', function() {
            let data = $(this).attr("data-object");
            data = JSON.parse(data);
            $('#buy_plan').prop('disabled', true);

            if (data.id !== currentActivePlan) {
                $('#buy_plan').prop('disabled', false);
            }

            $('#plan_name').text(`${data.name}`);
            $('#plan_price').text(`$${data.price}`);
            $('#subtotal_plan_price').text(`$${data.price}`);
            $('#one_time_fee').text(`$${data.setup_fee}`);
            $('#plan_period').text(`${data.plan_type}`);
            $('#total_price').text(`$${data.price + data.setup_fee}`);
            $('#plan_id').val(data.id);
            if (data.id !== currentActivePlan) {
                $('#buy_plan').prop('disabled', false);
            }
        });

        // navs-subscription-details-button
        const urlParams = new URLSearchParams(window.location.search);
        let tab = urlParams.get('tab');

        if (tab == 'subscription') {
            $('#navs-subscription-details-button').click();
        }

        // $('.plan')[0].click();
    </script>
@endpush
