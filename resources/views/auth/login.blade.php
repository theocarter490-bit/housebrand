<!DOCTYPE html>

<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title> {{ @generalSetting()->site_name }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{@getFilePath(shopSetting()->favicon)}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />


    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/rtl/core.css')) }}" />
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/rtl/theme-default.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/fonts/fontawesome.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/fonts/tabler-icons.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/fonts/flag-icons.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('assets/css/demo.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/css/main.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/node-waves/node-waves.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/typeahead-js/typeahead.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/@form-validation/umd/styles/index.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap">

    <link rel="stylesheet" href="{{ asset(mix('/assets/vendor/libs/apex-charts/apex-charts.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/swiper/swiper.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')) }}">
    <link rel="stylesheet"
        href="{{ asset(mix('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')) }}">
    <link rel="stylesheet"
        href="{{ asset(mix('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')) }}">
    <link rel="stylesheet"
        href="{{ asset(mix('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css')) }}">


</head>

<?php
if (empty(loginBG())) {
    $css = "background: url('/public/backend/images/settings/body-bg.jpg')  no-repeat center; background-size: cover; ";
} else {
    if (!empty(loginBG()->image)) {
        $css = "background: url('" . getFilePath(loginBG()->image) . "')  no-repeat center; background-size: cover; ";
    } else {
        $css = 'background:' . loginBG()->color;
    }
}
?>

<body style="{{ $css }}">
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="py-4">
                <!-- Login -->
                <div class="card" style="max-width: 480px; margin: 0 auto;">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="#" class="app-brand-link gap-2">
                                <img src="{{ @getFilePath(shopSetting()->logo) }}" alt="House Brand" height="60px"
                                    width="auto">
                            </a>
                        </div>
                        <!-- /Logo -->
                        @if(session('error_login'))
                            <h3 class="text-danger text-center">{{ session('error_login') }}</h3>
                        @endsession

                        <h4 class="mb-1 pt-2">Welcome to {{ @generalSetting()->site_name }}!</h4>
                        <p class="mb-4">Please sign-in to your account and manage your store</p>

                        <form id="" class="mb-3" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email or Username</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}" placeholder="Enter your email" autofocus />
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    {{-- <a href="auth-forgot-password-basic.html">
                                        <small>Forgot Password?</small>
                                    </a> --}}
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer" id="c-toggle-password">
                                        <i class="ti ti-eye-off" id="toggle-icon"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            @error('message')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="mb-3">
                                <div class=" d-flex gap-2">
                                    <input type="checkbox" name="remember_me" value="1">
                                    <label class="form-check-label" for="remember-me">Remember Me</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-rounded btn-info text-nowrap w-100" type="submit">Sign
                                    In</button>
                            </div>
                        </form>

                        {{-- demo login  --}}
                        @if (config('app.demo_mode'))
                            @include('auth.demo_users')
                        @endif
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <script>
        document.getElementById('c-toggle-password').addEventListener('click', function() {
            let passwordInput = document.getElementById('password');
            let icon = document.getElementById('toggle-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            }
        });
    </script>
</body>

</html>
