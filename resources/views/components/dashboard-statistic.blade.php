<div class="col-xl-8  col-xl-7 col-12">
    <div class="row ">
        @foreach ($data as $item)
            <a class="col-xl-4 col-md-6 col-sm-6 mb-4 " href="{{@$item['route']??'#'}}">
                <div class="card h-100 {{ @$item['border_class'] }}" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ @$item['tooltip'] }}">
                    <div class="card-body p-3">
                        <div class="col-12 row">
                            <div class="col-5 d-flex align-items-center">
                                <div class="avatar">
                                    <span class="avatar-initial rounded {{ @$item['bg_class'] }}">
                                        <i class="{{ @$item['icon_class'] }} ti-md"></i>
                                    </span>
                                </div>
                                <h4 class="ms-1 mb-0">{{ @$item['value'] }}</h4>
                            </div>
                            <div class="col-7">
                                @foreach ($item['items'] as $value)
                                    <p class="text-primary mb-0">{{ $value['label'] }}: <span
                                            class="{{ @$value['stat_class'] }}">{{ @$value['value'] }}</span> </p>
                                @endforeach
                            </div>
                            <p class="mb-1">{{ @$item['label'] }}</p>
                            <div class="d-flex align-items-center">
                                <div class="progress w-100">
                                    <div class="progress-bar bg-info" style="width: {{ @$item['start'] }}%"
                                        role="progressbar" aria-valuenow="{{ @$item['start'] }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: {{ @$item['end'] }}%" aria-valuenow="{{ @$item['end'] }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

</div>
