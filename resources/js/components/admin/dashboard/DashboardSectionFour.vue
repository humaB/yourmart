<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="pl-4 pt-4">Purchase Orders</h4>
                <div class="card-body">
                    <div class="row" style="margin-left: -10px">
                        <!-- cards -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="card bg-info">
                                <div class="card-statistic-4 text-white">
                                    <div class="align-items-center justify-content-between">
                                        <div class="row">
                                            <div class="col-lg-8 col-md-6 col-sm-6 col-xs-6 pr-0">
                                                <div class="card-content">
                                                    <h5 class="font-15">Payable Amount</h5>
                                                    <h2 class="mb-3 font-18">
                                                        {{ formatPrice( inventoryStatus?.purchaseOrders?.totalAmount) }}
                                                    </h2>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 pl-0">
                                                <div class="banner-img">
                                                    <img :src="public_url + '/assets2/img/banner/2.png'" alt="" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="card bg-success">
                                <div class="card-statistic-4">
                                    <div class="align-items-center justify-content-between">
                                        <div class="row">
                                            <div class="col-lg-8 col-md-6 col-sm-6 col-xs-6 pr-0">
                                                <div class="card-content text-white">
                                                    <h5 class="font-15">Paid Amount</h5>
                                                    <h2 class="mb-3 font-18">
                                                        {{ formatPrice( inventoryStatus?.purchaseOrders?.paid) }}
                                                    </h2>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 pl-0">
                                                <div class="banner-img">
                                                    <img :src="public_url + '/assets2/img/banner/4.png'" alt="" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="card bg-warning">
                                <div class="card-statistic-4">
                                    <div class="align-items-center justify-content-between">
                                        <div class="row">
                                            <div class="col-lg-8 col-md-6 col-sm-6 col-xs-6 pr-0">
                                                <div class="card-content">
                                                    <h5 class="font-15">
                                                        Remaining Payable's
                                                    </h5>
                                                    <h2 class="mb-3 font-18">
                                                        {{ formatPrice( inventoryStatus?.purchaseOrders?.remaining) }}
                                                    </h2>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 pl-0">
                                                <div class="banner-img">
                                                    <img :src="public_url + '/assets2/img/banner/1.png'" alt="" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table style="table-layout: fixed; width: 100%;">
                            <tr>
                                <td style="padding : 10px">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon l-bg-cyan">
                                            <i class="fa fa-check-circle"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="padding-20">
                                                <div class="text-right">
                                                    <h3 class="font-light mb-0">
                                                        <i class="ti-arrow-up text-success"></i>
                                                        {{ formatPrice( inventoryStatus?.purchaseOrders?.totalPo) }}
                                                    </h3>
                                                    <span class="text-muted">Total Purchase Orders</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding : 10px">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon l-bg-orange">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="padding-20">
                                                <div class="text-right">
                                                    <h3 class="font-light mb-0">
                                                        <i class="ti-arrow-up text-success"></i>
                                                        {{ formatPrice( inventoryStatus?.purchaseOrders?.pending) }}
                                                    </h3>
                                                    <span class="text-muted">Pending</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding : 10px">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon l-bg-purple">
                                            <i class="fa fa-thumbs-up"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="padding-20">
                                                <div class="text-right">
                                                    <h3 class="font-light mb-0">
                                                        <i class="ti-arrow-up text-success"></i>
                                                        {{ formatPrice( inventoryStatus?.purchaseOrders?.approved) }}
                                                    </h3>
                                                    <span class="text-muted">Approved</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding : 10px">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon l-bg-green">
                                            <i class="fa fa-thumbs-down"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="padding-20">
                                                <div class="text-right">
                                                    <h3 class="font-light mb-0">
                                                        <i class="ti-arrow-up text-success"></i>
                                                        {{ formatPrice( inventoryStatus?.purchaseOrders?.rejected) }}
                                                    </h3>
                                                    <span class="text-muted">Rejected</span>
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
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Inventory Status</h4>
                </div>
                <div class="card-body">
                    <div class="row m-1 mt-3">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-purple">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h4 class="font-light mb-0"> {{ inventoryStatus.categories }}</h4>
                                            <span class="text-muted">Active Categories</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-green">
                                    <i class="fas fa-hiking"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h4 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ inventoryStatus.totalTags }}
                                            </h4>
                                            <span class="text-muted">Tags</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-cyan">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h4 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ inventoryStatus.highStock }}
                                            </h4>
                                            <span class="text-muted">High Stock</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon l-bg-orange">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="padding-20">
                                        <div class="text-right">
                                            <h4 class="font-light mb-0">
                                                <i class="ti-arrow-up text-success"></i> {{ inventoryStatus.lowStock }}
                                            </h4>
                                            <span class="text-muted">Low Stock</span>
                                        </div>
                                    </div>
                                </div>
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
        name : 'DashboardSectionFour',
        props : ['inventoryStatus'],
        data() {
          return {
              web_url : process.env.MIX_WEB_URL,
              public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
          };
        },
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
