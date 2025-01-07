<template>
    <!-- Modal -->
    <div class="modal fade" id="markasBeingReturn" tabindex="-1" role="dialog" aria-labelledby="markasReplacementTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Confirmation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body row">
                <div class="col-md-12">
                    <h5>Are you sure you want to mark this as being return ?</h5>
                    <code>This will have no impact on accounting but will stand in receivable's from leopard</code>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" @click="markasBeingReturnConfirmation()" v-if="!loader">Yes, Mark as Being Return</button>
                <button type="button" class="btn btn-danger btn-progress disabled" v-else>Yes, Mark as Being Return</button>

            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
        </div>
    </div>
</template>
<script>
    export default {
        name : 'OrderMarkasBeingReturnConfirmation',
        props : ['loader', 'orderID'],
        methods : {
            markasBeingReturnConfirmation(){
                this.$emit('markasBeingReturnConfirmation', { id : this.orderID })
            }
        }
    }
</script>
