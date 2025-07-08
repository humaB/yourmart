<template>
    <!-- Modal -->
    <div class="modal fade" id="deleteNotification" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12">
                        <h5>Are you sure you want to delete this?</h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" @click="deleteConfirmation()" v-if="!btnLoading">Yes, Delete</button>
                    <button type="button" class="btn btn-danger btn-progress disabled" v-else>Yes, Delete</button>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "DeletePublicNotication",
    props : ['btnLoading'],
    methods : {
        deleteConfirmation(){
            this.$emit('deleteConfirmation', true);
        }
    }
}
</script>
