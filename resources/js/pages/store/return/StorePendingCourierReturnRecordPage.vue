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
                    <div class="col-md-12 mt-3" v-if="loader">
                        <bullet-list-loader :width="250"> </bullet-list-loader>
                    </div>
                    <div class="col-12" v-else>
                      <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>Order #</th>
                                        <th>SRN #</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Created Date</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in inwards" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.srn ? item.srn.order.tracking_number : '' }}</td>
                                        <td>{{ item.srn_id }}</td>
                                        <td>{{ item.product ? item.product.title : '' }}</td>
                                        <td>{{ item.quantity}}</td>
                                        <td>{{ formatDate(item.created_at) }}</td>

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
        <form method="POST" :action="public_url+'inventory/products/store/inward-record/pdf'" target="_blank" ref="summaryForm">
            <input type="hidden" name="_token" :value="csrf" >
            <input type="hidden" name="id" :value="pid" >
        </form>

    </div>
</template>
<script>
  import TableHeader from "../../../components/table/TableHeaderComponent.vue";
  import { BulletListLoader } from "vue-content-loader";
  import moment from "moment";

    export default {
        name : 'StoreInWardRecordPage',
        components: {
            TableHeader,
            BulletListLoader
        },
        data() {
            return {
                public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
                api_url: window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Courier Return Record",
                },
                inwards : [],
                csrf : '',
                pid : '',
                filter: {
                    from: new Date().toISOString().substr(0, 10),
                    to: new Date().toISOString().substr(0, 10),
                    courier : ""
                },
                loader : true
            };
        },
        created(){
            // CSRF token value assigning
            this.csrf = $('meta[name=csrf-token]').attr('content');
            this.fetchInwards({ from : null , to : null});
        },
        methods : {
            filterFunction(){
                this.fetchInwards( this.filter );
            },
            printPurchaseOrder( id ){
                return;
                this.pid = id;
                const form = this.$refs.summaryForm;
                setTimeout(()=>{
                    form.submit();
                },500)
            },
            formatDate(date) {
                return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
            },
            fetchInwards( data ){
                let vm = this;
                vm.loader = true;
                axios
                .post(this.api_url + "inventory/products/store/product-returned/records", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.inwards = results;

                    setTimeout(()=>{
                        this.dataTable()
                    },300);

                    vm.loader = false;
                })
            },
            dataTable(){
                $('#table').DataTable({
                    dom: "Bfrtip",
                    buttons: [{
                        extend: "excel",
                        title: 'Returns Details'
                        },
                    ],
                })
            }
        }
    }
</script>
