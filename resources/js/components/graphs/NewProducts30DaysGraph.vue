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
            // Comprehensive safety checks
            if (!series) {
                console.warn('No series data provided');
                return;
            }
            
            if (!Array.isArray(series)) {
                console.warn('Series is not an array:', series);
                return;
            }
            
            if (series.length === 0) {
                console.warn('Series array is empty');
                return;
            }

            if (!series[0]) {
                console.warn('First series item is undefined');
                return;
            }

            if (!series[0].data) {
                console.warn('Series data is undefined');
                return;
            }

            if (!Array.isArray(series[0].data)) {
                console.warn('Series data is not an array:', series[0].data);
                return;
            }
            
            // Safe max calculation - NO SPREAD OPERATOR
            let maxValue = 100; // Default fallback
            
            if (seriesData.length > 0) {
                // Use reduce instead of Math.max(...array) to avoid spread operator
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
                    formatter: (val) => {
                        return String(val);
                    },
                    offsetY: -20,
                    style: {
                        fontSize: "12px",
                        colors: ["#9aa0ac"],
                    },
                },
                series: series,
                xaxis: {
                    categories: Array.isArray(categories) ? categories : [],
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
                    max: maxValue,
                },
                title: {
                    text: `Daily Products (Total: ${total}, Avg: ${average})`,
                    floating: true,
                    offsetY: 320,
                    align: "center",
                    style: {
                        color: "#9aa0ac",
                    },
                },
                colors: ['#00E396'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: ['#00a76f'],
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 0.8,
                        stops: [0, 100]
                    }
                }
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
                console.log('Watcher triggered with:', newGraph);
                
                // Comprehensive null checks
                if (!newGraph) {
                    console.warn('newGraph is null or undefined');
                    return;
                }
                
                if (!newGraph.series) {
                    console.warn('newGraph.series is null or undefined');
                    return;
                }
                
                if (!Array.isArray(newGraph.series)) {
                    console.warn('newGraph.series is not an array:', newGraph.series);
                    return;
                }
                
                if (newGraph.series.length === 0) {
                    console.warn('newGraph.series array is empty');
                    return;
                }

                if (!newGraph.series[0]) {
                    console.warn('First series item is undefined');
                    return;
                }
               
                // Call initChart with safe data
                this.initChart(newGraph.categories, [newGraph.series[0]]);
            },
            immediate: true,
            deep: true,
        },
    },
    mounted() {
        console.log('Component mounted, initial data:', this.newproducts30daysgraph);
       
    }
}
</script>