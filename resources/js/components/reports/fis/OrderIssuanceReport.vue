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

                            <div class="row" style="margin-left: -10px">
                                <!-- cards -->
                                    <table style="table-layout: fixed; width: 100%;">
                                        <tr>
                                            <td style="padding : 10px">
                                                <div class="card card-statistic-1">
                                                    <div class="card-icon l-bg-cyan">
                                                        <i class="fa fa-shopping-bag"></i>
                                                    </div>
                                                    <div class="card-wrap">
                                                        <div class="padding-20">
                                                            <div class="text-right">
                                                                <h4 class="font-light mb-0">
                                                                    <i class="ti-arrow-up text-success"></i>
                                                                    {{ formatPrice(totalIssuanceQuantity) }}
                                                                </h4>
                                                                <span class="text-muted">Products</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="padding : 10px">
                                                <div class="card card-statistic-1">
                                                    <div class="card-icon l-bg-orange">
                                                        <i class="fas fa-clock"></i>
                                                    </div>
                                                    <div class="card-wrap">
                                                        <div class="padding-20">
                                                            <div class="text-right">
                                                                <h4 class="font-light mb-0">
                                                                    <i class="ti-arrow-up text-success"></i>
                                                                    {{ formatPrice(totalIssuancePurchased) }}
                                                                </h4>
                                                                <span class="text-muted">Purchased</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="padding : 10px">
                                                <div class="card card-statistic-1">
                                                    <div class="card-icon l-bg-green">
                                                        <i class="fas fa-boxes"></i>
                                                    </div>
                                                    <div class="card-wrap">
                                                        <div class="padding-20">
                                                            <div class="text-right">
                                                                <h4 class="font-light mb-0">
                                                                    <i class="ti-arrow-up text-success"></i>
                                                                    {{  formatPrice(totalSellingQuantity.toFixed(0)) }}
                                                                </h4>
                                                                <span class="text-muted">Selling</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="padding : 10px">
                                                <div class="card card-statistic-1">
                                                    <div class="card-icon l-bg-purple">
                                                        <i class="fas fa-undo"></i>
                                                    </div>
                                                    <div class="card-wrap">
                                                        <div class="padding-20">
                                                            <div class="text-right">
                                                                <h4 class="font-light mb-0">
                                                                    <i class="ti-arrow-up text-success"></i>
                                                                    {{ totalReturnQuantity }}
                                                                </h4>
                                                                <span class="text-muted">Returns</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td style="padding : 10px">
                                                <div class="card card-statistic-1">
                                                    <div class="card-icon l-bg-cyan">
                                                        <i class="fas fa-credit-card"></i>
                                                    </div>
                                                    <div class="card-wrap">
                                                        <div class="padding-20">
                                                            <div class="text-right">
                                                                <h4 class="font-light mb-0">
                                                                    <i class="ti-arrow-up text-success"></i>
                                                                    {{ formatPrice(totalReturnAmount) }}
                                                                </h4>
                                                                <span class="text-muted">Return Amount</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="row" style="margin-left: -10px">
                                    <!-- cards -->
                                        <table style="table-layout: fixed; width: 100%;">
                                            <tr>
                                                <td style="padding : 10px">
                                                    <div class="card card-statistic-1">
                                                        <div class="card-icon l-bg-cyan">
                                                            <i class="fa fa-shopping-bag"></i>
                                                        </div>
                                                        <div class="card-wrap">
                                                            <div class="padding-20">
                                                                <div class="text-right">
                                                                    <h4 class="font-light mb-0">
                                                                        <i class="ti-arrow-up text-success"></i>
                                                                        {{ totalIssuanceQuantity - totalReturnQuantity }}
                                                                    </h4>
                                                                    <span class="text-muted">Net Quantity</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="padding : 10px">
                                                    <div class="card card-statistic-1">
                                                        <div class="card-icon l-bg-orange">
                                                            <i class="fas fa-clock"></i>
                                                        </div>
                                                        <div class="card-wrap">
                                                            <div class="padding-20">
                                                                <div class="text-right">
                                                                    <h4 class="font-light mb-0">
                                                                        <i class="ti-arrow-up text-success"></i>
                                                                        {{ formatPrice( netBuyingCost ) }}
                                                                    </h4>
                                                                    <span class="text-muted">Net Buying Cost</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="padding : 10px">
                                                    <div class="card card-statistic-1">
                                                        <div class="card-icon l-bg-green">
                                                            <i class="fas fa-boxes"></i>
                                                        </div>
                                                        <div class="card-wrap">
                                                            <div class="padding-20">
                                                                <div class="text-right">
                                                                    <h4 class="font-light mb-0">
                                                                        <i class="ti-arrow-up text-success"></i>
                                                                        {{  formatPrice(netSales.toFixed(0)) }}
                                                                    </h4>
                                                                    <span class="text-muted">Net Sales</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="padding : 10px">
                                                    <div class="card card-statistic-1">
                                                        <div class="card-icon l-bg-purple">
                                                            <i class="fas fa-credit-card"></i>
                                                        </div>
                                                        <div class="card-wrap">
                                                            <div class="padding-20">
                                                                <div class="text-right">
                                                                    <h4 class="font-light mb-0">
                                                                        <i class="ti-arrow-up text-success"></i>
                                                                        {{ formatPrice(profit) }}
                                                                    </h4>
                                                                    <span class="text-muted">Profit</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>


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
                                        <th>Selling Price</th>
                                        <th>Sales</th>
                                        <th>Return Quantity</th>
                                        <th>Return Amount</th>
                                        <th>Net Quantity</th>
                                        <th>Net Buying Cost</th>
                                        <th>Net Sales</th>
                                        <th>Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="item.id">
                                       <td>{{ item['sku']}}</td>
                                       <td>{{ item['name']}}</td>
                                       <td>{{ item['quantity']}}</td>
                                       <td>{{ item['purchase_rate']}}</td>
                                       <td>{{ formatPrice( item['purchase_rate'] * item['quantity'] )}}</td>
                                       <td>{{ item['issance_price']}}</td>
                                       <td>{{ formatPrice( item['quantity'] * item['issance_price'] )}}</td>
                                       <td>{{ item['returned'] }}</td>
                                       <td>{{ formatPrice( item['returned'] * item['issance_price'] ) }}</td>
                                       <td>{{ item['quantity'] - item['returned'] }}</td>
                                       <td>{{ formatPrice( (item['quantity'] - item['returned']) * item['purchase_rate'] ) }}</td>
                                       <td>{{ formatPrice( (item['quantity'] - item['returned']) * item['issance_price'] ) }}</td>
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
    computed: {
        totalIssuanceQuantity() {
            return Object.values(this.data).reduce((total, item) => {
                return total + item.quantity;
            }, 0);
        },
        totalIssuancePurchased() {
            return Object.values(this.data).reduce((total, item) => {
                return total + (parseFloat(item.quantity) * parseFloat(item.purchase_rate));
            }, 0);
        },
        totalReturnQuantity() {
            return Object.values(this.data).reduce((total, item) => {
                return total + item.returned;
            }, 0);
        },
        totalReturnAmount() {
            return Object.values(this.data).reduce((total, item) => {
                return total + (item.returned * item.issance_price);
            }, 0);
        },
        totalSellingQuantity() {
            return Object.values(this.data).reduce((total, item) => {
                return total + (parseFloat(item.quantity) * parseFloat(item.issance_price));
            }, 0);
        },
        netBuyingCost() {
            return Object.values(this.data).reduce((total, item) => {
                return total + ((parseFloat(item.quantity) - parseFloat(item.returned)) * parseFloat(item.purchase_rate));
            }, 0);
        },
        netSales() {
            return Object.values(this.data).reduce((total, item) => {
                return total + ((parseFloat(item.quantity) - parseFloat(item.returned)) * parseFloat(item.issance_price));
            }, 0);
        },
        profit() {
            let totalProfit = 0;
            Object.values(this.data).forEach(item => {
                const netQuantity = (item['quantity'] - item['returned']);
                const netSale = item['issance_price'] * netQuantity;
                const netPurchase = netQuantity * item['purchase_rate'];
                totalProfit += netSale - netPurchase;
            });
            return totalProfit;
        }
    },
    methods: {
        calculateProfit(item) {
            const netQuantity = (item['quantity'] - item['returned']);
            const netSale = item['issance_price'] * netQuantity;
            const netPurchase = netQuantity * item['purchase_rate'];
            const profit = netSale - netPurchase;
            return profit;
        },
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : '';
        },
        formatPrice: function formatPrice(price) {
            const value = parseFloat(price)
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
