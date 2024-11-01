<template>
    <div>
        <div class="row">

            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">
                    <TableHeader :tableHeader="tableHeader" />

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
            :view="'viewOnly'"
            :details="details" :loader="commentLoader" :role="role" @addComment="addComment($event)"
            @forward="forward($event)" @reject="reject($event)" @revert="revert($event)"
            @fetchDropshipperDetails="fetchDropshipperDetails($event)" @updatePaidAmount="updatePaidAmount($event)"
            @updatePackagingAmount="updatePackagingAmount($event)" @markasReplacement="markasReplacement($event)" />

        <DropshipperDetails :details="dropShipperDetails" />

        <TrackingDetailPopup :trackingDetails="trackingDetails" />

    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../../components/table/TableHeaderComponent.vue";
import OrderDetailView from "../../../../components/inventory/product/order/OrderDetailView.vue";
import DropshipperDetails from "../../../../components/admin/request/DropshipperDetails.vue";
import TrackingDetailPopup from "../../../../components/inventory/product/order/TrackingDetailPopup.vue";

export default {
    name: 'ProductOrderPage',
    components: {
        TableHeader,
        BulletListLoader,
        OrderDetailView,
        DropshipperDetails,
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
            role: '',
            dropShipperDetails: {},
            trackingDetails: [],
            orderID: '',
        };
    },
    created() {
        this.fetchOrders();
    },
    methods: {

        fetchTracking(id) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/orders/tracking", { id })
                .then((response) => {
                    vm.trackingDetails = response.data.response
                });
        },

        formatDate(date) {
            return date ? moment.utc(date).format('DD-MMM-YYYY') : 'N/A';
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        fetchOrders() {
            let vm = this;

            vm.loader = true;
            this.clearDataTable();

            axios
                .get(this.api_url + "inventory/products/orders/records")
                .then((response) => {
                    vm.orders = response.data.response
                    vm.loader = false;
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
