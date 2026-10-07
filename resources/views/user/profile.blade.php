@php use App\Models\Role; @endphp
@extends('layouts.master')

@section('title', $title ?? __('Profile'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('User Profile', ['user/index' => 'Users', 'user' => 'Profile']) !!}

        <div class="row">
            <!-- User Sidebar -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <img class="img-fluid rounded mb-3 pt-1 mt-4"
                                     src="{{ asset(getFilePath($user->avatar)) }}"
                                     height="100" width="100" alt="User avatar"/>
                                <div class="user-info text-center">
                                    <h4 class="mb-2">{{ $user->name }}</h4>
                                    <span class="badge bg-label-warning">{{$user->role->name}}</span>
                                    @if ($user->role_id == Role::DESIGNER)
                                        <div class="rating rating-sm mt-1">
                                            {!! renderStarRating(getAverageRating($user)) !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around flex-wrap mt-3 pt-3 pb-4 border-bottom">
                            @if ($user && $user->role_id !== 4)
                                <div class="d-flex align-items-start me-4 mt-3 gap-2">
                                    <span class="badge bg-label-primary p-2 rounded"><i
                                            class="ti ti-checkbox ti-sm"></i></span>
                                    <div>
                                        <p class="mb-0 fw-medium">{{ $user->products_count }}</p>
                                        <small>{{_trans('keyword.Total Product')}}</small>
                                    </div>
                                </div>
                            @endif
                            <div class="d-flex align-items-start mt-3 gap-2">
                                <span class="badge bg-label-primary p-2 rounded"><i
                                        class="ti ti-briefcase ti-sm"></i></span>
                                <div>
                                    <p class="mb-0 fw-medium">{{ $user->seller_orders_count }}</p>
                                    <small>{{_trans('keyword.Total Order')}}</small>
                                </div>
                            </div>
                        </div>
                        <p class="mt-4 small text-uppercase text-muted">{{_trans('keyword.Details')}}</p>
                        <div class="info-container">
                            <ul class="list-unstyled">

                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">{{_trans('keyword.Email')}}:</span>
                                    <span>{{ $user->email }}</span>
                                </li>
                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">{{_trans('keyword.Status')}}:</span>
                                    @if ($user->active_status == 1)
                                        <span class="badge bg-label-success">{{_trans('keyword.Active')}}</span>
                                    @else
                                        <span class="badge bg-label-danger">{{_trans('keyword.Inactive')}}</span>
                                    @endif

                                </li>
                                @if(checkIfUserIsClient($user))
                                    <li class="mb-2 pt-1">
                                        <span class="fw-medium me-1">{{_trans('keyword.Subscription Status')}}:</span>
                                        {!! subscriptionStatus($user) !!}
                                    </li>
                                @endif

                                @if(checkIfUserIsClient($user) && isset($user->freeTrailCode))
                                    <li class="mb-2 pt-1">
                                        <span class="fw-medium me-1">{{_trans('keyword.Free Trail Code')}}:</span>
                                        {{$user->freeTrailCode->code}}
                                    </li>
                                @endif

                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">{{_trans('keyword.Signup Source')}}:</span>
                                    <span class="badge bg-label-success">{{$user->user_source}}</span>

                                </li>

                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">{{_trans('keyword.Role')}}:</span>
                                    <span class="badge bg-label-info">{{ $user->role->name}}</span>
                                </li>
                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">{{_trans('keyword.Contact')}}:</span>
                                    <span>{{ $user->phone }}</span>
                                </li>
                                <li class="mb-2 pt-1">
                                    <span class="fw-medium me-1">{{_trans('keyword.Address')}}:</span>
                                    <span>{{ $user->address }}</span>
                                </li>
                                @if(Auth::user()->role_id == Role::SUPER_ADMIN &&($user->role_id == Role::DESIGNER || $user->role_id == Role::MANUFACTURER))
                                    <li class="mb-2 pt-1">
                                        <span class="fw-medium me-1">{{_trans('keyword.Login As')}}:</span>
                                        <a href="javascript:;" class="btn btn-danger me-3"
                                           onclick="loginAs()">{{ $user->name }}</a>
                                    </li>
                                @endif
                            </ul>
                            <div class="d-flex justify-content-center">


                                <a href="javascript:;" class="btn btn-primary me-3" data-bs-target="#editUser"
                                   data-bs-toggle="modal">{{_trans('keyword.Edit')}}</a>
                                @if (hasPermission('user_status_change'))
                                    @if ($user->active_status == 1)
                                        <a href="javascript:;"
                                           class="btn btn-label-danger suspend-user">{{_trans('keyword.Suspended')}}</a>
                                    @else
                                        <a href="javascript:;"
                                           class="btn btn-label-success suspend-user">{{_trans('keyword.Activate')}}</a>
                                    @endif
                                @endif
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
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-pills-justified-home"
                                    aria-controls="navs-pills-justified-home"
                                    aria-selected="true">
                                <i class="tf-icons ti ti-user-check ti-xs me-1"></i> {{_trans('keyword.Account')}}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#navs-pills-justified-profile"
                                    aria-controls="navs-pills-justified-profile"
                                    aria-selected="false">
                                <i class="tf-icons ti ti-lock ti-xs me-1"></i> {{_trans('keyword.Security')}}
                            </button>
                        </li>


                        @if ($user && $user->role_id !== Role::CUSTOMER && checkIfUserIsClient($user))
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-justified-setting"
                                        aria-controls="navs-pills-justified-setting" aria-selected="false">
                                    <i class="tf-icons ti ti-settings ti-xs me-1"></i> {{_trans('keyword.Shop Setting')}}
                                </button>
                            </li>
                        @endif
                        @if(checkIfUserIsClient($user) && $user->subscription_required == 1)

                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-subscription-details"
                                        id="navs-subscription-details-button"
                                        aria-controls="navs-subscription-details"
                                        aria-selected="false">
                                    <i class="tf-icons ti ti-lock ti-xs me-1"></i> {{_trans('keyword.Subscription')}}
                                </button>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content p-0 bg-transparent shadow-none">
                        <div class="tab-pane fade show active" id="navs-pills-justified-home" role="tabpanel">
                            <!-- Order table -->
                            <div class="card mb-4">
                                <h5 class="card-header pb-0">Order List</h5>

                                <div class="card-datatable ">
                                    <table class="order-data-table table border-top">
                                        <thead>
                                        <tr>
                                            <th>{{_trans('keyword.SL')}}</th>
                                            <th>{{_trans('keyword.Order ID')}}</th>
                                            <th>{{_trans('keyword.Designer')}}</th>
                                            <th>{{_trans('keyword.No. of Item')}}</th>
                                            <th>{{_trans('keyword.Date')}}</th>
                                            <th>{{_trans('keyword.Action')}}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <!-- /Order table -->


                            <!-- Cart table -->

                            <div class="card mb-4">
                                <h5 class="card-header pb-0">{{_trans('keyword.Cart List')}}</h5>

                                <div class="card-datatable ">
                                    <table class="cart-data-table table border-top">
                                        <thead>
                                        <tr>
                                            <th>{{_trans('keyword.SL')}}</th>
                                            <th>{{_trans('keyword.Image')}}</th>
                                            <th>{{_trans('keyword.Product').' '._trans('keyword.Name')}}</th>
                                            <th>{{_trans('keyword.Variation')}}</th>
                                            <th>{{_trans('keyword.Designer')}}</th>
                                            <th>{{_trans('keyword.Action')}}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <!-- /Cart table -->
                            <!-- Wishlist table -->

                            <div class="card mb-4">
                                <h5 class="card-header pb-0">{{_trans('keyword.WishList')}}</h5>

                                <div class="card-datatable ">
                                    <table class="wishlist-data-table table border-top">
                                        <thead>
                                        <tr>
                                            <th>{{_trans('keyword.SL')}}</th>
                                            <th>{{_trans('keyword.Image')}}</th>
                                            <th>{{_trans('keyword.Product Name')}}</th>
                                            <th>{{_trans('keyword.Action')}}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            @if ($user && $user->role_id !== 4)
                                <div class="card mb-4">
                                    <h5 class="card-header pb-0">{{_trans('keyword.Product').' '._trans('keyword.List')}}</h5>
                                    <div class="card-datatable ">
                                        <table class="product-data-table table border-top">
                                            <thead>
                                            <tr>
                                                <th>{{_trans('keyword.SL')}}</th>
                                                <th>{{_trans('keyword.Name')}}</th>
                                                <th>{{_trans('keyword.Image')}}</th>
                                                <th>{{_trans('keyword.Base Price')}}</th>
                                                <th>{{_trans('keyword.Status')}}</th>
                                                <th width="100px">{{_trans('keyword.Action')}}</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- /Wishlist table -->
                        </div>
                        <div class="tab-pane fade" id="navs-pills-justified-profile" role="tabpanel">
                            <div class="card">
                                <div class="d-flex justify-content-between align-items-center"
                                     style="padding-top: 24px; padding-left: 24px; padding-right: 25px">
                                    <h5>{{_trans('keyword.Change').' '._trans('keyword.Password')}}</h5>
                                    <button type="submit" class="btn btn-warning" data-id="{{$user->id}}"
                                            id="reset-password">
                                        {{_trans('keyword.Reset Password')}}
                                        <span class="loader"></span>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <form id="passwordFrom" class="row g-3">
                                        <input type="text" id="userID" hidden value="{{ $user->id }}">

                                        <div class="col-md-4">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="current_password">Current
                                                    Password</label>
                                                <div class="input-group input-group-merge">
                                                    <input class="form-control" type="password" id="current_password"
                                                           name="current_password"
                                                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                           aria-describedby="multicol-password2"/>
                                                    <span class="input-group-text cursor-pointer"
                                                          id="multicol-password2"><i class="ti ti-eye-off"></i></span>
                                                </div>
                                                <span class="text-danger currentPasswordError error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="formValidationPass">New Password</label>
                                                <div class="input-group input-group-merge">
                                                    <input class="form-control" type="password" id="formValidationPass"
                                                           name="formValidationPass"
                                                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                           aria-describedby="multicol-password2"/>
                                                    <span class="input-group-text cursor-pointer"
                                                          id="multicol-password2"><i class="ti ti-eye-off"></i></span>
                                                </div>
                                                <span class="text-danger passwordError error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="formValidationConfirmPass">Confirm
                                                    Password</label>
                                                <div class="input-group input-group-merge">
                                                    <input class="form-control" type="password"
                                                           id="formValidationConfirmPass"
                                                           name="formValidationConfirmPass"
                                                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                           aria-describedby="multicol-confirm-password2"/>
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

                        @if ($user && $user->role_id !== 4)
                            <div class="tab-pane fade" id="navs-pills-justified-setting" role="tabpanel">
                                <div class="col-12 mb-4">
                                    <div class="bs-stepper vertical wizard-vertical-icons-example mt-2">
                                        <div class="bs-stepper-header">
                                            <div class="step" data-target="#system-info-setting">
                                                <button type="button" class="step-trigger">
                                                    <span class="bs-stepper-circle">
                                                        <i class="ti ti-file-description"></i>
                                                    </span>
                                                    <span class="bs-stepper-label">
                                                        <span
                                                            class="bs-stepper-title">{{_trans('keyword.System Info')}}</span>
                                                        <span
                                                            class="bs-stepper-subtitle">{{_trans('keyword.Setup').' '._trans('keyword.System Info')}}</span>
                                                    </span>
                                                </button>
                                            </div>
                                            <div class="line"></div>
                                            <div class="step" data-target="#logo-setting">
                                                <button type="button" class="step-trigger">
                                                    <span class="bs-stepper-circle">
                                                        <i class="ti ti-user"></i>
                                                    </span>
                                                    <span class="bs-stepper-label">
                                                        <span
                                                            class="bs-stepper-title">{{_trans('keyword.Site Logo')}}</span>
                                                        <span
                                                            class="bs-stepper-subtitle">{{_trans('keyword.Add').' '._trans('keyword.Site Logo')}}</span>
                                                    </span>
                                                </button>
                                            </div>
                                            <div class="line"></div>
                                            <div class="step" data-target="#social-links-vertical">
                                                <button type="button" class="step-trigger">
                                                    <span class="bs-stepper-circle"><i
                                                            class="ti ti-brand-instagram"></i>
                                                    </span>
                                                    <span class="bs-stepper-label">
                                                        <span class="bs-stepper-title">Social Links</span>
                                                        <span
                                                            class="bs-stepper-subtitle">{{_trans('keyword.Add').' '._trans('keyword.Social Links')}}</span>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="bs-stepper-content">
                                            <!-- System Info -->
                                            <div id="system-info-setting" class="content">
                                                <form method="POST" id="system-info-setting-form">
                                                    @csrf
                                                    <div class="content-header mb-3">
                                                        <h6 class="mb-0">{{_trans('keyword.System').' '._trans('keyword.Info')}}</h6>
                                                        <small>{{_trans('keyword.Enter Your System Info')}}.</small>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-sm-6">
                                                            <label class="form-label"
                                                                   for="shop_name">{{_trans('keyword.Shop Name')}} <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" id="shop_name" name="shop_name"
                                                                   class="form-control" placeholder="House Brand"
                                                                   value="{{ optional($user->shop)->shop_name }}"/>

                                                            <span class="text-danger shop_nameError error"></span>

                                                        </div>
                                                        <div class="col-sm-12">
                                                            <label class="form-label"
                                                                   for="address">{{_trans('keyword.Address')}} <span
                                                                    class="text-danger">*</span></label>
                                                            <div class="input-group input-group-merge">
                                                                <textarea name="address" id="address"
                                                                          class="form-control"
                                                                          placeholder="24/A, Road-6, Miami"
                                                                          rows="3">{{ optional($user->shop)->location }}</textarea>

                                                            </div>

                                                            <span class="text-danger addressError error"></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label"
                                                                   for="phone">{{_trans('keyword.Phone')}} <span
                                                                    class="text-danger">*</span></label>
                                                            <div class="input-group input-group-merge">
                                                                <input type="text" id="phone" name="phone"
                                                                       class="form-control"
                                                                       placeholder="+88754451415"
                                                                       value="{{ optional($user->shop)->phone }}"/>
                                                            </div>
                                                            <span class="text-danger phoneError error"></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label"
                                                                   for="email">{{_trans('keyword.Email')}} <span
                                                                    class="text-danger">*</span></label>
                                                            <div class="input-group input-group-merge">
                                                                <input type="email" id="email" name="email"
                                                                       class="form-control"
                                                                       value="{{ optional($user->shop)->email }}"
                                                                       placeholder="housebrand@example.com"/>
                                                            </div>

                                                            <span class="text-danger emailError error"></span>

                                                        </div>
                                                        <div class="col-sm-12">
                                                            <label class="form-label"
                                                                   for="mapLocation">{{_trans('keyword.Map Location')}}</label>
                                                            <div class="input-group input-group-merge">
                                                                <textarea id="map_location" name="map_location"
                                                                          class="form-control"
                                                                          placeholder="Iframe Map Location"
                                                                          rows="3">{{ optional($user->shop)->map_location }}</textarea>
                                                            </div>

                                                            <span class="text-danger map_locationError error"></span>

                                                        </div>
                                                        <div class="col-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit">
                                                                <span
                                                                    class="align-middle d-sm-inline-block d-none me-sm-1">{{_trans('keyword.Submit')}}
                                                                    <span class="loader"></span>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- Site Logo -->
                                            <div id="logo-setting" class="content">
                                                <form method="POST" id="logo-setting-form"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="content-header mb-3">
                                                        <h6 class="mb-0">{{_trans('keyword.Site Logo')}}</h6>
                                                        <small>{{_trans('keyword.Enter Your Site Logo.')}}</small>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-6">
                                                            <div class="card mb-4">
                                                                <h5 class="card-header">{{_trans('keyword.logo')}}</h5>
                                                                <!-- Account -->
                                                                <div class="card-body">
                                                                    <div
                                                                        class="d-flex align-items-start align-items-sm-center gap-4">
                                                                        <img
                                                                            src="{{ getFilePath(optional($user->shop)->logo) }}"
                                                                            onerror="this.onerror=null;this.src='{{ asset('assets/img/illustrations/page-pricing-enterprise.png') }}'"
                                                                            alt="user-avatar"
                                                                            class="d-block w-px-100 h-px-100 rounded"
                                                                            id="lightLogo"/>

                                                                        <div class="button-wrapper">
                                                                            <label for="lightLogoInput"
                                                                                   class="btn btn-primary mb-3 waves-effect waves-light"
                                                                                   tabindex="0">
                                                                                <span
                                                                                    class="d-none d-sm-block">{{_trans('keyword.logo')}}</span>
                                                                                <i
                                                                                    class="ti ti-upload d-block d-sm-none"></i>
                                                                                <input type="file" id="lightLogoInput"
                                                                                       name="light_logo"
                                                                                       class=" lightLogo-account-file-input"
                                                                                       hidden=""
                                                                                       accept="image/png, image/jpeg, image/jpg"/>
                                                                            </label>
                                                                            <button type="button"
                                                                                    class="btn btn-label-secondary  mb-3 waves-effect lightLogo-account-image-reset">
                                                                                <i
                                                                                    class="ti ti-refresh-dot d-block d-sm-none"></i>
                                                                                <span
                                                                                    class="d-none d-sm-block">{{_trans('keyword.Reset')}}</span>
                                                                            </button>

                                                                            <div
                                                                                class="text-muted">{{ _trans('keyword.Allowed JPG, GIF or PNG. Max size of 800KB') }}</div>
                                                                            <span
                                                                                class="text-danger light_logoError error"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- /Account -->
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="card mb-4">
                                                                <h5 class="card-header">{{_trans('keyword.Favicon')}}</h5>
                                                                <!-- Account -->
                                                                <div class="card-body">
                                                                    <div
                                                                        class="d-flex align-items-start align-items-sm-center gap-4">

                                                                        <img
                                                                            src="{{ getFilePath(optional($user->shop)->favicon) }}"
                                                                            onerror="this.onerror=null;this.src='{{ asset('assets/img/illustrations/page-pricing-enterprise.png') }}'"
                                                                            class="d-block w-px-100 h-px-100 rounded"
                                                                            id="feviconLogo"/>
                                                                        <div class="button-wrapper">
                                                                            <label for="feviconLogoInput"
                                                                                   class="btn btn-primary mb-3 waves-effect waves-light"
                                                                                   tabindex="0">
                                                                                <span
                                                                                    class="d-none d-sm-block">{{_trans('keyword.favicon')}}</span>
                                                                                <i
                                                                                    class="ti ti-upload d-block d-sm-none"></i>
                                                                                <input type="file"
                                                                                       id="feviconLogoInput"
                                                                                       class="fevicon-account-file-input"
                                                                                       name="favicon" hidden=""
                                                                                       accept="image/png, image/jpeg, image/jpg">
                                                                            </label>
                                                                            <button type="button"
                                                                                    class="btn btn-label-secondary fevicon-account-image-reset mb-3 waves-effect">
                                                                                <i
                                                                                    class="ti ti-refresh-dot d-block d-sm-none"></i>
                                                                                <span
                                                                                    class="d-none d-sm-block">{{_trans('keyword.Reset')}}</span>
                                                                            </button>

                                                                            <div
                                                                                class="text-muted">{{ _trans('keyword.Allowed JPG, GIF or PNG. Max size of 800KB') }}</div>
                                                                            <span
                                                                                class="text-danger faviconError error"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- /Account -->
                                                            </div>
                                                        </div>


                                                        <div class="col-8">
                                                            <div class="card mb-4">
                                                                <h5 class="card-header">{{_trans('keyword.Current Banner')}}</h5>
                                                                <!-- Account -->
                                                                <div class="card-body">
                                                                    <img
                                                                        src="{{ getFilePath(optional($user->shop)->banner) }}"
                                                                        id="darkLogo"
                                                                        onerror="this.onerror=null;this.src='{{ asset('assets/img/illustrations/page-pricing-enterprise.png') }}'"
                                                                        alt="user-avatar"
                                                                        class="d-block w-100 h-px-100 rounded"/>

                                                                </div>
                                                                <!-- /Account -->
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card mb-4">
                                                                <h5 class="card-header">{{_trans('keyword.Banner')}}</h5>
                                                                <!-- Account -->
                                                                <div class="card-body">
                                                                    <div
                                                                        class="d-flex align-items-start align-items-sm-center gap-4">
                                                                        <div class="button-wrapper">
                                                                            <label for="darkLogoInput"
                                                                                   class="btn btn-primary  mb-3 waves-effect waves-light"
                                                                                   tabindex="0">
                                                                                <span
                                                                                    class="d-none d-sm-block">{{_trans('keyword.Banner')}}</span>
                                                                                <i
                                                                                    class="ti ti-upload d-block d-sm-none"></i>
                                                                                <input type="file" id="darkLogoInput"
                                                                                       class="darkLogo-account-file-input"
                                                                                       name="banner" hidden=""
                                                                                       accept="image/png, image/jpeg, image/jpg">
                                                                            </label>
                                                                            <button type="button"
                                                                                    class="btn btn-label-secondary darkLogo-account-image-reset mb-3 waves-effect">
                                                                                <i
                                                                                    class="ti ti-refresh-dot d-block d-sm-none"></i>
                                                                                <span
                                                                                    class="d-none d-sm-block">{{_trans('keyword.Reset')}}</span>
                                                                            </button>

                                                                            <div
                                                                                class="text-muted">{{ _trans('keyword.Allowed JPG, GIF or PNG. Max size of 800KB') }}</div>
                                                                            <span
                                                                                class="text-danger dark_logoError error"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- /Account -->
                                                            </div>
                                                        </div>

                                                        <div class="col-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit">
                                                                <span
                                                                    class="align-middle d-sm-inline-block d-none me-sm-1">{{_trans('keyword.Submit')}}
                                                                    <span class="loader"></span>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- Social Links -->
                                            <div id="social-links-vertical" class="content">
                                                <form method="POST" id="social-links-vertical-form">
                                                    @csrf
                                                    <div class="content-header mb-3">
                                                        <h6 class="mb-0">{{_trans('keyword.Social Links')}}</h6>
                                                        <small>{{_trans('keyword.Enter Your Social Links.')}}</small>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-sm-6">
                                                            <label class="form-label" for="twitter1">Twitter</label>
                                                            <input type="text" id="twitter1" name="twitter"
                                                                   class="form-control"
                                                                   value="{{ optional($user->shop)->twitter_url }}"
                                                                   placeholder="https://twitter.com/abc"/>
                                                            <span class="text-danger twitterError error"></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label" for="facebook1">Facebook</label>
                                                            <input type="text" id="facebook1" name="facebook"
                                                                   class="form-control"
                                                                   value="{{ optional($user->shop)->facebook_url }}"
                                                                   placeholder="https://facebook.com/abc"/>
                                                            <span class="text-danger facebookError error"></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label" for="facebook1">Instagram</label>
                                                            <input type="text" id="instagram" name="instagram"
                                                                   class="form-control"
                                                                   value="{{ optional($user->shop)->instagram_url }}"
                                                                   placeholder="https://instagram.com/abc"/>
                                                            <span class="text-danger instagramError error"></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label" for="linkedin1">Linkedin</label>
                                                            <input type="text" id="linkedin1" name="linkedin"
                                                                   class="form-control"
                                                                   value="{{ optional($user->shop)->linkedin }}"
                                                                   placeholder="https://linkedin.com/abc"/>
                                                            <span class="text-danger linkedinError error"></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label" for="linkedin1">Youtube</label>
                                                            <input type="text" id="youtube" name="youtube"
                                                                   class="form-control"
                                                                   value="{{ optional($user->shop)->youtube_url }}"
                                                                   placeholder="https://youtube.com/abc"/>
                                                            <span class="text-danger youtubeError error"></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label" for="linkedin1">Tiktok</label>
                                                            <input type="text" id="tiktok" name="tiktok"
                                                                   class="form-control"
                                                                   value="{{ optional($user->shop)->tiktok_url }}"
                                                                   placeholder="https://tiktok.com/abc"/>
                                                            <span class="text-danger tiktokError error"></span>
                                                        </div>
                                                        <div class="col-12 d-flex justify-content-end">
                                                            <button class="btn btn-primary" type="submit">
                                                                <span
                                                                    class="align-middle d-sm-inline-block d-none me-sm-1">{{_trans('keyword.Submit')}}
                                                                    <span class="loader"></span>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="tab-pane fade" id="navs-subscription-details" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    @if (checkIfUserIsClient($user) && @$user->lastSubscription->latestItem->expire_at && @$user->trail_mode != 1)
                                        <!-- Plan Card -->
                                        <div class="card border-0 shadow-sm overflow-hidden mb-4">
                                            <div class="card-header bg-label-primary py-3 border-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="m-0 fw-bold text-primary">
                                                        Current
                                                        Plan: {{ optional(optional($user->lastSubscription)->plan)->name }}
                                                    </h5>
                                                    <span class="badge bg-primary text-white rounded-pill px-3">Active Plan</span>
                                                </div>
                                            </div>

                                            <div class="card-body p-4">
                                                @if (optional($user->lastSubscription)->cancel_at_period_end === 1 && isSeller())
                                                    <div
                                                        class="alert alert-danger d-flex align-items-center mb-4 border-0 bg-light-danger shadow-none"
                                                        role="alert">
                                                        <i class="ti ti-alert-circle me-2 fs-4"></i>
                                                        <div class="small">
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
                                                            <h2 class="mb-0 fw-bold text-primary">{{ getCurrency() }}{{ optional(optional($user->lastSubscription)->plan)->price }}</h2>
                                                            <sub
                                                                class="text-muted ms-1">/{{ ucfirst(optional(optional($user->lastSubscription)->plan)->plan_type) }}</sub>
                                                        </div>

                                                        <div class="text-dark opacity-75 lh-base mb-4">
                                                            {!! ucfirst(optional(optional($user->lastSubscription)->plan)->description) !!}
                                                        </div>

                                                        @php
                                                            $percent = calculateTimeProgressPercent(optional(optional($user->lastSubscription)->latestItem)->started_at, optional(optional($user->lastSubscription)->latestItem)->expire_at);
                                                        @endphp

                                                    </div>

                                                    <div class="col-lg-7 border-start-lg">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-3 ps-lg-3"
                                                            style="font-size: 0.75rem; letter-spacing: 1px;">Included
                                                            Modules</h6>
                                                        <div class="row row-cols-1 row-cols-md-2 g-2 ps-lg-3">
                                                            @if(json_decode($user->shop->modules))
                                                                @foreach (json_decode($user->shop->modules) as $module)
                                                                    <div class="col">
                                                                        <div
                                                                            class="d-flex align-items-center p-2 rounded-2 bg-light-subtle border">
                                                                            <i class="ti ti-square-check-filled text-success me-2 fs-5"></i>
                                                                            <div class="flex-grow-1">
                                                                                <small
                                                                                    class="d-block fw-medium text-dark">{{ ucfirst(str_replace('-', ' ', $module->slug)) }}</small>
                                                                                <small class="text-muted">Limit: <span
                                                                                        class="fw-bold text-primary">{{ ucfirst($module->limit) }}</span></small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="col-12 ps-lg-3">
                                                                    <p class="text-muted small">No modules assigned to
                                                                        this shop.</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $percent = calculateTimeProgressPercent(optional(optional($user->lastSubscription)->latestItem)->started_at, optional(optional($user->lastSubscription)->latestItem)->expire_at);
                                                @endphp
                                                <div class="mt-4 pt-4 border-top">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span
                                                            class="text-heading fw-semibold">Subscription Progress</span>
                                                        <span
                                                            class="text-muted small">{{ getDaysAndHoursDifferences(\Carbon\Carbon::now(), optional($user->lastSubscription)->latestItem->expire_at) }}</span>
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
                                            </div>
                                        </div>
                                        <!-- /Plan Card -->
                                    @elseif(@$user->trail_mode == 1)
                                        <div class="card border-0 shadow-sm overflow-hidden mb-3">
                                            <div class="card-header bg-label-warning py-3 border-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="m-0 fw-bold text-warning-emphasis">Current
                                                        Plan: {{ @$freeTrailPlan->name }}</h5>
                                                    <span class="badge bg-warning text-white rounded-pill px-3">Free Trial</span>
                                                </div>
                                            </div>

                                            <div class="card-body p-4">
                                                <div class="row g-4">
                                                    <div class="col-lg-5">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-3"
                                                            style="font-size: 0.75rem; letter-spacing: 1px;">Plan
                                                            Overview</h6>
                                                        <div class="text-dark opacity-75 lh-base">
                                                            {!! ucfirst( @$freeTrailPlan->description ) !!}
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-7 border-start-lg">
                                                        <h6 class="text-muted text-uppercase fw-semibold mb-3 ps-lg-3"
                                                            style="font-size: 0.75rem; letter-spacing: 1px;">Included
                                                            Modules</h6>
                                                        <div class="row row-cols-1 row-cols-md-2 g-2 ps-lg-3">
                                                            @foreach (json_decode($user->shop->modules) as $module)
                                                                <div class="col">
                                                                    <div
                                                                        class="d-flex align-items-center p-2 rounded-2 bg-light-subtle border">
                                                                        <i class="ti ti-circle-check-filled text-success me-2 fs-5"></i>
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
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card mb-4">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title m-0"><strong>Cancel Request List</strong></h5>
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
                                                                <span class="badge bg-label-success">Accepted</span>
                                                            @elseif ($list->status == 2)
                                                                <span class="badge bg-label-danger">Cancelled</span>
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
                                            <th>Download Invoice</th>
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
                            <h3 class="mb-2">{{_trans('keyword.Edit User Information')}}</h3>
                            <p class="text-muted">{{_trans('keyword.Updating user details will receive a privacy audit.')}}</p>
                        </div>
                        <form id="editUserForm" class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserFirstName">{{_trans('keyword.Name')}} <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="modalEditUserName" name="modalEditUserName"
                                       class="form-control" placeholder="John" value="{{ $user->name }}"/>
                                <span class="text-danger usernameError error"></span>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserPhone">{{_trans('keyword.Phone')}}</label>
                                <input type="text" id="modalEditPhone" name="modalEditPhone" class="form-control"
                                       placeholder="+87554442" value="{{ $user->phone }}"/>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserEmail">{{_trans('keyword.Email')}}</label>
                                <input type="text" value="{{ $user->email }}" id="modalEditUserEmail"
                                       name="modalEditUserEmail" class="form-control" placeholder="example@domain.com"
                                       readonly/>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditTaxID">{{_trans('keyword.Address')}}</label>
                                <input type="text" value="{{ $user->address }}" id="modalEditAddress"
                                       name="modalEditTaxID" class="form-control"
                                       placeholder="Richfield Springs, NY 13439"/>
                            </div>

                            <div class="col-6">
                                <!-- Image -->
                                <div class="mb-3">
                                    <label class="form-label" for="user-image">{{_trans('keyword.Image')}}</label>
                                    <input class="form-control" type="file" name="image" id="modalUserImage"/>
                                    <span class="text-danger imageError error"></span>
                                </div>
                            </div>


                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">{{_trans('keyword.Update')}}
                                    <span class="loader"></span>
                                </button>
                                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                        aria-label="Close">
                                    {{_trans('keyword.Cancel')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Edit User Modal -->


        <!-- /Modal -->
    </div>

@endsection

@push('scripts')
    <script>
        $(function () {

            var table = $('.order-data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('user.orders', $user->id) }}',
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'designer.name'
                    },
                    {
                        data: 'items_count',
                        name: 'items_count',
                        searchable: false
                    },
                    {
                        data: 'order_date',
                        name: 'order_date',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                initComplete: function () {
                    $('.order-data-table').wrap('<div class="overflow-auto"></div>');
                },
                order: [2, "desc"], //set any columns order asc/desc
                lengthMenu: [5, 10, 30, 50], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search in order list",
                },
            });

            var table = $('.cart-data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('user.carts', $user->id) }}',
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'thumbnail_img',
                        name: 'thumbnail_img',
                        searchable: false
                    },
                    {
                        data: 'product_name',
                        name: 'product.name'
                    },
                    {
                        data: 'variation',
                        name: 'variation',
                        searchable: false
                    },
                    {
                        data: 'seller',
                        name: 'seller.name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                initComplete: function () {
                    $('.cart-data-table').wrap('<div class="overflow-auto"></div>');
                },
                order: [2, "desc"], //set any columns order asc/desc
                lengthMenu: [5, 10, 30, 50], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search in cart list",
                },
            });

            var table = $('.wishlist-data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('user.wishlist', $user->id) }}',
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'thumbnail_img',
                        name: 'thumbnail_img',
                        searchable: false
                    },
                    {
                        data: 'product_name',
                        name: 'product.name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                initComplete: function () {
                    $('.wishlist-data-table').wrap('<div class="overflow-auto"></div>');
                },
                order: [2, "desc"], //set any columns order asc/desc
                lengthMenu: [5, 10, 30, 50], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search in Wishlist",
                },
            });
            @if ($user && $user->role_id !== 4)
            var table = $('.product-data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('user.products', $user->id) }}',
                columns: [{
                    data: '',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'thumbnail_img',
                        name: 'thumbnail_img'
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                columnDefs: [{
                    targets: 0,
                    className: "control",
                    responsivePriority: 1,
                    render: function () {
                        return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                    },
                }],
                order: [2, "desc"], //set any columns order asc/desc
                dom: '<"card-header d-flex flex-wrap pb-2"' +
                    "<f>" +
                    '<"d-flex justify-content-center justify-content-md-end align-items-baseline"<"dt-action-buttons d-flex justify-content-center flex-md-row mb-3 mb-md-0 ps-1 ms-1 align-items-baseline"lB>>' +
                    ">t" +
                    '<"row mx-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    ">",
                initComplete: function () {
                    $('.product-data-table').wrap('<div class="overflow-auto"></div>');
                },
                lengthMenu: [5, 10, 20], //for length of menu
                language: {
                    sLengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search Product",
                },
                // Button for offcanvas
                buttons: [],
            });
            @endif



            $('#passwordFrom').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData();

                let user_id = $('#userID').val();
                let password = $("#formValidationPass").val();
                let currentPassword = $("#current_password").val();
                let confirmPassword = $("#formValidationConfirmPass").val();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('user_id', {{ $user->id }});
                formData.append('password', password);
                formData.append('current_password', currentPassword);
                formData.append('password_confirmation', confirmPassword);

                loader.show();
                submitButton.prop('disabled', true);

                $('.error').text('');
                $.ajax({
                    url: '{{ route('user.passwordReset') }}',
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        if (response.status == 403) {
                            $('.currentPasswordError').text(response.errors?.current_password ? response.errors
                                ?.current_password[0] : '');
                            $('.passwordError').text(response.errors?.password ? response.errors
                                ?.password[0] : '');
                            $('.confirmPasswordError').text(response.errors
                                ?.password_confirmation ? response.errors
                                ?.password_confirmation[0] : '');
                        } else if (response.status == 200) {
                            toastr.success(response.message);
                        }
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });

            })

            $('#reset-password').on('click', function (e) {
                event.preventDefault();

                let userId = $(this).data('id');


                Swal.fire({
                    title: 'Are you sure?',
                    text: "The password will be reset to the default password.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Reset',
                    customClass: {
                        confirmButton: 'btn btn-primary me-2 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        loader.show();
                        submitButton.prop('disabled', true);

                        $.ajax({
                            url: '{{ route('user.defaultPassword') }}',
                            method: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                user_id: userId,
                            },
                            success: function (response) {
                                if (response.status === 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: response.message,
                                        customClass: {
                                            confirmButton: 'btn btn-success waves-effect waves-light'
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message,
                                        customClass: {
                                            confirmButton: 'btn btn-success waves-effect waves-light'
                                        }
                                    });
                                }
                            },
                            error: function (error) {
                                // Handle error response from the server
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: error.responseJSON?.message || 'Something went wrong!',
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    }
                                });
                            },
                            complete: function () {
                                loader.hide();
                                submitButton.prop('disabled', false);
                            }
                        });


                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: 'Cancelled',
                            text: 'Cancelled Change Status:)',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success waves-effect waves-light'
                            }
                        });
                    }
                });
            })

            $('#editUserForm').on('submit', function (e) {
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
                    success: function (response) {
                        if (response.status === 403) {
                            $('.usernameError').text(response.errors?.name ? response.errors
                                .name[0] : '');
                            $('.imageError').text(response.errors?.avatar ? response
                                .errors.avatar[0] : '');
                        } else if (response.status === 200) {
                            toastr.success(response.message);
                            $('.btn-close').click();
                            location.reload();
                        }
                    },
                    error: function (error) {
                        toastr.error(error.responseJSON.message);
                    },
                    complete: function () {
                        loader.hide();
                        submitButton.prop('disabled', false);
                    }
                });

            })

            const suspendUser = document.querySelector('.suspend-user');

            // Suspend User javascript
            if (suspendUser) {
                suspendUser.onclick = function () {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert user!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Change Status',
                        customClass: {
                            confirmButton: 'btn btn-primary me-2 waves-effect waves-light',
                            cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                        },
                        buttonsStyling: false
                    }).then(function (result) {
                        if (result.value) {
                            $.ajax({
                                url: '{{ route('user.changeStatus') }}',
                                method: 'POST',
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    user_id: {{ $user->id }},
                                },
                                success: function (response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Changed!',
                                        text: response.text,
                                        customClass: {
                                            confirmButton: 'btn btn-success waves-effect waves-light'
                                        }
                                    }).then(function (confirm) {
                                        if (confirm.value) {
                                            location.reload();
                                        }
                                    });
                                },
                                error: function (error) {
                                    toastr.error(error.responseJSON.message);
                                }
                            });

                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire({
                                title: 'Cancelled',
                                text: 'Cancelled Change Status:)',
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-success waves-effect waves-light'
                                }
                            });
                        }
                    });
                };
            }

        });
    </script>

    <script>
        $('#system-info-setting-form').on('submit', function (e) {
            e.preventDefault();

            loader.show();
            submitButton.prop('disabled', true);

            $('.error').text('');
            $.ajax({
                url: '{{ route('user.shopInfoUpdate') }}',
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    // element = $('input[name="element_name"]');
                    shop_name: $('#shop_name').val(),
                    address: $('#address').val(),
                    phone: $('#phone').val(),
                    email: $('#email').val(),
                    map_location: $('#map_location').val(),
                    user_id: {{ $user->id }},
                },
                success: function (response) {
                    if (response.status == 403) {
                        $('.shop_nameError').text(response.errors?.shop_name ? response.errors
                            ?.shop_name[
                            0] : '');
                        $('.siteTitleError').text(response.errors?.siteTitle ? response.errors
                            ?.siteTitle[0] : '');
                        $('.addressError').text(response.errors?.address ? response.errors?.address[0] :
                            '');
                        $('.phoneError').text(response.errors?.phone ? response.errors?.phone[0] : '');
                        $('.emailError').text(response.errors?.email ? response.errors?.email[0] : '');

                    } else if (response.status == 200) {
                        toastr.success(response.message);
                    }
                },
                error: function (error) {
                    toastr.error(error.responseJSON.message);
                },
                complete: function () {
                    loader.hide();
                    submitButton.prop('disabled', false);
                }
            });
        });


        $('#logo-setting-form').on('submit', function (e) {
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

            loader.show();
            submitButton.prop('disabled', true);

            $('.error').text('');
            $.ajax({
                url: '{{ route('user.shoplogoUpdate') }}',
                type: 'POST',
                contentType: 'multipart/form-data',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
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
                error: function (error) {
                    toastr.error(error.responseJSON.message);
                },
                complete: function () {
                    loader.hide();
                    submitButton.prop('disabled', false);
                }
            });
        });


        $('#social-links-vertical-form').on('submit', function (e) {
            e.preventDefault();

            loader.show();
            submitButton.prop('disabled', true);

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
                success: function (response) {
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
                },
                error: function (error) {
                    loader.hide();
                    toastr.error(error.responseJSON.message);
                },
                complete: function () {
                    loader.hide();
                    submitButton.prop('disabled', false);
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

        function loginAs() {
            Swal.fire({
                title: 'Are you sure to login?',
                text: "You will be logout from your account!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Login',
                customClass: {
                    confirmButton: 'btn btn-primary me-2 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ route('user.loginAs') }}',
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            user_id: {{ $user->id }},
                        },
                        success: function (response) {
                            window.location.href = "/dashboard"
                        },
                        error: function (error) {
                            toastr.error(error.responseJSON.message);
                        }
                    });

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Cancelled Login submission',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-success waves-effect waves-light'
                        }
                    });
                }
            });
        }

        // navs-subscription-details-button
        const urlParams = new URLSearchParams(window.location.search);
        let tab = urlParams.get('tab');

        if (tab == 'subscription') {
            $('#navs-subscription-details-button').click();
        }
    </script>
@endpush
