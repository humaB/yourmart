<template>
    <div>
        <!-- Modal -->
        <div class="modal fade" id="productDetailView" tabindex="-1" role="dialog"
            aria-labelledby="productDetailViewTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Product View</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Main Product Details -->
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>Title</th>
                                            <td>{{ product.title }}</td>
                                        </tr>
                                        <tr>
                                            <th>Short Description</th>
                                            <td>{{ product.short_description }}</td>
                                        </tr>
                                        <tr>
                                            <th>Brand</th>
                                            <td>{{ product.brand ? product.brand.name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Category</th>
                                            <td>{{ product.category.name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shipping Method</th>
                                            <td>{{ product.shipping ? product.shipping.name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Hero Image</th>
                                            <td>
                                                <a :href="getImageUrl(product.hero_image)"
                                                            target="_blank" rel="noopener noreferrer">
                                                <img :src="getImageUrl(product.hero_image)" alt="Hero Image"
                                                    class="user-img mr-2" width="100" />
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Video Link</th>
                                            <td>{{ product.video_link ? product.video_link : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Product Description</th>
                                            <td>
                                                <span v-html="product.product_description"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Product Highlight</th>
                                            <td v-html="product.product_highlight || 'N/A'"></td>
                                        </tr>
                                        <tr>
                                            <th>Warranty</th>
                                            <td>{{ product.warranty }}</td>
                                        </tr>
                                        <tr>
                                            <th>Max Quantity</th>
                                            <td>{{ product.max_quantity || 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Quantity Step</th>
                                            <td>{{ product.quantity_step }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Variations -->
                            <div class="col-md-12 mt-4">
                                <h5>Product Variations</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <th>Regular Price</th>
                                            <th>Sale Price</th>
                                            <th>Forecasted Stock</th>
                                            <th>Pending Order</th>
                                            <th>In Stock</th>
                                            <th>Images</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="variation in product.variations" :key="variation.id">
                                            <td>{{ variation.sku }}</td>
                                            <td>{{ variation.color ? variation.color.name : '-' }}</td>
                                            <td>{{ variation.size ? variation.size.name : '-' }}</td>
                                            <td>{{ variation.regular_price }}</td>
                                            <td>{{ variation.sale_price }}</td>
                                            <td>{{ variation.stock }}</td>
                                            <td>{{ variation.stock }}</td>
                                            <td>{{ variation.stock }}</td>

                                            <td class="text-truncate">
                                                <ul class="list-unstyled order-list m-b-0 m-b-0">
                                                    <li class="team-member team-member-sm"
                                                        v-for="image in variation.images" :key="image.id">
                                                        <a :href="getImageUrl(image.attachment.attachment)"
                                                            target="_blank" rel="noopener noreferrer">
                                                            <img class="rounded-circle"
                                                                :src="getImageUrl(image.attachment.attachment)"
                                                                alt="user" data-toggle="tooltip" title=""
                                                                data-original-title="">
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Discounts -->
                            <div class="col-md-12 mt-4" v-if="product.discounts && product.discounts.length > 0">
                                <h5>Discounts Per Quantity</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="discount in product.discounts" :key="discount.id">
                                            <td>{{ discount.quantity }}</td>
                                            <td>{{ discount.price }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>



                            <!-- Sale Schedule -->
                            <div class="col-md-6 mt-4" v-if="product.sale_schedule">
                                <h5>Sale Schedule</h5>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>From</th>
                                            <td>{{ product.sale_schedule.from }}</td>
                                        </tr>
                                        <tr>
                                            <th>To</th>
                                            <td>{{ product.sale_schedule.to }}</td>
                                        </tr>
                                        <tr>
                                            <th>Price</th>
                                            <td>{{ product.sale_schedule.price }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Dimensions -->
                            <div class="col-md-6 mt-4" v-if="product.dimensions">
                                <h5>Dimensions</h5>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>Weight</th>
                                            <td>{{ product.dimensions.weight }}</td>
                                        </tr>
                                        <tr>
                                            <th>Length</th>
                                            <td>{{ product.dimensions.length }}</td>
                                        </tr>
                                        <tr>
                                            <th>Height</th>
                                            <td>{{ product.dimensions.height }}</td>
                                        </tr>
                                        <tr>
                                            <th>Width</th>
                                            <td>{{ product.dimensions.width }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-12 mt-4" v-if="(product.up_sells && product.up_sells.length > 0) || (product.cross_sells && product.cross_sells.length > 0) || (product.bought_togethers && product.bought_togethers.length > 0)">
                                <h5>Related Products</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Upsells</th>
                                            <th>Cross Sells</th>
                                            <th>Bought Together</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div v-if="product.up_sells.length > 0">
                                                    <ul>
                                                        <li v-for="item in product.up_sells" :key="item.id">
                                                            {{ item.product.title }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div v-if="product.cross_sells.length > 0">
                                                    <ul>
                                                        <li v-for="item in product.cross_sells" :key="item.id">
                                                            {{ item.product.title }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div v-if="product.bought_togethers.length > 0">
                                                    <ul>
                                                        <li v-for="item in product.bought_togethers" :key="item.id">
                                                            {{ item.product.title }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ProductDetailView',
    props: ['product'],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
        }
    },
    methods: {
        getImageUrl(imageId) {
            // Check if the image is null
            if (!imageId) {
                return this.public_url + 'assets/img/blank_image.jpg';
            }
            return this.public_url + 'storage/uploads/inventory/products/media/' + imageId;
        }
    }
}
</script>
