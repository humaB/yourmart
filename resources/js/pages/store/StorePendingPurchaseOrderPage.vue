<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
              <div class="card card-primary">
                <TableHeader :tableHeader="tableHeader" />

                <div class="card-body">
                  <!-- Table -->
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>PO #</th>
                                        <th>Supplier</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in purchaseOrders" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.supplier.full_name }}</td>
                                        <td>{{ formatDate(item.created_at) }}</td>
                                        <td>
                                            <button class="btn btn-primary" data-toggle="modal" data-target="#purchaseOrderDetail" @click="fetchDetail(item)"> Receive Product</button>
                                            <button class="btn btn-dark" @click="printPurchaseOrder( item.id )"><i class="fa fa-print"></i> Print</button>
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

        <!-- Summary PRINT -->
        <form method="POST" :action="public_url+'inventory/products/purchase-orders/pdf'" target="_blank" ref="summaryForm">
            <input type="hidden" name="_token" :value="csrf" >
            <input type="hidden" name="id" :value="pid" >
        </form>

        <StoreProductInwardPopup
            :loader="btnLoader"
            :details="details"
            @generatePass="generatePass( $event )"
        />

    </div>
</template>
<script>

import StoreProductInwardPopup from "../../components/store/StoreProductInwardPopup.vue";
    import TableHeader from "../../components/table/TableHeaderComponent.vue";

  import moment from "moment";
    export default {
        name : 'StorePendingPurchaseOrderPage',
        components: {
            TableHeader,
            StoreProductInwardPopup
        },
        data() {
            return {
                public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
                api_url: window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Pending Purchase Order",
                },
                products : [],
                suppliers : [],
                btnLoader : false,
                purchaseOrders : [],
                csrf : '',
                pid : '',
                details : {}
            };
        },
        created(){
            // CSRF token value assigning
            this.csrf = $('meta[name=csrf-token]').attr('content');
            this.fetchPurchaseOrders();
        },
        methods : {
            fetchDetail( data ){
                this.details = data;
            },
            printPurchaseOrder( id ){
                this.pid = id;
                const form = this.$refs.summaryForm;
                setTimeout(()=>{
                    form.submit();
                },500)
            },
            formatDate(date) {
                return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
            },
            fetchPurchaseOrders(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/store/pending-purchase-orders")
                .then((response) => {
                    const results = response.data.response;
                    vm.purchaseOrders = results;
                })
                .catch((err) => this.fetchPurchaseOrders());
            },
            generatePass(data){
                console.log(data);

                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/store/product-inward", data)
                .then((response) => {
                    vm.btnLoader = false;
                    this.fetchPurchaseOrders();
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
            }
        }
    }
</script>
