<!-- Radial bar Chart -->
<div class="col-xl-4 col-md-6 col-12 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Expense Graph</h5>
        </div>
        <div class="card-body">
            <div id="radialBarChart"></div>
        </div>
    </div>
</div>
<!-- /Radial bar Chart -->

@push('scripts')
    <script>
        (function() {

                $.ajax({
                    url: "/dashboard/expense-graph",
                    method: "get",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        let tagName = [];
                        let tagData = [];
                        let tagColor = [];

                        $(response.expenseGraph).each(function(index, value) {
                            tagName.push(value.name);
                            tagData.push(value.expenses_sum_amount);
                            tagColor.push('#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'));
                        });

                        expenseGraph(tagName, tagData,tagColor);
                    },
                    error: function(error) {
                        console.log(error.responseJSON.message);
                    },
                });

                function expenseGraph(tagName, tagData,tagColor) {
                    const radialBarChartEl = document.querySelector('#radialBarChart'),
                        radialBarChartConfig = {
                            chart: {
                                height: 400,
                                type: 'radialBar'
                            },
                            colors: tagColor,
                            plotOptions: {
                                radialBar: {
                                    size: 185,
                                    hollow: {
                                        size: '40%'
                                    },
                                    track: {
                                        margin: 10,
                                        background: config.colors_label.secondary
                                    },
                                    dataLabels: {
                                        name: {
                                            fontSize: '2rem',
                                            fontFamily: 'Public Sans'
                                        },
                                        value: {
                                            fontSize: '1.2rem',
                                            color: legendColor,
                                            fontFamily: 'Public Sans',
                                            formatter: function (val) {
                                                return "$" + parseInt(val).toFixed(2);
                                            },
                                        },
                                        total: {
                                            show: true,
                                            fontWeight: 400,
                                            fontSize: '1.3rem',
                                            color: headingColor,
                                            label: tagName[0],
                                            formatter: function (w) {
                                                return "$" + parseInt(w.globals.series).toFixed(2);
                                            },
                                        }
                                    }
                                }
                            },
                            grid: {
                                borderColor: borderColor,
                                padding: {
                                    top: -25,
                                    bottom: -20
                                }
                            },
                            legend: {
                                show: true,
                                position: 'bottom',
                                labels: {
                                    colors: legendColor,
                                    useSeriesColors: false
                                }
                            },
                            stroke: {
                                lineCap: 'round'
                            },
                            series: tagData,
                            labels: tagName,
                        };
                    if (typeof radialBarChartEl !== undefined && radialBarChartEl !== null) {
                        const radialChart = new ApexCharts(radialBarChartEl, radialBarChartConfig);
                        radialChart.render();
                    }
                }
                }
            )();
    </script>
@endpush
