<template>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-6">
        <div class="card">
            <div class="card-header">
                <h4>Dropshipper Registration</h4>
            </div>
            <div class="card-body">
                <div class="recent-report__chart">
                    <div id="chart2"></div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'DashboardDropshipperGraph',
    props: ['dropshipperGraph'],
    data() {
        return {
            chart: null,
        };
    },
    methods: {
        initChart(categories, series) {
            const options = {
                chart: {
                    height: 350,
                    type: "bar",
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            position: "top",
                        },
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val) => `${val}`,
                    offsetY: -20,
                    style: {
                        fontSize: "12px",
                        colors: ["#9aa0ac"],
                    },
                },
                series: series,
                xaxis: {
                    categories: categories,
                    position: "top",
                    labels: {
                        offsetY: -18,
                        style: {
                            colors: "#9aa0ac",
                        },
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                },
                yaxis: {
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                    labels: {
                        show: false,
                    },
                    max: Math.max(...series[0].data) * 1.2, // Add 20% space above the highest value
                },
                title: {
                    text: "Dropshipper Registrations (Last 12 Months)",
                    floating: true,
                    offsetY: 320,
                    align: "center",
                    style: {
                        color: "#9aa0ac",
                    },
                },
            };

            if (this.chart) {
                this.chart.destroy();
            }

            this.chart = new ApexCharts(this.$el.querySelector("#chart2"), options);
            this.chart.render();
        },
    },
    watch : {
        dropshipperGraph: {
            handler(newGraph) {
                if (newGraph.series && newGraph.series.length > 0) {
                    this.initChart(newGraph.categories, [newGraph.series[0]]);
                }
            },
            immediate: true, // Ensures the chart initializes on the first load
            deep: true,
        },
    }
}
</script>
