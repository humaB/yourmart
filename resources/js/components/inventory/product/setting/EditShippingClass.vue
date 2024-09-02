<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="editShippingClassDetails" tabindex="-1" role="dialog"
            aria-labelledby="editShippingClassDetailsTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Edit Shipping Class Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <h5>Shipping Class Name <span class="text-danger">*</span></h5>
                            <input type="text" class="form-control" placeholder="Add Shipping Class"
                                v-model="className">
                            <code>Length ( {{ classNameLength }} / 50 )</code>
                        </div>
                        <div class="col-md-12 mt-2">
                            <h5>Description <span class="text-danger">*</span></h5>
                            <input type="text" class="form-control"
                                placeholder="Please enter short description of shipping class"
                                v-model="shortDescription">
                            <code>Length ( {{ shortDescriptionLength }} / 100 )</code>
                        </div>

                        <div class="col-md-12 mt-5">
                            <h5>Rate Method <span class="text-danger">*</span></h5>
                            <v-select v-model="rateMethod" :options="methods">
                            </v-select>
                            <code>Select the method used to calculate the shipping rate. Options may include flat rate, weight-based rate, or dimension-based rate. This selection determines how the shipping cost will be calculated for the items in your cart.</code>
                        </div>

                        <!-- Conditional Fields Based on Rate Method Code -->
                        <div v-if="rateMethod.code === 1" class="col-md-12 mt-5">
                            <h6>Free Shipping Configuration</h6>
                            <p class="text-muted">Please enter the minimum order amount required for free shipping and
                                the rate that will be applied.</p>
                            <input type="text" @keypress="onlyNumber" v-model="minimumOrder" class="form-control"
                                placeholder="Minimum Order Amount" />
                            <input type="text" @keypress="onlyNumber" v-model="rate" class="form-control mt-2"
                                placeholder="Rate" />
                        </div>

                        <div v-if="rateMethod.code === 2" class="col-md-12 mt-5">
                            <h6>Flat Rate Configuration</h6>
                            <p class="text-muted">Enter the flat rate that will be applied regardless of weight or
                                dimension.</p>
                            <input type="text" @keypress="onlyNumber" v-model="flatRate" class="form-control"
                                placeholder="Flat Rate" />
                        </div>

                        <div v-if="rateMethod.code === 3" class="col-md-12 mt-5">
                            <h6>Weight-Based Shipping Configuration</h6>
                            <p class="text-muted">Enter the base rate and the rate per unit of weight. The total cost
                                will be calculated as: Base Rate + (Rate Per Unit * Weight).</p>
                            <input type="text" @keypress="onlyNumber" v-model="baseRate" class="form-control"
                                placeholder="Base Rate" />
                            <input type="text" @keypress="onlyNumber" v-model="ratePerUnit" class="form-control mt-2"
                                placeholder="Rate Per Unit (e.g., per kg)" />
                            <input type="text" @keypress="onlyNumber" v-model="minimumOrder" class="form-control mt-2"
                                placeholder="Minimum Weight" />
                        </div>

                        <div v-if="rateMethod.code === 4" class="col-md-12 mt-5">
                            <h6>Dimension-Based Shipping Configuration</h6>
                            <p class="text-muted">Enter the base rate and the rate per unit of dimension. The total cost
                                will be calculated as: Base Rate + (Rate Per Unit * Dimension).</p>
                            <input type="text" @keypress="onlyNumber" v-model="baseRate" class="form-control"
                                placeholder="Base Rate" />
                            <input type="text" @keypress="onlyNumber" v-model="ratePerUnit" class="form-control mt-2"
                                placeholder="Rate Per Unit (e.g., per cubic meter)" />
                            <input type="text" @keypress="onlyNumber" v-model="minimumOrder" class="form-control mt-2"
                                placeholder="Minimum cubic meter" />
                        </div>

                        <div v-if="rateMethod.code === 3 || rateMethod.code === 4" class="col-md-12 mt-5">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Rate Method</th>
                                        <th>Base Rate</th>
                                        <th>Rate Per Unit</th>
                                        <th v-if="rateMethod.code === 3">Minimum Weight</th>
                                        <th v-if="rateMethod.code === 4">Minimum Cubic Meter</th>
                                        <th>Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ rateMethod.label }}</td>
                                        <td v-if="rateMethod.code === 3 || rateMethod.code === 4">{{ baseRate }}</td>
                                        <td v-if="rateMethod.code === 3 || rateMethod.code === 4">{{ ratePerUnit }}</td>
                                        <td
                                            v-if="rateMethod.code === 1 || rateMethod.code === 3 || rateMethod.code === 4">
                                            {{ minimumOrder }}</td>
                                        <td v-if="rateMethod.code === 2">{{ flatRate }}</td>
                                        <td>{{ calculateTotalCost() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="handleSubmit()" v-if="!loader" class="btn btn-primary">Update
                            Shipping
                            Class</button>
                        <button type="button" v-else class="btn btn-primary btn-progress disabled">Update Shipping
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
    name: 'EditShippingClass',
    props: ['loader', 'details'],
    data() {
        return {
            className: '',
            shortDescription: '',
            rateMethod: { code: 0, label: 'Select from the following' },
            methods: [
                { code: 1, label: 'Free Shipping' },
                { code: 2, label: 'Flat Rate' },
                { code: 3, label: 'Weight Based' },
                { code: 4, label: 'Dimension Based' }
            ],
            minimumOrder: null,
            rate: null,
            flatRate: null,
            baseRate: null,
            ratePerUnit: null,
        }
    },
    computed: {
        classNameLength() {
            return this.className.length > 50 ? 50 : this.className.length;
        },
        shortDescriptionLength() {
            return this.shortDescription.length > 100 ? 100 : this.shortDescription.length;
        }
    },
    mounted() {
        this.rateMethod = this.methods.find(method => method.label === this.details.rate_type) || { code: 0, label: 'Select from the following' };
    },
    methods: {
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
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
        handleSubmit(){
            let vm = this;
            if (vm.className == '') {
                return swal({
                    title: "Error",
                    text: "Please add some class name, thanks.",
                    icon: "error",
                    timer: 3000,
                });
            }

            if (vm.shortDescription == '') {
                return swal({
                    title: "Error",
                    text: "Please add some description, thanks.",
                    icon: "error",
                    timer: 3000,
                });
            }
            // Validate the form based on the selected rate method
            if (vm.rateMethod.code === 1) { // Free Shipping
                if (vm.minimumOrder === null || vm.rate === null) {
                    return swal({
                        title: "Error",
                        text: "Please fill in both the Minimum Order Amount and Rate for Free Shipping.",
                        icon: "error",
                        timer: 3000,
                    });
                }
            } else if (vm.rateMethod.code === 2) { // Flat Rate
                if (vm.flatRate === null) {
                    return swal({
                        title: "Error",
                        text: "Please enter the Flat Rate.",
                        icon: "error",
                        timer: 3000,
                    });
                }
            } else if (vm.rateMethod.code === 3) { // Weight-Based
                if (vm.baseRate === null || vm.ratePerUnit === null) {
                    return swal({
                        title: "Error",
                        text: "Please fill in both the Base Rate and Rate Per Unit for Weight-Based Shipping.",
                        icon: "error",
                        timer: 3000,
                    });
                }
            } else if (vm.rateMethod.code === 4) { // Dimension-Based
                if (vm.baseRate === null || vm.ratePerUnit === null) {
                    return swal({
                        title: "Error",
                        text: "Please fill in both the Base Rate and Rate Per Unit for Dimension-Based Shipping.",
                        icon: "error",
                        timer: 3000,
                    });
                }
            } else {
                return swal({
                    title: "Error",
                    text: "Please select a valid Rate Method.",
                    icon: "error",
                    timer: 3000,
                });
            }

            const data = {
                id : vm.details.id,
                name: vm.className,
                description: vm.shortDescription,
                rate_type: vm.rateMethod.label, // Use the code of the selected rate method
                minimum_order: vm.minimumOrder,
                rate: vm.rate,
                flat_rate: vm.flatRate,
                base_rate: vm.baseRate,
                rate_per_unit: vm.ratePerUnit
            };

            vm.$emit('editClass', data);
        },
    },
    watch: {
        details: {
            immediate: true,
            handler(newVal) {
                if (newVal) {
                    this.className = newVal.name || '';
                    this.shortDescription = newVal.description || '';
                    this.minimumOrder = newVal.details.minimum_order || null;
                    this.rate = newVal.details.flat_rate || null;
                    this.flatRate = newVal.details.flat_rate || null;
                    this.baseRate = newVal.details.base_rate || null;
                    this.ratePerUnit = newVal.details.rate_per_unit || null;
                    this.rateMethod = this.methods.find(method => method.label === newVal.details.rate_type) || { code: 0, label: 'Select from the following' };
                }
            }
        }
    },
}
</script>
