 <!-- page title  -->
 <div class="row">
    <div class="col-12">
        <div class="dashboard_header mb_50">
            <div class="row">
                <div class="col-lg-6">
                    <div class="dashboard_header_title">
                        <h3>{{$title ?? _trans('keyword.Dashboard')}} </h3>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard_breadcam text-right">
                        {{ Breadcrumbs::render(@$breadcrumb) }}
                        {{-- @php
                            $last_key = array_key_last ($subtitle);
                        @endphp
                        <p>
                            @foreach ($subtitle as $key => $item)
                                @if($key != $last_key)
                                    <a href="{{$item['link']}}">{{$item['name']}}</a> <i class="fas fa-caret-right"></i>
                                @else
                                    {{$item['name']}}
                                @endif
                            @endforeach
                        </p> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>