<template>
    <!-- Modal -->
    <div class="modal fade" id="invalidBank" tabindex="-1" role="dialog" aria-labelledby="invalidBank" aria-hidden="true">
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
                    <h5>Are you sure sent notification to dropshipper of in valid bank details ?</h5>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" @click="markasInValid()" v-if="!loader">Yes, In-valid Details</button>
                <button type="button" class="btn btn-danger btn-progress disabled" v-else>Yes, In-valid Details</button>

            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
        </div>
    </div>
</template>
<script>
    export default {
        name : 'DropshipperInvalidBankConfirmation',
        props : ['loader'],
        methods : {
            markasInValid(){
                this.$emit('markasInValid', true)
            }
        }
    }
</script>
