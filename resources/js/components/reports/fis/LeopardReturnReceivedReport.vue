<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Returns Received</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="submitFunction">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label for="date">Select Courier</label>
                                        <select class="form-control" v-model="filter.courier">
                                            <option value="">Select from the following</option>
                                            <option value="1">Leopard</option>
                                            <option value="2">PostEx</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="date">From</label>
                                        <input type="date" name="from" class="form-control" v-model="filter.from" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="date">To</label>
                                        <input type="date" name="to" class="form-control" v-model="filter.to" />
                                    </div>
                                    <div class="col-md-3 form-group pt-4">
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
                            <table class="table table-bordered" id="leopard_return_received" v-else>
                                <thead>

                                    <tr>
                                        <th>Sr #</th>
                                        <th>Product Name </th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.product_name  }}</td>
                                        <td>{{ item.total_qty }}</td>
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
    name: 'LeopardReturnReceivedReport',
    props: ['data', 'loader'],
    components: {
        BulletListLoader
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            filter: {
                courier : "",
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
            this.$emit('leopardReturnReceivedFilter', this.filter);
        },
        clearDataTable() {
            const table = $('#leopard_return_received').DataTable();
            table.destroy();
        },
    },
    watch: {
        data(newLedger) {
            setTimeout(() => {
                $('#leopard_return_received').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Leopard Returns Received',
                        }, 'csv', {
                            extend: 'excel',
                            title: 'Leopard Returns Received',
                        }
                    ]
                });
            }, 300);
        }
    }
}
</script>
