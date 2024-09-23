<template>
    <div>

        <!-- Modal -->
        <div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="confirmation" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12">
                        <h5>Are you sure you want to {{ action.decision }} this PO?</h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="confimation()" v-if="!decisionLoader">Yes, {{ action.decision }}</button>
                    <button type="button" class="btn btn-primary btn-progress disabled" v-else>Yes, {{ action.decision }}</button>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
            </div>
        </div>

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
                                        <th>Total Amount</th>
                                        <th>Remaining Amount</th>
                                        <th>Created Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in purchaseOrders" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.supplier.full_name }}</td>
                                        <td>{{ item.total_amount }}</td>
                                        <td>{{ item.remaining_amount }}</td>
                                        <td>{{ formatDate(item.created_at) }}</td>
                                        <td>
                                            <span class="badge badge-warning text-dark" v-if="item.status == 0">Pending</span>
                                            <span class="badge badge-success" v-if="item.status == 1">Approved</span>
                                            <span class="badge badge-danger"  v-if="item.status == 2">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-success" data-toggle="modal" data-target="#confirmation" @click="decision(item.id, 'Approve')" v-if="item.status == 0">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <button class="btn btn-danger" data-toggle="modal" data-target="#confirmation" @click="decision(item.id, 'Reject')" v-if="item.status == 0">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <button class="btn btn-dark" @click="printPurchaseOrder( item.id )"><i class="fa fa-print"></i></button>
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

          <CreateNewPurchaseOrder
            :products="products"
            :suppliers="suppliers"
            :loader="btnLoader"
            @addPO="addPO($event)"
            @searchProduct="searchProduct($event)"
          />



        <!-- Summary PRINT -->
        <form method="POST" :action="public_url+'inventory/products/purchase-orders/pdf'" target="_blank" ref="summaryForm">
            <input type="hidden" name="_token" :value="csrf" >
            <input type="hidden" name="id" :value="pid" >
        </form>

    </div>
</template>
<script>
  import TableHeader from "../../../components/table/TableHeaderComponent.vue";
  import CreateNewPurchaseOrder from "../../../components/inventory/purchase_order/CreateNewPurchaseOrder.vue";
  import moment from "moment";
    export default {
        name : 'AdminPurchaseOrderPage',
        components: {
            TableHeader,
            CreateNewPurchaseOrder
        },
        data() {
            return {
                public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
                api_url: window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Purchase Order",
                    link: "#",
                    target: "#createNewPurchaseOrder"
                },
                products : [],
                suppliers : [],
                btnLoader : false,
                purchaseOrders : [],
                csrf : '',
                pid : '',
                action : {
                    id : '',
                    decision : ''
                },
                decisionLoader : false
            };
        },
        created(){
            // CSRF token value assigning
            this.csrf = $('meta[name=csrf-token]').attr('content');
            this.fetchPurchaseOrders();
            this.fetchSuppilers();
        },
        methods : {
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
            searchProduct( data ) {
                let vm = this;
                axios
                    .post(this.api_url + "inventory/products/drop-down", data)
                    .then((response) => {
                        const results = response.data.response;
                        vm.products = results;
                    });
            },
            fetchSuppilers(){
                let vm = this;
                axios
                .get(this.api_url + "suppliers/drop-down")
                .then((response) => {
                    const results = response.data.response;
                    vm.suppliers = results;
                })
                .catch((err) => this.fetchSuppilers());
            },
            fetchPurchaseOrders(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/purchase-orders")
                .then((response) => {
                    const results = response.data.response;
                    vm.purchaseOrders = results;
                })
                .catch((err) => this.fetchPurchaseOrders());
            },
            addPO( data ){
                let vm = this;
                vm.btnLoader = true;
                axios
                .post(this.api_url + "inventory/products/purchase-orders", data)
                .then((response) => {
                    vm.btnLoader = false;
                    vm.$emit('created', true);
                    vm.fetchPurchaseOrders();
                    return swal({
                        title: "Success",
                        text: 'Request sent for approval',
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
            decision( id , decision ){
                this.action = {
                    id : id,
                    decision : decision
                }
            },
            confimation(){

                this.decisionLoader = true;
                axios.post(this.api_url + "inventory/products/purchase-orders/decisions", this.action)
                .then((response) => {
                    this.decisionLoader = false;

                    this.fetchPurchaseOrders();
                    $("#confirmation").modal('hide');

                    return swal({
                        title: "Success",
                        text: 'Decision Made Successfully',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    this.decisionLoader = false;
                });
            }
        }
    }
</script>
