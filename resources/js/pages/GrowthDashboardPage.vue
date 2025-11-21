<template>
    <div>


            <div class="card-body table-responsive" v-if="loader">
                <bullet-list-loader :width="250"> </bullet-list-loader>
            </div>

            <div class="row" v-else>

            <OrdersectionGrowthDashboard :dropshipper="pendingPayouts" :pendingRequests="pendingRequests"
                :allProcessedOrders="allProcessedOrders" :todaysData="todaysData"/>
    </div>

    <hr class="border border-secondary border-2 opacity-50">

               <div class="row">

                <div class="col-12 col-sm-12 col-lg-12">
                    <h2> Orders </h2>
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="orders"
                    title="Orders"
                />
                <hr class="border border-secondary border-2 opacity-50">
                <h2> Sales </h2>
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="sales"
                    title="Sales"
                    :is-currency="true"
                />
           
                <hr class="border border-secondary border-2 opacity-50">
                <h2> Profit </h2>
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="profit"
                    title="Profit"
                    :is-currency="true"
                />
                <hr class="border border-secondary border-2 opacity-50">
                <h2> Registrations </h2>
                <DropshipperApprovedGraph :dropshipperGraphLast120Days="dropshipperGraphLast120Days"/>
                <!-- <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="returns"
                    title="Returns"
                /> -->
              </div>

            
</div>


        
            
</div>
</template>

<script>
import CourierStatsGraph from '../components/admin/dashboard/CourierStatsGraph.vue';
import DashboardDropshipperGraph from '../components/admin/dashboard/DashboardDropshipperGraph.vue';
import DashboardRevenueOrderChart from '../components/admin/dashboard/DashboardRevenueOrderChart.vue';
import DashboardSectionFour from '../components/admin/dashboard/DashboardSectionFour.vue';
import DashboardSectionOne from '../components/admin/dashboard/DashboardSectionOne.vue';
import DashboardSectionThree from '../components/admin/dashboard/DashboardSectionThree.vue';
import OrdersectionGrowthDashboard from '../components/admin/dashboard/OrdersectionGrowthDashboard.vue';
import DashboardTopFiveDropshipper from '../components/admin/dashboard/DashboardTopFiveDropshipper.vue';
import DashboardTopFiveProduct from '../components/admin/dashboard/DashboardTopFiveProduct.vue';
import DashboardTopFiveSupplier from '../components/admin/dashboard/DashboardTopFiveSupplier.vue';
import DropshipperDetails from '../components/admin/request/DropshipperDetails.vue';
import DropshipperApprovedGraph from '../components/graphs/DropshipperApprovedGraph.vue';
import NewProducts30DaysGraph from '../components/graphs/NewProducts30DaysGraph.vue'; 
import GenericBarChart from '../components/graphs/GenericBarChart.vue';
import TicketTypesGraph from "../components/graphs/TicketTypesGraph.vue";

import { BulletListLoader } from "vue-content-loader";

export default {
    name: 'DashboardPage',
    components: {
        DropshipperDetails,
        DashboardSectionOne,
        OrdersectionGrowthDashboard,
        DashboardSectionThree,
        DashboardTopFiveDropshipper,
        DashboardTopFiveProduct,
        DashboardTopFiveSupplier,
        DashboardSectionFour,
        DashboardDropshipperGraph,
        DashboardRevenueOrderChart,
         // 30 days graphs
        DropshipperApprovedGraph,
        NewProducts30DaysGraph,
        GenericBarChart ,
        TicketTypesGraph,
         // 30 days graphs
        CourierStatsGraph,
        BulletListLoader
    },
    data() {
        return {
            api_url: process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            filter: {
                dropshipper: 0,
                shop: 0,
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            },
            loader : true,
            topTenProducts: [],
            dropshipper: {},
            topDropshippers: [],
            totalOrders: 0,
            totalTicketSum: {
                total_tickets: 0,
                awaiting_your_reply: 0,
                awaiting_yourmart_reply: 0,
                closed: 0,
                expired: 0,
                reviewed: 0,
                in_process: 0,
            },
            dropShipperDetails: {},
            po: {
                totalPo: 0,
                approved: 0,
                pending: 0,
                rejected: 0,
                totalAmount: 0,
                remaining: 0,
                paid: 0
            },
            orders: {
                totalOrder: 0,
                inProcess: 0,
                outOfDelivery: 0,
                delivered: 0,
                returns: 0,
                normalOrders: 0,
                darazOrders: 0,
                cashOrders: 0,
                grossSales: 0,
                itemSolds: 0,
                productCost: 0,
                packing: 0,
                packingProfit: 0,
                courier: 0,
                courierProfit: 0,
                costOfGood: 0,
                grossProfit: 0
            },
            dropshipper: {
                total: 0,
                paid: 0,
                remaining: 0,
                total_sellers: 0
            },
            inventoryStatus: {},
            categoryWiseProducts: [],
            tagWiseProducts: [],
            fastMovingProducts: [],
            slowMovingProducts: [],
            lowStock: [],
            highStock: [],
            approvedDropshipper: {},
            activeSeller: {},
            liveProduct: {},
            orderProcessed: {},
            pendingPayouts: {},
            pendingRequests: {
                dropshippers: {
                    total: 0,
                    pending: 0,
                    approved: 0,
                    reject: 0
                },
                supplier: {
                    total: 0,
                    pending: 0,
                    approved: 0,
                    reject: 0
                },
            },
            allProcessedOrders: {},
            processOrders: {},
            topFiveDropshippers: [],
            topFiveProduct: [],
            topFiveSuppliers: [],
            // 30 days graphs
            dropshipperGraphLast120Days: {},
            newproducts30daysgraph:{},
            dashboardGraphs: {},
            ticketTypesGraphData: {},
           
            // 30 days graphs
            dropshipperGraph : {},
            revenueOrderGraph : {},
            levelsWidget : {
                level1 : 0,
                level2 : 0,
                level3 : 0,
                topRatedSeller : 0
            },
            courierPerformance : []
        };
    },
    created() {
        this.fetchData({ from: null, to: null });
        this.fetchTicketStatusCounts();
        this.fetchCategoryandTagWiseProducts();
    },
    methods: {
        fetchDropshipperDetails(id) {
            let vm = this;
            axios
                .post(this.api_url + "dropshippers/details", { id })
                .then((response) => {
                    vm.dropShipperDetails = response.data.response[0]
                });

        },
        fetchData(data) {
            let vm = this;
            vm.loader = true;
            axios
                .post(this.api_url + "growthdashboard/", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.approvedDropshipper = results.approvedDropshipper;
                    vm.pendingPayouts = results.payOuts;
                    vm.pendingRequests = results.pendingRequests;
                     // 30 days graphs
                     vm.todaysData = results.todaysData || {};
        
                    vm.dropshipperGraphLast120Days = results.dropshipperGraphLast120Days;
                    vm.dashboardGraphs = results.dashboardGraphs || {};
                    vm.newproducts30daysgraph = results.newproducts30daysgraph;
                    vm.ticketTypesGraphData = results.ticketTypesGraphData;
                    
             // 30 days graphs
                   
             vm.renderChart()
                })

        },
        applyFilter() {
            this.fetchData(this.filter);
            this.clearDatatable();
        },
        resetFilter() {
            this.fetchData({from : null, to : null});
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        getPercentage(statusCount) {
            if (this.totalTicketSum.total_tickets === 0) return 0;
            return Math.round((statusCount / this.totalTicketSum.total_tickets) * 100);
        },
        renderChart() {
            const ctx = document.getElementById("myChart20").getContext('2d');

            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.courierPerformance.labels,
                    datasets: this.courierPerformance.datasets
                },
                options: {
                    legend: {
                        display: true
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
