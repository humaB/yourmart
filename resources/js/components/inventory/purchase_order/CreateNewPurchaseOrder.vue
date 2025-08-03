<template>
    <div>
        <div class="modal fade" id="createNewPurchaseOrder" tabindex="-1" role="dialog" aria-labelledby="groupForm"
            aria-hidden="true" data-backdrop="false" style="background-color: rgba(0, 0, 0, 0.2)">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 85%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Create Purchase Order</h6>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">


                            <div class="form-group form-float col-6 col-md-3 col-lg-4">
                                <div class="form-line">
                                    <h6 class="form-label">Select Supplier <span style="color: red">*</span></h6>
                                    <v-select :options="suppliers" v-model="vendor">
                                    </v-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h6 class="form-label">Advance <span style="color: red">* ( in Percentage % )</span>
                                </h6>
                                <input type="text" class="form-control" v-model="advance" required
                                    @keypress="onlyNumber" />
                            </div>
                            <div class="col-md-3">
                                <h6 class="form-label">After Delivery <span style="color: red">* ( in Percentage %
                                        )</span></h6>
                                <input type="text" class="form-control" v-model="delivery" @keypress="onlyNumber"
                                    required />
                            </div>

                            <div class="form-group col-md-3">
                                <label><strong>Inventory Type</strong></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="inventoryType"
                                        id="supplierInventory" value="1" v-model="inventoryType">
                                    <label class="form-check-label" for="supplierInventory">
                                        Supplier Inventory
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="inventoryType"
                                        id="yourmartInventory" value="0" v-model="inventoryType">
                                    <label class="form-check-label" for="yourmartInventory">
                                        Yourmart Inventory
                                    </label>
                                </div>
                            </div>


                            <div v-for="(expense, index) in expenses" :key="index" class="row col-md-12">
                                <div class="form-group form-float col-md-5 mt-3">
                                    <div class="form-line">
                                        <h6>Select Product <span class="text-danger">*</span></h6>
                                        <v-select :options="products" v-model="expense.product" @search="searchProduct">
                                        </v-select>
                                        <code>Please Enter 3 or more characters to Search Product</code>
                                    </div>
                                </div>

                                <div class="form-group form-float col-md-2 mt-3">
                                    <h6>Rate <span class="text-danger">*</span></h6>
                                    <input type="text" style="font-size:22px" v-model="expense.rate"
                                        class="form-control" required @keypress="onlyNumber" />
                                </div>

                                <div class="form-group form-float col-md-2 mt-3">
                                    <h6>Quantity <span class="text-danger">*</span></h6>
                                    <input type="text" style="font-size:22px" v-model="expense.qty" class="form-control"
                                        required @keypress="onlyNumber" />
                                </div>

                                <!-- Display the total (rate * qty) next to the quantity input -->
                                <div class="form-group form-float col-md-2 mt-3">
                                    <h6>Total:</h6>
                                    <p style="font-size:22px">{{ expenseTotal(expense) }}</p>
                                </div>

                                <div class="form-group form-float col-md-1 mt-3">
                                    <label class="form-label">Action</label><br>
                                    <div class="form-line d-flex">

                                        <!-- Show plus button if it's the last row -->
                                        <button v-if="index === expenses.length - 1" type="button" @click="addExpense"
                                            class="btn btn-icon btn-info mr-2">
                                            <i class="fas fa-plus"></i>
                                        </button>

                                        <!-- Show remove button if there's more than one row -->
                                        <button v-if="expenses.length > 1" type="button" @click="removeExpense(index)"
                                            class="btn btn-icon btn-danger">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Section -->

                            <div class="col-md-12">
                                <h4 class="text-right">Summary</h4>
                                <div class="float-right col-md-4">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td>{{ subtotal }}</td>
                                        </tr>
                                        <tr>
                                            <td>Discount Amount:</td>
                                            <td><input type="text" v-model="discount" class="form-control"
                                                    @input="calculateTotals" @keypress="onlyNumber"></td>
                                        </tr>
                                        <tr>
                                            <td>Tax Amount:</td>
                                            <td><input type="text" v-model="tax" class="form-control"
                                                    @input="calculateTotals" @keypress="onlyNumber"></td>
                                        </tr>
                                        <tr>
                                            <td>Delivery Charges:</td>
                                            <td><input type="text" v-model="deliveryCharges" class="form-control"
                                                    @input="calculateTotals" @keypress="onlyNumber"></td>
                                        </tr>
                                        <tr>
                                            <td>Net Amount:</td>
                                            <td>{{ netAmount }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" @click="add()" class="btn btn-primary" v-if="!loader">
                            Create PO
                        </button>
                        <button type="button" class="btn btn-primary btn-progress disabled" v-else>
                            Create PO
                        </button>
                        <button type="button" @click="close()" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "CreateNewPurchaseOrder",
    props: ['products', 'suppliers', 'srID', 'categories', "loader"],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            api_url: window.location.origin + process.env.MIX_API_URL,
            expenses: [
                { product: { code: 0, label: "Select from the following" }, rate: '', qty: '' } // Initialize with one row
            ],
            vendor: { code: 0, label: "Select From the Following" },
            advance: 0,
            delivery: 0,
            discount: 0,
            tax: 0,
            deliveryCharges: 0,
            inventoryType : '0'
        };
    },
    computed: {
        // Calculate the subtotal (sum of all (rate * qty) values)
        subtotal() {
            return this.expenses.reduce((total, expense) => {
                const rate = parseFloat(expense.rate) || 0;
                const qty = parseFloat(expense.qty) || 0;
                return total + (rate * qty);
            }, 0).toFixed(2);
        },
        // Calculate the net amount: subtotal - discount + tax
        netAmount() {
            const subtotal = parseFloat(this.subtotal) || 0;
            const discount = parseFloat(this.discount) || 0;
            const tax = parseFloat(this.tax) || 0;
            const delivery = parseFloat(this.deliveryCharges) || 0;
            return (subtotal - discount + (tax + delivery)).toFixed(2);
        }
    },
    mounted() {
        this.$parent.$on("saved", (value) => {
            if (value) {
                this.close();
            }
        });
    },
    methods: {
        expenseTotal(expense) {
            const rate = parseFloat(expense.rate) || 0;
            const qty = parseFloat(expense.qty) || 0;
            return (rate * qty).toFixed(2);
        },
        calculateTotals() {
            // Trigger recalculation when discount or tax changes
        },
        searchProduct(search) {
            if (search.length >= 3) {
                this.$emit('searchProduct', { search })
            }
        },
        removeExpense(index) {
            if (this.expenses.length > 1) {
                this.expenses.splice(index, 1);
            } else {
                alert('You must have at least one product row.');
            }
        },
        addExpense() {
            this.expenses.push({ product: { code: 0, label: "Select from the following" }, rate: '', qty: '' });

        },
        add() {
            let vm = this;

            if (vm.vendor.code == '0') {
                return swal({
                    title: "Required",
                    text: "Please Select Vendor First",
                    icon: "error",
                    timer: 3000,
                });
            }

            if (vm.advance == 0 && vm.delivery == 0) {
                return swal({
                    title: "Required",
                    text: "Please add payment terms",
                    icon: "error",
                    timer: 3000,
                });
            }

            if (this.expenses.length === 0 || !this.expenses.some(expense => expense.product && expense.rate && expense.qty)) {
                return swal({
                    title: "Required",
                    text: "Please add a product, rate, and quantity.",
                    icon: "error",
                    timer: 3000,
                });
            }

            let fd = new FormData();
            fd.append('vendor', vm.vendor.code);
            fd.append('advance', vm.advance);
            fd.append('delivery', vm.delivery);
            fd.append('discount', vm.discount);
            fd.append('deliveryCharges', vm.deliveryCharges);
            fd.append('tax', vm.tax);
            fd.append('inventoryType', vm.inventoryType);

            // Append the expenses array (convert to JSON string)
            fd.append('expenses', JSON.stringify(vm.expenses));

            this.close();
            vm.$emit("addPO", fd);

        },

        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        formatPrice: function formatPrice(price) {
            var string = price.toString();
            return string.replace(/,/g, "").replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        close() {
            let vm = this;
            vm.vendor = { code: 0, label: "Select From the Following" };
            vm.discount = 0;
            vm.tax = 0;
            vm.advance = 0;
            vm.delivery = 0;
            vm.deliveryCharges = 0;
            vm.expenses = [
                { product: { code: 0, label: "Select from the following" }, rate: '', qty: '' } // Initialize with one row
            ];
        },
    },
};
</script>
