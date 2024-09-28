<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="ticket" tabindex="-1" role="dialog" aria-labelledby="ticket" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 90%;" role="document">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <div>
                            Order # <b>{{ details.shop ? details.shop.store_name.substring(0, 3)+'-' : '' }}{{ details.order_no }}</b>
                        </div> <!-- Empty div to push the content to the right -->
                        <div class="d-flex">
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
                                        <div class="mail-heading">
                                            <h4 class="vew-mail-header">
                                                <h3>Order Details # {{ details.shop ? details.shop.store_name.substring(0, 3)+'-' : '' }}{{ details.order_no }}</h3>
                                            </h4>
                                        </div>
                                        <hr />
                                        <div class="view-mail p-t-20">


                                            <!-- Customer Information -->
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h5>Customer Information</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p><strong>Name:</strong> {{ details.customer_name }}</p>
                                                    <p><strong>Address:</strong> {{ details.address }}</p>
                                                    <p><strong>Phone Number 1:</strong> {{ details.phone_number }}</p>
                                                    <p><strong>Phone Number 2:</strong> {{ details.phone_number2 }}</p>
                                                    <p><strong>City:</strong> {{ details.city ? details.city.name : ''
                                                        }}</p>
                                                </div>
                                            </div>

                                            <!-- Order Information -->
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h5>Order Information</h5>
                                                </div>
                                                <div class="card-body">
                                                    <h5>Order # {{ details.shop ? details.shop.store_name.substring(0, 3)+'-' : '' }}{{ details.order_no }}</h5>
                                                    <p class="mt-2"><strong>Courier Service:</strong> {{ details.courier ? details.courier.courier_name : 'N/A' }}</p>
                                                    <p><strong>Selected Package :</strong> {{ details.range ? details.range.category.name : 'N/A' }}</p>
                                                    <p><strong>Courier Instructions:</strong> {{ details.instructions }}</p>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Shop:</strong> {{ details.shop ? details.shop.store_name: 'N/A' }}</p>
                                                            <p><strong>Order Notes:</strong> {{ details.order_note }}</p>
                                                        </div>
                                                        <div class="col-md-6 text-right">
                                                            <p><strong>Sub Total:</strong> {{ parseFloat(details.total_bill) - ( parseFloat(details.courier_service_price)  + parseFloat(details.packaging_price) ) }}</p>
                                                            <p><strong>Courier Charges :</strong> {{ details.courier_service_price }}</p>
                                                            <p><strong>Packing Charges :</strong> {{ details.packaging_price }}</p>
                                                        </div>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-md-12 text-right row">
                                                            <div class="col-md-6">
                                                               <h5> <strong>Total Amount:</strong></h5>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h5>{{ formatPrice(details.total_bill) }}</h5>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h5> <strong>Paid Amount:</strong></h5>
                                                             </div>
                                                             <div class="col-md-6">
                                                                 <h5>{{ formatPrice(details.paid_amount) }}</h5>
                                                             </div>
                                                             <div class="col-md-6">
                                                                <h5> <strong>Sell Price:</strong></h5>
                                                             </div>
                                                             <div class="col-md-6">
                                                                 <h5>{{  formatPrice(details.selling_price) }}</h5>
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
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Quantity</th>
                                                                <th>Price</th>
                                                                <th>Packing Price</th>
                                                                <th>Shipping</th>
                                                                <th>Total Cost</th>
                                                                <th>Sell Price</th>
                                                                <th>Net Profit</th>
                                                                <!-- <th>Images</th> -->
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="item in details.items" :key="item.id">
                                                                <td>
                                                                    <b>SKU : </b>{{ item.variation.sku  }}<br>
                                                                    <b>Title : </b>{{ item.variation.product.title }}<br>
                                                                    <b>Description : </b>{{ item.variation.product.short_description }}<br>
                                                                    <b>Color : </b>{{ item.variation.color ?item.variation.color.name : '-'  }}<br>
                                                                    <b>Size : </b>{{ item.variation.size ?item.variation.size.name : '-'  }}
                                                                </td>
                                                                <td>{{ item.quantity }}</td>
                                                                <td>{{ parseFloat(item.price) - (parseFloat(item.packaging_cost) + parseFloat(item.courier_cost) ) }}</td>
                                                                <td>{{ item.packaging_cost }}</td>
                                                                <td>{{ item.courier_cost }}</td>
                                                                <td>{{ item.price }}</td>
                                                                <td>{{ item.sell_price }}</td>
                                                                <td>{{ parseFloat(item.sell_price) - parseFloat(item.price) }}</td>
                                                                <!-- <td class="text-truncate">
                                                                    <ul class="list-unstyled order-list m-b-0 m-b-0">
                                                                        <li class="team-member team-member-sm"
                                                                            v-for="image in item.variation.images"
                                                                            :key="image.id">
                                                                            <a :href="getImageUrl(image.attachment.attachment)"
                                                                                target="_blank"
                                                                                rel="noopener noreferrer">
                                                                                <img class="rounded-circle"
                                                                                    :src="getImageUrl(image.attachment.attachment)"
                                                                                    alt="user" data-toggle="tooltip"
                                                                                    title="" data-original-title="">
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </td> -->
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td><b>Total</b></td>
                                                                <td><!-- Total quantity (if needed) --></td>
                                                                <td><!-- Total net cost (calculated below) --></td>
                                                                <td class="h5">{{ totalPackagingCost }}</td>
                                                                <td class="h5">{{ totalCourierCost }}</td>
                                                                <td class="h5">{{ totalPrice }}</td>
                                                                <td class="h5">{{ totalSellPrice }}</td>
                                                                <td class="h5">{{ totalNetProfit }}</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="attachment-mail">
                                            <p>
                                                <span>
                                                    <i class="fa fa-paperclip"></i> {{ details.attachments ?
                                                    details.attachments.length : 0 }} attachments — </span>
                                                <a href="#">Download all attachments</a>
                                            </p>
                                            <div class="row"
                                                v-if="details.attachments && details.attachments.length > 0">
                                                <div class="col-md-2" v-for="item in details.attachments"
                                                    :key="item.id">
                                                    <a target="_blank" :href="setImage(item.attachment)">
                                                        <img class="img-thumbnail img-responsive"
                                                            v-if="isImage(item.attachment)" alt="attachment"
                                                            :src="setImage(item.attachment)">
                                                        <i v-else class="img-thumbnail img-responsive fas fa-file p-5"
                                                            style="color: red;"></i>
                                                    </a>
                                                    <a class="name" :href="setImage(item.attachment)" target="_blank">
                                                        {{ truncatedAttachmentName(item.attachment) }}
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
                                                        {{ item.activity }} by {{ item.user ? item.user.name : '' }}  - <small class="text-muted">{{
                                                            formatDate( item.created_at ) }}</small>
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
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary" @click="forward()" v-if="!loader">
                            Forward Order
                        </button>
                        <button class="btn btn-primary btn-progress disabled"  v-else>
                            Forward
                        </button>
                        <button class="btn btn-danger" @click="reject()" v-if="!rejectLoader">
                           Reject Order
                        </button>
                        <button class="btn btn-danger btn-progress disabled"  v-else>
                            Forward
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
    props: ["details", "loader", "id", 'role', 'statuses', 'users', 'rejectLoader'],
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
            taggedUsers: []
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
                return total + parseFloat(item.price);
            }, 0).toFixed(2) : 0;
        },
        totalPackagingCost() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + parseFloat(item.packaging_cost);
            }, 0).toFixed(2) : 0;
        },
        totalCourierCost() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + parseFloat(item.courier_cost);
            }, 0).toFixed(2) : 0;
        },
        totalSellPrice() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + parseFloat(item.sell_price);
            }, 0).toFixed(2) : 0;
        },
        totalNetProfit() {
            return this.details && this.details.items ? this.details.items.reduce((total, item) => {
                return total + (parseFloat(item.sell_price) - parseFloat(item.price));
            }, 0).toFixed(2) : 0;
        }
    },
    methods: {
        formatPrice: function formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        getImageUrl(imageId) {
            // Check if the image is null
            if (!imageId) {
                return this.public_url + '/assets/img/blank_image.jpg';
            }
            return this.public_url + '/storage/uploads/inventory/products/media/' + imageId;
        },
        forward(){
            this.$emit('forward', { id : this.details.id });
        },
        reject(){
            this.$emit('reject', { id : this.details.id });
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
                this.public_url + "/storage/uploads/support_tickets/" + path;
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
                this.public_url + "/storage/uploads/support_tickets/" + path;
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
            if (attachment.length > maxLength) {
                return attachment.substring(0, maxLength) + '...';
            }
            return attachment;
        },
        close() {
            let vm = this;
            vm.comment = '';
            vm.attachment = '';
            vm.filteredUsers = [],
                vm.tagSearchQuery = '',
                vm.cursorPosition = 0,
                vm.highlightedIndex = -1,
                vm.taggedUsers = []
            $("input[type=file]").val("");
        }
    },
};
</script>
