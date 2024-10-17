<template>

        <div class="col-xl-6 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Order - Profit Analysis</h4>
                </div>
                <div class="card-body">

                    <div id="chart4" class="chartsh"></div>
                </div>
            </div>
        </div>

</template>
<script>
export default {
    name: 'RevenueChat',
    props : ['revenueDates', 'revenueGraphData'],
    data(){
        return {
            chart4Instance : ''
        }
    },
    methods: {
        chart4() {
            const options = {
                chart: {
                    height: 385,
                    type: 'area',
                    toolbar: {
                        show: true
                    }
                },
                colors: ['#999b9c', '#4CC2B0'],
                fill: {
                    colors: ['#999b9c', '#4CC2B0']
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                markers: {
                    colors: ['#999b9c', '#4CC2B0']
                },
                series: this.revenueGraphData,
                legend: {
                    show: false
                },
                xaxis: {
                    categories: this.revenueDates,
                    labels: {
                        style: {
                            colors: '#9aa0ac'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            color: '#9aa0ac'
                        }
                    }
                }
            };

            // Destroy the existing chart instance if it exists
            if (this.chart4Instance) {
                this.chart4Instance.destroy();
            }

            // Create a new chart instance and render it
            this.chart4Instance = new ApexCharts(document.querySelector('#chart4'), options);
            this.chart4Instance.render();
        }
    },
    watch: {
        // Watch for changes in revenueGraphData
        revenueGraphData: {
        handler(newValue, oldValue) {
            this.chart4(); // Run the chart4 method when revenueGraphData changes
        },
        deep: true, // If revenueGraphData is an object or array, deep: true ensures Vue watches for changes inside it
        }
    }
}
</script>
