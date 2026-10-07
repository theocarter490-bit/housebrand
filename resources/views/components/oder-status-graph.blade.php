<div class="col-xl-4 col-md-6 col-12 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Order Status Graph</h5>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-12">
                <div id="orderStatusChart"></div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
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
            url: "/dashboard/orderCount?{!!request()->server('QUERY_STRING')!!}",
            method: "get",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function(response) {
                let tagName = [];
                let tagData = [];
                let tagColor = [];
                let maxData = 0;

                $(response.orderCount).each(function(index, value) {
                    if(value.total>maxData)
                    {
                        maxData = value.total;
                    }
                    tagName.push(value.name);
                    tagData.push(value.total);
                    tagColor.push(value.color);

                });

                horizontalGraph(tagName, tagData, tagColor,(maxData+10));
            },
            error: function(error) {
                console.log(error.responseJSON.message);
            },
        });

        function horizontalGraph(tagName, tagData, tagColor,maxData) {
            const horizontalBarChartEl = document.querySelector(
                    "#orderStatusChart"
                ),
                horizontalBarChartConfig = {
                    chart: {
                        height: 400,
                        type: "bar",
                        toolbar: {
                            show: true,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            barHeight: "20%",
                            barWidth: "10%",
                            distributed: true,
                            startingShape: "rounded",
                            borderRadius: 7,
                        },
                    },
                    grid: {
                        strokeDashArray: 10,
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: false,
                            },
                        },
                        yaxis: {
                            lines: {
                                show: false,
                            },
                        },
                        padding: {
                            top: -15,
                            bottom: -5,
                        },
                    },

                    colors: tagColor,
                    dataLabels: {
                        enabled: false,
                        style: {
                            colors: ["#000"],
                            fontWeight: 200,
                            fontSize: "13px",
                            fontFamily: "Public Sans",
                        },
                        formatter: function(val, opts) {
                            return horizontalBarChartConfig.labels[
                                opts.dataPointIndex
                            ];
                        },
                        offsetX: 0,
                        dropShadow: {
                            enabled: true,
                        },
                    },
                    labels: tagName,
                    series: [{
                        name:'count',
                        data: tagData,
                    }, ],

                    xaxis: {
                        categories: tagName,
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: true,
                        },
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: "13px",
                            },
                            formatter: function(val) {
                                return `${val}`;
                            },
                        },
                    },
                    yaxis: {
                        max: maxData,
                        labels: {
                            style: {
                                colors: [labelColor],
                                fontFamily: "Public Sans",
                                fontSize: "13px",
                            },
                        },
                    },
                    tooltip: {
                        enabled: true,
                        style: {
                            fontSize: "12px",
                        },
                        onDatasetHover: {
                            highlightDataSeries: false,
                        },
                        custom: function({
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w,
                        }) {
                            return (
                                '<div class="px-1 py-1">' +
                                "<span>" +
                                series[seriesIndex][dataPointIndex] +
                                "</span>" +
                                "</div>"
                            );
                        },
                    },
                    legend: {
                        show: false,
                    },
                };
            if (
                typeof horizontalBarChartEl !== undefined &&
                horizontalBarChartEl !== null
            ) {
                const horizontalBarChart = new ApexCharts(
                    horizontalBarChartEl,
                    horizontalBarChartConfig
                );
                horizontalBarChart.render();
            }
        }
    </script>
@endpush
