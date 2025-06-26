<template>
    <div
        class="modal fade"
        id="newData"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">
                        Add New Email Template
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

                        <div class="form-group col-md-6">
                            <label>Type <span class="text-danger">*</span></label>
                            <select class="form-control" v-model="type">
                                <option selected value="0">Select From Following</option>
                                <option selected value="dropshipper_application_received">Dropshipper Application Received</option>
                                <option selected value="dropshipper_application_approved">Dropshipper Application Approved</option>
                                <option selected value="dropshipper_application_rejected">Dropshipper Application Rejected</option>
                                <option selected value="supplier_application_received">Supplier Application Received</option>
                                <option selected value="supplier_application_approved">Supplier Application Approved</option>
                                <option selected value="supplier_application_rejected">Supplier Application Rejected</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="name"/>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Body <span class="text-danger">*</span></label>
                            <textarea class="summernote"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" v-if="!btnLoading" @click="add()">Add New {{ (type).toUpperCase() }}</button>
                    <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add New {{ (type).toUpperCase() }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
  props: ['btnLoading', 'addData'],
  data() {
    return {
        name : '',
        type : '0',
    };
  },
  mounted() {
        this.$parent.$on("saved", (value) => {
            if (value) {
                this.close();
            }
        });
    },
  methods: {
        add() {
            const description = $('.summernote').summernote('code');
            if (!this.name || this.type == 0 || !description) {
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Subject, Type, Body field are required',
                });
            }

            const data = {
                name : this.name,
                type : this.type,
                description : description
            }
            this.$emit('add' , data);
        },
        close(){
            this.name = "";
            this.type = "0";
            $('.summernote').summernote('code', '');
        }
    },
}
</script>


