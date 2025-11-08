<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Suspected Duplicate Dropshipper Accounts</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="fetchDuplicateDropshippers">
                                <div class="row">
                                    <div class="col-md-12 form-group pt-4">
                                        <button class="btn btn-block btn-primary">Fetch Suspected Duplicates</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <div class="card-body table-responsive" v-if="loader">
                                <bullet-list-loader :width="250">
                                </bullet-list-loader>
                            </div>
                            <table class="table table-bordered" id="duplicate_dropshipper_list" v-else>
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact #</th>
                                        <th>Total Payable</th>
                                        <th>Total Paid</th>
                                        <th>Remaining Amount</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Duplicate Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.full_name }}</td>
                                        <td>{{ item.email }}</td>
                                        <td>{{ item.whatsapp_number }}</td>
                                        <td>{{ formatPrice(item.total_payable) }}</td>
                                        <td>{{ formatPrice(item.total_paid) }}</td>
                                        <td>{{ formatPrice(item.remaining_amount) }}</td>
                                        <td>
                                            <span v-if="item.status == 0" class="badge badge-warning">Pending</span>
                                            <span v-if="item.status == 1" class="badge badge-success">Approved</span>
                                            <span v-if="item.status == 2" class="badge badge-danger">Rejected</span>
                                            <span v-if="item.status == 3" class="badge badge-danger">Deactivated</span>
                                        </td>
                                        <td>{{ formatDate(item.created_at) }}</td>
                                        <td>
                                            <span class="badge badge-warning" v-if="isEmailDuplicate(item)">Duplicate Email</span>
                                            <span class="badge badge-info" v-if="isPhoneDuplicate(item)">Duplicate Phone</span>
                                            <span class="badge badge-secondary" v-if="isNameDuplicate(item)">Duplicate Name</span>
                                        </td>
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
    name: 'SuspectedDuplicateDropshippers',
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
        fetchDuplicateDropshippers() {
            this.$emit('fetchDuplicateDropshippers');
        },
        clearDataTable() {
            const table = $('#duplicate_dropshipper_list').DataTable();
            if (table) {
                table.destroy();
            }
        },
        isEmailDuplicate(item) {
            return this.data.filter(d => d.email === item.email && d.email).length > 1;
        },
        isPhoneDuplicate(item) {
            return this.data.filter(d => d.whatsapp_number === item.whatsapp_number && d.whatsapp_number).length > 1;
        },
        isNameDuplicate(item) {
            return this.data.filter(d => d.full_name === item.full_name && d.full_name).length > 1;
        }
    },
    watch: {
        data(newData) {
            this.$nextTick(() => {
                setTimeout(() => {
                    this.clearDataTable();
                    $('#duplicate_dropshipper_list').DataTable({
                        "bSort": false,
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'copy',
                                title: 'Suspected Duplicate Dropshipper Accounts',
                            }, 
                            'csv', 
                            {
                                extend: 'excel',
                                title: 'Suspected Duplicate Dropshipper Accounts',
                            }
                        ]
                    });
                }, 300);
            });
        }
    },
    beforeDestroy() {
        this.clearDataTable();
    }
}
</script>