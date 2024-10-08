<template>
<!-- Modal -->
<div class="modal fade" id="returnProduct" tabindex="-1" role="dialog" aria-labelledby="returnProductTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Confimation</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body row">
            <div class="col-md-12">
                <h5>Please scan tracking number</h5>
                <input type="text" name="" id="" class="form-control" v-model="scannedBarcode">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="addToStock()" v-if="!loader">Add to stock</button>
            <button type="button" class="btn btn-primary btn-progress disabled" v-else>Add to stock</button>
            <button type="button" class="btn btn-secondary" @click="close()" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
    export default {
        name : 'StoreProductReturnPopup',
        props : ['detail', 'loader'],
        data(){
            return {
                scannedBarcode : ''
            }
        },
        mounted() {
            this.$parent.$on("saved", (value) => {
                if (value) {
                    this.close();
                }
            });
        },
        methods : {
            addToStock(){
                if(this.scannedBarcode == ''){
                    return swal({
                        title: "Required",
                        text: 'Please scan tracking number, thanks',
                        icon: "error",
                        timer: 3000,
                    });
                }

                if(this.scannedBarcode != this.detail.tracking_number){
                    return swal({
                        title: "Error",
                        text: 'Tracking Number Doesnt matched',
                        icon: "error",
                        timer: 3000,
                    });
                }

                this.$emit('addToStock', { id : this.detail.id })
            },
            close(){
                this.scannedBarcode = ''
            }
        }
    }
</script>
