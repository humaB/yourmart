<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
              <div class="card card-primary">
                <TableHeader :tableHeader="tableHeader" />

                <div class="card-body">
                  <!-- Table -->
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>Product</th>
                                        <th>Quantity</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in products" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.product.title }}</td>
                                        <td>{{ item.stock}}</td>
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

    export default {
        name : 'StoreStockPage',
        components: {
            TableHeader,
        },
        data() {
            return {
                api_url: window.location.origin + process.env.MIX_API_URL,
                tableHeader: {
                    heading: "Product Stock",
                },
                products : [],
            };
        },
        created(){
            this.fetchStock();
        },
        methods : {
            fetchStock(){
                let vm = this;
                axios
                .get(this.api_url + "inventory/products/store/stocks")
                .then((response) => {
                    const results = response.data.response;
                    vm.products = results;
                })
                .catch((err) => this.fetchStock());
            },
        }
    }
</script>
