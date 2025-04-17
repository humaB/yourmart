<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">
                    <TableHeader :tableHeader="tableHeader" />

                    <div class="row px-4">
                        <div class="col-md-12 mb-2 text-right">
                            <button class="btn btn-danger" @click="updateLevel()" v-if="!btnLoader">Run Corn Job</button>
                            <button class="btn btn-danger btn-progress disabled" v-else>Run Corn Job</button>

                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-purple">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h3 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ totalRequest }}
                                            </h3>
                                            <span class="text-muted">Total Request's</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-green">
                                    <i class="fas fa-spinner"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h3 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ pendingRequest }}
                                            </h3>
                                            <span class="text-muted">Pending</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-cyan">
                                    <i class="fas fa-thumbs-up"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h3 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ approvedRequest }}
                                            </h3>
                                            <span class="text-muted">Approved</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-orange">
                                    <i class="fas fa-thumbs-down"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h3 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ rejectedRequest }}
                                            </h3>
                                            <span class="text-muted">Rejected</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="applyFilter" class="row col-md-12">
                            <div class="col-md-3">
                                <label for="">Select Status</label>
                                <select v-model="filter.status" class="form-control">
                                    <option value="">Select from the following</option>
                                    <option value="0">Pending</option>
                                    <option value="1">Approved</option>
                                    <option value="2">Rejected</option>
                                    <option value="3">Deactivated</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="">Level</label>
                                <select v-model="filter.level" class="form-control">
                                    <option value="">Select from the following</option>
                                    <option>New Seller</option>
                                    <option>Level 01</option>
                                    <option>Level 02</option>
                                    <option>Level 03</option>
                                    <option>Top Rated Seller</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="">Incentive</label>
                                <select v-model="filter.incentive" class="form-control">
                                    <option value="">Select from the following</option>
                                    <option value="0">Pending</option>
                                    <option value="1">Given</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="">From</label>
                                <input type="date" v-model="filter.from" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="">To</label>
                                <input type="date" v-model="filter.to" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="">Action</label><br>
                                <button class="btn btn-primary mr-2 btn-block" @click="applyFilter">Filter</button>
                                <button class="btn btn-danger btn-block" @click="resetFilter">Reset</button>
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
                                                        <td>
                                                            <span v-if="item.status == 0"
                                                                class="badge badge-warning">Pending</span>
                                                            <span v-if="item.status == 1"
                                                                class="badge badge-success">Approved</span>
                                                            <span v-if="item.status == 2"
                                                                class="badge badge-danger">Rejected</span>
                                                            <span v-if="item.status == 3"
                                                                class="badge badge-danger">Deactivated</span>
                                                        </td>
                                                        <td>
                                                            {{ item.seller_level }}
                                                        </td>
                                                        <td>
                                                            {{ item?.level?.is_completed == '1' ? 'Completed' : 'Pending' }}
                                                        </td>
                                                        <td>{{ formatDate(item.created_at) }}</td>
                                                        <td width="20%">
                                                            <button class="btn btn-info" @click="fetchDetail(item.id)"
                                                                data-toggle="modal" data-target="#dropShipperDetail"
                                                                title="View Details"><i class="fa fa-eye"></i></button>
                                                            <button class="btn btn-dark" @click="printRequest(item.id)"
                                                                title="Print"><i class="fa fa-print"></i></button>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="card-footer text-right">
                                                <nav class="d-inline-block">
                                                  <ul class="pagination mb-0">
                                                    <li class="page-item" :class="{ disabled: page === 1 }">
                                                      <a class="page-link" href="#" @click.prevent="fetchRecord(page - 1)">
                                                        <i class="fas fa-chevron-left"></i>
                                                      </a>
                                                    </li>

                                                    <li class="page-item" v-for="n in pagesToShow" :key="n.key" :class="{ active: page === n.page, disabled: n.ellipsis }">
                                                      <a v-if="!n.ellipsis" class="page-link" href="#" @click.prevent="fetchRecord(n.page)">
                                                        {{ n.page }}
                                                      </a>
                                                      <span v-else class="page-link">...</span>
                                                    </li>

                                                    <li class="page-item" :class="{ disabled: page === pagination.last_page }">
                                                      <a class="page-link" href="#" @click.prevent="fetchRecord(page + 1)">
                                                        <i class="fas fa-chevron-right"></i>
                                                      </a>
                                                    </li>
                                                  </ul>
                                                </nav>
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

        <DropshipperDetails :details="details" :loader="btnLoader" @decision="decision($event)"
            @updateDropshipperInformation="updateDropshipperInformation($event)" />

        <DropshipperPayment ref="dropshipperPayment" :orders="orders" :addData="addData" :loader="paymentLoader"
            :accountCash="accountCash" :accountBanks="accountBanks" @add="addPayment" />

        <DropshipperPaymentHistory :history="paymentHistorys" :selectedDropshipper="selectedDropshipper" />

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

export default {
    name: 'DropShipperRequestPage',
    components: {
        TableHeader,
        BulletListLoader,
        DropshipperDetails,
        DropshipperPayment,
        DropshipperPaymentHistory
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Dropshipper Request's",
            },
            th: ["Sr #", "Name", "Email", "Contact #", "Total Payable", "Total Paid", "Remaining Amount", "Status","Level","Incentive", "Added Date", "Action"],
            table_id: "moq_table",
            guestQuantity: 0,
            registeredQuantity: 0,
            btnLoader: false,
            records: [],
            orders: [],
            accountBanks: [],
            accountCash: [],
            loader: true,
            details: {},
            activeStatus: '',
            editDetails: {},
            totalRequest: 0,
            pendingRequest: 0,
            approvedRequest: 0,
            rejectedRequest: 0,
            id: '',
            filter: {
                status: '',
                from: '',
                to: '',
                level : "",
                incentive : ""
            },
            addData: {
                shop_id: { code: 0, label: "Select from the following" },
                type: null,
                from_account: { code: 0, label: "Select from the following" },
                amount: null,
                narration: null,
                id: ''
            },
            paymentLoader: false,
            selectedDropshipper: '',
            paymentHistorys: [],
            pagination: {},
            page: 1,
        };
    },
    computed: {
        pagesToShow() {
        const pages = [];
        const total = this.pagination.last_page;
        const current = this.page;

        if (total <= 7) {
            for (let i = 1; i <= total; i++) {
            pages.push({ page: i, key: i });
            }
        } else {
            pages.push({ page: 1, key: 'start' });

            if (current > 4) {
            pages.push({ page: '...', key: 'start-dots', ellipsis: true });
            }

            const start = Math.max(2, current - 2);
            const end = Math.min(total - 1, current + 2);

            for (let i = start; i <= end; i++) {
            pages.push({ page: i, key: i });
            }

            if (current < total - 3) {
            pages.push({ page: '...', key: 'end-dots', ellipsis: true });
            }

            pages.push({ page: total, key: 'end' });
        }

        return pages;
        }
    },
    created() {
        this.csrf = $('meta[name=csrf-token]').attr('content');
        this.fetchRecord(1);
        this.addDataReset = JSON.parse(JSON.stringify(this.addData));
    },
    methods: {
        updateLevel(){
            let vm = this;
            vm.btnLoader = true;
            axios
                .post(this.api_url + "dropshippers/update-levels")
                .then((response) => {
                    vm.fetchRecord(vm.page);
                    vm.btnLoader = false;
                    return swal({
                        title: "Success",
                        text: 'Information updated successfully',
                        icon: "success",
                        timer: 3000,
                    });
                }).catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        updateDropshipperInformation(data) {
            let vm = this;
            axios
                .post(this.api_url + "dropshippers", data)
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
        decision(data) {
            let vm = this;

            vm.btnLoader = true;
            axios
                .post(this.api_url + "dropshippers/decisions", data)
                .then((response) => {
                    vm.fetchRecord(vm.page);
                    $(".modal").click();
                    this.btnLoader = false;
                    return swal({
                        title: "Success",
                        text: 'Decision Made Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                }).catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        fetchRecord( page = 1) {
            let vm = this;
            vm.page = page;

            let url = this.api_url + "dropshippers";

            axios
                .get(url, {
                    params: {
                        status: vm.filter.status,
                        from: vm.filter.from,
                        to: vm.filter.to,
                        page : page,
                        level : vm.filter.level,
                        incentive : vm.filter.incentive
                    },
                })
                .then((response) => {
                    vm.records = response.data.response.dropshippers.data;
                    vm.pagination = response.data.response.pagination;

                    // Calculate request statistics
                    // vm.totalRequest = vm.records.length;
                    // vm.pendingRequest = vm.records.filter(record => record.status === 0).length;
                    // vm.approvedRequest = vm.records.filter(record => record.status === 1).length;
                    // vm.rejectedRequest = vm.records.filter(record => record.status === 2).length;

                    vm.loader = false;
                });
        },
        paymentHistory(id) {
            let vm = this;
            vm.selectedDropshipper = id;
            axios
                .post(this.api_url + "dropshippers/payments/history", { id: id })
                .then((response) => {
                    const results = response.data.response
                    vm.paymentHistorys = results
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
                to: '',
                level : "",
                incentive : ""
            }

            this.clearDataTable();
            this.fetchRecord();
        },
        paymentDetail(id) {
            let vm = this;
            vm.activeStatus = status;

            vm.selectedDropshipper = id;
            axios
                .post(this.api_url + "dropshippers/payments/data", { id: id })
                .then((response) => {
                    const results = response.data.response
                    vm.orders = results.orders
                    vm.accountBanks = results.banks
                    vm.accountCash = results.cash
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

            this.paymentLoader = true;

            this.addData.id = this.selectedDropshipper;
            axios
                .post(this.api_url + "dropshippers/payments/add", this.addData)
                .then((response) => {
                    swal({
                        title: "Success",
                        text: 'Saved',
                        icon: "success",
                        timer: 3000,
                    });
                    this.$refs.dropshipperPayment.paymentShopPayments(this.addData.shop_id);
                    this.addData = JSON.parse(JSON.stringify(this.addDataReset));

                    this.paymentLoader = false;
                });
        },
        dataTable() {
            if ($.fn.DataTable.isDataTable("#moq_table")) {
                $('#moq_table').DataTable().destroy();
            }
            setTimeout(function () {
                $("#moq_table").DataTable({
                    "paging": false,
                    "pageLength": 20,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": false,
                    "autoWidth": false,
                });
            }, 300);
        },
        clearDataTable() {
            const table = $("#moq_table").DataTable();
            table.destroy();
        },
    },
    watch: {
        records: {
        deep: true, // if `record` is an object and you want to track nested changes
        handler(newVal, oldVal) {
            this.dataTable()
        }
        }
  },
}
</script>
