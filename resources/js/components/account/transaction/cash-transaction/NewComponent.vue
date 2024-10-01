<template>
    <div
        class="modal fade"
        id="newTransaction"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">
                        New Cash Transaction
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="d-block">Transaction Type <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="cp" value="CP" v-model="addData.type">
                            <label class="form-check-label" for="cp">Cash Payment</label>
                            </div>
                            <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="cr" value="CR" v-model="addData.type">
                            <label class="form-check-label" for="cr">Cash Receipt</label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Cash Ledger <span class="text-danger">*</span></label>
                            <v-select :options="cashes" v-model="addData.cash_ledger" :reduce="option => option.code">
                            </v-select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Cash Narration <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="addData.narration"/>
                        </div>
                    </div>
                    <div class="border p-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <h5>Transaction <small class="text-danger">(Amount  {{ finalData.total_amount }})</small></h5>
                            <div>
                                <button type="button" class="btn btn-outline-success" @click="addTransactionRow">Add Account</button>
                            </div>
                        </div>
                        <div class="row" v-if="transactionLoop > 0">
                            <div class="form-group col-md-4">
                                <label>Ledger <span class="text-danger">*</span></label>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Amount <span class="text-danger">*</span></label>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Narration <span class="text-danger">*</span></label>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Remove</label>
                            </div>
                        </div>
                        <div class="row" v-for="(i, index) in transactionLoop" :key="index">
                            <div class="form-group mb-2 col-md-4">
                                <v-select :options="heads" v-model="addData.ledgers[index]" :reduce="option => option.code" @input="saveTransactionRow($event, 'first', index)">
                                </v-select>
                            </div>
                            <div class="form-group mb-2 col-md-3">
                                <input class="form-control" type="text" :value="addData.amounts[index]" @keyup="saveTransactionRow($event, 'second', index)" @keypress="numberValidate($event,{dot:true})">
                            </div>
                            <div class="form-group mb-2 col-md-3">
                                <input class="form-control" type="text" :value="addData.narrations[index]" @change="saveTransactionRow($event, 'third', index)" @keyup.enter="addTransactionRow" />
                            </div>
                            <div class="form-group mb-2 col-md-2">
                                <button type="button" class="mt-1 btn-sm btn btn-outline-danger" @click="removeTransactionRow($event, index)"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" :class="{ 'disabled btn-progress': btnLoading }" @click=add()>Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  props: ['btnLoading', 'addData', 'cashes', 'heads'],
  data() {
    return {
        finalData: {
            total_amount : 0,
        },
        transactionLoop: 0,
    }
  },
  methods: {
        add() {
            this.$emit('add');
        },
        saveTransactionRow(event, fieldName, index) {
            if (fieldName == "first") {
                this.addData.ledgers[index] = event;
            }
            if (fieldName == "second") {
                this.addData.amounts[index] = event.target.value ? event.target.value : 0;
            }
            if (fieldName == "third") {
                this.addData.narrations[index] = event.target.value;
            }
            this.totalAmount();
        },
        addTransactionRow() {
            this.transactionLoop++;
            this.totalAmount();
        },
        removeTransactionRow(event, index) {
            this.addData.ledgers.splice(index, 1);
            this.addData.amounts.splice(index, 1);
            this.addData.narrations.splice(index, 1);
            this.transactionLoop--;
            this.totalAmount();
        },
        totalAmount()
        {
            this.finalData.total_amount = this.addData.amounts.reduce((acc, current) => acc + parseFloat(current), 0);
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
    }
}
</script>