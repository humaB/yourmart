
<template>
    <div class="row">

        <div class="col-md-12">

            <table style="table-layout: fixed; width: 100%;">
                <tr>
                    <td style="width: 20%; padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-cyan">
                                <i class="fa fa-shopping-bag"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                           {{ todaysData.orders || 0 }}
                                        </h3>
                                        <span class="text-muted">Today's Orders
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 20%;padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-orange">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                            {{ todaysData.sales || 0 }}
                                        </h3>
                                        <span class="text-muted">Net Sales
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 25%;padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-purple">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                            {{ todaysData.profit || 0 }}
                                        </h3>
                                        <span class="text-muted">Est. Profit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 20%;padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-green">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                            <!-- {{ pendingRequests.pendingReceivable || 0 }} -->
                                        </h3>
                                        <span class="text-muted">Active Sellers</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 20%;padding : 10px">
                        <div class="card card-statistic-1">
                            <div class="card-icon l-bg-cyan">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">
                                            <i class="ti-arrow-up text-success"></i>
                                            {{ todaysData.todaysRegistrations || 0 }}
                                        </h3>
                                        <span class="text-muted">New Registrations
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
            </table>
            
        </div>
     

    </div>
 
</template>
<script>
    export default {
        name : 'OrdersectionGrowthDashboard',
        props : ['dropshipper', 'applications', 'orders', 'todaysData'],
        methods : {
            getApplicationPercentage(count, total) {
                if (count === 0) return 0; // Correct check to prevent division by zero
                const percentage = Math.round((count / total) * 100);
                return percentage;
            },
           
        }
    }
</script>
