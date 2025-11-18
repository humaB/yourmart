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
                                    <!-- DataTables will populate this automatically -->
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
            dataTable: null,
            tableData: []
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
                this.prepareTableData(newGroups);
                this.$nextTick(() => {
                    this.initDataTable();
                });
            },
            immediate: true
        }
    },
    methods: {
        prepareTableData(groups) {
            this.tableData = [];
            
            groups.forEach((group, idx) => {
                const groupId = `group-${idx}`;
                
                // Parent row
                this.tableData.push({
                    serial: idx + 1,
                    id: group.parent.id,
                    full_name: group.parent.full_name,
                    email: group.parent.email,
                    whatsapp_number: group.parent.whatsapp_number,
                    cnic_number: group.parent.cnic_number,
                    account_number: group.parent.account_number,
                    account_iban: group.parent.account_iban,
                    remaining_amount: group.parent.remaining_amount,
                    duplicateCount: group.children.length,
                    groupIndex: idx,
                    isParent: true,
                    isDuplicate: false,
                    duplicateFields: {},
                    rowClass: ''
                });
                
                // Child rows (if expanded)
                if (this.expandedGroups[groupId]) {
                    group.children.forEach((child, childIdx) => {
                        this.tableData.push({
                            serial: '',
                            id: child.id,
                            full_name: child.full_name,
                            email: child.email,
                            whatsapp_number: child.whatsapp_number,
                            cnic_number: child.cnic_number,
                            account_number: child.account_number,
                            account_iban: child.account_iban,
                            remaining_amount: child.remaining_amount,
                            duplicateCount: 0,
                            groupIndex: idx,
                            isParent: false,
                            isDuplicate: true,
                            duplicateFields: group.duplicateFields,
                            rowClass: 'alert child-row'
                        });
                    });
                }
            });
        },

        initDataTable() {
            // Destroy existing DataTable if it exists
            if (this.dataTable) {
                this.dataTable.destroy();
                $('#duplicate_dropshipper_list').off('click', '.expand-btn');
            }

            // Initialize DataTable with data
            this.dataTable = $('#duplicate_dropshipper_list').DataTable({
                data: this.tableData,
                columns: [
                    { 
                        data: 'serial',
                        render: (data, type, row) => {
                            return row.isParent ? data : '';
                        }
                    },
                    { data: 'full_name' },
                    { data: 'email' },
                    { data: 'whatsapp_number' },
                    { data: 'cnic_number' },
                    { data: 'account_number' },
                    { data: 'account_iban' },
                    { 
                        data: 'remaining_amount',
                        render: (data, type, row) => {
                            return this.formatPrice(data || 0);
                        }
                    },
                    {
                        data: null,
                        render: (data, type, row) => {
                            if (row.isParent) {
                                return `
                                    <span class="badge badge-primary mr-2">${row.duplicateCount} duplicates</span>
                                    <button class="btn btn-info btn-sm expand-btn" data-group-index="${row.groupIndex}">
                                        <i class="fa ${this.expandedGroups[`group-${row.groupIndex}`] ? 'fa-chevron-down' : 'fa-chevron-right'}"></i>
                                    </button>
                                    <a class="btn btn-primary btn-sm" href="${this.public_url}/dropshippers/preview?id=${row.id}&contact=${row.whatsapp_number}" target="_blank">
                                        <i class="fa fa-eye"></i> 
                                    </a>
                                `;
                            } else {
                                const badges = [];
                                if (row.duplicateFields.email) badges.push('<span class="badge badge-warning mr-1">Email</span>');
                                if (row.duplicateFields.phone) badges.push('<span class="badge badge-primary mr-1">Phone</span>');
                                if (row.duplicateFields.cnic) badges.push('<span class="badge badge-success mr-1">CNIC</span>');
                                if (row.duplicateFields.account) badges.push('<span class="badge badge-danger mr-1">Account</span>');
                                if (row.duplicateFields.iban) badges.push('<span class="badge badge-info mr-1">IBAN</span>');
                                return badges.join(' ');
                            }
                        }
                    }
                ],
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
                ],
                createdRow: (row, data, dataIndex) => {
                    // Add CSS classes to rows
                    if (data.rowClass) {
                        $(row).addClass(data.rowClass);
                    }
                },
                drawCallback: () => {
                    // Re-attach events after DataTable redraws
                    this.attachExpandEvents();
                },
                initComplete: () => {
                    // Attach events after initial load
                    this.attachExpandEvents();
                }
            });
        },

        attachExpandEvents() {
            // Remove any existing event handlers to prevent duplicates
            $('#duplicate_dropshipper_list').off('click', '.expand-btn');
            
            // Attach event delegation for expand buttons
            $('#duplicate_dropshipper_list').on('click', '.expand-btn', (event) => {
                event.preventDefault();
                event.stopPropagation();
                
                const groupIndex = $(event.currentTarget).data('group-index');
                this.toggleGroup(groupIndex);
            });
        },

        toggleGroup(groupIndex) {
            const groupId = `group-${groupIndex}`;
            this.$set(this.expandedGroups, groupId, !this.expandedGroups[groupId]);
            
            // Rebuild data and refresh DataTable
            this.prepareTableData(this.groupedData);
            
            // Refresh DataTable with new data
            this.$nextTick(() => {
                if (this.dataTable) {
                    this.dataTable.clear();
                    this.dataTable.rows.add(this.tableData);
                    this.dataTable.draw();
                    this.attachExpandEvents();
                }
            });
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
        }
    },
    beforeDestroy() {
        // Clean up DataTable and events when component is destroyed
        if (this.dataTable) {
            this.dataTable.destroy();
            $('#duplicate_dropshipper_list').off('click', '.expand-btn');
        }
    }
};
</script>

<style>
.child-row {
    background-color: #f8d7da !important;
    color: #721c24;
}

span.badge {
    margin: 2px;
}

/* Ensure expand buttons are clickable in DataTables */
.expand-btn {
    cursor: pointer;
    z-index: 10;
    position: relative;
}
</style>