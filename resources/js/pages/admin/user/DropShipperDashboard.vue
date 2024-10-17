<template>
    <div>
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h5>Preview Dashboard for {{ customerName }},</h5>

            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <h6>Account Health Status</h6>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" :style="'width:' + accountHealth + '%'"
                        :aria-valuenow="accountHealth" aria-valuemin="0" aria-valuemax="100">
                        {{ accountHealth }}%
                    </div>
                </div>
            </div>
         
        </div>
        <div class="row" style="margin-left: -10px">
            <!-- cards -->
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card bg-info">
                    <div class="card-statistic-4 text-white">
                        <div class="align-items-center justify-content-between">
                            <div class="row">
                                <div class="col-lg-8 col-md-6 col-sm-6 col-xs-6 pr-0">
                                    <div class="card-content">
                                        <h5 class="font-15">Receivable Amount</h5>
                                        <h2 class="mb-3 font-18">
                                            {{ formatPrice(totalProfit) }}
                                        </h2>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img :src="public_url + '/assets2/img/banner/2.png'" alt="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card bg-success">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row">
                                <div class="col-lg-8 col-md-6 col-sm-6 col-xs-6 pr-0">
                                    <div class="card-content text-white">
                                        <h5 class="font-15">Received Amount</h5>
                                        <h2 class="mb-3 font-18">
                                            {{ formatPrice(totalPaid) }}
                                        </h2>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img :src="public_url + '/assets2/img/banner/4.png'" alt="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card bg-warning">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row">
                                <div class="col-lg-8 col-md-6 col-sm-6 col-xs-6 pr-0">
                                    <div class="card-content">
                                        <h5 class="font-15">
                                            Current Balance in Wallet
                                        </h5>
                                        <h2 class="mb-3 font-18">
                                            {{ formatPrice(totalRemaining) }}
                                        </h2>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img :src="public_url + '/assets2/img/banner/1.png'" alt="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">

            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-bg">
                        <div class="chartjs-size-monitor"
                            style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                            <div class="chartjs-size-monitor-expand"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                            </div>
                        </div>
                        <div class="p-t-20 d-flex justify-content-between">
                            <div class="col">
                                <h6 class="mb-0">Sales</h6>
                                <span class="font-weight-bold mb-0 font-20">{{ formatPrice(totalSales) }}</span>
                            </div>
                            <i class="fas fa-diagnoses card-icon col-green font-30 p-r-30"></i>
                        </div>
                        <canvas id="cardChart2" height="92" width="350"
                            style="display: block; height: 74px; width: 280px;" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-bg">
                        <div class="chartjs-size-monitor"
                            style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                            <div class="chartjs-size-monitor-expand"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                            </div>
                        </div>
                        <div class="p-t-20 d-flex justify-content-between">
                            <div class="col">
                                <h6 class="mb-0">Cost of Products</h6>
                                <span class="font-weight-bold mb-0 font-20">{{ formatPrice(totalProductCost) }}</span>
                            </div>
                            <i class="fas fa-chart-bar card-icon col-indigo font-30 p-r-30"></i>
                        </div>
                        <canvas id="cardChart3" height="92" width="350"
                            style="display: block; height: 74px; width: 280px;" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-bg">
                        <div class="chartjs-size-monitor"
                            style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                            <div class="chartjs-size-monitor-expand"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                            </div>
                        </div>
                        <div class="p-t-20 d-flex justify-content-between">
                            <div class="col">
                                <h6 class="mb-0">Shipping & Packing</h6>
                                <span class="font-weight-bold mb-0 font-20">{{ formatPrice(totalPackingCourier)
                                    }}</span>
                            </div>
                            <i class="fas fa-hand-holding-usd card-icon col-cyan font-30 p-r-30"></i>
                        </div>
                        <canvas id="cardChart4" height="92" width="350"
                            style="display: block; height: 74px; width: 280px;" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-bg">
                        <div class="chartjs-size-monitor"
                            style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                            <div class="chartjs-size-monitor-expand"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink"
                                style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                            </div>
                        </div>
                        <div class="p-t-20 d-flex justify-content-between">
                            <div class="col">
                                <h6 class="mb-0">Profit</h6>
                                <span class="font-weight-bold mb-0 font-20">{{ formatPrice(totalSales -
                                    (totalProductCost + totalPackingCourier)) }}</span>
                            </div>
                            <i class="fas fa-address-card card-icon col-orange font-30 p-r-30"></i>
                        </div>
                        <canvas id="cardChart1" height="92" width="350"
                            style="display: block; height: 74px; width: 280px;" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <table style="table-layout: fixed; width: 100%;">
            <tr>
                <td style="width: 20%; padding : 10px">
                    <div class="card card-statistic-1">
                        <div class="card-icon l-bg-cyan">
                            <i class="fa fa-shopping-bag"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">
                                        <i class="ti-arrow-up text-success"></i> {{ totalOrders }}
                                    </h3>
                                    <span class="text-muted">Total Order</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="width: 20%;padding : 10px">
                    <div class="card card-statistic-1">
                        <div class="card-icon l-bg-orange">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">
                                        <i class="ti-arrow-up text-success"></i> {{ inProcessOrder }}
                                    </h3>
                                    <span class="text-muted">In Process</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="width: 25%;padding : 10px">
                    <div class="card card-statistic-1">
                        <div class="card-icon l-bg-purple">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">
                                        <i class="ti-arrow-up text-success"></i> {{ outFordeliveredOrders }}
                                    </h3>
                                    <span class="text-muted">Out For Delivery</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="width: 22%;padding : 10px">
                    <div class="card card-statistic-1">
                        <div class="card-icon l-bg-green">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">
                                        <i class="ti-arrow-up text-success"></i> {{ deliveredOrders }}
                                    </h3>
                                    <span class="text-muted">Delivered</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="width: 20%;padding : 10px">
                    <div class="card card-statistic-1">
                        <div class="card-icon l-bg-cyan">
                            <i class="fas fa-undo"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">
                                        <i class="ti-arrow-up text-success"></i> {{ failedOrder }}
                                    </h3>
                                    <span class="text-muted">Return</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>



        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Courier Performance (Last 7 days)
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <canvas id="myChart2" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Chart -->
            <RevenueChat :revenueGraphData="revenueGraphData" :revenueDates="revenueDates" />

            <RevenueBarChat :barChart="barChart" />
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Top Selling Products</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>SKU #</th>
                                        <th>Product Price</th>
                                        <th>Courier Price</th>
                                        <th>Packaging Price</th>
                                        <th>Total Cost</th>
                                        <th>Selling Price</th>
                                        <th>Net Profit</th>
                                        <th>Item Sold</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in topFiveProducts" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.variation ? item.variation.product.title : '' }}</td>
                                        <td>{{ item.variation ? item.variation.sku : '' }}</td>
                                        <td>{{ item.total_price }}</td>
                                        <td>{{ item.total_courier_cost }}</td>
                                        <td>{{ item.total_packaging_cost }}</td>
                                        <td>{{ (parseFloat(item.total_price) + parseFloat(item.total_courier_cost) +
                                            parseFloat(item.total_packaging_cost) ) }}</td>
                                        <td>{{ item.total_sell_price }}</td>
                                        <td>{{ parseFloat(item.total_sell_price) - (parseFloat(item.total_price) +
                                            parseFloat(item.total_courier_cost) + parseFloat(item.total_packaging_cost)
                                            ) }}</td>
                                        <td>{{ item.total_quantity }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Buisness Stores -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Buisness Stores</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="save-stage" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Store Name</th>
                                        <th>Started Date</th>
                                        <th>Receivable Amount</th>
                                        <th>Received Amount</th>
                                        <th>Current Balance</th>
                                        <th>Account Health</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in stores" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.store_name }}</td>
                                        <td>
                                            {{ formatDate(item.created_at) }}
                                        </td>
                                        <td>{{ item.total_payable }}</td>
                                        <td>{{ item.total_paid }}</td>
                                        <td>{{ item.total_remaining }}</td>
                                        <td class="align-middle">
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar"
                                                    :style="'width:' + item.health + '%'" :aria-valuenow="item.health"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    {{ item.health }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import moment from "moment";
import RevenueChat from "../../../components/dropshipper/RevenueChat.vue";
import RevenueBarChat from "../../../components/dropshipper/RevenueBarChat.vue";

export default {
    name: "DropShipperDashboard",
    components: {
        RevenueChat,
        RevenueBarChat
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + "/",
            topSaleProducts: [],
            tickets: [],
            ticketFilter: {
                status: "",
                ticket_number_type: '',
                ticket_number: ''
            },
            totalOrders: 0,
            totalSales: 0,
            totalProfit: 0,
            totalRemaining: 0,
            totalPaid: 0,
            totalProductCost: 0,
            totalPackingCourier: 0,
            deliveredOrders: 0,
            inProcessOrder: 0,
            outFordeliveredOrders: 0,
            failedOrder: 0,
            customerName: "",
            stores: [],
            totalTicketSum: {
                total_tickets: 0,
                awaiting_your_reply: 0,
                awaiting_yourmart_reply: 0,
                closed: 0,
                expired: 0,
                reviewed: 0,
                in_process: 0,
            },
            revenueDates: [],
            revenueGraphData: [],
            topFiveProducts: [],
            filter: {
                id: null, // To store the id from the URL
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            },
            leopardPerformance: [],
            accountHealth: 0,
            barChart: [],
        };
    },
    created() {
        this.getParamsFromUrl();
        this.fetchData();
    },

    methods: {
        getParamsFromUrl() {
            // Use URLSearchParams to extract the id and contact from the URL
            const params = new URLSearchParams(window.location.search);
            this.filter.id = params.get('id');
        },
        resetFilter() {
            this.filter = {
                from: '2020-01-01',
                to: '2050-01-01'
            }
            this.applyFilter();

            this.filter = {
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            }
        },
        applyFilter() {
            axios
                .post(this.api_url + "dropshippers", this.filter)
                .then((response) => {
                    const result = response.data.response;

                    // Assign the result values to the Vue data properties
                    this.totalProfit = result.totalProfit;
                    this.totalRemaining = result.totalRemaining;
                    this.totalPaid = result.totalPaid;

                    this.totalSales = result.totalSales;
                    this.totalProductCost = result.totalProductCost;
                    this.totalPackingCourier = result.totalPackingCourier;
                    this.totalOrders = result.totalOrders;

                    this.deliveredOrders = result.deliveredOrders;
                    this.inProcessOrder = result.inProcessOrder;
                    this.failedOrder = result.failedOrder;
                    this.outFordeliveredOrders = result.outFordeliveredOrders;
                    this.customerName = result.name;
                    this.stores = result.stores;

                    this.revenueGraphData = result.revenueGraphData;
                    this.revenueDates = result.revenueDates;

                    this.topFiveProducts = result.topFiveProducts;

                })
                .catch((err) => {

                });
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        formatDate(date) {
            return date ? moment(date).format("DD-MMM-YYYY") : "N/A";
        },
        fetchData() {
            axios
                .post(this.api_url + "dropshippers/preview", this.filter)
                .then((response) => {
                    const result = response.data.response;

                    // Assign the result values to the Vue data properties
                    this.totalProfit = result.totalProfit;
                    this.totalRemaining = result.totalRemaining;
                    this.totalPaid = result.totalPaid;

                    this.totalSales = result.totalSales;
                    this.totalProductCost = result.totalProductCost;
                    this.totalPackingCourier = result.totalPackingCourier;
                    this.totalOrders = result.totalOrders;

                    this.deliveredOrders = result.deliveredOrders;
                    this.inProcessOrder = result.inProcessOrder;
                    this.failedOrder = result.failedOrder;
                    this.outFordeliveredOrders = result.outFordeliveredOrders;
                    this.customerName = result.name;
                    this.stores = result.stores;

                    this.revenueGraphData = result.revenueGraphData;
                    this.revenueDates = result.revenueDates;
                    this.barChart = result.barChart;


                    this.topFiveProducts = result.topFiveProducts;

                    this.leopardPerformance = result.leopardPerformance;
                    this.accountHealth = result.accountHealth;

                    this.renderChart();
                })
                .catch((err) => {

                });
        },

        renderChart() {
            const ctx = document.getElementById("myChart2").getContext('2d');

            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.leopardPerformance.labels,
                    datasets: this.leopardPerformance.datasets
                },
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                drawBorder: false,
                                color: '#f2f2f2',
                            },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 10,
                                fontColor: "#9aa0ac", // Font Color
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                fontColor: "#9aa0ac", // Font Color
                            }
                        }]
                    }
                }
            });
        }
    },
};
</script>
