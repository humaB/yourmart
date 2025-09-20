<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row col-md-12 mb-5">
                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="supplier-tab3" data-toggle="tab" href="#supplier3" role="tab"
                                        aria-controls="supplier" aria-selected="true">Supplier Inventory</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="yourmart-tab3" data-toggle="tab" href="#yourmart3" role="tab"
                                        aria-controls="yourmart" aria-selected="false">YourMart Inventory</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="overall-tab3" data-toggle="tab" href="#overall3" role="tab"
                                        aria-controls="overall" aria-selected="false">Over all</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" id="myTabContent2">

                            <div class="tab-pane fade show active" id="supplier3" role="tabpanel"
                                aria-labelledby="supplier-tab3">
                                <div class="col-12 col-md-12 col-lg-12">
                                    <SupplierInventoryTab :supplierStats="supplierStats" :suppliers="suppliers" :filter='filter'
                                        @fetchPurchaseOrders="fetchPurchaseOrders($event)"
                                        @submitFunction="submitFunction($event)"
                                    />
                                </div>
                            </div>
                            <div class="tab-pane fade" id="yourmart3" role="tabpanel"
                                aria-labelledby="yourmart-tab3">
                                <div class="col-12 col-md-12 col-lg-12">
                                     <YourmartInventoryTab :yourmartStats="yourmartStats" :suppliers="suppliers" :filter='filter'
                                        @fetchPurchaseOrders="fetchPurchaseOrders($event)"
                                        @submitFunction="submitFunction($event)"
                                    />
                                </div>
                            </div>
                            <div class="tab-pane fade" id="overall3" role="tabpanel"
                                aria-labelledby="overall-tab3">
                                <div class="col-12 col-md-12 col-lg-12">
                                     <OverallInventoryTab :overallStats="overallStats" :suppliers="suppliers" :filter='filter'
                                        @fetchPurchaseOrders="fetchPurchaseOrders($event)"
                                        @submitFunction="submitFunction($event)"
                                    />
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <PurchaseOrderDetails :purchaseOrders="purchaseOrders" />
    </div>
</template>
<script>
import OverallInventoryTab from '../../../components/supplier/OverallInventoryTab.vue';
import PurchaseOrderDetails from '../../../components/supplier/PurchaseOrderDetails.vue';
import SupplierInventoryTab from '../../../components/supplier/SupplierInventoryTab.vue';
import YourmartInventoryTab from '../../../components/supplier/YourmartInventoryTab.vue';

export default {
    name: "SupplierDashboardPage",
    components: {
        PurchaseOrderDetails,
        SupplierInventoryTab,
        YourmartInventoryTab,
        OverallInventoryTab
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + "/",
            filter: {
                supplier: { code: "0", label: "Select from the following" },
                from: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),

            },
            supplierStats : {
                totalStockValue: 0,
                totalSoldOutValue: 0,
                totalPaid: 0,
                balance: 0,
                products: [],
            },
            yourmartStats : {
                totalStockValue: 0,
                totalSoldOutValue: 0,
                totalPaid: 0,
                balance: 0,
                products: [],
            },
            overallStats : {
                totalStockValue: 0,
                totalSoldOutValue: 0,
                totalPaid: 0,
                balance: 0,
                products: [],
            },
            purchaseOrders: [],
            suppliers: []
        }
    },
    created() {
        this.fetchData(this.filter);
        this.fetchSupplier();
        this.fetchProducts(this.filter);
    },
    methods: {
        submitFunction() {
            this.fetchProducts(this.filter);
            this.fetchData(this.filter);
        },
        fetchPurchaseOrders(data) {
            axios
                .post(this.api_url + "suppliers/purchase-orders", data)
                .then((response) => {
                    const result = response.data.response;
                    this.purchaseOrders = result;
                })
                .catch((err) => {

                });
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        fetchSupplier() {
            axios
                .get(this.api_url + "suppliers/drop-down")
                .then((response) => {
                    const result = response.data.response;

                    this.suppliers = result;
                })
                .catch((err) => {

                });
        },
        fetchData(filter) {
            axios
                .post(this.api_url + "suppliers/dashboard", filter)
                .then((response) => {
                    const result = response.data.response;

                    this.supplierStats.totalStockValue = result.supplierReceived;
                    this.supplierStats.totalSoldOutValue = result.supplierIssued;
                    this.supplierStats.totalPaid = result.supplierPaid;
                    this.supplierStats.balance = result.supplierBalance;

                    this.yourmartStats.totalStockValue = result.yourmartReceived;
                    this.yourmartStats.totalSoldOutValue = result.yourmartIssued;
                    this.yourmartStats.totalPaid = result.yourmartPaid;
                    this.yourmartStats.balance = result.yourmartBalance;

                    this.overallStats.totalStockValue = result.overallReceived;
                    this.overallStats.totalSoldOutValue = result.overallIssued;
                    this.overallStats.totalPaid = result.overallPaid;
                    this.overallStats.balance = result.overallBalance;
                })
                .catch((err) => {

                });
        },
        fetchProducts(filter) {
            if ($.fn.DataTable.isDataTable("#dataTable")) {
                $('#dataTable').DataTable().destroy();
            }
            axios
                .post(this.api_url + "suppliers/products", filter)
                .then((response) => {
                    const result = response.data.response;
                    this.supplierStats.products = result.supplier;
                    this.yourmartStats.products = result.yourmart;
                    this.overallStats.products = result.overall;

                    setTimeout(function () {
                        $("#dataTable").DataTable({
                            "bSort": false,
                            dom: 'Bfrtip',
                            buttons: ['excel']
                        });
                    }, 300);

                    setTimeout(function () {
                        $("#dataTable2").DataTable({
                            "bSort": false,
                            dom: 'Bfrtip',
                            buttons: ['excel']
                        });
                    }, 300);

                    setTimeout(function () {
                        $("#dataTable3").DataTable({
                            "bSort": false,
                            dom: 'Bfrtip',
                            buttons: ['excel']
                        });
                    }, 300);
                })
                .catch((err) => {

                });
        },
    }
}
</script>
