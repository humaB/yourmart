<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="adjustStock" tabindex="-1" role="dialog" aria-labelledby="adjustStockTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Adjust Stock</h5><br>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <code>This will have direct impact on stock numbers without accounts impact</code>
                        </div>
                            <div class="col-md-10 mb-3">
                                <v-select :options="productsDropdown" v-model="selectedProduct"></v-select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <button @click="scanBarcode('1')" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Press To Add
                                </button>
                            </div>
                            <div class="col-12">
                                <!-- Product List -->
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Action</th>
                                            <th>Remarks</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(product, index) in products" :key="index">
                                            <td>
                                                {{ product.name }}
                                                <br />
                                                <img :src="product.image" width="50" height="50"
                                                    alt="Product Image" />
                                            </td>
                                            <td>
                                                <input type="text" v-model="product.quantity"  @keypress="onlyNumber"
                                                     class="form-control" />
                                            </td>
                                            <td>
                                                <select name="" id="" v-model="product.action" class="form-control">
                                                    <option value="increment">Increment</option>
                                                    <option value="decrement">Decrement</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" v-model="product.remark">
                                            </td>

                                            <td>
                                                <button class="btn btn-danger"
                                                    @click="removeProduct(index)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="updateStock()" v-if="!loader">Update Stock</button>
                        <button type="button" class="btn btn-primary btn-progress disabled"  v-else>Update Stock</button>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    name: 'AdjustStock',
    props : ['productsDropdown', 'loader'],
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            selectedProduct: { code: 0, label: 'Select from the following' },
            products : [],
        }
    },
    methods: {
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        scanBarcode(product = null) {

            let vm = this;

            axios.post(this.api_url + "inventory/products/scanned-data", { id: vm.selectedProduct.code })
                .then((res) => {
                    const result = res.data.response[0];

                    const product = {
                        id: result.id,
                        name: result.product.title,
                        image: vm.public_url + 'storage/uploads/inventory/products/media/' + result.product.hero_image,
                        action : 'increment'
                    };

                    // Check if product already exists in the list
                    const existingProduct = this.products.find((p) => p.name === product.name);

                    if (existingProduct) {
                        // Increase quantity if product already exists
                        existingProduct.quantity += 1;
                        existingProduct.total = existingProduct.price * existingProduct.quantity;
                    } else {
                        // Add new product to the list
                        this.products.push({
                            ...product,
                            quantity: 1,
                            action : 'increment'
                        });
                    }
                    this.selectedProduct = { code: 0, label: 'Select from the following' }

                })
                .catch()
        },
        removeProduct(index) {
            this.products.splice(index, 1);
        },
        updateStock(){
            let vm = this;
            if (vm.products.length == 0) {
                return swal({
                    title: "Required",
                    text: 'Please add some products in table first',
                    icon: "error",
                    timer: 3000,
                });
            }

            const incrementActions = [];
            const decrementActions = [];

             // Categorize products by action type
            this.products.forEach(product => {
                if (product.action === 'increment') {
                    incrementActions.push(product);
                } else if (product.action === 'decrement') {
                    decrementActions.push(product);
                }
            });

            // Structured data to send to Laravel
            const payload = {
                increments: incrementActions,
                decrements: decrementActions
            };

            vm.$emit('adjustStock', payload);

            vm.products = [];
        }
    }
}
</script>
