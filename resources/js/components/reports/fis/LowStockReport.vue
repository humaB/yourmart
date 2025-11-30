<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Stock Report</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="$emit('lowStockfilter', filter)">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>Status Filter</label>
                                        <select class="form-control" v-model="filter.status">
                                            <option value="all">All Status</option>
                                            <option value="Negative Stock">Negative Stock</option>
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
                                        <th>Product Title</th>
                                        <th>30 Days Sale</th>
                                        <th>Avg. Daily Sales</th>
                                        <th>Desired Days</th>
                                        <th>Stock Required</th>
                                        <th>Current Stock</th>
                                        <th>Status</th>
                                        <th>Restock Qty</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in data" :key="product.sku">
                                        <td>{{ product.sku }}</td>
                                        <td>
                                            <ul class="list-unstyled order-list m-b-0">
                                                <li class="team-member team-member-sm">
                                                    <a :href="getImageUrl(product.image)" target="_blank">
                                                        <img class="rounded-circle" :src="getImageUrl(product.image)" width="35" height="35">
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <a :href="'https://yourmart.pk/products/' + product.name" target="_blank">
                                                {{ product.name }}
                                            </a>
                                        </td>
                                        <td>{{ product.sales_30_days }}</td>
                                        <td>{{ product.avg_daily_sales }}</td>
                                        <td>{{ product.desired_days }}</td>
                                        <td>{{ product.stock_required }}</td>
                                        <td :class="getStockClass(product.current_stock, product.status)">
                                            {{ product.current_stock }}
                                            <span v-if="product.has_negative_stock" class="badge badge-danger badge-sm ml-1">
                                                
                                            </span>
                                        </td>
                                        <td>
                                             <span class="badge" :class="getStatusBadgeClass(product.status)">
                                                {{ product.status }}
                                            </span>
                                            <!-- <span class="btn btn-icon" :class="getStatusBadgeClass(product.status)" :title="product.status">
                                              <i class="getStatusIcon(product.status)"></i>  {{ product.status }}
                                            </span> -->
                                        </td>
                                        <td>
                                            <span v-if="product.restock_warning" class="text-danger font-weight-bold small">
                                                ⚠️ {{ product.restock_warning }}
                                            </span>
                                            <span v-else-if="product.restock_qty > 0" class="text-danger font-weight-bold">
                                                {{ product.restock_qty }}
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
    name: 'StockReport',
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
        getImageUrl(imageId) {
            if (!imageId) {
                return this.public_url + 'assets/img/blank_image.jpg';
            }
            return this.public_url + '/storage/uploads/inventory/products/media/' + imageId;
        },
        getStockClass(currentStock, status) {
            if (status === 'Negative Stock') return 'text-danger font-weight-bold bg-light-danger';
            if (status === 'Out of Stock') return 'text-danger font-weight-bold';
            if (status === 'Low Stock') return 'text-warning font-weight-bold';
            return 'text-success';
        },
        totalIssuanceQuantity() {
            return Object.values(this.data).reduce((total, item) => {
                return total + item.quantity;
            }, 0);
        },
        totalIssuancePurchased() {
            return Object.values(this.data).reduce((total, item) => {
                return total + (parseFloat(item.quantity) * parseFloat(item.purchase_rate));
            }, 0);
        },
        totalReturnQuantity() {
            return Object.values(this.data).reduce((total, item) => {
                return total + item.returned;
            }, 0);
        },
        getStatusBadgeClass(status) {
            switch (status) {
                case 'Negative Stock':
                    return 'badge-warning';
                case 'Out of Stock':
                    return 'badge-danger';
                case 'Low Stock':
                    return 'badge-warning';
                case 'Sufficient':
                    return 'badge-success';
                default:
                    return 'badge-secondary';
            }
        },
//         getStatusIcon(status) {
//     switch (status) {
//         case 'Negative Stock':
//             return 'fas fa-exclamation-circle';
//         case 'Out of Stock':
//             return 'fas fa-times-circle';
//         case 'Low Stock':
//             return 'fas fa-exclamation-triangle';
//         case 'Sufficient':
//             return 'fas fa-check-circle';
//         default:
//             return 'fas fa-circle';
//     }
// }
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
                    "order": [[7, "asc"]], // Sort by Current Stock (column index 7)
                    "pageLength": 25,
                    "lengthMenu": [10, 25, 50, 100],
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Stock Report',
                            exportOptions: {
                                columns: ':visible'
                            }
                        }, 
                        {
                            extend: 'csv',
                            title: 'Stock Report',
                            exportOptions: {
                                columns: ':visible'
                            }
                        }, 
                        {
                            extend: 'excel',
                            title: 'Stock Report',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdf',
                            title: 'Stock Report',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Stock Report',
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
