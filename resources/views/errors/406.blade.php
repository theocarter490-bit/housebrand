@include('layouts.header')

<body>
<!-- Content -->

<div class="container-xxl container-p-y">
    <div class="misc-wrapper">
        <h2 class="mb-1 mt-4">You are not subscribed!</h2>
        <p class="mb-4 mx-2">Please contact with admin or buy a new plan.</p>
        <a href="{{route('subscription.plan.buyPlan')}}" class="btn btn-primary mb-4">Buy Plan</a>
        <div class="mt-4">
            <img src="{{ asset('assets/img/illustrations/auth-register-multisteps-illustration.png') }}" alt="page-misc-error" width="225"
                 class="img-fluid" />
        </div>
    </div>
</div>
<div class="container-fluid misc-bg-wrapper">
    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" alt="page-misc-error"
         data-app-light-img="illustrations/bg-shape-image-light.png"
         data-app-dark-img="illustrations/bg-shape-image-dark.png" />
</div>

<!-- Page JS -->
</body>
@include('layouts.footer_script')

</html>
