@include('layouts.header')

<body>
    <!-- Content -->

    <!-- Not Authorized -->
    <div class="container-xxl ">
        <div class="misc-wrapper">
            <h2 class="mb-1">You are not subscribed!</h2>
            <p class="mb-4 mx-2">
                You do not have permission to view this page using the credentials that you have provided while login.
                <br />
                Please contact your site administrator or renew the plan.
            </p>

            <div class="card px-3">
                <div class="row">
                    <div class="col-lg-7 card-body border-end">

                        <h4 class="mt-2 mb-1">Plan List</h4>
                        <p class="mb-2">
                            All plans include 40+ advanced tools and features to boost your product. <br />
                            Choose the best plan to fit your needs.
                        </p>
                        {{-- @foreach ($plans as $index=>$plan)
                            <div class="plan
                            form-check custom-option custom-option-basic {{$index==0?'checked':''}}"
                                role="button" data-object = "{{ $plan }}">
                                <label
                                    class="form-check-label custom-option-content form-check-input-payment d-flex gap-3 align-items-center"
                                    for="plan{{ $plan->id }}">
                                    <input name="planRadio" class="form-check-input" type="radio"  {{$index==0?'checked':''}}
                                        value="{{ $plan->id }}" id="plan{{ $plan->id }}" required />
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 w-100">
                                        <div class="text-start">
                                            <h5 class="mb-0">{{ $plan->name }}</h5>
                                            <p class="mb-0">{!! $plan->description !!}</p>
                                        </div>
                                        <div class="ms-2">${{ $plan->setup_fee }}/{{ $plan->plan_type }}</div>
                                    </div>
                                </label>

                            </div>
                        @endforeach --}}

                        @foreach ($plans as $index => $plan)
                            <div class="plan form-check custom-option custom-option-basic position-relative {{$index==0?'checked':''}}"
                                role="button" data-object="{{ $plan }}">

                                {{-- Modern Top-Right Badge --}}
                                {{-- @if ($user->lastSubscription->plan_id == $plan->id)
                                    <div class="current-plan-badge">
                                        <i class="ti ti-star-filled me-1"></i> Current Plan
                                    </div>
                                @endif --}}

                                <label
                                    class="form-check-label custom-option-content form-check-input-payment d-flex gap-3 align-items-center"
                                    for="plan{{ $plan->id }}">
                                    <input name="planRadio" class="form-check-input" type="radio"
                                        {{$index==0?'checked':''}}
                                        value="{{ $plan->id }}" id="plan{{ $plan->id }}" required />
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-2 p-2 w-100">
                                        <div class="text-start">
                                            <h5 class="mb-0">{{ $plan->name }}</h5>
                                            <p class="mb-0">{!! $plan->description !!}</p>
                                            <ul class="m-0 p-0">
                                                @foreach (json_decode($plan->modules) as $module)
                                                    <li style="list-style: none;">
                                                        <i class="ti ti-check text-success"></i>
                                                        {{ ucfirst(str_replace('-', ' ', $module->slug)) }} :
                                                        {{ $module->limit }}
                                                    </li>
                                                @endforeach
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
                            <p class="mb-1" id="plan_name">A simple start for everyone</p>
                            <div class="d-flex align-items-center">
                                <h1 class="text-heading display-5 mb-1"><span id="plan_price"></span></h1>
                                <sub>/<span id="plan_period"></span></sub>
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
                                @foreach ($paymentMethods as $index=>$paymentMethod)
                                    <div class="col-md mb-md-0 mb-2">
                                        <div class="form-check custom-option custom-option-basic {{$index==0?'checked':''}}">
                                            <label
                                                class="form-check-label custom-option-content form-check-input-payment d-flex gap-3 align-items-center"
                                                for="{{ $paymentMethod['name'] }}">
                                                <input name="customRadioTemp" class="form-check-input" type="radio"
                                                    value="{{ $paymentMethod['id'] }}" id="{{ $paymentMethod['name'] }}"
                                                    required {{$index==0?'checked':''}} />
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
                                    <button class="btn btn-primary" type="submit" id="buy_plan">
                                        <span class="me-2">Proceed with Payment</span>
                                        <i class="ti ti-arrow-right scaleX-n1-rtl"></i>
                                    </button>
                                </div>
                            </form>

                            <p class="mt-4 pt-2">
                                By continuing, you accept to our Terms of Services and Privacy Policy. Please note that
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

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{-- <div class="container-fluid misc-bg-wrapper">
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" alt="page-misc-not-authorized"
            data-app-light-img="illustrations/bg-shape-image-light.png"
            data-app-dark-img="illustrations/bg-shape-image-dark.png" />
    </div> --}}
    <!-- /Not Authorized -->


    <section class="section-py bg-body first-section-pt">
        <div class="container mt-2">

        </div>
    </section>

    <!-- Page JS -->
</body>
@include('layouts.footer_script')


<script>
    $(document).on('click', '.plan', function() {
        let data = $(this).attr("data-object");
        data = JSON.parse(data);
        console.log(data);
        $('#plan_name').text(`${data.name}`);
        $('#plan_price').text(`$${data.price}`);
        $('#subtotal_plan_price').text(`$${data.price}`);
        $('#one_time_fee').text(`$${data.setup_fee}`);
        $('#plan_period').text(`${data.plan_type}`);
        $('#total_price').text(`$${data.price + data.setup_fee}`);
        $('#plan_id').val(data.id);
    });

    $('.plan')[0].click();
</script>


</html>
