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
                                                    <th>Gate Received Quantity</th>
                                                    <th>Already Received Quantity</th>
                                                    <th>Received Quantity</th>
                                                    <th>Scan QR </th>
                                                    <th>Generate QR</th>
                                                </tr>
                                            </thead>
                                            <tbody v-if="details">
                                                <tr v-for="(item, index) in details.details" :key="item.id">
                                                    <td class="h5">{{ item.product ? item.product.title : '-' }}</td>
                                                    <td class="h5">{{ item.quantity }}</td>
                                                    <td class="h5">{{ item.gate_received_quantity }}</td>
                                                    <td class="h5">{{ item.store_received_quantity }}</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter received quantity"
                                                                @keypress="onlyNumber"
                                                                @keyup="receivedQuantity($event, index, item.id)"
                                                             />
                                                             <span v-if="received[index] && received[index].qty > item.gate_received_quantity" style="color: red;">Received quantity cannot be greater than gate received quantity</span>
                                                    </td>
                                                    <td>
                                                        <input  type="text" class="form-control" placeholder="Please scan QR code here" @keyup="addedQr($event, index, item.id)">
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" @click="generateQRCode(index, received ,item.product, details.supplier)">
                                                            Generate QR code
                                                        </button>
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
import QRCode from 'qrcode';
export default {
    name: 'StoreProductInwardPopup',
    props : ['details', 'loader'],
    data() {
        return {
            api_url: window.location.origin + process.env.MIX_API_URL,
            tableHeader: {
                heading: "Pending Inwards",
            },
            received: [],
            quantity: 0,
            qrCodeDataUrl : ''
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
        async generateQRCode(index, received, product, supplier) {
            try {
                // Get the first three characters of the product title and supplier's full name
                const shortTitle = product.title.substring(0, 3);
                const supplierTitle = supplier.full_name.substring(0, 3);

                // Generate the QR code using the combined string
                this.qrCodeDataUrl = await QRCode.toDataURL(`${supplierTitle}-${shortTitle}-${product.id}`);

                      // Check if received array is defined and index is valid
                if (received && Array.isArray(received) && received[index]) {
                    // Store the generated QR code URL in the received array
                    this.received[index].qrCodeDataUrl = `${supplierTitle}-${shortTitle}-${product.id}`
                } else {
                    alert('Please type some value in receiving column for this product')
                    return;
                }
                // Open the QR code in a new tab
                const newWindow = window.open();
                newWindow.document.write(`<img src="${this.qrCodeDataUrl}" alt="QR Code">`);
                newWindow.document.title = "QR Code";

            } catch (error) {
                console.error("Error generating QR code:", error);
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
            const remainingQuantity = this.details.details[index].gate_received_quantity - this.details.details[index].store_received_quantity;
            if (value > remainingQuantity) {
                this.received[index] = { qty: remainingQuantity, id , qrCodeDataUrl };
                console.log(this.received[index]);

                return swal({
                        title: "Error",
                        text: "Received quantity cannot be greater than remaining quantity",
                        icon: "error",
                        timer: 3000,
                    });
            } else {
                this.received[index] = { qty: value, id , qrCodeDataUrl};
            }
        },
        addedQr(event, index, id) {
            const value = event.target.value;
            this.received[index].qrCodeDataUrl = value;
        },
        close(){
            vm.qrCodeDataUrl = ''
        }
    },
    watch: {
    'details.details': {
        handler(newValue) {
            this.received = newValue.map((item) => ({
                qty: 0, // initialize with a default value
                id: item.id,
                qrCodeDataUrl: '', // initialize with a default value
            }));
        },
        immediate: true, // run handler when component is created
        deep: true, // watch nested properties
    },
    }
}
</script>
