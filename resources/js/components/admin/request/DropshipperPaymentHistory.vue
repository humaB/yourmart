<template>
    <!-- Modal -->
    <div class="modal fade" id="dropshipperHistory" tabindex="-1" role="dialog"
        aria-labelledby="dropshipperHistoryTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Payment History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr #</th>
                                    <th>Receipt #</th>
                                    <th>Against Order</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Proof of payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(group, index) in groupedData" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ group.document_id }}</td>
                                    <td>
                                        <span v-for="(shopOrder, shopIndex) in group.shop_orders" :key="shopIndex">
                                            {{ shopOrder.store_name.substring(0, 3) + '-' + shopOrder.order_no }}
                                            <br v-if="shopIndex < group.shop_orders.length - 1" />
                                        </span>
                                    </td>
                                    <td>{{ group.total_debit.toFixed(2) }}</td>
                                    <td>{{ formatDate(group.created_at) }}</td>
                                    <td v-if="group.attachment">
                                        <a target="_blank"
                                            :href="`${public_url}/public/storage/uploads/dropshipper/payments/${group.attachment}`">Preview</a>
                                    </td>
                                    <td v-else>-</td>
                                    <td>
                                        <button class="btn btn-dark" @click="printRequest(group.document_id)">
                                            <i class="fa fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark" @click="printLedger()"><i class="fa fa-print"></i> Print Ledger</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
        <!-- Summary PRINT -->
        <form method="POST" :action="public_url + '/dropshippers/payment-history'" target="_blank" ref="paymentHistory">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="document" :value="id">
            <input type="hidden" name="dropshipper" :value="selectedDropshipper">
        </form>

        <!-- Summary PRINT -->
        <form method="POST" :action="public_url + '/dropshippers/ledger'" target="_blank" ref="paymentLedger">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="dropshipper" :value="selectedDropshipper">
        </form>
    </div>
</template>
<script>
export default {
    name: 'DropshipperPaymentHistory',
    props: ['history', 'selectedDropshipper'],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            id: '',
            csrf: ''
        }
    },
    created() {
        this.csrf = $('meta[name=csrf-token]').attr('content');
    },
    computed: {
        groupedData() {
            const grouped = {};
            this.history.forEach((payment) => {
                const documentId = payment.document_id;
                const attachment = payment.attachment;
                if (!grouped[documentId]) {
                    grouped[documentId] = {
                        document_id: documentId,
                        total_debit: 0,
                        shop_orders: [],
                        attachment: attachment,
                        created_at: payment.created_at,
                    };
                }
                grouped[documentId].total_debit += parseFloat(payment.debit);
                grouped[documentId].shop_orders.push({
                    store_name: payment.order.shop.store_name,
                    order_no: payment.order.order_no,
                });
            });
            return Object.values(grouped);
        },
    },
    methods: {
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
        },
        printRequest(id) {
            this.id = id;
            const form = this.$refs.paymentHistory;
            setTimeout(() => {
                form.submit();
            }, 500)
        },
        printLedger() {
            const form = this.$refs.paymentLedger;
            setTimeout(() => {
                form.submit();
            }, 500)
        },
    }
}
</script>
