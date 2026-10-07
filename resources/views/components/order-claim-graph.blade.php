<div class="col-xl-4 mb-4 col-lg-4 col-12">
    <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
                <h5 class="m-0 me-2">Order Claim Graph</h5>
            </div>
        </div>
        <div class="card-body">
            <div id="orderClaimChart" class="pt-md-4"></div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        (function() {

                $.ajax({
                    url: "/dashboard/orderClaimCount?{!!request()->server('QUERY_STRING')!!}",
                    method: "get",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        let tagName = [];
                        let tagData = [];
                        let tagColor = [];

                        $(response.orderClaimCount).each(function(index, value) {
                            tagName.push(value.status);
                            tagData.push(value.total);
                            tagColor.push('#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'));
                        });

                        orderClaimStatusChart(tagName, tagData, tagColor);
                    },
                    error: function(error) {
                        console.log(error.responseJSON.message);
                    },
                });

                function orderClaimStatusChart(tagName, tagData, tagColor) {
                    const deliveryExceptionsChartE1 = document.querySelector(
                            "#orderClaimChart"
                        ),
                        deliveryExceptionsChartConfig = {
                            chart: {
                                height: 420,
                                parentHeightOffset: 0,
                                type: "pie",
                            },
                            labels: tagName,
                            series: tagData,
                            colors: tagColor,
                            stroke: {
                                width: 0,
                            },
                            dataLabels: {
                                formatter(val, opts) {
                                    const name = opts.w.globals.labels[opts.seriesIndex]
                                    return [name, val.toFixed(1) + '%']
                                },
                            },
                            legend: {
                                show: true,
                                position: "bottom",
                                offsetY: 10,
                                markers: {
                                    width: 8,
                                    height: 8,
                                    offsetX: -3,
                                },
                                itemMargin: {
                                    horizontal: 15,
                                    vertical: 5,
                                },
                                fontSize: "13px",
                                fontFamily: "Public Sans",
                                fontWeight: 400,
                                labels: {
                                    colors: headingColor,
                                    useSeriesColors: true,
                                },
                            },
                            plotOptions: {
                                pie: {
                                    dataLabels: {
                                        offset: -5,
                                    },
                                },
                            },
                            grid: {
                                padding: {
                                    top: 15,
                                },
                            },
                            states: {
                                hover: {
                                    filter: {
                                        type: "none",
                                    },
                                },
                            },
                            responsive: [{
                                breakpoint: 420,
                                options: {
                                    chart: {
                                        height: 360,
                                    },
                                },
                            }, ],
                        };
                    if (
                        typeof deliveryExceptionsChartE1 !== undefined &&
                        deliveryExceptionsChartE1 !== null
                    ) {
                        const deliveryExceptionsChart = new ApexCharts(
                            deliveryExceptionsChartE1,
                            deliveryExceptionsChartConfig
                        );
                        deliveryExceptionsChart.render();
                    }
                }
            }
        )();
    </script>
@endpush
