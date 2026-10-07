<div class="col-xl-4 col-md-6 col-12 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
                <h5 class="m-0 me-2">Order Payment Graph</h5>
            </div>
        </div>
        <div class="card-body">
            <div id="orderPaymentStatusChart" class="pt-md-4"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {

                $.ajax({
                    url: "/dashboard/orderPaymentCount?{!!request()->server('QUERY_STRING')!!}",
                    method: "get",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        let tagName = [];
                        let tagData = [];
                        let tagColor = [];

                        $(response.orderPaymentCount).each(function (index, value) {
                            tagName.push(value.name);
                            tagData.push(value.total);
                            tagColor.push('#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'));
                        });

                        orderPaymentStatusChart(tagName, tagData, tagColor);
                    },
                    error: function (error) {
                        console.log(error.responseJSON.message);
                    },
                });

                function orderPaymentStatusChart(tagName, tagData, tagColor) {
                    const deliveryExceptionsChartE1 = document.querySelector(
                            "#orderPaymentStatusChart"
                        ),
                        deliveryExceptionsChartConfig = {
                            chart: {
                                height: 420,
                                parentHeightOffset: 0,
                                type: "donut",
                            },
                            labels: tagName,
                            series: tagData,
                            colors: tagColor,
                            stroke: {
                                width: 0,
                            },
                            dataLabels: {
                                enabled: false,
                                formatter: function (val, opt) {
                                    return parseInt(val).toFixed(2) + "$";
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
                                    useSeriesColors: true,
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
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: "77%",
                                        labels: {
                                            show: true,
                                            value: {
                                                fontSize: "26px",
                                                fontFamily: "Public Sans",
                                                color: headingColor,
                                                fontWeight: 500,
                                                offsetY: -30,
                                                formatter: function (val) {
                                                    return "$" + parseInt(val).toFixed(2);
                                                },
                                            },
                                            name: {
                                                offsetY: 20,
                                                fontFamily: "Public Sans",
                                            },
                                            total: {
                                                show: true,
                                                fontSize: ".75rem",
                                                label: "Total Money",
                                                color: labelColor,
                                                formatter: function (w) {
                                                    let total = 0;
                                                    $(w.globals.series).each(function (index, value) {
                                                        total += value;
                                                    })
                                                    return "$" + parseInt(total).toFixed(2);
                                                },
                                            },
                                        },
                                    },
                                    initialActiveIndex: 0
                                },
                            },
                            responsive: [{
                                breakpoint: 420,
                                options: {
                                    chart: {
                                        height: 360,
                                    },
                                },
                            },],
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
