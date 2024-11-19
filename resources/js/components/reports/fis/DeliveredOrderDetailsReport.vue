<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Delivered Orders Details</h5>
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
                            <table class="table table-bordered" id="delivered_order_details" v-else>
                                <thead>

                                    <tr>
                                        <th>Sr #</th>
                                        <th>Date</th>
                                        <th>Product Name </th>
                                        <th>Average Price </th>
                                        <th>Total Price Cost</th>
                                        <th>Sell Rate </th>
                                        <th>Quantity</th>
                                        <th>Total Sold Price</th>
                                        <th>Courier Charges</th>
                                        <th>Packaging Charges</th>
                                        <th>Advance + COD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ formatDate(item.created_at) }}</td>

                                        <td>{{ item.variation ? item.variation.product.title : '' }}</td>
                                        <td>{{ item.variation ? item.variation.avg_price : '0' }}</td>
                                        <td>{{ item.variation ? item.variation.avg_price * item.quantity : '0' }}</td>
                                        <td>{{ formatPrice(item.price) }}</td>

                                        <td>{{ item.quantity }}</td>

                                        <td>{{ formatPrice(item.quantity * item.price) }}</td>
                                        <td>{{ formatPrice(item.courier_cost) }}</td>
                                        <td>{{ formatPrice(item.packaging_cost) }}</td>
                                        <td>{{ formatPrice(item.sell_price) }}</td>
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
    name: 'DeliveredOrderDetailsReport',
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
            this.$emit('deliveredOrderFilter', this.filter);
        },
        clearDataTable() {
            const table = $('#delivered_order_details').DataTable();
            table.destroy();
        },
    },
    watch: {
        data(newLedger) {
            setTimeout(() => {
                $('#delivered_order_details').DataTable({
                    "bSort": false,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Delivered Order Details',
                        }, 'csv', {
                            extend: 'excel',
                            title: 'Delivered Order Details',
                        }
                    ]
                });
            }, 300);
        }
    }
}
</script>
