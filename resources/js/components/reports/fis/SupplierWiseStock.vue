<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Supplier Wise Stock</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="submitFunction">
                                <div class="row">

                                    <div class="col-md-12 form-group pt-4">
                                        <button class="btn btn-block btn-primary">Fetch Record</button>
                                    </div>


                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <div class="card-body table-responsive" v-if="loader">
                                <bullet-list-loader :width="250">
                                </bullet-list-loader>
                            </div>
                            <table class="table table-bordered" id="supplier_wise_stock" v-else>
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>Supplier</th>
                                        <th>Product</th>
                                        <th>Quantity #</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.supplier.full_name }}</td>
                                        <td>{{ item.product.title }}</td>
                                        <td>{{ item.quantity }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import moment from 'moment';
import { BulletListLoader } from 'vue-content-loader';

export default {
    name: 'SupplierWiseStock',
    props: ['data', 'loader'],
    components: {
        BulletListLoader
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
        }
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
            this.$emit('supplierStockFilter', this.filter);
        },
        clearDataTable() {
            const table = $('#supplier_wise_stock').DataTable();
            table.destroy();
        },
    },
    watch: {
        data(newLedger) {
            setTimeout(() => {
                $('#supplier_wise_stock').DataTable({
                    "bSort": false,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Dropshipper List',
                        }, 'csv', {
                            extend: 'excel',
                            title: 'Dropshipper List',
                        }
                    ]
                });
            }, 300);
        }
    }
}
</script>
