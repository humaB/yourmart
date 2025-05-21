<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
              <div class="card card-primary">
                <TableHeader :tableHeader="tableHeader" />

                <div class="card-body">
                  <!-- Table -->
                  <div class="row">
                    <form @submit.prevent="filterFunction" class="col-md-12 row mb-3">
                        <div class="col-md-3">
                            <label>Courier</label>
                           <select name="" id="" v-model="filter.courier" class="form-control">
                                <option value="">Select from the following</option>
                                <option value="1">Leopard</option>
                                <option value="2">PostEx</option>
                           </select>
                        </div>
                        <div class="col-md-3">
                            <label>From</label>
                            <input type="date" class="form-control" v-model="filter.from">
                        </div>
                        <div class="col-md-3">
                            <label>To</label>
                            <input type="date" class="form-control" v-model="filter.to">
                        </div>
                        <div class="col-md-3">
                            <label>Action</label>
                            <button class="btn btn-primary w-100"> Filter</button>
                        </div>
                    </form>
                    <div class="col-12">
                      <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered" id="order_table">
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>Order #</th>
                                        <th>Courier</th>
                                        <th>Tracking Number</th>
                                        <th>Dropshipper</th>
                                        <th>Items</th>
                                        <th>Amount</th>
                                        <th>Order Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in pendingReturns" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            {{ item.shop && item.shop.store_name ? item.shop.store_name.substring(0, 3) + '-' + item.order_no : item.order_no }}
                                        </td>
                                        <td>{{ item?.courier?.courier_name }}</td>
                                        <td>{{ item.tracking_number }}</td>
                                        <td>{{ item.user.name }}</td>
                                        <td>
                                            <ul>
                                                <li v-for="product in item.items" :key="'items-'+item.id">{{ product?.variation?.product?.title }}</li>
                                            </ul>
                                        </td>
                                        <td>{{ item.total_bill }}</td>
                                        <td>{{ formatDate(item.created_at) }}</td>
                                        <td>
                                            <button class="btn btn-primary" data-toggle="modal" data-target="#returnProduct" @click="assignBarcode(item)"> Receive Product</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                  </div>
                  <!-- END TABLE -->
                </div>
              </div>
            </div>
          </div>
          </div>


        <StoreProductReturnPopup
            :loader="btnLoader"
            :detail="detail"
            @addToStock="addToStock( $event )"
        />

    </div>
</template>
<script>

import StoreProductReturnPopup from "../../../components/store/return/StoreProductReturnPopup.vue";
import TableHeader from "../../../components/table/TableHeaderComponent.vue";

  import moment from "moment";
    export default {
        name : 'StorePendingCourierReturnPage',
        components: {
            TableHeader,
            StoreProductReturnPopup
        },
        data() {
            return {
                public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
                api_url: window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Pending Returns",
                },
                btnLoader : false,
                pendingReturns : [],
                detail : '',
                filter: {
                    from: new Date().toISOString().substr(0, 10),
                    to: new Date().toISOString().substr(0, 10),
                    courier : ""
                },
            };
        },
        created(){
            this.fetchReturnOrders({ from : null , to : null});
        },
        methods : {
            filterFunction(){
                this.fetchReturnOrders( this.filter );
            },
            assignBarcode( detail ){
                this.detail = detail;
            },
            formatDate(date) {
                return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
            },
            fetchReturnOrders( data ){
                let vm = this;
                axios
                .post(this.api_url + "inventory/products/store/pending-returns", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.pendingReturns = results;
                    vm.dataTable()
                })
                .catch((err) => this.fetchReturnOrders());
            },
            addToStock(data){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/store/product-returned", data)
                .then((response) => {

                    $("#returnProduct").modal('hide');
                    this.$emit('saved', true)
                    vm.btnLoader = false;
                    this.fetchReturnOrders();
                    return swal({
                        title: "Success",
                        text: 'In ward Created Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
            },
            dataTable(){
                if ($.fn.DataTable.isDataTable("#order_table")) {
                    $('#order_table').DataTable().destroy();
                }
                    setTimeout(function () {
                        $('#order_table').DataTable({
                        dom: "Bfrtip",
                        buttons: [{
                            extend: "excel",
                            title: 'Returns Details'
                            },
                        ],
                    })
                }, 300);
            }
        }
    }
</script>
