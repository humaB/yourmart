<template>

    <!-- Modal -->
    <div class="modal fade" id="addCourierCategory" tabindex="-1" role="dialog" aria-labelledby="addCourierCategory"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">

                    <div class="col-md-12 mt-3">
                        <label for=""><b>Category Courier Name <span class="text-danger">*</span></b></label>
                        <input type="text" class="form-control" v-model="name">
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for=""><b>Internal Label <span class="text-danger">*</span></b></label>
                        <input type="text" class="form-control" v-model="internalLabel">
                    </div>

                    <!-- Category Ranges -->
                    <div v-for="(range, index) in ranges" :key="index" class="col-md-12 mt-4">
                        <h6>Range {{ index + 1 }} (Max 3 ranges)</h6>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="Min Quantity" v-model="range.minimum_quantity" @keypress="onlyNumber" />
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="Max Quantity" v-model="range.maximum_quantity" @keypress="onlyNumber" />
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="Base Rate" v-model="range.base_rate" @keypress="onlyNumber" />
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="Charge Per Kg" v-model="range.per_kg" @keypress="onlyNumber" />
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" placeholder="Rate Per Kg" v-model="range.per_kg_rate" @keypress="onlyNumber" />
                            </div>
                            <div class="col-md-1">
                                <input type="text" class="form-control" placeholder="FAC Tax" v-model="range.fc" @keypress="onlyNumber" />
                            </div>
                            <div class="col-md-1">
                                <input type="text" class="form-control" placeholder="GST" v-model="range.gst" @keypress="onlyNumber" />
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger mt-2" @click="removeRange(index)" v-if="ranges.length > 1">Remove Range</button>
                    </div>

                    <div class="col-md-12 mt-3">
                        <button type="button" @click="addRange()" class="btn btn-outline-success" :disabled="ranges.length >= 3">
                            Add Range (Max 3)
                        </button>
                    </div>

                      <!-- Impact Table -->
                      <div class="col-md-12 mt-5">
                        <h6>Impact on Shipping Costs</h6>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Min Quantity</th>
                                    <th>Max Quantity</th>
                                    <th>Base Rate</th>
                                    <th>Charge Per Kg</th>
                                    <th>Rate Per Kg</th>
                                    <th>FAC TAX</th>
                                    <th>GST TAX</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(range, index) in ranges" :key="index">
                                    <td>{{ range.minimum_quantity }}</td>
                                    <td>{{ range.maximum_quantity }}</td>
                                    <td>{{ range.base_rate }}</td>
                                    <td>{{ range.per_kg }}</td>
                                    <td>{{ range.per_kg_rate }}</td>
                                    <td>{{ range.fc }} %</td>
                                    <td>{{ range.gst }} %</td>
                                    <td>{{ calculateTotalAmount(range) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                             <!-- Weight Input and Range Calculation -->
                             <div class="col-md-12 mt-5">
                                <h6>Test Your Shipping Cost</h6>
                                <div class="form-group">
                                    <label for="weight">Enter Weight (kg)</label>
                                    <input type="number" class="form-control" v-model="testWeight" id="weight" @keypress="onlyNumber" placeholder="Enter weight in kg">
                                </div>
                                <div v-if="selectedRange">
                                    <h6>Based on your weight, the range is: {{ selectedRange.minimum_quantity }} - {{ selectedRange.maximum_quantity }} kg</h6>
                                    <h6>Total Shipping Cost: {{ calculateTotalCostForWeight(selectedRange) }}</h6>
                                </div>
                                <div v-else-if="testWeight">
                                    <h6>No valid range found for the entered weight.</h6>
                                </div>
                            </div>


                    <div class="col-md-12 mt-5 text-right">
                        <button type="button" class="btn btn-primary" v-if="!loader" @click="addNewCategory()">Add New Category</button>
                        <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New Category</button>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AddCourierCategory',
    props : ['loader', 'details'],
    data(){
        return {
            internalLabel : '',
            name : '',
            ranges: [
                {
                    minimum_quantity: '',
                    maximum_quantity: '',
                    base_rate: '',
                    per_kg: '',
                    per_kg_rate: '',
                    fc : '',
                    gst : ''
                },
            ],
            testWeight: '',  // User input for weight
        }
    },
    computed: {
        selectedRange() {
            // Find the appropriate range for the given weight
            return this.ranges.find(range =>
                this.testWeight >= parseFloat(range.minimum_quantity) && this.testWeight <= parseFloat(range.maximum_quantity)
            );
        }
    },
    mounted() {
        this.$parent.$on("categorySaved", (value) => {
            if (value) {
                this.close();
            }
        });
    },
    methods : {
        toggleEdit(item) {
            let vm = this;
            item.editable = !item.editable;
            if (!item.editable) {
                // Check if any changes were made
                if (
                    item.name !== item.originalData.name ||
                    item.description !== item.originalData.description
                ) {
                    const fd = new FormData();
                    fd.append('id', item.id);
                    fd.append('name', item.name);
                    fd.append('description',item.description);

                    vm.$emit('editCategory', fd);
                }
            }
        },
        addNewCategory(){
            let vm = this;
            if( vm.name == '' ){
                return swal({
                    title: "Required",
                    text: "Please add Category Name first, thanks",
                    icon: "error",
                    timer: 3000,
                });
            }

           const fd = new FormData();
           fd.append('id', vm.details.id);
           fd.append('name', vm.name);
           fd.append('internalLabel', vm.internalLabel);
           // Loop through the ranges array and append each range to FormData
            vm.ranges.forEach((range, index) => {
                fd.append(`ranges[${index}][minimum_quantity]`, range.minimum_quantity);
                fd.append(`ranges[${index}][maximum_quantity]`, range.maximum_quantity);
                fd.append(`ranges[${index}][base_rate]`, range.base_rate);
                fd.append(`ranges[${index}][per_kg]`, range.per_kg);
                fd.append(`ranges[${index}][per_kg_rate]`, range.per_kg_rate);
                fd.append(`ranges[${index}][fc]`, range.fc);
                fd.append(`ranges[${index}][gst]`, range.gst);
            });

           vm.$emit('addNewCategory', fd);
        },
        addRange() {
            if (this.ranges.length < 3) {
                this.ranges.push({
                    minimum_quantity: '',
                    maximum_quantity: '',
                    base_rate: '',
                    per_kg: '',
                    per_kg_rate: '',
                    fc : '',
                    gst : ''
                });
            }
        },
        removeRange(index) {
            this.ranges.splice(index, 1);
        },
        calculateTotalAmount(range) {
            // Calculate base amount (either base_rate or per_kg_rate)
            const baseAmount = parseFloat(range.base_rate) + parseFloat(range.per_kg_rate);

            // Calculate FC TAX and GST TAX based on the base amount
            const fcTax = (parseFloat(range.fc) / 100) * baseAmount;
            const gstTax = (parseFloat(range.gst) / 100) * (baseAmount+ fcTax);

            // Total amount = base amount + fcTax + gstTax
            const totalAmount = baseAmount + fcTax + gstTax;

            // Return total amount, formatted to 2 decimal places
            return totalAmount.toFixed(2);
        },
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

            const fcTax = (parseFloat(range.fc) / 100) * baseAmount;
            const gstTax = (parseFloat(range.gst) / 100) * (baseAmount + fcTax);
            return (baseAmount + fcTax + gstTax).toFixed(2);
        },
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        close(){
            this.name = '';
            this.internalLabel = '';
            this. ranges = [
                {
                    minimum_quantity: '',
                    maximum_quantity: '',
                    base_rate: '',
                    per_kg: '',
                    per_kg_rate: '',
                    fc : '',
                    gst : ''
                },
            ];
        }
    }
}
</script>
