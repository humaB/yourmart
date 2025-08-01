<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Statistics <code>( Processed Orders)</code></h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills" id="myTab3" role="tablist">
                            <li class="nav-item">
                              <a class="nav-link active" id="home-tab3" data-toggle="tab" href="#overall" role="tab"
                                aria-controls="home" aria-selected="true">Over All</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" id="profile-tab3" data-toggle="tab" href="#normal" role="tab"
                                aria-controls="profile" aria-selected="false">Normal</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" id="contact-tab3" data-toggle="tab" href="#daraz" role="tab"
                                aria-controls="contact" aria-selected="false">Daraz</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contact-tab3" data-toggle="tab" href="#cash" role="tab"
                                  aria-controls="contact" aria-selected="false">Cash</a>
                              </li>
                          </ul>
                          <div class="tab-content" id="myTabContent2">
                            <div class="tab-pane fade show active" id="overall" role="tabpanel" aria-labelledby="home-tab3">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th>-</th>
                                                <th>Total Orders</th>
                                                <th>Product Price</th>
                                                <th>Courier</th>
                                                <th>Packaging</th>
                                                <th>Total Sales</th>
                                                <th>Amount Received</th>
                                                <th>Amount Remaining</th>
                                            </tr>
                                            <tr>
                                                <th>Today’s</th>
                                                <td>{{ formatPrice(todaySummary.totalOrders) }}</td>
                                                <td>{{ formatPrice(todaySummary.productPrice - ( todaySummary.courier + todaySummary.packaging )) }}</td>
                                                <td>{{ formatPrice(todaySummary.courier) }}</td>
                                                <td>{{ formatPrice(todaySummary.packaging) }}</td>
                                                <td>{{ formatPrice(todaySummary.totalSales) }}</td>
                                                <td>{{ formatPrice(todaySummary.amountReceived) }}</td>
                                                <td>{{ formatPrice(todaySummary.amountRemaining) }}</td>
                                            </tr>

                                            <!-- Overall Orders -->
                                            <tr>
                                                <th>Overall</th>
                                                <td>{{ formatPrice(overallSummary.totalOrders) }}</td>
                                                <td>{{ formatPrice(overallSummary.productPrice - ( overallSummary.courier + overallSummary.packaging) ) }}</td>
                                                <td>{{ formatPrice(overallSummary.courier) }}</td>
                                                <td>{{ formatPrice(overallSummary.packaging) }}</td>
                                                <td>{{ formatPrice(overallSummary.totalSales) }}</td>
                                                <td>{{ formatPrice(overallSummary.amountReceived) }}</td>
                                                <td>{{ formatPrice(overallSummary.amountRemaining) }}</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="normal" role="tabpanel" aria-labelledby="profile-tab3">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th>-</th>
                                                <th>Total Orders</th>
                                                <th>Product Price</th>
                                                <th>Courier</th>
                                                <th>Packaging</th>
                                                <th>Total Sales</th>
                                                <th>Amount Received</th>
                                                <th>Amount Remaining</th>
                                            </tr>
                                            <tr>
                                                <th>Today’s</th>
                                                <td>{{formatPrice(normalToday.totalOrders) }}</td>
                                                <td>{{ formatPrice(normalToday.productPrice - ( normalToday.courier + normalToday.packaging )) }}</td>
                                                <td>{{ formatPrice(normalToday.courier) }}</td>
                                                <td>{{ formatPrice(normalToday.packaging) }}</td>
                                                <td>{{ formatPrice(normalToday.totalSales) }}</td>
                                                <td>{{ formatPrice(normalToday.amountReceived) }}</td>
                                                <td>{{ formatPrice(normalToday.amountRemaining) }}</td>
                                            </tr>

                                            <!-- Overall Orders -->
                                            <tr>
                                                <th>Overall</th>
                                                <td>{{ formatPrice(normalSummary.totalOrders) }}</td>
                                                <td>{{ formatPrice(normalSummary.productPrice - ( normalSummary.courier + normalSummary.packaging) ) }}</td>
                                                <td>{{ formatPrice(normalSummary.courier) }}</td>
                                                <td>{{ formatPrice(normalSummary.packaging) }}</td>
                                                <td>{{ formatPrice(normalSummary.totalSales) }}</td>
                                                <td>{{ formatPrice(normalSummary.amountReceived) }}</td>
                                                <td>{{ formatPrice(normalSummary.amountRemaining) }}</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="cash" role="tabpanel" aria-labelledby="contact-tab3">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th>-</th>
                                                <th>Total Orders</th>
                                                <th>Product Price</th>
                                                <th>Courier</th>
                                                <th>Packaging</th>
                                                <th>Total Sales</th>
                                                <th>Amount Received</th>
                                                <th>Amount Remaining</th>
                                            </tr>
                                            <tr>
                                                <th>Today’s</th>
                                                <td>{{ formatPrice(cashToday.totalOrders) }}</td>
                                                <td>{{ formatPrice(cashToday.productPrice - ( cashToday.courier + cashToday.packaging )) }}</td>
                                                <td>{{ formatPrice(cashToday.courier) }}</td>
                                                <td>{{ formatPrice(cashToday.packaging) }}</td>
                                                <td>{{ formatPrice(cashToday.totalSales) }}</td>
                                                <td>{{ formatPrice(cashToday.amountReceived) }}</td>
                                                <td>{{ formatPrice(cashToday.amountRemaining) }}</td>
                                            </tr>

                                            <!-- Overall Orders -->
                                            <tr>
                                                <th>Overall</th>
                                                <td>{{ formatPrice(cashSummary.totalOrders) }}</td>
                                                <td>{{ formatPrice(cashSummary.productPrice - ( cashSummary.courier + cashSummary.packaging) ) }}</td>
                                                <td>{{ formatPrice(cashSummary.courier) }}</td>
                                                <td>{{ formatPrice(cashSummary.packaging) }}</td>
                                                <td>{{ formatPrice(cashSummary.totalSales) }}</td>
                                                <td>{{ formatPrice(cashSummary.amountReceived) }}</td>
                                                <td>{{ formatPrice(cashSummary.amountRemaining) }}</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="daraz" role="tabpanel" aria-labelledby="contact-tab3">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th>-</th>
                                                <th>Total Orders</th>
                                                <th>Product Price</th>
                                                <th>Courier</th>
                                                <th>Packaging</th>
                                                <th>Total Sales</th>
                                                <th>Amount Received</th>
                                                <th>Amount Remaining</th>
                                            </tr>
                                            <tr>
                                                <th>Today’s</th>
                                                <td>{{formatPrice(darazToday.totalOrders) }}</td>
                                                <td>{{ formatPrice(darazToday.productPrice - ( darazToday.courier + darazToday.packaging )) }}</td>
                                                <td>{{ formatPrice(darazToday.courier) }}</td>
                                                <td>{{ formatPrice(darazToday.packaging) }}</td>
                                                <td>{{ formatPrice(darazToday.totalSales) }}</td>
                                                <td>{{ formatPrice(darazToday.amountReceived) }}</td>
                                                <td>{{ formatPrice(darazToday.amountRemaining) }}</td>
                                            </tr>

                                            <!-- Overall Orders -->
                                            <tr>
                                                <th>Overall</th>
                                                <td>{{ formatPrice(darazSummary.totalOrders) }}</td>
                                                <td>{{ formatPrice(darazSummary.productPrice - ( darazSummary.courier + darazSummary.packaging) ) }}</td>
                                                <td>{{ formatPrice(darazSummary.courier) }}</td>
                                                <td>{{ formatPrice(darazSummary.packaging) }}</td>
                                                <td>{{ formatPrice(darazSummary.totalSales) }}</td>
                                                <td>{{ formatPrice(darazSummary.amountReceived) }}</td>
                                                <td>{{ formatPrice(darazSummary.amountRemaining) }}</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                          </div>

                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card card-primary">
                    <TableHeader :tableHeader="tableHeader" />

                    <div class="card" v-if="role == 'admin' || role == 'supervisor'">
                        <div class="card-body px-2">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Total Orders</th>
                                        <th>Collection</th>
                                        <th>Inventory</th>
                                        <th>QC Manager</th>
                                        <th>Packing </th>
                                        <th>Audit Manager</th>
                                        <th>Dispatched</th>
                                        <th>Under Review</th>
                                        <th>Cancelled</th>
                                        <th>Ready for return</th>
                                        <th>Delivered</th>
                                        <th>Returned</th>
                                        <th>Returned to store</th>
                                    </tr>
                                    <tr>
                                        <td>{{ totalOrders.totalOrders }}</td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.orderCollection) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-warning"
                                                    :style="{ width: getPercentage(totalOrders.orderCollection) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.orderCollection }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.inventoryManager) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-primary"
                                                    :style="{ width: getPercentage(totalOrders.inventoryManager) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.inventoryManager }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.qcManager) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-info"
                                                    :style="{ width: getPercentage(totalOrders.qcManager) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.qcManager }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.packing) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-dark"
                                                    :style="{ width: getPercentage(totalOrders.packing) + '%' }"></div>
                                            </div>
                                            {{ totalOrders.packing }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.audit) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-light"
                                                    :style="{ width: getPercentage(totalOrders.audit) + '%' }"></div>
                                            </div>
                                            {{ totalOrders.audit }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.dispatched) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-success"
                                                    :style="{ width: getPercentage(totalOrders.dispatched) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.dispatched }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.underReview) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-warning"
                                                    :style="{ width: getPercentage(totalOrders.underReview) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.underReview }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.rejected) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-danger"
                                                    :style="{ width: getPercentage(totalOrders.rejected) + '%' }"></div>
                                            </div>
                                            {{ totalOrders.rejected }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.readyForReturn) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-danger"
                                                    :style="{ width: getPercentage(totalOrders.readyForReturn) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.readyForReturn }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.delivered) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-success"
                                                    :style="{ width: getPercentage(totalOrders.delivered) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.delivered }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.returned) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-danger"
                                                    :style="{ width: getPercentage(totalOrders.returned) + '%' }"></div>
                                            </div>
                                            {{ totalOrders.returned }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-text text-right text-secondary">
                                                {{ getPercentage(totalOrders.returnedToStock) }}%
                                            </div>
                                            <div class="progress" data-height="6">
                                                <div class="progress-bar bg-danger"
                                                    :style="{ width: getPercentage(totalOrders.returnedToStock) + '%' }">
                                                </div>
                                            </div>
                                            {{ totalOrders.returnedToStock }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <form @submit.prevent="applyFilter" class="row col-md-12 mb-3">
                            <div class="col-md-3">
                                <label for="">Dropshipper</label>
                                <v-select :options="dropshippers" v-model="filter.dropshipper"></v-select>
                            </div>
                            <div class="col-md-2">
                                <label for="">Order Type</label>
                                <select v-model="filter.type" class="form-control">
                                    <option value="">Select from the following</option>
                                    <option value="Normal">Normal</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Daraz">Daraz</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="">Select Status</label>
                                <select v-model="filter.status" class="form-control">
                                    <option value="">Select from the following</option>
                                    <option value="0">Order Collection</option>
                                    <option value="1">Inventory Manager</option>
                                    <option value="2">QA Manager</option>
                                    <option value="3">Packing/Dispatch</option>
                                    <option value="4">Auditor</option>
                                    <option value="6">Under Review</option>
                                    <option value="7">Rejected</option>
                                    <option value="11">Out for Delivery</option>
                                    <option value="12">Ready for return</option>
                                    <option value="8">Delivered</option>
                                    <option value="9">Returned</option>
                                    <option value="10">Returned to store</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="">From</label>
                                <input type="date" v-model="filter.from" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="">To</label>
                                <input type="date" v-model="filter.to" class="form-control">
                            </div>

                                <div class="col-md-3">
                                    <label for="">Select Courier</label>
                                    <select v-model="filter.courier" class="form-control">
                                        <option value="">Select from the following</option>
                                        <option value="1">Leopard</option>
                                        <option value="2">PostEx</option>
                                    </select>
                                </div>
                            <div class="col-md-2">
                                <label for="">Action</label><br>
                                <button class="btn btn-primary mr-2" @click="applyFilter">Filter</button>
                                <button class="btn btn-danger" @click="resetFilter">Reset</button>
                            </div>
                        </form>
                    </div>

                    <div class="card-body row">
                        <!-- Table -->
                        <div class="col-md-12 mt-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-md-12 mb-2 text-right">
                                            <button class="btn btn-primary" @click="multipleActionFunc('Product List')" v-if="role == 'admin' || role == 'supervisor' || role == 'inventory manager'">Get Products List</button>
                                            <button class="btn btn-primary" @click="multipleActionFunc('Print Labels')" v-if="role == 'admin' || role == 'supervisor' || role == 'qc manager' || role == 'packing & dispatch manager'">Print Courier Labels</button>
                                        </div>
                                        <div class="card-body table-responsive" v-if="loader">
                                            <bullet-list-loader :width="250"> </bullet-list-loader>
                                        </div>
                                        <div class="col-md-12 table-responsive" v-else>

                                            <table class="table table-bordered" :id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Sr #</th>
                                                        <th>
                                                            <input type="checkbox"
                                                                class="form-control custom-checkbox"
                                                                v-model="checkedAllOrders"
                                                                @change="toggleAllOrders">
                                                        </th>
                                                        <th>Reference ID</th>
                                                        <th>Type</th>
                                                        <th>Dropshipper</th>
                                                        <th>Order #</th>
                                                        <th>Tracking Number</th>
                                                        <th>Product Price</th>
                                                        <th>Courier</th>
                                                        <th>Packaging</th>
                                                        <th>Total Tax</th>
                                                        <th>Total Cost</th>
                                                        <th>Received</th>
                                                        <th>Remaining</th>
                                                        <th>COD</th>
                                                        <th>Advance</th>
                                                        <th>Total Payable</th>
                                                        <th>Total Paid</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Re-attempt</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item, index) in orders" :key="item.id">
                                                        <td>{{ index + 1 }}</td>
                                                        <td>
                                                            <div class="pretty p-default p-round p-thick">
                                                                <input type="checkbox"
                                                                    class="form-control custom-checkbox"
                                                                    v-model="selectedOrders" :value="item.id">
                                                            </div>
                                                        </td>
                                                        <td>{{ item.id }}</td>
                                                        <td>{{ item.type }}</td>
                                                        <td>{{ item.user ? item.user.name : '-' }}</td>
                                                        <td>
                                                            {{ item.shop ? `${item.shop.store_name.substring(0,
                                                                3)}-${item.order_no}` : item.order_no }}
                                                        </td>
                                                        <td>
                                                            <span v-if="item.type === 'Normal'">
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#trackingInformation"
                                                                    @click="fetchTracking(item.id)">
                                                                    {{ item.tracking_number }}
                                                                </a>
                                                            </span>
                                                            <span v-else>
                                                                {{ item.type }}
                                                            </span>
                                                        </td>

                                                        <td>{{ formatPrice(item.product_cost) }}</td>
                                                        <td>{{ item.courier_service_price }}</td>
                                                        <td>{{ item.packaging_price }}</td>
                                                        <td>{{ item.shipping_tax + item.subtotal_tax }}</td>
                                                        <td>{{ formatPrice(item.total_bill) }}</td>
                                                        <td>{{ formatPrice(item.paid_amount) }}</td>
                                                        <td>{{ formatPrice(item.remaining_amount) }}</td>
                                                        <td>{{ formatPrice(item.selling_price) }}</td>
                                                        <td>{{ formatPrice(item.advance_amount) }}</td>
                                                        <td>{{ formatPrice(item.total_profit) }}</td>
                                                        <td>{{ formatPrice(item.total_paid_profit) }}</td>
                                                        <td>{{ formatDate(item.created_at) }}</td>
                                                        <td>
                                                            <span class="badge badge-warning text-dark"
                                                                v-if="item.status == 0">Order Collection</span>
                                                            <span class="badge badge-info text-dark"
                                                                v-else-if="item.status == 1">Inventory Issuance</span>
                                                            <span class="badge badge-secondary"
                                                                v-else-if="item.status == 2">QC</span>
                                                            <span class="badge badge-success"
                                                                v-else-if="item.status == 3">Packing/Dispatch</span>
                                                            <span class="badge badge-warning text-dark"
                                                                v-else-if="item.status == 4">Audit</span>
                                                            <span class="badge badge-succes"
                                                                v-else-if="item.status == 5">Dispatched</span>
                                                            <span class="badge badge-danger"
                                                                v-else-if="item.status == 6">Cancellation Under
                                                                Review</span>
                                                            <span class="badge badge-danger"
                                                                v-else-if="item.status == 7">Cancelled</span>
                                                            <span class="badge badge-success"
                                                                v-else-if="item.status == 8">Delivered</span>
                                                            <span class="badge badge-danger"
                                                                v-else-if="item.status == 9">Returned</span>
                                                            <span class="badge badge-danger"
                                                                v-else-if="item.status == 10">Returned To Stock</span>
                                                            <span class="badge badge-warning"
                                                                v-else-if="item.status == 11">Out for delivery</span>
                                                            <span class="badge badge-warning"
                                                                v-else-if="item.status == 12">Ready for Return</span>
                                                        </td>
                                                        <td v-if="item.status == 12 && (role == 'admin' || role == 'supervisor' || role == 'auditor')">
                                                            <button v-if="!item.re_attempt" class="btn btn-primary" @click="fetchDetail(item.id)" data-toggle="modal" data-target="#orderReattempt" title="Press to reattempt">
                                                                Active
                                                            </button>
                                                            <button v-else class="btn btn-success" @click="fetchDetail(item.id)" data-toggle="modal" data-target="#orderReattempt" title="Press to reattempt">
                                                                Already Applied
                                                            </button>
                                                        </td>
                                                        <td v-else>-</td>
                                                        <td>
                                                            <button class="btn btn-info" @click="fetchDetail(item.id)"
                                                                data-toggle="modal" data-target="#ticket"
                                                                title="View Details"><i class="fa fa-eye"></i></button>

                                                            <button class="btn btn-danger" title="Mark as being return" @click="markasReplacement({ id : item.id })" data-toggle="modal" data-target="#markasBeingReturn" v-if="item.status == 11">
                                                                <i class="fas fa-undo-alt"></i>
                                                            </button>
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

        <OrderDetailView :revertLoader="revertLoader" :rejectLoader="rejectLoader" :paidAmountLoader="paidAmountLoader"
            :details="details" :loader="commentLoader" :role="role" @addComment="addComment($event)"
            @forward="forward($event)" @forwardToTest="forwardToTest($event)" @reject="reject($event)" @revert="revert($event)"
            @fetchDropshipperDetails="fetchDropshipperDetails($event)" @updatePaidAmount="updatePaidAmount($event)"
            @updatePackagingAmount="updatePackagingAmount($event)" @markasReplacement="markasReplacement($event)"
            @markAsBeingReturn="markasReplacement($event)"
            @markAsDelivered="markasReplacement($event)"
            @addDiscount="addDiscount($event)"
            @deleteComment="deleteComment($event)"
            @markAsHold="markasReplacement($event)"
        />

        <DropshipperDetails :details="dropShipperDetails" />

        <TrackingDetailPopup :trackingDetails="trackingDetails" />
        <!-- Modal -->
        <OrderMarkasReplacementConfirmation :orderID="orderID" :loader="markasReplacementLoader"
            @markasReplacementConfirmation="markasReplacementConfirmation($event)" />

        <OrderMarkasBeingReturnConfirmation
            :orderID="orderID"
            :loader="markasReplacementLoader"
            @markasBeingReturnConfirmation="markasBeingReturnConfirmation($event)"
        />

        <OrderMarkasDeliveredConfirmation
            :orderID="orderID"
            :loader="markasReplacementLoader"
            @markasDeliveredConfirmation="markasDeliveredConfirmation($event)"
        />

        <OrderSelectedProductList
            :products="selectedProductList"
        />

        <OrderSelectedLabelPrint
            :labels="selectedOrderLabels"
        />

        <OrderReattempt
            :loader="btnLoader"
            :details="details"
            @reAttempt="reAttempt($event)"
        />

        <OrderAdjustmentConfirmation
            :orderID="orderID"
            :loader="markasReplacementLoader"
            @markasNotDelivered="markasNotDelivered($event)"
        />

        <OrderMarkasHoldConfirmation
            :orderID="orderID"
            :loader="markasReplacementLoader"
            @markasHoldConfirmation="markasHoldConfirmation($event)"
        />

    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";
import TableHeader from "../../../../components/table/TableHeaderComponent.vue";
import OrderDetailView from "../../../../components/inventory/product/order/OrderDetailView.vue";
import DropshipperDetails from "../../../../components/admin/request/DropshipperDetails.vue";
import TrackingDetailPopup from "../../../../components/inventory/product/order/TrackingDetailPopup.vue";
import OrderMarkasReplacementConfirmation from "../../../../components/inventory/product/order/OrderMarkasReplacementConfirmation.vue";
import OrderMarkasBeingReturnConfirmation from "../../../../components/inventory/product/order/OrderMarkasBeingReturnConfirmation.vue";
import OrderMarkasDeliveredConfirmation from "../../../../components/inventory/product/order/OrderMarkasDeliveredConfirmation.vue";
import OrderSelectedProductList from "../../../../components/inventory/product/order/OrderSelectedProductList.vue";
import OrderSelectedLabelPrint from "../../../../components/inventory/product/order/OrderSelectedLabelPrint.vue";
import OrderReattempt from "../../../../components/inventory/product/order/OrderReattempt.vue";
import OrderAdjustmentConfirmation from "../../../../components/inventory/product/order/OrderAdjustmentConfirmation.vue";
import OrderMarkasHoldConfirmation from "../../../../components/inventory/product/order/OrderMarkasHoldConfirmation.vue";

export default {
    name: 'ProductOrderPage',
    components: {
        TableHeader,
        BulletListLoader,
        OrderDetailView,
        DropshipperDetails,
        OrderMarkasReplacementConfirmation,
        TrackingDetailPopup,
        OrderMarkasBeingReturnConfirmation,
        OrderMarkasDeliveredConfirmation,
        OrderSelectedProductList,
        OrderSelectedLabelPrint,
        OrderReattempt,
        OrderAdjustmentConfirmation,
        OrderMarkasHoldConfirmation
    },
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "All Orders",
            },
            th: ["Sr #", "Order #", "Belongs To", "Total Amount", "Paid Amount", "Remaining Amount", "Added Date", "Status", "Action"],
            table_id: "moq_table",
            btnLoader: false,
            orders: [],
            loader: true,
            details: {},
            commentLoader: false,
            rejectLoader: false,
            revertLoader: false,
            role: '',
            dropShipperDetails: {},
            paidAmountLoader: false,
            totalOrders: {
                totalOrders: 0,
                orderCollection: 0,
                inventoryManager: 0,
                qcManager: 0,
                packing: 0,
                audit: 0,
                dispatched: 0,
                underReview: 0,
                rejected: 0,
                delivered: 0,
                returned: 0,
                returnedToStock: 0,
                readyForReturn : 0
            },
            filter: {
                dropshipper : { code : 0 , label : 'Select from the following'},
                type : '',
                status: '',
                from: '',
                to: '',
                courier : ""
            },
            trackingDetails: [],
            orderID: '',
            markasReplacementLoader: false,
            dropshippers : [],
            checkedAllOrders : false,
            selectedOrders: [],
            multipleAction: '',
            selectedProductList : [],
            selectedOrderLabels : []
        };
    },
    computed: {
        // Calculate today's summary
        todaySummary() {
            let today = new Date().toISOString().slice(0, 10); // Get today's date in YYYY-MM-DD format

            return this.orders.reduce((totals, order) => {
                let orderDate = new Date(order.created_at).toISOString().slice(0, 10);

                if (orderDate === today && (order.status !== 6 && order.status !== 7)) {
                    totals.totalOrders++;
                    totals.productPrice += parseFloat(order.total_bill || 0);
                    totals.courier += parseFloat(order.courier_service_price || 0);
                    totals.packaging += parseFloat(order.packaging_price || 0);
                    totals.totalSales += parseFloat(order.total_bill || 0);
                    totals.amountReceived += parseFloat(order.paid_amount || 0);
                    totals.amountRemaining += parseFloat(order.remaining_amount || 0);
                }

                return totals;
            }, {
                totalOrders: 0,
                productPrice: 0,
                courier: 0,
                packaging: 0,
                totalSales: 0,
                amountReceived: 0,
                amountRemaining: 0,
            });
        },

        // Calculate overall summary
        overallSummary() {
            return this.orders.reduce((totals, order) => {
                if ((order.status !== 6 && order.status !== 7)) {
                    totals.totalOrders++;
                    totals.productPrice += parseFloat(order.total_bill || 0);
                    totals.courier += parseFloat(order.courier_service_price || 0);
                    totals.packaging += parseFloat(order.packaging_price || 0);
                    totals.totalSales += parseFloat(order.total_bill || 0);
                    totals.amountReceived += parseFloat(order.paid_amount || 0);
                    totals.amountRemaining += parseFloat(order.remaining_amount || 0);
                }
                return totals;
            }, {
                totalOrders: 0,
                productPrice: 0,
                courier: 0,
                packaging: 0,
                totalSales: 0,
                amountReceived: 0,
                amountRemaining: 0,
            });
        },
        // Calculate summary for "Normal" type
        normalToday() {

            let today = new Date().toISOString().slice(0, 10); // Get today's date in YYYY-MM-DD format

            return this.orders.reduce((totals, order) => {
                let orderDate = new Date(order.created_at).toISOString().slice(0, 10);

                if (orderDate === today && order.type === 'Normal' && (order.status !== 6 && order.status !== 7)) {

                    totals.totalOrders++;
                    totals.productPrice += parseFloat(order.total_bill || 0);
                    totals.courier += parseFloat(order.courier_service_price || 0);
                    totals.packaging += parseFloat(order.packaging_price || 0);
                    totals.totalSales += parseFloat(order.total_bill || 0);
                    totals.amountReceived += parseFloat(order.paid_amount || 0);
                    totals.amountRemaining += parseFloat(order.remaining_amount || 0);
                }
                return totals;
            }, {
                totalOrders: 0,
                productPrice: 0,
                courier: 0,
                packaging: 0,
                totalSales: 0,
                amountReceived: 0,
                amountRemaining: 0,
            });
        },
            // Calculate summary for "Normal" type
        normalSummary() {
            return this.orders.reduce((totals, order) => {
                if (order.type === 'Normal' && (order.status !== 6 && order.status !== 7)) {
                    totals.totalOrders++;
                    totals.productPrice += parseFloat(order.total_bill || 0);
                    totals.courier += parseFloat(order.courier_service_price || 0);
                    totals.packaging += parseFloat(order.packaging_price || 0);
                    totals.totalSales += parseFloat(order.total_bill || 0);
                    totals.amountReceived += parseFloat(order.paid_amount || 0);
                    totals.amountRemaining += parseFloat(order.remaining_amount || 0);
                }
                return totals;
            }, {
                totalOrders: 0,
                productPrice: 0,
                courier: 0,
                packaging: 0,
                totalSales: 0,
                amountReceived: 0,
                amountRemaining: 0,
            });
        },
        // Calculate summary for "Daraz" type
        darazToday() {
            let today = new Date().toISOString().slice(0, 10); // Get today's date in YYYY-MM-DD format
                return this.orders.reduce((totals, order) => {
                    let orderDate = new Date(order.created_at).toISOString().slice(0, 10);
                    if (orderDate === today && order.type === 'Daraz' && order.status <= 5) {
                        totals.totalOrders++;
                        totals.productPrice += parseFloat(order.total_bill || 0);
                        totals.courier += parseFloat(order.courier_service_price || 0);
                        totals.packaging += parseFloat(order.packaging_price || 0);
                        totals.totalSales += parseFloat(order.total_bill || 0);
                        totals.amountReceived += parseFloat(order.paid_amount || 0);
                        totals.amountRemaining += parseFloat(order.remaining_amount || 0);
                    }
                    return totals;
                }, {
                    totalOrders: 0,
                    productPrice: 0,
                    courier: 0,
                    packaging: 0,
                    totalSales: 0,
                    amountReceived: 0,
                    amountRemaining: 0,
                });
        },
        // Calculate summary for "Daraz" type
        darazSummary() {
            return this.orders.reduce((totals, order) => {
                if (order.type === 'Daraz' && order.status <= 5) {
                    totals.totalOrders++;
                    totals.productPrice += parseFloat(order.total_bill || 0);
                    totals.courier += parseFloat(order.courier_service_price || 0);
                    totals.packaging += parseFloat(order.packaging_price || 0);
                    totals.totalSales += parseFloat(order.total_bill || 0);
                    totals.amountReceived += parseFloat(order.paid_amount || 0);
                    totals.amountRemaining += parseFloat(order.remaining_amount || 0);
                }
                return totals;
            }, {
                totalOrders: 0,
                productPrice: 0,
                courier: 0,
                packaging: 0,
                totalSales: 0,
                amountReceived: 0,
                amountRemaining: 0,
            });
        },
        // Calculate summary for "Cash" type
        cashToday() {
            let today = new Date().toISOString().slice(0, 10); // Get today's date in YYYY-MM-DD format
            return this.orders.reduce((totals, order) => {
                let orderDate = new Date(order.created_at).toISOString().slice(0, 10);
                if (orderDate === today && order.type === 'Cash' && order.status <= 5) {
                    totals.totalOrders++;
                    totals.productPrice += parseFloat(order.total_bill || 0);
                    totals.courier += parseFloat(order.courier_service_price || 0);
                    totals.packaging += parseFloat(order.packaging_price || 0);
                    totals.totalSales += parseFloat(order.total_bill || 0);
                    totals.amountReceived += parseFloat(order.paid_amount || 0);
                    totals.amountRemaining += parseFloat(order.remaining_amount || 0);
                }
                return totals;
            }, {
                totalOrders: 0,
                productPrice: 0,
                courier: 0,
                packaging: 0,
                totalSales: 0,
                amountReceived: 0,
                amountRemaining: 0,
            });
        },
        // Calculate summary for "Cash" type
        cashSummary() {
        return this.orders.reduce((totals, order) => {
            if (order.type === 'Cash' && order.status <= 5) {
                totals.totalOrders++;
                totals.productPrice += parseFloat(order.total_bill || 0);
                totals.courier += parseFloat(order.courier_service_price || 0);
                totals.packaging += parseFloat(order.packaging_price || 0);
                totals.totalSales += parseFloat(order.total_bill || 0);
                totals.amountReceived += parseFloat(order.paid_amount || 0);
                totals.amountRemaining += parseFloat(order.remaining_amount || 0);
            }
            return totals;
        }, {
            totalOrders: 0,
            productPrice: 0,
            courier: 0,
            packaging: 0,
            totalSales: 0,
            amountReceived: 0,
            amountRemaining: 0,
        });
        },
    },
    created() {
        this.fetchOrders();
        this.fetchDropshippers();
    },
    methods: {
        reAttempt( data ){
            let vm = this;
            vm.btnLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/re-attempts", data)
                .then((response) => {
                    const results = response.data.response;
                    vm.btnLoader = false;
                    vm.fetchDetail(data.id);
                    vm.fetchOrders();
                    return swal({
                        title: "Success",
                        text: "Re-attempt request sent successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });;
        },
        multipleActionFunc( value ) {
            let vm = this;

            const data = {
                products: vm.selectedOrders,
                action: value
            }
            vm.btnLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/actions", data)
                .then((response) => {
                    vm.btnLoader = false;
                    vm.selectedOrders = [];

                    const results = response.data.response;
                    if(value == "Product List"){
                        vm.selectedProductList = results;
                        $("#selectedOrderList").modal('show')
                    }
                    if( value == "Print Labels"){
                        vm.selectedOrderLabels = results;
                        $("#selectedOrderPrints").modal('show')
                    }

                    vm.multipleAction = "";

                }).catch((err) => {
                    vm.btnLoader = false;
                    return swal({
                        title: "Error",
                        text: 'Oops, Something went wrong please try again',
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        toggleAllOrders() {
            if (this.checkedAllOrders) {
                // If "Check All" is checked, select all orders
                this.selectedOrders = this.orders.map(order => order.id);
            } else {
                // If "Check All" is unchecked, deselect all orders
                this.selectedOrders = [];
            }
        },
        fetchDropshippers() {
            axios.get(this.api_url + "dropshippers/drop-down")
                .then((res) => {
                    const results = res.data.response;
                    this.dropshippers = results;
                });
        },
        markasReplacement(data) {
            this.orderID = data.id
        },
        markasReplacementConfirmation() {
            let vm = this;
            vm.markasReplacementLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/mark-as-replacement", { id: this.orderID })
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
        markasBeingReturnConfirmation() {
            let vm = this;
            vm.markasReplacementLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/mark-as-being-return", { id: this.orderID })
                .then((response) => {
                    vm.markasReplacementLoader = false;

                    $("#markasBeingReturn").modal('hide');
                    this.fetchOrders();
                    this.fetchDetail(this.orderID);
                    return swal({
                        title: "Success",
                        text: "Marked as Returned Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        markasDeliveredConfirmation() {
            let vm = this;
            vm.markasReplacementLoader = true;
            axios
                .post(this.api_url + "inventory/products/orders/mark-as-delivered", { id: this.orderID })
                .then((response) => {
                    vm.markasReplacementLoader = false;

                    $("#markasDelivered").modal('hide');
                    this.fetchOrders();
                    this.fetchDetail(this.orderID);
                    return swal({
                        title: "Success",
                        text: "Marked as Delivered Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        markasNotDelivered() {
            let vm = this;
            vm.markasReplacementLoader = true;
            axios
            .post(this.api_url + "inventory/products/orders/mark-as-not-delivered", { id: this.orderID })
                .then((response) => {
                    vm.markasReplacementLoader = false;

                    $("#markasDelivered").modal('hide');
                    this.fetchOrders();
                    this.fetchDetail(this.orderID);
                    return swal({
                        title: "Success",
                        text: "Marked as Delivered Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        markasHoldConfirmation() {
            let vm = this;
            vm.markasReplacementLoader = true;
            axios
            .post(this.api_url + "inventory/products/orders/mark-as-hold", { id: this.orderID })
                .then((response) => {
                    vm.markasReplacementLoader = false;

                    $("#markasHold").modal('hide');
                    this.fetchDetail(this.orderID);
                    return swal({
                        title: "Success",
                        text: "Marked as Hold Successfully",
                        icon: "success",
                        timer: 3000,
                    });
                });
        },
        fetchTracking(id) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/orders/tracking", { id })
                .then((response) => {
                    vm.trackingDetails = response.data.response
                });
        },
        applyFilter() {
            this.fetchOrders();
        },
        resetFilter() {
            let vm = this;
            vm.filter = {
                type : '',
                dropshipper : { code : 0 , label : 'Select from the following'},
                status: '',
                from: '',
                to: '',
                courier : ""
            }

            this.fetchOrders();
        },
        getPercentage(statusCount) {
            if (this.totalOrders.totalOrders === 0) return 0;
            return Math.round((statusCount / this.totalOrders.totalOrders) * 100);
        },
        formatDate(date) {
            return date ? moment.utc(date).format('DD-MMM-YYYY') : 'N/A';
        },
        formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        updatePaidAmount(data) {
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
        addDiscount(data) {
            let vm = this;
            vm.paidAmountLoader = true;
            axios.post(this.api_url + "inventory/products/orders/add-discount", data)
                .then((response) => {

                    this.fetchDetail(data.id);

                    vm.paidAmountLoader = false;
                    return swal({
                        title: "Success",
                        text: "Discount added Successfully",
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
        updatePackagingAmount(data) {
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
        fetchOrders() {
            let vm = this;

            vm.loader = true;
            if ($.fn.DataTable.isDataTable("#moq_table")) {
                this.clearDataTable();
            }

            axios
                .get(this.api_url + "inventory/products/orders", {
                    params: {
                        droshipper : vm.filter.dropshipper.code,
                        type : vm.filter.type,
                        status: vm.filter.status,
                        from: vm.filter.from,
                        to: vm.filter.to,
                        courier: vm.filter.courier,
                    },
                })
                .then((response) => {
                    vm.orders = response.data.response.orders
                    vm.role = response.data.response.role

                    // Reset totalOrders object before populating it
                    vm.totalOrders = {
                        totalOrders: 0,
                        orderCollection: 0,
                        inventoryManager: 0,
                        qcManager: 0,
                        packing: 0,
                        audit: 0,
                        underReview: 0,
                        rejected: 0,
                        dispatched: 0,
                        delivered: 0,
                        returned: 0,
                        returnedToStock: 0,
                        readyForReturn : 0
                    };

                    // Process orders and calculate total counts based on the status
                    vm.orders.forEach((order) => {
                        vm.totalOrders.totalOrders++;
                        switch (order.status) {
                            case 0: // Order Collection
                                vm.totalOrders.orderCollection++;
                                break;
                            case 1: // Inventory Manager
                                vm.totalOrders.inventoryManager++;
                                break;
                            case 2: // QA Manager
                                vm.totalOrders.qcManager++;
                                break;
                            case 3: // Packing/Dispatch
                                vm.totalOrders.packing++;
                                break;
                            case 4: // Auditor
                                vm.totalOrders.audit++;
                                break;
                            case 5: // Dispatched
                                vm.totalOrders.dispatched++;
                                break;
                            case 6: // Under Review
                                vm.totalOrders.underReview++;
                                break;
                            case 7: // Rejected
                                vm.totalOrders.rejected++;
                                break;
                            case 8: // Delivered
                                vm.totalOrders.delivered++;
                                break;
                            case 9: // Returned
                                vm.totalOrders.returned++;
                                break;
                            case 10: // Returned
                                vm.totalOrders.returnedToStock++;
                                break;
                            case 12: //Ready for return
                                vm.totalOrders.readyForReturn++;
                                break;
                        }
                    });

                    setTimeout(() => {
                        this.dataTable();
                    }, 300);
                    vm.loader = false;
                });
        },
        fetchDetail(id) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/orders/details", { id })
                .then((response) => {
                    vm.details = response.data.response[0]
                });
        },
        fetchDropshipperDetails(data) {
            let vm = this;
            axios
                .post(this.api_url + "dropshippers/details", { id: data.id })
                .then((response) => {
                    vm.dropShipperDetails = response.data.response[0]
                });

        },
        forward(data) {
            let vm = this;
            vm.commentLoader = true;
            axios.post(this.api_url + "inventory/products/orders/update-status", data)
                .then((response) => {

                    vm.fetchOrders();
                    $("#ticket").modal('hide');
                    
                    vm.commentLoader = false;
                    vm.$emit('commentAdded', true);


                    return swal({
                        title: "Success",
                        text: "Forwarded successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
                .catch((err) => {
                    vm.commentLoader = false;

                    return swal({
                        title: "Error",
                        text: err.response.data.response[0],
                        icon: "error",
                        timer: 3000,
                    });
                });
        },
        forwardToTest(data) {
            let vm = this;
            axios.post(this.api_url + "inventory/products/orders/update-status/test", data)
            .then((response) => {})
        },
        reject(data) {
            let vm = this;
            vm.rejectLoader = true;
            axios.post(this.api_url + "inventory/products/orders/reject", data)
                .then((response) => {

                    vm.fetchOrders();
                    vm.rejectLoader = false;

                    setTimeout(() => {
                        $("#ticket").modal('hide');
                    }, 2000);

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
        revert(data) {
            let vm = this;
            vm.revertLoader = true;
            axios.post(this.api_url + "inventory/products/orders/revert", data)
                .then((response) => {

                    vm.fetchOrders();
                    vm.revertLoader = false;

                    setTimeout(() => {
                        $("#ticket").modal('hide');
                    }, 2000);

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
        deleteComment(data) {
            let vm = this;
            axios.post(this.api_url + "inventory/products/orders/comments/delete", data)
                .then((response) => {

                    this.fetchDetail(data.id);
                    return swal({
                        title: "Success",
                        text: "Comment deleted successfully",
                        icon: "success",
                        timer: 3000,
                    });
                })
        },
        changeStatus(data) {
            let vm = this;
            axios
                .post(this.api_url + "inventory/products/settings/shipping-classes/change-status", data)
                .then((response) => {
                    vm.fetchRecord();
                    vm.activeStatus = !vm.activeStatus;
                });
        },
        dataTable() {
            $("#moq_table").DataTable({
                dom: "Bfrtip",
                buttons: ["copy", "csv", "excel"],
            });
        },
        clearDataTable() {
            $("#moq_table").DataTable().clear().destroy();
        },
    },
}
</script>
<style scoped>
.custom-checkbox {
    width: 16px;
    /* Adjust the width as needed */
    height: 16px;
    /* Adjust the height as needed */
    transform: scale(0.8);
    /* You can also use scale to adjust the size */
}
</style>
