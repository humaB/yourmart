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
                    <div class="col-md-7">
                      <div class="form-group">
                          <label for="dropdownField">Select Head:</label>
                          <v-select :options="shopHeads" v-model="addData.head_id" :reduce="option => option.code">
                          </v-select>
                      </div>

                      <!-- Radio buttons for payment method -->
                      <div class="form-group">
                          <label>Payment Method:</label><br />
                          <div class="form-check">
                              <input type="radio" class="form-check-input" id="paymentCash" value="cash" v-model="addData.type">
                              <label class="form-check-label" for="paymentCash">Cash</label>
                          </div>
                          <div class="form-check">
                              <input type="radio" class="form-check-input" id="paymentBank" value="bank" v-model="addData.type">
                              <label class="form-check-label" for="paymentBank">Bank</label>
                          </div>
                      </div>

                      <div class="form-group" v-if="addData.type == 'bank'">
                          <label for="dropdownField">Select Bank Account:</label>
                          <v-select :options="accountBanks" v-model="addData.from_account" :reduce="option => option.code">
                          </v-select>
                      </div>
                      
                      <div class="form-group" v-if="addData.type == 'cash'">
                          <label for="dropdownField">Select Cash Account:</label>
                          <v-select :options="accountCash" v-model="addData.from_account" :reduce="option => option.code">
                          </v-select>
                      </div>

                      <!-- Amount input field -->
                      <div class="form-group">
                          <label for="amountField">Amount:</label>
                          <input type="number" v-model="addData.amount" class="form-control" id="amountField" placeholder="Enter amount" />
                      </div>
                    </div>
                    <div class="col-md-5 border">
                        <h5>Payment History</h5>
                    </div>
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
    props: ['shopHeads', 'addData', 'loader','accountCash','accountBanks', 'details'],
    data() {
          return {
              web_url : process.env.MIX_WEB_URL,
          };
      },
    methods : {
      add(){
        this.$emit('add')
      }
    }
}
</script>

<style scoped>
/* Add any specific styling for the modal content here */
</style>
