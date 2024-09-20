<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="addCourier" tabindex="-1" role="dialog" aria-labelledby="addCourierTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Add New Courier Service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <h5>Courier Service Name <span class="text-danger">*</span></h5>
                            <input type="text" class="form-control" placeholder="Courier Service Name"
                                v-model="courierName">
                        </div>

                        <div class="col-md-6 mt-3">
                            <h5>Courier Contact Person Name <span class="text-danger">*</span></h5>
                            <input type="text" class="form-control" placeholder="Courier Contact Person Name"
                                v-model="contactPerson">
                        </div>


                        <div class="col-md-6 mt-3">
                            <h5>Courier Contact Person Number <span class="text-danger">*</span></h5>
                            <input type="text" class="form-control" placeholder="Courier Contact Person Number"
                                v-model="contactPersonNumber">
                        </div>

                        <div class="col-md-12 row mt-5">
                            <div class="col-md-6">
                                <h5>Select Category <span class="text-danger">*</span></h5>
                            </div>
                            <div class="col-md-6">
                                <a href="#" data-toggle="modal" data-target="#addCourierCategory"
                                    class="btn btn-outline-primary"
                                    style="height: 17px; line-height: 1px; padding: 8px; float: right">Add
                                    New Category</a>
                            </div>
                            <div class="col-md-12">
                                <code>Select the method used to calculate the shipping rate. This selection determines how the shipping cost will be calculated for the items in your cart.</code>
                            </div>


                            <div v-for="(category, index) in categoriesList" :key="index"
                                class="col-md-12 row mt-3 category-select-wrapper">
                                <div class="col-md-10">
                                    <v-select v-model="category.selected" :options="categories"
                                        @input="fetchRange(category.selected, index)"></v-select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary" @click="addCategory"><i
                                            class="fa fa-plus"></i></button>
                                    <button class="btn btn-danger " v-if="categoriesList.length > 1"
                                        @click="removeCategory(index)"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>

                            <!-- Weight Input and Range Calculation -->
                            <div class="col-md-12 mt-5">
                                <h6>Test Your Shipping Cost</h6>
                                <div class="form-group">
                                    <label for="weight">Enter Weight (kg)</label>
                                    <input type="text" class="form-control" v-model="testWeight" id="weight"
                                        @keypress="onlyNumber" placeholder="Enter weight in kg">
                                </div>
                                <div v-if="selectedRange">
                                    <h6>Based on your weight, the range is: {{ selectedRange.minimum_quantity }} - {{
                                        selectedRange.maximum_quantity }} kg</h6>
                                    <h6>Total Shipping Cost: {{ calculateTotalCostForWeight(selectedRange) }}</h6>
                                </div>
                                <div v-else-if="testWeight">
                                    <h6>No valid range found for the entered weight.</h6>
                                </div>
                                <div v-if="selectedRange">
                                    Best Offer:
                                    <ul>
                                        <li>Category: {{ categories.find(cat => cat.code === selectedRange.category_id).label }}</li>
                                        <li>Minimum Quantity: {{ selectedRange.minimum_quantity }}</li>
                                        <li>Maximum Quantity: {{ selectedRange.maximum_quantity }}</li>
                                        <li>Base Rate: {{ selectedRange.base_rate }}</li>
                                        <li>Total Cost: {{ calculateTotalCostForWeight(selectedRange) }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="handleSubmit()" v-if="!loader" class="btn btn-primary">Add
                            Courier Service</button>
                        <button type="button" v-else class="btn btn-primary btn-progress disabled">Add Shipping
                            Class</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AddShippingClass',
    props: ['loader', 'categories', 'ranges'],
    data() {
        return {
            courierName: '',
            contactPerson: '',
            contactPersonNumber: '',
            selectCategory: { code: 0, label: 'Select from the following' },
            categoriesList: [
                { selected: { code: 0, label: 'Select from the following' } },  // Initialize with one empty category selection
            ],
            testWeight: ''
        }
    },
    computed: {
        selectedRange: {
            get() {
                // Initialize minimum price and best range
                let minPrice = Infinity;
                let bestRange = null;
                console.log("here");

                // Check if ranges are loaded
                if (Object.keys(this.ranges).length > 0) {

                    // Iterate over all categories and ranges
                    Object.values(this.ranges).forEach((categoryRanges) => {

                        categoryRanges.forEach((range) => {

                            // Check if testWeight falls within the range
                            if (this.testWeight >= parseFloat(range.minimum_quantity) && this.testWeight <= parseFloat(range.maximum_quantity)) {
                                // Calculate total cost for this range
                                const totalCost = this.calculateTotalCostForWeight(range);

                                // Update minimum price and best range if necessary
                                if (parseFloat(totalCost) < minPrice) {
                                    minPrice = parseFloat(totalCost);
                                    bestRange = range;
                                }
                            }
                        });
                    });
                }

                return bestRange;
            }
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
        calculateTotalCostForWeight(range) {
            let baseAmount;

            if (this.testWeight > parseFloat(range.minimum_quantity) && range.per_kg_rate > 0) {
                // Calculate extra weight beyond the minimum quantity
                const extraWeight = parseFloat((this.testWeight - parseFloat(range.minimum_quantity)).toFixed(2));  // Fix precision to 2 decimal places

                // Check if there is any extra weight (even a small fraction)
                if (extraWeight > 0) {
                    // Calculate steps based on per_kg (e.g., 0.5 kg steps)
                    const steps = Math.floor(extraWeight / parseFloat(range.per_kg)) + 1;  // Start counting from the first extra step

                    // Multiply steps by the per_kg_rate to get the extra cost
                    const extraCost = steps * parseFloat(range.per_kg_rate);

                    // Total cost is base rate plus extra cost
                    baseAmount = parseFloat(range.base_rate) + extraCost;
                }

            } else {
                baseAmount = parseFloat(range.base_rate);
            }


            const fcTax = (parseFloat(range.fac_tax) / 100) * baseAmount;
            const gstTax = (parseFloat(range.gst_tax) / 100) * (baseAmount + fcTax);
            return (baseAmount + fcTax + gstTax).toFixed(2);
        },
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        addCategory() {
            // Add a new category object to the categoriesList array
            this.categoriesList.push({ selected: { code: 0, label: 'Select from the following' } });
        },
        removeCategory(index) {
            const removedCategoryId = this.categoriesList[index].selected.code;
            // Remove the category from the categoriesList array
            this.categoriesList.splice(index, 1);
            // Remove the corresponding ranges
            delete this.ranges[removedCategoryId];
        },
        fetchRange(selectedCategory, index) {
            if (selectedCategory.code != 0) {
                this.$emit('fetchRange', { category: selectedCategory })
            }
        },
        calculateTotalCost() {
            const unitQuantity = 10; // Example quantity of units for calculation

            if (this.rateMethod.code === 1) {
                return this.minimumOrder ? `Free for orders over ${this.minimumOrder}` : 'N/A';
            } else if (this.rateMethod.code === 2) {
                return this.flatRate !== null ? `${this.flatRate}` : 'N/A';
            } else if (this.rateMethod.code === 3 || this.rateMethod.code === 4) {
                if (this.baseRate !== null && this.ratePerUnit !== null) {
                    const totalCost = parseFloat(this.baseRate) + (parseFloat(this.ratePerUnit) * unitQuantity);
                    return `${totalCost} (Base Rate: ${this.baseRate} + ${this.ratePerUnit} per unit for ${unitQuantity} units)`;
                }
                return 'N/A';
            } else {
                return 'N/A';
            }
        },
        handleSubmit() {
            let vm = this;
            if (vm.courierName == '') {
                return swal({
                    title: "Error",
                    text: "Please add some class name, thanks.",
                    icon: "error",
                    timer: 3000,
                });
            }

            if (vm.contactPerson == '') {
                return swal({
                    title: "Error",
                    text: "Please add contact person name, thanks.",
                    icon: "error",
                    timer: 3000,
                });
            }

            if (vm.contactPersonNumber == '') {
                return swal({
                    title: "Error",
                    text: "Please add contact person number, thanks.",
                    icon: "error",
                    timer: 3000,
                });
            }
            const data = {
                name: vm.courierName,
                contactPerson: vm.contactPerson,
                contactPersonNumber: vm.contactPersonNumber,
                categories: vm.categoriesList.map((category) => ({
                    code: category.selected.code,
                    label: category.selected.label,
                })),
            };

            vm.$emit('addNewCourier', data);
        },
        close() {
            this.courierName = '';
            this.contactPersonNumber = '';
            this.contactPerson = '';
            this.selectCategory = { code: 0, label: 'Select from the following' };
            this.categoriesList = [
                { selected: { code: 0, label: 'Select from the following' } },  // Initialize with one empty category selection
            ];
            this.testWeight = '';

        }
    },
    watch: {
        ranges(newValue) {
            // Recompute selectedRange when ranges changes
            this.selectedRange;
        }
    }
}
</script>
