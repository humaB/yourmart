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
                            <table class="table table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>Order #</th>
                                        <th>SRN #</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in inwards" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.srn ? item.srn.order.tracking_number : '' }}</td>
                                        <td>{{ item.srn_id }}</td>
                                        <td>{{ item.variation ? item.variation.product.title : '' }}</td>
                                        <td>{{ item.quantity}}</td>
                                        <td>{{ formatDate(item.created_at) }}</td>
                                        <td>
                                            <button class="btn btn-dark" @click="printPurchaseOrder( item.srn_id )"><i class="fa fa-print"></i> Print</button>
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
        <form method="POST" :action="public_url+'inventory/products/store/inward-record/pdf'" target="_blank" ref="summaryForm">
            <input type="hidden" name="_token" :value="csrf" >
            <input type="hidden" name="id" :value="pid" >
        </form>

    </div>
</template>
<script>
    import TableHeader from "../../../components/table/TableHeaderComponent.vue";

  import moment from "moment";
    export default {
        name : 'StoreInWardRecordPage',
        components: {
            TableHeader,
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
            };
        },
        created(){
            // CSRF token value assigning
            this.csrf = $('meta[name=csrf-token]').attr('content');
            this.fetchInwards();
        },
        methods : {
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
            fetchInwards(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/store/product-returned")
                .then((response) => {
                    const results = response.data.response;
                    vm.inwards = results;

                    setTimeout(()=>{
                        this.dataTable()
                    },300)
                })
                .catch((err) => this.fetchInwards());
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
