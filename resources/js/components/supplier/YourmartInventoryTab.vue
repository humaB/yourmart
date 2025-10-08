<template>
    <div>
        <div class="row">
            <!-- Stock -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-purple">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20">
                            <div class="text-right">
                                <h3 class="font-light mb-0">
                                    <i class="ti-arrow-up text-success"></i> {{ formatPrice(yourmartStats.totalStockValue) }}
                                </h3>
                                <span class="text-muted">Stock</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sold Out -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-green">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20">
                            <div class="text-right">
                                <h3 class="font-light mb-0">
                                    <i class="ti-arrow-up text-success"></i> {{ formatPrice(yourmartStats.totalSoldOutValue) }}
                                </h3>
                                <span class="text-muted">Sold Out</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Received -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-cyan">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20">
                            <div class="text-right">
                                <h3 class="font-light mb-0">
                                    <i class="ti-arrow-up text-success"></i> {{ formatPrice(yourmartStats.totalPaid) }}
                                </h3>
                                <span class="text-muted">Payment Received</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon l-bg-orange">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="padding-20">
                            <div class="text-right">
                                <h3 class="font-light mb-0">
                                    <i class="ti-arrow-up text-success"></i> {{ formatPrice(yourmartStats.balance) }}
                                </h3>
                                <span class="text-muted">Balance</span>
                            </div>
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
                            <label for="date">Supplier</label>
                            <v-select :options="suppliers" v-model="filter.supplier"></v-select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="date">From</label>
                            <input type="date" name="from" class="form-control" v-model="filter.from" />
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="date">To</label>
                            <input type="date" name="to" class="form-control" v-model="filter.to" />
                        </div>
                        <div class="col-md-3 form-group pt-4">
                            <button class="btn btn-block btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered text-center align-middle w-100" id="dataTable2">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Product</th>
                            <th rowspan="2">SKU</th>
                            <th colspan="3">STOCK IN-TAKE</th>
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
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Example empty rows -->
                        <tr v-for="(row, index) in yourmartStats.products" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ row.product_name }}</td>
                            <td>{{ row.sku }}</td>
                            <td>{{ row.stock_in_qty }}</td>
                            <td>{{ row.stock_in_price }}</td>
                            <td>{{ formatPrice(row.stock_in_amount) }}</td>
                            <td>{{ row.sold_out_qty }}</td>
                            <td>{{ formatPrice(row.sold_out_qty * row.stock_in_price) }}</td>
                            <td>{{ row.balance_qty }}</td>
                            <td>{{ formatPrice(row.balance_amount) }}</td>
                            <td>
                                <button class="btn btn-primary" @click="fetchPurchaseOrders(row.slug, 'yourmart')" data-toggle="modal"
                                    data-target="#purchaseOrderDetails">
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
    name: "YourmartInventoryTab",
    props : [ 'yourmartStats', 'filter', 'suppliers'],
    methods : {
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        fetchPurchaseOrders( slug, type){
            this.$emit('fetchPurchaseOrders', { slug, type });
        },
        submitFunction(){
            this.filter.status = '';
            this.$emit('submitFunction')
        }
    }
}
</script>
