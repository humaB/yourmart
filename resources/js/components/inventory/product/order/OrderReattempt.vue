<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="orderReattempt" tabindex="-1" role="dialog"
            aria-labelledby="orderReattemptTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Re-attempt</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <h5 class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fa fa-shopping-bag"></i>
                                        Order # {{ details.shop ? details.shop.store_name.substring(0, 3) + '-' : '' }}{{ details.order_no }}
                                    </span>
                                    <span>
                                        <i class="fa fa-clock-o"></i>
                                        Date/Time : {{ formatNormalDate(details.created_at) }}
                                    </span>
                                </h5>

                                <div v-if="details.type == 'Normal'">
                                    <h6>
                                        <i class="fa fa-barcode"></i>
                                        Tracking # {{ details.tracking_number }}
                                    </h6>

                                    <a :href="details.slip_link" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fa fa-print"></i>
                                        Print Slip
                                    </a>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <p>
                                                <i class="fa fa-truck"></i>
                                                <strong>Courier Service:</strong> {{ details.courier ? details.courier.courier_name : 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>
                                                <i class="fa fa-comment"></i>
                                                <strong>Courier Instructions:</strong> {{ details.instructions }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" v-if="!details.re_attempt">
                            <strong>Enter Advice</strong>
                            <textarea name="" id="" v-model="remarks" class="form-control" placeholder="Enter remarks for re-attempt (instructions for courier company)"></textarea>
                        </div>
                        <div class="col-md-12" v-else>
                            <strong>Added Advice : {{ details?.re_attempt?.advice }}</strong>
                        </div>
                    </div>
                    <div class="modal-footer" v-if="!details.re_attempt">
                        <button type="button" class="btn btn-primary" @click="reAttempt()" v-if="!loader">Ask to Re-attempt</button>
                        <button type="button" class="btn btn-primary btn-progress disabled" v-else>Ask to Re-attempt</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    <div class="modal-footer" v-else>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "OrderReattempt",
    props : ['details', 'loader'],
    data(){
        return {
            remarks : ""
        }
    },
    methods : {
        formatNormalDate(date) {
            return date ? moment(date).format('DD-MMM-YYYY hh:mm A') : 'N/A';
        },
        formatPrice: function formatPrice(price) {
            var string = parseFloat(price).toString();
            return string
                .replace(/,/g, "")
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        },
        reAttempt(){
            let vm = this;
            if( vm.remarks == ""){
                return swal({
                        title: "Required",
                        text: "Please fill reattempt reason, thanks",
                        icon: "warning",
                        timer: 3000,
                    });
            }

            vm.$emit("reAttempt", { id : vm.details.id, remarks : vm.remarks});
        }
    }
}
</script>
