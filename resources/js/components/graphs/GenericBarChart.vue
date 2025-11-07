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
            // Handle both new structure (datasets) and old structure (series)
            if (this.graphData.datasets && this.graphData.datasets[this.graphType]) {
                return this.graphData.datasets[this.graphType];
            } else if (this.graphData.series && this.graphData.series[0]) {
                return this.graphData.series[0];
            }
            return {};
        },
        color() {
            return this.dataset.color || this.getDefaultColor();
        },
        stats() {
            return this.dataset.stats || { total: 0, average: 0 };
        },
        formattedTotal() {
            if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                return 'Rs. ' + (this.stats.total || 0).toLocaleString();
            }
            return (this.stats.total || 0).toLocaleString();
        },
        formattedAverage() {
            if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                return 'Rs. ' + (this.stats.average || 0).toLocaleString();
            }
            return (this.stats.average || 0).toLocaleString();
        },
        chartTitle() {
  
          const stats = this.stats || {};
          const total = stats.total || 0;
          const average = Math.round(stats.average || 0); 
            return `Daily ${this.title} (Total: ${total.toLocaleString()}, Avg: ${average})`;
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
  
            // Calculate max value
            let maxValue = 100;
            if (this.seriesData.length > 0) {
                const currentMax = Math.max(...this.seriesData);
                maxValue = currentMax > 0 ? currentMax * 1.2 : 100;
            }
  
            const options = {
                chart: {
                    height: 300,
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
                        if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                            return 'Rs. ' + val.toLocaleString();
                        }
                        return val.toString();
                    },
                    offsetY: -20,
                    style: {
                        fontSize: "11px",
                        colors: ["#9aa0ac"],
                    },
                },
                series: [{
                    name: this.dataset.name || this.title,
                    data: this.seriesData
                }],
                xaxis: {
                    categories: this.graphData.categories || [],
                    position: "top",
                    labels: {
                        offsetY: -18,
                        style: {
                            fontSize: "11px",
                            colors: "#9aa0ac",
                        },
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { 
                        show: false 
                    },
                    max: maxValue,
                },
                title: {
                    text: this.chartTitle,
                    floating: true,
                    offsetY: 270,
                    align: "center",
                    style: {
                        color: "#9aa0ac",
                        fontSize: "14px"
                    },
                },
                colors: [this.color],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: [this.getGradientColor(this.color)],
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 0.8,
                        stops: [0, 100]
                    }
                },
                tooltip: {
                    y: {
                        formatter: (val) => {
                            if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                                return 'Rs.' + val.toLocaleString();
                            }
                            return val.toString();
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
  
        getDefaultColor() {
            const colors = {
                'orders': '#7367F0',
                'sales': '#28C76F',
                'profit': '#00E396',
                'returns': '#EA5455',
                'newproducts': '#FF9F43'
            };
            return colors[this.graphType] || '#7367F0';
        },
  
        getGradientColor(baseColor) {
            const gradients = {
                '#7367F0': '#5E50EE',
                '#28C76F': '#20A759',
                '#e36cd9 ': '#9a56ff ',
                '#EA5455': '#e03131',
                '#FF9F43': '#ff8a1e'
            };
            return gradients[baseColor] || baseColor;
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
  .graph-stats {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
  }
  .stat-item {
    padding: 0.5rem;
  }
  .stat-label {
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
  }
  .stat-value {
    font-size: 1.25rem;
    font-weight: 600;
  }
  </style>