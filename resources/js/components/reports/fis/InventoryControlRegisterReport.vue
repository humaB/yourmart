<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Inventory Control Register</h5>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-12">
                            <form @submit.prevent="submitFunction">
                                <div class="row">
                                  <div class="col-md-4 form-group">
                                    <label for="date">From</label>
                                    <input
                                      type="date"
                                      name="from"
                                      class="form-control"
                                      v-model="filter.from"
                                    />
                                  </div>
                                  <div class="col-md-4 form-group">
                                    <label for="date">To</label>
                                    <input
                                      type="date"
                                      name="to"
                                      class="form-control"
                                      v-model="filter.to"
                                    />
                                  </div>
                                  <div class="col-md-4 form-group pt-4">
                                    <button class="btn btn-block btn-primary">Filter</button>
                                  </div>


                                </div>
                              </form>
                        </div>
                        <div class="col-md-12 table-responsive">
                            <div class="card-body" v-if="loader">
                                <bullet-list-loader  :width="250" >
                                </bullet-list-loader>
                              </div>
                            <table class="table table-bordered" id="inventory_control_register" v-else>
                                <thead>
                                    <tr >
                                        <th colspan="3" class="border h5">Item Description</th>
                                        <th colspan="3" class="border h5">Opening</th>
                                        <th colspan="3" class="border h5">Purchase</th>
                                        <th colspan="3" class="border h5">Issuance</th>
                                        <th colspan="4" class="border h5">Balance</th>

                                    </tr>
                                    <tr>
                                        <th>Sr #</th>
                                        <th>SKU</th>
                                        <th class="border-left">Product Name </th>

                                        <th>Rate</th>
                                        <th >Qty</th>
                                        <th class="border-left">Total Value</th>

                                        <th>Rate</th>
                                        <th >Stock</th>
                                        <th class="border-left">Total Value</th>

                                        <th>Rate </th>
                                        <th>Issued</th>
                                        <th class="border-left">Total Cost of Issuance</th>

                                        <th>Returned</th>
                                        <th>Balance Quantity</th>
                                        <th>Closing Balance</th>
                                        <th>Gross Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <template v-for="(item , index) in data" >
                                  <tr v-if="item.good_receive[0] || item.issuance[0] || item.opening_stock[0]" :key="item.id">
                                      <td>{{ index + 1 }}</td>
                                      <td>{{ item.variation.sku }}</td>
                                      <td>{{ item.title }}
                                         || Current Average Rate <b>{{ item.variation.avg_price}}</b>
                                      </td>

                                      <!-- Opening -->
                                      <td>{{ item.opening_stock[0] ? formatPrice( item.opening_stock[0].rate ) : '-' }}</td>
                                      <td>
                                        <a href="#" v-if=" item.opening_stock[0]" data-toggle="modal" data-target="#grnDetailPopup" @click="fetchGRNDetail( item.id )">{{ calculateQuantityDifference(item) }}</a>
                                        <span v-else>-</span>
                                      </td>

                                      <td>{{ item.opening_stock[0] ? formatPrice( calculateQuantityDifference(item) * parseFloat(item.opening_stock[0].rate) ) : '-'}}</td>

                                      <!-- Received -->
                                      <td>{{ item.good_receive[0] ? formatPrice( item.good_receive[0].rate ) : '-' }}</td>
                                      <td>
                                        <a href="#" v-if=" item.good_receive[0]" data-toggle="modal" data-target="#grnDetailPopup" >{{ item.good_receive[0].quantity  }}</a>
                                        <span v-else>-</span>
                                      </td>

                                      <td>{{ item.good_receive[0] ? formatPrice( parseFloat(item.good_receive[0].quantity) * parseFloat(item.good_receive[0].rate) ) : '-'}}</td>

                                      <!-- Issuance -->
                                      <td>{{ item.issuance[0] ? formatPrice( item.issuance[0].rate ) : '-'}}</td>
                                      <td>
                                        <a href="#" v-if=" item.issuance[0]" data-toggle="modal" data-target="#sinDetailPopup">{{ item.issuance[0].quantity  }}</a>
                                        <span v-else>-</span>
                                      </td>
                                      <td>{{ item.issuance[0] ? formatPrice( parseFloat(item.issuance[0].quantity) *  parseFloat(item.issuance[0].rate) ) : '-'}}</td>

                                      <td>{{ item.return[0] ? formatPrice( parseFloat(item.return[0].quantity) ) : '-'}}</td>
                                      <td>{{ calculateClosingBalance(item) }}</td>
                                      <!-- Closing Balance -->
                                      <td>{{ formatPrice(calculateClosingBalance(item) * item.variation.avg_price) }}</td>
                                      <td>{{ formatPrice(calculateGrossProfite(item)) }}</td>
                                    </tr>
                                  </template>
                                </tbody>
                              </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import { BulletListLoader } from 'vue-content-loader';

export default {
    name: 'InventoryControlRegisterReport',
    props : ['data', 'loader'],
    components : {
        BulletListLoader
    },
    data(){
        return {
            filter: {
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            },
        }
    },
    methods : {
        calculateQuantityDifference(item) {
            // Get the quantity from opening_stock, defaulting to 0 if not available
            const openingStockQty = item.opening_stock && item.opening_stock[0]
                ? item.opening_stock[0].quantity
                : 0;

            // Get the quantity from opening_issuance, defaulting to 0 if not available
            const openingIssuanceQty = item.opening_issuance && item.opening_issuance[0]
                ? item.opening_issuance[0].quantity
                : 0;

            // Get the quantity from opening_returns, defaulting to 0 if not available
            const openingReturnsQty = item.opening_returns && item.opening_returns[0]
                ? item.opening_returns[0].quantity
                : 0;

            // Calculate the difference
            return (openingStockQty + openingReturnsQty) - openingIssuanceQty;
        },
        calculateClosingBalance(item) {
            // Calculate Opening Balance
            const openingStockQty = item.opening_stock && item.opening_stock[0]
                ? parseFloat(item.opening_stock[0].quantity)
                : 0;
            const openingReturnsQty = item.opening_returns && item.opening_returns[0]
                ? parseFloat(item.opening_returns[0].quantity)
                : 0;
            const openingIssuanceQty = item.opening_issuance && item.opening_issuance[0]
                ? parseFloat(item.opening_issuance[0].quantity)
                : 0;

            const openingBalance = (openingStockQty + openingReturnsQty) - openingIssuanceQty;

            // Calculate Current Balance
            const grnQty = item.good_receive && item.good_receive[0]
                ? parseFloat(item.good_receive[0].quantity)
                : 0;
            const returnQty = item.return && item.return[0]
                ? parseFloat(item.return[0].quantity)
                : 0;
            const issuanceQty = item.issuance && item.issuance[0]
                ? parseFloat(item.issuance[0].quantity)
                : 0;

            const currentBalance = (grnQty + returnQty) - issuanceQty;

            // Calculate Closing Balance
            return openingBalance + currentBalance;
        },
        calculateGrossProfite(item) {

            const issuanceQty = item.issuance && item.issuance[0]
                ? parseFloat(item.issuance[0].quantity)
                : 0;

            const issuanceRate = item.issuance && item.issuance[0]
                ? parseFloat(item.issuance[0].rate)
                : 0;

            const purchaseRate = item.variation.avg_price;
            const profit = (issuanceQty * issuanceRate) - (issuanceQty * purchaseRate);
            // Calculate Closing Balance
            return profit;
        },
        formatPrice: function formatPrice(price) {
        const value = parseFloat(price).toFixed(2)
        var string = value.toString();
        return string
          .replace(/,/g, "")
          .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
      },
      submitFunction(){
        this.clearDataTable()
        this.$emit('inventoryControlregisterReportFilter', this.filter);
      },
      clearDataTable(){
        const table = $('#inventory_control_register').DataTable();
        table.destroy();
      },
    },
    watch:{
    data(newLedger){
      setTimeout(() => {
        $('#inventory_control_register').DataTable({
                searching: true,
                paging: false,
                ordering: false,
                info: false,
               "bSort" : false,
                dom: 'Bfrtip',
                buttons: [
                {
                    extend: 'copy',
                    title : 'Inventory Control Register',
                  }, 'csv', {
                    extend: 'excel',
                    title : 'Inventory Control Register',
                  }
                ]
          });
      }, 300);
    }
  }
}
</script>
