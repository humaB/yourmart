<template>
    <div
        class="modal fade"
        id="editData"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">
                        Add New Public Notification
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
                            <label>Notification Color <span class="text-danger">*</span></label>
                            <select class="form-control" v-model="color">
                                <option selected value="0">Select From Following</option>
                                <option selected value="Message Message--orange">Orange</option>
                                <option selected value="Message Message--green">Green</option>
                                <option selected value="Message">Blue</option>
                                <option selected value="Message Message--red">Red</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="title"/>
                            <small>Like 'New Arrival' , 'Holidays' , 'New Policy'</small>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="message" style="height: 150px!important;"></textarea>
                            <small>Please avoid extra spacing</small>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Upload Image <span class="text-danger">(optional)</span></label>
                            <input type="file" id="imageInput" class="form-control" @change="setAttachment($event)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-primary" v-if="!btnLoading" @click="update()">Update Notification</button>
                    <button type="button" class="btn btn-primary btn-progress disabled" v-else>Update Notification</button>
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
        title : "",
        color : "0",
        message : "",
        image  : ""
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
        setAttachment( event ){
            this.image = event.target.files[0];
        },
        update() {

            if (!this.title || this.color == '0' || !this.message ) {
                return swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Color, Title, Message fields are required',
                });
            }

            const fd = new FormData();
            fd.append('id', this.editData.id);
            fd.append('color', this.color);
            fd.append('title', this.title);
            fd.append('message', this.message);
            fd.append('image', this.image);
            this.$emit('update' , fd);
        },
        close(){
            this.title = "";
            this.message = "";
            this.image = "";
            this.color = "0";
            $('#imageInput').val('');
        }
    },
    watch: {
        editData(data) {
            this.title = this.editData.title;
            this.message = this.editData.message;
            this.color = this.editData.color;
        }
    }
}
</script>


