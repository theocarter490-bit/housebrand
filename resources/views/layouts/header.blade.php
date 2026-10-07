<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="{{asset('assets/')}}" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ config('app.name',  @generalSetting()->site_name) }} | @yield('title')
    </title>

    <meta name="description" content=""/>
    <meta name="_token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{@getFilePath(shopSetting()->favicon)}}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>

    <!-- PWA  -->
      <meta name="theme-color" content="#6777ef"/>
      <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
      <link rel="manifest" href="{{ asset('/manifest.json') }}">


    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/rtl/core.css'))}}"/>
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/rtl/theme-default.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/fonts/fontawesome.css'))}}">

    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/fonts/tabler-icons.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/fonts/flag-icons.css'))}}">

    <link rel="stylesheet" href="{{ asset(mix('assets/css/demo.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/css/main.css'))}}">

    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/node-waves/node-waves.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/typeahead-js/typeahead.css'))}}">

    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/@form-validation/umd/styles/index.min.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css'))}}">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap">

    <link rel="stylesheet" href="{{ asset(mix('/assets/vendor/libs/apex-charts/apex-charts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/swiper/swiper.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css'))}}">
    <link rel="stylesheet"
          href="{{ asset(mix('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css'))}}">
    <link rel="stylesheet"
          href="{{ asset(mix('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css'))}}">

    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/cards-advance.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/flatpickr/flatpickr.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/select2/select2.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/animate-css/animate.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/sweetalert2/sweetalert2.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/bootstrap-select/bootstrap-select.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/bs-stepper/bs-stepper.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/quill/typography.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/quill/katex.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/quill/editor.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/app-ecommerce.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/css/custom.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/dropzone/dropzone.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/tagify/tagify.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/toastr/toastr.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/app-chat.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/jkanban/jkanban.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/app-kanban.css'))}}">

    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-misc.css'))}}">

    <link rel="stylesheet" href="{{ url('/css/custom-style.css') }}">

    @stack('styles')
</head>
<body>

@include('layouts.preloader')
