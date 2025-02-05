<template>
    <div>
        <div class="card">
            <div class="card-header">
              <h4>Courier Record</h4>
            </div>
            <div class="card-body">
              <ul class="nav nav-pills" id="myTab3" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="home-tab3" data-toggle="tab" href="#home3" role="tab" aria-controls="home" aria-selected="true">Pending Shipments</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="profile-tab3" data-toggle="tab" href="#profile3" role="tab" aria-controls="profile" aria-selected="false">Handed-Over to Courier</a>
                </li>

              </ul>

              <div class="row">
                  <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12 mt-5">
                    <div class="card bg-info">
                        <div class="card-statistic-4 text-white">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="col-lg-8 col-md-6 col-sm-6 col-xs-6 pr-0">
                                        <div class="card-content">
                                            <h5 class="font-15">Pending Shipments Amount</h5>
                                            <h2 class="mb-3 font-18">
                                                {{ formatPrice(totalPendingShipment) }}
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

                <div class="col-xl-63 col-lg-6 col-md-6 col-sm-6 col-xs-12 mt-5">
                    <div class="card bg-success">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row">
                                    <div class="col-lg-8 col-md-6 col-sm-6 col-xs-6 pr-0">
                                        <div class="card-content text-white">
                                            <h5 class="font-15">Handed-Over Shipments Amount</h5>
                                            <h2 class="mb-3 font-18">
                                                {{ formatPrice(totalHandOver) }}
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
              </div>
              <div class="tab-content" id="myTabContent2">
                <div class="tab-pane fade show active" id="home3" role="tabpanel" aria-labelledby="home-tab3">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                          <div class="card card-primary">
                            <TableHeader :tableHeader="tableHeader" />

                            <div class="card-body">
                              <!-- Table -->
                              <div class="row">
                                <div class="col-md-12 mb-3">
                                    <h5>Scan Tracking #</h5>
                                    <input type="text" class="form-control" placeholder="Scan order which are dispatched"  @keypress.enter="scanOrder" v-model="orderNumber" :disabled="loader">
                                </div>

                                <form @submit.prevent="filterFunction" class="col-md-12 row">
                                    <div class="col-md-4">
                                        <label>From</label>
                                        <input type="date" class="form-control" v-model="filter.from">
                                    </div>
                                    <div class="col-md-4">
                                        <label>To</label>
                                        <input type="date" class="form-control" v-model="filter.to">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Action</label>
                                        <button class="btn btn-primary w-100"> Filter</button>
                                    </div>
                                </form>
                                <div class="col-md-12 mt-3">
                                        <table class="table table-bordered" id="order_table">
                                            <thead>
                                                <tr>
                                                    <th>Sr #</th>
                                                    <th>Order #</th>
                                                    <th>Tracking Number</th>
                                                    <th>Dropshipper</th>
                                                    <th>Amount</th>
                                                    <th>Order Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item,index) in pendingDispatchs" :key="item.id">
                                                    <td>{{ index + 1 }}</td>
                                                    <td>
                                                        {{ item.shop && item.shop.store_name ? item.shop.store_name.substring(0, 3) + '-' + item.order_no : item.order_no }}
                                                      </td>
                                                      <td>
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#trackingInformation"
                                                            @click="fetchTracking(item.id)">
                                                            {{ item.tracking_number }}
                                                        </a>

                                                    </td>
                                                    <td>{{ item.user.name }}</td>
                                                    <td>{{ formatPrice(item.total_bill) }}</td>
                                                    <td>{{ formatDate(item.created_at) }}</td>

                                                </tr>
                                            </tbody>
                                        </table>
                              </div>
                              <!-- END TABLE -->
                            </div>
                          </div>
                        </div>
                      </div>
                      </div>

                </div>
                <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile-tab3">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                          <div class="card card-primary">
                            <div class="card-header">
                                <h4>Shipped</h4>
                            </div>

                            <div class="card-body">
                              <!-- Table -->
                              <div class="row">

                                <form @submit.prevent="filterFunction" class="col-md-12 row">
                                    <div class="col-md-4">
                                        <label>From</label>
                                        <input type="date" class="form-control" v-model="filter.from">
                                    </div>
                                    <div class="col-md-4">
                                        <label>To</label>
                                        <input type="date" class="form-control" v-model="filter.to">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Action</label>
                                        <button class="btn btn-primary w-100"> Filter</button>
                                    </div>
                                </form>
                                <div class="col-md-12 mt-3">
                                        <table class="table table-bordered" id="dispatched-order_table">
                                            <thead>
                                                <tr>
                                                    <th>Sr #</th>
                                                    <th>Order #</th>
                                                    <th>Tracking Number</th>
                                                    <th>Amount</th>
                                                    <th>Dispatched Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item,index) in dispatcheds" :key="item.id">
                                                    <td>{{ index + 1 }}</td>
                                                    <td>
                                                        {{ item.order.shop && item.order.shop.store_name ? item.order.shop.store_name.substring(0, 3) + '-' + item.order.order_no : item.order.order_no }}
                                                    </td>
                                                    <td>{{ item.tracking_number }}</td>
                                                    <td>{{ formatPrice(item.total_amount) }}</td>
                                                    <td>{{ formatDate(item.created_at) }}</td>

                                                </tr>
                                            </tbody>
                                        </table>
                              </div>
                              <!-- END TABLE -->
                            </div>
                          </div>
                        </div>
                      </div>
                      </div>
                </div>

              </div>
            </div>
          </div>

          <TrackingDetailPopup :trackingDetails="trackingDetails" />

    </div>
</template>
<script>

import { filter } from "lodash";
import TableHeader from "../../../../components/table/TableHeaderComponent.vue";
import TrackingDetailPopup from "../../../../components/inventory/product/order/TrackingDetailPopup.vue";
import moment from "moment";

    export default {
        name : 'StorePendingCourierReturnPage',
        components: {
            TableHeader,
            TrackingDetailPopup
        },
        data() {
            return {
                public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
                api_url: window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Pending Shipments",
                },
                btnLoader : false,
                pendingDispatchs : [],
                detail : '',
                filter: {
                    from: new Date().toISOString().substr(0, 10),
                    to: new Date().toISOString().substr(0, 10),
                },
                orderNumber : "",
                loader : false,
                dispatcheds : [],
                trackingDetails: [],
            };
        },
        computed:{
            totalPendingShipment() {
                return this.pendingDispatchs.reduce((acc, current) => {
                    return acc + parseFloat(current.total_bill);
                    }, 0)
            },
            totalHandOver() {
                return this.dispatcheds.reduce((acc, current) => {
                    return acc + parseFloat(current.total_amount);
                    }, 0)
            }
        },
        created(){
            this.fetchPendingDispatchs({ from : null , to : null});
        },
        methods : {
            fetchTracking(id) {
                let vm = this;
                axios
                    .post(this.api_url + "inventory/products/orders/tracking", { id })
                    .then((response) => {
                        vm.trackingDetails = response.data.response
                    });
            },
            formatPrice(price) {
                const value = parseFloat(price).toFixed(2)
                var string = value.toString();
                return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            },
            scanOrder() {

                const scanned = this.pendingDispatchs.find((order) => {
                    return order.tracking_number === this.orderNumber;
                });

                if(scanned){
                    let vm = this;
                    vm.loader = true;
                    axios
                    .post(this.api_url + "inventory/products/orders/dispatched-scanned", scanned )
                    .then((response) => {
                        this.fetchPendingDispatchs({ from : null , to : null});
                        this.orderNumber = "";
                        vm.loader = false;
                        return swal({
                            title: "Success",
                            text: 'Order Dispatched',
                            icon: "success",
                            timer: 3000,
                        });
                    })
                }else{
                    vm.loader = false;
                    return swal({
                        title: "Error",
                        text: 'No Order Found',
                        icon: "error",
                        timer: 3000,
                    });
                }
            },
            filterFunction(){
                this.fetchPendingDispatchs( this.filter );
            },
            assignBarcode( detail ){
                this.detail = detail;
            },
            formatDate(date) {
                return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
            },
            fetchPendingDispatchs(data){
                let vm = this;
                axios
                .post(this.api_url + "inventory/products/orders/pending-dispatchs", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.pendingDispatchs = results.pendings;
                    vm.dispatcheds = results.dispatched;
                    vm.dataTable()
                })
                .catch((err) => this.fetchReturnOrders());
            },
            dataTable(){
                if ($.fn.DataTable.isDataTable("#order_table")) {
                    $('#order_table').DataTable().destroy();
                }
                if ($.fn.DataTable.isDataTable("#dispatched-order_table")) {
                    $('#dispatched-order_table').DataTable().destroy();
                }
                    setTimeout(function () {
                        $('#order_table').DataTable({
                            dom: "Bfrtip",
                            buttons: [{
                                extend: "excel",
                                title: 'Pending Shipments'
                                },
                            ],
                        })

                        $('#dispatched-order_table').DataTable({
                            dom: "Bfrtip",
                            buttons: [{
                                extend: "excel",
                                title: 'Handed-Over Shipments'
                                },
                            ],
                        })
                }, 300);
            }
        }
    }
</script>
