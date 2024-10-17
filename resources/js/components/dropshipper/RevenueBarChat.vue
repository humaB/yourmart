<template>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
        <div class="card">
            <div class="card-header">
                <h4>Month-Wise Profit</h4>
            </div>
            <div class="card-body">
                <div class="recent-report__chart">
                    <div id="barChart"></div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'RevenueBarChat',
    props : ['barChart'],
    data(){
        return {
            chart: null, // Holds the chart instance
        }
    },
    methods: {
        createBarChart() {
            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            this.chart = am4core.create("barChart", am4charts.XYChart);
            this.chart.scrollbarX = new am4core.Scrollbar();

            // Add data
            this.chart.data = this.barChart;

            // Create axes
            let categoryAxis = this.chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "country";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.verticalCenter = "middle";
            categoryAxis.renderer.labels.template.rotation = 270;
            categoryAxis.tooltip.disabled = true;
            categoryAxis.renderer.minHeight = 110;
            categoryAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

            let valueAxis = this.chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.minWidth = 50;
            valueAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

            // Create series
            let series = this.chart.series.push(new am4charts.ColumnSeries());
            series.sequencedInterpolation = false;
            series.dataFields.valueY = "visits";
            series.dataFields.categoryX = "country";
            series.tooltipText = "[{categoryX}: bold]{valueY}[/]";
            series.columns.template.strokeWidth = 0;

            series.tooltip.pointerOrientation = "vertical";

            series.columns.template.column.cornerRadiusTopLeft = 10;
            series.columns.template.column.cornerRadiusTopRight = 10;
            series.columns.template.column.fillOpacity = 0.8;

            // on hover, make corner radiuses bigger
            let hoverState = series.columns.template.column.states.create("hover");
            hoverState.properties.cornerRadiusTopLeft = 0;
            hoverState.properties.cornerRadiusTopRight = 0;
            hoverState.properties.fillOpacity = 1;

            series.columns.template.adapter.add("fill", (fill, target) => {
                return this.chart.colors.getIndex(target.dataItem.index);
            });

            // Cursor
            this.chart.cursor = new am4charts.XYCursor();
        },
    },
    watch: {
        // Watch for changes in revenueGraphData
        barChart: {
            handler(newValue, oldValue) {
                this.createBarChart(); // Run the chart4 method when revenueGraphData changes
            },
            deep: true, // If revenueGraphData is an object or array, deep: true ensures Vue watches for changes inside it
        }
    }
}
</script>
