<template>
    <div>
        <div class="row">
            <div class="dashboard-cards col-md-12">
                <!-- Stock -->
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-purple">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20 text-right">
                            <h3 class="font-light mb-0">
                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(supplierStats.totalStockValue)
                                }}
                            </h3>
                            <span class="text-muted">Stock</span>
                        </div>
                    </div>
                </div>

                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-orange">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20 text-right">
                            <h3 class="font-light mb-0">
                                <i class="ti-arrow-up text-success"></i> {{
                                    formatPrice(supplierStats.totalSaleInProcess) }}
                            </h3>
                            <span class="text-muted">Sales in process</span>
                        </div>
                    </div>
                </div>

                <!-- Sold Out -->
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-green">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20 text-right">
                            <h3 class="font-light mb-0">
                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(supplierStats.totalSoldOutValue)
                                }}
                            </h3>
                            <span class="text-muted">Sold out</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Received -->
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-cyan">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20 text-right">
                            <h3 class="font-light mb-0">
                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(supplierStats.totalPaid) }}
                            </h3>
                            <span class="text-muted">Payment Received</span>
                        </div>
                    </div>
                </div>

                <!-- Balance -->
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-orange">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20 text-right">
                            <h3 class="font-light mb-0">
                                <i class="ti-arrow-up text-success"></i> {{ formatPrice(supplierStats.balance) }}
                            </h3>
                            <span class="text-muted">Balance</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form @submit.prevent="submitFunction">
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="">Status</label>
                            <select name="" id="" class="form-control" v-model="filter.status">
                                <option value="">-- Select Status -- </option>
                                <option value="In Stock">In Stock</option>
                                <option value="In Process">In Process</option>
                                <option value="Sold Out">Sold Out</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="date">Supplier</label>
                            <v-select :options="suppliers" v-model="filter.supplier"></v-select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="date">From</label>
                            <input type="date" name="from" class="form-control" v-model="filter.from" />
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="date">To</label>
                            <input type="date" name="to" class="form-control" v-model="filter.to" />
                        </div>
                        <div class="col-md-2 form-group">
                             <label for="date">Action</label>
                            <button class="btn btn-block btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered text-center align-middle" id="supplierDataTable">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Image</th>
                            <th rowspan="2">Product</th>
                            <th rowspan="2">SKU</th>
                            <th colspan="3">STOCK IN-TAKE</th>
                            <th colspan="2">SALE IN PROCESS</th>
                            <th colspan="2">SOLD OUT</th>
                            <th colspan="2">BALANCE</th>
                            <th rowspan="2">P.O.s</th>
                        </tr>
                        <tr>
                            <th>QTY</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>QTY</th>
                            <th>Amount</th>
                            <th>QTY</th>
                            <th>Amount</th>
                            <th>QTY</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in supplierStats.products" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td class="text-truncate">
                                <ul class="list-unstyled order-list m-b-0 m-b-0">
                                    <li class="team-member team-member-sm">
                                        <a :href="getImageUrl(row.hero_image)" target="_blank">
                                            <img class="rounded-circle" :src="getImageUrl(row.hero_image)">
                                        </a>
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <a :href="web_url + 'products/' + row.slug" target="_blank">
                                    {{ row.product_name }}
                                </a>
                            </td>
                            <td>{{ row.sku || '' }}</td>
                            <td>{{ row.stock_in_qty || 0 }}</td>
                            <td>{{ row.stock_in_price || 0 }}</td>
                            <td>{{ formatPrice((row.stock_in_amount || 0)) }}</td>

                            <td>{{ row.inprocess_qty || 0 }}</td>
                            <td>{{ formatPrice(((row.inprocess_qty || 0) * (row.stock_in_price || 0))) }}</td>

                            <td>{{ row.sold_out_qty || 0 }}</td>
                            <td>{{ formatPrice(((row.sold_out_qty || 0) * (row.stock_in_price || 0))) }}</td>

                            <td>{{ row.balance_qty || 0 }}</td>
                            <td>{{ formatPrice((row.balance_amount || 0)) }}</td>

                            <td>
                                <button class="btn btn-primary" @click="fetchPurchaseOrders(row.slug)"
                                    data-toggle="modal" data-target="#purchaseOrderDetails">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "SupplierInventoryTab",
    props: ['supplierStats', 'filter', 'suppliers'],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + "/",
            web_url: process.env.MIX_WEB_URL,
        }
    },
    methods: {
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        fetchPurchaseOrders(slug, type) {
            this.$emit('fetchPurchaseOrders', { slug, type });
        },
        submitFunction() {
            this.$emit('submitFunction')
        },
        getImageUrl(imageId) {
            // Check if the image is null
            if (!imageId) {
                return this.public_url + 'assets/img/blank_image.jpg';
            }
            return this.public_url + 'storage/uploads/inventory/products/media/' + imageId;
        },
    }
}
</script>
<style scoped>
/* Custom grid for 5 equal cards */
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
}

/* Responsive: stack to fewer columns on smaller devices */
@media (max-width: 1200px) {
    .dashboard-cards {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .dashboard-cards {
        grid-template-columns: 1fr;
    }
}
</style>
