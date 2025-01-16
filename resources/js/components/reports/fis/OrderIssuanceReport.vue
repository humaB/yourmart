<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Order Issuance Report</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="submitFunction">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label for="date">From</label>
                                        <input type="date" name="from" class="form-control" v-model="filter.from" />
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="date">To</label>
                                        <input type="date" name="to" class="form-control" v-model="filter.to" />
                                    </div>
                                    <div class="col-md-4 form-group pt-4">
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
                            <table class="table table-bordered" id="order_issuance_table" v-else>
                                <thead>

                                    <tr>
                                        <th>SKU</th>
                                        <th>Product Name </th>
                                        <th>Quantity</th>
                                        <th>Purchase Price </th>
                                        <th>Purchase Cost</th>
                                        <th>Issuance Price</th>
                                        <th>Issuance Cost</th>
                                        <th>Returns Cost</th>
                                        <th>Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="item.id">
                                       <td>{{ item['sku']}}</td>
                                       <td>{{ item['name']}}</td>
                                       <td>{{ item['quantity']}}</td>
                                       <td>{{ item['purchase_rate']}}</td>
                                       <td>{{ formatPrice( item['purchase_cost'] )}}</td>
                                       <td>{{ item['issance_price']}}</td>
                                       <td>{{ formatPrice( item['issance_cost'] )}}</td>
                                       <td>{{ formatPrice( item['returned'] * item['issance_price'] ) }}</td>
                                       <td>{{ formatPrice( calculateProfit(item)) }}</td>
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
    name: 'OrderIssuanceReport',
    props: ['data', 'loader'],
    components: {
        BulletListLoader
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            filter: {
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            },
        }
    },
    methods: {
        calculateProfit(item) {
            const netSale = item['issance_cost'];
            const returnCost = item['returned'] * item['issance_price'];
            const purchaseCost = (item['quantity'] - item['returned']) * item['purchase_rate'];
            const profit = netSale - returnCost - purchaseCost;
            return profit;
        },
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
            this.$emit('orderIssuanceReportFilter', this.filter);
        },
        clearDataTable() {
            const table = $('#order_issuance_table').DataTable();
            table.destroy();
        },
    },
    watch: {
        data(newLedger) {
            setTimeout(() => {
                $('#order_issuance_table').DataTable({
                    "bSort": false,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Order Issuance Report',
                        }, 'csv', {
                            extend: 'excel',
                            title: 'Order Issuance Report',
                        }
                    ]
                });
            }, 300);
        }
    }
}
</script>
