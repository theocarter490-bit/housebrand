<div class="col-xl-4 mb-4 col-lg-4 col-12">
    <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
                <h5 class="m-0 me-2">Product Graph</h5>
            </div>
        </div>
        <div class="card-body">
            <div id="productStatusCountChart" class="pt-md-4"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
                let cardColor, headingColor, legendColor, labelColor, borderColor;
                if (isDarkStyle) {
                    cardColor = config.colors_dark.cardColor;
                    labelColor = config.colors_dark.textMuted;
                    legendColor = config.colors_dark.bodyColor;
                    headingColor = config.colors_dark.headingColor;
                    borderColor = config.colors_dark.borderColor;
                } else {
                    cardColor = config.colors.cardColor;
                    labelColor = config.colors.textMuted;
                    legendColor = config.colors.bodyColor;
                    headingColor = config.colors.headingColor;
                    borderColor = config.colors.borderColor;
                }

                // Chart Colors
                const chartColors = {
                    donut: {
                        series1: config.colors.success,
                        series2: "#28c76fb3",
                        series3: "#28c76f80",
                        series4: config.colors_label.success,
                    },
                    line: {
                        series1: config.colors.warning,
                        series2: config.colors.primary,
                        series3: "#7367f029",
                    },
                };

                $.ajax({
                    url: "/dashboard/productStatusCount?{!!request()->server('QUERY_STRING')!!}",
                    method: "get",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        let tagName = [];
                        let tagData = [];
                        let tagColor = [];

                        $(response.productStatusCount).each(function (index, value) {
                            tagName.push(value.is_published == 1 ? 'Publish' : 'Unpublish');
                            tagData.push(value.total);
                            tagColor.push('#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'));
                        });

                        productStatusCountChart(tagName, tagData, tagColor);
                    },
                    error: function (error) {
                        console.log(error.responseJSON.message);
                        // handle the error case
                    },
                });

                function productStatusCountChart(tagName, tagData, tagColor) {
                    const deliveryExceptionsChartE1 = document.querySelector(
                            "#productStatusCountChart"
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
                                    return parseInt(val).toFixed(2) ;
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
                            tooltip: {
                                theme: false,
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
                                                    return  parseInt(val).toFixed(2);
                                                },
                                            },
                                            name: {
                                                offsetY: 20,
                                                fontFamily: "Public Sans",
                                            },
                                            total: {
                                                show: true,
                                                fontSize: ".75rem",
                                                label: "Total Products",
                                                color: labelColor,
                                                formatter: function (w) {
                                                    let total = 0;
                                                    $(w.globals.series).each(function (index, value) {
                                                        total += value;
                                                    })
                                                    return  total;
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
