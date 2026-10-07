<div class=" mb-4 col-xl-4 col-12">
    <div class="card h-100 border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 1rem;">
        <div class="d-flex align-items-center row h-100 g-0 position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-25" style="font-size: 5rem; line-height: 0;">🎉</div>
            <div class="col-7 z-1">
                <div class="card-body p-4">
                    <h5 class="card-title mb-1 text-white fw-bold">{{ _trans('keyword.Congratulations') }}</h5>
                    <h4 class="mb-2 text-white">{{@$data->shop->shop_name}}!</h4>
                    <p class="mb-3 opacity-75 small">{{ _trans('keyword.Best Seller of the Month') }} - <strong>{{@$month}}</strong></p>

                    <div class="mb-3">
                        <span class="d-block small opacity-75">Total Orders</span>
                        <span class="fs-4 fw-bold">{{@$order_count}}</span>
                    </div>

                    <a href="{{env('APP_FRONTEND_URL').'/designer/'.optional(@$data->shop)->slug}}" class="btn btn-light text-primary fw-bold px-4 rounded-pill shadow-sm" target="_blank">{{ _trans('keyword.Visit Shop') }}</a>
                </div>
            </div>
            <div class="col-5 text-center d-flex align-items-end justify-content-center h-100">
                <div class="card-body p-0 pb-3">
                    <img src="{{asset('assets/img/illustrations/card-advance-sale.png')}}" style="height: 150px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3)); transform: scale(1.1);" alt="view sales">
                </div>
            </div>
        </div>
    </div>
</div>
