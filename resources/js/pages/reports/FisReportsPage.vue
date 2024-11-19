<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">
                    <TableHeader :tableHeader="tableHeader" />
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 col-6">
                                <h6>
                                    1.
                                    <a href="#" @click="controlRegister()"><i class="fas fa-fax"></i> Inventory Control
                                        Register</a>
                                </h6>
                            </div>

                            <div class="col-md-4 col-6">
                                <h6>
                                    2.
                                    <a href="#" @click="goodReceived()"><i class="fas fa-fax"></i> Inventory Good
                                        Received</a>
                                </h6>
                            </div>

                            <div class="col-md-4 col-6">
                                <h6>
                                    3.
                                    <a href="#" @click="goodIssued()"><i class="fas fa-fax"></i> Inventory Good
                                        Issued</a>
                                </h6>
                            </div>

                            <div class="col-md-4 col-6">
                                <h6>
                                    4.
                                    <a href="#" @click="goodReturn()"><i class="fas fa-fax"></i> Inventory Good
                                        Returns</a>
                                </h6>
                            </div>

                            <div class="col-md-4 col-6">
                                <h6>
                                    4.
                                    <a href="#" @click="deliveredOrder()"><i class="fas fa-fax"></i> Delivered Order Detail</a>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <InventoryControlRegisterReport v-if="report == 'control-register-report'" :data="controlRegisterData"
            :loader="loader" @inventoryControlregisterReportFilter="inventoryControlregisterReportFilter($event)" />

        <InventoryGoodReceivedReport v-if="report == 'good-received-report'" :role="role" :products="products"
            :data="goodReceivedData" :loader="loader" @inventoryGoodReceivedilter="inventoryGoodReceivedilter($event)"
            @deleteGRN="deleteGRN($event)" />

        <InventoryGoodIssuanceReport v-if="report == 'good-issued-report'" :products="products" :data="goodIssuedData"
            :loader="loader" @inventoryGoodIssuedFilter="inventoryGoodIssuedFilter($event)" />

        <InventoryGoodReturnReport v-if="report == 'good-return-report'" :products="products" :data="goodReturnData"
            :loader="loader" @inventoryGoodReturnFilter="inventoryGoodReturnFilter($event)" />

        <DeliveredOrderDetailsReport v-if="report == 'delivered-order-report'" :data="deliveredOrderData"
            :loader="loader" @deliveredOrderFilter="deliveredOrderFilter($event)" />

        <!-- Modal -->
        <div class="modal fade" id="deleteGRN" tabindex="-1" role="dialog" aria-labelledby="deleteGRNTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <h5>Are you sure you want to delete this {{ grnDetails ? grnDetails.product.title : '' }}
                                from GRN # {{ grnDetails.id ? grnDetails.grn_id : '' }}?</h5>
                            <ul>
                                <li>This will have impact on average price of this product</li>
                                <li>This will have impact on total and remaining amount of PO</li>
                                <li>This will only be delete if PO amount is not and remaining amount is equal or
                                    greater than this product cost</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" @click="deleteGRNConfirmation()" v-if="!deleteLoader">Yes,
                            Proceed</button>
                            <button type="button" class="btn btn-danger btn-progress danger" v-else>Yes,
                                Proceed</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</template>
<script>
import DeliveredOrderDetailsReport from '../../components/reports/fis/DeliveredOrderDetailsReport.vue';
import InventoryControlRegisterReport from '../../components/reports/fis/InventoryControlRegisterReport.vue';
import InventoryGoodIssuanceReport from '../../components/reports/fis/InventoryGoodIssuanceReport.vue';
import InventoryGoodReceivedReport from '../../components/reports/fis/InventoryGoodReceivedReport.vue';
import InventoryGoodReturnReport from '../../components/reports/fis/InventoryGoodReturnReport.vue';

import TableHeader from '../../components/table/TableHeaderComponent.vue';

export default {
    name: 'FisReportsPage',
    components: {
        TableHeader,
        InventoryControlRegisterReport,
        InventoryGoodReceivedReport,
        InventoryGoodIssuanceReport,
        InventoryGoodReturnReport,
        DeliveredOrderDetailsReport
    },
    data() {
        return {
            api_url: process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            tableHeader: {
                heading: "FIS Reports",
            },
            products: [],
            report: '',
            controlRegisterData: [],
            goodReceivedData: [],
            goodIssuedData: [],
            goodReturnData: [],
            deliveredOrderData : [],
            loader: false,
            deleteLoader : false,
            role: null,
            grnDetails: {
                product: '',
                id: ''
            }
        }
    },
    created() {
        this.fetchProducts();
    },
    methods: {
        deliveredOrder() {
            this.report = 'delivered-order-report'
        },
        deliveredOrderFilter(data) {
            let vm = this;
            vm.loader = true;
            axios.post(vm.api_url + 'reports/fis/delivered-order-details', data)
                .then((res) => {
                    const results = res.data.response;
                    vm.deliveredOrderData = results;
                    vm.loader = false;
                })
        },
        deleteGRNConfirmation() {
            let vm = this;
            vm.deleteLoader = true;
            axios
                .post(this.api_url + "reports/fis/good-received/delete" , this.grnDetails )
                .then((response) => {
                    vm.deleteLoader = false;
                    $('#deleteGRN').modal('hide');
                    this.$emit('GRNdeleted', true)
                    return swal({
                        title: "Success",
                        text: 'Deleted Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                }).catch((err) => {
                    vm.deleteLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        deleteGRN(data) {
            this.grnDetails = data
        },
        fetchProducts() {
            let vm = this;
            axios
                .get(this.api_url + "inventory/products/complete-drop-down")
                .then((response) => {
                    vm.products = response.data.response;
                }).catch((err) => {
                    vm.fetchProducts();
                });
        },
        goodReturn() {
            this.report = 'good-return-report'
        },
        inventoryGoodReturnFilter(data) {
            let vm = this;
            vm.loader = true;
            axios.post(vm.api_url + 'reports/fis/good-returns', data)
                .then((res) => {
                    const results = res.data.response;
                    vm.goodReturnData = results;
                    vm.loader = false;
                })
        },
        goodIssued() {
            this.report = 'good-issued-report'
        },
        inventoryGoodIssuedFilter(data) {
            let vm = this;
            vm.loader = true;
            axios.post(vm.api_url + 'reports/fis/good-issued', data)
                .then((res) => {
                    const results = res.data.response;
                    vm.goodIssuedData = results;
                    vm.loader = false;
                })
        },
        goodReceived() {
            this.report = 'good-received-report'
        },
        inventoryGoodReceivedilter(data) {
            let vm = this;
            vm.loader = true;
            axios.post(vm.api_url + 'reports/fis/good-received', data)
                .then((res) => {
                    const results = res.data.response;
                    vm.goodReceivedData = results.goods;
                    vm.role = results.role;
                    vm.loader = false;
                })
        },
        controlRegister() {
            this.report = 'control-register-report'
        },
        inventoryControlregisterReportFilter(data) {
            let vm = this;
            vm.loader = true;
            axios.post(vm.api_url + 'reports/fis/inventory-control-register', data)
                .then((res) => {
                    const results = res.data.response;
                    vm.controlRegisterData = results;
                    vm.loader = false;
                })
        }
    }
}
</script>
