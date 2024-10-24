<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Statistics <code>( In process )</code></h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>-</th>
                                        <th>Total Orders</th>
                                        <th>Product Price</th>
                                        <th>Courier</th>
                                        <th>Packaging</th>
                                        <th>Total Sales</th>
                                        <th>Amount Received</th>
                                        <th>Amount Remaining</th>
                                    </tr>
                                    <tr>
                                        <th>Today’s</th>
                                        <td>{{formatPrice(todaySummary.totalOrders) }}</td>
                                        <td>{{ formatPrice(todaySummary.productPrice - ( todaySummary.courier + todaySummary.packaging )) }}</td>
                                        <td>{{ formatPrice(todaySummary.courier) }}</td>
                                        <td>{{ formatPrice(todaySummary.packaging) }}</td>
                                        <td>{{ formatPrice(todaySummary.totalSales) }}</td>
                                        <td>{{ formatPrice(todaySummary.amountReceived) }}</td>
                                        <td>{{ formatPrice(todaySummary.amountRemaining) }}</td>
                                    </tr>

                                    <!-- Overall Orders -->
                                    <tr>
                                        <th>Overall</th>
                                        <td>{{ formatPrice(overallSummary.totalOrders) }}</td>
                                        <td>{{ formatPrice(overallSummary.productPrice - ( overallSummary.courier + overallSummary.packaging) ) }}</td>
                                        <td>{{ formatPrice(overallSummary.courier) }}</td>
                                        <td>{{ formatPrice(overallSummary.packaging) }}</td>
                                        <td>{{ formatPrice(overallSummary.totalSales) }}</td>
                                        <td>{{ formatPrice(overallSummary.amountReceived) }}</td>
                                        <td>{{ formatPrice(overallSummary.amountRemaining) }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">
                    <TableHeader :tableHeader="tableHeader" />

                    <div class="card" v-if="role == 'admin' || role == 'supervisor'">
                        <div class="card-body px-2">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Total Orders</th>
                                        <th>Collection</th>
                                        <th>Inventory</th>
                                        <th>QC Manager</th>
                                        <th>Packing </th>
                                        <th>Audit Manager</th>
                                        <th>Dispatched</th>
                                        <th>Under Review</th>
                                        <th>Rejected</th>
                                        <th>Delivered</th>
                                        <th>Returned</th>
                                        <th>Returned to store</th>
                                    </tr>
                                    <tr>
                                        <td>{{ totalOrders.totalOrders }}</td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.orderCollection) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-warning"
                                                    :style="{ width: getPercentage(totalOrders.orderCollection) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.orderCollection }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.inventoryManager) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-primary"
                                                    :style="{ width: getPercentage(totalOrders.inventoryManager) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.inventoryManager }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.qcManager) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-info"
                                                    :style="{ width: getPercentage(totalOrders.qcManager) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.qcManager }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.packing) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-dark"
                                                    :style="{ width: getPercentage(totalOrders.packing) + '%' }"></div>
                                            </div>
                                            {{ totalOrders.packing }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.audit) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-light"
                                                    :style="{ width: getPercentage(totalOrders.audit) + '%' }"></div>
                                            </div>
                                            {{ totalOrders.audit }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.dispatched) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-success"
                                                    :style="{ width: getPercentage(totalOrders.dispatched) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.dispatched }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.underReview) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-warning"
                                                    :style="{ width: getPercentage(totalOrders.underReview) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.underReview }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.rejected) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-danger"
                                                    :style="{ width: getPercentage(totalOrders.rejected) + '%' }"></div>
                                            </div>
                                            {{ totalOrders.rejected }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.delivered) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-success"
                                                    :style="{ width: getPercentage(totalOrders.delivered) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.delivered }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.returned) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-danger"
                                                    :style="{ width: getPercentage(totalOrders.returned) + '%' }"></div>
                                            </div>
                                            {{ totalOrders.returned }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.returnedToStock) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-danger"
                                                    :style="{ width: getPercentage(totalOrders.returnedToStock) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.returnedToStock }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <form @submit.prevent="applyFilter" class="row col-md-12 mb-3">
                            <div class="col-md-3">
                                <label for="">Select Status</label>
                                <select v-model="filter.status" class="form-control">
                                    <option value="">Select from the following</option>
                                    <option value="0">Order Collection</option>
                                    <option value="1">Inventory Manager</option>
                                    <option value="2">QA Manager</option>
                                    <option value="3">Packing/Dispatch</option>
                                    <option value="4">Auditor</option>
                                    <option value="5">Under Review</option>
                                    <option value="7">Rejected</option>
                                    <option value="11">Out for Delivery</option>
                                    <option value="8">Delivered</option>
                                    <option value="9">Returned</option>
                                    <option value="10">Returned to store</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="">From</label>
                                <input type="date" v-model="filter.from" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="">To</label>
                                <input type="date" v-model="filter.to" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="">Action</label><br>
                                <button class="btn btn-primary mr-2" @click="applyFilter">Filter</button>
                                <button class="btn btn-danger" @click="resetFilter">Reset</button>
                            </div>
                        </form>
                    </div>

                    <div class="card-body row">
                        <!-- Table -->
                        <div class="col-md-12 mt-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="card-body table-responsive" v-if="loader">
                                            <bullet-list-loader :width="250"> </bullet-list-loader>
                                        </div>
                                        <div class="col-md-12 table-responsive" v-else>
                                            <table class="table table-bordered" :id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Sr #</th>
                                                        <th>Reference ID</th>
                                                        <th>Type</th>
                                                        <th>Dropshipper</th>
                                                        <th>Order #</th>
                                                        <th>Tracking Number</th>
                                                        <th>Product Price</th>
                                                        <th>Courier</th>
                                                        <th>Packaging</th>
                                                        <th>Total Cost</th>
                                                        <th>Received</th>
                                                        <th>Remaining</th>
                                                        <th>COD</th>
                                                        <th>Advance</th>
                                                        <th>Total Payable</th>
                                                        <th>Total Paid</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item, index) in orders" :key="item.id">
                                                        <td>{{ index + 1 }}</td>
                                                        <td>{{ item.id }}</td>
                                                        <td>{{ item.type }}</td>
                                                        <td>{{ item.user ? item.user.name : '-' }}</td>
                                                        <td>
                                                            {{ item.shop ? `${item.shop.store_name.substring(0,
                                                                3)}-${item.order_no}` : item.order_no }}
                                                        </td>
                                                        <td>
                                                            <span v-if="item.type === 'Normal'">
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#trackingInformation"
                                                                    @click="fetchTracking(item.id)">
                                                                    {{ item.tracking_number }}
                                                                </a>
                                                            </span>
                                                            <span v-else>
                                                                {{ item.type }}
                                                            </span>
                                                        </td>

                                                        <td>{{ formatPrice(parseFloat(item.total_bill) - (
                                                            parseFloat(item.courier_service_price) +
                                                            parseFloat(item.packaging_price))) }}</td>
                                                        <td>{{ item.courier_service_price }}</td>
                                                        <td>{{ item.packaging_price }}</td>
                                                        <td>{{ formatPrice(item.total_bill) }}</td>
                                                        <td>{{ formatPrice(item.paid_amount) }}</td>
                                                        <td>{{ formatPrice(item.remaining_amount) }}</td>
                                                        <td>{{ formatPrice(item.selling_price) }}</td>
                                                        <td>{{ formatPrice(item.advance_amount) }}</td>
                                                        <td>{{ formatPrice(item.total_profit) }}</td>
                                                        <td>{{ formatPrice(item.total_paid_profit) }}</td>
                                                        <td>
                                                            <span class="badge badge-warning text-dark"
                                                                v-if="item.status == 0">Order Collection</span>
                                                            <span class="badge badge-info text-dark"
                                                                v-else-if="item.status == 1">Inventory Issuance</span>
                                                            <span class="badge badge-secondary"
                                                                v-else-if="item.status == 2">QC</span>
                                                            <span class="badge badge-success"
                                                                v-else-if="item.status == 3">Packing/Dispatch</span>
                                                            <span class="badge badge-warning text-dark"
                                                                v-else-if="item.status == 4">Audit</span>
                                                            <span class="badge badge-succes"
                                                                v-else-if="item.status == 5">Dispatched</span>
                                                            <span class="badge badge-danger"
                                                                v-else-if="item.status == 6">Rejection Under
                                                                Review</span>
                                                            <span class="badge badge-danger"
                                                                v-else-if="item.status == 7">Rejected</span>
                                                            <span class="badge badge-success"
                                                                v-else-if="item.status == 8">Delivered</span>
                                                            <span class="badge badge-danger"
                                                                v-else-if="item.status == 9">Returned</span>
                                                            <span class="badge badge-danger"
                                                                v-else-if="item.status == 10">Returned To Stock</span>
                                                            <span class="badge badge-warning"
                                                                v-else-if="item.status == 11">Out for delivery</span>
                                                        </td>
                                                        <td>{{ formatDate(item.created_at) }}</td>
                                                        <td>
                                                            <button class="btn btn-info" @click="fetchDetail(item.id)"
                                                                data-toggle="modal" data-target="#ticket"
                                                                title="View Details"><i class="fa fa-eye"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- END TABLE -->
                    </div>
                </div>
            </div>
        </div>

        <OrderDetailView :revertLoader="revertLoader" :rejectLoader="rejectLoader" :paidAmountLoader="paidAmountLoader"
            :details="details" :loader="commentLoader" :role="role" @addComment="addComment($event)"
            @forward="forward($event)" @reject="reject($event)" @revert="revert($event)"
            @fetchDropshipperDetails="fetchDropshipperDetails($event)" @updatePaidAmount="updatePaidAmount($event)"
            @updatePackagingAmount="updatePackagingAmount($event)" @markasReplacement="markasReplacement($event)" />
        <DropshipperDetails :details="dropShipperDetails" />

        <TrackingDetailPopup :trackingDetails="trackingDetails" />
        <!-- Modal -->
        <OrderMarkasReplacementConfirmation :orderID="orderID" :loader="markasReplacementLoader"
            @markasReplacementConfirmation="markasReplacementConfirmation($event)" />

    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../../components/table/TableHeaderComponent.vue";
import OrderDetailView from "../../../../components/inventory/product/order/OrderDetailView.vue";
import DropshipperDetails from "../../../../components/admin/request/DropshipperDetails.vue";
import TrackingDetailPopup from "../../../../components/inventory/product/order/TrackingDetailPopup.vue";
import OrderMarkasReplacementConfirmation from "../../../../components/inventory/product/order/OrderMarkasReplacementConfirmation.vue";

export default {
    name: 'ProductOrderPage',
    components: {
        TableHeader,
        BulletListLoader,
        OrderDetailView,
        DropshipperDetails,
        OrderMarkasReplacementConfirmation,
        TrackingDetailPopup
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "All Orders",
            },
            th: ["Sr #", "Order #", "Belongs To", "Total Amount", "Paid Amount", "Remaining Amount", "Added Date", "Status", "Action"],
            table_id: "moq_table",
            btnLoader: false,
            orders: [],
            loader: true,
            details: {},
            commentLoader: false,
            rejectLoader: false,
            revertLoader: false,
            role: '',
            dropShipperDetails: {},
            paidAmountLoader: false,
            totalOrders: {
                totalOrders: 0,
                orderCollection: 0,
                inventoryManager: 0,
                qcManager: 0,
                packing: 0,
                audit: 0,
                dispatched: 0,
                underReview: 0,
                rejected: 0,
                delivered: 0,
                returned: 0,
                returnedToStock: 0
            },
            filter: {
                status: '',
                from: '',
                to: '',
            },
            trackingDetails: [],
            orderID: '',
            markasReplacementLoader: false
        };
    },
    computed: {
        // Calculate today's summary
        todaySummary() {
            let today = new Date().toISOString().slice(0, 10); // Get today's date in YYYY-MM-DD format

            return this.orders.reduce((totals, order) => {
                let orderDate = new Date(order.created_at).toISOString().slice(0, 10);

                if (orderDate === today && order.status <= 4) {
                    totals.totalOrders++;
                    totals.productPrice += parseFloat(order.total_bill || 0);
                    totals.courier += parseFloat(order.courier_service_price || 0);
                    totals.packaging += parseFloat(order.packaging_price || 0);
                    totals.totalSales += parseFloat(order.total_bill || 0);
                    totals.amountReceived += parseFloat(order.paid_amount || 0);
                    totals.amountRemaining += parseFloat(order.remaining_amount || 0);
                }

                return totals;
            }, {
                totalOrders: 0,
                productPrice: 0,
                courier: 0,
                packaging: 0,
                totalSales: 0,
                amountReceived: 0,
                amountRemaining: 0,
            });
        },

        // Calculate overall summary
        overallSummary() {
            return this.orders.reduce((totals, order) => {
                if (order.status <= 4) {
                    totals.totalOrders++;
                    totals.productPrice += parseFloat(order.total_bill || 0);
                    totals.courier += parseFloat(order.courier_service_price || 0);
                    totals.packaging += parseFloat(order.packaging_price || 0);
                    totals.totalSales += parseFloat(order.total_bill || 0);
                    totals.amountReceived += parseFloat(order.paid_amount || 0);
                    totals.amountRemaining += parseFloat(order.remaining_amount || 0);
                }
                return totals;
            }, {
                totalOrders: 0,
                productPrice: 0,
                courier: 0,
                packaging: 0,
                totalSales: 0,
                amountReceived: 0,
                amountRemaining: 0,
            });
        }
    },

    created() {
        this.fetchOrders();
    },
    methods: {
        markasReplacement(data) {
            this.orderID = data.id
        },
        markasReplacementConfirmation() {
            let vm = this;
            vm.markasReplacementLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/mark-as-replacement", { id: this.orderID })
                .then((response) => {
                    vm.markasReplacementLoader = false;

                    $("#markasReplacement").modal('hide');
                    this.fetchDetail(this.orderID);
                    return swal({
                        title: "Success",
                        text: "Marked as Replacement Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        fetchTracking(id) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/orders/tracking", { id })
                .then((response) => {
                    vm.trackingDetails = response.data.response
                });
        },
        applyFilter() {
            this.clearDataTable();
            this.fetchOrders();
        },
        resetFilter() {
            let vm = this;
            vm.filter = {
                status: '',
                from: '',
                to: ''
            }

            this.clearDataTable();
            this.fetchOrders();
        },
        getPercentage(statusCount) {
            if (this.totalOrders.totalOrders === 0) return 0;
            return Math.round((statusCount / this.totalOrders.totalOrders) * 100);
        },
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        updatePaidAmount(data) {
            let vm = this;
            vm.paidAmountLoader = true;
            axios.post(this.api_url + "inventory/products/orders/update-paid-amount", data)
                .then((response) => {

                    this.fetchDetail(data.id);

                    vm.paidAmountLoader = false;
                    return swal({
                        title: "Success",
                        text: "Amount Updated Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.paidAmountLoader = false;
                    return swal({
                        title: "Error",
                        text: "Oops.. Something went wrong",
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        updatePackagingAmount(data) {
            let vm = this;
            vm.paidAmountLoader = true;
            axios.post(this.api_url + "inventory/products/orders/update-packaging-amount", data)
                .then((response) => {

                    this.fetchDetail(data.id);

                    vm.paidAmountLoader = false;
                    return swal({
                        title: "Success",
                        text: "Amount Updated Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.paidAmountLoader = false;
                    return swal({
                        title: "Error",
                        text: "Oops.. Something went wrong",
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        fetchOrders() {
            let vm = this;

            vm.loader = false;
            axios
                .get(this.api_url + "inventory/products/orders", {
                    params: {
                        status: vm.filter.status,
                        from: vm.filter.from,
                        to: vm.filter.to,
                    },
                })
                .then((response) => {
                    vm.orders = response.data.response.orders
                    vm.role = response.data.response.role

                    // Reset totalOrders object before populating it
                    vm.totalOrders = {
                        totalOrders: 0,
                        orderCollection: 0,
                        inventoryManager: 0,
                        qcManager: 0,
                        packing: 0,
                        audit: 0,
                        underReview: 0,
                        rejected: 0,
                        dispatched: 0,
                        delivered: 0,
                        returned: 0,
                        returnedToStock: 0
                    };

                    // Process orders and calculate total counts based on the status
                    vm.orders.forEach((order) => {
                        vm.totalOrders.totalOrders++;
                        switch (order.status) {
                            case 0: // Order Collection
                                vm.totalOrders.orderCollection++;
                                break;
                            case 1: // Inventory Manager
                                vm.totalOrders.inventoryManager++;
                                break;
                            case 2: // QA Manager
                                vm.totalOrders.qcManager++;
                                break;
                            case 3: // Packing/Dispatch
                                vm.totalOrders.packing++;
                                break;
                            case 4: // Auditor
                                vm.totalOrders.audit++;
                                break;
                            case 5: // Dispatched
                                vm.totalOrders.dispatched++;
                                break;
                            case 6: // Under Review
                                vm.totalOrders.underReview++;
                                break;
                            case 7: // Rejected
                                vm.totalOrders.rejected++;
                                break;
                            case 8: // Delivered
                                vm.totalOrders.delivered++;
                                break;
                            case 9: // Returned
                                vm.totalOrders.returned++;
                                break;
                            case 10: // Returned
                                vm.totalOrders.returnedToStock++;
                                break;
                        }
                    });
                });
        },
        fetchDetail(id) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/orders/details", { id })
                .then((response) => {
                    vm.details = response.data.response[0]
                });
        },
        fetchDropshipperDetails(data) {
            let vm = this;
            axios
                .post(this.api_url + "dropshippers/details", { id: data.id })
                .then((response) => {
                    vm.dropShipperDetails = response.data.response[0]
                });

        },
        forward(data) {
            let vm = this;
            vm.commentLoader = true;
            axios.post(this.api_url + "inventory/products/orders/update-status", data)
                .then((response) => {

                    vm.fetchOrders();
                    vm.commentLoader = false;
                    vm.$emit('commentAdded', true);
                    setTimeout(() => {
                        $("#ticket").modal('hide');
                    }, 2000)
                    return swal({
                        title: "Success",
                        text: "Forwarded successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.commentLoader = false;
                });
        },
        reject(data) {
            let vm = this;
            vm.rejectLoader = true;
            axios.post(this.api_url + "inventory/products/orders/reject", data)
                .then((response) => {

                    vm.fetchOrders();
                    vm.rejectLoader = false;

                    setTimeout(() => {
                        $("#ticket").modal('hide');
                    }, 2000);

                    return swal({
                        title: "Success",
                        text: "Order Rejected Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.rejectLoader = false;
                });
        },
        revert(data) {
            let vm = this;
            vm.revertLoader = true;
            axios.post(this.api_url + "inventory/products/orders/revert", data)
                .then((response) => {

                    vm.fetchOrders();
                    vm.revertLoader = false;

                    setTimeout(() => {
                        $("#ticket").modal('hide');
                    }, 2000);

                    return swal({
                        title: "Success",
                        text: "Order Revert Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.revertLoader = false;
                });
        },
        addComment(data) {
            let vm = this;
            vm.commentLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/comments", data)
                .then((response) => {

                    vm.fetchDetail(vm.details.id);
                    vm.commentLoader = false;
                    vm.$emit('commentAdded', true);
                    return swal({
                        title: "Success",
                        text: "Your Comment added successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.commentLoader = false;
                });
        },
        changeStatus(data) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/settings/shipping-classes/change-status", data)
                .then((response) => {
                    vm.fetchRecord();
                    vm.activeStatus = !vm.activeStatus;
                });
        },
        dataTable() {
            $("#moq_table").DataTable();
        },
        clearDataTable() {
            const table = $("#moq_table").DataTable();
            table.destroy();
        },
    },
    watch: {
        orders(newLedger) {
            this.clearDataTable();
            setTimeout(() => {
                $("#moq_table").DataTable({
                    dom: "Bfrtip",
                    buttons: ["copy", "csv", "excel"],
                });
            }, 300);
        },
    },
}
</script>
