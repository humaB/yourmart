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
                                        <div class="col-md-12" v-else>
                                            <table class="table table-bordered" :id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th v-for="(item, index) in th" :key="item">{{ item }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item, index) in orders" :key="item.id">
                                                        <td>{{ index + 1 }}</td>
                                                        <td>
                                                            {{ item.shop ? `${item.shop.store_name.substring(0, 3)}-${item.order_no}` : item.order_no }}
                                                        </td>
                                                        <td>{{ item.user ? item.user.name : 'GUEST' }}</td>
                                                        <td>{{ formatDate(item.created_at) }}</td>
                                                        <td>
                                                            <span class="badge badge-warning text-dark" v-if="item.status == 0">Order Collection</span>
                                                            <span class="badge badge-info text-dark" v-else-if="item.status == 1">Inventory Issuance</span>
                                                            <span class="badge badge-secondary" v-else-if="item.status == 2">QC</span>
                                                            <span class="badge badge-success" v-else-if="item.status == 3">Packing/Dispatch</span>
                                                            <span class="badge badge-sucess" v-else-if="item.status == 4">Audit</span>
                                                            <span class="badge badge-sucess" v-else-if="item.status == 5">With Courier</span>
                                                            <span class="badge badge-danger" v-else-if="item.status == 6">Rejected</span>
                                                        </td>
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

        <OrderDetailView
            :rejectLoader="rejectLoader"
            :details="details"
            :loader="commentLoader"
            :role="role"
            @addComment="addComment($event)"
            @forward="forward($event)"
            @reject="reject($event)"
            @fetchDropshipperDetails="fetchDropshipperDetails($event)"
        />
        <DropshipperDetails
            :details="dropShipperDetails"
        />
    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../../components/table/TableHeaderComponent.vue";
import OrderDetailView from "../../../../components/inventory/product/order/OrderDetailView.vue";
import DropshipperDetails from "../../../../components/admin/request/DropshipperDetails.vue";

export default {
    name: 'ProductOrderPage',
    components: {
        TableHeader,
        BulletListLoader,
        OrderDetailView,
        DropshipperDetails
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Pending Orders",
            },
            th: ["Sr #", "Order #", "Belongs To", "Added Date", "Status","Action"],
            table_id: "moq_table",
            btnLoader: false,
            orders: [],
            loader: true,
            details: {},
            commentLoader : false,
            rejectLoader : false,
            role : '',
            dropShipperDetails : {}
        };
    },
    created() {
        this.fetchOrders();
    },
    methods: {
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
        },
        fetchOrders() {
            let vm = this;

            vm.loader = false;
            axios
                .get(this.api_url + "inventory/products/orders")
                .then((response) => {
                    vm.orders = response.data.response.orders
                    vm.role   = response.data.response.role

                    setTimeout(() => {
                        vm.dataTable();
                    }, 300);
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
        fetchDropshipperDetails( data ){

            let vm = this;

            axios
            .post(this.api_url + "dropshippers/details", { id : data.id })
            .then((response) => {
                    vm.dropShipperDetails = response.data.response[0]
             });

        },
        forward( data ){
            let vm = this;
            vm.commentLoader = true;
            axios.post(this.api_url + "inventory/products/orders/update-status", data)
            .then((response) => {

            vm.fetchOrders();
            vm.commentLoader = false;
            vm.$emit('commentAdded', true);
            setTimeout( () => {
                $("#ticket").modal('hide');
            },2000)
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
        reject( data ){
            let vm = this;
            vm.rejectLoader = true;
            axios.post(this.api_url + "inventory/products/orders/reject", data)
            .then((response) => {

            vm.fetchOrders();
            vm.rejectLoader = false;

            setTimeout( () => {
                $("#ticket").modal('hide');
            },2000)
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
    }
}
</script>
