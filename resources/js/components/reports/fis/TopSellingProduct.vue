<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Top Selling Products</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="submitFunction">
                                <div class="row">
                                    <div class="col-md-12 form-group pt-4">
                                        <button class="btn btn-block btn-primary">Fetch</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <div class="card-body table-responsive" v-if="loader">
                                <bullet-list-loader :width="250">
                                </bullet-list-loader>
                            </div>
                            <table class="table table-bordered" id="top-selling-products" v-else>
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
                                    <tr v-for="(item, index) in data" :key="index">
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
        </div>

    </div>
</template>
<script>
import moment from 'moment';
import { BulletListLoader } from 'vue-content-loader';

export default {
    name: 'InventoryGoodReturnReport',
    props: ['data', 'loader', 'products'],
    components: {
        BulletListLoader
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            filter: {
                product: { code: 0, label: 'Select from the following' },
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            },
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
            this.$emit('topSellingProductsFilter', this.filter);
        },
        clearDataTable() {
            const table = $('#top-selling-products').DataTable();
            table.destroy();
        },
    },
    watch: {
        data(newLedger) {
            setTimeout(() => {
                $('#top-selling-products').DataTable({
                    "bSort": false,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Top Selling Products',
                        }, 'csv', {
                            extend: 'excel',
                            title: 'Top Selling Products',
                        }
                    ]
                });
            }, 300);
        }
    }
}
</script>
