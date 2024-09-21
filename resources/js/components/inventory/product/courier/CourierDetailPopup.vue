<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="courierDetailPopup" tabindex="-1" role="dialog" aria-labelledby="courierDetailPopup"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Courier Service Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <h6>Courier Service Name </h6>
                            <h5>{{ details.courier_name }}</h5>
                        </div>

                        <div class="col-md-6 mt-3">
                            <h6>Courier Contact Person Name </h6>
                            <h5>{{ details.contact_person }}</h5>
                        </div>


                        <div class="col-md-6 mt-3">
                            <h6>Courier Contact Person Number </h6>
                            <h5>{{ details.contact_person_contact }}</h5>
                        </div>

                        <div class="col-md-12 row mt-5">
                            <div class="col-md-6">
                                <h5>Packages</h5>
                            </div>
                            <div class="col-md-6">
                                <a href="#" data-toggle="modal" data-target="#addCourierCategory"
                                    class="btn btn-outline-primary"
                                    style="height: 17px; line-height: 1px; padding: 8px; float: right">Add
                                    New Package</a>
                            </div>

                            <div class="col-md-12" v-if="details.categories">
                                <ul>
                                  <li v-for="category in details.categories" :key="category.id">
                                    <b>{{ category.name }}</b> - Internal label <b>{{ category.internal_label }}</b>
                                    <ul>
                                      <li v-for="range in category.ranges" :key="range.id">
                                        Range : {{ range.minimum_quantity }} - {{ range.maximum_quantity }} kg:
                                        Base Rate: {{ range.base_rate }},
                                        Per Kg Rate: {{ range.per_kg_rate }},
                                        Total: {{ range.total }}
                                      </li>
                                    </ul>
                                  </li>
                                </ul>
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
                                        <li>Category: {{ details.categories.find(cat => cat.id === selectedRange.category_id).name }} - Internal Label : {{ details.categories.find(cat => cat.id === selectedRange.category_id).internal_label }}</li>
                                        <li>Minimum Quantity: {{ selectedRange.minimum_quantity }}</li>
                                        <li>Maximum Quantity: {{ selectedRange.maximum_quantity }}</li>
                                        <li>Base Rate: {{ selectedRange.base_rate }}</li>
                                        <li>Total Cost: {{ calculateTotalCostForWeight(selectedRange) }}</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-12 mt-2" v-if="selectedRange && details.categories">
                                <table class="table table-striped">
                                    <thead>
                                      <tr>
                                        <th>Weight (kg)</th>
                                        <th v-for="category in details.categories" :key="category.id">{{ category.name }}</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr v-for="(weight, index) in weights" :key="index">
                                        <td>{{ weight }}</td>
                                        <td v-for="category in details.categories" :key="category.id">
                                          {{ calculateTotalCostForWeight({ testWeight: weight, range: category.ranges.find(r => weight >= parseFloat(r.minimum_quantity) && weight <= parseFloat(r.maximum_quantity)) }) }}
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'CourierDetailPopup',
    props: ['loader', 'details', 'ranges'],
    data() {
        return {
            courierName: '',
            contactPerson: '',
            contactPersonNumber: '',
            selectCategory: { code: 0, label: 'Select from the following' },
            categoriesList: [
                { selected: { code: 0, label: 'Select from the following' } },  // Initialize with one empty category selection
            ],
            testWeight: '',
            weights: [],
        }
    },
    mounted() {
        for (let i = 0.5; i <= 15; i += 0.5) {
        this.weights.push(i);
        }
    },
    computed: {
        selectedRange() {
            if (!this.ranges || !this.testWeight || this.testWeight === '') return null;

            let minPrice = Infinity;
            let bestRange = null;

            try {
            Object.values(this.ranges).forEach((categoryRanges) => {
                categoryRanges.forEach((range) => {
                if (this.testWeight >= parseFloat(range.minimum_quantity) && this.testWeight <= parseFloat(range.maximum_quantity)) {
                    const totalCost = this.calculateTotalCostForWeight(range);
                    if (parseFloat(totalCost) < minPrice) {
                    minPrice = parseFloat(totalCost);
                    bestRange = range;
                    }
                }
                });
            });
            } catch (error) {
            console.error('Error calculating selected range:', error);
            }

            return bestRange;
        }
        },
    methods: {

        calculateTotalCostForWeight({ testWeight, range }) {
            if (!range) return 0;

            let baseAmount = parseFloat(range.base_rate);
            const weightDiff = testWeight - parseFloat(range.minimum_quantity);

            if (weightDiff > 0 && range.per_kg_rate > 0) {
                const extraWeight = parseFloat(weightDiff.toFixed(2));
                const weightSteps = Math.ceil(extraWeight / parseFloat(range.per_kg));
                const extraCost = weightSteps * parseFloat(range.per_kg_rate);
                baseAmount += extraCost;
            }

            const facTax = (parseFloat(range.fac_tax) / 100) * baseAmount;
            const gstTax = (parseFloat(range.gst_tax) / 100) * (baseAmount + facTax);
            return (baseAmount + facTax + gstTax).toFixed(2);
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
}
</script>
