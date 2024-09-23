<template>
    <div>
        <div class="modal fade" id="purchaseOrderDetail" tabindex="-1" role="dialog"
            aria-labelledby="purchaseOrderDetail" aria-hidden="true" data-backdrop="false"
            style="background-color: rgba(0, 0, 0, 0.2)">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 90%" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Receive Goods</h5>
                        <button type="button" class="close" data-dismiss="modal"  aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>PO # {{ details ? details.id : "" }}</h5>
                                        </div>
                                        <div>
                                            <h5>
                                                Vendor : {{ details.supplier ? details.supplier.full_name : "" }}
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Quantity</th>
                                                    <th>Already Received Quantity</th>
                                                    <th>Received Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody v-if="details">
                                                <tr v-for="(item, index) in details.details" :key="item.id">
                                                    <td class="h5">{{ item.product ? item.product.title : '-' }}</td>
                                                    <td class="h5">{{ item.quantity }}</td>
                                                    <td class="h5">{{ item.gate_received_quantity }}</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter received quantity"
                                                                @keypress="onlyNumber"
                                                            @keyup="receivedQuantity($event, index, item.id)"
                                                             />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" v-if="!loader"
                            @click="generatePass()">
                            Generate Gate Pass
                        </button>
                    <a href="#" class="btn disabled btn-primary btn-progress"
                        v-else>Progress</a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'GateProductInwardPopup',
    props : ['details', 'loader'],
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Pending Inwards",
            },
            received: [],
            quantity: 0,
        };
    },
    methods: {
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        generatePass() {
            let vm = this;

            const fd = new FormData();
            fd.append("details", JSON.stringify(vm.received));
            fd.append("po", vm.details.id);
            vm.$emit('generatePass', fd);
        },
        receivedQuantity(event, index, id) {
            const value = event.target.value;
            this.received[index] = { qty: value, id };
        },
        close(){

        }
    }
}
</script>
