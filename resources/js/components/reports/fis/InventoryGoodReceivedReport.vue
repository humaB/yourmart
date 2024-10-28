<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Inventory Good Received Register</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="submitFunction">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">Select Product</label>
                                        <v-select :options="products" v-model="filter.product"></v-select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="">PO #</label>
                                        <input type="text" class="form-control" v-model="filter.po">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="date">From</label>
                                        <input type="date" name="from" class="form-control" v-model="filter.from" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="date">To</label>
                                        <input type="date" name="to" class="form-control" v-model="filter.to" />
                                    </div>
                                    <div class="col-md-2 form-group pt-4">
                                        <button class="btn btn-block btn-primary">Filter</button>
                                    </div>


                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <div class="card-body table-responsive" v-if="loader">
                                <bullet-list-loader :width="250">
                                </bullet-list-loader>
                            </div>
                            <table class="table table-bordered" id="inventory_good_received" v-else>
                                <thead>

                                    <tr>
                                        <th>Sr #</th>
                                        <th>Date</th>
                                        <th>GRN #</th>
                                        <th>PO #</th>
                                        <th>Supplier</th>
                                        <th>Product Name </th>
                                        <th>Rate </th>
                                        <th>Received Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td class="h5">{{ formatDate(item.created_at) }}</td>
                                        <td class="h5">
                                            <a @click="printGRNDetails(item.grn_id)" href="#">{{ item.grn_id }}</a>
                                        </td>
                                        <td class="h5">
                                            <a @click="printPurchaseOrder(item.grn.po_id)" href="#">
                                                {{ item.grn.po_id }}
                                            </a>
                                        </td>
                                        <td class="h5">
                                            {{ item.grn && item.grn.purchase_order && item.grn.purchase_order.supplier ?
                                                item.grn.purchase_order.supplier.full_name : '-' }}
                                        </td>

                                        <td class="h5">{{ item.product ? item.product.title : '' }}</td>
                                        <td class="h5">{{ formatPrice(item.price) }}</td>

                                        <td class="h5">{{ item.quantity }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

          <!-- GRN PRINT -->
     <form method="POST" :action="public_url+'/inventory/products/store/inward-record/pdf'" target="_blank" ref="requestForm">
        <input type="hidden" name="_token" :value="csrf" >
        <input type="hidden" name="id" :value="grnID" >
      </form>

    <!-- PO PRINT -->
    <form method="POST" :action="public_url+'/inventory/products/purchase-orders/pdf'" target="_blank" ref="summaryForm">
      <input type="hidden" name="_token" :value="csrf" >
      <input type="hidden" name="id" :value="pid" >
    </form>

    </div>
</template>
<script>
import moment from 'moment';
import { BulletListLoader } from 'vue-content-loader';

export default {
    name: 'InventoryGoodReceivedReport',
    props: ['data', 'loader', 'products'],
    components: {
        BulletListLoader
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            filter: {
                product: { code: 0, label: 'Select from the following' },
                po: '',
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            },
            grnID : '',
            pid : ''
        }
    },
    created() {
      this.csrf = $('meta[name=csrf-token]').attr('content');
    },
    methods: {
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : '';
        },
        formatPrice: function formatPrice(price) {
            const value = parseFloat(price).toFixed(2)
            var string = value.toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        submitFunction() {
            this.clearDataTable()
            this.$emit('inventoryGoodReceivedilter', this.filter);
        },
        clearDataTable() {
            const table = $('#inventory_good_received').DataTable();
            table.destroy();
        },
        printGRNDetails(id) {
            this.grnID = id;
            const form = this.$refs.requestForm;
            setTimeout(() => {
                form.submit();
            }, 500)
        },
        printPurchaseOrder(id) {
            this.pid = id;
            const form = this.$refs.summaryForm;
            setTimeout(() => {
                form.submit();
            }, 500)
        },
    },
    watch: {
        data(newLedger) {
            setTimeout(() => {
                $('#inventory_good_received').DataTable({
                    "bSort": false,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Inventory Good Received',
                        }, 'csv', {
                            extend: 'excel',
                            title: 'Inventory Good Received',
                        }
                    ]
                });
            }, 300);
        }
    }
}
</script>
