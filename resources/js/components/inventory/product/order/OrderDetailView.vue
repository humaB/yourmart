<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="ticket" tabindex="-1" role="dialog" aria-labelledby="ticket" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 90%;" role="document">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <div>
                            Order # <b>{{ details.shop ? details.shop.store_name.substring(0, 3) + '-' : '' }}{{
                                details.order_no }}</b>, Order Type <b>{{ details.type }}</b>
                        </div> <!-- Empty div to push the content to the right -->
                        <div class="d-flex">
                            <h5>
                                <span class="badge badge-warning text-dark" v-if="details.status == 0">Order
                                    Collection</span>
                                <span class="badge badge-info text-dark" v-else-if="details.status == 1">Inventory
                                    Issuance</span>
                                <span class="badge badge-secondary" v-else-if="details.status == 2">QC</span>
                                <span class="badge badge-success"
                                    v-else-if="details.status == 3">Packing/Dispatch</span>
                                <span class="badge badge-warning text-dark" v-else-if="details.status == 4">Audit</span>
                                <span class="badge badge-succes" v-else-if="details.status == 5">Dispatched</span>
                                <span class="badge badge-danger" v-else-if="details.status == 6">Cancellation Under
                                    Review</span>
                                <span class="badge badge-danger" v-else-if="details.status == 7">Cancelled</span>
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body row">
                        <!-- Subject Section -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="boxs mail_listing">
                                    <div class="inbox-body no-pad">
                                        <div class="mail-heading d-flex justify-content-between">
                                            <h4 class="vew-mail-header">
                                                <h3>Order Details # {{ details.shop ?
                                                    details.shop.store_name.substring(0, 3)+'-' : '' }}{{
                                                    details.order_no }}</h3>
                                            </h4>
                                            <h4 v-if="details.is_replacement == 1">
                                                Replacement Order
                                            </h4>
                                        </div>
                                        <hr />
                                        <div class="view-mail p-t-20">


                                            <!-- Customer Information -->
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h5>Customer Information</h5>
                                                </div>
                                                <div class="card-body row">
                                                    <div class="col-md-8">
                                                        <p><strong>Name:</strong> {{ details.customer_name }}</p>
                                                        <p><strong>Address:</strong> {{ details.address }}</p>
                                                        <p><strong>Phone Number 1:</strong> {{ details.phone_number }}
                                                        </p>
                                                        <p><strong>Phone Number 2:</strong> {{ details.phone_number2 }}
                                                        </p>
                                                        <p><strong>City:</strong> {{ details.city ? details.city.name :
                                                            ''
                                                            }}</p>
                                                    </div>
                                                    <div class="col-md-4"
                                                        v-if="details.user && details.user.dropshipper">
                                                        <div class="card author-box">
                                                            <div class="card-body">
                                                                <div class="author-box-center">
                                                                    <img alt="image"
                                                                        :src="`${web_url}public/storage/uploads/dropshipper/${details.user.dropshipper.profile_image}`"
                                                                        width="100%"
                                                                        class="rounded-circle author-box-picture">
                                                                    <div class="clearfix"></div>
                                                                    <div class="author-box-name">
                                                                        <a href="#"
                                                                            @click="fetchDropshipperDetails(details.user.dropshipper.id)"
                                                                            data-toggle="modal"
                                                                            data-target="#dropShipperDetail">{{
                                                                            details.user.dropshipper.full_name }}</a>
                                                                        <div class="author-box-job">{{
                                                                            details.user.dropshipper.whatsapp_number }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Order Information -->
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h5>Order Information</h5>
                                                </div>
                                                <div class="card-body">
                                                    <h5 class="d-flex justify-content-between align-items-center">
                                                        <span>Order # {{ details.shop ?
                                                            details.shop.store_name.substring(0, 3) + '-' : '' }}{{
                                                            details.order_no }}</span>
                                                        <span>Date/Time : {{ formatNormalDate(details.created_at)
                                                            }}</span>
                                                    </h5>
                                                    <div class="row mt-3">
                                                        <div class="col-md-8">
                                                            <h5 v-if="details.type == 'Normal'">Tracking # {{
                                                                details.tracking_number }}</h5>
                                                            <a v-if="details.type == 'Normal'" :href="details.slip_link"
                                                                target="_blank">Press to Print</a>
                                                            <p v-if="details.type == 'Normal'" class="mt-2">
                                                                <strong>Courier Service:</strong> {{ details.courier ?
                                                                details.courier.courier_name : 'N/A' }}</p>
                                                            <p v-if="details.type == 'Normal'"><strong>Selected Package
                                                                    :</strong> {{ details.range ?
                                                                details.range.category.name : 'N/A' }}</p>
                                                            <p v-if="details.type == 'Normal'"><strong>Courier
                                                                    Instructions:</strong> {{ details.instructions }}
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4 text-right">
                                                            <p><strong>Shop:</strong> {{ details.shop ?
                                                                details.shop.store_name: 'N/A' }}</p>
                                                            <p><strong>Order Notes:</strong> {{ details.order_note }}
                                                            </p>
                                                            <p><strong>No of labels:</strong> {{ details.no_of_labels }}
                                                            </p>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-md-12 row">
                                                            <div class="col-md-12">

                                                                <table class="table table-bordered table-sm">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><strong>Product Cost:</strong></td>
                                                                            <td class="text-left">{{
                                                                                formatPrice(parseFloat(details.total_bill)
                                                                                -
                                                                                (parseFloat(details.courier_service_price)
                                                                                + parseFloat(details.packaging_price)))
                                                                                }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Courier Charges:</strong></td>
                                                                            <td class="text-left">{{
                                                                                formatPrice(details.courier_service_price)
                                                                                }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Packing Charges:</strong></td>
                                                                            <td class="text-left">{{
                                                                                formatPrice(details.packaging_price) }}
                                                                            </td>
                                                                        </tr>

                                                                        <!-- Total Order Amount -->
                                                                        <tr>
                                                                            <td class="h6"><strong>Total Order
                                                                                    Amount:</strong></td>
                                                                            <td class="text-left font-weight-bold">{{
                                                                                formatPrice(details.total_bill) }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <h6>{{ details.type }} Order</h6>
                                                                <table class="table table-bordered table-sm">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><strong>Your Mart</strong></td>
                                                                            <td><strong>Receivable</strong></td>
                                                                            <td><strong>Received</strong></td>
                                                                            <td ><strong>Remaining</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong></strong></td>
                                                                            <td v-if="details.type == 'Normal'">{{ formatPrice(details.total_bill) }}</td>
                                                                            <td v-if="details.type == 'Normal'">{{formatPrice(details.advance_amount) }}</td>
                                                                            <td v-if="details.type == 'Normal'">{{ formatPrice(parseFloat(details.total_bill) - parseFloat(details.advance_amount)) }}</td>

                                                                            <td v-if="details.type == 'Cash' || details.type ==  'Daraz'">{{ formatPrice(details.total_bill) }}</td>
                                                                            <td v-if="details.type == 'Cash' || details.type ==  'Daraz'">{{ formatPrice(details.paid_amount) }}</td>
                                                                            <td v-if="details.type == 'Cash' || details.type ==  'Daraz'">{{ formatPrice(details.total_bill - details.paid_amount) }}</td>
                                                                        </tr>

                                                                    </tbody>

                                                                </table>
                                                                <table class="table table-bordered table-sm">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><strong>Dropshipper</strong></td>
                                                                            <td><strong>Receivable</strong></td>
                                                                            <td><strong>Received</strong></td>
                                                                            <td ><strong>Remaining</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Advance</strong></td>
                                                                            <td v-if="details.type == 'Normal'">{{ formatPrice(details.advance_amount) }}</td>
                                                                            <td v-if="details.type == 'Normal'">{{ formatPrice(details.advance_amount) }}</td>
                                                                            <td v-if="details.type == 'Normal'">0</td>

                                                                            <td v-if="details.type == 'Cash' || details.type ==  'Daraz'">{{ formatPrice(details.total_bill) }}</td>
                                                                            <td v-if="details.type == 'Cash' || details.type ==  'Daraz'">{{ formatPrice(details.paid_amount) }}</td>
                                                                            <td v-if="details.type == 'Cash' || details.type ==  'Daraz'">{{ formatPrice(details.total_bill - details.paid_amount) }}</td>
                                                                        </tr>


                                                                        <tr>
                                                                            <td><strong>COD</strong></td>
                                                                            <td>{{ formatPrice(details.selling_price) }}</td>
                                                                            <td>{{ formatPrice(details.paid_amount - details.advance_amount) }}</td>
                                                                            <td>{{ formatPrice((details.selling_price - details.paid_amount) + parseFloat(details.advance_amount)) }}</td>
                                                                        </tr>


                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <td><strong>Total</strong></td>
                                                                            <td class="h5" v-if="details.type == 'Normal'">
                                                                                <!-- Total Receivable: Sum of all receivable values -->
                                                                                {{ formatPrice(parseFloat(details.advance_amount) + parseFloat(details.selling_price)) }}
                                                                            </td>
                                                                            <td class="h5" v-if="details.type == 'Normal'">
                                                                                <!-- Total Received: Sum of all received amounts -->
                                                                                {{ formatPrice(details.paid_amount) }}
                                                                            </td>
                                                                            <td class="h5" v-if="details.type == 'Normal'">
                                                                                <!-- Total Remaining: Sum of all remaining amounts -->
                                                                                {{ formatPrice( parseFloat(details.selling_price) + parseFloat(details.advance_amount) - parseFloat(details.paid_amount) ) }}
                                                                            </td>


                                                                            <td class="h5" v-if="details.type == 'Cash' || details.type ==  'Daraz'">{{ formatPrice(details.total_bill) }}</td>
                                                                            <td class="h5" v-if="details.type == 'Cash' || details.type ==  'Daraz'">{{ formatPrice(details.paid_amount) }}</td>
                                                                            <td class="h5" v-if="details.type == 'Cash' || details.type ==  'Daraz'">{{ formatPrice(details.total_bill - details.paid_amount) }}</td>
                                                                        </tr>


                                                                    </tfoot>
                                                                </table>
                                                            </div>

                                                            <div class="col-md-12" v-if="details.type == 'Normal'">
                                                                <h6>Overall Stats</h6>
                                                                <table class="table table-bordered table-sm">
                                                                    <tbody>
                                                                        <tr>

                                                                            <td><strong>COD + Advance : </strong></td>
                                                                            <td class="h5">{{ formatPrice( parseFloat(details.selling_price) + parseFloat(details.advance_amount) ) }}</td>
                                                                            <td><strong>Your Mart : </strong></td>
                                                                            <td class="h5">{{ formatPrice( details.total_bill ) }}</td>
                                                                            <td><strong>Dropshipper : </strong></td>
                                                                            <td class="h5">{{ formatPrice(( parseFloat(details.selling_price) + parseFloat(details.advance_amount)) - parseFloat(details.total_bill) ) }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Order Items -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Items</h5>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-bordered" id="products_items_table">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>Product</th>
                                                                <th>Item Price</th>
                                                                <th>Quantity</th>
                                                                <th>Total Price</th>
                                                                <th>Packing Price</th>
                                                                <th>Shipping</th>
                                                                <th>Total Cost</th>
                                                                <th>Sell Price</th>
                                                                <th>Total Payable</th>
                                                                <!-- <th>Net Profit</th> -->
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="item in details.items" :key="item.id">
                                                                <td class="text-truncate" v-if="item.variation">
                                                                    <ul class="list-unstyled order-list m-b-0 m-b-0">
                                                                        <li class="team-member team-member-sm">
                                                                            <a :href="getImageUrl(item.variation.images[0].attachment.attachment)"
                                                                                target="_blank">
                                                                                <img class="rounded-circle"
                                                                                    :src="getImageUrl(item.variation.images[0].attachment.attachment)">
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                                <td v-else>
                                                                    -
                                                                </td>
                                                                <td v-if="item.variation">
                                                                    <b>SKU : </b>{{ item.variation.sku }}<br>
                                                                    <b>Title : </b>{{ item.variation.product.title
                                                                    }}<br>
                                                                    <b>Description : </b>{{
                                                                    item.variation.product.short_description }}<br>
                                                                    <b>Color : </b>{{ item.variation.color
                                                                    ?item.variation.color.name : '-' }}<br>
                                                                    <b>Size : </b>{{ item.variation.size
                                                                    ?item.variation.size.name : '-' }}
                                                                </td>
                                                                <td v-else>
                                                                    Packing Only
                                                                </td>

                                                                <td>{{ parseFloat(item.price) }}</td>
                                                                <td>{{ item.quantity }}</td>
                                                                <td>{{ parseFloat(item.quantity) * (
                                                                    parseFloat(item.price) ) }}</td>
                                                                <td>{{ item.packaging_cost }}</td>
                                                                <td>{{ item.courier_cost }}</td>
                                                                <td>{{ (parseFloat(item.quantity) *
                                                                    parseFloat(item.price)) +
                                                                    (parseFloat(item.packaging_cost) +
                                                                    parseFloat(item.courier_cost)) }}</td>

                                                                <td v-if="details.is_replacement == 1">0</td>
                                                                <td v-else>{{ item.sell_price }}</td>

                                                                <td v-if="details.is_replacement == 1">0</td>
                                                                <td v-else>{{ parseFloat(item.sell_price) - (
                                                                    (parseFloat(item.quantity) * parseFloat(item.price)
                                                                    ) + (parseFloat(item.packaging_cost) +
                                                                    parseFloat(item.courier_cost) ) ) }}</td>
                                                                <!-- <td v-if="details.is_replacement == 1">0</td>
                                                                <td v-else>
                                                                    {{
                                                                      parseFloat(item.sell_price) === (
                                                                        (parseFloat(item.quantity) * parseFloat(item.price))
                                                                        + parseFloat(item.packaging_cost)
                                                                        + parseFloat(item.courier_cost)
                                                                      )
                                                                      ? 0
                                                                      : (
                                                                          parseFloat(item.sell_price)
                                                                          - (
                                                                              (parseFloat(item.quantity) * parseFloat(item.price))
                                                                              + parseFloat(item.packaging_cost)
                                                                              + parseFloat(item.courier_cost)
                                                                              + (
                                                                                (parseFloat(details.advance_amount) / (parseFloat(details.total_bill) - (parseFloat(details.courier_service_price) + parseFloat(details.packaging_price))))
                                                                                * (parseFloat(item.price) * parseFloat(item.quantity))
                                                                              )
                                                                            )
                                                                        ).toFixed(0)
                                                                    }}
                                                                  </td> -->

                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td></td>
                                                                <td><b>Total</b></td>
                                                                <td><!-- Total quantity (if needed) --></td>
                                                                <td><!-- Total quantity (if needed) --></td>
                                                                <td class="h5">{{ totalPrice }}</td>
                                                                <td class="h5">{{ totalPackagingCost }}</td>
                                                                <td class="h5">{{ totalCourierCost }}</td>
                                                                <td class="h5">{{ totalBasePrice }}</td>

                                                                <td v-if="details.is_replacement == 1">0</td>
                                                                <td v-else class="h5">{{ totalSellPrice }}</td>

                                                                <td v-if="details.is_replacement == 1">0</td>
                                                                <td class="h5" v-else>{{ totalPaybale }}</td>

                                                                <!-- <td v-if="details.is_replacement == 1">0</td>
                                                                <td v-else class="h5">{{ totalNetProfit }}</td> -->
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Order Retuns -->
                                            <div class="card" v-if="details.returns">
                                                <div class="card-header">
                                                    <h5>Returned Items</h5>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>Product</th>
                                                                <th>Quantity</th>
                                                                <th>Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="item in details.returns.details" :key="item.id">
                                                                <td class="text-truncate" v-if="item.product.variation">
                                                                    <ul class="list-unstyled order-list m-b-0 m-b-0">
                                                                        <li class="team-member team-member-sm">
                                                                            <a :href="getImageUrl(item.product.variation.images[0].attachment.attachment)"
                                                                                target="_blank">
                                                                                <img class="rounded-circle"
                                                                                    :src="getImageUrl(item.product.variation.images[0].attachment.attachment)">
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                                <td>
                                                                    <b>SKU : </b>{{ item.product.variation.sku }}<br>
                                                                    <b>Title : </b>{{ item.product.title }}<br>
                                                                    <b>Description : </b>{{
                                                                    item.product.short_description }}<br>
                                                                    <b>Color : </b>{{ item.product.variation.color
                                                                    ?item.product.variation.color.name : '-' }}<br>
                                                                    <b>Size : </b>{{ item.product.variation.size
                                                                    ?item.product.variation.size.name : '-' }}
                                                                </td>
                                                                <td>{{ item.quantity }}</td>
                                                                <td>{{ parseFloat(item.quantity) * (
                                                                    parseFloat(item.price) ) }}</td>

                                                            </tr>
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="attachment-mail">
                                            <p>
                                                <span>
                                                    <i class="fa fa-paperclip"></i> {{ (details.daraz_labels ? details.daraz_labels.length : 0) + (details.attachments ? details.attachments.length : 0) }} attachments — </span>
                                                <!-- <a href="#">Download all attachments</a> -->
                                            </p>
                                            <div class="row" v-if="details">
                                                <div class="col-md-2" v-if="details.payment_proof_attachment">
                                                    <a target="_blank" :href="setImage(details.payment_proof_attachment)">
                                                      <img class="img-thumbnail img-responsive" alt="attachment"
                                                           :src="`${web_url}${details.type === 'Cash' ? 'public/storage/uploads/dropshipper/payments/' : 'public/storage/uploads/payments/'}${details.payment_proof_attachment}`">
                                                    </a>
                                                    <a class="name"
                                                       :href="`${web_url}${details.type === 'Cash' ? 'public/storage/uploads/dropshipper/payments/' : 'public/storage/uploads/payments/'}${details.payment_proof_attachment}`"
                                                       target="_blank">
                                                      {{ truncatedAttachmentName(details.payment_proof_attachment) }}
                                                    </a>
                                                  </div>

                                                   <!-- Attachments from comments array -->
                                                   <div class="col-md-2" v-for="item in details.attachments"  :key="item.id" v-if="details.attachments.length > 0">
                                                    <a target="_blank" :href="setCommentImage(item.attachment)">
                                                      <img class="img-thumbnail img-responsive" v-if="isImage(item.attachment)" alt="attachment" :src="setCommentImage(item.attachment)">
                                                      <i
                                                          v-else
                                                          class="img-thumbnail img-responsive fas fa-file p-5"
                                                          style="color: red;"
                                                      ></i>
                                                    </a>
                                                    <a class="name" :href="setCommentImage(item.attachment)" target="_blank"> {{ truncatedAttachmentName(item.attachment) }}
                                                    </a>
                                                  </div>


                                                <!-- Attachments from daraz_labels array -->
                                                <div class="col-md-2" v-for="attachment in details.daraz_labels" v-if="details.daraz_labels.length > 0"
                                                    :key="attachment.id">
                                                    <a target="_blank" :href="setImage(attachment.attachment)">
                                                        <img class="img-thumbnail img-responsive"
                                                            alt="daraz label attachment"
                                                            :src="`${web_url}public/storage/uploads/labels/${attachment.attachment}`">
                                                    </a>
                                                    <a class="name"
                                                        :href="`${web_url}public/storage/uploads/labels/${attachment.attachment}`"
                                                        target="_blank">
                                                        {{ truncatedAttachmentName(attachment.attachment) }}
                                                    </a>
                                                </div>

                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div id="accordion">
                                        <div class="accordion">
                                            <div class="accordion-header" @click="changeLabel()" role="button"
                                                data-toggle="collapse" data-target="#panel-body-1" aria-expanded="true">
                                                <h4>
                                                    <i
                                                        :class="activityStatus ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                                                    {{ activityStatus ? 'Hide' : 'Show' }} Activity Log
                                                </h4>
                                            </div>
                                            <div class="accordion-body collapse" id="panel-body-1"
                                                data-parent="#accordion" style="">
                                                <ul>
                                                    <li class="mb-0" v-for="item in details.activity" :key="item.id">
                                                        {{ item.activity }} by {{ item.user ? item.user.name : '' }} -
                                                        <small class="text-muted">{{
                                                            formatDate(item.created_at) }}</small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Comment Sections -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="chat">
                                    <div class="chat-header clearfix row">
                                        <div class="col-md-12 row">
                                            <div class="col-md-6">
                                                <div class="chat-with">Activity</div>
                                                <div class="chat-num-messages">{{ details.comments ?
                                                    details.comments.length : 0 }} Total Comments</div>
                                            </div>

                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <form class="card-header-form">
                                                <input type="text" name="search" v-model="searchQuery"
                                                    class="form-control" placeholder="Search">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="chat-box" id="mychatbox">
                                    <div class="card-body chat-content">
                                        <div v-for="comment in filteredComments" v-bind:key="comment.id"
                                            class="support-ticket media pb-1 mb-3">
                                            <img :src="setProfile()" class="user-img mr-2" alt="">
                                            <div class="media-body ml-3">
                                                <span class="font-weight-bold">{{ comment.comment }}</span><br />
                                                <a :href="setCommentImage(comment.attachment)" target="_blank"
                                                    v-if="comment.attachment">Click to see Attachment</a>
                                                <p class="my-1"></p>
                                                <small class="text-muted">Comment by <span
                                                        class="font-weight-bold font-13">{{ comment.user.name }} ( {{
                                                            comment.user.role }} )</span>
                                                    &nbsp;&nbsp; -{{ formatDate(comment.created_at) }}</small>
                                            </div>
                                            <button class="btn btn-danger btn-sm" v-if="role == 'admin'"
                                                @click="deleteComment(comment.id)"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-footer chat-form">
                                        <form id="chat-form" @submit.prevent="addComment()">
                                            <input type="text" class="form-control" v-model="comment"
                                                placeholder="Type a comment" />
                                            <input type="file" class="form-control" @change="addAttachment"
                                                placeholder="Type a comment" />
                                            <button class="btn btn-primary" v-if="!loader">
                                                <i class="far fa-paper-plane"></i>
                                            </button>
                                            <button class="btn btn-primary btn-progress disabled" v-else>
                                                <i class="far fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="card" v-if="role == 'inventory manager'">
                                <div class="card-body row">
                                    <div class="col-md-12">
                                        <h5>Please scan products</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>QR Code</th>
                                                    <!-- <th>Images</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in details.items" :key="item.id">

                                                    <td>
                                                        <b>SKU : </b>{{ item.variation.sku }}<br>
                                                        <b>Title : </b>{{ item.variation.product.title }}<br>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            v-model="item.addedQuantity"
                                                            @keypress.enter="validateQR(item, index)">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            v-model="item.scannedQRNumber"
                                                            @keypress.enter="validateQR(item, index)">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Please press enter or scan with barcode reader</td>
                                                </tr>
                                            </tbody>


                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card" v-if="role == 'packing & dispatch manager'">
                                <div class="card-body">
                                    <h5>Scan the Order Parcel</h5>
                                    <input type="text" class="form-control" v-model="scannedTrackingNumber">
                                </div>
                            </div>

                            <div class="card"
                                v-if="(role == 'order collection manager' || role == 'admin') && details.status < 7">
                                <div class="card-body row">
                                    <div class="col-md-12">
                                        <h5>Confirm Paid Amount</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" @keypress="onlyNumber"
                                            v-model="paidAmount">
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" v-if="!paidAmountLoader"
                                            @click="updatePaidAmount()">Update Amount</button>
                                        <button class="btn btn-primary btn-progress disabled" v-else>Update
                                            Amount</button>
                                    </div>
                                </div>
                            </div>

                            <div class="card" v-if="(role == 'admin') && details.status == 0">
                                <div class="card-body row">
                                    <div class="col-md-12">
                                        <h5>Add Discount Amount</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" @keypress="onlyNumber"
                                            v-model="discount">
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" v-if="!paidAmountLoader"
                                            @click="addDiscount()">Add Discount</button>
                                        <button class="btn btn-primary btn-progress disabled" v-else>Update
                                            Amount</button>
                                    </div>
                                </div>
                            </div>

                            <div class="card"
                                v-if="(role == 'order collection manager' || role == 'admin') && details.status < 7 && details.type == 'Daraz'">
                                <div class="card-body row">
                                    <div class="col-md-12">
                                        <h5>Confirm Packaging Amount</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" @keypress="onlyNumber"
                                            v-model="packagingAmount">
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" v-if="!paidAmountLoader"
                                            @click="updatePackagingAmount()">Update Amount</button>
                                        <button class="btn btn-primary btn-progress disabled" v-else>Update
                                            Amount</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="modal-footer d-dlex justify-content-between" v-if="view != 'viewOnly' && details.type != 'Cash' && details.status < 8">
                        <div>
                            <button class="btn btn-info" data-toggle="modal" data-target="#markasReplacement"
                                @click="markasReplacement()"
                                v-if="(role == 'order collection' || role == 'admin') && details.is_replacement == 0">
                                <i class="fas fa-arrow-right"></i> Mark as Replacement
                            </button>

                            <button class="btn btn-danger" @click="revertBack()"
                                v-if="details.status > 0 && !revertLoader && role != 'supervisor'">
                                <i class="fas fa-undo-alt"></i> Revert to Pre Step
                            </button>
                            <button class="btn btn-danger btn-progress disabled"
                                v-else-if="revertLoader && role != 'supervisor'">
                                <i class="fas fa-undo-alt"></i> Revert to Pre Step
                            </button>

                            <button class="btn btn-danger" @click="reject()" v-if="!rejectLoader && role != 'supervisor'">
                                <i class="fa fa-trash"></i> Cancel Order
                            </button>
                        </div>

                        <div>
                            <button class="btn btn-primary" @click="forward()" v-if="!loader && role != 'supervisor'">
                                <i class="fas fa-paper-plane"></i> Forward Order
                            </button>
                            <button class="btn btn-primary btn-progress disabled" v-else-if="loader">
                                Forward
                            </button>

                            <button class="btn btn-danger btn-progress disabled" v-else-if="rejectLoader">
                                Forward
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Close
                            </button>
                        </div>

                    </div>
                    <div class="modal-footer" v-else-if="view != 'viewOnly' && details.type == 'Cash'">

                        <button class="btn btn-primary" @click="forward()" v-if="!loader && role != 'supervisor'">
                            <i class="fas fa-paper-plane"></i> Forward Order
                        </button>
                        <button class="btn btn-primary btn-progress disabled" v-else-if="loader">
                            Forward
                        </button>
                        <button class="btn btn-danger" @click="reject()" v-if="!rejectLoader && role != 'supervisor'">
                            <i class="fa fa-trash"></i> Cancel Order
                        </button>
                        <button class="btn btn-danger btn-progress disabled" v-else-if="rejectLoader">
                            Forward
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                    <div class="modal-footer" v-else>
                        <button class="btn btn-danger" @click="markAsBeingReturn()" data-toggle="modal" data-target="#markasBeingReturn" v-if="role != 'supervisor' && details.status == 11">
                            <i class="fas fa-undo-alt"></i> Mark as Being Return
                        </button>
                        <button class="btn btn-success" @click="markAsDelivered()" data-toggle="modal" data-target="#markasDelivered" v-if="role != 'supervisor' && details.status == 11">
                            <i class="fas fa-check"></i> Mark as Delivered
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>

export default {
    name: "OrderDetailView",
    props: ["details", "loader", "id", 'role', 'statuses', 'users', 'rejectLoader', 'paidAmountLoader', 'revertLoader', 'view'],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            comment: '',
            attachment: '',
            searchQuery: '',
            activityStatus: false,
            statusMessages: {
                Pending: "added a ticket",
                "In Progress": "started working on the ticket",
                "Sent for Approval": "sent the ticket for approval",
                Approved: "approved the ticket",
                Rejected: "rejected the ticket",
                Completed: "resolved the ticket",
            },
            showDropdown: false,
            filteredUsers: [],
            tagSearchQuery: '',
            cursorPosition: 0,
            highlightedIndex: -1,
            taggedUsers: [],
            web_url: process.env.MIX_WEB_URL,
            scannedTrackingNumber: '', // Store the scanned QR code for tracking number
            paidAmount: '',
            packagingAmount: '',
            discount: ''
        }
    },
    mounted() {
        this.initializeTooltips();
        this.$parent.$on("commentAdded", (value) => {
            if (value) {
                this.close();
            }
        });
    },
    computed: {
        filteredComments() {
            if (!this.searchQuery) {
                return this.details.comments;
            }
            const query = this.searchQuery.toLowerCase();
            return this.details.comments.filter(comment => {
                const roles = Array.isArray(comment.user.role) ? comment.user.role : [comment.user.role];
                return (
                    comment.comment.toLowerCase().includes(query) ||
                    comment.user.name.toLowerCase().includes(query) ||
                    roles.some(role => role.toLowerCase().includes(query))
                );
            });
        },
        totalPrice() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + parseFloat(item.price) * item.quantity;
            }, 0).toFixed(0) : 0;
        },
        totalBasePrice() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + this.calculateItemProfit(item);
            }, 0).toFixed(0) : 0;
        },
        totalPackagingCost() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + parseFloat(item.packaging_cost);
            }, 0).toFixed(0) : 0;
        },
        totalCourierCost() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + parseFloat(item.courier_cost);
            }, 0).toFixed(0) : 0;
        },
        totalSellPrice() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + parseFloat(item.sell_price);
            }, 0).toFixed(0) : 0;
        },
        totalPaybale() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + (parseFloat(item.sell_price) - ((parseFloat(item.price) * item.quantity) + parseFloat(item.courier_cost) + parseFloat(item.packaging_cost)));
            }, 0).toFixed(0) : 0;
        },
        totalNetProfit() {
            return this.details && this.details.items
                ? this.details.items.reduce((total, item) => {
                    const advance = this.details.advance_amount;
                    const subTotal = parseFloat(this.details.total_bill) -
                        (parseFloat(this.details.courier_service_price) + parseFloat(this.details.packaging_price));
                    const itemTotal = parseFloat(item.price) * parseFloat(item.quantity);
                    const advanceAmount = (advance / subTotal) * itemTotal;

                    const totalCost = itemTotal + parseFloat(item.packaging_cost) + parseFloat(item.courier_cost);
                    const sellPrice = parseFloat(item.sell_price);

                    // If total cost equals sell price, return 0 for that item, otherwise return the calculated difference
                    const netProfit = sellPrice === totalCost ? 0 : sellPrice - (totalCost + advanceAmount);

                    return total + netProfit;
                }, 0).toFixed(0)
                : 0;
        }

    },
    methods: {
        deleteComment(comment) {
            this.$emit('deleteComment', { id: this.details.id, comment });
        },
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        calculateItemProfit(item) {
            return ((parseFloat(item.packaging_cost) + parseFloat(item.courier_cost))) + (parseFloat(item.price) * parseFloat(item.quantity));
        },
        fetchDropshipperDetails(id) {
            this.$emit('fetchDropshipperDetails', { id })
        },
        updatePaidAmount() {
            this.$emit('updatePaidAmount', { id: this.details.id, amount: this.paidAmount });
        },
        addDiscount() {
            if (this.discount == '' || this.discount == '0') {
                return swal({
                    title: "Error",
                    text: `Please add discount amount first`,
                    icon: "error",
                    timer: 3000,
                });
            }
            this.$emit('addDiscount', { id: this.details.id, amount: this.discount });
            this.discount = ''
        },
        updatePackagingAmount() {
            this.$emit('updatePackagingAmount', { id: this.details.id, amount: this.packagingAmount });
        },
        formatPrice: function formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        formatNormalDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY hh:mm A') : 'N/A';
        },
        validateQR(item, index) {
            this.errors = [];

            // Ensure the addedQuantity is valid
            if (item.addedQuantity !== item.quantity) {
                this.$set(this.details.items, index, {
                    ...item,
                    scannedQR: false
                });
                return swal({
                    title: "Error",
                    text: `Entered quantity does not match the expected quantity. Expected: ${item.quantity}, Entered: ${item.addedQuantity}`,
                    icon: "error",
                    timer: 3000,
                });
            }

            // Ensure the QR code is valid
            if (item.scannedQRNumber !== item.variation.barcode.barcode) {
                this.$set(this.details.items, index, {
                    ...item,
                    scannedQR: false
                });
                return swal({
                    title: "Error",
                    text: "QR code does not match the item",
                    icon: "error",
                    timer: 3000,
                });
            }

            // If all validations pass, mark the item as scanned
            this.$set(this.details.items, index, {
                ...item,
                scannedQR: true
            });

            swal({
                title: "Success",
                text: "Item validated successfully!",
                icon: "success",
                timer: 3000,
            });
        },
        getImageUrl(imageId) {
            // Check if the image is null
            if (!imageId) {
                return this.public_url + '/assets/img/blank_image.jpg';
            }
            return this.public_url + '/storage/uploads/inventory/products/media/' + imageId;
        },
        forward() {
            if (this.role == 'inventory manager') {
                // Check if any item hasn't been scanned
                const unscannedItems = this.details.items.filter(item => !item.scannedQR);

                // If any item is unscanned, display an error
                if (unscannedItems.length > 0) {
                    return swal({
                        title: "Error",
                        text: "Some items have not been scanned. Please scan all items before proceeding.",
                        icon: "error",
                        timer: 3000,
                    });
                }
            }

            // Check if the role is 'packing & dispatch manager'
            if (this.role === 'packing & dispatch manager' && this.details.type == 'Normal') {
                // Check if the scanned tracking number matches the details tracking number
                if (this.scannedTrackingNumber !== this.details.tracking_number) {
                    return swal({
                        title: "Error",
                        text: "The scanned QR code does not match the tracking number.",
                        icon: "error",
                        timer: 3000,
                    });
                }
            }

            this.$emit('forward', { id: this.details.id });
        },
        markasReplacement() {
            this.$emit('markasReplacement', { id: this.details.id });
        },
        markAsBeingReturn() {
            this.$emit('markAsBeingReturn', { id: this.details.id });
        },
        markAsDelivered() {
            this.$emit('markAsDelivered', { id: this.details.id });
        },
        revertBack() {
            this.$emit('revert', { id: this.details.id });
        },
        reject() {
            this.$emit('reject', { id: this.details.id });
        },
        formatDate(date) {
            return date ? moment(date).fromNow() : 'N/A';
        },
        initializeTooltips() {
            // Ensure tooltips are initialized for dynamically added elements
            $('[data-toggle="tooltip"]').tooltip();
        },
        changeLabel() {
            this.activityStatus = !this.activityStatus;
        },
        status(status) {
            this.$emit('changeStatus', { status });
        },
        setImage(path) {
            if (path == "") {
                this.image = this.public_url + "/assets/img/blank_image.jpg";
                return this.image;
            }
            this.image =
                this.public_url + "/storage/uploads/order/comments/attachments/" + path;
            return this.image;
        },
        isImage(attachment) {
            // Check if the file is an image
            return /\.(jpeg|jpg|gif|png)$/i.test(attachment);
        },
        setCommentImage(path) {
            if (path == "") {
                this.image = this.public_url + "/assets/img/blank_image.jpg";
                return this.image;
            }
            this.image =
                this.public_url + "/storage/uploads/order/comments/attachments/" + path;
            return this.image;
        },
        setProfile(path) {
            if (!path) {
                this.image = this.public_url + "/assets/img/blank_image.jpg";
                return this.image;
            }
            this.image =
                this.public_url + "/storage/uploads/employee/profile/" + path.profile;
            return this.image;
        },
        addAttachment(event) {
            this.attachment = event.target.files[0];
        },
        addComment() {
            let vm = this;
            const fd = new FormData();
            fd.append('id', vm.details.id);
            fd.append('comment', vm.comment);
            fd.append('attachment', vm.attachment);

            vm.$emit('addComment', fd)
        },
        truncatedAttachmentName(attachment) {
            const maxLength = 20; // Set your desired max length here
            return attachment.substring(0, maxLength) + '...';
        },
        dataTable() {

        },
        clearDataTable() {
            const table = $("#products_items_table").DataTable();
            table.destroy();
        },
        close() {
            let vm = this;
            vm.comment = '';
            vm.attachment = '';
            vm.scannedTrackingNumber = "";
            vm.paidAmount = ""
            $("input[type=file]").val("");
        }
    },
    watch: {
        details(newLedger) {
            this.clearDataTable()
            setTimeout(() => {
                $("#products_items_table").DataTable({
                    paging: false,
                    ordering: false,
                    info: false,
                    dom: "Bfrtip",
                    buttons: [{
                        extend: "excel",
                        title: 'Order Details'
                    },
                    ],
                });
            }, 300);
        },
    },
};
</script>
