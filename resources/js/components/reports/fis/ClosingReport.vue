<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Daily Business Report</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="submitFunction">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="date" name="" id="" class="form-control" v-model="filter.from">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" name="" id="" class="form-control" v-model="filter.to">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <button class="btn btn-block btn-primary">Fetch</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="row col-md-12">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="card card-statistic-1">
                                    <div class="card-icon l-bg-purple">
                                        <i class="fas fa-shopping-basket"></i> <!-- Updated Icon -->
                                    </div>
                                    <div class="card-wrap">
                                        <div class="padding-20">
                                            <div class="text-right">
                                                <h3 class="font-light mb-0">
                                                    <i class="ti-arrow-up text-success"></i> {{ total.totalOrders }}
                                                </h3>
                                                <span class="text-muted">Total Orders</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="card card-statistic-1">
                                    <div class="card-icon l-bg-green">
                                        <i class="fas fa-chart-bar"></i> <!-- Updated Icon -->
                                    </div>
                                    <div class="card-wrap">
                                        <div class="padding-20">
                                            <div class="text-right">
                                                <h3 class="font-light mb-0">
                                                    <i class="ti-arrow-up text-success"></i> {{ total.totalSales }}
                                                </h3>
                                                <span class="text-muted">Total Sales</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="card card-statistic-1">
                                    <div class="card-icon l-bg-cyan">
                                        <i class="fas fa-calendar-day"></i> <!-- Updated Icon -->
                                    </div>
                                    <div class="card-wrap">
                                        <div class="padding-20">
                                            <div class="text-right">
                                                <h3 class="font-light mb-0">
                                                    <i class="ti-arrow-up text-success"></i> {{ (parseFloat(total.totalOrders) / dateDiffInDays(filter.from, filter.to) ).toFixed(0) }}
                                                </h3>
                                                <span class="text-muted">Avg Per Day Order</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="card card-statistic-1">
                                    <div class="card-icon l-bg-orange">
                                        <i class="fas fa-money-bill-wave"></i> <!-- Updated Icon -->
                                    </div>
                                    <div class="card-wrap">
                                        <div class="padding-20">
                                            <div class="text-right">
                                                <h3 class="font-light mb-0">
                                                    <i class="ti-arrow-up text-success"></i> {{ formatPrice(total.totalSales / dateDiffInDays(filter.from, filter.to) ) }}
                                                </h3>
                                                <span class="text-muted">Avg Per Day Sale</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card-body table-responsive" v-if="loader">
                                <bullet-list-loader :width="250">
                                </bullet-list-loader>
                            </div>


                            <table class="table table-bordered" id="closing-report" v-else>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>PostEx</th>
                                        <th>Leopards</th>
                                        <th>Daraz</th>
                                        <th>Cash</th>
                                        <th>Total Orders</th>
                                        <th>Sales</th>
                                        <th>Return PostEx</th>
                                        <th>Return Leopard</th>
                                        <th>Total Returns</th>
                                        <th>Tickets</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.date }}</td>
                                        <td>{{ item.postEx }}</td>
                                        <td>{{ item.leopards }}</td>
                                        <td>{{ item.daraz }}</td>
                                        <td>{{ item.cash }}</td>
                                        <td>{{ item.totalOrders }}</td>
                                        <td>{{ formatPrice(item.totalSales) }}</td>
                                        <td>{{ item.postExReturns }}</td>
                                        <td>{{ item.leopardReturns }}</td>
                                        <td>{{ item.totalReturns }}</td>
                                        <td>{{ item.tickets }}</td>
                                    </tr>


                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td></td>
                                        <td>Total</td>
                                        <td>{{ total.postEx }}</td>
                                        <td>{{ total.leopards }}</td>
                                        <td>{{ total.daraz }}</td>
                                        <td>{{ total.cash }}</td>
                                        <td>{{ total.totalOrders }}</td>
                                        <td>{{ formatPrice(total.totalSales) }}</td>
                                        <td>{{ total.postExReturns }}</td>
                                        <td>{{ total.leopardReturns }}</td>
                                        <td>{{ total.totalReturns }}</td>
                                        <td>{{ total.tickets }}</td>
                                    </tr>
                                </tfoot>
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
        total() {
            return this.data.reduce((acc, item) => {
                acc.postEx += item.postEx || 0;
                acc.leopards += item.leopards || 0;
                acc.daraz += item.daraz || 0;
                acc.cash += item.cash || 0;
                acc.totalOrders += item.totalOrders || 0;
                acc.totalSales += item.totalSales || 0;
                acc.postExReturns += item.postExReturns || 0;
                acc.leopardReturns += item.leopardReturns || 0;
                acc.totalReturns += item.totalReturns || 0;
                acc.tickets += item.tickets || 0;
                return acc;
            }, {
                postEx: 0,
                leopards: 0,
                daraz: 0,
                cash: 0,
                totalOrders: 0,
                totalSales: 0,
                postExReturns: 0,
                leopardReturns: 0,
                totalReturns: 0,
                tickets: 0,
            });
        }
    },
    methods: {
        dateDiffInDays(from, to) {
            const fromDate = new Date(from);
            const toDate = new Date(to);
            const timeDiff = Math.abs(toDate - fromDate);
            const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
            console.log(daysDiff + 1);

            return parseFloat(daysDiff + 1) || 1; // Avoid division by zero
        },
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : '';
        },
        formatPrice: function formatPrice(price) {
            const value = parseFloat(price).toFixed(0)
            var string = value.toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        submitFunction() {
            this.clearDataTable()
            this.$emit('closingReportFilter', this.filter);
        },
        clearDataTable() {
            const table = $('#closing-report').DataTable();
            table.destroy();
        },
    },
    watch: {
        data(newLedger) {
            setTimeout(() => {
                $('#closing-report').DataTable({
                    paging: false,         // Disable pagination
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Closing Report',
                        }, 'csv', {
                            extend: 'excel',
                            title: 'Closing Report',
                        }
                    ]
                });
            }, 300);
        }
    }
}
</script>
