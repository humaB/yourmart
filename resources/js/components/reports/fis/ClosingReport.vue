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
                                    <div class="col-md-8">
                                        <input type="date" name="" id="" class="form-control" v-model="filter.date">
                                    </div>
                                    <div class="col-md-4 form-group">
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
                            <table class="table table-bordered" id="closing-report" v-else>
                                <thead>
                                    <tr>
                                        <th>#</th>
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
                                    <tr>
                                        <td>#</td>
                                        <td>{{ data.postEx }}</td>
                                        <td>{{ data.leopards }}</td>
                                        <td>{{ data.daraz }}</td>
                                        <td>{{ data.cash }}</td>
                                        <td>{{ data.totalOrders }}</td>
                                        <td>{{ data.totalSales }}</td>
                                        <td>{{ data.postExReturns }}</td>
                                        <td>{{ data.leopardReturns }}</td>
                                        <td>{{ data.totalReturns }}</td>
                                        <td>{{ data.tickets }}</td>
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
    props: ['data', 'loader'],
    components: {
        BulletListLoader
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            filter: {
                date: new Date().toISOString().substr(0, 10),
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
                    "bSort": false,
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
