<template>
    <div>
        <div class="row">
            <div class="col-md-12">

                <form @submit.prevent="applyFilter" class="row col-md-12 mb-3">

                    <div class="col-md-4">
                        <label for="">From</label>
                        <input type="date" v-model="filter.from" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="">To</label>
                        <input type="date" v-model="filter.to" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="">Action</label><br>
                        <button class="btn btn-primary mr-2" @click="applyFilter">Filter</button>
                        <button class="btn btn-danger" @click="resetFilter">Reset</button>
                    </div>
                </form>
            </div>
            </div>

            <div class="card-body table-responsive" v-if="loader">
                <bullet-list-loader :width="250"> </bullet-list-loader>
            </div>

            <div class="row" v-else>

            <DashboardSectionOne :orders="orders" :approvedDropshipper="approvedDropshipper"
                :activeSeller="activeSeller" :liveProduct="liveProduct" :orderProcessed="orderProcessed" :levelsWidget="levelsWidget" />

            </div>

            <div class="row">
                <DashboardDropshipperGraph
                    :dropshipperGraph="dropshipperGraph"
                />


                <DashboardRevenueOrderChart
                    :revenueOrderGraph="revenueOrderGraph"
                />

                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Courier Statistics</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="myChart20" height="70"></canvas>
                            <div class="statistic-details mt-1">
                            </div>
                        </div>
                    </div>

                </div>
                                   
                    <!-- New products last 30 days -->

                    <div class="row">
    <!-- Orders Graph -->
     
    <div class="col-12 col-sm-12 col-lg-12">
                <DropshipperApprovedGraph :dropshipperGraphLast120Days="dropshipperGraphLast120Days"/>
            </div> 
    <div class="col-12 col-sm-12 col-lg-12 mb-4">
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="orders"
                    title="Orders"
                />
            </div>

            <!-- Sales Graph -->
            <div class="col-12 col-sm-12 col-lg-12 mb-4">
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="sales"
                    title="Sales"
                    :is-currency="true"
                />
            </div>

            <!-- Profit Graph -->
            <div class="col-12 col-sm-12 col-lg-12 mb-4">
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="profit"
                    title="Profit"
                    :is-currency="true"
                />
            </div>

            <!-- Returns Graph -->
            <div class="col-12 col-sm-12 col-lg-12 mb-4">
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="returns"
                    title="Returns"
                />
            </div>
            <div class="col-md-12">
                <NewProducts30DaysGraph :newproducts30daysgraph="newproducts30daysgraph"/>
             </div>  

            
</div>
 <!-- 30 days graph -->



                
            </div>

        
            <div class="card-body table-responsive" v-if="loader">
                <bullet-list-loader :width="250"> </bullet-list-loader>
            </div>

            <div class="row" v-else>

            <DashboardSectionTwo :dropshipper="pendingPayouts" :pendingRequests="pendingRequests"
                :allProcessedOrders="allProcessedOrders" />

            <DashboardSectionThree :allProcessedOrders="allProcessedOrders" :processOrders="processOrders" />

            <DashboardTopFiveDropshipper :topFiveDropshippers='topFiveDropshippers' />

            <DashboardTopFiveProduct :topFiveProduct='topFiveProduct' />

            <DashboardTopFiveSupplier :topFiveSuppliers='topFiveSuppliers' />

            <DashboardSectionFour :inventoryStatus="inventoryStatus" />
 
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Tickets Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Total Tickets</th>
                                    <th>Awaiting Your Reply</th>
                                    <th>Awaiting YourMart Reply</th>
                                    <th>Closed</th>
                                    <th>Expired</th>
                                    <th>Reviewed</th>
                                    <th>In-Process</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ totalTicketSum.total_tickets }}</td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.awaiting_your_reply) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-success"
                                                :style="{ width: getPercentage(totalTicketSum.awaiting_your_reply) + '%' }">
                                            </div>
                                        </div>
                                        {{ getPercentage(totalTicketSum.awaiting_your_reply) }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.awaiting_yourmart_reply) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-primary"
                                                :style="{ width: getPercentage(totalTicketSum.awaiting_yourmart_reply) + '%' }">
                                            </div>
                                        </div>
                                        {{ totalTicketSum.awaiting_yourmart_reply }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.closed) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-danger"
                                                :style="{ width: getPercentage(totalTicketSum.closed) + '%' }"></div>
                                        </div>
                                        {{ totalTicketSum.closed }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.expired) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-success"
                                                :style="{ width: getPercentage(totalTicketSum.expired) + '%' }"></div>
                                        </div>
                                        {{ totalTicketSum.expired }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.reviewed) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-info"
                                                :style="{ width: getPercentage(totalTicketSum.reviewed) + '%' }"></div>
                                        </div>
                                        {{ totalTicketSum.reviewed }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.in_process) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-info"
                                                :style="{ width: getPercentage(totalTicketSum.in_process) + '%' }">
                                            </div>
                                        </div>
                                        {{ totalTicketSum.in_process }}
                                    </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <TicketTypesGraph :ticketTypesGraphData="ticketTypesGraphData"/>
             </div> 
             
            <div class="col-md-6 mt-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Categories-Wise Published Products</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li v-for="category in categoryWiseProducts" :key="category.id"
                                class="list-group-item d-flex justify-content-between align-items-center">
                                {{ category.name }}
                                <span class="badge badge-primary badge-pill">{{ category.product_count }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mt-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Tag-Wise Published Products</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li v-for="tag in tagWiseProducts" :key="tag.id"
                                class="list-group-item d-flex justify-content-between align-items-center">
                                {{ tag.name }}
                                <span class="badge badge-primary badge-pill">{{ tag.tagged_count }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <DropshipperDetails :details="dropShipperDetails" />
    </div>
</template>

<script>
import CourierStatsGraph from '../components/admin/dashboard/CourierStatsGraph.vue';
import DashboardDropshipperGraph from '../components/admin/dashboard/DashboardDropshipperGraph.vue';
import DashboardRevenueOrderChart from '../components/admin/dashboard/DashboardRevenueOrderChart.vue';
import DashboardSectionFour from '../components/admin/dashboard/DashboardSectionFour.vue';
import DashboardSectionOne from '../components/admin/dashboard/DashboardSectionOne.vue';
import DashboardSectionThree from '../components/admin/dashboard/DashboardSectionThree.vue';
import DashboardSectionTwo from '../components/admin/dashboard/DashboardSectionTwo.vue';
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
        DashboardSectionTwo,
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
        fetchCategoryandTagWiseProducts(){
            let vm = this;
                axios
                .get(this.api_url + "users/dashboard/product-wise-count")
                .then((response) => {

                    const results = response.data.response;
                    vm.categoryWiseProducts  = results.categoryWiseProducts,
                    vm.tagWiseProducts       = results.tagWiseProducts
                })
        },
        fetchTicketStatusCounts() {
            axios.get(this.api_url + 'tickets/status-counts').then((response) => {
                const data = response.data;
                this.totalTicketSum.total_tickets = data.total_tickets;
                this.totalTicketSum.awaiting_your_reply = data.awaiting_your_reply;
                this.totalTicketSum.awaiting_yourmart_reply = data.awaiting_yourmart_reply;
                this.totalTicketSum.closed = data.closed;
                this.totalTicketSum.expired = data.expired;
                this.totalTicketSum.reviewed = data.reviewed;
                this.totalTicketSum.in_process = data.in_process;
            });
        },
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
                .post(this.api_url + "users/dashboard", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.approvedDropshipper = results.approvedDropshipper;
                    vm.activeSeller = results.activeSeller;
                    vm.liveProduct = results.liveProduct;
                    vm.orderProcessed = results.orderProcessed;
                    vm.pendingPayouts = results.payOuts;
                    vm.pendingRequests = results.pendingRequests;
                    vm.allProcessedOrders = results.allProcessedOrders;
                    vm.processOrders = results.orders;
                    vm.topFiveDropshippers = results.topFiveDropshippers;
                    vm.topFiveProduct = results.topFiveSellingProduct;
                    vm.topFiveSuppliers = results.topFiveSuppliers;
                    vm.inventoryStatus = results.inventoryStatus;
                    vm.dropshipperGraph = results.dropshipperGraph;
                    vm.revenueOrderGraph = results.revenueOrderGraph;
                    vm.courierPerformance = results.courierPerformance;
                    const levels = results.levels
                    vm.levelsWidget = {
                        level1: levels.filter(level => level.level === 'Level 01').length,
                        level2: levels.filter(level => level.level === 'Level 02').length,
                        level3: levels.filter(level => level.level === 'Level 03').length,
                        topRatedSeller: levels.filter(level => level.level === 'Top Rated Seller').length
                    };
                    vm.loader = false;

                     // 30 days graphs
        
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
