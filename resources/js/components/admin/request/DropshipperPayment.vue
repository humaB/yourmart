<template>
    <div class="modal fade" id="dropShipperPayment" tabindex="-1" role="dialog" aria-labelledby="dropShipperDetailTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dropShipperDetailTitle">Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dropdownField">Select Shop <span class="text-danger">*</span></label>
                                <v-select :options="shops" v-model="addData.shop_id" :reduce="option => option.code" @input="paymentShopPayments(addData.shop_id)">
                                </v-select>
                            </div>
                        </div>
                        <!-- Radio buttons for payment method -->
                        <div class="col-md-4">
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

                        <div class="col-md-4">
                            <div class="form-group" v-if="addData.type == 'bank'">
                                <label for="dropdownField">Select Bank Account <span class="text-danger">*</span></label>
                                <v-select :options="accountBanks" v-model="addData.from_account" :reduce="option => option.code">
                                </v-select>
                            </div>
                            
                            <div class="form-group" v-else-if="addData.type == 'cash'">
                                <label for="dropdownField">Select Cash Account <span class="text-danger">*</span></label>
                                <v-select :options="accountCash" v-model="addData.from_account" :reduce="option => option.code">
                                </v-select>
                            </div>

                            <div class="form-group" v-else>
                                <label for="dropdownField">Select Payment Method First</label>
                                <input type="text" disabled class="form-control"/>
                            </div>

                        </div>

                        <!-- Amount input field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amountField">Amount <span class="text-danger">*</span></label>
                                <input type="text" v-model="addData.amount" class="form-control" id="amountField" placeholder="Enter amount" @keypress="numberValidate($event,{dot:true})" />
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amountField">Narration</label>
                                <input type="text" v-model="addData.narration" class="form-control" id="amountField" placeholder="Enter amount" />
                            </div>
                        </div>
                       
                    </div>
                
                    <div class=" py-1">
                        <h5 class="text-capitalize">{{shop.store_name??''}} Payment History</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Narration</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in shopPayments" :key="item.id">
                                    <td>{{ item.debit }}</td>
                                    <td>{{ item.narration??'No Added' }}</td>
                                    <td>{{ item.time }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" :class="loader ? 'btn-progress disabled' : ''" @click="add()">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DropshipperPayment',
    props: ['shops', 'addData', 'loader','accountCash','accountBanks', 'details'],
    data() {
          return {
              web_url : process.env.MIX_WEB_URL,
              api_url : window.location.origin + process.env.MIX_API_URL,
              shopPayments: [],
              shop: [],
          };
      },
    methods : {
      add(){
        this.$emit('add')
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
        paymentShopPayments( id ){
            axios
            .post(this.api_url + "dropshippers/shops/payments", { id:id })
            .then((response) => {
                this.shopPayments = response.data.shopPayments;
                this.shop = response.data.shop;
            });
        },
    }
}
</script>

<style scoped>
/* Add any specific styling for the modal content here */
</style>
