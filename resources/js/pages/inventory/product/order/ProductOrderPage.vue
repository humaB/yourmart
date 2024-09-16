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
                                                        <td>{{ item.id }}</td>
                                                        <td>{{ item.user ? item.user.name : '' }}</td>
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

        <OrderDetailView
            :details="details"
            :loader="commentLoader"
            @addComment="addComment($event)"
            @forward="forward($event)"
        />
    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../../components/table/TableHeaderComponent.vue";
import OrderDetailView from "../../../../components/inventory/product/order/OrderDetailView.vue";

export default {
    name: 'ProductOrderPage',
    components: {
        TableHeader,
        BulletListLoader,
        OrderDetailView
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Pending Orders",
            },
            th: ["Sr #", "Order #", "Belongs To", "Added Date", "Action"],
            table_id: "moq_table",
            btnLoader: false,
            orders: [],
            loader: true,
            details: {},
            commentLoader : false
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
                    vm.orders = response.data.response

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
        forward( data ){
            let vm = this;
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
