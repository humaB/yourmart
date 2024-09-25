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
                                                    <v-select :options="packagingOptions"
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
                                                <a  :href="getImageUrl(product.hero_image)" target="_blank" rel="noopener noreferrer">
                                                    <img :src="getImageUrl(product.hero_image)" alt="Hero Image" class="user-img mr-2" width="100" />
                                                </a>

                                            </td>
                                            <td>
                                                <button  @click="addImage('Hero' , 'edit')" data-toggle="modal" data-target="#uploadProductImage" class="btn btn-sm btn-primary">Edit</button>
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
                                                <button class="btn btn-primary" data-toggle="modal" data-target="#uploadProductImage" @click="addImage(variation.color ? variation.color.name : 'Blank', 'colorEdit', variation.id)">Add Images</button>
                                                <button class="btn btn-primary" @click="editProductVariant(variation)" data-toggle="modal" data-target="#editProductVariant"><i class="fa fa-edit"></i> Edit</button>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Discounts -->
                            <div class="col-md-12 mt-4">
                                <h5>Discounts Per Quantity</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(discount, index) in product.discounts" :key="discount.id">
                                            <td>
                                                <template v-if="editingIndex === index">
                                                    <input type="text" v-model="discount.quantity" class="form-control" />
                                                </template>
                                                <template v-else>
                                                    {{ discount.quantity }}
                                                </template>
                                            </td>
                                            <td>
                                                <template v-if="editingIndex === index">
                                                    <input type="text" @keypress="onlyNumber" v-model="discount.price" :min="0" step="0.01" class="form-control" />
                                                </template>
                                                <template v-else>
                                                    {{ discount.price }}
                                                </template>
                                            </td>
                                            <td>
                                                <template v-if="editingIndex === index">
                                                    <button @click="saveDiscount(index)" class="btn btn-success btn-sm">Save</button>
                                                    <button @click="cancelDiscountEdit(index)" class="btn btn-danger btn-sm ml-2">Cancel</button>
                                                </template>
                                                <template v-else>
                                                    <button @click="editDiscount(index)" class="btn btn-primary btn-sm">Edit</button>
                                                </template>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                                                    <!-- Dimensions -->
<div class="col-md-12 mt-4">
    <h5>Dimensions</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Weight (kg)</th>
                <th>Length</th>
                <th>Width</th>
                <th>Height</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <template v-if="dimensionEditing && product.dimensions">
                        <input type="text" v-model="product.dimensions.weight" class="form-control" />
                    </template>
                    <template v-else>
                        {{ product.dimensions ? product.dimensions.weight : 0 }}
                    </template>
                </td>
                <td>
                    <template v-if="dimensionEditing && product.dimensions">
                        <input type="text" @keypress="onlyNumber" v-model="product.dimensions.length" :min="0" step="0.01" class="form-control" />
                    </template>
                    <template v-else>
                        {{ product.dimensions ? product.dimensions.length : 0 }}
                    </template>
                </td>
                <td>
                    <template v-if="dimensionEditing && product.dimensions">
                        <input type="text" @keypress="onlyNumber" v-model="product.dimensions.width" :min="0" step="0.01" class="form-control" />
                    </template>
                    <template v-else>
                        {{ product.dimensions ? product.dimensions.width : 0 }}
                    </template>
                </td>
                <td>
                    <template v-if="dimensionEditing && product.dimensions">
                        <input type="text" @keypress="onlyNumber" v-model="product.dimensions.height" :min="0" step="0.01" class="form-control" />
                    </template>
                    <template v-else>
                        {{ product.dimensions ? product.dimensions.height : 0 }}
                    </template>
                </td>
                <td>
                    <template v-if="dimensionEditing">
                        <button @click="saveDimension" class="btn btn-success btn-sm">Save</button>
                        <button @click="cancelDimensionEdit" class="btn btn-danger btn-sm ml-2">Cancel</button>
                    </template>
                    <template v-else>
                        <button @click="editDimension" class="btn btn-primary btn-sm">Edit</button>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>
</div>

                            <div class="col-md-12 mt-4">
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
                                                <div class="p-3 row">
                                                    <div class="col-md-10 ">
                                                        <v-select :options="productOptions" v-model="upsell"
                                                            multiple @search="searchProduct">
                                                        </v-select>
                                                        <small>Please Enter 3 or more characters to Search
                                                            Product</small>
                                                    </div>
                                                    <div class="col-md2">
                                                        <button class="mt-2 btn btn-primary" @click="updateUpSell('upsell')">Add</button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <ul>
                                                        <li v-for="item in product.up_sells" :key="item.id">
                                                            {{ item.product.title }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="p-3 row">
                                                    <div class="col-md-10 ">
                                                        <v-select :options="productOptions" v-model="crossSell"
                                                            multiple @search="searchProduct">
                                                        </v-select>
                                                        <small>Please Enter 3 or more characters to Search
                                                            Product</small>
                                                    </div>
                                                    <div class="col-md2">
                                                        <button class="mt-2 btn btn-primary" @click="updateUpSell('crossSell')">Add</button>
                                                    </div>
                                                </div>
                                                <div class="mt-3">

                                                    <ul>
                                                        <li v-for="item in product.cross_sells" :key="item.id">
                                                            {{ item.product.title }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="p-3 row">
                                                    <div class="col-md-10 ">
                                                        <v-select :options="productOptions" v-model="boughtTogether"
                                                            multiple @search="searchProduct">
                                                        </v-select>
                                                        <small>Please Enter 3 or more characters to Search
                                                            Product</small>
                                                    </div>
                                                    <div class="col-md2">
                                                        <button class="mt-2 btn btn-primary" @click="updateUpSell('boughtTogether')">Add</button>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
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

                            <div class="col-md-12 mt-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tags</th>
                                            <th>Other Attributes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="p-3 row">
                                                    <div class="col-md-10 ">
                                                        <v-select :options="tags" v-model="selectedTags" multiple>
                                                        </v-select>

                                                    </div>
                                                    <div class="col-md2">
                                                        <button class="mt-2 btn btn-primary" @click="updateTags('tags')">Add</button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <ul>
                                                        <li v-for="item in product.tags" :key="item.id">
                                                            {{ item.tag.name }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="p-3 row">
                                                    <div class="col-md-10 ">
                                                        <v-select :options="attributes" v-model="selectedAttributes"
                                                            multiple>
                                                        </v-select>
                                                    </div>
                                                    <div class="col-md2">
                                                        <button class="mt-2 btn btn-primary" @click="updateTags('attributes')">Add</button>
                                                    </div>
                                                </div>
                                                <div class="mt-3">

                                                    <ul>
                                                        <li v-for="item in product.attributes" :key="item.id">
                                                            {{ item.attribute.name }}
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
                        <div class="dropdown">
                            <a href="#" data-toggle="dropdown" class="btn btn-dark dropdown-toggle">Change Status</a>
                            <div class="dropdown-menu">
                              <a href="#" class="dropdown-item has-icon border-bottom" @click="changeStatus('publish')"><i class="fa fa-paper-plane"></i>Publish</a>
                              <a href="#" class="dropdown-item has-icon border-bottom" @click="changeStatus('save')"><i class="fas fa-save"></i>Save in Draft</a>
                            </div>
                          </div>
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
    props: ["brands", "categories", "tags", "attributes", "packagingOptions", "loader", "product", "productNotUpdated", "productOptions", "addedTags", "heroImage"],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            selectedShipping: { code: 0, label: 'Select from the following' },
            brand: { code: 0, label: "Select from the following" },
            category: { code: 0, label: "Select from the following" },
            selectedTags: {},
            selectedAttributes : {},
            upsell: {},
            crossSell: {},
            boughtTogether: {},
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
            },
            editingIndex: null, // Track which row is being edited
            dimensionEditing: false,
            dimension: {
                weight: '',
                length: '',
                width: '',
                height: ''
            },
            originalDimension: {}
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
        editDimension() {
            if (!this.product.dimensions) {
                this.product.dimensions = {
                    weight: 0,
                    length: 0,
                    width: 0,
                    height: 0
                };
            }
            this.dimensionEditing = true;
        },
        cancelDimensionEdit() {
            this.dimensionEditing = false;
            if (!this.product.dimensions) {
                this.product.dimensions = null;
            }
        },
        saveDimension() {
            // API call to save dimension
            this.dimensionEditing = false;
            const data = {
                dimensions : this.product.dimensions,
                product :  this.product.id
            }

            this.$emit('updateDimensions', data);
        },
        addImage(image, type, id = null) {
            this.$emit('changeImage', { image , type, id, title : this.product.title })
        },
        changeStatus( data ){
            this.$emit('changeStatus', {id : this.product.id , status : data});
        },
        updateUpSell( type ){
            // Access the dynamic property based on the 'type' value
            const dataToSend = this[type];

            this.$emit('updateUpSell', { id : this.product.id , products : dataToSend, type : type})
        },
        updateTags( type ){
            // Access the dynamic property based on the 'type' value
            const dataToSend = this[type];

            this.$emit('updateTags', { id : this.product.id , products : dataToSend, type : type})
        },
        searchProduct(search) {
            if (search.length >= 3) {
                this.$emit('searchProduct', { search })
            }
        },
        editDiscount(index) {
            this.editingIndex = index; // Set the row index to edit
        },
        cancelDiscountEdit(index) {
            this.editingIndex = null; // Exit edit mode
        },
        saveDiscount(index) {
            this.editingIndex = null
            const discounts = this.product.discounts[index];
            const data = {
                discounts : discounts,
                product :  this.product.id
            }

            this.$emit('updateDiscount', data);
        },
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
                    this.product.brand  = {name : this.brand.label};
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
