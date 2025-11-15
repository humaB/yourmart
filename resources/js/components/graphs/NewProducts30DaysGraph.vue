<template>
    <div class="card">
        <div class="card-header">
            <h4>New Products</h4>
        </div>
        <div class="card-body">
            <div class="recent-report__chart">
                <div id="newproducts30daysgraph"></div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NewProducts30DaysGraph',
    props: ['newproducts30daysgraph'],
    data() {
        return {
            chart: null,
        };
    },
    methods: {
        initChart(categories, series) {
            const seriesData = series[0].data;
            const total = seriesData.reduce((sum, value) => sum + value, 0);
            const average = seriesData.length > 0 ? (total / seriesData.length).toFixed(1) : 0;
            let maxValue = 100; // Default fallback
            
            if (seriesData.length > 0) {
                let currentMax = seriesData[0];
                for (let i = 1; i < seriesData.length; i++) {
                    if (seriesData[i] > currentMax) {
                        currentMax = seriesData[i];
                    }
                }
                maxValue = currentMax * 1.2;
            }

            const options = {
                chart: {
                    height: 350,
                    type: "bar",
                    toolbar: {
        show: false,
        tools: {
            download: false
        }
    }
                },
                toolbar: {
                    show: false,
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
                    text: "New Products Added (Last 30 Days)",
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

            const chartElement = this.$el.querySelector("#newproducts30daysgraph");
            if (chartElement) {
                this.chart = new ApexCharts(chartElement, options);
                this.chart.render();
            }
        },
    },


    watch: {
        newproducts30daysgraph: {
            handler(newGraph) {
                if (newGraph.series && newGraph.series.length > 0) {
                    this.initChart(newGraph.categories, [newGraph.series[0]]);
                }
               
            },
            
            // immediate: true,
            deep: true,
        },
    },
    mounted() {
        console.log('Component mounted, initial data:', this.newproducts30daysgraph);
       
    }
}
</script>