<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Shop List for PostEx</h5>
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

                            <table class="table table-bordered" id="low-stock-product" v-else>
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>Shop Name</th>
                                        <th>Shop Code</th>
                                        <th>Current Shipper Code</th>
                                        <th>Required Shipper Code</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in data" :key="'list-'+index.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.store_name }}</td>
                                        <td>{{ item.shop_id }}</td>
                                        <td>{{ item.current }}</td>
                                        <td>{{ item.required }}</td>
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
    name: 'ShopListForPostEx',
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
            this.$emit('shopListPostExFilter', this.filter);
        },
        clearDataTable() {
            const table = $('#low-stock-product').DataTable();
            table.destroy();
        },
    },
    watch: {
        data(newLedger) {
            setTimeout(() => {
                $('#low-stock-product').DataTable({
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
