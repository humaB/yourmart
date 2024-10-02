<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Open Tickets</h4>
                </div>
                <div class="card mx-3">
                <div class="card-header">
                    <h4>Ticket Status</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>Total Tickets</th>
                                <th>Awaiting Your Reply</th>
                                <th>Awaiting YourMart Reply</th>
                                <th>Closed</th>
                                <th>Expired</th>
                                <th>Reviewed</th>
                                <th>In-Process</th>
                            </tr>
                            <tr>
                                <td>{{ totalTicketSum.total_tickets }}</td>
                                <td class="align-middle">
                                    <div class="progress-text text-right text-secondary">
                                        {{ getPercentage(totalTicketSum.awaiting_your_reply) }}%
                                    </div>
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-success" :style="{ width: getPercentage(totalTicketSum.awaiting_your_reply) + '%' }"></div>
                                    </div>
                                    {{ getPercentage(totalTicketSum.awaiting_your_reply) }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress-text text-right text-secondary">
                                        {{ getPercentage(totalTicketSum.awaiting_yourmart_reply) }}%
                                    </div>
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-primary" :style="{ width: getPercentage(totalTicketSum.awaiting_yourmart_reply) + '%' }"></div>
                                    </div>
                                    {{ totalTicketSum.awaiting_yourmart_reply }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress-text text-right text-secondary">
                                        {{ getPercentage(totalTicketSum.closed) }}%
                                    </div>
                                    <div class="progress"  data-height="6">
                                        <div class="progress-bar bg-danger" :style="{ width: getPercentage(totalTicketSum.closed) + '%' }"></div>
                                    </div>
                                    {{ totalTicketSum.closed }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress-text text-right text-secondary">
                                        {{ getPercentage(totalTicketSum.expired) }}%
                                    </div>
                                    <div class="progress" data-height="6">
                                        <div class="progress-bar bg-success" :style="{ width: getPercentage(totalTicketSum.expired) + '%' }"></div>
                                    </div>
                                    {{ totalTicketSum.expired }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress-text text-right text-secondary">
                                        {{ getPercentage(totalTicketSum.reviewed) }}%
                                    </div>
                                    <div class="progress" data-height="6" >
                                        <div class="progress-bar bg-info" :style="{ width: getPercentage(totalTicketSum.reviewed) + '%' }"></div>
                                    </div>
                                    {{ totalTicketSum.reviewed }}
                                </td>
                                <td class="align-middle">
                                    <div class="progress-text text-right text-secondary">
                                        {{ getPercentage(totalTicketSum.in_process) }}%
                                    </div>
                                    <div class="progress" data-height="6" >
                                        <div class="progress-bar bg-info" :style="{ width: getPercentage(totalTicketSum.in_process) + '%' }"></div>
                                    </div>
                                    {{ totalTicketSum.in_process }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
                <div class="mx-4">
                    <form role="form" @submit.prevent="fetchTicketData">
                        <h6>Filter</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select v-model="ticketFilter.status" class="border form-control" style="height: 50px">
                                        <option selected value="" disabled>Please Select Status</option>
                                        <option value="Awaiting Your Reply">Awaiting Your Reply</option>
                                        <option value="Awaiting YourMart Reply">Awaiting YourMart Reply</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Expired">Expired</option>
                                        <option value="Reviewed">Reviewed</option>
                                        <option value="In-Process">In-Process</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select v-model="ticketFilter.ticket_number_type" class="border form-control" style="height: 50px">
                                        <option value="" selected disabled>Ticket Number</option>
                                        <option value="yourmart_ticket_number">YourMart Ticket Number</option>
                                        <option value="order_number">Your Order Number</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input v-model="ticketFilter.ticket_number" type="text" class="form-control" placeholder="Please Enter Ticket Number" style="height: 50px" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary px-5 p-2 btn-block">Search</button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" @click="clearFilters" class="btn btn-outline-primary p-2 px-3 btn-block">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="save-stage" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Order Number</th>
                                    <th>Ticket Type</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="ticket in tickets" :key="ticket.id">
                                    <td>{{ ticket.id }}</td>
                                    <td>{{ ticket.order_no || 'N/A' }}</td>
                                    <td>{{ ticket.ticket_type }}</td>
                                    <td>{{ ticket.message }}</td>
                                    <td>{{ ticket.status || 'Pending' }}</td>
                                    <td>{{ new Date(ticket.created_at).toLocaleDateString() }}</td>
                                    <td>
                                        <button @click="viewTicketDetails(ticket)" class="btn btn-info">View</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>


        <!-- Modal for Ticket Details -->
        <div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-labelledby="ticketDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl"> <!-- Add 'modal-lg' for a larger modal -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ticket Details</h5>
                        <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <p class="mb-1"><b>Ticket ID:</b></p>
                                    <p class="mb-1">{{ selectedTicket.id }}</p>

                                    <p class="my-1"><b>Order Number:</b></p>
                                    <p class="mb-1">{{ selectedTicket.order_no || 'N/A' }}</p>

                                    <p class="my-1"><b>Ticket Type:</b></p>
                                    <p class="mb-1">{{ selectedTicket.ticket_type }}</p>

                                    <p class="my-1"><b>Message:</b></p>
                                    <p class="mb-1">{{ selectedTicket.message }}</p>

                                    <p class="my-1"><b>Status:</b></p>
                                    <p class="mb-1">{{ selectedTicket.status || 'Pending' }}</p>

                                    <p class="my-1"><b>Date:</b></p>
                                    <p class="mb-1">{{ new Date(selectedTicket.created_at).toLocaleDateString() }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <strong>Image:</strong><br>
                                    <img :src="web_url + 'storage/uploads/tickets/' + selectedTicket.file_path" alt="Ticket Image" class="img-fluid" style="max-width: 100%; height: auto;">
                                </div>
                            </div>
                        </div>

                        <!-- Chat Box -->
                        <div>
                            <strong>Chat:</strong>
                            <div class="border rounded p-2 mb-2" ref="chatContainer" style="height: 200px; overflow-y: auto;">
                                <!-- Display Previous Chat Messages -->
                                <div v-for="(chat, index) in chats" :key="index" class="mb-2">
                                    <div :class="{'text-white bg-primary w-75 rounded float-right px-2': user.id === chat.added_by,
                                                'text-dark bg-secondary w-75 rounded float-left px-2': user.id !== chat.added_by}">
                                        <p class="d-flex justify-content-between mb-0">
                                            <strong>{{ chat.added_by === user.id ? 'You' : chat.added_by_name.name }}:</strong>
                                            <strong :class="chat.added_by === user.id ? 'text-white' : 'text-dark'"><small>{{ chat.status }}</small></strong>
                                            <span>
                                                <a :class="chat.added_by === user.id ? 'text-white' : 'text-dark'" v-if="chat.file_path != null" :href="public_url + 'storage/uploads/tickets/message/'+chat.file_path" target="_blank"><small>Attachment</small></a>
                                            </span>
                                        </p>
                                        <p class="mb-0">{{ chat.chat_message }}</p>
                                        <div class="text-right"><small style="font-size:10px">{{ timeFormat(chat.created_at,'HH:mm | DD-MM-YYYY') }}</small></div>
                                    </div>
                                    <div class="clearfix"></div> <!-- This ensures proper clearing of floats -->
                                </div>
                            </div>

                            <!-- New Fields for Message, Attachment, and Status -->
                            <div class="mb-2 row">
                                <div class="col-12"> <!-- Half width for the file input -->
                                    <textarea v-model="postMessage.chatMessage" placeholder="Type your message here..." rows="3" class="form-control mb-2"></textarea>
                                </div>
                                <div class="col-6"> <!-- Half width for the file input -->
                                    <input type="file" class="form-control" @change="onFileChange" />
                                </div>
                                <div class="col-6"> <!-- Half width for the select dropdown -->
                                    <select v-model="postMessage.selectedStatus" class="form-control">
                                        <option value="Awaiting Your Reply">Awaiting Your Reply</option>
                                        <option value="Awaiting YourMart Reply">Awaiting YourMart Reply</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Expired">Expired</option>
                                        <option value="Reviewed">Reviewed</option>
                                        <option value="In-Process">In-Process</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
                        <button :disabled="btnLoading" type="button" class="btn btn-primary" @click="addMessage">Send</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>

import { BulletListLoader } from "vue-content-loader";
import moment from "moment";

export default {
    name: 'TicketPage',
    components: {
        BulletListLoader,
    },
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            api_url: window.location.origin + process.env.MIX_API_URL,
            web_url: process.env.MIX_WEB_URL,
            tickets: [],
            chats: [],
            showModal: false,
            btnLoading: false,
            selectedTicket: {},
            user: {},
            ticketFilter: {
                status: '',
                ticket_number_type: '',
                ticket_number: ''
            },
            totalTicketSum: {
                total_tickets: 0,
                awaiting_your_reply: 0,
                awaiting_yourmart_reply: 0,
                closed: 0,
                expired: 0,
                reviewed: 0,
                in_process: 0,
            },
            postMessage: {
                chatMessage: '',
                selectedStatus: '',
                selectedFile: null,
                ticketId: null
            },
        };
    },
    created() {
        this.fetchTicketData();
    },
    methods: {
        async addMessage() {
            // Validation for chat message and status
            if (!this.postMessage.chatMessage || this.postMessage.chatMessage.trim() === '') {
                this.btnLoading = false;
                return swal({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Chat message is required.',
                });
            }
            this.btnLoading = true; // Set loading state to true

            const formData = new FormData();

            // Append data to FormData
            formData.append('chatMessage', this.postMessage.chatMessage);
            formData.append('selectedStatus', this.postMessage.selectedStatus);
            formData.append('ticketId', this.selectedTicket.id); // Include the ticket ID
            // Append the selected file if it exists
            if (this.postMessage.selectedFile) {
                formData.append('selectedFile', this.postMessage.selectedFile);
            }

            axios.post(this.api_url + "tickets/messages/add", formData,{
                headers: {
                    'Content-Type': 'multipart/form-data' // Set the content type for file uploads
                }
            })
            .then((response) => {

                this.$emit('saved', true);
                this.getMessages();
                this.chatScrollBottom();
                this.postMessage = {
                    chatMessage: '',
                    selectedStatus: this.postMessage.selectedStatus,
                    selectedFile: null,
                    ticketId: this.selectedTicket.id,
                },
                this.fetchTicketData();
                this.btnLoading = false;
            }).catch((err) => {
                this.btnLoading = false;
            });
        },
        onFileChange(event) {
            const file = event.target.files[0]; // Get the selected file
            this.postMessage.selectedFile = file; // Store the file in postMessage
        },
        fetchTicketData() {
            // Fetch tickets based on filters
            axios
                .post(this.api_url + "tickets", this.ticketFilter)
                .then((response) => {
                this.tickets = response.data.response;
                })
                .catch((error) => {
                console.error("Error fetching tickets:", error);
                });
        },
        clearFilters() {
            // Clear the filters
            this.ticketFilter = {
                status: '',
                ticket_number_type: '',
                ticket_number: ''
            };
            // Optionally, you can refetch the tickets without filters
            this.fetchTicketData();
        },
        getMessages() {
            axios.post(this.api_url + 'tickets/messages/particular',{ticket_id:this.selectedTicket.id}).then((response) => {
                this.chats = response.data.messages;
                this.user = response.data.user;
                this.chatScrollBottom();
            });
        },
        fetchTicketStatusCounts() {
            axios.get(this.api_url + 'tickets/status-counts').then((response) => {
                const data = response.data;
                this.totalTicketSum.total_tickets = data.total_tickets;
                this.totalTicketSum.awaiting_your_reply = data.awaiting_your_reply;
                this.totalTicketSum.awaiting_yourmart_reply = data.awaiting_yourmart_reply;
                this.totalTicketSum.closed = data.closed;
                this.totalTicketSum.expired = data.expired;
                this.totalTicketSum.reviewed = data.reviewed;
                this.totalTicketSum.in_process = data.in_process;
            });
        },
        chatScrollBottom() {
            this.$nextTick(() => {
                const container = this.$refs.chatContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight; // Scroll to the bottom
                }
            });
        },
        viewTicketDetails(ticket) {
            axios.post(this.api_url + 'tickets/particular',{ticket_id:ticket.id}).then((response) => {
                this.selectedTicket = response.data.ticket;
                this.selectedTicket.file_path = response.data.ticket.file_path ? response.data.ticket.file_path : 'empty.png';
                this.postMessage.selectedStatus = ticket.status;
                this.getMessages();
            });
             // Set the selected ticket


            $('#ticketDetailsModal').modal('show'); // Open the modal using jQuery
        },
        closeModal() {
            $('#ticketDetailsModal').modal('hide'); // Close the modal using jQuery
            this.chatMessage = ''; // Clear the chat message when closing
        },
        getPercentage(statusCount) {
            if (this.totalTicketSum.total_tickets === 0) return 0;
            return Math.round((statusCount / this.totalTicketSum.total_tickets) * 100);
        },
        timeFormat(time,format) {
            return moment(time).format(format)
        },
    },
    mounted() {
        this.fetchTicketStatusCounts();
    },
}
</script>
