<template>
    <div class="col-xl-6 col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Revenue-Orders Chart</h4>
            </div>
            <div class="card-body">
                <div class="recent-report__chart">
                    <div id="chart3"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "DashboardRevenueOrderChart",
    props: ["revenueOrderGraph"],
    data() {
        return {
            chart: null,
        };
    },
    methods: {
        initChart() {

            const revenueData = this.revenueOrderGraph.series.find(series => series.name === "Revenue").data;
            const maxRevenue = Math.max(...revenueData) + 500000; // Add some padding for visual clarity

            const options = {
                chart: {
                    height: 350,
                    type: "line",
                    shadow: {
                        enabled: true,
                        color: "#000",
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 1,
                    },
                    toolbar: {
                        show: false,
                    },
                },
                colors: ["#77B6EA", "#545454"],
                dataLabels: {
                    enabled: true,
                },
                stroke: {
                    curve: "smooth",
                },
                series:  this.revenueOrderGraph.series,
                title: {
                    text: "",
                    align: "left",
                },
                grid: {
                    borderColor: "#e7e7e7",
                    row: {
                        colors: ["#f3f3f3", "transparent"], // Alternating colors
                        opacity: 0.5,
                    },
                },
                markers: {
                    size: 6,
                },
                xaxis: {
                    categories: this.revenueOrderGraph.categories,
                    title: {
                        text: "Last 6 Months",
                    },
                    labels: {
                        style: {
                            colors: "#9aa0ac",
                        },
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            color: "#9aa0ac",
                        },
                    },
                    min: 0,
                    max: Math.ceil(maxRevenue), // Add 10% padding above the max value
                },
                legend: {
                    position: "top",
                    horizontalAlign: "right",
                    floating: true,
                    offsetY: -25,
                    offsetX: -5,
                },
            };

            if (this.chart) {
                this.chart.destroy(); // Destroy the existing chart instance
            }

            this.chart = new ApexCharts(this.$el.querySelector("#chart3"), options);
            this.chart.render(); // Render the chart
        },
    },
    watch: {
        revenueOrderGraph: {
            handler(newGraph) {
                this.initChart();
            },
            deep: true,
        },
    },
};
</script>
