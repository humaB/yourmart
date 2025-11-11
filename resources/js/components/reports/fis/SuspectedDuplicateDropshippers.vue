<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Duplicate Dropshipper Accounts</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="submitFunction">
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
                                        <th>CNIC</th>
                                        <th>Account Number</th>
                                        <th>IBAN</th> 
                                        <th>Remaining Amount</th>
                                        <th>Reason/Similarity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in data" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.full_name }}</td>
                                        <td>{{ item.email }}</td>
                                        <td>{{ item.whatsapp_number }}</td>
                                        <td>{{ item.cnic_number }}</td>
                                        <td>{{ item.account_number }}</td>
                                        <td>{{ item.account_iban }}</td>
                                        <td>{{ formatPrice(item.remaining_amount) }}</td>
                                        
                                        <td>
                                            <span class="custom-badge badge badge-warning" v-if="isEmailDuplicate(item)">Duplicate Email</span>
                                            <span class="custom-badge badge badge-primary" v-if="isPhoneDuplicate(item)">Duplicate Phone</span>
                                            <span class="custom-badge badge badge-success" v-if="isCNICDuplicate(item)">Duplicate CNIC</span>
                                            <span class="custom-badge badge badge-danger" v-if="isAcoountDuplicate(item)">Duplicate Account</span>
                                            <span class="custom-badge badge badge-warning" v-if="isIBANDuplicate(item)">Duplicate IBAN</span>
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
        submitFunction() {
            this.$emit('DuplicateDropshippersfilter');
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
        isCNICDuplicate(item) {
            return this.data.filter(d => d.cnic_number === item.cnic_number && d.cnic_number).length > 1;
        },
        isPhoneDuplicate(item) {
            return this.data.filter(d => d.whatsapp_number === item.whatsapp_number && d.whatsapp_number).length > 1;
        },
        isAcoountDuplicate(item) {
            return this.data.filter(d => d.account_number === item.account_number && d.account_number).length > 1;
        },
        isIBANDuplicate(item) {
            return this.data.filter(d => d.account_iban === item.account_iban && d.account_iban).length > 1;
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

<style scoped>
.custom-badge{
    margin-top: 5px;;
    margin-bottom: 5px;;
}
</style>