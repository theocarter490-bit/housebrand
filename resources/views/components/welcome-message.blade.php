<div class="col-xl-4 col-12 mb-4">
    <div class="card border-0 position-relative overflow-hidden text-white"
         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 1rem;">

        <div class="position-absolute top-0 end-0 bg-white opacity-10 rounded-circle"
             style="width: 150px; height: 150px; margin-right: -50px; margin-top: -50px;"></div>

        <div class="card-body position-relative z-1 p-4">
            <h4 class="text-white fw-bold mb-1">Welcome back, {{ auth()->user()->name }} 👋🏻</h4>
            <p class="text-white opacity-75 mb-4 small">
                Your progress is awesome! Keep it up for rewards.
            </p>

            <div class="row g-3">
                <div class="col-6">
                    <div class="d-flex align-items-center bg-white rounded p-3 h-100 shadow-sm">
                        <span class="bg-label-primary p-2 rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="ti ti-device-laptop fs-4"></i>
                        </span>
                        <div>
                            <p class="mb-0 small  text-dark">Orders</p>
                            <h5 class="mb-0 fw-bold text-dark">{{ @$data['orderCount'] }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="d-flex align-items-center bg-white rounded p-3 h-100 shadow-sm">
                        <span class="bg-label-info p-2 rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="ti ti-bulb fs-4"></i>
                        </span>
                        <div>
                            <p class="mb-0 small text-dark">Amount</p>
                            <h5 class="mb-0 fw-bold text-dark">{{ getPriceFormat(@$data['orderAmount']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
