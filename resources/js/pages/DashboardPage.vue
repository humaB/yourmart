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

            <DashboardSectionOne :orders="orders" :approvedDropshipper="approvedDropshipper"
                :activeSeller="activeSeller" :liveProduct="liveProduct" :orderProcessed="orderProcessed" />

            <DashboardDropshipperGraph
                :dropshipperGraph="dropshipperGraph"
            />

            <DashboardRevenueOrderChart
                :revenueOrderGraph="revenueOrderGraph"
            />

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
                                <tr>
                                    <th>Total Tickets</th>
                                    <th>Awaiting Your Reply</th>
                                    <th>Awaiting YourMart Reply</th>
                                    <th>Closed</th>
                                    <th>Expired</th>
                                    <th>Reviewed</th>
                                    <th>In-Process</th>
                                </tr>
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
                            </table>
                        </div>
                    </div>
                </div>
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

            <!-- <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Top 10 Dropshippers</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="topDropshipperTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Stores</th>
                                        <th>Orders</th>
                                        <th>Returned Orders</th>
                                        <th>Success Rate</th>
                                        <th>Sales</th>
                                        <th>COGS</th>
                                        <th>Packing & Labeling</th>
                                        <th>Profit</th>
                                        <th>Payable</th>
                                        <th>Withdraw</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in topDropshippers" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td><a href="#" @click="fetchDropshipperDetails(item.dropshipper.id)"
                                                data-toggle="modal" data-target="#dropShipperDetail">{{ item.name }}</a>
                                        </td>
                                        <td>{{ item.dropshipper.shops?.length || 0 }}</td>
                                        <td>{{ item.total_orders }}</td>
                                        <td>{{ item.total_returns }}</td>
                                        <td class="align-middle" width="30%">
                                            <div class="progress-text text-right">
                                                {{ calculateHealth(item) }}%
                                            </div>
                                            <div class="progress" data-height="2">
                                                <div :class="['progress-bar', calculateHealth(item) > 90 ? 'bg-success' : 'bg-primary']"
                                                    :style="{ width: calculateHealth(item) + '%' }">
                                                </div>
                                            </div>
                                        </td>

                                        <td>{{ formatPrice(calculateDeliveredSales(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateProductCost(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateTotalCost(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateProfit(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(item.dropshipper.total_payable) }}</td>
                                        <td>{{ formatPrice(item.dropshipper.total_paid) }}</td>
                                        <td>{{ formatPrice(item.dropshipper.remaining_amount) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Top Selling Products</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="topSellingProductTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>SKU #</th>
                                        <th>Item Sold</th>
                                        <th>Buying Avg Price</th>
                                        <th>Buying Cost</th>
                                        <th>Selling Avg Price</th>
                                        <th>Selling Cost</th>
                                        <th>Net Profit</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in topTenProducts" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.variation ? item.variation.product.title : '' }}</td>
                                        <td>{{ item.variation ? item.variation.sku : '' }}</td>
                                        <td>{{ item.total_quantity }}</td>
                                        <td>{{ item.variation.avg_price }}</td>
                                        <td>{{ formatPrice(item.variation.avg_price * item.total_quantity) }}</td>
                                        <td>{{ (item.selling_price / item.total_quantity).toFixed(2) }}</td>
                                        <td>{{ formatPrice(item.selling_price) }}</td>

                                        <td>{{ formatPrice(parseFloat(item.selling_price) - (
                                            parseFloat(item.total_quantity) * parseFloat(item.variation.avg_price)))
                                            }}</td>
                                        <td>{{ ((parseFloat(item.selling_price) - (parseFloat(item.total_quantity) *
                                            parseFloat(item.variation.avg_price))) / (parseFloat(item.total_quantity)
                                                * parseFloat(item.variation.avg_price)) * 100).toFixed(2) }}%</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body row">
                        <div class="col-md-6 mt-5">
                            <h6>Low Stock Products</h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>QTY SOLD</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in lowStock" :key="product.id">
                                        <td>{{ product.title }}</td>
                                        <td>{{ product.variation.sku || '' }}</td>
                                        <td>{{ product.issuance_sum_quantity || 0 }}</td>
                                        <td>{{ formatPrice( product.issuance_sum_total || 0 ) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 mt-5">
                            <h6>High Stock Products</h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>QTY SOLD</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in highStock" :key="product.id">
                                        <td>{{ product.title }}</td>
                                        <td>{{ product.variation.sku || '' }}</td>
                                        <td>{{ product.issuance_sum_quantity || 0 }}</td>
                                        <td>{{ formatPrice( product.issuance_sum_total || 0 ) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> -->

        </div>
        <DropshipperDetails :details="dropShipperDetails" />
    </div>
</template>

<script>
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
        DashboardRevenueOrderChart
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
            dropshipperGraph : {},
            revenueOrderGraph : {}
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
        calculateHealth(item) {
            const deliveredCount = item.total_orders; // Count of delivered orders
            const returnedCount = item.total_returns;   // Count of returned orders

            // You can now use these counts for further logic, e.g., calculating account health
            const totalOrders = deliveredCount + returnedCount;
            let accountHealth = 0;
            if (totalOrders > 0) {
                accountHealth = (deliveredCount / totalOrders) * 100;
            }
            return Math.round(accountHealth);
        },
        calculateDeliveredSales(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.selling_price) + parseFloat(order.advance_amount), 0);
        },
        calculateProductCost(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.total_bill) - parseFloat(order.courier_service_price) - parseFloat(order.packaging_price), 0);
        },
        calculateTotalCost(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.courier_service_price) + parseFloat(order.packaging_price), 0);
        },
        calculateProfit(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + (parseFloat(order.selling_price) + parseFloat(order.advance_amount)) - (parseFloat(order.total_bill)), 0);
        },
        fetchData(data) {
            let vm = this;
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
                })

        },

        applyFilter() {
            this.fetchData(this.filter);
            this.clearDatatable();
            this.top10Dropshippers(this.filter);
            this.fetchPurchaseOrders(this.filter);
        },
        resetFilter() {

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
        }
    },
};
</script>
