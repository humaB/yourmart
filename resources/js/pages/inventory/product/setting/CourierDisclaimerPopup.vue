<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="courierDisclaimer" tabindex="-1" role="dialog"
            aria-labelledby="courierDisclaimerTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Add Disclaimer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="" id="" v-model="courier.disclaimer_text">
                            <small>This will show in remarks section in printed label</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="updateDisclaimer()">Update Disclaimer</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name : "CourierDisclaimerPopup",
        props : ['courier'],
        methods : {
            updateDisclaimer(){
                const data = {
                    courier : this.courier.id,
                    text : this.courier.disclaimer_text
                }
                this.$emit('updateDisclaimer', data)
            }
        }
    }
</script>
