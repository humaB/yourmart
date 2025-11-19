<template>
    <div class="modal fade" id="supplierPayment" tabindex="-1" role="dialog" aria-labelledby="supplierDetailTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierDetailTitle">Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">


                        <!-- Radio buttons for payment method -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Method <span class="text-danger">*</span></label><br />
                                <select v-model="addData.type" class="form-control">
                                    <option value="null">Select from the following</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                </select>
                                <!-- <div class="form-check d-inline-block">
                                    <input type="radio" class="form-check-input" id="paymentCash" value="cash" v-model="addData.type">
                                    <label class="form-check-label" for="paymentCash">Cash</label>
                                </div>
                                <div class="form-check d-inline-block">
                                    <input type="radio" class="form-check-input" id="paymentBank" value="bank" v-model="addData.type">
                                    <label class="form-check-label" for="paymentBank">Bank</label>
                                </div> -->
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" v-if="addData.type == 'bank'">
                                <label for="dropdownField">Select Bank Account <span
                                        class="text-danger">*</span></label>
                                <v-select :options="accountBanks" v-model="addData.from_account"
                                    :reduce="option => option.code">
                                </v-select>
                            </div>

                            <div class="form-group" v-else-if="addData.type == 'cash'">
                                <label for="dropdownField">Select Cash Account <span
                                        class="text-danger">*</span></label>
                                <v-select :options="accountCash" v-model="addData.from_account"
                                    :reduce="option => option.code">
                                </v-select>
                            </div>

                            <div class="form-group" v-else>
                                <label for="dropdownField">Select Payment Method First</label>
                                <input type="text" disabled class="form-control" />
                            </div>

                        </div>

                        <!-- Amount input field -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amountField">Amount <span class="text-danger">*</span></label>
                                <input type="text" v-model="addData.amount" class="form-control" id="amountField"
                                    placeholder="Enter amount"
                                    @keypress="numberValidate($event, { dot: true, negative: true })" />
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="amountField">Narration</label>
                                <input type="text" v-model="addData.narration" class="form-control" id="amountField"
                                    placeholder="Enter Narration" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="amountField">Proof of payment</label>
                                <input type="file" @change="setAttachment" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <h5>Account Information</h5>
                            <hr>
                        </div>

                        <div class="col-md-3 col-6">
                            <strong>Bank Name:</strong>
                            <br>
                            <p class="text-muted">{{ details.bank ? details.bank.name : '-' }}</p>
                        </div>

                        <div class="col-md-3 col-6">
                            <strong>Account Number:</strong>
                            <br>
                            <p class="text-muted">{{ details.account_number || 'N/A' }}</p>
                        </div>

                        <div class="col-md-3 col-6">
                            <strong>Account Title</strong>
                            <br>
                            <p class="text-muted">{{ details.account_title || 'N/A' }}</p>
                        </div>
                        <div class="col-md-3 col-6">
                            <strong>Account IBAN</strong>
                            <br>
                            <p class="text-muted">{{ details.account_iban || 'N/A' }}</p>
                        </div>

                        <div class="col-md-4 col-6">
                            <strong>Total Payable</strong>
                            <br>
                            <h5 class="text-muted">{{ formatPrice( selectedSupplier.data ? selectedSupplier.data.po_total_order_amount : details.total_profit) }}</h5>
                        </div>
                        <div class="col-md-4 col-6">
                            <strong>Total Paid</strong>
                            <br>
                            <h5 class="text-muted">{{ formatPrice( selectedSupplier.data ? selectedSupplier.data.po_total_paid_amount : details.total_paid_profit) }}</h5>
                        </div>
                        <div class="col-md-4 col-6">
                            <strong>Remaining Balance</strong>
                            <br>
                            <h5 class="text-muted">{{ formatPrice(selectedSupplier.data ? selectedSupplier.data.po_total_remaining_amount : details.total_profit - details.total_paid_profit) }}
                            </h5>
                        </div>

                    </div>

                    <div class="py-1">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Total Payable</th>
                                    <th>Total Paid</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in orders" :key="item.id">
                                    <td>PO-{{ item.id }}</td>
                                    <td>{{ item.total_amount }}</td>
                                    <td>{{ item.total_amount - item.remaining_amount  }}</td>
                                    <td>{{ item.remaining_amount }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td>{{ totalProfit }}</td>
                                    <td>{{ totalBalance }}</td>
                                    <td>{{ totalPaidProfit }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="add()" v-if="!loader">Add Payment</button>
                    <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add Payment</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { invalid } from 'moment';

export default {
    name: 'SupplierPayment',
    props: ['orders', 'addData', 'loader', 'accountCash', 'accountBanks', 'details', 'selectedSupplier'],
    data() {
        return {
            web_url: process.env.MIX_WEB_URL,
            api_url: window.location.origin + process.env.MIX_API_URL,
            shopPayments: [],
            shop: [],
        };
    },
    computed: {
        totalProfit() {
            return this.orders.reduce((sum, item) => sum + parseFloat(item.total_amount), 0).toFixed(2);
        },
        totalPaidProfit() {
            return this.orders.reduce((sum, item) => sum + parseFloat(item.remaining_amount), 0).toFixed(2);
        },
        totalBalance() {
            return this.orders.reduce((sum, item) => sum + (parseFloat(item.total_amount) - parseFloat(item.remaining_amount)), 0).toFixed(2);
        },
    },
    methods: {
        setAttachment(event) {
            this.addData.attachment = event.target.files[0];
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        add() {
            this.$emit('add')
        },
        fetchTracking(id) {
            this.$emit('fetchTracking', { id })
        },
        fetchOrderDetails(id) {
            this.$emit('fetchOrderDetails', { id })
        },
        numberValidate(event, { dot = false, maxLen = null, negative = false, comma = false } = {}) {

            const charCode = event.charCode;
            const value = event.target.value.toString().replace(/,/g, '');

            // Allow numbers (48-57), dot (46), and control keys (0)
            if ((charCode >= 48 && charCode <= 57) || charCode === 0) {

                // Check the length if it's not null
                if (maxLen !== null && value.length >= maxLen) {
                    event.preventDefault();
                    return false;
                }

                return true;
            }
            // Accept dot
            if (dot && charCode === 46) {
                // Allow only one dot
                if (value.includes('.')) {
                    event.preventDefault();
                    return false;
                }

                // Check the length if it's not null
                if (maxLen !== null && value.length >= maxLen) {
                    event.preventDefault();
                    return false;
                }
                return true;
            }
            // Accept negative value
            if (negative && charCode === 45) {
                if (value.includes('-') || value.length !== 0) {
                    event.preventDefault();
                    return false;
                }
                return true;
            }

            event.preventDefault();
            return false;
        },
        paymentShopPayments(id) {
            let vm = this;

            axios
                .post(this.api_url + "dropshippers/shops/payments", { id: id })
                .then((response) => {
                    this.shopPayments = response.data.shopPayments;
                    this.shop = response.data.shop;

                });
        },
    }
}
</script>


