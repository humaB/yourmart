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
                                    <th>Against PO</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Proof of payment</th>
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(group, index) in groupedData" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ group.document_id }}</td>
                                    <td>
                                        <span v-for="(shopOrder, shopIndex) in group.po" :key="shopIndex">
                                            PO-{{ shopOrder.number }}
                                            <br v-if="shopIndex < group.po.length - 1" />
                                        </span>
                                    </td>
                                    <td>{{ group.total_debit.toFixed(2) }}</td>
                                    <td>{{ formatDate(group.created_at) }}</td>
                                    <td v-if="group.attachment">
                                        <a target="_blank"
                                            :href="`${public_url}/public/storage/uploads/dropshipper/payments/${group.attachment}`">Preview</a>
                                    </td>
                                    <td v-else>-</td>
                                    <!-- <td>
                                        <button class="btn btn-dark" @click="printRequest(group.document_id)">
                                            <i class="fa fa-print"></i>
                                        </button>
                                    </td> -->
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
    name: 'SupplierPaymentHistory',
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
                const posting_id = payment.posting_id;
                if (!grouped[documentId]) {
                    grouped[documentId] = {
                        document_id: documentId,
                        total_debit: 0,
                        attachment: attachment,
                        po : [],
                        created_at: payment.created_at,
                    };
                }
                grouped[documentId].total_debit += parseFloat(payment.debit);
                 grouped[documentId].po.push({
                    number: posting_id,
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
