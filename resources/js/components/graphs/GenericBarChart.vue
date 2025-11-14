<template>
    <div class="card">
        <div class="card-header">
            <h4>{{ title }}</h4>
        </div>
        <div class="card-body">
            <div class="recent-report__chart">
                <div :id="chartId"></div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'GenericBarChart',
    props: {
        graphData: {
            type: Object,
            required: true
        },
        graphType: {
            type: String,
            required: true
        },
        title: {
            type: String,
            required: true
        },
        isCurrency: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            chart: null,
        };
    },
    computed: {
        chartId() {
            return `${this.graphType}Graph`;
        },
        dataset() {
            if (this.graphData.datasets && this.graphData.datasets[this.graphType]) {
                return this.graphData.datasets[this.graphType];
            } else if (this.graphData.series && this.graphData.series[0]) {
                return this.graphData.series[0];
            }
            return {};
        },
        chartColor() {
            return this.getChartColor();
        },
        stats() {
            return this.dataset.stats || { total: 0, average: 0 };
        },
        seriesData() {
            return this.dataset.data || [];
        }
    },
    methods: {
        initChart() {
            if (!this.seriesData || !Array.isArray(this.seriesData) || this.seriesData.length === 0) {
                console.warn(`No data available for ${this.graphType}`);
                return;
            }

            const options = {
                chart: {
                    height: 350,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val) => {
                        if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                            return 'Rs. ' + val.toLocaleString();
                        }
                        return val;
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#9aa0ac"]
                    }
                },
                series: [{
                    name: this.dataset.name || this.title,
                    data: this.seriesData
                }],
                xaxis: {
                    categories: this.graphData.categories || [],
                    position: 'top',
                    labels: {
                        offsetY: -18,
                        style: {
                            colors: '#9aa0ac',
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                fill: {
                    gradient: {
                        shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.25,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [50, 0, 100, 100]
                    },
                },
                yaxis: {
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false,
                    },
                    labels: {
                        show: false,
                    }
                },
                colors: [this.chartColor],
                tooltip: {
                    y: {
                        formatter: (val) => {
                            if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                                return 'Rs. ' + val.toLocaleString();
                            }
                            return val;
                        }
                    }
                }
            };

            if (this.chart) {
                this.chart.destroy();
            }

            this.$nextTick(() => {
                const chartElement = document.getElementById(this.chartId);
                if (chartElement) {
                    this.chart = new ApexCharts(chartElement, options);
                    this.chart.render();
                }
            });
        },

        getChartColor() {
            // Assign specific colors based on chart order or type
            const colorMap = {
                // First chart: rgb(0, 143, 251)
                1: 'rgb(0, 143, 251)',
                first: 'rgb(0, 143, 251)',
                orders: 'rgb(0, 143, 251)',
                
                // Second chart: rgb(0, 227, 150)
                2: 'rgb(0, 227, 150)',
                second: 'rgb(0, 227, 150)',
                sales: 'rgb(0, 227, 150)',
                
                // Third chart: rgb(254, 176, 25)
                3: 'rgb(254, 176, 25)',
                third: 'rgb(254, 176, 25)',
                profit: 'rgb(254, 176, 25)',
                
                // Fourth chart: rgb(119, 93, 208)
                4: 'rgb(119, 93, 208)',
                fourth: 'rgb(119, 93, 208)',
                returns: 'rgb(119, 93, 208)'
            };

            return colorMap[this.graphType] || colorMap[1]; // Default to first color
        }
    },
    watch: {
        graphData: {
            handler() {
                this.initChart();
            },
            deep: true
        }
    },
    mounted() {
        this.initChart();
    },
    beforeDestroy() {
        if (this.chart) {
            this.chart.destroy();
        }
    }
}
</script>

<style scoped>
.card {
    margin-bottom: 1rem;
}
</style>