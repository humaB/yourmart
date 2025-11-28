<template>
    <div class="card">
        <div class="card-header">
            <h4>Active Sellers</h4>
        </div>
        <div class="card-body">
            <div class="activeseller__chart">
                <div id="activeSellersMonthly"></div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
name: 'ActiveSellersMonthly',
props: ['activeSellersMonthly'],
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
                width: '100%',
                
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
                max: Math.max(...series[0].data) * 1.2, 
            },
            
            title: {
                text: `Active Sellers(Total: ${total}, Avg: ${average})`,
                floating: true,
                offsetY: 320,
                align: "center",
                style: {
                    color: "#9aa0ac",
                },
            },
            
            colors: ['#6366F1'], 
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: "vertical",
                shadeIntensity: 0.5,
                gradientToColors: ['#A855F7'],
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 0.8,
                stops: [0, 100]
            }
        },
        };

        if (this.chart) {
            this.chart.destroy();
        }

        this.chart = new ApexCharts(this.$el.querySelector("#activeSellersMonthly"), options);
        this.chart.render();
    },
},
watch : {
    activeSellersMonthly: {
        handler(newGraph) {
            if (newGraph.series && newGraph.series.length > 0) {
                this.initChart(newGraph.categories, [newGraph.series[0]]);
               
            }
        },
        // immediate: true, // Ensures the chart initializes on the first load
        deep: true,
    },
}
}
</script>1


<style scoped>
.recent-report__chart, #activeSellersMonthly {
width: 100%;
overflow: hidden;
}

/* #dropshipper120DaysChart {
min-width: 100%;
} */
</style>