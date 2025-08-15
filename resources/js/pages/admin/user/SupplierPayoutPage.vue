<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">

                    <div class="card-body">
                        <div class="col-md-12">

                            <ul class="nav nav-pills mb-3" id="myTab3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="yourmart-tab3" data-toggle="tab" href="#yourmart3"
                                        role="tab" aria-controls="yourmart" aria-selected="true">Pending Payouts
                                        YourMart</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="supplier-tab3" data-toggle="tab" href="#supplier3" role="tab"
                                        aria-controls="supplier" aria-selected="false">Pending Payouts Suppliers</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="overall-tab3" data-toggle="tab" href="#overall" role="tab"
                                        aria-controls="overall" aria-selected="false">Over All Record</a>
                                </li>

                            </ul>
                        </div>
                        <div class="tab-content" id="myTabContent2">

                            <div class="tab-pane fade show active" id="yourmart3" role="tabpanel"
                                aria-labelledby="yourmart-tab3">
                                <div class="row">
                                    <div class="col-md-12 row px-4">
                                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                            <div class="card card-statistic-1">
                                                <div class="card-icon l-bg-purple">
                                                    <i class="fa fa-hand-holding-usd"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="padding-20">
                                                        <div class="text-right">
                                                            <h3 class="font-light mb-0">
                                                                <i class="ti-arrow-up text-success"></i> {{
                                                                    formatPrice(totalPayable) }}
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
                                                                <i class="ti-arrow-up text-success"></i> {{
                                                                    formatPrice(totalPaid)
                                                                }}
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
                                                                <i class="ti-arrow-up text-success"></i> {{
                                                                    formatPrice(totalRemaining)
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
                                                                <i class="ti-arrow-up text-success"></i> {{
                                                                    remainingDropshippers }}
                                                            </h3>
                                                            <span class="text-muted">Total Suppliers</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                                                    <th v-for="(item, index) in th" :key="item">{{ item
                                                                    }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(item, index) in records" :key="item.id">
                                                                    <td>{{ index + 1 }}</td>
                                                                    <td>{{ item.supplier.full_name }}</td>
                                                                    <td>{{ item.supplier.email }}</td>
                                                                    <td>{{ formatPrice(item.total_order_amount) }}</td>
                                                                    <td>{{ formatPrice(item.total_order_amount -
                                                                        item.total_remaining_amount) }}</td>
                                                                    <td>{{ formatPrice(item.total_remaining_amount) }}
                                                                    </td>

                                                                    <td class="d-flex justify-content-between">
                                                                        <button class="btn btn-primary mr-2"
                                                                            @click="paymentDetail(item.supplier.id, '0')"
                                                                            data-toggle="modal"
                                                                            data-target="#supplierPayment"
                                                                            title="Payment"><i
                                                                                class="fas fa-credit-card"></i></button>
                                                                        <button class="btn btn-info mr-2"
                                                                            @click="fetchDetail(item.id)"
                                                                            data-toggle="modal"
                                                                            data-target="#supplierDetail"
                                                                            title="View Details"><i
                                                                                class="fa fa-eye"></i></button>

                                                                        <button class="btn btn-primary"
                                                                            @click="paymentHistory(item.supplier.id)"
                                                                            data-toggle="modal"
                                                                            data-target="#dropshipperHistory"
                                                                            title="Payment"><i
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
                                </div>
                            </div>

                            <div class="tab-pane fade" id="supplier3" role="tabpanel" aria-labelledby="supplier-tab3">
                                <div class="row">
                                    <div class="col-md-12 row px-4">
                                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                            <div class="card card-statistic-1">
                                                <div class="card-icon l-bg-purple">
                                                    <i class="fa fa-hand-holding-usd"></i>
                                                </div>
                                                <div class="card-wrap">
                                                    <div class="padding-20">
                                                        <div class="text-right">
                                                            <h3 class="font-light mb-0">
                                                                <i class="ti-arrow-up text-success"></i> {{
                                                                    formatPrice(suppliertotalPayable) }}
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
                                                                <i class="ti-arrow-up text-success"></i> {{
                                                                    formatPrice(suppliertotalPaid)
                                                                }}
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
                                                                <i class="ti-arrow-up text-success"></i> {{
                                                                    formatPrice(suppliertotalRemaining)
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
                                                                <i class="ti-arrow-up text-success"></i> {{
                                                                    supplierremainingDropshippers }}
                                                            </h3>
                                                            <span class="text-muted">Total Suppliers</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Table -->
                                    <div class="col-md-12 mt-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="card-body table-responsive" v-if="loader">
                                                        <bullet-list-loader :width="250"> </bullet-list-loader>
                                                    </div>
                                                    <div class="col-md-12 table-responsive" v-else>
                                                        <table class="table table-bordered" id="moq_table2">
                                                            <thead>
                                                                <tr>
                                                                    <th v-for="(item, index) in th" :key="item">{{ item
                                                                    }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(item, index) in supplierrecords"
                                                                    :key="item.id">
                                                                    <td>{{ index + 1 }}</td>
                                                                    <td>{{ item.supplier.full_name }}</td>
                                                                    <td>{{ item.supplier.email }}</td>
                                                                    <td>{{ formatPrice(item.total_order_amount) }}</td>
                                                                    <td>{{ formatPrice(item.total_order_amount -
                                                                        item.total_remaining_amount) }}</td>
                                                                    <td>{{ formatPrice(item.total_remaining_amount) }}
                                                                    </td>

                                                                    <td class="d-flex justify-content-between">
                                                                        <button class="btn btn-primary mr-2"
                                                                            @click="paymentDetail(item.supplier.id, '1')"
                                                                            data-toggle="modal"
                                                                            data-target="#supplierPayment"
                                                                            title="Payment"><i
                                                                                class="fas fa-credit-card"></i></button>
                                                                        <button class="btn btn-info mr-2"
                                                                            @click="fetchDetail(item.id)"
                                                                            data-toggle="modal"
                                                                            data-target="#supplierDetail"
                                                                            title="View Details"><i
                                                                                class="fa fa-eye"></i></button>

                                                                        <button class="btn btn-primary"
                                                                            @click="paymentHistory(item.supplier.id)"
                                                                            data-toggle="modal"
                                                                            data-target="#dropshipperHistory"
                                                                            title="Payment"><i
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
                                </div>
                            </div>

                            <div class="tab-pane fade" id="overall" role="tabpanel" aria-labelledby="overall-tab3">
                                <div class="row">
                                    <div class="col-md-12 mt-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="">

                                                    <table class="table w-100 table-bordered" id="moq_table3">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr</th>
                                                                <th>Name</th>
                                                                <th>Email</th>
                                                                <th>Total Payable</th>
                                                                <th>Total Paid</th>
                                                                <th>Remaining Amount</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr v-for="(item, index) in overall"
                                                                    :key="item.id">
                                                                    <td>{{ index + 1 }}</td>
                                                                    <td>{{ item.supplier.full_name }}</td>
                                                                    <td>{{ item.supplier.email }}</td>
                                                                    <td>{{ formatPrice(item.total_order_amount) }}</td>
                                                                    <td>{{ formatPrice(item.total_order_amount -
                                                                        item.total_remaining_amount) }}</td>
                                                                    <td>{{ formatPrice(item.total_remaining_amount) }}
                                                                    </td>

                                                                    <td class="d-flex justify-content-between">
                                                                
                                                                        <button class="btn btn-info mr-2"
                                                                            @click="fetchDetail(item.id)"
                                                                            data-toggle="modal"
                                                                            data-target="#supplierDetail"
                                                                            title="View Details"><i
                                                                                class="fa fa-eye"></i></button>

                                                                        <button class="btn btn-primary"
                                                                            @click="paymentHistory(item.supplier.id)"
                                                                            data-toggle="modal"
                                                                            data-target="#dropshipperHistory"
                                                                            title="Payment"><i
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
                            </div>
                        </div>
                        <!-- END TABLE -->
                    </div>
                </div>
            </div>
        </div>

        <SupplierDetails :details="details" :loader="btnLoader" @decision="decision($event)"
            @updateInformation="updateInformation($event)" />

        <!-- Summary PRINT -->
        <form method="POST" :action="public_url + '/requests/suppliers/pdf'" target="_blank" ref="requestForm">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="id" :value="id">
        </form>

        <SupplierPayment :orders="orders" :addData="addData" :loader="btnLoader" :details="details"
            :accountCash="accountCash" :accountBanks="accountBanks" @add="addPayment" />

        <SupplierPaymentHistory :selectedSupplier="selectedSupplier" :history="paymentHistorys" />
    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../components/table/TableHeaderComponent.vue";
import SupplierDetails from "../../../components/admin/request/SupplierDetails.vue";
import SupplierPayment from "../../../components/admin/request/SupplierPayment.vue";
import SupplierPaymentHistory from "../../../components/admin/request/SupplierPaymentHistory.vue";

export default {
    name: 'SupplierRequestPage',
    components: {
        TableHeader,
        BulletListLoader,
        SupplierDetails,
        SupplierPayment,
        SupplierPaymentHistory
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Pending Payouts",
            },
            th: ["Sr #", "Name", "email", "Total Payable", "Total Paid", "Remaining Amount", "Action"],
            table_id: "moq_table",
            addData: {
                shop_id: { code: 0, label: "Select from the following" },
                type: null,
                from_account: { code: 0, label: "Select from the following" },
                amount: null,
                narration: null,
                id: '',
                attachment: null
            },
            addDataReset: {},
            orders: [],
            accountBanks: [],
            accountCash: [],
            btnLoader: false,
            records: [],
            loader: true,
            details: {},
            editDetails: {},
            id: '',
            totalPayable: 0,
            totalPaid: 0,
            totalRemaining: 0,
            remainingDropshippers: 0,
            selectedSupplier: { id: "", type: "" },
            paymentHistorys: [],

            suppliertotalPayable: 0,
            suppliertotalPaid: 0,
            suppliertotalRemaining: 0,
            supplierremainingDropshippers: 0,
            supplierrecords: [],
            overall : []
        };
    },
    created() {
        this.csrf = $('meta[name=csrf-token]').attr('content');
        this.fetchSupplierRecord();
        this.fetchYourmartRecord();
        this.fetchOverallRecord();
        this.addDataReset = JSON.parse(JSON.stringify(this.addData));
    },
    methods: {
        paymentHistory(id) {
            let vm = this;
            vm.selectedSupplier.id = id;
            axios
                .post(this.api_url + "suppliers/payments/history", { id: id })
                .then((response) => {
                    const results = response.data.response
                    vm.paymentHistorys = results
                });
        },
        updateInformation(data) {
            let vm = this;
            this.clearDataTable();
            axios
                .post(this.api_url + "suppliers", data)
                .then((response) => {
                    vm.fetchRecord(vm.page);

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
        addPayment() {

            if (this.addData.type == null || this.addData.from_account == null) {
                return swal({
                    title: "Error",
                    text: 'Please fill all field',
                    icon: "error",
                    timer: 3000,
                });
            }

            const fd = new FormData()
            // Append each field from addData to the FormData object
            fd.append('id', this.selectedSupplier.id);
            fd.append('inventoryType', this.selectedSupplier.type);

            fd.append('type', this.addData.type);
            fd.append('from_account', this.addData.from_account); // Sending only the code (adjust as needed)
            fd.append('amount', this.addData.amount);
            fd.append('narration', this.addData.narration);

            // If the attachment is a file, append it as well
            if (this.addData.attachment instanceof File) {
                fd.append('attachment', this.addData.attachment);
            }

            this.addData.id = this.selectedSupplier.id;
            this.btnLoader = true;

            axios
                .post(this.api_url + "suppliers/payments/add", fd)
                .then((response) => {

                    this.paymentDetail(this.addData.id);
                    this.addData = JSON.parse(JSON.stringify(this.addDataReset));

                    this.btnLoader = false;

                    this.fetchRecord();

                    return swal({
                        title: "Success",
                        text: 'Saved',
                        icon: "success",
                        timer: 3000,
                    });
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
        paymentDetail(id, type) {
            let vm = this;

            vm.selectedSupplier.id = id;
            vm.selectedSupplier.type = type;

            axios
                .post(this.api_url + "suppliers/payments/data", { id: id, type: type })
                .then((response) => {
                    const results = response.data.response
                    vm.orders = results.orders;
                    vm.accountBanks = results.banks;
                    vm.accountCash = results.cash;
                    vm.details = results.supplier;
                });
        },
        fetchSupplierRecord() {
            let vm = this;

            vm.loader = false;
            axios
                .get(this.api_url + "suppliers/payments/suppliers")
                .then((response) => {
                    const results = response.data.response

                    vm.suppliertotalPayable = results.total_payable;
                    vm.suppliertotalPaid = results.total_paid;
                    vm.suppliertotalRemaining = results.total_remaining;
                    vm.supplierremainingDropshippers = results.remaining_dropshippers;
                    vm.supplierrecords = results.suppliers;

                    setTimeout(() => {
                        vm.dataTable();
                    }, 300);
                });
        },
        fetchYourmartRecord() {
            let vm = this;

            vm.loader = false;
            axios
                .get(this.api_url + "suppliers/payments/yourmart")
                .then((response) => {
                    const results = response.data.response

                    vm.totalPayable = results.total_payable;
                    vm.totalPaid = results.total_paid;
                    vm.totalRemaining = results.total_remaining;
                    vm.remainingDropshippers = results.remaining_dropshippers;
                    vm.records = results.suppliers;

                    setTimeout(() => {
                        vm.dataTable2();
                    }, 300);
                });
        },
        fetchOverallRecord() {
            let vm = this;

            vm.loader = false;
            axios
                .get(this.api_url + "suppliers/payments/overalls")
                .then((response) => {
                    const results = response.data.response

                    vm.overall = results;

                    setTimeout(() => {
                        vm.dataTable3();
                    }, 300);
                });
        },
        fetchDetail(id, status) {
            let vm = this;
            vm.activeStatus = status;

            axios
                .post(this.api_url + "suppliers/details", { id })
                .then((response) => {
                    vm.details = response.data.response[0]
                });
        },
        dataTable() {
            $("#moq_table").DataTable({
                "bSort": false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pending Payouts',
                    }
                ]
            });
        },
        dataTable2() {
            $("#moq_table2").DataTable({
                "bSort": false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pending Payouts',
                    }
                ]
            });
        },
        dataTable3() {
            $("#moq_table3").DataTable({
                "bSort": false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Overall Payouts',
                    }
                ]
            });
        },
        clearDataTable() {
            const table = $("#moq_table").DataTable();
            table.destroy();
        },
    }
}
</script>
