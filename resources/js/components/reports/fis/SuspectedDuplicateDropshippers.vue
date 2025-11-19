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
                                <thead>
                                    <tr>
                                        <th></th> <!-- Expand/Collapse column -->
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
            dataTable: null,
            tableData: [],
            expandedGroups: new Set() // Track expanded groups
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
                        duplicateFields: this.getDuplicateFields(item, duplicates),
                        groupIndex: groups.length
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
                // Only add parent rows to main table data
                // Child rows will be shown via expandable rows
                this.tableData.push({
                    id: group.parent.id,
                    full_name: group.parent.full_name,
                    email: group.parent.email,
                    whatsapp_number: group.parent.whatsapp_number,
                    cnic_number: group.parent.cnic_number,
                    account_number: group.parent.account_number,
                    account_iban: group.parent.account_iban,
                    remaining_amount: group.parent.remaining_amount,
                    groupIndex: idx,
                    isParent: true,
                    duplicateCount: group.children.length,
                    children: group.children,
                    duplicateFields: group.duplicateFields
                });
            });
        },

        initDataTable() {
            // Destroy existing DataTable if it exists
            if (this.dataTable) {
                this.dataTable.destroy();
                $('#duplicate_dropshipper_list').off('click', '.expand-btn');
            }
            

            // Initialize DataTable
            this.dataTable = $('#duplicate_dropshipper_list').DataTable({
                data: this.tableData,
                        language: {
            emptyTable: "" // This replaces the empty colspan row
        },

                columns: [
                    {
                        // Expand/Collapse button column
                        data: null,
                        className: 'dt-control',
                        orderable: false,
                        defaultContent: '',
                        render: (data, type, row) => {
                            const isExpanded = this.expandedGroups.has(row.groupIndex);
                            return `
                                <button class="btn btn-sm expand-btn ${isExpanded ? 'btn-secondary' : 'btn-info'}" 
                                        data-group-index="${row.groupIndex}"
                                        title="${isExpanded ? 'Collapse' : 'Expand'}">
                                    <i class="fa ${isExpanded ? 'fa-chevron-down' : 'fa-chevron-right'}"></i>
                                </button>
                            `;
                        }
                    },
                    { 
                        data: 'groupIndex',
                        render: (data, type, row) => {
                            return data + 1;
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
                         orderable: false, 
                        render: (data, type, row) => {
                            return `
                                <span class="badge badge-primary mr-2">${row.duplicateCount} duplicates</span>
                                <a class="btn btn-primary btn-sm" href="${this.public_url}/dropshippers/preview?id=${row.id}&contact=${row.whatsapp_number}" target="_blank">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            `;
                        }
                    }
                ],
                 order: [[1, 'asc']],
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
                drawCallback: () => {
                    this.attachExpandEvents();
                },
                initComplete: () => {
                    this.attachExpandEvents();
                }
            });
        },

        attachExpandEvents() {
            // Remove any existing event handlers
            $('#duplicate_dropshipper_list').off('click', '.expand-btn');
            
            // Attach event delegation for expand buttons
            $('#duplicate_dropshipper_list').on('click', '.expand-btn', (event) => {
                event.preventDefault();
                event.stopPropagation();
                
                const $btn = $(event.currentTarget);
                const groupIndex = parseInt($btn.data('group-index'));
                const tr = $btn.closest('tr');
                const row = this.dataTable.row(tr);
                
                this.toggleGroup(row, groupIndex, $btn);
            });
        },

        toggleGroup(row, groupIndex, $btn) {
            if (this.expandedGroups.has(groupIndex)) {
                // Collapse the group
                this.collapseGroup(row, groupIndex, $btn);
            } else {
                // Expand the group
                this.expandGroup(row, groupIndex, $btn);
            }
        },

        expandGroup(row, groupIndex, $btn) {
            const parentData = row.data();
            
            // Create child rows HTML
            let childRowsHtml = '';
            
            parentData.children.forEach((child, index) => {
                const badges = [];
                if (parentData.duplicateFields.email) badges.push('<span class="badge badge-warning mr-1">Email</span>');
                if (parentData.duplicateFields.phone) badges.push('<span class="badge badge-primary mr-1">Phone</span>');
                if (parentData.duplicateFields.cnic) badges.push('<span class="badge badge-success mr-1">CNIC</span>');
                if (parentData.duplicateFields.account) badges.push('<span class="badge badge-danger mr-1">Account</span>');
                if (parentData.duplicateFields.iban) badges.push('<span class="badge badge-info mr-1">IBAN</span>');
                
                childRowsHtml += `
                    <tr class="child-row alert-danger">
                        <td></td>
                        <td></td>
                        <td >${child.full_name}</td>
                        <td>${child.email}</td>
                        <td>${child.whatsapp_number}</td>
                        <td>${child.cnic_number}</td>
                        <td>${child.account_number}</td>
                        <td>${child.account_iban}</td>
                        <td>${this.formatPrice(child.remaining_amount)}</td>
                        <td>
                            ${badges.join(' ')}
                            <a class="btn btn-primary btn-sm" href="${this.public_url}/dropshippers/preview?id=${child.id}&contact=${child.whatsapp_number}" target="_blank">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });

            row.child(
                $(`${childRowsHtml}`)
            ).show();
            $btn.removeClass('btn-info').addClass('btn-secondary');
            $btn.find('i').removeClass('fa-chevron-right').addClass('fa-chevron-down');
            $btn.attr('title', 'Collapse');
            
            this.expandedGroups.add(groupIndex);
            row.nodes().to$().addClass('shown');
        },

        collapseGroup(row, groupIndex, $btn) {
            row.child.hide();
            
            $btn.removeClass('btn-secondary').addClass('btn-info');
            $btn.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
            $btn.attr('title', 'Expand');
            
            this.expandedGroups.delete(groupIndex);
            
            row.nodes().to$().removeClass('shown');
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
        if (this.dataTable) {
            this.dataTable.destroy();
            $('#duplicate_dropshipper_list').off('click', '.expand-btn');
        }
    }
};
</script>

<style scoped>
.alert-danger {
    background-color: #f8d7da !important;
    color: #721c24;
}

span.badge {
    margin: 2px;
}

.btn-sm {
    margin: 0 2px;
}
/* tr.shown {
    background-color: #f8f9fa !important;
} */

/* .child-row td {
    padding-left: 40px !important;
    border-top: 1px solid #dee2e6;
} */

.dt-control {
    text-align: center;
}
</style>

<style>
/* table.dataTable tbody tr.child-row td {
    background-color: #f8d7da !important;
    color: #721c24;
} */
/* 
table.dataTable tbody tr.shown td {
    
    background-color: #e3f2fd !important;
} */
</style>