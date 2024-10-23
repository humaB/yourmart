<template>
    <div>
        <div class="row">
            <div class="col-md-12 card">
                <div class="card-header">
                    <h5>Insight's</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-12">
                        <table style="table-layout: fixed; width: 100%;">
                            <tr>
                              <td style="padding : 5px">
                                <div class="card">
                                    <div class="card-body card-type-3">
                                      <div class="row">
                                        <div class="col">
                                          <h6 class="text-muted mb-0">Products</h6>
                                          <span class="font-weight-bold mb-0">{{ totalProducts }}</span>
                                        </div>

                                      </div>

                                    </div>
                                  </div>
                              </td>
                              <td style="padding : 5px">
                                <div class="card">
                                    <div class="card-body card-type-3">
                                      <div class="row">
                                        <div class="col">
                                          <h6 class="text-muted mb-0">Quantity</h6>
                                          <span class="font-weight-bold mb-0">{{ totalQuantity }}</span>
                                        </div>

                                      </div>

                                    </div>
                                  </div>
                              </td>
                              <td style="padding : 5px">
                                <div class="card">
                                    <div class="card-body card-type-3">
                                      <div class="row">
                                        <div class="col">
                                          <h6 class="text-muted mb-0">Out of Stock</h6>
                                         <a href="#" @click="filterStock('out_of_stock')"> <span class="font-weight-bold mb-0">{{ outOfStock }}</span></a>
                                        </div>

                                      </div>

                                    </div>
                                  </div>
                              </td>
                              <td style="padding : 5px">
                                <div class="card">
                                    <div class="card-body card-type-3">
                                      <div class="row">
                                        <div class="col">
                                          <h6 class="text-muted mb-0">Low Stock</h6>
                                          <a href="#" @click="filterStock('low_stock')"> <span class="font-weight-bold mb-0">{{ lowStock }}</span></a>
                                        </div>

                                      </div>

                                    </div>
                                  </div>
                              </td>
                              <td style="padding : 10px">
                                <div class="card">
                                    <div class="card-body card-type-3">
                                      <div class="row">
                                        <div class="col">
                                          <h6 class="text-muted mb-0">High Stock</h6>
                                          <a href="#" @click="filterStock('high_stock')"> <span class="font-weight-bold mb-0">{{ highStock }}</span> </a>
                                        </div>

                                      </div>

                                    </div>
                                  </div>
                              </td>
                              <td style="padding : 5px">
                                <div class="card">
                                    <div class="card-body card-type-3">
                                      <div class="row">
                                        <div class="col">
                                          <h6 class="text-muted mb-0">With out Barcode</h6>
                                          <span class="font-weight-bold mb-0">{{ withoutBarcode }}</span>
                                        </div>

                                      </div>

                                    </div>
                                  </div>
                              </td>

                            </tr>
                            </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
              <div class="card card-primary">
                <TableHeader :tableHeader="tableHeader" />

                <div class="card-body">
                  <!-- Table -->
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-body table-responsive" v-if="loader">
                            <bullet-list-loader :width="250"> </bullet-list-loader>
                        </div>
                        <div class="card-body" v-else>
                            <table class="table table-bordered" id="stock_table">
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>Reference ID</th>
                                        <th>SKU</th>
                                        <th>Product</th>
                                        <th>Avg Price</th>
                                        <th>Quantity</th>
                                        <th>Barcode</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in products" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.sku }}</td>
                                        <td>{{ item.product ? item.product.title : '-'}}</td>
                                        <td>{{ item.avg_price || 0 }}</td>
                                        <td>{{ item.stock }}</td>
                                        <td>
                                            {{ item.barcode ? item.barcode.barcode : '-' }}
                                        </td>
                                        <td width="20%">
                                            <div class="d-flex">
                                              <input
                                                type="text"
                                                class="form-control"
                                                v-model="newBarcodes[item.id]"
                                                placeholder="Enter barcode"
                                              />
                                              <button class="btn btn-sm btn-primary" @click="updateBarcode(item, newBarcodes[item.id])">Update</button>
                                            </div>
                                          </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                  </div>
                  <!-- END TABLE -->
                </div>
              </div>
            </div>
          </div>
          </div>

    </div>
</template>
<script>

  import TableHeader from "../../components/table/TableHeaderComponent.vue";
  import { BulletListLoader } from "vue-content-loader";
    export default {
        name : 'StoreStockPage',
        components: {
            TableHeader,
            BulletListLoader
        },
        data() {
            return {
                api_url: window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Product Stock",
                },
                loader : true,
                products : [],
                newBarcodes: {},
                totalProducts : 0,
                totalQuantity : 0,
                outOfStock : 0,
                lowStock : 0,
                withoutBarcode : 0,
                highStock: 0
            };
        },
        created(){
            this.fetchStock();
        },
        methods : {
            filterStock( filter ){
                let vm = this;
                vm.clearDataTable()
                vm.loader = true;
                axios
                .post(this.api_url + "inventory/products/store/stocks/filter" , { filter })
                .then((response) => {
                    const results = response.data.response;
                    vm.products = results;

                    vm.loader = false;

                    setTimeout(() => {
                        this.dataTable()
                    },300)
                })
            },
            fetchStock(){
                let vm = this;
                vm.loader = true;
                axios
                .get(this.api_url + "inventory/products/store/stocks")
                .then((response) => {
                    const results = response.data.response;
                    vm.products = results.stock;
                    vm.totalProducts = results.totalProducts;
                    vm.totalQuantity = results.totalQuantity;
                    vm.lowStock = results.lowStock;
                    vm.highStock = results.highStock;
                    vm.outOfStock = results.outOfStock;
                    vm.withoutBarcode = results.withoutBarcodeCount
                    vm.loader = false;

                    setTimeout(() => {
                        this.dataTable()
                    },300)
                })
                .catch((err) => this.fetchStock());
            },
            updateBarcode(item, newBarcode) {
                let vm = this;
                vm.clearDataTable()
                const data = {
                    id : item.id,
                    barcode : newBarcode
                }

                axios
                .post(this.api_url + "inventory/products/store/stocks/update-barcode", data)
                .then((response) => {

                    this.fetchStock();
                    return swal({
                        title: "Success",
                        text: 'Barcode updated successfully',
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => this.fetchStock());
            },
            dataTable() {
                $("#stock_table").DataTable({
                    dom: "Bfrtip",
                    buttons: ["copy","csv","excel"],
                });
            },
            clearDataTable() {
                //
                const table = $("#stock_table").DataTable();
                table.destroy();
            },
        }
    }
</script>
