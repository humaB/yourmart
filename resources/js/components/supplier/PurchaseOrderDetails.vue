<template>

    <!-- Modal -->
    <div class="modal fade" id="purchaseOrderDetails" tabindex="-1" role="dialog"
        aria-labelledby="purchaseOrderDetailsTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseOrderDetailsTitle">Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Total Amount</th>
                                    <th>Remaining Amount</th>
                                    <th>Date</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in purchaseOrders" :key="'purchase'+order.id">
                                    <td>PO-{{ order.id }}</td>
                                    <td>{{ order.total_amount }}</td>
                                    <td>{{ order.remaining_amount }}</td>
                                    <td>{{ formatDate(order.created_at) }}</td>

                                    <td>
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                                <tr class="bg-primary">
                                                    <th class="text-white">Product</th>
                                                    <th class="text-white">Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="detail in order.details" :key="detail.id">
                                                    <td>{{ detail.product.title }}</td>
                                                    <td>{{ detail.quantity }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
    name: "PurchaseOrderDetails",
    props: ['purchaseOrders'],
    methods : {
        formatDate(date) {
            return date ? moment(date).format("DD-MMM-YYYY") : "N/A";
        },
    }
}
</script>
