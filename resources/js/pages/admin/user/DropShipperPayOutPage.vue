<template>
    <div>
        <div class="row">
            <div class="card-body">
                <ul class="nav nav-pills" id="myTab3" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="home-tab3" data-toggle="tab" href="#home3" role="tab" aria-controls="home" aria-selected="true">Pending Payouts</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="profile-tab3" data-toggle="tab" href="#profile3" role="tab" aria-controls="profile" aria-selected="false">Overall Record</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="level-tab3" data-toggle="tab" href="#level3" role="tab" aria-controls="level" aria-selected="false">Level & Rewards <span class="badge badge-primary">{{ incompleteLevelsCount  }}</span></a>
                  </li>
                </ul>
                <div class="tab-content" id="myTabContent2">

                  <div class="tab-pane fade show active" id="home3" role="tabpanel" aria-labelledby="home-tab3">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card card-primary">
                            <TableHeader :tableHeader="tableHeader" />

                            <div class="row px-4">
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon l-bg-purple">
                                            <i class="fa fa-hand-holding-usd"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="padding-20">
                                                <div class="text-right">
                                                    <h3 class="font-light mb-0">
                                                        <i class="ti-arrow-up text-success"></i> {{ formatPrice(totalPayable) }}
                                                    </h3>
                                                    <span class="text-muted">Total Payouts</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon l-bg-green">
                                            <i class="fa fa-thumbs-up"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="padding-20">
                                                <div class="text-right">
                                                    <h3 class="font-light mb-0">
                                                        <i class="ti-arrow-up text-success"></i> {{ formatPrice(totalPaid) }}
                                                    </h3>
                                                    <span class="text-muted">Total Paid</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon l-bg-cyan">
                                            <i class="fa fa-calculator"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="padding-20">
                                                <div class="text-right">
                                                    <h3 class="font-light mb-0">
                                                        <i class="ti-arrow-up text-success"></i> {{ formatPrice(totalRemaining)
                                                        }}
                                                    </h3>
                                                    <span class="text-muted">Total Remaining</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1">
                                        <div class="card-icon l-bg-orange">
                                            <i class="fa fa-clipboard-list"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="padding-20">
                                                <div class="text-right">
                                                    <h3 class="font-light mb-0">
                                                        <i class="ti-arrow-up text-success"></i> {{ remainingDropshippers }}
                                                    </h3>
                                                    <span class="text-muted">Total Sellers</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body row">
                                <!-- Table -->
                                <div class="col-md-12 mt-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 card-body table-responsive" v-if="loader">
                                                    <bullet-list-loader :width="250"> </bullet-list-loader>
                                                </div>
                                                <div class="col-md-12 table-responsive" v-else>
                                                    <h5>Total Recommended Payouts : {{ formatPrice(totalRecommended) }}</h5>
                                                    <table class="table table-bordered" :id="table_id">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr #</th>
                                                                <th>Name</th>
                                                                <th>Email</th>
                                                                <th>Cycle</th>
                                                                <th>Due Date</th>
                                                                <th>Total Payable</th>
                                                                <th>Total Paid</th>
                                                                <th>Remaining Amount</th>
                                                                <th>DC &amp; Packing</th>
                                                                <th>Reserved Amount</th>
                                                                <th>Recommended Pay</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in records" :key="item.id">
                                                                <td>{{ index + 1 }}</td>
                                                                <td>{{ item.full_name }}</td>
                                                                <td>{{ item.email }}</td>
                                                                <td>{{ item.payment_cycle}}</td>
                                                                <td>{{ getNextDueDate(item.voucher_created_at, item.payment_cycle) }}</td> <!-- Next Due Date -->
                                                                <td>{{ formatPrice(item.profit) }}</td>
                                                                <td>{{ formatPrice(item.paid_profit) }}</td>
                                                                <td>{{ formatPrice(item.profit -item.paid_profit) }}</td>

                                                                <td>{{ formatPrice(item.reserved) }}</td>
                                                                <td>{{ getRecommendedPay(item) }}</td>
                                                                <td>{{ formatPrice(getNetBalance(item)) }}</td>

                                                                <td width="20%">
                                                                    <button class="btn btn-info" @click="fetchDetail(item.id)"
                                                                        data-toggle="modal" data-target="#dropShipperDetail"
                                                                        title="View Details"><i class="fa fa-eye"></i></button>
                                                                    <button class="btn btn-dark" @click="printRequest(item.id)"
                                                                        title="Print"><i class="fa fa-print"></i></button>
                                                                    <button class="btn btn-primary"
                                                                        @click="paymentDetail(item.id)" data-toggle="modal"
                                                                        data-target="#dropShipperPayment" title="Payment"><i
                                                                            class="fas fa-credit-card"></i></button>
                                                                    <button class="btn btn-primary"
                                                                        @click="paymentHistory(item.id)" data-toggle="modal"
                                                                        data-target="#dropshipperHistory" title="Payment"><i
                                                                                class="far fa-clock"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- END TABLE -->
                            </div>
                        </div>
                    </div>
                  </div>

                  <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile-tab3">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h5>All Payouts Record</h5>
                            </div>
                            <div class="card-body">
                                <div class="row px-4">
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-purple">
                                                <i class="fa fa-hand-holding-usd"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ formatPrice(totalPayable) }}
                                                        </h3>
                                                        <span class="text-muted">Total Payouts</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-green">
                                                <i class="fa fa-thumbs-up"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ formatPrice(totalPaid) }}
                                                        </h3>
                                                        <span class="text-muted">Total Paid</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-cyan">
                                                <i class="fa fa-calculator"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ formatPrice(totalRemaining)
                                                            }}
                                                        </h3>
                                                        <span class="text-muted">Total Remaining</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-orange">
                                                <i class="fa fa-clipboard-list"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ remainingDropshippers }}
                                                        </h3>
                                                        <span class="text-muted">Total Sellers</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="card-body table-responsive" v-if="loader">
                                                    <bullet-list-loader :width="250"> </bullet-list-loader>
                                                </div>
                                <div class="col-md-12 table-responsive" v-else>
                                    <table class="table table-bordered" id="payout-record">
                                        <thead>
                                            <tr>
                                                <th v-for="(item, index) in th2" :key="item">{{ item }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in payOuts" :key="item.id">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ item.full_name }}</td>
                                                <td>{{ item.email }}</td>
                                                <td>{{ item.whatsapp_number }}</td>
                                                <td>{{ formatPrice(item.profit) }}</td>
                                                <td>{{ formatPrice(item.paid_profit) }}</td>
                                                <td>{{ formatPrice(item.profit -item.paid_profit) }}</td>


                                                <td>{{ formatDate(item.created_at) }}</td>
                                                <td width="20%">
                                                    <button class="btn btn-info" @click="fetchDetail(item.id)"
                                                        data-toggle="modal" data-target="#dropShipperDetail"
                                                        title="View Details"><i class="fa fa-eye"></i></button>
                                                    <button class="btn btn-dark" @click="printRequest(item.id)"
                                                        title="Print"><i class="fa fa-print"></i></button>
                                                    <button class="btn btn-primary"
                                                        @click="paymentDetail(item.id)" data-toggle="modal"
                                                        data-target="#dropShipperPayment" title="Payment"><i
                                                            class="fas fa-credit-card"></i></button>
                                                    <button class="btn btn-primary"
                                                        @click="paymentHistory(item.id)" data-toggle="modal"
                                                        data-target="#dropshipperHistory" title="Payment"><i
                                                            class="far fa-clock"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>


                  <div class="tab-pane fade" id="level3" role="tabpanel" aria-labelledby="level-tab3">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h5>Level & Rewards</h5>
                            </div>
                            <div class="card-body">
                                <div class="row px-4">
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-purple">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ formatPrice(levelsWidget.level1) }}
                                                        </h3>
                                                        <span class="text-muted">Level 01 Seller</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-green">
                                                <i class="fas fa-user-check"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ formatPrice(levelsWidget.level2) }}
                                                        </h3>
                                                        <span class="text-muted">Level 02 Seller</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-cyan">
                                                <i class="fas fa-tasks"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ formatPrice(levelsWidget.level3) }}
                                                        </h3>
                                                        <span class="text-muted">Level 03 Seller</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                        <div class="card card-statistic-1">
                                            <div class="card-icon l-bg-orange">
                                                <i class="fas fa-crown"></i>
                                            </div>
                                            <div class="card-wrap">
                                                <div class="padding-20">
                                                    <div class="text-right">
                                                        <h3 class="font-light mb-0">
                                                            <i class="ti-arrow-up text-success"></i> {{ levelsWidget.topRatedSeller }}
                                                        </h3>
                                                        <span class="text-muted">Top Rated Seller</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <form @submit.prevent="applyFilter" class="row col-md-12">

                                        <div class="col-md-4">
                                            <label for="">Level</label>
                                            <select v-model="filter.level" class="form-control">
                                                <option value="">Select from the following</option>
                                                <option>New Seller</option>
                                                <option>Level 01</option>
                                                <option>Level 02</option>
                                                <option>Level 03</option>
                                                <option>Top Rated Seller</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="">Incentive</label>
                                            <select v-model="filter.incentive" class="form-control">
                                                <option value="">Select from the following</option>
                                                <option value="0">Pending</option>
                                                <option value="1">Given</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="">Action</label><br>
                                            <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                            <button class="btn btn-danger" @click="resetFilter">Reset</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <table class="table table-bordered table-striped" id="level-record">
                                        <thead>
                                            <tr>
                                                <th>Sr #</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Whatsapp Number</th>
                                                <th>Level</th>
                                                <th>Rewards</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in levels" :key="item.id">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ item?.dropshipper?.full_name }}</td>
                                                <td>{{ item?.dropshipper?.email }}</td>
                                                <td>{{ item?.dropshipper?.whatsapp_number }}</td>
                                                <td>{{ item.level }}</td>
                                                <td width="40%">
                                                    <div class="col-md-12" v-if="!editMode"
                                                    v-for="(requirement, key) in item?.details.requirement"
                                                    :key="'level-'+key">
                                                      <p>
                                                        <strong>{{ key }}</strong> —
                                                        <span :class="requirement.filled ? 'text-success' : 'text-muted'">
                                                          {{ requirement.filled ? '✔️ Completed' : '⏳ Not Completed' }}
                                                        </span>
                                                      </p>
                                                    </div>
                                                    <div class="col-md-3" v-if="editMode"
                                                    v-for="(requirement, key) in item?.details.requirement"
                                                    :key="key">
                                                <div class="form-check">
                                                 <input class="form-check-input"
                                                        type="checkbox"
                                                        :id="`requirement-${index}-${key}`"
                                                        v-model="requirement.filled" />
                                                    <label class="form-check-label" :for="`requirement-${index}-${key}`">{{ key }}</label>
                                                </div>
                                                </div>
                                                </td>
                                                <td width="20%">
                                                    <button
                                                        v-if="!editMode"
                                                        class="btn btn-primary ml-1"
                                                        @click="enableEdit(item)"
                                                        title="Edit"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </button>

                                                    <button
                                                        v-if="editMode"
                                                        class="btn btn-success ml-1"
                                                        @click="saveItem(item)"
                                                        title="Save"
                                                    >
                                                        <i class="fa fa-save"></i>
                                                    </button>

                                                    <button class="btn btn-info" @click="fetchDetail(item.dropshipper_id)"
                                                        data-toggle="modal" data-target="#dropShipperDetail"
                                                        title="View Details"><i class="fa fa-eye"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>

                </div>
              </div>

        </div>

        <DropshipperDetails :details="details" :loader="btnLoader" @decision="decision($event)"
         @updateDropshipperInformation="updateDropshipperInformation($event)"
        />

        <DropshipperPayment ref="dropshipperPayment"
            :orders="orders"
            :addData="addData"
            :loader="paymentLoader"
            :details="details"
            :accountCash="accountCash"
            :accountBanks="accountBanks"
            :reservedAmount="reservedAmount"
            @add="addPayment"
            @fetchTracking="fetchTracking($event)"
            @fetchOrderDetails="fetchOrderDetails( $event )"
        />

        <DropshipperPaymentHistory
            :selectedDropshipper="selectedDropshipper"
            :history="paymentHistorys"
        />

        <TrackingDetailPopup
            :trackingDetails="trackingDetails"
        />

        <OrderDetailView
            :revertLoader="revertLoader"
            :rejectLoader="rejectLoader"
            :paidAmountLoader="paidAmountLoader"
            :details="orderDetails"
            :loader="commentLoader"
            :role="role"
            @addComment="addComment($event)"
            @forward="forward($event)"
            @reject="reject($event)"
            @revert="revert($event)"
            @fetchDropshipperDetails="fetchDropshipperDetails($event)"
            @updatePaidAmount="updatePaidAmount( $event )"
            @updatePackagingAmount="updatePackagingAmount( $event )"
            @markasReplacement="markasReplacement($event)"
        />

        <OrderMarkasReplacementConfirmation
            :orderID="orderID"
            :loader="markasReplacementLoader"
            @markasReplacementConfirmation="markasReplacementConfirmation($event)"
        />

        <!-- Summary PRINT -->
        <form method="POST" :action="public_url + '/requests/dropshippers/pdf'" target="_blank" ref="requestForm">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="id" :value="id">
        </form>
    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../components/table/TableHeaderComponent.vue";
import DropshipperDetails from "../../../components/admin/request/DropshipperDetails.vue";
import DropshipperPayment from "../../../components/admin/request/DropshipperPayment.vue";
import DropshipperPaymentHistory from "../../../components/admin/request/DropshipperPaymentHistory.vue";
import TrackingDetailPopup from "../../../components/inventory/product/order/TrackingDetailPopup.vue";
import OrderDetailView from "../../../components/inventory/product/order/OrderDetailView.vue";
import OrderMarkasReplacementConfirmation from "../../../components/inventory/product/order/OrderMarkasReplacementConfirmation.vue";
import { filter } from "lodash";

export default {
    name: 'DropShipperPayOutPage',
    components: {
        TableHeader,
        BulletListLoader,
        DropshipperDetails,
        DropshipperPayment,
        DropshipperPaymentHistory,
        TrackingDetailPopup,
        OrderDetailView,
        OrderMarkasReplacementConfirmation
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Dropshipper Pay outs",
            },
            th2: ["Sr #", "Name", "Email", "Contact #", "Total Payable", "Total Paid", "Remaining Amount", "Added Date", "Action"],
            table_id: "moq_table",
            btnLoader: false,
            records: [],
            payOuts : [],
            orders: [],
            accountBanks: [],
            accountCash: [],
            loader: true,
            details: {},
            totalPayable: 0,
            totalPaid: 0,
            totalRemaining: 0,
            remainingDropshippers: 0,
            totalRecommended: 0,
            id: '',
            filter: {
                status: '',
                from: '',
                to: '',
            },
            addData: {
                shop_id: { code: 0, label: "Select from the following" },
                type: null,
                from_account: { code: 0, label: "Select from the following" },
                amount: null,
                narration: null,
                id: '',
                attachment : null
            },
            paymentLoader: false,
            selectedDropshipper: '',
            paymentHistorys : [],
            trackingDetails : [],
            //Order
            orderDetails: {},
            commentLoader : false,
            rejectLoader : false,
            revertLoader : false,
            role : '',
            paidAmountLoader : false,
            orderID : '',
            markasReplacementLoader : false,
            levels : [],
            editMode : false,
            levelsWidget : {
                level1 : 0,
                level2 : 0,
                level3 : 0,
                topRatedSeller : 0
            },
            filter : {
                level : "",
                incentive : ""
            },
            reservedAmount : 0
        };
    },
    computed: {
        incompleteLevelsCount() {
            return this.levels.filter(level => level.is_completed == '0').length;
        }
    },
    created() {
        this.csrf = $('meta[name=csrf-token]').attr('content');
        this.fetchRecord();
        this.fetchPayoutRecord();
        this.addDataReset = JSON.parse(JSON.stringify(this.addData));
    },
    methods: {
        getRecommendedPay(item) {
            const balance = item.profit - item.paid_profit;
            if (balance < 0) return '0';
            return this.formatPrice(balance < item.reserved ? balance : item.reserved);
        },
        getNetBalance(item) {
            return (item.profit - item.paid_profit) - item.reserved;
        },
        applyFilter(){
            let vm = this;
            axios
                .post(this.api_url + "dropshippers/levels/filters", vm.filter)
                .then((response) => {
                    vm.levels = response.data.response;
                });
        },
        resetFilter(){
            let vm = this;
            vm.filter = {
                level : "",
                incentive : ""
            }
            axios
                .post(this.api_url + "dropshippers/levels/filters", vm.filter)
                .then((response) => {
                    vm.levels = response.data.response;
                });
        },
        enableEdit(item) {
            this.editMode = true; // Make item editable
        },
        saveItem(item) {
            this.editMode = false;
            let vm = this;
            axios
                .post(this.api_url + "dropshippers/levels/update-requirements", item)
                .then((response) => {
                    vm.levels = response.data.response;
                    return swal({
                        title: "Success",
                        text: "Successfully Updated",
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        fetchPayoutRecord() {
            let vm = this;

            vm.loader = false;
            axios
                .get(this.api_url + "dropshippers/pay-outs/records")
                .then((response) => {
                    vm.payOuts = response.data.response
                });
        },
        getNextDueDate(voucherCreatedAt, paymentCycle) {
            if (!voucherCreatedAt || !paymentCycle) return "N/A"; // Return 'N/A' if data is missing

            const cycleDays = {
                'Weekly': 7,
                'Bi-Weekly': 14,
                'Tri-Weekly': 21,
                'Monthly': 30
            };

            const daysToAdd = cycleDays[paymentCycle];
            if (!daysToAdd) return "N/A"; // Handle unsupported cycles

            const createdDate = new Date(voucherCreatedAt);
            const nextDueDate = new Date(createdDate.setDate(createdDate.getDate() + daysToAdd));
            return nextDueDate.toISOString().split('T')[0]; // Format as YYYY-MM-DD
        },
        markasReplacement(data){
            this.orderID = data.id
        },
        markasReplacementConfirmation(){
            let vm = this;
            vm.markasReplacementLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/mark-as-replacement", { id : this.orderID })
                .then((response) => {
                    vm.markasReplacementLoader = false;

                    $("#markasReplacement").modal('hide');
                    this.fetchDetail(this.orderID);
                    return swal({
                        title: "Success",
                        text: "Marked as Replacement Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        updateDropshipperInformation( data ){
                let vm = this;
                axios
                    .post(this.api_url + "dropshippers", data)
                    .then((response) => {
                        vm.fetchRecord();

                        return swal({
                            title: "Success",
                            text: 'Information updated successfully',
                            icon: "success",
                            timer: 3000,
                        });
                    }).catch((err) => {

                        return swal({
                            title: "Error",
                            text: err.response.data.response[0],
                            icon: "error",
                            timer: 3000,
                        });
                    });
        },
        fetchTracking(id){
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/orders/tracking", { id })
                .then((response) => {
                    vm.trackingDetails = response.data.response
                });
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        printRequest(id) {
            this.id = id;
            const form = this.$refs.requestForm;
            setTimeout(() => {
                form.submit();
            }, 500)
        },
        formatDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY') : 'N/A';
        },
        fetchRecord() {
            let vm = this;

            vm.loader = false;
            axios
                .get(this.api_url + "dropshippers/pay-outs")
                .then((response) => {
                    const results = response.data.response

                    // Calculate request statistics
                    vm.totalPayable = results.total_payable;
                    vm.totalPaid = results.total_paid;
                    vm.totalRemaining = results.total_remaining;
                    vm.remainingDropshippers = results.remaining_dropshippers;
                    vm.totalRecommended = results.total_recommended;
                    vm.levels = results.levels;
                    vm.levelsWidget = {
                        level1: vm.levels.filter(level => level.level === 'Level 01').length,
                        level2: vm.levels.filter(level => level.level === 'Level 02').length,
                        level3: vm.levels.filter(level => level.level === 'Level 03').length,
                        topRatedSeller: vm.levels.filter(level => level.level === 'Top Rated Seller').length
                    };

                    // Assuming `results.remaining_dropshippers` is the array of dropshipper data
                    vm.records= results.dropshippers.map(dropshipper => {
                        // Check if the structure and dropshipper_last_paid_voucher exist
                        if (dropshipper.general_ledger?.dropshipper_shop_ledger?.dropshipper_last_paid_voucher) {
                            dropshipper.voucher_created_at = dropshipper.general_ledger.dropshipper_shop_ledger.dropshipper_last_paid_voucher.created_at;
                        } else {
                            // If not available, set it to null
                            dropshipper.voucher_created_at = null;
                        }
                        return dropshipper;
                    });
                });
        },
        fetchDetail(id, status) {
            let vm = this;
            vm.activeStatus = status;

            axios
                .post(this.api_url + "dropshippers/details", { id })
                .then((response) => {
                    vm.details = response.data.response[0]
                });
        },
        paymentDetail(id) {
            let vm = this;

            vm.selectedDropshipper = id;
            axios
                .post(this.api_url + "dropshippers/payments/data", { id: id })
                .then((response) => {
                    const results = response.data.response
                    vm.orders = results.orders
                    vm.accountBanks = results.banks
                    vm.accountCash = results.cash
                    vm.details = results.dropshipper,
                    vm.reservedAmount = results.reserved_amount;
                });
        },
        paymentHistory( id ){
            let vm = this;
            vm.selectedDropshipper = id;
            axios
                .post(this.api_url + "dropshippers/payments/history", { id: id })
                .then((response) => {
                    const results = response.data.response
                    vm.paymentHistorys = results
                });
        },
        addPayment() {

            if (this.addData.type == null || this.addData.from_account == null) {
                return swal({
                    title: "Error",
                    text: 'Please fill all field',
                    icon: "error",
                    timer: 3000,
                });
            }


            const fd = new FormData()
            // Append each field from addData to the FormData object
            fd.append('id', this.selectedDropshipper);
            fd.append('type', this.addData.type);
            fd.append('from_account', this.addData.from_account); // Sending only the code (adjust as needed)
            fd.append('amount', this.addData.amount);
            fd.append('narration', this.addData.narration);

            // If the attachment is a file, append it as well
            if (this.addData.attachment instanceof File) {
                fd.append('attachment', this.addData.attachment);
            }

            this.addData.id = this.selectedDropshipper;
            this.paymentLoader = true;

            axios
                .post(this.api_url + "dropshippers/payments/add", fd)
                .then((response) => {

                    this.paymentDetail(this.addData.id);
                    this.addData = JSON.parse(JSON.stringify(this.addDataReset));

                    this.paymentLoader = false;

                    this.fetchRecord();

                    return swal({
                        title: "Success",
                        text: 'Saved',
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        dataTable() {
            $("#moq_table").DataTable();
            $("#payout-record").DataTable();
            $("#level-record").DataTable();
        },
        clearDataTable() {
            const table = $("#moq_table").DataTable();
            $("#payout-record").DataTable().destroy();
            $("#level-record").DataTable().destroy();
            table.destroy();
        },
        fetchOrderDetails(id) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/orders/details", { id })
                .then((response) => {
                    vm.orderDetails = response.data.response[0]
                });
        },
        addComment(data) {
            let vm = this;
            vm.commentLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/comments", data)
                .then((response) => {

                    vm.fetchDetail(vm.details.id);
                    vm.commentLoader = false;
                    vm.$emit('commentAdded', true);
                    return swal({
                        title: "Success",
                        text: "Your Comment added successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.commentLoader = false;
                });
        },
        forward( data ){
            let vm = this;
            vm.commentLoader = true;
            axios.post(this.api_url + "inventory/products/orders/update-status", data)
            .then((response) => {

            vm.fetchOrders();
            vm.commentLoader = false;
            vm.$emit('commentAdded', true);
            setTimeout( () => {
                $("#ticket").modal('hide');
            },2000)
            return swal({
                title: "Success",
                text: "Forwarded successfully",
                icon: "success",
                timer: 3000,
            });
            })
            .catch((err) => {
                vm.commentLoader = false;
            });
        },
        reject( data ){
            let vm = this;
            vm.rejectLoader = true;
            axios.post(this.api_url + "inventory/products/orders/reject", data)
            .then((response) => {

            vm.fetchOrders();
            vm.rejectLoader = false;

            setTimeout( () => {
                $("#ticket").modal('hide');
            },2000);

            return swal({
                title: "Success",
                text: "Order Rejected Successfully",
                icon: "success",
                timer: 3000,
            });
            })
            .catch((err) => {
                vm.rejectLoader = false;
            });
        },
        revert( data ){
            let vm = this;
            vm.revertLoader = true;
            axios.post(this.api_url + "inventory/products/orders/revert", data)
            .then((response) => {

            vm.fetchOrders();
            vm.revertLoader = false;

            setTimeout( () => {
                $("#ticket").modal('hide');
            },2000);

            return swal({
                title: "Success",
                text: "Order Revert Successfully",
                icon: "success",
                timer: 3000,
            });
            })
            .catch((err) => {
                vm.revertLoader = false;
            });
        },
        updatePaidAmount( data ){
            let vm = this;
            vm.paidAmountLoader = true;
            axios.post(this.api_url + "inventory/products/orders/update-paid-amount", data)
            .then((response) => {

            this.fetchDetail(data.id);

            vm.paidAmountLoader = false;
                return swal({
                    title: "Success",
                    text: "Amount Updated Successfully",
                    icon: "success",
                    timer: 3000,
                });
            })
            .catch((err) => {
                vm.paidAmountLoader = false;
                return swal({
                    title: "Error",
                    text: "Oops.. Something went wrong",
                    icon: "error",
                    timer: 3000,
                });
            });
        },
        updatePackagingAmount( data ){
            let vm = this;
            vm.paidAmountLoader = true;
            axios.post(this.api_url + "inventory/products/orders/update-packaging-amount", data)
            .then((response) => {

            this.fetchDetail(data.id);

            vm.paidAmountLoader = false;
                return swal({
                    title: "Success",
                    text: "Amount Updated Successfully",
                    icon: "success",
                    timer: 3000,
                });
            })
            .catch((err) => {
                vm.paidAmountLoader = false;
                return swal({
                    title: "Error",
                    text: "Oops.. Something went wrong",
                    icon: "error",
                    timer: 3000,
                });
            });
        },
    },
    watch: {
        records(newLedger) {
            setTimeout(() => {
                $("#moq_table").DataTable({
                    dom: "Bfrtip",
                    buttons: ["copy", "csv", "excel"],
                });
            }, 300);
        },
        payOuts(newLedger) {
            setTimeout(() => {
                $("#payout-record").DataTable({
                    dom: "Bfrtip",
                    buttons: ["copy", "csv", "excel"],
                });
            }, 300);
        },
        levels(newLedger) {
            if ($.fn.DataTable.isDataTable("#level-record")) {
                $('#level-record').DataTable().destroy();
            }
            setTimeout(() => {
                $("#level-record").DataTable({
                    dom: "Bfrtip",
                    buttons: ["copy", "csv", "excel"],
                });
            }, 300);
        },
    },
}
</script>
