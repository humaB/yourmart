<template>
    <div>
        <div class="card-body table-responsive" v-if="loader">
            <bullet-list-loader :width="250"> </bullet-list-loader>
        </div>

        <div class="row" v-else>
            <!-- Today's Data Cards -->
            <OrdersectionGrowthDashboard :todaysData="todaysData"/>
        </div>

        <hr class="border border-secondary border-2 opacity-50">

        <!-- Charts Section -->
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <h2>Orders</h2>
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="orders"
                    title="Orders"
                />
                
                <hr class="border border-secondary border-2 opacity-50">
                <h2>Sales</h2>
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="sales"
                    title="Sales"
                    :is-currency="true"
                />
           
                <hr class="border border-secondary border-2 opacity-50">
                <h2>Profit</h2>
                <GenericBarChart 
                    :graph-data="dashboardGraphs"
                    graph-type="profit"
                    title="Profit"
                    :is-currency="true"
                />
                
                <hr class="border border-secondary border-2 opacity-50">
                <h2>Registrations</h2>
                <DropshipperApprovedGraph :dropshipperGraphLast120Days="dropshipperGraphLast120Days"/>
            </div>
        </div>
    </div>
</template>

<script>
import DropshipperApprovedGraph from '../components/graphs/DropshipperApprovedGraph.vue';
import GenericBarChart from '../components/graphs/GenericBarChart.vue';
import OrdersectionGrowthDashboard from '../components/admin/dashboard/OrdersectionGrowthDashboard.vue';
import { BulletListLoader } from "vue-content-loader";

export default {
    name: 'DashboardPage',
    components: {
        OrdersectionGrowthDashboard,
        DropshipperApprovedGraph,
        GenericBarChart,
        BulletListLoader
    },
    data() {
        return {
            api_url: process.env.MIX_API_URL,
            loader: true,
            todaysData: {},
            dashboardGraphs: {},
            dropshipperGraphLast120Days: {},
        };
    },
    created() {
        this.fetchData({ from: null, to: null });
    },
    methods: {
        fetchData(data) {
            let vm = this;
            vm.loader = true;
            axios
                .post(this.api_url + "growthdashboard", data)
                .then((response) => {
                    const results = response.data.response;
                    
                    // Set the data
                    vm.todaysData = results.todaysData || {};
                    vm.dashboardGraphs = results.dashboardGraphs || {};
                    vm.dropshipperGraphLast120Days = results.dropshipperGraphLast120Days || {};
                    
                    vm.loader = false;
                })
                .catch(error => {
                    console.error('API Error:', error);
                    vm.loader = false;
                });
        }
    },
};
</script>