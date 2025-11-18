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

                        <div class="row mt-3">
                                <div class="col-md-12 mb-3">
                                    <button class="btn btn-secondary buttons-html5 mr-2" @click="exportTable('copy')">
                                        <i class="fa fa-copy"></i> Copy
                                    </button>
                                    <button class="btn btn-success mr-2" @click="exportTable('csv')">
                                        <i class="fa fa-file-csv"></i> CSV
                                    </button>
                                    <button class="btn btn-primary" @click="exportTable('excel')">
                                        <i class="fa fa-file-excel"></i> Excel
                                    </button>
                                </div>
                            </div>

                        <div v-if="loader" class="text-center py-4">
                            <bullet-list-loader :width="250"></bullet-list-loader>
                        </div>

                        <div v-else>
                            <div class="table-responsive">
                                <table class="table table-striped table-md">
                                    <tbody>
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
                                    <tr v-for="(item, index) in paginatedData" :key="item.id"
                                        :class="item.isDuplicate ? 'alert alert-danger danger-bs' : ''">
                                        
                                        <td v-if="item.isParent">{{ getSerialNumber(index) }}</td>
                                        <td v-else></td>
                                        
                                        <td>{{ item.full_name }}</td>
                                        <td>{{ item.email }}</td>
                                        <td>{{ item.whatsapp_number }}</td>
                                        <td>{{ item.cnic_number }}</td>
                                        <td>{{ item.account_number }}</td>
                                        <td>{{ item.account_iban }}</td>
                                        <td>{{ formatPrice(item.remaining_amount) }}</td>
                                        <td>
                                            <template v-if="item.isParent">
                                                <div class="badge badge-danger">{{ item.duplicateCount }} duplicates</div>
                                                <button class="btn btn-info btn-sm" @click="toggleGroup(item.groupIndex)">
                                                    <i :class="expandedGroups[`group-${item.groupIndex}`] ? 'fa fa-chevron-down' : 'fa fa-chevron-right'"></i>
                                                </button>
                                                <a class="btn btn-primary btn-sm" :href="`${public_url}/dropshippers/preview?id=${item.id}&contact=${item.whatsapp_number}`" target="_blank">
                                                    <i class="fa fa-eye"></i> 
                                                </a>
                                            </template>
                                            <template v-else>
                                                <span class="badge badge-warning mr-1" v-if="item.duplicateFields.email">Email</span>
                                                <span class="badge badge-primary mr-1" v-if="item.duplicateFields.phone">Phone</span>
                                                <span class="badge badge-success mr-1" v-if="item.duplicateFields.cnic">CNIC</span>
                                                <span class="badge badge-danger mr-1" v-if="item.duplicateFields.account">Account</span>
                                                <span class="badge badge-info mr-1" v-if="item.duplicateFields.iban">IBAN</span>
                                            </template>
                                        </td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="dataTables_info">
                                        Showing {{ startIndex + 1 }} to {{ endIndex }} of {{ totalRows }} entries
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers float-right">
                                        <ul class="pagination">
                                            <li class="paginate_button page-item previous" :class="{ disabled: currentPage === 1 }">
                                                <a href="#" class="page-link" @click.prevent="changePage(currentPage - 1)">Previous</a>
                                            </li>
                                            
                                            <li v-for="page in pages" :key="page" class="paginate_button page-item" :class="{ active: page === currentPage }">
                                                <a href="#" class="page-link" @click.prevent="changePage(page)">{{ page }}</a>
                                            </li>
                                            
                                            <li class="paginate_button page-item next" :class="{ disabled: currentPage === totalPages }">
                                                <a href="#" class="page-link" @click.prevent="changePage(currentPage + 1)">Next</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Export Buttons -->
                           
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
            currentPage: 1,
            perPage: 10
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
        },
        totalRows() {
            return this.flatData.length;
        },
        totalPages() {
            return Math.ceil(this.totalRows / this.perPage);
        },
        startIndex() {
            return (this.currentPage - 1) * this.perPage;
        },
        endIndex() {
            return Math.min(this.startIndex + this.perPage, this.totalRows);
        },
        paginatedData() {
            return this.flatData.slice(this.startIndex, this.endIndex);
        },
        pages() {
            const pages = [];
            const startPage = Math.max(1, this.currentPage - 2);
            const endPage = Math.min(this.totalPages, startPage + 4);
            
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }
            return pages;
        }
    },
    watch: {
        groupedData: {
            handler(newGroups) {
                this.buildFlatData(newGroups);
            },
            immediate: true
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

        getSerialNumber(index) {
            return this.startIndex + index + 1;
        },

        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },

        toggleGroup(groupIndex) {
            const groupId = `group-${groupIndex}`;
            this.$set(this.expandedGroups, groupId, !this.expandedGroups[groupId]);
            this.buildFlatData(this.groupedData);
            // Reset to first page when expanding/collapsing
            this.currentPage = 1;
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

        exportTable(type) {
            const data = this.flatData.map(item => ({
                'Name': item.full_name,
                'Email': item.email,
                'Contact #': item.whatsapp_number,
                'CNIC': item.cnic_number,
                'Account Number': item.account_number,
                'IBAN': item.account_iban,
                'Remaining Amount': this.formatPrice(item.remaining_amount),
                'Type': item.isParent ? 'Parent' : 'Duplicate'
            }));

            let content, mimeType, filename;

            switch (type) {
                case 'csv':
                    content = this.convertToCSV(data);
                    mimeType = 'text/csv';
                    filename = 'duplicate_dropshippers.csv';
                    break;
                case 'excel':
                    content = this.convertToCSV(data);
                    mimeType = 'application/vnd.ms-excel';
                    filename = 'duplicate_dropshippers.xls';
                    break;
                case 'copy':
                    const tableText = data.map(row => 
                        Object.values(row).join('\t')
                    ).join('\n');
                    navigator.clipboard.writeText(tableText);
                    alert('Data copied to clipboard!');
                    return;
            }

            const blob = new Blob([content], { type: mimeType });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            window.URL.revokeObjectURL(url);
        },

        convertToCSV(data) {
            if (data.length === 0) return '';
            
            const headers = Object.keys(data[0]);
            const csvRows = [
                headers.join(','),
                ...data.map(row => headers.map(header => {
                    const value = row[header] || '';
                    return `"${value.toString().replace(/"/g, '""')}"`;
                }).join(','))
            ];
            
            return csvRows.join('\n');
        }
    }
};
</script>

<style scoped>
.danger-bs {
    background-color: #f8d7da !important;
    color: #721c24;
}
span.badge {
    margin: 1px;
}
.btn-secondary{
    background-color: #666 !important;
}
</style> 