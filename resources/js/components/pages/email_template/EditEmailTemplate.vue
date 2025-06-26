<template>
    <div class="modal fade" id="editData" tabindex="-1" role="dialog" aria-labelledby="editCourseLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCourseLabel">
                        Edit Email Template
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Type <span class="text-danger">*</span></label>
                            <select class="form-control" v-model="type">
                                <option selected value="0">Select From Following</option>
                                <option selected value="dropshipper_application_received">Dropshipper Application
                                    Received</option>
                                <option selected value="dropshipper_application_approved">Dropshipper Application
                                    Approved</option>
                                <option selected value="dropshipper_application_rejected">Dropshipper Application
                                    Rejected</option>
                                <option selected value="supplier_application_received">Supplier Application Received
                                </option>
                                <option selected value="supplier_application_approved">Supplier Application Approved
                                </option>
                                <option selected value="supplier_application_rejected">Supplier Application Rejected
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="name" />
                        </div>

                        <div class="form-group col-md-12">
                            <label>Body <span class="text-danger">*</span></label>
                            <textarea class="summernote descriptionEdit"></textarea>
                            <!-- <textarea class="form-control" v-model="editData.description"></textarea> -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    Send Test Email
                                </div>
                                <div class="card-body col-md-12 row">
                                    <div class="col-md-8">
                                        <label for="">Receiver Email</label>
                                        <input type="email" name="" id="" class="form-control" v-model="receiverEmail">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="Action">Action</label><br>
                                        <button class="btn btn-primary w-100" v-if="!btnLoading" @click="sendTestMail()">Send email</button>
                                        <button type="button" class="btn btn-primary btn-progress disabled" v-else>Send email</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" v-if="!btnLoading" @click="update()">Edit Information
                        {{ (type).toUpperCase() }}</button>
                    <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New {{
                        (type).toUpperCase() }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: ['btnLoading', 'editData'],
    data() {
        return {
            name: '',
            type: '0',
            description: '',
            receiverEmail : ""
        };
    },
    methods: {
        sendTestMail(){
            if( this.receiverEmail == ''){
                return swal({
                    icon: 'warning',
                    title: 'Email Required',
                    text: 'Please add received email address',
                });
            }
            this.$emit('sendTestMail', { id : this.editData.id , email : this.receiverEmail});
        },
        update() {
            const description = $('.descriptionEdit').summernote('code');
            if (!this.name || this.type == 0 || !description) {
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Title, type, Description field are required',
                });
            }

            const data = {
                id: this.editData.id,
                name: this.name,
                type: this.type,
                description: description
            }

            this.$emit('update', data);
        },
    },
    watch: {
        editData(data) {
            this.name = this.editData.subject;
            this.type = this.editData.type;
            $('.descriptionEdit').summernote('code', this.editData.body);
        }
    }
};
</script>
