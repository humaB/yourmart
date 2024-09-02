<template>
    <div>
        <div class="modal fade" id="shippingClassDetails" tabindex="-1" role="dialog"
            aria-labelledby="shippingClassDetails" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Shipping Class Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12 mt-5">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Rate Method</th>
                                        <th v-if="['Weight Based', 'Dimension Based'].includes(details.rate_type)">Base
                                            Rate</th>
                                        <th v-if="['Weight Based', 'Dimension Based'].includes(details.rate_type)">Rate
                                            Per Unit</th>
                                        <th v-if="['Free Shipping'].includes(details.rate_type)">Minimum Amount</th>
                                        <th v-if="details.rate_type === 'Weight Based'">Minimum Weight</th>
                                        <th v-if="details.rate_type === 'Dimension Based'">Minimum Cubic Meter</th>
                                        <th>Total Cost</th>
                                        <th v-if="['Free Shipping'].includes(details.rate_type)">Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ details.rate_type }}</td>
                                        <td v-if="['Weight Based', 'Dimension Based'].includes(details.rate_type)">{{
                                            details.base_rate }}</td>
                                        <td v-if="['Weight Based', 'Dimension Based'].includes(details.rate_type)">{{
                                            details.rate_per_unit }}</td>
                                        <td
                                            v-if="['Free Shipping'].includes(details.rate_type)">
                                            {{ details.minimum_order }}
                                        </td>
                                        <td
                                            v-if="['Weight Based', 'Dimension Based'].includes(details.rate_type)">
                                            {{ details.minimum_order }}
                                         </td>
                                        <td
                                            v-if="['Free Shipping'].includes(details.rate_type)">
                                            {{ details.flat_rate }}
                                        </td>
                                        <td v-if="details.rate_type === 'Flat Rate'">{{ details.flat_rate }}</td>
                                        <td  v-if="['Free Shipping', 'Weight Based', 'Dimension Based'].includes(details.rate_type)">{{ calculateTotalCost() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            :class="status == 0 ? 'btn btn-success' : 'btn btn-danger'"
                            @click="toggleActivation">
                            {{ status == 0 ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ShippingClassDetail',
    props: ['details', 'status'],
    methods: {
        calculateTotalCost() {
            const unitQuantity = 10; // Example quantity of units for calculation

            // Check the rateMethod label instead of code
            if (this.details.rate_type  === 'Free Shipping') {
                return this.details.minimum_order
                    ? `Free for orders over ${this.details.minimum_order}`
                    : 'N/A';
            } else if (this.details.rate_type  === 'Flat Rate') {
                return this.details.flat_rate !== null
                    ? `${this.details.flat_rate}`
                    : 'N/A';
            } else if (this.details.rate_type  === 'Weight Based' || this.details.rate_type  === 'Dimension Based') {
                if (this.details.base_rate !== null && this.details.rate_per_unit !== null) {
                    const totalCost = parseFloat(this.details.base_rate) + (parseFloat(this.details.rate_per_unit) * unitQuantity);
                    return `${totalCost} (Base Rate: ${this.details.base_rate} + ${this.details.rate_per_unit} per unit for ${unitQuantity} units)`;
                }
                return 'N/A';
            } else {
                return 'N/A';
            }
        },
        toggleActivation() {
        if (this.status) {
            // Logic to deactivate
            this.deactivate();
        } else {
            // Logic to activate
            this.activate();
        }
        },
        activate() {
            // Update the status and/or make an API call to activate
            this.$emit('changeStatus', { id : this.details.shipping_class_id,  status : 1 });
        },
        deactivate() {
            // Update the status and/or make an API call to deactivate
            this.$emit('changeStatus', { id : this.details.shipping_class_id,  status : 0 });
        }
    }
}
</script>
