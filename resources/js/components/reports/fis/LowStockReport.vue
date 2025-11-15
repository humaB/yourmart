<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Stock Report</h5> <!-- Updated title -->
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="$emit('lowStockfilter', filter)">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>Status Filter</label>
                                        <select class="form-control" v-model="filter.status">
                                            <option value="all">All Status</option>
                                            <option value="Out of Stock">Out of Stock</option>
                                            <option value="Low Stock">Low Stock</option>
                                            <option value="Sufficient">Sufficient</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group pt-4">
                                        <button class="btn btn-block btn-primary">Fetch Report</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <div class="card-body table-responsive" v-if="loader">
                                <bullet-list-loader :width="250"></bullet-list-loader>
                            </div>
                            <table class="table table-striped dataTable no-footer" id="low-stock-report-table" v-else>
                                <thead>
                                    <tr>
                                        <th>Product SKU</th>
                                        <th>Image</th>
                                        <th>Product Link</th>
                                        <th>Current Stock</th>
                                        <th>Sales (30 Days)</th>
                                        <th>Avg Daily Sales</th>
                                        <th>Lead Time</th>
                                        <th>Safety Stock</th>
                                        <th>Low Stock Level</th>
                                        <th>Status</th>
                                        <th>Recommended Reorder Qty</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- <tr v-for="product in data" :key="product.sku" 
                                        :class="getStatusRowClass(product.status)"> -->
                                        <tr v-for="product in data" :key="product.sku" 
                                        >
                                        <td>{{ product.sku }}</td>
                                        <td><img class="rounded-circle" :src="product.image" alt="Product Image" width="35"></td>
                                        <!-- <td>{{ product.image }}</td> -->
                                        <td><a :href="'/products/' + product.name" target="_blank">
        {{ product.name }}
    </a></td>
                                        <td :class="getStockClass(product.current_stock, product.low_stock_level, product.status)">
                                            {{ product.current_stock }}
                                        </td>
                                        <td>{{ product.sales_30_days }}</td>
                                        <td>{{ product.avg_daily_sales }}</td>
                                        <td>{{ product.lead_time }} days</td>
                                        <td>{{ product.safety_stock }}</td>
                                        <td>{{ product.low_stock_level }}</td>
                                        <td>
                                            <span class="badge badge-shadow" :class="getStatusBadgeClass(product.status)">
                                                {{ product.status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="product.recommended_reorder_qty > 0" class="text-danger font-weight-bold">
                                                {{ product.recommended_reorder_qty }}
                                            </span>
                                            <span v-else class="text-muted">-</span>
                                        </td>
                                        <td>{{ product.last_updated }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="!loader && data.length === 0" class="text-center py-4">
                                <p class="text-muted">No products found.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { BulletListLoader } from 'vue-content-loader';

export default {
    name: 'StockReport', // Updated name
    props: ['data', 'loader'],
    components: {
        BulletListLoader
    },
    data() {
        return {
            filter: {
                status: 'all'
            }
        }
    },
    methods: {
        // getStatusRowClass(status) {
        //     switch (status) {
        //         case 'Out of Stock':
        //             return 'table-danger';
        //         case 'Low Stock':
        //             return 'table-warning';
        //         case 'Sufficient':
        //             return ''; // 
        //         default:
        //             return '';
        //     }
        // },
        getStockClass(currentStock, lowStockLevel, status) {
            if (status === 'Out of Stock') return 'text-danger font-weight-bold';
            if (status === 'Low Stock') return 'text-warning font-weight-bold';
            return 'text-success'; // Sufficient stock in green
        },
        getStatusBadgeClass(status) {
            switch (status) {
                case 'Out of Stock':
                    return 'badge-danger';
                case 'Low Stock':
                    return 'badge-warning';
                case 'Sufficient':
                    return 'badge-success';
                default:
                    return 'badge-secondary';
            }
        }
    },
    watch: {
        data(newData) {
            this.$nextTick(() => {
                // Destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable('#low-stock-report-table')) {
                    $('#low-stock-report-table').DataTable().destroy();
                }
                
                $('#low-stock-report-table').DataTable({
                    "bSort": true,
                    "order": [[2, "asc"]], // Sort by Current Stock ascending
                    "pageLength": 25,
                    "lengthMenu": [10, 25, 50, 100],
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Stock Products Report', // Updated title
                            exportOptions: {
                                columns: ':visible'
                            }
                        }, 
                        {
                            extend: 'csv',
                            title: 'Stock Products Report', // Updated title
                            exportOptions: {
                                columns: ':visible'
                            }
                        }, 
                        {
                            extend: 'excel',
                            title: 'Stock Products Report', // Updated title
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdf',
                            title: 'Stock Products Report', // Updated title
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Stock Products Report', // Updated title
                            exportOptions: {
                                columns: ':visible'
                            }
                        }
                    ]
                });
            });
        }
    }
}
</script>

<style scoped>
/* .table-responsive {
    max-height: 600px;
}

.badge {
    font-size: 0.85em;
    padding: 0.4em 0.6em;
}

.table-danger {
    background-color: #f8d7da;
}

.table-warning {
    background-color: #fff3cd;
}


.text-danger {
    color: #dc3545 !important;
}

.text-warning {
    color: #e6ac00 !important;
}

.text-success {
    color: #28a745 !important;
}

.font-weight-bold {
    font-weight: 700 !important;
} */
</style>