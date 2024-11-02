<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">
                    <TableHeader :tableHeader="tableHeader" />

                    <div class="row px-4">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-purple">
                                    <i class="fa fa-hand-holding-usd"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h3 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(totalPayable) }}
                                            </h3>
                                            <span class="text-muted">Total Payouts</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-green">
                                    <i class="fa fa-thumbs-up"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h3 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(totalPaid) }}
                                            </h3>
                                            <span class="text-muted">Total Paid</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-cyan">
                                    <i class="fa fa-calculator"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h3 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(totalRemaining)
                                                }}
                                            </h3>
                                            <span class="text-muted">Total Remaining</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-orange">
                                    <i class="fa fa-clipboard-list"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h3 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ remainingDropshippers }}
                                            </h3>
                                            <span class="text-muted">Total Sellers</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                        <div class="col-md-12" v-else>
                                            <table class="table table-bordered" :id="table_id" ref="datatable">
                                                <thead>
                                                    <tr>
                                                        <th v-for="(item, index) in th" :key="item">{{ item }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item, index) in records" :key="item.id">
                                                        <td>{{ index + 1 }}</td>
                                                        <td>{{ item.full_name }}</td>
                                                        <td>{{ item.email }}</td>
                                                        <td>{{ item.whatsapp_number }}</td>
                                                        <td>{{ formatPrice(item.total_payable) }}</td>
                                                        <td>{{ formatPrice(item.total_paid) }}</td>
                                                        <td>{{ formatPrice(item.remaining_amount) }}</td>
                                                        <td width="20%">
                                                            <button class="btn btn-info" @click="fetchDetail(item.id)"
                                                                data-toggle="modal" data-target="#dropShipperDetail"
                                                                title="View Details"><i class="fa fa-eye"></i></button>
                                                            <button class="btn btn-dark" @click="printRequest(item.id)"
                                                                title="Print"><i class="fa fa-print"></i></button>
                                                            <button class="btn btn-primary"
                                                                @click="paymentDetail(item.id)" data-toggle="modal"
                                                                data-target="#dropShipperPayment" title="Payment"><i
                                                                    class="fas fa-credit-card"></i></button>
                                                            <button class="btn btn-primary"
                                                                @click="paymentHistory(item.id)" data-toggle="modal"
                                                                data-target="#dropshipperHistory" title="Payment"><i
                                                                        class="far fa-clock"></i></button>
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

        <DropshipperDetails :details="details" :loader="btnLoader" @decision="decision($event)"
         @updateDropshipperInformation="updateDropshipperInformation($event)"
        />

        <DropshipperPayment ref="dropshipperPayment"
            :orders="orders"
            :addData="addData"
            :loader="paymentLoader"
            :details="details"
            :accountCash="accountCash"
            :accountBanks="accountBanks"
            @add="addPayment"
            @fetchTracking="fetchTracking($event)"
            @fetchOrderDetails="fetchOrderDetails( $event )"
        />

        <DropshipperPaymentHistory
            :selectedDropshipper="selectedDropshipper"
            :history="paymentHistorys"
        />

        <TrackingDetailPopup
            :trackingDetails="trackingDetails"
        />

        <OrderDetailView
            :revertLoader="revertLoader"
            :rejectLoader="rejectLoader"
            :paidAmountLoader="paidAmountLoader"
            :details="orderDetails"
            :loader="commentLoader"
            :role="role"
            @addComment="addComment($event)"
            @forward="forward($event)"
            @reject="reject($event)"
            @revert="revert($event)"
            @fetchDropshipperDetails="fetchDropshipperDetails($event)"
            @updatePaidAmount="updatePaidAmount( $event )"
            @updatePackagingAmount="updatePackagingAmount( $event )"
            @markasReplacement="markasReplacement($event)"
        />

        <OrderMarkasReplacementConfirmation
            :orderID="orderID"
            :loader="markasReplacementLoader"
            @markasReplacementConfirmation="markasReplacementConfirmation($event)"
        />

        <!-- Summary PRINT -->
        <form method="POST" :action="public_url + '/requests/dropshippers/pdf'" target="_blank" ref="requestForm">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="id" :value="id">
        </form>
    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../components/table/TableHeaderComponent.vue";
import DropshipperDetails from "../../../components/admin/request/DropshipperDetails.vue";
import DropshipperPayment from "../../../components/admin/request/DropshipperPayment.vue";
import DropshipperPaymentHistory from "../../../components/admin/request/DropshipperPaymentHistory.vue";
import TrackingDetailPopup from "../../../components/inventory/product/order/TrackingDetailPopup.vue";
import OrderDetailView from "../../../components/inventory/product/order/OrderDetailView.vue";
import OrderMarkasReplacementConfirmation from "../../../components/inventory/product/order/OrderMarkasReplacementConfirmation.vue";

export default {
    name: 'DropShipperPayOutPage',
    components: {
        TableHeader,
        BulletListLoader,
        DropshipperDetails,
        DropshipperPayment,
        DropshipperPaymentHistory,
        TrackingDetailPopup,
        OrderDetailView,
        OrderMarkasReplacementConfirmation
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Dropshipper Pay outs",
            },
            th: ["Sr #", "Name", "Email", "Contact #", "Total Payable", "Total Paid", "Remaining Amount", "Action"],
            table_id: "moq_table",
            btnLoader: false,
            records: [],
            orders: [],
            accountBanks: [],
            accountCash: [],
            loader: true,
            details: {},
            totalPayable: 0,
            totalPaid: 0,
            totalRemaining: 0,
            remainingDropshippers: 0,
            id: '',
            filter: {
                status: '',
                from: '',
                to: '',
            },
            addData: {
                shop_id: { code: 0, label: "Select from the following" },
                type: null,
                from_account: { code: 0, label: "Select from the following" },
                amount: null,
                narration: null,
                id: '',
                attachment : null
            },
            paymentLoader: false,
            selectedDropshipper: '',
            paymentHistorys : [],
            trackingDetails : [],
            //Order
            orderDetails: {},
            commentLoader : false,
            rejectLoader : false,
            revertLoader : false,
            role : '',
            paidAmountLoader : false,
            orderID : '',
            markasReplacementLoader : false
        };
    },
    created() {
        this.csrf = $('meta[name=csrf-token]').attr('content');
        this.fetchRecord();
        this.addDataReset = JSON.parse(JSON.stringify(this.addData));
    },
    methods: {
        markasReplacement(data){
            this.orderID = data.id
        },
        markasReplacementConfirmation(){
            let vm = this;
            vm.markasReplacementLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/mark-as-replacement", { id : this.orderID })
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
        updateDropshipperInformation( data ){
                let vm = this;
                axios
                    .post(this.api_url + "dropshippers", data)
                    .then((response) => {
                        vm.fetchRecord();

                        return swal({
                            title: "Success",
                            text: 'Information updated successfully',
                            icon: "success",
                            timer: 3000,
                        });
                    }).catch((err) => {

                        return swal({
                            title: "Error",
                            text: err.response.data.response[0],
                            icon: "error",
                            timer: 3000,
                        });
                    });
        },
        fetchTracking(id){
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/orders/tracking", { id })
                .then((response) => {
                    vm.trackingDetails = response.data.response
                });
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        printRequest(id) {
            this.id = id;
            const form = this.$refs.requestForm;
            setTimeout(() => {
                form.submit();
            }, 500)
        },
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
        },
        fetchRecord() {
            let vm = this;

            vm.loader = false;
            axios
                .get(this.api_url + "dropshippers/pay-outs")
                .then((response) => {
                    const results = response.data.response

                    // Calculate request statistics
                    vm.totalPayable = results.total_payable;
                    vm.totalPaid = results.total_paid;
                    vm.totalRemaining = results.total_remaining;
                    vm.remainingDropshippers = results.remaining_dropshippers;

                    vm.records = results.dropshippers;
                });
        },
        fetchDetail(id, status) {
            let vm = this;
            vm.activeStatus = status;

            axios
                .post(this.api_url + "dropshippers/details", { id })
                .then((response) => {
                    vm.details = response.data.response[0]
                });
        },
        applyFilter() {
            this.clearDataTable();
            this.fetchRecord();
        },
        resetFilter() {
            let vm = this;
            vm.filter = {
                status: '',
                from: '',
                to: ''
            }

            this.clearDataTable();
            this.fetchRecord();
        },
        paymentDetail(id) {
            let vm = this;

            vm.selectedDropshipper = id;
            axios
                .post(this.api_url + "dropshippers/payments/data", { id: id })
                .then((response) => {
                    const results = response.data.response
                    vm.orders = results.orders
                    vm.accountBanks = results.banks
                    vm.accountCash = results.cash
                    vm.details = results.dropshipper
                });
        },
        paymentHistory( id ){
            let vm = this;
            vm.selectedDropshipper = id;
            axios
                .post(this.api_url + "dropshippers/payments/history", { id: id })
                .then((response) => {
                    const results = response.data.response
                    vm.paymentHistorys = results
                });
        },
        addPayment() {

            if (this.addData.type == null || this.addData.amount < 1 || this.addData.from_account == null) {
                return swal({
                    title: "Error",
                    text: 'Please fill all field',
                    icon: "error",
                    timer: 3000,
                });
            }


            const fd = new FormData()
            // Append each field from addData to the FormData object
            fd.append('id', this.selectedDropshipper);
            fd.append('type', this.addData.type);
            fd.append('from_account', this.addData.from_account); // Sending only the code (adjust as needed)
            fd.append('amount', this.addData.amount);
            fd.append('narration', this.addData.narration);

            // If the attachment is a file, append it as well
            if (this.addData.attachment instanceof File) {
                fd.append('attachment', this.addData.attachment);
            }

            this.addData.id = this.selectedDropshipper;
            this.paymentLoader = true;

            axios
                .post(this.api_url + "dropshippers/payments/add", fd)
                .then((response) => {

                    this.paymentDetail(this.addData.id);
                    this.addData = JSON.parse(JSON.stringify(this.addDataReset));

                    this.paymentLoader = false;

                    this.fetchRecord();

                    return swal({
                        title: "Success",
                        text: 'Saved',
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        dataTable() {
            $("#moq_table").DataTable();
        },
        clearDataTable() {
            const table = $("#moq_table").DataTable();
            table.destroy();
        },
        fetchOrderDetails(id) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/orders/details", { id })
                .then((response) => {
                    vm.orderDetails = response.data.response[0]
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
            },2000);

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
        revert( data ){
            let vm = this;
            vm.revertLoader = true;
            axios.post(this.api_url + "inventory/products/orders/revert", data)
            .then((response) => {

            vm.fetchOrders();
            vm.revertLoader = false;

            setTimeout( () => {
                $("#ticket").modal('hide');
            },2000);

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
        updatePaidAmount( data ){
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
        updatePackagingAmount( data ){
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
    },
    watch: {
        records(newLedger) {
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
