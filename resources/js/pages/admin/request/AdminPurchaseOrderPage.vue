<template>
    <div>

        <!-- Modal -->
        <div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="confirmation"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <h5>Are you sure you want to {{ action.decision }} this PO?</h5>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="confimation()" v-if="!decisionLoader">Yes,
                            {{ action.decision }}</button>
                        <button type="button" class="btn btn-primary btn-progress disabled" v-else>Yes, {{
                            action.decision }}</button>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 card">
                <div class="card-header">
                    <h5>Insight's</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-12">
                        <table style="table-layout: fixed; width: 100%;">
                            <tr>
                                <td style="padding : 5px">
                                    <div class="card">
                                        <div class="card-body card-type-3">
                                            <div class="row">
                                                <div class="col">
                                                    <h6 class="text-muted mb-0">Total POs</h6>
                                                    <span class="font-weight-bold mb-0">{{ totalPo }}</span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </td>
                                <td style="padding : 5px">
                                    <div class="card">
                                        <div class="card-body card-type-3">
                                            <div class="row">
                                                <div class="col">
                                                    <h6 class="text-muted mb-0">Approved</h6>
                                                    <span class="font-weight-bold mb-0">{{ approved }}</span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </td>
                                <td style="padding : 5px">
                                    <div class="card">
                                        <div class="card-body card-type-3">
                                            <div class="row">
                                                <div class="col">
                                                    <h6 class="text-muted mb-0">Pending</h6>
                                                    <span class="font-weight-bold mb-0">{{ pending }}</span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </td>
                                <td style="padding : 5px">
                                    <div class="card">
                                        <div class="card-body card-type-3">
                                            <div class="row">
                                                <div class="col">
                                                    <h6 class="text-muted mb-0">Total Amount</h6>
                                                    <span class="font-weight-bold mb-0">{{ formatPrice(totalAmount)
                                                        }}</span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </td>
                                <td style="padding : 10px">
                                    <div class="card">
                                        <div class="card-body card-type-3">
                                            <div class="row">
                                                <div class="col">
                                                    <h6 class="text-muted mb-0">Payment Made</h6>
                                                    <span class="font-weight-bold mb-0">{{ formatPrice(paid) }}</span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </td>
                                <td style="padding : 5px">
                                    <div class="card">
                                        <div class="card-body card-type-3">
                                            <div class="row">
                                                <div class="col">
                                                    <h6 class="text-muted mb-0">Remaining</h6>
                                                    <span class="font-weight-bold mb-0">{{ formatPrice(remaining)
                                                        }}</span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </td>

                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">
                    <TableHeader :tableHeader="tableHeader" />

                    <div class="card-body">
                        <!-- Table -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-bordered" id="table">
                                            <thead>
                                                <tr>
                                                    <th>Sr #</th>
                                                    <th>PO #</th>
                                                    <th>Supplier</th>
                                                    <th>Inventory Type</th>
                                                    <th>Total Amount</th>
                                                    <th>Remaining Amount</th>
                                                    <th>Created Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in purchaseOrders" :key="item.id">
                                                    <td>{{ index + 1 }}</td>
                                                    <td>{{ item.id }}</td>
                                                    <td>{{ item.supplier.full_name }}</td>
                                                    <td>{{ item.supplier_stock == '1' ? 'Supplier' : 'YourMart' }}</td>

                                                    <td>{{ formatPrice(item.total_amount) }}</td>
                                                    <td>{{ formatPrice(item.remaining_amount) }}</td>
                                                    <td>{{ formatDate(item.created_at) }}</td>
                                                    <td>
                                                        <span class="badge badge-warning text-dark"
                                                            v-if="item.status == 0">Pending</span>
                                                        <span class="badge badge-success"
                                                            v-if="item.status == 1">Approved</span>
                                                        <span class="badge badge-danger"
                                                            v-if="item.status == 2">Rejected</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-success" data-toggle="modal"
                                                            data-target="#confirmation"
                                                            @click="decision(item.id, 'Approve')"
                                                            v-if="item.status == 0 && role == 'admin'">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-danger" data-toggle="modal"
                                                            data-target="#confirmation"
                                                            @click="decision(item.id, 'Reject')"
                                                            v-if="item.status == 0 && role == 'admin'">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-info" @click="fetchPOAttachments(item.id)" data-toggle="modal"
                                                            data-target="#addPOAttachment"><i
                                                                class="fa fa-upload"></i></button>
                                                        <button class="btn btn-dark"
                                                            @click="printPurchaseOrder(item.id)"><i
                                                                class="fa fa-print"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- END TABLE -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CreateNewPurchaseOrder :products="products" :suppliers="suppliers" :loader="btnLoader" @addPO="addPO($event)"
            @searchProduct="searchProduct($event)" />

        <AddPOAttachment :btnLoading="decisionLoader" :addData="attachmentData" :attachments="attachments"
            @uploadAttachment="uploadAttachment($event)" />

        <!-- Summary PRINT -->
        <form method="POST" :action="public_url + 'inventory/products/purchase-orders/pdf'" target="_blank"
            ref="summaryForm">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="id" :value="pid">
        </form>

    </div>
</template>
<script>
import TableHeader from "../../../components/table/TableHeaderComponent.vue";
import CreateNewPurchaseOrder from "../../../components/inventory/purchase_order/CreateNewPurchaseOrder.vue";
import AddPOAttachment from "../../../components/inventory/purchase_order/AddPOAttachment.vue";

import moment from "moment";
export default {
    name: 'AdminPurchaseOrderPage',
    components: {
        TableHeader,
        CreateNewPurchaseOrder,
        AddPOAttachment
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Purchase Order",
                link: "#",
                target: "#createNewPurchaseOrder"
            },
            attachmentData: {
                file: '',
                attachment: '',
            },
            attachments: [],
            products: [],
            suppliers: [],
            btnLoader: false,
            purchaseOrders: [],
            csrf: '',
            pid: '',
            action: {
                id: '',
                decision: ''
            },
            decisionLoader: false,
            role: '',
            totalPo: 0,
            approved: 0,
            pending: 0,
            totalAmount: 0,
            paid: 0,
            remaining: 0,
            po : '',
            addDataReset : {}
        };
    },
    created() {
        // CSRF token value assigning
        this.csrf = $('meta[name=csrf-token]').attr('content');
        this.fetchPurchaseOrders();
        this.fetchSuppilers();
        // this.fetchPurchaseOrderStats();
        this.addDataReset = {...this.attachmentData}
    },
    methods: {
        fetchPOAttachments(po) {
            this.po = po;
            axios
                .post(this.api_url + "inventory/products/purchase-orders/attachments", { po })
                .then((response) => {
                    const results = response.data.response;
                    this.attachments = results;
                })
        },
        uploadAttachment() {
            if (!this.attachmentData.file) {
                return this.$swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please add file name first, thanks',
                });
            }
            if (!this.attachmentData.attachment) {
                return this.$swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please upload attachment first, thanks',
                });
            }

            const fd = new FormData();
            fd.append('file', this.attachmentData.file);
            fd.append('attachment', this.attachmentData.attachment);
            fd.append('po', this.po);

            this.decisionLoader = true
            axios
                .post(this.api_url + "inventory/products/purchase-orders/attachments/upload", fd)
                .then((response) => {
                    this.fetchPOAttachments(this.po);
                    this.decisionLoader = false;
                    this.attachmentData = { ...this.addDataReset };

                    return swal({
                        title: "Success",
                        text: 'Attachment upload successfully',
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        printPurchaseOrder(id) {
            this.pid = id;
            const form = this.$refs.summaryForm;
            setTimeout(() => {
                form.submit();
            }, 500)
        },
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
        },
        searchProduct(data) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/drop-down", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.products = results;
                });
        },
        fetchSuppilers() {
            let vm = this;
            axios
                .get(this.api_url + "suppliers/drop-down")
                .then((response) => {
                    const results = response.data.response;
                    vm.suppliers = results;
                })
                .catch((err) => this.fetchSuppilers());
        },
        fetchPurchaseOrders() {
            let vm = this;
            axios
                .get(this.api_url + "inventory/products/purchase-orders")
                .then((response) => {
                    const results = response.data.response;
                    vm.purchaseOrders = results.purchase_orders;
                    vm.role = results.role;
                    vm.totalPo = results.totalPo;
                    vm.approved = results.approved;
                    vm.pending = results.pending;
                    vm.totalAmount = results.totalAmount;
                    vm.remaining = results.remaining;
                    vm.paid = vm.totalAmount - vm.remaining;

                    setTimeout(() => {
                        vm.dataTable()
                    }, 300)
                })
                .catch((err) => this.fetchPurchaseOrders());
        },
        fetchPurchaseOrderStats() {
            let vm = this;
            axios
                .get(this.api_url + "inventory/products/purchase-orders/status-counts")
                .then((response) => {
                    const results = response.data.response[0];

                    vm.role = results.role;
                    vm.totalPo = results.totalPo;
                    vm.approved = results.approved;
                    vm.pending = results.pending;
                    vm.totalAmount = results.totalAmount;
                    vm.remaining = results.remaining;
                    vm.paid = vm.totalAmount - vm.remaining;
                })
                .catch((err) => this.fetchPurchaseOrderStats());
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        dataTable() {
            $('#table').DataTable({
                dom: "Bfrtip",
                buttons: [{
                    extend: "excel",
                    title: 'Returns Details'
                },
                ],
            })
        },
        addPO(data) {
            let vm = this;
            vm.btnLoader = true;
            axios
                .post(this.api_url + "inventory/products/purchase-orders", data)
                .then((response) => {
                    vm.btnLoader = false;
                    vm.$emit('created', true);
                    vm.fetchPurchaseOrders();
                    return swal({
                        title: "Success",
                        text: 'Request sent for approval',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        decision(id, decision) {
            this.action = {
                id: id,
                decision: decision
            }
        },
        confimation() {

            this.decisionLoader = true;
            axios.post(this.api_url + "inventory/products/purchase-orders/decisions", this.action)
                .then((response) => {
                    this.decisionLoader = false;

                    this.fetchPurchaseOrders();
                    $("#confirmation").modal('hide');

                    return swal({
                        title: "Success",
                        text: 'Decision Made Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    this.decisionLoader = false;
                });
        }
    }
}
</script>
