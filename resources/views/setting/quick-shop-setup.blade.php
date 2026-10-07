@php use App\Models\Role; @endphp
@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Quick Setup'))


<style>
    .theme-setup-btn.attention {
        border: 2px dashed #7367f0;
        background: linear-gradient(135deg, #f5f4ff, #ffffff);
        box-shadow: 0 0 0 rgba(115, 103, 240, 0.4);
        animation: themePulse 2s infinite;
        overflow: visible !important;
    }

    .theme-setup-btn.attention:hover {
        box-shadow: 0 0 20px rgba(115, 103, 240, 0.4);
        transform: translateY(-1px);
    }

    /* Floating badge */
    .theme-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ff4d4f;
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 999px;
        box-shadow: 0 4px 10px rgba(255, 77, 79, 0.4);
        animation: badgeBounce 1.5s infinite;
    }

    /* Pulse animation */
    @keyframes themePulse {
        0% {
            box-shadow: 0 0 0 0 rgba(115, 103, 240, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(115, 103, 240, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(115, 103, 240, 0);
        }
    }

    /* Badge bounce */
    @keyframes badgeBounce {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

</style>
@section('content')
    <div id="chat-loading" class="vue-loading">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading...</p>
        </div>
    </div>
    <div class="col-12 mb-4" id="quick-shop-setup-app" v-cloak>
        {!! breadcrumb(_trans('keyword.Quick Shop Setup') . ' ', [
            '#' => _trans('keyword.System') . ' ' . _trans('keyword.Settings'),
            'setting' => _trans('keyword.Quick Shop Setup'),
        ]) !!}
        <div class="d-flex mb-2 justify-content-center align-items-end">
            <div class="">
                <div class="d-flex gap-3">
                    <div>
                        <a class="btn btn-muted" target="_blank"
                           v-if="{{ auth()->user()->role_id !== \App\Models\Role::SUPER_ADMIN ? 'true' : 'false' }}"
                           href="{{env('APP_FRONTEND_URL').'/designer/'.shopSetting()->slug}}"><i
                                class="ti ti-player-play me-1 px-0"></i>Preview</a>
                    </div>
                    {{--                    <div>--}}
                    {{--                        <button class="btn btn-muted bg-label-primary me-1"><i class="ti ti-device-imac"></i></button>--}}
                    {{--                        <button class="btn btn-muted "><i class="ti ti-device-mobile"></i></button>--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-9">
                <div class="col-12 mb-4">
                    <div class="bs-stepper  ">
                        <div class="bs-stepper-header">
                            <div class="step" :class="{ active: section === item.value }"
                                 v-for="(item, index) in sections" @click="setSection(item.value)">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">@{{ index+1 }}</span>
                                    <span class="bs-stepper-label">
                                      <span class="bs-stepper-title">@{{ item.label }}<i v-if="sections.length!=index+1"
                                                                                         class="ti ti-chevron-right"></i></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="bs-stepper-content" style="max-height: 100vh; overflow-y: auto; overflow-x: hidden;"
                             ref="stepperContent">
                            <header-content
                                :shop-setting="shopSetting"
                                :color-theme="colorTheme"
                            ></header-content>

                            <main>
                                <!-- 1. Hero Section -->
                                <div ref="heroSectionRef">
                                    <hero-content
                                        :key="heroContentKey"
                                        v-if="shopSetting"
                                        :shop-setting="shopSetting">
                                    </hero-content>
                                </div>

                                <!-- 2. Browse By Category -->
                                <div ref="categorySectionRef">
                                    <category-content
                                        v-if="shopSetting"
                                        :shop-setting="shopSetting"
                                        :color-theme="colorTheme">
                                    </category-content>
                                </div>

                                <!-- 3. Products -->
                                <div ref="productSectionRef">
                                    <product-content v-if="shopSetting"
                                                     :shop-setting="shopSetting"
                                                     :color-theme="colorTheme"
                                    ></product-content>
                                </div>

                                <!-- 4. Inspiration -->
                                <div ref="inspirationSectionRef">
                                    <inspiration-content
                                        :key="inspirationContentKey"
                                        v-if="shopSetting"
                                        :shop-setting="shopSetting"
                                        :color-theme="colorTheme">
                                    </inspiration-content>
                                </div>

                                <!-- 5. Portfolio -->
                                <div ref="portfolioSectionRef">
                                    <portfolio-content
                                        v-if="shopSetting"
                                        :key="portfolioContentKey"
                                        :shop-setting="shopSetting"
                                        :color-theme="colorTheme">
                                    </portfolio-content>
                                </div>

                                <!-- 6. Gallery -->
                                <div ref="gallerySectionRef">
                                    <gallery-content
                                        :key="galleryContentKey"
                                        v-if="shopSetting"
                                        :shop-setting="shopSetting"
                                        :color-theme="colorTheme">
                                    </gallery-content>
                                </div>
                            </main>

                            <!-- Footer -->
                            <footer-content
                                :color-theme="colorTheme"
                                app-url="{{ env('APP_FRONTEND_URL') }}"
                                is-super-admin="{{ auth()->user()->role_id === Role::SUPER_ADMIN ? 'true' : 'false' }}"
                            ></footer-content>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <h5 class="card-header">Shop Setting</h5>
                    <div class="card-body">
                        <div class="mb-3 w-100">
                            <button class="btn btn-label-primary position-relative  @if(
                                    shopSetting()->shop_name == null ||
                                    shopSetting()->logo == null ||
                                    shopSetting()->location == null ||
                                    shopSetting()->phone == null ||
                                    checkIfStripeIsSetup(getUserId()) == false
                                ) theme-setup-btn attention visible  @endif"
                                    type="button" data-bs-toggle="modal"
                                    data-bs-target="#multiStepModal">
                                <i class="ti ti-home-cog me-1"></i>
                                Shop Setting
                                @if(
                                    shopSetting()->shop_name == null ||
                                    shopSetting()->logo == null ||
                                    shopSetting()->location == null ||
                                    shopSetting()->phone == null ||
                                    checkIfStripeIsSetup(getUserId()) == false
                                )
                                    <span class="theme-badge">Setup Required</span>
                                @endif
                            </button>
                            <button class="btn btn-label-primary position-relative"
                                    type="button" data-bs-toggle="modal"
                                    data-bs-target="#colorThemeModal">

                                <i class="ti ti-palette me-1"></i>
                                Theme Setting
                                <!-- Attention Badge -->
                            </button>

                        </div>


                        <div class="mb-3 w-100 d-flex justify-content-between gap-2">

                            {{-- Email Setting --}}
                            <a href="{{ route('setting.email-setting.index') }}"
                               class="btn btn-label-primary position-relative  @if (!$emailSetting) theme-setup-btn attention visible @endif">
                                <i class="ti ti-mail-cog me-1"></i>
                                Email Setting
                                @if (!$emailSetting)
                                    <span class="theme-badge">Setup Required</span>
                                @endif
                            </a>

                            {{-- Payment Setting --}}
                            <a href="{{ route('setting.payment-method.index') }}"
                               class="btn btn-label-primary position-relative @if ($paymentMethodStatus->isEmpty()) theme-setup-btn attention visible @endif">
                                <i class="ti ti-credit-card me-1"></i>
                                Payment Setting
                                @if ($paymentMethodStatus->isEmpty())
                                    <span class="theme-badge">Setup Required</span>
                                @endif
                            </a>

                        </div>
                        <div class="w-100 mb-3">
                            <select class="form-select w-100 bg-label-primary" v-model="section"
                                    @change="setSection(section)">
                                <option v-for="item in sections" :key="item.value" :value="item.value">
                                    @{{ item.label }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <keep-alive>
                                <component v-if="shopSetting" v-bind:is="section" :shop-setting="shopSetting"
                                           v-on:proceed-to-next="nextStep"
                                           v-on:update-content="updateContent"
                                ></component>
                            </keep-alive>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-initial-setting-modal v-cloak/>

    <div class="modal fade" id="colorThemeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Color Theme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modern Wizard -->
                    <div class="col-12">
                        <x-color-theme/>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module" src="{{asset(mix('js/quick-shop-setting/index.js'))}}"></script>
@endpush


