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
                                    <a href="#" @click="controlRegister()"
                                      ><i class="fas fa-fax"></i> Inventory Control Register</a
                                    >
                                  </h6>
                            </div>

                            <div class="col-md-4 col-6">
                                <h6>
                                    2.
                                    <a href="#" @click="goodReceived()"
                                      ><i class="fas fa-fax"></i> Inventory Good Received</a
                                    >
                                  </h6>
                            </div>

                            <div class="col-md-4 col-6">
                                <h6>
                                    3.
                                    <a href="#" @click="goodIssued()"
                                      ><i class="fas fa-fax"></i> Inventory Good Issued</a
                                    >
                                  </h6>
                            </div>

                            <div class="col-md-4 col-6">
                                <h6>
                                    4.
                                    <a href="#" @click="goodReturn()"
                                      ><i class="fas fa-fax"></i> Inventory Good Returns</a
                                    >
                                  </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <InventoryControlRegisterReport
            v-if="report == 'control-register-report'"
            :data="controlRegisterData"
            :loader="loader"
            @inventoryControlregisterReportFilter="inventoryControlregisterReportFilter($event)"
        />

        <InventoryGoodReceivedReport
            v-if="report == 'good-received-report'"
            :products="products"
            :data="goodReceivedData"
            :loader="loader"
            @inventoryGoodReceivedilter="inventoryGoodReceivedilter($event)"
        />

        <InventoryGoodIssuanceReport
            v-if="report == 'good-issued-report'"
            :products="products"
            :data="goodIssuedData"
            :loader="loader"
            @inventoryGoodIssuedFilter="inventoryGoodIssuedFilter($event)"
        />

        <InventoryGoodReturnReport
            v-if="report == 'good-return-report'"
            :products="products"
            :data="goodReturnData"
            :loader="loader"
            @inventoryGoodReturnFilter="inventoryGoodReturnFilter($event)"
        />

    </div>
</template>
<script>
import InventoryControlRegisterReport from '../../components/reports/fis/InventoryControlRegisterReport.vue';
import InventoryGoodIssuanceReport from '../../components/reports/fis/InventoryGoodIssuanceReport.vue';
import InventoryGoodReceivedReport from '../../components/reports/fis/InventoryGoodReceivedReport.vue';
import InventoryGoodReturnReport from '../../components/reports/fis/InventoryGoodReturnReport.vue';
import TableHeader from '../../components/table/TableHeaderComponent.vue';

export default {
    name: 'FisReportsPage',
    components : {
        TableHeader,
        InventoryControlRegisterReport,
        InventoryGoodReceivedReport,
        InventoryGoodIssuanceReport,
        InventoryGoodReturnReport
    },
    data(){
        return {
            api_url: process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            tableHeader: {
                heading: "FIS Reports",
            },
            products : [],
            report : '',
            controlRegisterData : [],
            goodReceivedData : [],
            goodIssuedData : [],
            goodReturnData : [],
            loader : false
        }
    },
    created(){
        this.fetchProducts();
    },
    methods : {
        fetchProducts(){
                let vm = this;
                axios
                    .get(this.api_url + "inventory/products/complete-drop-down")
                    .then((response) => {
                        vm.products = response.data.response;
                    }).catch((err) => {
                        vm.fetchProducts();
                    });
        },
        goodReturn(){
            this.report = 'good-return-report'
        },
        inventoryGoodReturnFilter(data){
            let vm = this;
            vm.loader = true;
            axios.post(vm.api_url+'reports/fis/good-returns', data)
            .then( (res) => {
                const results = res.data.response;
                vm.goodReturnData = results;
                vm.loader = false;
            })
        },
        goodIssued(){
            this.report = 'good-issued-report'
        },
        inventoryGoodIssuedFilter(data){
            let vm = this;
            vm.loader = true;
            axios.post(vm.api_url+'reports/fis/good-issued', data)
            .then( (res) => {
                const results = res.data.response;
                vm.goodIssuedData = results;
                vm.loader = false;
            })
        },
        goodReceived(){
            this.report = 'good-received-report'
        },
        inventoryGoodReceivedilter(data){
            let vm = this;
            vm.loader = true;
            axios.post(vm.api_url+'reports/fis/good-received', data)
            .then( (res) => {
                const results = res.data.response;
                vm.goodReceivedData = results;
                vm.loader = false;
            })
        },
        controlRegister(){
            this.report = 'control-register-report'
        },
        inventoryControlregisterReportFilter(data){
            let vm = this;
            vm.loader = true;
            axios.post(vm.api_url+'reports/fis/inventory-control-register', data)
            .then( (res) => {
                const results = res.data.response;
                vm.controlRegisterData = results;
                vm.loader = false;
            })
        }
    }
}
</script>
