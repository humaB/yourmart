<template>
    <!-- Modal -->
    <div class="modal fade" id="addPOAttachment" tabindex="-1" role="dialog"
        aria-labelledby="addPOAttachmentTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Attachments</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-4">
                        <label for="">File Name</label>
                        <input type="text" class="form-control" v-model="addData.file">
                    </div>
                    <div class="col-md-4">
                        <label for="">Attachment</label>
                        <input type="file" class="form-control" @change="setAttachment($event)">
                    </div>
                    <div class="col-md-4">
                        <label for="">Action</label><br>
                        <button type="button" class="btn btn-primary" :class="{ 'disabled btn-progress': btnLoading }" @click="uploadAttachment()">Upload Attachment</button>

                    </div>

                    <div class="col-md-12 mt-5">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr #</th>
                                    <th>File Name</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(attachment, index) in attachments" :key="attachment.id">
                                    <td>{{ attachment.id }}</td>
                                    <td>{{ attachment.file }}</td>
                                    <td>
                                        <a :href="previewImage(attachment.attachment)" target="_blank"
                                        >{{ attachment.attachment }}</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AddProjectAttachment',
    props : ['btnLoading', 'addData', "attachments"],
    data(){
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
        }
    },
    methods : {
        setAttachment( event ){
            this.addData.attachment = event.target.files[0];
        },
        uploadAttachment(){
            this.$emit('uploadAttachment')
        },
        previewImage(path) {
            return this.public_url+"storage/uploads/purchase_orders/attachments/" + path;
        },
    }
}
</script>
