<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">
                    <TableHeader :tableHeader="tableHeader" />

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <!-- Scan Barcode -->
                                        <div class="row">
                                            <!-- <div class="col-md-6 mb-3">
                                                <label for="">Select Dropshipper <span class="text-danger">( optional )</span></label>
                                                <v-select :options="dropshippers" v-model="selectedDropshipper" @input="fetchDropshipperDetails()"></v-select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="">Select Shop <span class="text-danger">( optional )</span></label>
                                                <v-select :options="shops" v-model="selectedShop"></v-select>
                                            </div> -->

                                            <div class="col-md-6 mb-3">
                                                <label for="">Customer Name <span class="text-danger">( optional )</span></label>
                                                <input type="text" class="form-control" v-model="customerName">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="">Customer Number <span class="text-danger">( optional )</span></label>
                                                <input type="text" class="form-control" v-model="customerPhone">
                                            </div>

                                            <!-- <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <input type="text" v-model="barcode" @keyup.enter="scanBarcode"
                                                        placeholder="Scan Barcode" class="form-control" />
                                                    <div class="input-group-append">
                                                        <button @click="scanBarcode" class="btn btn-primary">
                                                            <i class="fas fa-barcode"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div> -->

                                            <div class="col-md-10 mb-3">
                                                    <v-select :options="productsDropdown" v-model="selectedProduct"></v-select>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <button @click="scanBarcode('1')" class="btn btn-primary">
                                                    <i class="fa fa-plus"></i> Press To Add
                                                </button>
                                            </div>
                                        </div>


                                        <!-- Total and Checkout -->
                                        <div class="row">
                                            <div class="col-7">
                                                <!-- Product List -->
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Quantity</th>
                                                            <th>Price</th>
                                                            <th>Total</th>
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
                                                                    @change="updateTotal" class="form-control" />
                                                            </td>
                                                            <td>{{ product.price }}</td>
                                                            <td>{{ product.total }}</td>
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
                                            <div class="col-5">
                                                <!-- Order Summary -->
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5>Order Summary</h5>
                                                        <hr />
                                                        <div class="row">
                                                            <div class="col-6">Total Item Quantity:</div>
                                                            <div class="col-6">{{ totalQuantity }}</div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-6">Discount:</div>
                                                            <div class="col-6"><input type="text" v-model="discount" class="form-control"></div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-6">Subtotal:</div>
                                                            <div class="col-6">{{ formatPrice(total) }}</div>
                                                        </div>
                                                        <hr />
                                                        <!-- Payment Methods -->
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h6>Payment Methods</h6>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">Cash:</div>
                                                            <div class="col-6">
                                                                <input type="test" @keypress="onlyNumber" v-model="cashPayment"
                                                                    class="form-control" />
                                                            </div>
                                                        </div>
                                                        <hr />
                                                        <div class="row">
                                                            <div class="col-12">Bank:</div>
                                                            <div class="col-md-6">
                                                                <select name="" id="" class="form-control"
                                                                    v-model="selectedBank">
                                                                    <option value="0">Select from the following</option>
                                                                    <option :value="bank.id" v-for="bank in banks"
                                                                        :key="bank.id">{{ bank.name }}</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <input type="text" @keypress="onlyNumber" v-model="bankPayment"
                                                                    class="form-control" />
                                                                <input type="file" @change="uploadProof" />
                                                            </div>

                                                            <div class="col-md-12 mt-3">
                                                                <p>Bank Name : <span style="float:right"><b>Meezan Bank</b></span></p>
                                                                <p>Account Name : <span style="float:right"><b>ECOMSTARTUPS</b></span></p>
                                                                <p>Account # : <span style="float:right"><b>04090110227095</b></span></p>
                                                                <p>Account IBAN : <span style="float:right"><b>PK37MEZN0004090110227095</b></span></p>
                                                            </div>
                                                        </div>
                                                        <hr />
                                                        <div class="row">
                                                            <div class="col-6 h5">Total Payment:</div>
                                                            <div class="col-6 h5">{{ formatPrice( totalPayment ) }}</div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6 h5">Remaining:</div>
                                                            <div class="col-6 h5">{{ formatPrice( change ) }}</div>
                                                        </div>
                                                        <button class="btn btn-primary w-100 mt-2" v-if="!loader" @click="checkOut()">
                                                            <i class="fas fa-credit-card"></i> Checkout
                                                        </button>

                                                        <button class="btn btn-primary w-100 mt-2 btn-progress disabled" v-else>
                                                            <i class="fas fa-credit-card"></i> Checkout
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TableHeader from "../../../components/table/TableHeaderComponent.vue";

export default {
    name: 'StoreCheckoutPage',
    components: {
        TableHeader
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            api_url: window.location.origin + process.env.MIX_API_URL,
            barcode: "",
            products: [],
            tableHeader: {
                heading: "POS System",
            },
            paymentMethod: "cash",
            proofOfPayment: null,
            cashPayment: 0,
            bankPayment: 0,
            selectedBank: '0',
            banks: [],
            loader : false,
            selectedProduct : { code : 0 , label : "Select from the following"},
            productsDropdown : [],
            dropshippers : [],
            selectedDropshipper : { code : 0 , label : "Select from the following"},
            dropshipperDetails : {},
            selectedShop : { code : 0 , label : "Select from the following"},
            shops : [],
            customerName : '',
            customerPhone : '',
            discount : 0
        };
    },
    created() {
        this.fetchBanks();
        this.fetchProducts();
        this.fetchDropshippers();
    },
    computed: {
        total() {
            return this.products.reduce((acc, product) => acc + parseFloat(product.total), 0) - parseFloat(this.discount);
        },
        totalQuantity() {
            return this.products.reduce((acc, product) => acc + parseFloat(product.quantity), 0);
        },
        change() {
            return this.totalPayment - this.total;
        },
        totalPayment() {
            return parseFloat(this.cashPayment || 0) + parseFloat(this.bankPayment || 0);
        },
    },
    methods: {
        formatPrice: function formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        fetchDropshippers() {
            axios.get(this.api_url + "dropshippers/drop-down")
                .then((res) => {
                    const results = res.data.response;
                    this.dropshippers = results;
                });
        },
        fetchDropshipperDetails(){

            axios.post(this.api_url + "dropshippers/details", { id : this.selectedDropshipper.code })
                .then((res) => {
                    const results = res.data.response;
                    this.dropshipperDetails = results[0];

                    this.customerName = this.dropshipperDetails.full_name;
                    this.customerPhone = this.dropshipperDetails.whatsapp_number;

                    this.selectedShop = { code : 0 , label : "Select from the following"};
                    this.shops =   this.dropshipperDetails.shops.map( ( arr ) => {
                        return {
                            code : arr.id,
                            label : arr.store_name
                        }
                    })
                });
        },
        fetchBanks() {
            axios.get(this.api_url + "accounts/heads/banks")
                .then((res) => {
                    const results = res.data.response;
                    this.banks = results;
                });
        },
        fetchProducts(){
            axios.get(this.api_url + "inventory/products/complete-drop-down")
                .then((res) => {
                    const results = res.data.response;
                    this.productsDropdown = results;
                });
        },
        uploadProof(event) {
            this.proofOfPayment = event.target.files[0];
        },
        scanBarcode( product = null ) {

            let vm = this;
            let data;
            if( product == '1'){
                data = {
                    id : vm.selectedProduct.code
                }
            }
            else{
                data = {
                    barcode: vm.barcode
                }
            }
            axios.post(this.api_url + "inventory/products/scanned-data", data)
                .then((res) => {
                    const result = res.data.response[0];

                    const product = {
                        id: result.id,
                        name: result.product.title,
                        image: vm.public_url + 'storage/uploads/inventory/products/media/' + result.product.hero_image,
                        price: result.sale_price,
                        discounts : result.product.discounts
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
                            discounts: product.discounts, // Calculate initial price based on quantity
                            price: product.price,
                            total: product.price,
                        });
                    }

                    this.barcode = "";
                    this.selectedProduct = { code : 0, label : 'Select from the following'}

                })
                .catch()
        },
        calculateDiscountedPrice(aLLdiscounts, quantity, price) {
            // Get the product's discount rules
            const discounts = aLLdiscounts;

            // Initialize the price as the regular sale price
            let discountedPrice = price;

            discounts.forEach(discount => {
                // Use the isInRange function to check if the quantity matches the discount range
                if (this.isInRange(quantity, discount.quantity)) {
                    discountedPrice = discount.price;
                }
            });

            return discountedPrice;
        },
            // Reusable function to check if quantity falls within a range
        isInRange(quantity, range) {
            // Check if range is "51+" or a standard range like "20-31"
            if (range.includes('+')) {
                const min = parseInt(range.split('+')[0], 10);
                return quantity >= min;
            } else {
                const [min, max] = range.split('-').map(Number);
                return quantity >= min && quantity <= max;
            }
        },
        updateTotal() {
            this.products.forEach((product) => {
                // Calculate the new price based on the current quantity
                const newPrice = this.calculateDiscountedPrice(product.discounts, product.quantity , product.price);

                // Update the product's price
                product.price = newPrice;

                // Recalculate the total using the updated price
                product.total = parseFloat(newPrice) * parseFloat(product.quantity);
            });
        },
        removeProduct(index) {
            this.products.splice(index, 1);
        },
        checkOut() {
            let vm = this;
            if (vm.products.length == 0) {
                return swal({
                    title: "Required",
                    text: 'Please add some products in cart first',
                    icon: "error",
                    timer: 3000,
                });
            }

            if( vm.selectedDropshipper.code != 0){
                if(vm.selectedShop.code == 0){
                    return swal({
                        title: "Required",
                        text: 'Please select shop first',
                        icon: "error",
                        timer: 3000,
                    });
                }
            }

            if (vm.totalPayment != vm.total) {
                return swal({
                    title: "Required",
                    text: 'Please check paid amount first',
                    icon: "error",
                    timer: 3000,
                });
            }

            if (vm.bankPayment > 0) {
                if (vm.selectedBank == '0') {
                    return swal({
                        title: "Required",
                        text: 'Please select bank first',
                        icon: "error",
                        timer: 3000,
                    });
                }
                if (!vm.proofOfPayment) {
                    return swal({
                        title: "Required",
                        text: 'Please upload proof of payment',
                        icon: "error",
                        timer: 3000,
                    });
                }
            }

            vm.loader = true;
            const fd = new FormData();
            vm.products.forEach((product, index) => {
                Object.keys(product).forEach((key) => {
                    fd.append(`products[${index}][${key}]`, product[key]);
                });
            });
            fd.append('paymentAttachment', vm.proofOfPayment);
            fd.append('cash', vm.cashPayment);
            fd.append('bank', vm.bankPayment);
            fd.append('bankAccount', vm.selectedBank);

            fd.append('customer', vm.customerName);
            fd.append('phone', vm.customerPhone);
            fd.append('dropshipper', vm.selectedDropshipper.code);
            fd.append('shop', vm.selectedShop.code);

            fd.append('discount', vm.discount)
            fd.append('total', vm.total);

            axios.post(this.api_url + "inventory/products/direct-checkout", fd)
                .then((res) => {
                    vm.loader = false;

                    vm.cashPayment = 0;
                    vm.bankPayment = 0;
                    vm.selectedBank = 0;
                    vm.proofOfPayment = '';

                    vm.selectedDropshipper = { code : 0 , label : 'Select from the following'};
                    vm.selectedShop = { code : 0 , label : 'Select from the following'};
                    vm.customerName = "";
                    vm.customerPhone = "";

                    vm.products = [];
                    $("input[type=file]").val('');

                    return swal({
                        title: "Success",
                        text: 'Order Sent to Inventory manager',
                        icon: "success",
                        timer: 3000,
                    });

                })

        }
    },
};
</script>
