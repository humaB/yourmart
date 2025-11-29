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
        color() {
            return this.dataset.color || this.getDefaultColor();
        },
        stats() {
            return this.dataset.stats || { total: 0, average: 0 };
        },
        formattedTotal() {
            if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                return (this.stats.total || 0).toLocaleString();
            }
            return (this.stats.total || 0).toLocaleString();
        },
        formattedAverage() {
            if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                return (this.stats.average || 0).toLocaleString();
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
                    formatter: (val) => {
                        // if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                        //     return val.toLocaleString();
                        // }
                        // return val.toString();
                        if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                // Remove decimals completely
                return Math.round(val).toLocaleString();
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
                toolbar: {
                    show: false,
                },
                tooltip: {
                    y: {
                        formatter: (val) => {
                            if (this.isCurrency || this.graphType === 'sales' || this.graphType === 'profit') {
                                return val.toLocaleString();
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
                'orders': '#9a56ff',     
                'sales': '#F59E0B',      
                'profit': '#9a56ff',
                'returns': '#EF4444'  
                // 'newproducts': '#F59E0B',    
                // 'graph1': '#4F46E5',     
                // 'graph2': '#10B981',     
                // 'graph3': '#8B5CF6' 
                
            };
            return colors[this.graphType] || '#7367F0';
        },
  
        getGradientColor(baseColor) {
            const gradients = {
                '#289cf5': '#84c0ec',
        '#9a56ff': '#e36cd9', 
        '#23bdb8': '#43e794', 
        '#F59E0B': '#D97706',
        '#4F46E5': '#4338CA'
        // '#10B981': '#059669',
        // '#8B5CF6': '#7C3AED',
        // '#EF4444': '#DC2626',
        // '#F59E0B': '#D97706'
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
  .stat-item {
    padding: 0.5rem;
  }
  .stat-label {
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
  }
  
  </style>