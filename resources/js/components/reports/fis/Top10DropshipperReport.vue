<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Top 10 Dropshippers</h5>
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
                            <table class="table table-bordered" id="top-10-dropshippers" v-else>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Stores</th>
                                        <th>Orders</th>
                                        <th>Returned Orders</th>
                                        <th>Success Rate</th>
                                        <th>Sales</th>
                                        <th>COGS</th>
                                        <th>Packing & Labeling</th>
                                        <th>Profit</th>
                                        <th>Payable</th>
                                        <th>Withdraw</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td><a href="#" @click="fetchDropshipperDetails(item.dropshipper.id)"
                                                data-toggle="modal" data-target="#dropShipperDetail">{{ item.name }}</a>
                                        </td>
                                        <td>{{ item.dropshipper.shops?.length || 0 }}</td>
                                        <td>{{ item.total_orders }}</td>
                                        <td>{{ item.total_returns }}</td>
                                        <td class="align-middle" width="30%">
                                            <div class="progress-text text-right">
                                                {{ calculateHealth(item) }}%
                                            </div>
                                            <div class="progress" data-height="2">
                                                <div :class="['progress-bar', calculateHealth(item) > 90 ? 'bg-success' : 'bg-primary']"
                                                    :style="{ width: calculateHealth(item) + '%' }">
                                                </div>
                                            </div>
                                        </td>

                                        <td>{{ formatPrice(calculateDeliveredSales(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateProductCost(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateTotalCost(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(calculateProfit(item.delivered_orders)) }}</td>
                                        <td>{{ formatPrice(item.dropshipper.total_payable) }}</td>
                                        <td>{{ formatPrice(item.dropshipper.total_paid) }}</td>
                                        <td>{{ formatPrice(item.dropshipper.remaining_amount) }}</td>
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
            this.$emit('top10DropshipperFilter', this.filter);
        },
        clearDataTable() {
            const table = $('#top-10-dropshippers').DataTable();
            table.destroy();
        },
        calculateHealth(item) {
            const deliveredCount = item.total_orders; // Count of delivered orders
            const returnedCount = item.total_returns;   // Count of returned orders

            // You can now use these counts for further logic, e.g., calculating account health
            const totalOrders = deliveredCount + returnedCount;
            let accountHealth = 0;
            if (totalOrders > 0) {
                accountHealth = (deliveredCount / totalOrders) * 100;
            }
            return Math.round(accountHealth);
        },
        calculateDeliveredSales(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.selling_price) + parseFloat(order.advance_amount), 0);
        },
        calculateProductCost(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.total_bill) - parseFloat(order.courier_service_price) - parseFloat(order.packaging_price), 0);
        },
        calculateTotalCost(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + parseFloat(order.courier_service_price) + parseFloat(order.packaging_price), 0);
        },
        calculateProfit(deliveredOrders) {
            return deliveredOrders.reduce((sum, order) => sum + (parseFloat(order.selling_price) + parseFloat(order.advance_amount)) - (parseFloat(order.total_bill)), 0);
        },
    },
    watch: {
        data(newLedger) {
            setTimeout(() => {
                $('#top-10-dropshippers').DataTable({
                    "bSort": false,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Top 10 Dropshippers',
                        }, 'csv', {
                            extend: 'excel',
                            title: 'Top 10 Dropshippers',
                        }
                    ]
                });
            }, 300);
        }
    }
}
</script>
