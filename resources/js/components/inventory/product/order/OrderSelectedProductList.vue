<template>
    <!-- Modal -->
    <div class="modal fade" id="selectedOrderList" tabindex="-1" role="dialog" aria-labelledby="selectedOrderList" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Selected Order Products List</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body row">
                <div class="col-md-12">
                    <div class="card" v-if="products.length > 0">
                        <div class="card-header">
                            <h5>Required Items</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered" id="required_product_table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in products" :key="item.id">
                                        <td class="text-truncate">
                                            <ul class="list-unstyled order-list m-b-0 m-b-0">
                                                <li class="team-member team-member-sm">
                                                    <a :href="getImageUrl(item.image)"
                                                        target="_blank">
                                                        <img class="rounded-circle"
                                                            :src="getImageUrl(item.image)">
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <b>SKU : </b>{{ item.sku }}<br>
                                            <b>Title : </b>{{ item.product }}<br>
                                        </td>
                                        <td>{{ item.quantity }}</td>

                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
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
        name : 'OrderSelectedProductList',
        props : ['products'],
        data(){
            return {
                public_url: window.location.origin + process.env.MIX_FOLDER_PATH,
            }
        },
        methods : {
            getImageUrl(imageId) {
                // Check if the image is null
                if (!imageId) {
                    return this.public_url + '/assets/img/blank_image.jpg';
                }
                return this.public_url + '/storage/uploads/inventory/products/media/' + imageId;
            },
            clearDataTable() {
                const table = $("#required_product_table").DataTable();
                table.destroy();
            },
        },
        watch: {
            products(newLedger) {
                this.clearDataTable()
                setTimeout(() => {
                    $("#required_product_table").DataTable({
                        paging: false,
                        ordering: false,
                        info: false,
                        dom: "Bfrtip",
                        buttons: [{
                            extend: "print",
                            title: 'Yourmart',
                            messageTop: "Selected Orders Product List",
                        },
                        ],
                    });
                }, 300);
            },
        },
    }
</script>
