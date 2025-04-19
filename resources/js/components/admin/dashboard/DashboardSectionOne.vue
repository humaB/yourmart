<template>
    <div class="row">

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                <div class="card-content">
                                    <h5 class="font-15">Approved Dropshipper</h5>
                                    <h2 class="mb-3 font-18">{{ approvedDropshipper.current_count || 0}}</h2>
                                    <p class="mb-0"><span :class="approvedDropshipper.trend == 'increase' ? 'col-green' : 'col-orange'">{{ approvedDropshipper.percentage_change || 0}}</span> {{ approvedDropshipper.trend || '-'}}</p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                <div class="banner-img">
                                    <img src="assets/img/banner/1.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                <div class="card-content">
                                    <h5 class="font-15"> Active Sellers</h5>
                                    <h2 class="mb-3 font-18">{{ activeSeller.current_count || 0}}</h2>
                                    <p class="mb-0"><span :class="activeSeller.trend == 'increase' ? 'col-green' : 'col-orange'">{{ activeSeller.percentage_change || 0}}</span> {{ activeSeller.trend || '-'}}</p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                <div class="banner-img">
                                    <img src="assets/img/banner/2.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                <div class="card-content">
                                    <h5 class="font-15">Live Products</h5>
                                    <h2 class="mb-3 font-18">{{ liveProduct.count || 0 }}</h2>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                <div class="banner-img">
                                    <img src="assets/img/banner/3.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row ">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                <div class="card-content">
                                    <h5 class="font-15">Current Stock Value</h5>
                                    <h2 class="mb-3 font-18">{{ formatPrice(liveProduct.currentStockValue || 0) }}</h2>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                <div class="banner-img">
                                    <img src="assets/img/banner/4.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-bg">
                    <div class="chartjs-size-monitor"
                        style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                        <div class="chartjs-size-monitor-expand"
                            style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                        </div>
                        <div class="chartjs-size-monitor-shrink"
                            style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                        </div>
                    </div>
                    <div class="p-t-20 d-flex justify-content-between">
                        <div class="col">
                            <h6 class="mb-0">Order Processed</h6>
                            <span class="font-weight-bold mb-0 font-20">{{ orderProcessed.processed || 0}}</span>
                        </div>
                        <i class="fas fa-address-card card-icon col-orange font-30 p-r-30"></i>
                    </div>
                    <canvas id="cardChart1" height="92" width="350" style="display: block; height: 74px; width: 280px;"
                        class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-bg">
                    <div class="chartjs-size-monitor"
                        style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                        <div class="chartjs-size-monitor-expand"
                            style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                        </div>
                        <div class="chartjs-size-monitor-shrink"
                            style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                        </div>
                    </div>
                    <div class="p-t-20 d-flex justify-content-between">
                        <div class="col">
                            <h6 class="mb-0">Sales</h6>
                            <span class="font-weight-bold mb-0 font-20">{{ formatPrice( orderProcessed.totalGrossSale || 0 ) }}</span>
                        </div>
                        <i class="fas fa-diagnoses card-icon col-green font-30 p-r-30"></i>
                    </div>
                    <canvas id="cardChart2" height="92" width="350" style="display: block; height: 74px; width: 280px;"
                        class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-bg">
                    <div class="chartjs-size-monitor"
                        style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                        <div class="chartjs-size-monitor-expand"
                            style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                        </div>
                        <div class="chartjs-size-monitor-shrink"
                            style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                        </div>
                    </div>
                    <div class="p-t-20 d-flex justify-content-between">
                        <div class="col">
                            <h6 class="mb-0">Cost of Good solds</h6>
                            <span class="font-weight-bold mb-0 font-20">{{ formatPrice(orderProcessed.totalCost || 0)}}</span>
                        </div>
                        <i class="fas fa-chart-bar card-icon col-indigo font-30 p-r-30"></i>
                    </div>
                    <canvas id="cardChart3" height="92" width="350" style="display: block; height: 74px; width: 280px;"
                        class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-bg">
                    <div class="chartjs-size-monitor"
                        style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                        <div class="chartjs-size-monitor-expand"
                            style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                        </div>
                        <div class="chartjs-size-monitor-shrink"
                            style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                        </div>
                    </div>
                    <div class="p-t-20 d-flex justify-content-between">
                        <div class="col">
                            <h6 class="mb-0">Gross Profit</h6>
                            <span class="font-weight-bold mb-0 font-20">{{ formatPrice(orderProcessed.grossProfit || 0) }}</span>
                        </div>
                        <i class="fas fa-hand-holding-usd card-icon col-cyan font-30 p-r-30"></i>
                    </div>
                    <canvas id="cardChart4" height="92" width="350" style="display: block; height: 74px; width: 280px;"
                        class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>

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
                                            {{ orderProcessed.returnRatio }}%
                                        </h3>
                                        <span class="text-muted">Return Ratio</span>
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
                                            {{ formatPrice( orderProcessed.courier || 0 )}}
                                        </h3>
                                        <span class="text-muted">Courier Charges</span>
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
                                            {{ formatPrice(orderProcessed.packing || 0) }}
                                        </h3>
                                        <span class="text-muted">Packing Charges</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
            </table>
        </div>
        <div class="row px-4 col-md-12">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-purple">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20">
                            <div class="text-right">
                                <h3 class="font-light mb-0">
                                    <i class="ti-arrow-up text-success"></i> {{ formatPrice(levelsWidget.level1) }}
                                </h3>
                                <span class="text-muted">Level 01 Seller</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-green">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20">
                            <div class="text-right">
                                <h3 class="font-light mb-0">
                                    <i class="ti-arrow-up text-success"></i> {{ formatPrice(levelsWidget.level2) }}
                                </h3>
                                <span class="text-muted">Level 02 Seller</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-cyan">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20">
                            <div class="text-right">
                                <h3 class="font-light mb-0">
                                    <i class="ti-arrow-up text-success"></i> {{ formatPrice(levelsWidget.level3) }}
                                </h3>
                                <span class="text-muted">Level 03 Seller</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-orange">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20">
                            <div class="text-right">
                                <h3 class="font-light mb-0">
                                    <i class="ti-arrow-up text-success"></i> {{ levelsWidget.topRatedSeller }}
                                </h3>
                                <span class="text-muted">Top Rated Seller</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'DashboardSectionOne',
    props : ['orders', 'approvedDropshipper', 'activeSeller', 'liveProduct', 'orderProcessed', 'levelsWidget'],
    methods : {
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
    }
}
</script>
