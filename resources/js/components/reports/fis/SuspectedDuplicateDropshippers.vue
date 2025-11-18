<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Duplicate Dropshipper Accounts</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submitFunction" class="mb-3">
                            <button class="btn btn-primary btn-block">Fetch Suspected Duplicates</button>
                        </form>

                        <div v-if="loader" class="text-center py-4">
                            <bullet-list-loader :width="250"></bullet-list-loader>
                        </div>

                        <div v-else class="table-responsive">
                            <table class="table table-striped dataTable no-footer" id="duplicate_dropshipper_list">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>

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
                                    <tr v-for="(item, index) in flatData" :key="item.id"
                                        :class="item.isParent ? '' : item.isDuplicate ? 'alert alert-danger' : ''">

                                        <td v-if="item.isParent">{{ item.groupIndex + 1 }}</td>
                                        <td v-else></td>


                                        <td>{{ item.full_name }}</td>
                                        <td>{{ item.email }}</td>
                                        <td>{{ item.whatsapp_number }}</td>
                                        <td>{{ item.cnic_number }}</td>
                                        <td>{{ item.account_number }}</td>
                                        <td>{{ item.account_iban }}</td>
                                        <td>{{ formatPrice(item.remaining_amount) }}</td>
                                        <td v-if="item.isParent">
                                            <span class="badge badge-primary mr-2">{{ item.duplicateCount }}
                                                duplicates</span>

                                            <button class="btn btn-info" @click="toggleGroup(item.groupId)">
                                                <i
                                                    :class="expandedGroups[item.groupId] ? 'fa fa-chevron-down' : 'fa fa-chevron-right'"></i>
                                            </button>
                                           <a class="btn btn-primary" :href="`${public_url}/dropshippers/preview?id=${item.id}&contact=${item.whatsapp_number}`" target="_blank">
                        <i class="fa fa-eye"></i> 
                    </a>
                                        </td>
                                        <td v-else >
                                            <template v-if="item.isDuplicate">
                                                <span class="badge badge-warning" v-if="item.duplicateFields.email">
                                                    Email</span>
                                                <span class="badge badge-primary" v-if="item.duplicateFields.phone">
                                                    Phone</span>
                                                <span class="badge badge-success" v-if="item.duplicateFields.cnic">
                                                    CNIC</span>
                                                <span class="badge badge-danger" v-if="item.duplicateFields.account">
                                                    Account</span>
                                                <span class="badge badge-info" v-if="item.duplicateFields.iban">
                                                    IBAN</span>
                                            </template>
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
        BulletListLoader,
        
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            api_url: window.location.origin + process.env.MIX_API_URL,
            expandedGroups: {},
            flatData: [],
           
        };
    },
    computed: {
        groupedData() {
            if (!this.data || !this.data.length) return [];
            const groups = [];
            const processedIds = new Set();

            this.data.forEach(item => {
                if (processedIds.has(item.id)) return;

                const duplicates = this.data.filter(other => {
                    if (other.id === item.id || processedIds.has(other.id)) return false;

                    const emailA = (item.email || '').toString().trim().toLowerCase();
                    const emailB = (other.email || '').toString().trim().toLowerCase();
                    const phoneA = (item.whatsapp_number || '').toString().trim();
                    const phoneB = (other.whatsapp_number || '').toString().trim();
                    const cnicA = (item.cnic_number || '').toString().trim();
                    const cnicB = (other.cnic_number || '').toString().trim();
                    const accA = (item.account_number || '').toString().trim();
                    const accB = (other.account_number || '').toString().trim();
                    const ibanA = (item.account_iban || '').toString().trim();
                    const ibanB = (other.account_iban || '').toString().trim();

                    return (
                        (emailA && emailA === emailB) ||
                        (phoneA && phoneA === phoneB) ||
                        (cnicA && cnicA === cnicB) ||
                        (accA && accA === accB) ||
                        (ibanA && ibanA === ibanB)
                    );
                });

                if (duplicates.length > 0) {
                    groups.push({
                        parent: item,
                        children: duplicates,
                        duplicateFields: this.getDuplicateFields(item, duplicates)
                    });
                    processedIds.add(item.id);
                    duplicates.forEach(d => processedIds.add(d.id));
                }
            });

            return groups;
        }
    },
    watch: {
        groupedData: {
            handler(newGroups) {
                this.buildFlatData(newGroups);
            },
            immediate: true
        },
        data() {
            this.$nextTick(() => setTimeout(this.initDataTable, 300));
        }
    },
    methods: {
        buildFlatData(groups) {
            this.flatData = [];

            groups.forEach((group, idx) => {
                const groupId = `group-${idx}`;
                // parent row
                this.flatData.push({
                    ...group.parent,
                    isParent: true,
                    groupId,
                    groupIndex: idx,
                    duplicateCount: group.children.length

                });
                if (this.expandedGroups[groupId]) {
                    group.children.forEach((child, cidx) => {
                        this.flatData.push({
                            ...child,
                            isParent: false,
                            isDuplicate: true,
                            groupId,
                            groupIndex: idx,
                            duplicateFields: group.duplicateFields
                        });
                    });
                }
            });
        },

        decision(data) {
            // Handle approve/reject/activate/deactivate
            console.log('Decision:', data);
            // Add your logic here
        },
        

        toggleGroup(groupId) {
            this.$set(this.expandedGroups, groupId, !this.expandedGroups[groupId]);
            this.buildFlatData(this.groupedData);
        },

        getDuplicateFields(parent, children) {
            const pEmail = (parent.email || '').toString().trim().toLowerCase();
            const pPhone = (parent.whatsapp_number || '').toString().trim();
            const pCnic = (parent.cnic_number || '').toString().trim();
            const pAcc = (parent.account_number || '').toString().trim();
            const pIban = (parent.account_iban || '').toString().trim();

            return {
                email: !!pEmail && children.some(c => (c.email || '').toString().trim().toLowerCase() === pEmail),
                phone: !!pPhone && children.some(c => (c.whatsapp_number || '').toString().trim() === pPhone),
                cnic: !!pCnic && children.some(c => (c.cnic_number || '').toString().trim() === pCnic),
                account: !!pAcc && children.some(c => (c.account_number || '').toString().trim() === pAcc),
                iban: !!pIban && children.some(c => (c.account_iban || '').toString().trim() === pIban)
            };
        },

        formatPrice(price) {
            const val = parseFloat(price || 0).toFixed(2);
            return val.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        submitFunction() {
            this.$emit('DuplicateDropshippersfilter');
        },

        initDataTable() {
            const table = $('#duplicate_dropshipper_list').DataTable();
            if (table) table.destroy();

            $('#duplicate_dropshipper_list').DataTable({
                bSort: false,
                paging: true,
                searching: true,
                info: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy', title: 'Suspected Duplicate Dropshipper Accounts' },
                    'csv',
                    { extend: 'excel', title: 'Suspected Duplicate Dropshipper Accounts' }
                ]
            });
        }
    },
    beforeDestroy() {
        const table = $('#duplicate_dropshipper_list').DataTable();
        if (table) table.destroy();
    }
};
</script>


<style scoped>
.alert.alert-danger {
    background-color: #f8d7da !important;
    color: #721c24;
}

span.badge {
    margin: 2px;
}
</style>