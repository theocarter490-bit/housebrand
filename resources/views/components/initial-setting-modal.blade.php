<div class="modal fade" id="multiStepModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Welcome to House Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Modern Wizard -->
                <div class="col-12">
                    <div class="bs-stepper wizard-icons wizard-icons-example shadow-none">
                        <div class="bs-stepper-header">
                            <div class="step" data-target="#account-details">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-icon">
                                        <svg viewBox="0 0 54 54">
                                            <use
                                                xlink:href="../../assets/svg/icons/form-wizard-account.svg#wizardAccount">
                                            </use>
                                        </svg>
                                    </span>
                                    <span class="bs-stepper-label">Shop Setting</span>
                                </button>
                            </div>
                            <div class="line">
                                <i class="ti ti-chevron-right"></i>
                            </div>
                            <div class="step" data-target="#personal-info">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-icon">
                                        <svg viewBox="0 0 58 54">
                                            <use
                                                xlink:href="../../assets/svg/icons/form-wizard-personal.svg#wizardPersonal">
                                            </use>
                                        </svg>
                                    </span>
                                    <span class="bs-stepper-label">Shop Logo</span>
                                </button>
                            </div>
                            <div class="line">
                                <i class="ti ti-chevron-right"></i>
                            </div>
                            <div class="step" data-target="#addressForm">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-icon">
                                        <svg viewBox="0 0 54 54">
                                            <use
                                                xlink:href="../../assets/svg/icons/form-wizard-address.svg#wizardAddress">
                                            </use>
                                        </svg>
                                    </span>
                                    <span class="bs-stepper-label">Social Link</span>
                                </button>
                            </div>
                            <div class="line">
                                <i class="ti ti-chevron-right"></i>
                            </div>
                            <div class="step" data-target="#social-links">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-icon">
                                        <svg viewBox="0 0 54 54">
                                            <use
                                                xlink:href="../../assets/svg/icons/form-wizard-social-link.svg#wizardSocialLink">
                                            </use>
                                        </svg>
                                    </span>
                                    <span class="bs-stepper-label">Polices</span>
                                </button>
                            </div>
                            <div class="line">
                                <i class="ti ti-chevron-right"></i>
                            </div>
                            <div class="step" data-target="#introduction-message">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-icon">
                                        <svg viewBox="0 0 54 54">
                                            <use
                                                xlink:href="../../assets/svg/icons/form-wizard-submit.svg#wizardSubmit">
                                            </use>
                                        </svg>
                                    </span>
                                    <span class="bs-stepper-label">Welcome</span>
                                </button>
                            </div>

                        </div>
                        <div class="bs-stepper-content">

                            <div id="account-details" class="content">
                                <form method="POST" id="system-info-setting-form">
                                    @csrf
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">{{ _trans('keyword.Shop') . ' ' . _trans('keyword.Info') }}
                                        </h6>
                                        <small>{{ _trans('keyword.Enter') . ' ' . _trans('keyword.Shop') . ' ' . _trans('keyword.Info') }}</small>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label"
                                                   for="shop_name">{{ _trans('keyword.Shop') . ' ' . _trans('keyword.Name') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" id="shop_name" name="shop_name" class="form-control"
                                                   placeholder="House Brand" value="{{ @$setting->shop_name }}"/>

                                            <span class="text-danger shop_nameError error"></span>

                                        </div>
                                        {{-- Home slider style  --}}
                                        @if (auth()->user()->role_id != App\Models\Role::MANUFACTURER)
                                            <div class="col-md-3 ecommerce-select2-dropdown">
                                                <label for="styleList"
                                                       class="form-label mb-1 d-flex justify-content-between align-items-center">
                                                    <span>{{ _trans('keyword.Home Slider Style') }}</span>
                                                </label>
                                                <select id="styleList" name="styleList" class="select2 form-select"
                                                        data-placeholder="Select Style" onchange="PreviewHomeStyle()">
                                                    <option value="1"
                                                        {{ @$setting->home_slider_style == 1 ? 'selected' : '' }}>
                                                        Style
                                                        1
                                                    </option>
                                                    <option value="2"
                                                        {{ @$setting->home_slider_style == 2 ? 'selected' : '' }}>
                                                        Style
                                                        2
                                                    </option>
                                                    <option value="3"
                                                        {{ @$setting->home_slider_style == 3 ? 'selected' : '' }}>
                                                        Style
                                                        3
                                                    </option>
                                                    <option value="4"
                                                        {{ @$setting->home_slider_style == 4 ? 'selected' : '' }}>
                                                        Video Banner
                                                    </option>
                                                </select>
                                            </div>


                                            <div class="col-md-3 ecommerce-select2-dropdown">
                                                <label for="previewImage"
                                                       class="form-label mb-1 d-flex justify-content-between align-items-center">
                                                    <span>{{ _trans('keyword.Preview') }}</span>
                                                </label>
                                                <img id="previewImage" width="100%" src="" alt="">
                                            </div>
                                            {{-- Home slider style --}}
                                        @endif
                                        <div class="col-sm-12">
                                            <label class="form-label"
                                                   for="address">{{ _trans('keyword.Address') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <textarea name="address" id="address" class="form-control"
                                                          placeholder="24/A, Road-6, Miami"
                                                          rows="3">{{ @$setting->location }}</textarea>

                                            </div>

                                            <span class="text-danger addressError error"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"
                                                   for="phone">{{ _trans('keyword.Phone') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" id="phone" name="phone"
                                                       class="form-control" placeholder="+88754451415"
                                                       value="{{ @$setting->phone }}"/>
                                            </div>
                                            <span class="text-danger phoneError error"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"
                                                   for="email">{{ _trans('keyword.Email') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <input type="email" id="email" name="email"
                                                       class="form-control" value="{{ @$setting->email }}"
                                                       placeholder="housebrand@example.com"/>
                                            </div>

                                            <span class="text-danger emailError error"></span>

                                        </div>
                                        @if(Auth::user()->role_id != \App\Models\Role::MANUFACTURER && hasModulePermission('ecommerce-support'))
                                            <div class="col-md-6 mb-2">
                                                <h6 class="card-header mb-2">{{ _trans('keyword.Shop Status') }} <span
                                                        class="text-danger">*</span></h6>
                                                <div class="row shop-status-setting-section">
                                                    <div class="col-md-6">
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content"
                                                                   for="customRadioTempPublicUser">
                                                                <input type="radio" id="customRadioTempPublicUser"
                                                                       class="form-check-input"
                                                                       onchange="changeShopSettingStatus()"
                                                                    {{ $setting->shop_status == 1 ? 'checked' : '' }}>
                                                                <span class="custom-option-header">
                                                                <span class="h6 mb-0">Published</span>
                                                            </span>
                                                                <span class="custom-option-body">
                                                                <small>Note: Shop Published</small>
                                                            </span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content"
                                                                   for="customRadioTempUserWiseUser">
                                                                <input type="radio" id="customRadioTempUserWiseUser"
                                                                       class="form-check-input"
                                                                       onchange="changeShopSettingStatus()"
                                                                    {{ $setting->shop_status == 0 ? 'checked' : '' }}>
                                                                <span class="custom-option-header">
                                                                <span class="h6 mb-0">Unpublished</span>
                                                            </span>
                                                                <span class="custom-option-body">
                                                                <small>Note: Shop Unpublished</small>
                                                            </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-6 mb-2">
                                            <h6 class="card-header mb-2">{{ _trans('keyword.Product Setting') }}
                                                <span
                                                    class="text-danger">*</span></h6>
                                            <div class="row product-setting-section">
                                                <div class="col-md-6">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content"
                                                               for="customRadioTempPublic">
                                                            <input type="radio" id="customRadioTempPublic"
                                                                   class="form-check-input"
                                                                   onchange="changeProductSettingStatus()"
                                                                {{ $setting->product_setting == 0 ? 'checked' : '' }}>
                                                            <span class="custom-option-header">
                                                                <span class="h6 mb-0">Public</span>
                                                            </span>
                                                            <span class="custom-option-body">
                                                                <small>Note: Product Setting Public</small>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content"
                                                               for="customRadioTempUserWise">
                                                            <input type="radio" id="customRadioTempUserWise"
                                                                   class="form-check-input"
                                                                   onchange="changeProductSettingStatus()"
                                                                {{ $setting->product_setting == 1 ? 'checked' : '' }}>
                                                            <span class="custom-option-header">
                                                                <span class="h6 mb-0">User Wise</span>
                                                            </span>
                                                            <span class="custom-option-body">
                                                                <small>Note: Product setting User Wise</small>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <label class="form-label"
                                                   for="mapLocation">{{ _trans('keyword.Map') . ' ' . _trans('keyword.Location') }}</label>
                                            <div class="input-group input-group-merge">
                                                <textarea id="map_location" name="map_location" class="form-control"
                                                          placeholder="Iframe Map Location"
                                                          rows="3">{{ @$setting->map_location }}</textarea>
                                            </div>

                                            <span class="text-danger map_locationError error"></span>

                                        </div>

                                        @if (hasPermission('site_info_update'))
                                            <div class="col-12 d-flex justify-content-end">

                                                <button class="btn btn-primary" type="submit">
                                                    <span
                                                        class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                    <i class="ti ti-arrow-right"></i>
                                                    <span class="loader"></span>

                                                </button>
                                            </div>
                                        @endif
                                        <div class="col-12 d-flex justify-content-between d-none">
                                            <button class="btn btn-label-secondary btn-prev">
                                                <i class="ti ti-arrow-left me-sm-1"></i>
                                                <span class="align-middle d-sm-inline-block ">Previous</span>
                                            </button>
                                            <button class="btn btn-primary btn-next" type="button"
                                                    id="shop-setting-next">
                                                <span class="align-middle d-sm-inline-block  me-sm-1">Next</span>
                                                <i class="ti ti-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- Account Details -->

                            <!-- Personal Info -->
                            <div id="personal-info" class="content">
                                <form method="POST" id="logo-setting-form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">{{ _trans('keyword.Site') . ' ' . _trans('keyword.Logo') }}
                                        </h6>
                                        <small>{{ _trans('keyword.Enter Your Site Logo') }}</small>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-xl-6">
                                            <div class="card">
                                                <h5 class="card-header py-2 px-4">{{ _trans('keyword.Logo') }}</h5>
                                                <!-- Account -->
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start align-items-sm-center gap-2">
                                                        <img src="{{ getFilePath(@$setting->logo) }}"
                                                             onerror="this.onerror=null;this.src='{{ asset('assets/img/illustrations/page-pricing-enterprise.png') }}'"
                                                             alt="user-avatar"
                                                             class="d-block w-px-100 h-px-100 rounded"
                                                             id="lightLogo"/>

                                                        <div class="button-wrapper">
                                                            <label for="lightLogoInput"
                                                                   class="btn btn-primary me-2 mb-3 waves-effect waves-light"
                                                                   tabindex="0">
                                                                <span
                                                                    class="d-none d-sm-block">{{ _trans('keyword.Upload') }}</span>
                                                                <i class="ti ti-upload d-block d-sm-none"></i>
                                                                <input type="file" id="lightLogoInput"
                                                                       name="light_logo"
                                                                       class=" lightLogo-account-file-input"
                                                                       hidden=""
                                                                       accept="image/png, image/jpeg, image/jpg"/>
                                                            </label>
                                                            <button type="button"
                                                                    class="btn btn-label-secondary  mb-3 waves-effect lightLogo-account-image-reset">
                                                                <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                                                <span
                                                                    class="d-none d-sm-block">{{ _trans('keyword.Reset') }}</span>
                                                            </button>


                                                            <span class="text-danger light_logoError error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /Account -->
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="card">
                                                <h5 class="card-header py-2 px-4">{{ _trans('keyword.Favicon') }}</h5>
                                                <!-- Account -->
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start align-items-sm-center gap-2">

                                                        <img src="{{ getFilePath(@$setting->favicon) }}"
                                                             onerror="this.onerror=null;this.src='{{ asset('assets/img/illustrations/page-pricing-enterprise.png') }}'"
                                                             class="d-block w-px-100 h-px-100 rounded"
                                                             id="feviconLogo"/>
                                                        <div class="button-wrapper">
                                                            <label for="feviconLogoInput"
                                                                   class="btn btn-primary me-2 mb-3 waves-effect waves-light"
                                                                   tabindex="0">
                                                                <span
                                                                    class="d-none d-sm-block">{{ _trans('keyword.Upload') }}</span>
                                                                <i class="ti ti-upload d-block d-sm-none"></i>
                                                                <input type="file" id="feviconLogoInput"
                                                                       class="fevicon-account-file-input" name="favicon"
                                                                       hidden=""
                                                                       accept="image/png, image/jpeg, image/jpg">
                                                            </label>
                                                            <button type="button"
                                                                    class="btn btn-label-secondary fevicon-account-image-reset mb-3 waves-effect">
                                                                <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                                                <span
                                                                    class="d-none d-sm-block">{{ _trans('keyword.Reset') }}</span>
                                                            </button>


                                                            <span class="text-danger faviconError error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /Account -->
                                            </div>
                                        </div>

                                        @if (auth()->user()->role_id != App\Models\Role::MANUFACTURER)
                                            <div class="col-xl-6">
                                                <div class="card">
                                                    <h5 class="card-header py-2 px-4">
                                                        {{ _trans('keyword.Current') . ' ' . _trans('keyword.Banner') }}
                                                    </h5>
                                                    <!-- Account -->
                                                    <div class="card-body">
                                                        <div
                                                            class="d-flex align-items-start align-items-sm-center gap-2">
                                                            <img src="{{ getFilePath(@$setting->banner) }}"
                                                                 id="darkLogo"
                                                                 onerror="this.onerror=null;this.src='{{ asset('assets/img/illustrations/page-pricing-enterprise.png') }}'"
                                                                 alt="user-avatar"
                                                                 class="d-block w-px-100 h-px-100 rounded"/>

                                                            <div class="button-wrapper">
                                                                <label for="darkLogoInput"
                                                                       class="btn btn-primary me-2 mb-3 waves-effect waves-light"
                                                                       tabindex="0">
                                                                <span
                                                                    class="d-none d-sm-block">{{ _trans('keyword.Upload') }}</span>
                                                                    <i class="ti ti-upload d-block d-sm-none"></i>
                                                                    <input type="file" id="darkLogoInput"
                                                                           class="darkLogo-account-file-input"
                                                                           name="banner"
                                                                           hidden=""
                                                                           accept="image/png, image/jpeg, image/jpg">
                                                                </label>
                                                                <button type="button"
                                                                        class="btn btn-label-secondary darkLogo-account-image-reset mb-3 waves-effect">
                                                                    <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                                                    <span
                                                                        class="d-none d-sm-block">{{ _trans('keyword.Reset') }}</span>
                                                                </button>


                                                                <span class="text-danger dark_logoError error"></span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- /Account -->
                                                </div>
                                            </div>
                                        @endif

                                        @if (auth()->user()->role_id != App\Models\Role::MANUFACTURER && auth()->user()->role_id != App\Models\Role::DESIGNER)
                                            <div class="col-xl-6">
                                                <div class="card">
                                                    <h5 class="card-header py-2 px-4">{{ _trans('keyword.Page Loader') }}
                                                    </h5>
                                                    <!-- Account -->
                                                    <div class="card-body">
                                                        <div
                                                            class="d-flex align-items-start align-items-sm-center gap-2">

                                                            <img src="{{ getFilePath(@$setting->loader) }}"
                                                                 onerror="this.onerror=null;this.src='{{ asset('assets/img/illustrations/page-pricing-enterprise.png') }}'"
                                                                 class="d-block w-px-100 h-px-100 rounded"
                                                                 id="loader"/>
                                                            <div class="button-wrapper">
                                                                <label for="loaderInput"
                                                                       class="btn btn-primary me-2 mb-3 waves-effect waves-light"
                                                                       tabindex="0">
                                                                <span
                                                                    class="d-none d-sm-block">{{ _trans('keyword.Upload') }}</span>
                                                                    <i class="ti ti-upload d-block d-sm-none"></i>
                                                                    <input type="file" id="loaderInput"
                                                                           class="loader-account-file-input"
                                                                           name="loader"
                                                                           hidden=""
                                                                           accept="image/png, image/jpeg, image/jpg">
                                                                </label>
                                                                <button type="button"
                                                                        class="btn btn-label-secondary loader-account-image-reset mb-3 waves-effect">
                                                                    <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                                                    <span
                                                                        class="d-none d-sm-block">{{ _trans('keyword.Reset') }}</span>
                                                                </button>


                                                                <span class="text-danger loaderError error"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- /Account -->
                                                </div>
                                            </div>
                                        @endif

                                        @if (hasPermission('site_logo_update'))
                                            <div class="col-12 d-flex justify-content-between">
                                                <lable class="btn btn-label-secondary btn-prev">
                                                    <i class="ti ti-arrow-left me-sm-1"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                </lable>
                                                <button class="btn btn-primary" type="submit">
                                                    <span
                                                        class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                    <i class="ti ti-arrow-right"></i>
                                                    <span class="loader"></span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                            <!-- Address -->
                            <div id="addressForm" class="content">
                                <form method="POST" id="social-links-vertical-form">
                                    @csrf
                                    <div class="content-header mb-3">
                                        <h6 class="mb-0">{{ _trans('keyword.Social Links') }}</h6>
                                        <small>Enter Your
                                            {{ _trans('keyword.Enter') . ' ' . _trans('keyword.Your') . ' ' . _trans('keyword.Social Links') }}</small>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label"
                                                   for="twitter1">{{ _trans('keyword.X(twitter)') }}</label>
                                            <input type="text" id="twitter1" name="twitter" class="form-control"
                                                   value="{{ @$setting->twitter_url }}"
                                                   placeholder="https://twitter.com/abc"/>
                                            <span class="text-danger twitterError error"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"
                                                   for="facebook1">{{ _trans('keyword.Facebook') }}</label>
                                            <input type="text" id="facebook1" name="facebook"
                                                   class="form-control" value="{{ @$setting->facebook_url }}"
                                                   placeholder="https://facebook.com/abc"/>
                                            <span class="text-danger facebookError error"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"
                                                   for="facebook1">{{ _trans('keyword.Instagram') }}</label>
                                            <input type="text" id="instagram" name="instagram"
                                                   class="form-control" value="{{ @$setting->instagram_url }}"
                                                   placeholder="https://instagram.com/abc"/>
                                            <span class="text-danger instagramError error"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"
                                                   for="linkedin1">{{ _trans('keyword.Linkedin') }}</label>
                                            <input type="text" id="linkedin1" name="linkedin"
                                                   class="form-control" value="{{ @$setting->linkedin }}"
                                                   placeholder="https://linkedin.com/abc"/>
                                            <span class="text-danger linkedinError error"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"
                                                   for="linkedin1">{{ _trans('keyword.Youtube') }}</label>
                                            <input type="text" id="youtube" name="youtube" class="form-control"
                                                   value="{{ @$setting->youtube_url }}"
                                                   placeholder="https://youtube.com/abc"/>
                                            <span class="text-danger youtubeError error"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label"
                                                   for="linkedin1">{{ _trans('keyword.Tiktok') }}</label>
                                            <input type="text" id="tiktok" name="tiktok" class="form-control"
                                                   value="{{ @$setting->tiktok_url }}"
                                                   placeholder="https://tiktok.com/abc"/>
                                            <span class="text-danger tiktokError error"></span>
                                        </div>

                                        @if (hasPermission('social_links_update'))
                                            <div class="col-12 d-flex justify-content-between">
                                                <lable class="btn btn-label-secondary btn-prev">
                                                    <i class="ti ti-arrow-left me-sm-1"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                </lable>
                                                <button class="btn btn-primary" type="submit">
                                                    <span
                                                        class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                    <i class="ti ti-arrow-right"></i>
                                                    <span class="loader"></span>

                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                            <!-- Social Links -->
                            <div id="social-links" class="content">
                                <!-- Terms & Polices -->
                                <div>
                                    <h6 class="mb-3">{{ _trans('keyword.Terms & Polices') }}</h6>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <small>{{ _trans('keyword.Shipping Policy') }}</small>
                                            <div class="form-control p-0 pt-1">
                                                <div class="shipping-policy-toolbar border-0 border-bottom">
                                                    <div class="d-flex justify-content-start">
                                                        <span class="ql-formats me-0">
                                                            <button class="ql-bold"></button>
                                                            <button class="ql-italic"></button>
                                                            <button class="ql-underline"></button>
                                                            <button class="ql-list" value="ordered"></button>
                                                            <button class="ql-list" value="bullet"></button>
                                                            <button class="ql-link"></button>
                                                            {{-- <button class="ql-image"></button> --}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="shipping_policy border-0 pb-4"
                                                     id="shipping_policy-description">
                                                    {!! @$setting->shipping_policy !!}
                                                </div>
                                            </div>
                                            <span class="text-danger shippingPolicyError error"></span>
                                        </div>
                                        <div class="col-6">
                                            <small>{{ _trans('keyword.Return Policy') }}</small>
                                            <div class="form-control p-0 pt-1">
                                                <div class="return-policy-toolbar border-0 border-bottom">
                                                    <div class="d-flex justify-content-start">
                                                        <span class="ql-formats me-0">
                                                            <button class="ql-bold"></button>
                                                            <button class="ql-italic"></button>
                                                            <button class="ql-underline"></button>
                                                            <button class="ql-list" value="ordered"></button>
                                                            <button class="ql-list" value="bullet"></button>
                                                            <button class="ql-link"></button>
                                                            {{-- <button class="ql-image"></button> --}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="return_policy border-0 pb-4"
                                                     id="return_policy-description">
                                                    {!! @$setting->return_policy !!}
                                                </div>
                                            </div>
                                            <span class="text-danger returnPolicyError error"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div>
                                            <small>{{ _trans('keyword.Disclaimer') }}</small>
                                            <div class="form-control p-0 pt-1">
                                                <div class="disclaimer-toolbar border-0 border-bottom">
                                                    <div class="d-flex justify-content-start">
                                                        <span class="ql-formats me-0">
                                                            <button class="ql-bold"></button>
                                                            <button class="ql-italic"></button>
                                                            <button class="ql-underline"></button>
                                                            <button class="ql-list" value="ordered"></button>
                                                            <button class="ql-list" value="bullet"></button>
                                                            <button class="ql-link"></button>
                                                            {{-- <button class="ql-image"></button> --}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="disclaimer border-0 pb-4" id="disclaimer-description">
                                                    {!! @$setting->disclaimer !!}
                                                </div>
                                            </div>
                                            <span class="text-danger disclaimerError error"></span>
                                        </div>
                                    </div>
                                </div>


                                @if (hasPermission('terms_and_policies_update'))
                                    <div class="col-12 d-flex justify-content-between mt-3">
                                        <lable class="btn btn-label-secondary btn-prev">
                                            <i class="ti ti-arrow-left me-sm-1"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </lable>
                                        <button id="add-terms-polices" class="btn btn-primary" type="button">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                            <i class="ti ti-arrow-right"></i>
                                            <span class="loader"></span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div id="introduction-message" class="content">
                                <div class="row">
                                    <div class="container-fluid py-2" id="introduction-message-container"
                                         style="max-height: 500px; overflow-y: auto">
                                        <div class="text-center">
                                            <h2>Welcome to House Brand</h2>
                                            <p class="text-muted">Manage your products, projects, tasks, time billing,
                                                and
                                                orders efficiently.</p>
                                        </div>

                                        <section class="mb-5">
                                            <h3>Product Management</h3>
                                            <p>Manage your products efficiently with features to add, edit, and organize
                                                them.</p>
                                            <ul>
                                                <li>Add new products with detailed descriptions</li>
                                                <li>Edit existing product details</li>
                                                <li>Organize products into categories</li>
                                            </ul>
                                        </section>

                                        <section class="mb-5">
                                            <h3>Project & Task Management</h3>
                                            <p>Keep track of your projects and tasks while efficiently managing time
                                                billing.</p>
                                            <ul>
                                                <li>Create and assign tasks to employees</li>
                                                <li>Monitor project progress</li>
                                                <li>Track time spent on each task</li>
                                            </ul>
                                        </section>

                                        <section class="mb-5">
                                            <h3>Orders & Payments</h3>
                                            <p>Setup a payment gateway to receive online payments and manage orders
                                                effectively.</p>
                                            <ul>
                                                <li>Process customer orders smoothly</li>
                                                <li>Enable multiple payment methods</li>
                                                <li>Track payment statuses</li>
                                            </ul>
                                        </section>

                                        <section class="mb-5">
                                            <h3>Employee Management</h3>
                                            <p>Manage your employees, roles, and permissions to maintain organizational
                                                control.</p>
                                            <ul>
                                                <li>Assign roles and permissions</li>
                                                <li>Monitor employee activity</li>
                                                <li>Streamline workforce management</li>
                                            </ul>
                                        </section>

                                        <section class="mb-5">
                                            <h3>Portfolio & Gallery</h3>
                                            <p>Customize your frontend shop with portfolio, inspiration, and gallery
                                                sections.</p>
                                            <ul>
                                                <li>Showcase products creatively</li>
                                                <li>Manage portfolio images</li>
                                                <li>Enhance branding with inspiration themes</li>
                                            </ul>
                                        </section>

                                        <section class="mb-5">
                                            <h3>Events & Notices</h3>
                                            <p>Manage all important events, documents, and notices from this panel.</p>
                                            <ul>
                                                <li>Schedule and publish events</li>
                                                <li>Maintain and share important documents</li>
                                                <li>Notify employees and customers effectively</li>
                                            </ul>
                                        </section>

                                        <section class="mb-5">
                                            <h3>Reports & Analytics</h3>
                                            <p>Gain insights by analyzing reports on every aspect of your shop data.</p>
                                            <ul>
                                                <li>View sales and revenue reports</li>
                                                <li>Analyze employee performance</li>
                                                <li>Track customer engagement trends</li>
                                            </ul>
                                        </section>

                                        <section class="mb-5">
                                            <h3>Customization & Branding</h3>
                                            <p>Customize the color theme of both your frontend shop and backend
                                                panel.</p>
                                            <ul>
                                                <li>Choose from predefined color themes or create your own</li>
                                                <li>Ensure consistency between your shop’s branding and UI</li>
                                                <li>Apply different themes for different user roles</li>
                                            </ul>
                                        </section>

                                        <section class="mb-5">
                                            <h3>Shop Setup & Profile</h3>
                                            <p>It is recommended to add a logo for your shop and complete all necessary
                                                details such as profile and shop data.</p>
                                            <ul>
                                                <li>Upload a shop logo for brand identity</li>
                                                <li>Complete your shop profile with contact details and descriptions
                                                </li>
                                                <li>Ensure all settings are configured for a smooth shopping
                                                    experience
                                                </li>
                                            </ul>
                                        </section>
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                                                    aria-label="Close">Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <!-- /Modern Wizard -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- form submit ajax --}}

    <script>
        $('#system-info-setting-form').on('submit', function (e) {
            e.preventDefault();

            loader.show();
            submitButton.prop('disabled', true);

            $('.error').text('');
            $.ajax({
                url: '{{ route('setting.shop-setting.system_info.store') }}',
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    // element = $('input[name="element_name"]');
                    shop_name: $('#shop_name').val(),
                    styleList: $('#styleList').val(),
                    address: $('#address').val(),
                    phone: $('#phone').val(),
                    email: $('#email').val(),
                    map_location: $('#map_location').val(),
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
                        $('#shop-setting-next').click();
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

            var lightLogo = ($('#lightLogoInput').length && $('#lightLogoInput').prop('files')?.[0]) || '';
            var darkLogo = ($('#darkLogoInput').length && $('#darkLogoInput').prop('files')?.[0]) || '';
            var fevicon = ($('#feviconLogoInput').length && $('#feviconLogoInput').prop('files')?.[0]) || '';
            var siteLoader = ($('#loaderInput').length && $('#loaderInput').prop('files')?.[0]) || '';

            formData.append('light_logo', lightLogo);
            formData.append('banner', darkLogo);
            formData.append('favicon', fevicon);
            formData.append('loader', siteLoader);
            formData.append('_token', "{{ csrf_token() }}");

            loader.show();
            submitButton.prop('disabled', true);


            $('.error').text('');
            $.ajax({
                url: '{{ route('setting.shop-setting.site_logo.store') }}',
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
                        $('.loaderError').text(response.errors?.loader ? response.errors?.loader[0] :
                            '');
                    } else if (response.status == 200) {
                        toastr.success(response.message);
                        $('#shop-setting-next').click();
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


        $('#social-links-vertical-form').on('submit', function (e) {
            e.preventDefault();

            loader.show();
            submitButton.prop('disabled', true);

            $('.error').text('');
            $.ajax({
                url: '{{ route('setting.shop-setting.social_link.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    twitter: $('#twitter1').val(),
                    facebook: $('#facebook1').val(),
                    instagram: $('#instagram').val(),
                    linkedin: $('#linkedin1').val(),
                    youtube: $('#youtube').val(),
                    tiktok: $('#tiktok').val(),
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
                        $('#shop-setting-next').click();
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

        $('#add-terms-polices').click(function (e) {
            e.preventDefault();
            loader.show();
            submitButton.prop('disabled', true);
            $.ajax({
                url: '{{ route('setting.shop-setting.terms_polices.store') }}',
                type: 'POST',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'shipping_policy': $('#shipping_policy-description').children().first().html(),
                    'disclaimer': $('#disclaimer-description').children().first().html(),
                    'return_policy': $('#return_policy-description').children().first().html(),
                },
                success: function (response) {
                    if (response.status === 403) {
                        toastr.error(response.message);
                    } else if (response.status === 200) {
                        toastr.success(response.message);
                        $('#shop-setting-next').click();
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
    </script>
    {{-- form submit ajax --}}

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


        let loaderImage = document.getElementById('loader');
        const loaderFileInput = document.querySelector('.loader-account-file-input'),
            resetloaderFileInput = document.querySelector('.loader-account-image-reset');

        if (loaderImage) {
            const resetImage = loaderImage.src;
            loaderFileInput.onchange = () => {
                if (loaderFileInput.files[0]) {
                    loaderImage.src = window.URL.createObjectURL(loaderFileInput.files[0]);
                }
            };
            resetloaderFileInput.onclick = () => {
                loaderFileInput.value = '';
                loaderImage.src = resetImage;
            };
        }


        function changeProductSettingStatus() {
            const formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");

            Swal.fire({
                title: 'Are you sure?',
                text: "To change the status",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Change it',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('setting.shop-setting.product-setting.changeStatus') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 200) {
                                toastr.success(response.message);
                                $(".product-setting-section").load(location.href +
                                    " .product-setting-section");
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function (error) {
                            $(".product-setting-section").load(location.href +
                                " .product-setting-section");
                            toastr.error(error.responseJSON.message);
                        }
                    });
                } else {
                    $(".product-setting-section").load(location.href + " .product-setting-section");
                }

            });
        }

        function changeShopSettingStatus() {
            const formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");

            Swal.fire({
                title: 'Are you sure?',
                text: "To change the status",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Change it',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('setting.shop-setting.shop-setting.changeStatus') }}',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            if (response.status === 200) {
                                toastr.success(response.message);
                                console.log('here1');
                                $(".shop-status-setting-section").load(location.href +
                                    " .shop-status-setting-section");
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function (error) {
                            $(".shop-status-setting-section").load(location.href +
                                " .shop-status-setting-section");
                            toastr.error(error.responseJSON.message);
                        }
                    });
                } else {
                    $(".shop-status-setting-section").load(location.href + " .shop-status-setting-section");
                }

            });
        }

        // Home Slider Style Preview
        function PreviewHomeStyle() {
            const styleList = document.getElementById('styleList');
            const previewImage = document.getElementById('previewImage');

            const imageMap = {
                1: '{{ asset('assets/img/slider_style/slider_style_1.gif') }}',
                2: '{{ asset('assets/img/slider_style/slider_style_2.gif') }}',
                3: '{{ asset('assets/img/slider_style/slider_style_3.gif') }}'
            };

            const selectedValue = styleList.value;
            previewImage.src = imageMap[selectedValue] || imageMap[1];
        }

        PreviewHomeStyle();
    </script>
@endpush


<style>
    #introduction-message-container::-webkit-scrollbar {
        width: 5px;
    }

    /* scrollbar track */
    #introduction-message-container::-webkit-scrollbar-track {
        background: #eee;
    }

    /* scrollbar handle */
    #introduction-message-container::-webkit-scrollbar-thumb {
        border-radius: 1rem;
        background-color: var(--color-bg-primary);
        background-image: linear-gradient(to top, var(--color-primary) 0%, var(--color-secondary) 100%);
    }
</style>
