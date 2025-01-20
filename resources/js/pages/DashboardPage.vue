<template>
    <div>
        <div class="row">
            <div class="col-md-12">

                <form @submit.prevent="applyFilter" class="row col-md-12 mb-3">

                    <div class="col-md-4">
                        <label for="">From</label>
                        <input type="date" v-model="filter.from" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="">To</label>
                        <input type="date" v-model="filter.to" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="">Action</label><br>
                        <button class="btn btn-primary mr-2" @click="applyFilter">Filter</button>
                        <button class="btn btn-danger" @click="resetFilter">Reset</button>
                    </div>
                </form>
            </div>

            <DashboardSectionOne
                :orders="orders"
                :approvedDropshipper="approvedDropshipper"
                :activeSeller="activeSeller"
                :liveProduct="liveProduct"
                :orderProcessed="orderProcessed"
            />

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-6">
                        <div class="card">
                          <div class="card-header">
                            <h4>Dropshipper Registration</h4>
                          </div>
                          <div class="card-body">
                            <div class="recent-report__chart">
                              <div id="chart2"></div>
                            </div>
                          </div>
                        </div>
                      </div>


                        <div class="col-xl-6 col-md-6 col-lg-6">
                          <div class="card">
                            <div class="card-header">
                              <h4>Revenue-Orders Chart</h4>
                            </div>
                            <div class="card-body">

                              <div class="recent-report__chart">
                                <div id="chart3"></div>
                              </div>
                            </div>
                          </div>
                        </div>


                        <DashboardSectionTwo
                            :dropshipper="pendingPayouts"
                            :applications="applications"
                            :pendingRequests="pendingRequests"
                            :allProcessedOrders="allProcessedOrders"
                        />

                        <DashboardSectionThree
                            :allProcessedOrders="allProcessedOrders"
                            :processOrders="processOrders"
                        />

                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="card gradient-bottom">
                                  <div class="card-header">
                                    <h4>Top 5 Dropshippers</h4>

                                  </div>
                                  <div class="card-body">
                                    <ul class="list-unstyled list-unstyled-border">
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-3.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">112 Sales</div>
                                          </div>
                                          <div class="media-title">Mobile</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="61%"></div>
                                              <div class="budget-price-label">$24,897</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="38%"></div>
                                              <div class="budget-price-label">$18,865</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-4.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">49 Sales</div>
                                          </div>
                                          <div class="media-title">Laptop</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="78%"></div>
                                              <div class="budget-price-label">$74,568</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="55%"></div>
                                              <div class="budget-price-label">$65,892</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-1.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">63 Sales</div>
                                          </div>
                                          <div class="media-title">Headphone</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="38%"></div>
                                              <div class="budget-price-label">$2,859</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="25%"></div>
                                              <div class="budget-price-label">$1,872</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-2.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">28 Sales</div>
                                          </div>
                                          <div class="media-title">Tablet</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="48%"></div>
                                              <div class="budget-price-label">$11,238</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="33%"></div>
                                              <div class="budget-price-label">$7,564</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-5.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">19 Sales</div>
                                          </div>
                                          <div class="media-title">Camera</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="91%"></div>
                                              <div class="budget-price-label">$7,285</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="74%"></div>
                                              <div class="budget-price-label">$5,147</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                    </ul>
                                  </div>
                                  <div class="card-footer pt-3 d-flex justify-content-center">
                                    <div class="budget-price justify-content-center">
                                      <div class="budget-price-square bg-primary" data-width="20"></div>
                                      <div class="budget-price-label">Total Paid</div>
                                    </div>
                                    <div class="budget-price justify-content-center">
                                      <div class="budget-price-square bg-danger" data-width="20"></div>
                                      <div class="budget-price-label">Balance</div>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="col-12 col-sm-6 col-lg-4">
                                <div class="card gradient-bottom">
                                  <div class="card-header">
                                    <h4>Top 5 Selling Products</h4>

                                  </div>
                                  <div class="card-body">
                                    <ul class="list-unstyled list-unstyled-border">
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-3.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">112 Sales</div>
                                          </div>
                                          <div class="media-title">Mobile</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="61%"></div>
                                              <div class="budget-price-label">$24,897</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="38%"></div>
                                              <div class="budget-price-label">$18,865</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-4.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">49 Sales</div>
                                          </div>
                                          <div class="media-title">Laptop</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="78%"></div>
                                              <div class="budget-price-label">$74,568</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="55%"></div>
                                              <div class="budget-price-label">$65,892</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-1.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">63 Sales</div>
                                          </div>
                                          <div class="media-title">Headphone</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="38%"></div>
                                              <div class="budget-price-label">$2,859</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="25%"></div>
                                              <div class="budget-price-label">$1,872</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-2.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">28 Sales</div>
                                          </div>
                                          <div class="media-title">Tablet</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="48%"></div>
                                              <div class="budget-price-label">$11,238</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="33%"></div>
                                              <div class="budget-price-label">$7,564</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-5.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">19 Sales</div>
                                          </div>
                                          <div class="media-title">Camera</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="91%"></div>
                                              <div class="budget-price-label">$7,285</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="74%"></div>
                                              <div class="budget-price-label">$5,147</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                    </ul>
                                  </div>
                                  <div class="card-footer pt-3 d-flex justify-content-center">
                                    <div class="budget-price justify-content-center">
                                      <div class="budget-price-square bg-primary" data-width="20"></div>
                                      <div class="budget-price-label">Selling Price</div>
                                    </div>
                                    <div class="budget-price justify-content-center">
                                      <div class="budget-price-square bg-danger" data-width="20"></div>
                                      <div class="budget-price-label">Product Cost</div>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="col-12 col-sm-6 col-lg-4">
                                <div class="card gradient-bottom">
                                  <div class="card-header">
                                    <h4>Top 5 Suppliers</h4>

                                  </div>
                                  <div class="card-body">
                                    <ul class="list-unstyled list-unstyled-border">
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-3.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">112 Sales</div>
                                          </div>
                                          <div class="media-title">Mobile</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="61%"></div>
                                              <div class="budget-price-label">$24,897</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="38%"></div>
                                              <div class="budget-price-label">$18,865</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-4.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">49 Sales</div>
                                          </div>
                                          <div class="media-title">Laptop</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="78%"></div>
                                              <div class="budget-price-label">$74,568</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="55%"></div>
                                              <div class="budget-price-label">$65,892</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-1.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">63 Sales</div>
                                          </div>
                                          <div class="media-title">Headphone</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="38%"></div>
                                              <div class="budget-price-label">$2,859</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="25%"></div>
                                              <div class="budget-price-label">$1,872</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-2.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">28 Sales</div>
                                          </div>
                                          <div class="media-title">Tablet</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="48%"></div>
                                              <div class="budget-price-label">$11,238</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="33%"></div>
                                              <div class="budget-price-label">$7,564</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="media">
                                        <img class="mr-3 rounded" width="55" src="assets/img/products/product-5.png" alt="product">
                                        <div class="media-body">
                                          <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">19 Sales</div>
                                          </div>
                                          <div class="media-title">Camera</div>
                                          <div class="mt-1">
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-primary" data-width="91%"></div>
                                              <div class="budget-price-label">$7,285</div>
                                            </div>
                                            <div class="budget-price">
                                              <div class="budget-price-square bg-danger" data-width="74%"></div>
                                              <div class="budget-price-label">$5,147</div>
                                            </div>
                                          </div>
                                        </div>
                                      </li>
                                    </ul>
                                  </div>
                                  <div class="card-footer pt-3 d-flex justify-content-center">
                                    <div class="budget-price justify-content-center">
                                      <div class="budget-price-square bg-primary" data-width="20"></div>
                                      <div class="budget-price-label">Total Paid</div>
                                    </div>
                                    <div class="budget-price justify-content-center">
                                      <div class="budget-price-square bg-danger" data-width="20"></div>
                                      <div class="budget-price-label">Balance</div>
                                    </div>
                                  </div>
                                </div>
                              </div>


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
                                                                            {{ formatPrice( po.totalAmount) }}
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
                                                                            {{ formatPrice(po.paid) }}
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
                                                                            {{ formatPrice(po.remaining) }}
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
                                                                            {{ po.totalPo }}
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
                                                                            {{ po.pending }}
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
                                                                            {{ po.approved }}
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
                                                                            {{ po.rejected }}
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
                                                                <h4 class="font-light mb-0"> 524</h4>
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
                                                                    <i class="ti-arrow-up text-success"></i> 4,000,0
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

                            <div class="col-md-6 mt-5">
                                <div class="card">
                                    <div class="card-header">
                                      <h4>Categories-Wise Published Products</h4>
                                    </div>
                                    <div class="card-body">
                                      <ul class="list-group">
                                        <li v-for="category in categoryWiseProducts" :key="category.id" class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ category.name }}
                                          <span class="badge badge-primary badge-pill">{{ category.product_count }}</span>
                                        </li>
                                      </ul>
                                    </div>
                                  </div>
                            </div>

                            <div class="col-md-6 mt-5">
                                <div class="card">
                                    <div class="card-header">
                                      <h4>Tag-Wise Published Products</h4>
                                    </div>
                                    <div class="card-body">
                                      <ul class="list-group">
                                        <li v-for="tag in tagWiseProducts" :key="tag.id" class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ tag.name }}
                                          <span class="badge badge-primary badge-pill">{{ tag.tagged_count }}</span>
                                        </li>
                                      </ul>
                                    </div>
                                  </div>
                            </div>


            <div class="col-md-12">
                <div class="card">
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
                                                        <h5 class="font-15">Normal / COD</h5>
                                                        <h2 class="mb-3 font-18">
                                                            {{ orders.normalOrders }}
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
                                                        <h5 class="font-15">Daraz</h5>
                                                        <h2 class="mb-3 font-18">
                                                            {{ orders.darazOrders }}
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
                                                            Direct Sales
                                                        </h5>
                                                        <h2 class="mb-3 font-18">
                                                            {{ orders.cashOrders }}
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

                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-header">
                                        <th colspan="5" class="h5">Sales Stats</th>
                                    </tr>
                                    <tr>
                                        <th>Gross Sales</th>
                                        <th>Item Sold</th>
                                        <th>Products</th>
                                        <th>Packing</th>
                                        <th>Courier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-end h6">{{ formatPrice(orders.grossSales) }}</td>
                                        <td class="text-end h6">{{ formatPrice(orders.itemSolds) }}</td>
                                        <td class="text-end h6">{{ formatPrice(orders.productCost) }}</td>
                                        <td class="text-end h6">
                                            <span><strong>Overall</strong> {{ formatPrice(orders.packing) }}</span><br>
                                            <span><strong>Ours</strong> {{ formatPrice(orders.packingProfit) }}</span>
                                        </td>
                                        <td class="text-end h6">
                                            <span><strong>Overall</strong> {{ formatPrice(orders.courier) }}</span><br>
                                            <span><strong>Ours</strong> {{ formatPrice(orders.courierProfit) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="0">Cost of Goods Sold </th>
                                        <th colspan="2" class="h6"> {{ formatPrice(orders.costOfGood) }}</th>
                                        <th colspan="0">Gross Profit</th>
                                        <th colspan="2" class="h6"> {{ formatPrice(orders.grossProfit) }}</th>
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
                                        <th>Profit</th>
                                        <th>Payable</th>
                                        <th>Withdraw</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in topDropshippers" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td><a href="#" @click="fetchDropshipperDetails(item.dropshipper.id)"
                                                data-toggle="modal" data-target="#dropShipperDetail">{{ item.name }}</a>
                                        </td>
                                        <td>{{ item.dropshipper.shops?.length || 0 }}</td>
                                        <td>{{ item.total_orders }}</td>
                                        <td>{{ item.total_returns }}</td>
                                        <td class="align-middle" width="30%">
                                            <div class="progress-text text-right">
                                                {{ calculateHealth(item) }}%
                                            </div>
                                            <div class="progress" data-height="2">
                                                <div :class="['progress-bar', calculateHealth(item) > 90 ? 'bg-success' : 'bg-primary']"
                                                    :style="{ width: calculateHealth(item) + '%' }">
                                                </div>
                                            </div>
                                        </td>

                                        <td>{{ formatPrice(calculateDeliveredSales(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateProductCost(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateTotalCost(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateProfit(item.delivered_orders)) }}</td>
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
                                            parseFloat(item.total_quantity) * parseFloat(item.variation.avg_price)))
                                            }}</td>
                                        <td>{{ ((parseFloat(item.selling_price) - (parseFloat(item.total_quantity) *
                                            parseFloat(item.variation.avg_price))) / (parseFloat(item.total_quantity)
                                                * parseFloat(item.variation.avg_price)) * 100).toFixed(2) }}%</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body row">
                        <div class="col-md-6 mt-5">
                            <h6>Low Stock Products</h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>QTY SOLD</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in lowStock" :key="product.id">
                                        <td>{{ product.title }}</td>
                                        <td>{{ product.variation.sku || '' }}</td>
                                        <td>{{ product.issuance_sum_quantity || 0 }}</td>
                                        <td>{{ formatPrice( product.issuance_sum_total || 0 ) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 mt-5">
                            <h6>High Stock Products</h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>QTY SOLD</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in highStock" :key="product.id">
                                        <td>{{ product.title }}</td>
                                        <td>{{ product.variation.sku || '' }}</td>
                                        <td>{{ product.issuance_sum_quantity || 0 }}</td>
                                        <td>{{ formatPrice( product.issuance_sum_total || 0 ) }}</td>
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
        <DropshipperDetails :details="dropShipperDetails" />
    </div>
</template>

<script>
import DashboardSectionOne from '../components/admin/dashboard/DashboardSectionOne.vue';
import DashboardSectionThree from '../components/admin/dashboard/DashboardSectionThree.vue';
import DashboardSectionTwo from '../components/admin/dashboard/DashboardSectionTwo.vue';
import DropshipperDetails from '../components/admin/request/DropshipperDetails.vue';

export default {
    name: 'DashboardPage',
    components: {
        DropshipperDetails,
        DashboardSectionOne,
        DashboardSectionTwo,
        DashboardSectionThree
    },
    data() {
        return {
            api_url: process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            filter: {
                dropshipper: 0,
                shop: 0,
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            },
            topTenProducts: [],
            dropshipper: {},
            topDropshippers: [],
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
            dropShipperDetails: {},
            po : {
                totalPo : 0,
                approved : 0,
                pending : 0,
                rejected : 0,
                totalAmount : 0,
                remaining : 0,
                paid : 0
            },
            orders : {
                totalOrder: 0,
                inProcess: 0,
                outOfDelivery: 0,
                delivered: 0,
                returns: 0,
                normalOrders: 0,
                darazOrders: 0,
                cashOrders: 0,
                grossSales : 0,
                itemSolds : 0,
                productCost : 0,
                packing : 0,
                packingProfit : 0,
                courier : 0,
                courierProfit : 0,
                costOfGood : 0,
                grossProfit : 0
            },
            dropshipper : {
                total : 0,
                paid : 0,
                remaining : 0,
                total_sellers : 0
            },
            inventoryStatus : {
                numberOfItemsInStock  : 0,
                stockValue            : 0,
                totalItemInReturn     : 0,
                totalReturnValue      : 0,
                grossStockValue       : 0,
                numberOfProductInStock : 0,
                outOfStock            : 0,
                publishedProducts     : 0,
                draftProducts         : 0,
                trashedProducts       : 0,
            },
            categoryWiseProducts : [],
            tagWiseProducts : [],
            fastMovingProducts : [],
            slowMovingProducts : [],
            lowStock : [],
            highStock : [],
            approvedDropshipper : {},
            activeSeller : {},
            liveProduct : {},
            orderProcessed : {},
            pendingPayouts : {},
            pendingRequests : {
                dropshippers: {
                    total: 0,
                    pending: 0,
                    approved: 0,
                    reject: 0
                },
                supplier: {
                    total: 0,
                    pending: 0,
                    approved: 0,
                    reject: 0
                },
            },
            allProcessedOrders : {},
            processOrders : {}
        };
    },
    created() {
        this.fetchData({from : null , to : null });
        this.top10SellingProducts();
        this.top10Dropshippers(this.filter);
        this.fetchTicketStatusCounts();
        this.fetchPurchaseOrders( this.filter );
        this.fetchCategoryandTagWiseProducts();
    },
    methods: {
        fetchPurchaseOrders( data ){
                let vm = this;
                axios
                .post(this.api_url + "inventory/products/purchase-orders/status-counts", data)
                .then((response) => {
                    const results = response.data.response[0];

                    vm.po.totalPo = results.purchaseOrders.totalPo;
                    vm.po.approved = results.purchaseOrders.approved;
                    vm.po.pending = results.purchaseOrders.pending;
                    vm.po.rejected = results.purchaseOrders.rejected;
                    vm.po.totalAmount = results.purchaseOrders.totalAmount;
                    vm.po.remaining = results.purchaseOrders.remaining;
                    vm.po.paid = vm.po.totalAmount - vm.po.remaining;

                    vm.inventoryStatus = {
                        numberOfItemsInStock  : results.numberOfItemsInStock,
                        stockValue            : results.stockValue,
                        totalItemInReturn     : results.totalItemInReturn,
                        totalReturnValue      : results.totalReturnValue,
                        grossStockValue       : results.grossStockValue,
                        numberOfProductInStock : results.numberOfProductInStock,
                        outOfStock            : results.outOfStock,
                        publishedProducts     : results.publishedProducts,
                        draftProducts         : results.draftProducts,
                        trashedProducts       : results.trashedProducts,

                        categoryWiseProducts  : results.categoryWiseProducts,
                        tagWiseProducts       : results.tagWiseProducts
                    }
                })
                .catch((err) => this.fetchPurchaseOrders());
            },
        fetchCategoryandTagWiseProducts(){
            let vm = this;
                axios
                .get(this.api_url + "users/dashboard/product-wise-count")
                .then((response) => {

                    const results = response.data.response;
                    vm.categoryWiseProducts  = results.categoryWiseProducts,
                    vm.tagWiseProducts       = results.tagWiseProducts
                    vm.fastMovingProducts    = results.fastMovingProducts
                    vm.slowMovingProducts    = results.slowMovingProducts

                    vm.lowStock    = results.lowStock
                    vm.highStock    = results.highStock

                    setTimeout(() => {
                        vm.tagWiseTable();
                        vm.categoryWiseTable();
                    },300)

                })
        },
        fetchTicketStatusCounts() {
            axios.get(this.api_url + 'tickets/status-counts').then((response) => {
                const data = response.data;
                this.totalTicketSum.total_tickets = data.total_tickets;
                this.totalTicketSum.awaiting_your_reply = data.awaiting_your_reply;
                this.totalTicketSum.awaiting_yourmart_reply = data.awaiting_yourmart_reply;
                this.totalTicketSum.closed = data.closed;
                this.totalTicketSum.expired = data.expired;
                this.totalTicketSum.reviewed = data.reviewed;
                this.totalTicketSum.in_process = data.in_process;
            });
        },
        fetchDropshipperDetails(id) {
            let vm = this;
            axios
                .post(this.api_url + "dropshippers/details", { id })
                .then((response) => {
                    vm.dropShipperDetails = response.data.response[0]
                });

        },
        calculateHealth(item) {
            const deliveredCount = item.total_orders; // Count of delivered orders
            const returnedCount = item.total_returns;   // Count of returned orders

            // You can now use these counts for further logic, e.g., calculating account health
            const totalOrders = deliveredCount + returnedCount;
            let accountHealth = 0;
            if (totalOrders > 0) {
                accountHealth = (deliveredCount / totalOrders) * 100;
            }
            return Math.round(accountHealth);
        },
        calculateDeliveredSales(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.selling_price) + parseFloat(order.advance_amount), 0);
        },
        calculateProductCost(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.total_bill) - parseFloat(order.courier_service_price) - parseFloat(order.packaging_price), 0);
        },
        calculateTotalCost(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.courier_service_price) + parseFloat(order.packaging_price), 0);
        },
        calculateProfit(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + (parseFloat(order.selling_price) + parseFloat(order.advance_amount)) - (parseFloat(order.total_bill)), 0);
        },
        fetchData( data ) {
            let vm = this;
            axios
                .post(this.api_url + "users/dashboard", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.approvedDropshipper = results.approvedDropshipper;
                    vm.activeSeller = results.activeSeller;
                    vm.liveProduct = results.liveProduct;
                    vm.orderProcessed = results.orderProcessed;
                    vm.pendingPayouts = results.payOuts;
                    vm.pendingRequests = results.pendingRequests;
                    vm.allProcessedOrders = results.allProcessedOrders;
                    vm.processOrders = results.orders;

                    vm.orders = {
                        totalOrder: results.totalOrder,
                        inProcess: results.inProcess,
                        outOfDelivery: results.outOfDelivery,
                        delivered: results.delivered,
                        returns: results.returns,
                        normalOrders: results.normalOrders,
                        darazOrders: results.darazOrders,
                        cashOrders: results.cashOrders,
                        grossSales : results.grossSales,
                        itemSolds : results.itemSolds,
                        productCost : results.productCost,
                        packing : results.packing,
                        packingProfit : results.packingProfit,
                        courier : results.courier,
                        courierProfit : results.courierProfit,
                        costOfGood : results.costOfGood,
                        grossProfit : results.grossProfit,
                    }
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
        top10Dropshippers( data ) {
            let vm = this;

            axios
                .post(this.api_url + "users/dashboard/top-10-dropshippers", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.topDropshippers = results.dropshippers;

                    // Initialize counters
                    let dropshipperApproved = 0;
                    let dropshipperRejected = 0;
                    let dropshipperPending = 0;

                    let supplierApproved = 0;
                    let supplierRejected = 0;
                    let supplierPending = 0;

                    // Count each status
                    results.dropshipperApplication.forEach(item => {
                        if (item.status === 1) {
                            dropshipperApproved++;
                        } else if (item.status === 2) {
                            dropshipperRejected++;
                        } else if (item.status === 0) {
                            dropshipperPending++;
                        }
                    });

                    // Update applications for dropshippers and suppliers
                    this.applications.dropshippers.total = results.dropshipperApplication.length;
                    this.applications.dropshippers.approved = dropshipperApproved;
                    this.applications.dropshippers.reject = dropshipperRejected;
                    this.applications.dropshippers.pending = dropshipperPending;

                    results.shipperApplication.forEach(item => {
                        if (item.status === 1) {
                            supplierApproved++;
                        } else if (item.status === 2) {
                            supplierRejected++;
                        } else if (item.status === 0) {
                            supplierPending++;
                        }
                    });

                    this.applications.supplier.total = results.shipperApplication.length;
                    this.applications.supplier.approved = supplierApproved;
                    this.applications.supplier.reject = supplierRejected;
                    this.applications.supplier.pending = supplierPending;

                    this.dropshipper = results.dropshipperPayouts

                    setTimeout(() => {
                        vm.topDropshipperTable();
                    }, 300)
                })

        },
        applyFilter() {
            this.fetchData( this.filter );
            this.clearDatatable();
            this.top10Dropshippers(this.filter);
            this.fetchPurchaseOrders( this.filter );
        },
        clearDatatable(){
            const table = $('#topDropshipperTable').DataTable();
            table.destroy();
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
        tagWiseTable() {
            $("#tagWiseTable").DataTable();
        },
        categoryWiseTable() {
            $("#categoryWiseTable").DataTable();
        },
        getPercentage(statusCount) {
            if (this.totalTicketSum.total_tickets === 0) return 0;
            return Math.round((statusCount / this.totalTicketSum.total_tickets) * 100);
        }
    },
};
</script>
