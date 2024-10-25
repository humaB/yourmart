<template>
    <div>
        <div class="row">
            <div class="col-md-12">

                <form @submit.prevent="applyFilter" class="row col-md-12 mb-3">
                    <div class="col-md-3">
                        <label for="">Select Dropshipper</label>
                        <select v-model="filter.status" class="form-control">
                            <option value="">Select from the following</option>
                            <option value="0">Order Collection</option>
                            <option value="1">Inventory Manager</option>
                            <option value="2">QA Manager</option>
                            <option value="3">Packing/Dispatch</option>
                            <option value="4">Auditor</option>
                            <option value="5">Under Review</option>
                            <option value="7">Rejected</option>
                            <option value="11">Out for Delivery</option>
                            <option value="8">Delivered</option>
                            <option value="9">Returned</option>
                            <option value="10">Returned to store</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="">Select Shop</label>
                        <select v-model="filter.status" class="form-control">
                            <option value="">Select from the following</option>
                            <option value="0">Order Collection</option>
                            <option value="1">Inventory Manager</option>
                            <option value="2">QA Manager</option>
                            <option value="3">Packing/Dispatch</option>
                            <option value="4">Auditor</option>
                            <option value="5">Under Review</option>
                            <option value="7">Rejected</option>
                            <option value="11">Out for Delivery</option>
                            <option value="8">Delivered</option>
                            <option value="9">Returned</option>
                            <option value="10">Returned to store</option>
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
                    <div class="col-md-2">
                        <label for="">Action</label><br>
                        <button class="btn btn-primary mr-2" @click="applyFilter">Filter</button>
                        <button class="btn btn-danger" @click="resetFilter">Reset</button>
                    </div>
                </form>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <h4 class="pl-4 pt-4">Orders & Pay-Outs</h4>
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
                                                            {{ formatPrice(dropshipper.total_payable) }}
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
                                                            {{ formatPrice(dropshipper.total_paid) }}
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
                                                            {{ formatPrice(dropshipper.remaining_amount) }}
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
                                    <td style="width: 20%; padding : 10px">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-cyan">
                                                <i class="fa fa-shopping-bag"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ totalOrders }}
                                                        </h3>
                                                        <span class="text-muted">Total Order</span>
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
                                                            <i class="ti-arrow-up text-success"></i> {{ inProcessOrder
                                                            }}
                                                        </h3>
                                                        <span class="text-muted">In Process</span>
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
                                                            <i class="ti-arrow-up text-success"></i> {{
                                                            outFordeliveredOrders }}
                                                        </h3>
                                                        <span class="text-muted">Out For Delivery</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width: 22%;padding : 10px">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-green">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ deliveredOrders
                                                            }}
                                                        </h3>
                                                        <span class="text-muted">Delivered</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width: 20%;padding : 10px">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-cyan">
                                                <i class="fas fa-undo"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ failedOrder }}
                                                        </h3>
                                                        <span class="text-muted">Return</span>
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
                    <h4 class="pl-4 pt-4">Purchase Orders & Pay-Outs</h4>
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
                                                            {{ formatPrice(dropshipper.total_payable) }}
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
                                                            {{ formatPrice(dropshipper.total_paid) }}
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
                                                            {{ formatPrice(dropshipper.remaining_amount) }}
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
                                                            <i class="ti-arrow-up text-success"></i> {{ totalOrders }}
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
                                                            <i class="ti-arrow-up text-success"></i> {{ inProcessOrder
                                                            }}
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
                                                            <i class="ti-arrow-up text-success"></i> {{
                                                            outFordeliveredOrders }}
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
                                                            <i class="ti-arrow-up text-success"></i> {{ deliveredOrders
                                                            }}
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
                        <h4>Tickets Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th>Total Tickets</th>
                                    <th>Awaiting Your Reply</th>
                                    <th>Awaiting YourMart Reply</th>
                                    <th>Closed</th>
                                    <th>Expired</th>
                                    <th>Reviewed</th>
                                    <th>In-Process</th>
                                </tr>
                                <tr>
                                    <td>{{ totalTicketSum.total_tickets }}</td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.awaiting_your_reply) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-success"
                                                :style="{ width: getPercentage(totalTicketSum.awaiting_your_reply) + '%' }">
                                            </div>
                                        </div>
                                        {{ getPercentage(totalTicketSum.awaiting_your_reply) }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.awaiting_yourmart_reply) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-primary"
                                                :style="{ width: getPercentage(totalTicketSum.awaiting_yourmart_reply) + '%' }">
                                            </div>
                                        </div>
                                        {{ totalTicketSum.awaiting_yourmart_reply }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.closed) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-danger"
                                                :style="{ width: getPercentage(totalTicketSum.closed) + '%' }"></div>
                                        </div>
                                        {{ totalTicketSum.closed }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.expired) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-success"
                                                :style="{ width: getPercentage(totalTicketSum.expired) + '%' }"></div>
                                        </div>
                                        {{ totalTicketSum.expired }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.reviewed) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-info"
                                                :style="{ width: getPercentage(totalTicketSum.reviewed) + '%' }"></div>
                                        </div>
                                        {{ totalTicketSum.reviewed }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text text-right text-secondary">
                                            {{ getPercentage(totalTicketSum.in_process) }}%
                                        </div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-info"
                                                :style="{ width: getPercentage(totalTicketSum.in_process) + '%' }">
                                            </div>
                                        </div>
                                        {{ totalTicketSum.in_process }}
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
                        <h4>Top Selling Products</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="topSellingProductTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>SKU #</th>
                                        <th>Item Sold</th>
                                        <th>Buying Avg Price</th>
                                        <th>Buying Cost</th>
                                        <th>Selling Avg Price</th>
                                        <th>Selling Cost</th>
                                        <th>Net Profit</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in topTenProducts" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.variation ? item.variation.product.title : '' }}</td>
                                        <td>{{ item.variation ? item.variation.sku : '' }}</td>
                                        <td>{{ item.total_quantity }}</td>
                                        <td>{{ item.variation.avg_price }}</td>
                                        <td>{{ formatPrice(item.variation.avg_price * item.total_quantity) }}</td>
                                        <td>{{ (item.selling_price / item.total_quantity).toFixed(2) }}</td>
                                        <td>{{ formatPrice(item.selling_price) }}</td>

                                        <td>{{ formatPrice(parseFloat(item.selling_price) - (
                                            parseFloat(item.total_quantity) * parseFloat(item.variation.avg_price) ) )
                                            }}</td>
                                        <td>{{ ((parseFloat(item.selling_price) - (parseFloat(item.total_quantity) *
                                            parseFloat(item.variation.avg_price))) / (parseFloat(item.total_quantity)
                                            * parseFloat(item.variation.avg_price) ) * 100).toFixed(2)}}%</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Top 10 Dropshippers</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="topDropshipperTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Stores</th>
                                        <th>Orders</th>
                                        <th>Returned Orders</th>
                                        <th>Success Rate</th>
                                        <th>Sales</th>
                                        <th>COGS</th>
                                        <th>Packing & Labeling</th>
                                        <th>Payable</th>
                                        <th>Withdraw</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in topDropshippers" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.dropshipper.shops?.length || 0 }}</td>
                                        <td>{{ item.total_orders }}</td>
                                        <td>{{ item.total_returns }}</td>
                                        <td>{{ calculateHealth(item) }}%</td>
                                        <td>{{ formatPrice(calculateDeliveredSales(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateProfit(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateTotalCost(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(item.dropshipper.total_payable) }}</td>
                                        <td>{{ formatPrice(item.dropshipper.total_paid) }}</td>
                                        <td>{{ formatPrice(item.dropshipper.remaining_amount) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>DS Applications Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="text-small float-right font-weight-bold text-muted">2,675</div>
                            <div class="font-weight-bold">Applications</div>
                            <div class="progress" data-height="5">
                                <div class="progress-bar l-bg-purple" role="progressbar" data-width="80%"
                                    aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="text-small float-right font-weight-bold text-muted">1,753</div>
                            <div class="font-weight-bold">Approved</div>
                            <div class="progress" data-height="5">
                                <div class="progress-bar l-bg-green" role="progressbar" data-width="67%"
                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="text-small float-right font-weight-bold text-muted">1,254</div>
                            <div class="font-weight-bold">Rejected</div>
                            <div class="progress" data-height="5">
                                <div class="progress-bar l-bg-orange" role="progressbar" data-width="58%"
                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="text-small float-right font-weight-bold text-muted">984</div>
                            <div class="font-weight-bold">In-Process</div>
                            <div class="progress" data-height="5">
                                <div class="progress-bar l-bg-yellow" role="progressbar" data-width="36%"
                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Suppliers Applications Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="text-small float-right font-weight-bold text-muted">2,675</div>
                            <div class="font-weight-bold">Applications</div>
                            <div class="progress" data-height="5">
                                <div class="progress-bar l-bg-purple" role="progressbar" data-width="80%"
                                    aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="text-small float-right font-weight-bold text-muted">1,753</div>
                            <div class="font-weight-bold">Approved</div>
                            <div class="progress" data-height="5">
                                <div class="progress-bar l-bg-green" role="progressbar" data-width="67%"
                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="text-small float-right font-weight-bold text-muted">1,254</div>
                            <div class="font-weight-bold">Rejected</div>
                            <div class="progress" data-height="5">
                                <div class="progress-bar l-bg-orange" role="progressbar" data-width="58%"
                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="text-small float-right font-weight-bold text-muted">984</div>
                            <div class="font-weight-bold">In-Process</div>
                            <div class="progress" data-height="5">
                                <div class="progress-bar l-bg-yellow" role="progressbar" data-width="36%"
                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
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
                                                <h4 class="font-light mb-0"> 524</h4>
                                                <span class="text-muted">Product in Stock</span>
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
                                                    <i class="ti-arrow-up text-success"></i> 4,000,0
                                                </h4>
                                                <span class="text-muted">Stock Value</span>
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
                                                    <i class="ti-arrow-up text-success"></i> 15
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
                                                    <i class="ti-arrow-up text-success"></i>10
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

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Courier Performance (Last 30 days)
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <canvas id="myChart2" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import { filter } from 'lodash';

export default {
    data() {
        return {
            api_url: process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            filter: {
                dropshipper: 0,
                shop: 0,
                from: '',
                to: '',
            },
            topTenProducts: [],
            dropshipper: {},
            topDropshippers : [],
            totalOrders: 0,
            totalTicketSum: {
                total_tickets: 0,
                awaiting_your_reply: 0,
                awaiting_yourmart_reply: 0,
                closed: 0,
                expired: 0,
                reviewed: 0,
                in_process: 0,
            },
        };
    },
    created() {
        this.fetchData();
        this.top10SellingProducts();
        this.top10Dropshippers();
    },
    methods: {
        calculateHealth( item ){
            const  deliveredCount = item.total_orders; // Count of delivered orders
            const returnedCount = item.total_returns;   // Count of returned orders

            // You can now use these counts for further logic, e.g., calculating account health
            const totalOrders = deliveredCount + returnedCount;
            let  accountHealth = 0;
            if (totalOrders > 0) {
                accountHealth = (deliveredCount / totalOrders) * 100;
            }
            return accountHealth.toFixed(2);
        },
        calculateDeliveredSales(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.selling_price) + parseFloat(order.advance_amount), 0);
        },
        calculateProfit(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.total_bill) - parseFloat(order.courier_service_price) - parseFloat(order.packaging_price), 0);
        },
        calculateTotalCost(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.courier_service_price) + parseFloat(order.packaging_price), 0);
        },
        fetchData() {
            let vm = this;
            axios
                .get(this.api_url + "users/dashboard")
                .then((response) => {
                    const results = response.data.response;
                })

        },
        top10SellingProducts() {
            let vm = this;
            axios
                .get(this.api_url + "users/dashboard/top-selling-products")
                .then((response) => {
                    const results = response.data.response;
                    vm.topTenProducts = results;
                    setTimeout(() => {
                        vm.topSellingProductTable();
                    }, 300)
                })

        },
        top10Dropshippers() {
            let vm = this;
            axios
                .get(this.api_url + "users/dashboard/top-10-dropshippers")
                .then((response) => {
                    const results = response.data.response;
                    vm.topDropshippers = results.dropshippers;
                    setTimeout(() => {
                        vm.topDropshipperTable();
                    }, 300)
                })

        },
        applyFilter() {

        },
        resetFilter() {

        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        topSellingProductTable() {
            $("#topSellingProductTable").DataTable({
                dom: "Bfrtip",
                buttons: ["copy", "csv", "excel"],
            });
        },
        topDropshipperTable() {
            $("#topDropshipperTable").DataTable({
                dom: "Bfrtip",
                buttons: ["copy", "csv", "excel"],
            });
        },
        getPercentage(statusCount) {
            if (this.totalTicketSum.total_tickets === 0) return 0;
            return Math.round((statusCount / this.totalTicketSum.total_tickets) * 100);
        },
    },
};
</script>
