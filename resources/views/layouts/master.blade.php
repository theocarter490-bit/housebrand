@include('layouts.header')

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

        @include('layouts.sidebar')

        <!-- Layout container -->
        <div class="layout-page">

            @include('layouts.topbar')

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->

                <div class="container-fluid flex-grow-1 container-p-y">
                    <div class="">
                        @if (shopSetting()->shop_name == null || shopSetting()->logo == null || shopSetting()->location == null || shopSetting()->phone == null || checkIfStripeIsSetup(getUserId()) == false)
                            <x-alert-message/>
                        @endif
                        @yield('content')
                    </div>
                </div>


                @include('layouts.footer')

                <div class="content-backdrop fade"></div>
            </div>
            <!-- / Layout page -->
        </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
</div>
@include('layouts.footer_script')

