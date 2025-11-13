<template>
    <div class="card">
        <div class="card-header">
            <h4>Ticket Raised - Last 30 Days</h4>
        </div>
        <div class="card-body">
            <div class="text-center text-muted py-4" v-if="!hasData">
                No ticket data available for the last 30 days
            </div>
            <div class="recent-report__chart" v-else>
                <div ref="chartContainer" style="width: 100%;"></div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TicketTypesGraph',
    props: {
        ticketTypesGraphData: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            chart: null
        };
    },
    computed: {
        hasData() {
            console.log('Checking data structure:', this.ticketTypesGraphData);
            
            // Check if data exists and has the expected structure
            const hasData = this.ticketTypesGraphData && 
                           this.ticketTypesGraphData.categories && 
                           Array.isArray(this.ticketTypesGraphData.categories) &&
                           this.ticketTypesGraphData.categories.length > 0 &&
                           this.ticketTypesGraphData.series &&
                           Array.isArray(this.ticketTypesGraphData.series) &&
                           this.ticketTypesGraphData.series.length > 0 &&
                           this.ticketTypesGraphData.series[0].data &&
                           Array.isArray(this.ticketTypesGraphData.series[0].data) &&
                           this.ticketTypesGraphData.series[0].data.length > 0;
            
            console.log('Has data result:', hasData);
            return hasData;
        }
    },
    methods: {
        initChart() {
            if (!this.hasData) {
                console.warn('No valid data available for ticket types graph');
                console.log('Current data:', this.ticketTypesGraphData);
                return;
            }

            console.log('Initializing chart with valid data:', this.ticketTypesGraphData);

            const seriesData = this.ticketTypesGraphData.series[0].data;
            const stats = this.ticketTypesGraphData.series[0].stats || { total: 0, average: 0 };
            const categories = this.ticketTypesGraphData.categories;

            const colors = [
                '#FF4560', '#008FFB', '#00E396', '#FEB019', '#775DD0',
                '#546E7A', '#26a69a', '#D10CE8', '#FF9F43', '#00D9E9',
                '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
                '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
            ];

            const options = {
                chart: {
                    type: 'bar',
                    height: 600,
                    width: '100%',
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 4,
                        columnWidth: '50%',
                        distributed: true,
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val;
                    },
                    // offsetY: -20,
                    // style: {
                    //     fontSize: '12px',
                    //     colors: ["#304758"]
                    // }
                },
                series: [{
                    name: 'Number of Tickets',
                    data: seriesData
                }],
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            colors: colors,
                            fontSize: '11px'
                        },
                        rotate: -45
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    title: {
                        text: 'Number of Tickets',
                        style: {
                            color: '#9aa0ac'
                        }
                    },
                    labels: {
                        style: {
                            colors: '#9aa0ac'
                        }
                    }
                },
                colors: colors,
                title: {
                    text: `Total Tickets`,
                    align: 'center',
                    offsetY: 10,
                    style: {
                        fontSize: '14px',
                        color: '#9aa0ac'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " tickets";
                        }
                    }
                },
                grid: {
                    borderColor: '#e7e7e7',
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    }
                }
            };

            // Destroy existing chart
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }

            // Create new chart using Vue ref
            this.$nextTick(() => {
                if (this.$refs.chartContainer) {
                    console.log('Chart container found, rendering chart...');
                    this.chart = new ApexCharts(this.$refs.chartContainer, options);
                    this.chart.render();
                    console.log('Chart rendered successfully');
                } else {
                    console.error('Chart container ref not found!');
                }
            });
        }
    },
    watch: {
        ticketTypesGraphData: {
            handler(newData) {
                console.log('Watcher triggered with new data:', newData);
                console.log('Data type:', typeof newData);
                console.log('Data keys:', Object.keys(newData || {}));
                
                if (this.hasData) {
                    console.log('Has valid data, initializing chart...');
                    this.$nextTick(() => {
                        this.initChart();
                    });
                } else {
                    console.log('No valid data in watcher');
                    console.log('Categories:', newData?.categories);
                    console.log('Series:', newData?.series);
                    console.log('Series[0]:', newData?.series?.[0]);
                    console.log('Series[0] data:', newData?.series?.[0]?.data);
                }
            },
            deep: true,
            immediate: true
        }
    },
    mounted() {
        console.log('Component mounted, initial data:', this.ticketTypesGraphData);
        if (this.hasData) {
            console.log('Has valid data on mount, initializing chart...');
            this.$nextTick(() => {
                // this.initChart();
            });
        } else {
            console.log('No valid data on mount');
        }
    },
    beforeUnmount() {
        if (this.chart) {
            this.chart.destroy();
        }
    }
}
</script>