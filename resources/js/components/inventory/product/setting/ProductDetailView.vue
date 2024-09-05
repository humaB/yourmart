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
                                        <!-- Title -->
                                        <tr>
                                            <th>Title</th>
                                            <td>
                                                <div v-if="!editingField.title">{{ product.title }}</div>
                                                <div v-else>
                                                    <input type="text" class="form-control" placeholder="Add Product Title"
                                                    v-model="editedProduct.title">
                                                    <code>Length ( {{ titleLength }} / 150 )</code>
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.title" @click="editField('title')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('title')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.title" @click="cancelEdit('title')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Short Description -->
                                        <tr>
                                            <th>Short Description</th>
                                            <td>
                                                <div v-if="!editingField.short_description">{{ product.short_description }}</div>
                                                <div v-else>
                                                    <input type="text" class="form-control"
                                                    placeholder="Please enter short description of product"
                                                    v-model="editedProduct.short_description">
                                                <code>Length ( {{ shortDescriptionLength }} / 150 )</code>
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.short_description" @click="editField('short_description')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('short_description')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.short_description" @click="cancelEdit('short_description')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Brand -->
                                        <tr>
                                            <th>Brand</th>
                                            <td>
                                                <div v-if="!editingField.brand_id">{{ product.brand ? product.brand.name : 'N/A' }}</div>
                                                <div v-else>
                                                    <v-select :options="brands" v-model="brand">
                                                    </v-select>
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.brand_id" @click="editField('brand_id')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('brand_id')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.brand_id" @click="cancelEdit('brand_id')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Category -->
                                        <tr>
                                            <th>Category</th>
                                            <td>
                                                <div v-if="!editingField.category_id">{{ product.category.name }}</div>
                                                <div v-else>
                                                    <v-select :options="categories" v-model="category">
                                                    </v-select>
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.category_id" @click="editField('category_id')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('category_id')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.category_id" @click="cancelEdit('category_id')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Shipping Method -->
                                        <tr>
                                            <th>Shipping Method</th>
                                            <td>
                                                <div v-if="!editingField.shipping_method_id">{{ product.shipping ? product.shipping.name : 'N/A' }}</div>
                                                <div v-else>
                                                    <v-select :options="shippingOptions"
                                                    v-model="selectedShipping">
                                                    </v-select>
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.shipping_method_id" @click="editField('shipping_method_id')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('shipping_method_id')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.shipping_method_id" @click="cancelEdit('shipping_method_id')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Hero Image -->
                                        <tr>
                                            <th>Hero Image</th>
                                            <td>
                                                <a v-if="!editingField.hero_image" :href="getImageUrl(product.hero_image)" target="_blank" rel="noopener noreferrer">
                                                    <img :src="getImageUrl(product.hero_image)" alt="Hero Image" class="user-img mr-2" width="100" />
                                                </a>
                                                <div v-else>
                                                    <input  type="file" class="form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.hero_image" @click="editField('hero_image')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('hero_image')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.hero_image" @click="cancelEdit('hero_image')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Video Link -->
                                        <tr>
                                            <th>Video Link</th>
                                            <td>
                                                <div v-if="!editingField.video_link">{{ product.video_link ? product.video_link : 'N/A' }}</div>
                                                <div v-else>
                                                    <input v-model="editedProduct.video_link" type="text" class="form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.video_link" @click="editField('video_link')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('video_link')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.video_link" @click="cancelEdit('video_link')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Product Description -->
                                        <tr>
                                            <th>Product Description</th>
                                            <td>
                                                <div v-if="!editingField.product_description" v-html="product.product_description || 'N/A'"></div>
                                                <div v-else>
                                                    <textarea class="summernote productDescriptionEdit"></textarea>
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.product_description" @click="editField('product_description')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('product_description')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.product_description" @click="cancelEdit('product_description')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Product Highlight -->
                                        <tr>
                                            <th>Product Highlight</th>
                                            <td>
                                                <div v-if="!editingField.product_highlight" v-html="product.product_highlight || 'N/A'"></div>
                                                <div v-else>
                                                    <textarea class="summernote productHighlightEdit"></textarea>
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.product_highlight" @click="editField('product_highlight')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('product_highlight')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.product_highlight" @click="cancelEdit('product_highlight')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Warranty -->
                                        <tr>
                                            <th>Warranty</th>
                                            <td>
                                                <div v-if="!editingField.warranty">{{ product.warranty }}</div>
                                                <div v-else>
                                                    <input v-model="editedProduct.warranty" type="text" class="form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.warranty" @click="editField('warranty')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('warranty')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.warranty" @click="cancelEdit('warranty')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Max Quantity -->
                                        <tr>
                                            <th>Max Quantity</th>
                                            <td>
                                                <div v-if="!editingField.max_quantity">{{ product.max_quantity || 'N/A' }}</div>
                                                <div v-else>
                                                    <input v-model="editedProduct.max_quantity" type="text" @keypress="onlyNumber" class="form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.max_quantity" @click="editField('max_quantity')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('max_quantity')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.max_quantity" @click="cancelEdit('max_quantity')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
                                        </tr>

                                        <!-- Quantity Step -->
                                        <tr>
                                            <th>Quantity Step</th>
                                            <td>
                                                <div v-if="!editingField.quantity_step">{{ product.quantity_step }}</div>
                                                <div v-else>
                                                    <input v-model="editedProduct.quantity_step" type="text" @keypress="onlyNumber" class="form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <button v-if="!editingField.quantity_step" @click="editField('quantity_step')" class="btn btn-sm btn-primary">Edit</button>
                                                <button v-else @click="saveField('quantity_step')" class="btn btn-sm btn-success">Save</button>
                                                <button v-if="editingField.quantity_step" @click="cancelEdit('quantity_step')" class="btn btn-sm btn-danger">Cancel</button>
                                            </td>
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
                                            <th>Action</th>
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
                                            <td>
                                                <button class="btn btn-primary" @click="editProductVariant(variation)" data-toggle="modal" data-target="#editProductVariant"><i class="fa fa-edit"></i></button>
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
    props: ["brands", "categories", "tags", "attributes", "shippingOptions", "loader", "product", "productNotUpdated"],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            selectedShipping: { code: 0, label: 'Select from the following' },
            brand: { code: 0, label: "Select from the following" },
            category: { code: 0, label: "Select from the following" },
            editingField: {
                title: false,
                short_description: false,
                brand_id: false,
                category_id: false,
                shipping_method_id: false,
                hero_image: false,
                video_link: false,
                product_description: false,
                product_highlight: false,
                warranty: false,
                max_quantity: false,
                quantity_step: false
            },
            editedProduct: {
                title: this.product.title || '',
                short_description: this.product.short_description || '',
                hero_image: this.product.hero_image || '',
                video_link: this.product.video_link || 'N/A',
                warranty: this.product.warranty || '',
                max_quantity: this.product.max_quantity || 'N/A',
                quantity_step: this.product.quantity_step || '',
            }
        }
    },
    computed: {
        titleLength() {
            return this.product.title.length > 150 ? 150 : this.product.title.length;
        },
        shortDescriptionLength() {
            return this.product.short_description.length > 150 ? 150 : this.product.short_description.length;
        }
    },
    methods: {
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        editProductVariant(variation){
            this.$emit('editProductVariant', variation);
        },
        editField(field) {
            this.editingField[field] = true;
            // Wait for Vue to update the DOM before initializing Summernote
            this.$nextTick(() => {
                if (field === 'product_description' || field === 'product_highlight') {
                    $(".productDescriptionEdit, .productHighlightEdit").summernote({
                        dialogsInBody: true,
                        minHeight: 200,
                        toolbar: [
                            ["style", ["bold"]],
                            ["para", ["ul", "ol", "paragraph"]]
                        ],
                        callbacks: {
                            onChange: (contents) => {
                                this.product.product_description = contents; // Update Vue data when content changes
                            }
                        }
                    });

                    const elementClass = (field === 'product_description') ? '.productDescriptionEdit' : '.productHighlightEdit';

                    // Initialize Summernote on the correct class
                    $(elementClass).summernote('code', this.product[field]);
                }
            });
        },
        saveField(field) {

            if (field === 'product_description' || field === 'product_highlight') {
                const elementClass = (field === 'product_description') ? '.productDescriptionEdit' : '.productHighlightEdit';
                // Initialize Summernote on the correct class
                const value = $(elementClass).summernote('code');
                this.product[field] = value;
                this.editedProduct[field] = value;
            }else if(field == 'brand_id' || field == 'category_id' || field == 'shipping_method_id'){
                if(field == 'brand_id'){
                    this.editedProduct[field] = this.brand.code;
                    this.pproduct.brand  = {name : this.brand.label};
                }
                if(field == 'category_id'){
                    this.editedProduct[field] = this.category.code;
                    this.product.category= {
                        name : this.category.label
                    }
                }
                if(field == 'shipping_method_id'){
                    this.editedProduct[field] = this.selectedShipping.code;
                    this.product.shipping = {
                        name : this.selectedShipping.label
                    }
                }
            }else{
                this.product[field] = this.editedProduct[field];
            }

            this.editingField[field] = false;
            this.$emit('updateProduct', {id : this.product.id, field: field, value: this.editedProduct[field]});
        },
        cancelEdit(field) {
            this.editingField[field] = false;
            this.editedProduct[field] = this.product[field]; // Revert to original value
        },
        getImageUrl(imageId) {
            // Check if the image is null
            if (!imageId) {
                return this.public_url + 'assets/img/blank_image.jpg';
            }
            return this.public_url + 'storage/uploads/inventory/products/media/' + imageId;
        }
    },
    watch: {
        product: {
            handler(newVal) {
                this.editedProduct = {
                    title: newVal.title || '',
                    short_description: newVal.short_description || '',
                    hero_image: newVal.hero_image || '',
                    video_link: newVal.video_link || 'N/A',
                    product_description: newVal.product_description || '',
                    product_highlight: newVal.product_highlight || 'N/A',
                    warranty: newVal.warranty || '',
                    max_quantity: newVal.max_quantity || 'N/A',
                    quantity_step: newVal.quantity_step || '',
                };
            },
            deep: true, // To watch for changes within nested properties
            immediate: true // To initialize editedProduct on component mount
        }
    }
}
</script>
