<template>
    <!-- Modal -->
    <div class="modal fade" id="createProduct" tabindex="-1" role="dialog" aria-labelledby="createProduct"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add New Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7 card mx-auto">
                            <div class="row">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <h5>Product Title <span class="text-danger">*</span></h5>
                                        <input type="text" class="form-control" placeholder="Add Product Title"
                                            v-model="title">
                                        <code>Length ( {{ titleLength }} / 150 )</code>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <h5>Short Description <span class="text-danger">*</span></h5>
                                        <input type="text" class="form-control"
                                            placeholder="Please enter short description of product"
                                            v-model="shortDescription">
                                        <code>Length ( {{ shortDescriptionLength }} / 150 )</code>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <h5>Product Description <span class="text-danger">*</span></h5>
                                        <textarea class="summernote productDescription"></textarea>
                                    </div>

                                    <div class="col-12 col-sm-6 col-lg-12 mt-3" v-if="selectedColors.length == 0">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>Product Gallery</h4>
                                                <a href="#" data-toggle="modal" data-target="#uploadProductImage"
                                                    @click="addImage('Blank')" class="btn btn-outline-primary"
                                                    style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                    New</a>
                                            </div>
                                            <div class="card-body">
                                                <div class="gallery">
                                                    <div v-for="(image, index) in images['Blank']" :key="index"
                                                        class="gallery-item"
                                                        :data-image="public_url + 'storage/uploads/inventory/products/media/' + image.attachment"
                                                        :data-title="image.alt"
                                                        :href="public_url + 'storage/uploads/inventory/products/media/' + image.attachment"
                                                        :title="image.alt"
                                                        :style="{ backgroundImage: 'url(' + public_url + 'storage/uploads/inventory/products/media/' + image.attachment + ')' }">
                                                        <!-- Remove Icon -->
                                                        <button @click="removeImage(index)" class="remove-icon">
                                                            &#10006;
                                                            <!-- This represents an "X" icon; you can use an actual icon from a library like FontAwesome -->
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cloned Product Galleries -->
                                    <div v-for="color in selectedColors" :key="color"
                                        class="col-12 col-sm-6 col-lg-12 mt-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4>{{ color }} Product Gallery</h4>
                                                <a href="#" data-toggle="modal" data-target="#uploadProductImage"
                                                    @click="addImage(color)" class="btn btn-outline-primary"
                                                    style="height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                    New</a>
                                            </div>
                                            <div class="card-body">
                                                <div class="gallery" v-if="images[color]">
                                                    <div v-for="(image, index) in images[color]" :key="index"
                                                        class="gallery-item"
                                                        :data-image="public_url + 'storage/uploads/inventory/products/media/' + image.attachment"
                                                        :data-title="image.alt"
                                                        :href="public_url + 'storage/uploads/inventory/products/media/' + image.attachment"
                                                        :title="image.alt"
                                                        :style="{ backgroundImage: 'url(' + public_url + 'storage/uploads/inventory/products/media/' + image.attachment + ')' }">
                                                        <!-- Remove Icon -->
                                                        <button @click="removeImage(color, index)" class="remove-icon">
                                                            &#10006;
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-5 card card-primary">

                                        <div class="card-body row">
                                            <div class="col-12 col-sm-12 col-md-4">
                                                <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="general-tab" data-toggle="tab"
                                                            href="#general" role="tab" aria-controls="home"
                                                            aria-selected="true">General</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="home-tab4" data-toggle="tab"
                                                            href="#home4" role="tab" aria-controls="home"
                                                            aria-selected="true">Dimensions</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="profile-tab4" data-toggle="tab"
                                                            href="#profile4" role="tab" aria-controls="profile"
                                                            aria-selected="false">Max & Step</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="contact-tab4" data-toggle="tab"
                                                            href="#contact4" role="tab" aria-controls="contact"
                                                            aria-selected="false">Shipping</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="linked-product" data-toggle="tab"
                                                            href="#linkedProduct" role="tab" aria-controls="contact"
                                                            aria-selected="false">Linked Product</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-8">

                                                <div class="tab-content no-padding" id="myTab2Content">
                                                    <div class="tab-pane fade show row active" id="general"
                                                        role="tabpanel" aria-labelledby="general-tab">
                                                        <div class="col-md-12 mt-2">
                                                            <p>Video Link <span class="text-danger">( optional )</span>
                                                            </p>
                                                            <input type="text" class="form-control" v-model="videoLink">
                                                            <small>Please add valid video link, thanks</small>
                                                        </div>
                                                        <div class="col-md-12 mt-2">
                                                            <p>Warranty <span class="text-danger">( optional )</span>
                                                            </p>
                                                            <input type="text" class="form-control" v-model="warranty">
                                                        </div>
                                                        <div class="col-md-12 mt-2">
                                                            <p>Product Highlights <span class="text-danger">( optional
                                                                    )</span></p>
                                                            <textarea class="summernote productHighlights"></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="tab-pane fade" id="home4" role="tabpanel"
                                                        aria-labelledby="home-tab4">
                                                        <div class="col-md-12 row">
                                                            <div class="col-md-4">
                                                                <p>Weight (kg)</p>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control"
                                                                    v-model="dimensions.weight">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 row">
                                                            <div class="col-md-4">
                                                                <p>Dimension(cm)</p>
                                                            </div>
                                                            <div class="col-md-8 d-flex justify-content-between">
                                                                <input type="text" class="form-control"
                                                                    placeholder="length" v-model="dimensions.length">
                                                                <input type="text" class="form-control"
                                                                    placeholder="width" v-model="dimensions.width">
                                                                <input type="text" class="form-control"
                                                                    placeholder="height" v-model="dimensions.heigth">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade row" id="profile4" role="tabpanel"
                                                        aria-labelledby="profile-tab4">
                                                        <div class="col-md-12 mt-3">
                                                            <p>Maximum Quantity <span class="text-danger">( optional
                                                                    )</span></p>
                                                            <input type="text" class="form-control"
                                                                @keypress="onlyNumber" v-model="maxQuantity">
                                                            <code>Enter Maximum Quantity for this product</code>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <p>Quantity Steps <span class="text-danger">( optional
                                                                    )</span></p>
                                                            <input type="text" class="form-control"
                                                                @keypress="onlyNumber" v-model="quantityStep">
                                                            <code>Enter Quantity Steps</code>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade row" id="contact4" role="tabpanel"
                                                        aria-labelledby="contact-tab4">
                                                        <div class="col-md-12 mt-4">
                                                            <p>Shipping Class <span class="text-danger">( optional
                                                                    )</span></p>
                                                            <v-select :options="shippingOptions"
                                                                v-model="selectedShipping">
                                                            </v-select>
                                                        </div>
                                                    </div>

                                                    <div class="tab-pane fade row" id="linkedProduct" role="tabpanel"
                                                        aria-labelledby="linked-product">
                                                        <div class="col-md-12">
                                                            <p>Upsells <span class="text-danger">( optional )</span></p>
                                                            <v-select :options="productOptions" v-model="upsell"
                                                                multiple @search="searchProduct">
                                                            </v-select>
                                                            <small>Please Enter 3 or more characters to Search
                                                                Product</small>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <p>Cross-sells <span class="text-danger">( optional )</span>
                                                            </p>
                                                            <v-select :options="productOptions" v-model="crossSell"
                                                                multiple @search="searchProduct">
                                                            </v-select>
                                                            <small>Please Enter 3 or more characters to Search
                                                                Product</small>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <hr>
                                                            <p>Bought Together<span class="text-danger">( optional
                                                                    )</span></p>
                                                            <v-select :options="productOptions" v-model="boughtTogether"
                                                                multiple @search="searchProduct">
                                                            </v-select>
                                                            <small>Please Enter 3 or more characters to Search
                                                                Product</small>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 card mx-auto">
                            <div class="row">
                                <div class="card-body">

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label"><b>Brand</b> <span class="text-danger">(
                                                        optional )</span></label>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="#" data-toggle="modal" data-target="#addBrand"
                                                    class="btn btn-outline-primary"
                                                    style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                    New</a>
                                            </div>
                                        </div>
                                        <v-select :options="brands" v-model="brand">
                                        </v-select>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label"><b>Category</b> <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="#" data-toggle="modal" data-target="#addCategory"
                                                    class="btn btn-outline-primary"
                                                    style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                    New</a>
                                            </div>
                                        </div>
                                        <v-select :options="categories" v-model="category">
                                        </v-select>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <div id="accordion">
                                            <div class="accordion">
                                                <div class="accordion-header" @click="toggleAttribute('selectColor')"
                                                    role="button" data-toggle="collapse" data-target="#panel-body-1"
                                                    aria-expanded="true">
                                                    <h4>
                                                        <i
                                                            :class="selectColor ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                                                        {{ selectColor ? 'Hide' : 'Show' }} Colors
                                                    </h4>
                                                </div>
                                                <div class="accordion-body collapse" id="panel-body-1"
                                                    data-parent="#accordion" style="">
                                                    <a href="#" data-toggle="modal" data-target="#addColor"
                                                        class="btn btn-outline-primary"
                                                        style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                        New</a><br>
                                                    <ol style="height:200px; overflow-y:scroll">
                                                        <li v-for="color in colors" :key="color.id">
                                                            <div class="pretty p-default">
                                                                <input type="checkbox" :value="color.name"
                                                                    v-model="selectedColors"> <label>{{ color.name
                                                                    }}</label>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="accordion">
                                                <div class="accordion-header" @click="toggleAttribute('selectSize')"
                                                    role="button" data-toggle="collapse" data-target="#panel-body-2"
                                                    aria-expanded="true">
                                                    <h4>
                                                        <i
                                                            :class="selectSize ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                                                        {{ selectSize ? 'Hide' : 'Show' }} Sizes
                                                    </h4>
                                                </div>
                                                <div class="accordion-body collapse" id="panel-body-2"
                                                    data-parent="#accordion" style="">
                                                    <a href="#" data-toggle="modal" data-target="#addSize"
                                                        class="btn btn-outline-primary"
                                                        style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                        New</a><br>
                                                    <ol style="height:200px; overflow-y:scroll">
                                                        <li v-for="item in sizes" :key="item.id">
                                                            <div class="pretty p-default">
                                                                <input type="checkbox" :value="item.id"
                                                                    v-model="selectedSizes"> <label>{{ item.name
                                                                    }}</label>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="accordion">
                                                <div class="accordion-header" @click="toggleAttribute('selectTag')"
                                                    role="button" data-toggle="collapse" data-target="#panel-body-3"
                                                    aria-expanded="true">
                                                    <h4>
                                                        <i
                                                            :class="selectTag ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                                                        {{ selectTag ? 'Hide' : 'Show' }} Tags
                                                    </h4>
                                                </div>
                                                <div class="accordion-body collapse" id="panel-body-3"
                                                    data-parent="#accordion" style="">
                                                    <a href="#" data-toggle="modal" data-target="#addTag"
                                                        class="btn btn-outline-primary"
                                                        style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                        New</a><br>
                                                    <ol style="height:200px; overflow-y:scroll">
                                                        <li v-for="item in tags" :key="item.id">
                                                            <div class="pretty p-default">
                                                                <input type="checkbox" :value="item.id"
                                                                    v-model="selectedTags"> <label>{{ item.name
                                                                    }}</label>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="accordion">
                                                <div class="accordion-header"
                                                    @click="toggleAttribute('selectOtherAttributes')" role="button"
                                                    data-toggle="collapse" data-target="#panel-body-4"
                                                    aria-expanded="true">
                                                    <h4>
                                                        <i
                                                            :class="selectOtherAttributes ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                                                        {{ selectOtherAttributes ? 'Hide' : 'Show' }} Other Attributes
                                                    </h4>
                                                </div>
                                                <div class="accordion-body collapse" id="panel-body-4"
                                                    data-parent="#accordion" style="">
                                                    <a href="#" data-toggle="modal" data-target="#addAttribute"
                                                        class="btn btn-outline-primary"
                                                        style=" height: 15px; line-height: 1px; padding: 6px; float: right">Add
                                                        New</a><br>
                                                    <ol style="height:200px; overflow-y:scroll">
                                                        <li v-for="(item, index) in attributes" :key="item.id">
                                                            <div class="pretty p-default">
                                                                <input type="checkbox" :value="item.id"
                                                                    v-model="selectedAttributes"> <label>{{ item.name
                                                                    }}</label>

                                                                <label>{{ item.name }}</label>
                                                            </div>
                                                            <!-- Conditionally render input field if checkbox is checked -->
                                                            <!-- <div v-if="selectedAttributes[index].checked">
                                                                <input type="text"

                                                                    class="form-control"
                                                                    :placeholder="'Enter details for ' + item">
                                                            </div> -->
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 my-3">
                                            <div class="card card-primary row">
                                                <div class="col-md-9 mx-auto pt-3">
                                                    <label for="" v-if="!heroImage.attachment"><b>Select Product
                                                            Image</b></label>
                                                    <img v-else
                                                        :src="public_url + 'storage/uploads/inventory/products/media/' + heroImage.attachment"
                                                        style="width: 100%" alt="">
                                                </div>
                                                <div class="col-md-12 my-2">
                                                    <button class="btn btn-primary w-100" data-toggle="modal"
                                                        data-target="#uploadProductImage"
                                                        @click="addImage('Hero')">Select Product Image</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <h5>Regular Price <span class="text-danger">*</span></h5>
                                            <input type="text" class="form-control" v-model="regularPrice" />
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <h5>Sale Price</h5>
                                            <input type="text" class="form-control" v-model="salePrice">
                                            <code><a href="#" @click="schedule.status = !schedule.status">click to add schedule</a></code>
                                        </div>
                                        <div class="col-md-12 row border p-3" v-if="schedule.status">
                                            <div class="col-md-6">
                                                <label for=""><b>Valid From</b></label>
                                                <input type="date" class="form-control" v-model="schedule.from">
                                            </div>
                                            <div class="col-md-6">
                                                <label for=""><b>Valid Till</b></label>
                                                <input type="date" class="form-control" v-model="schedule.to">
                                            </div>
                                        </div>


                                        <div class="col-md-12 mt-5">
                                            <h5>Discount per Quantity <code>( optional )</code></h5>
                                        </div>
                                        <div class="form-group form-float col-md-12 row"
                                            v-for="(item, index) in discountPerQty" :key="index">

                                            <div class="col-md-5">
                                                <label for="">Quantity</label>
                                                <input type="text" class="form-control" v-model="item.quantity">
                                                <code>Example : 20 - 50</code>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="">Price</label>
                                                <input type="text" class="form-control" v-model="item.price"
                                                    @keypress="onlyNumber">
                                                <code>1,463</code>
                                            </div>
                                            <div class="col-md-2 ">
                                                <label for="">Action</label><br>
                                                <div class="d-flex align-item-center">
                                                    <i class="btn btn-primary fa fa-plus" style="height:35px"
                                                        @click="addDiscountQuantityOneRow(index)"></i>
                                                    <i class="btn btn-danger fa fa-trash ml-1" style="height:35px"
                                                        @click="removeDiscountQuantityRow(index)"
                                                        v-if="discountPerQty.length > 1"></i>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-primary daterange-btn icon-left btn-icon"><i
                            class="fas fa-calendar"></i> Scheduled Publish Date
                    </a>
                    <button type="button" v-if="!loader" class="btn btn-primary" @click="submitProduct()"><i
                            class="fa fa-paper-plane" aria-hidden="true"></i>
                        Publish</button>
                    <button type="button" v-else class="btn btn-primary btn-progress disabled"><i
                            class="fa fa-paper-plane" aria-hidden="true"></i>
                        Publish</button>
                    <button type="button" class="btn btn-warning text-dark">
                        <i class="fas fa-save"></i>
                        Save in draft
                    </button>
                    <button type="button" class="btn btn-secondary" @click="close()" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AddProductPopup',
    props: ["images", "heroImage", "brands", "categories", "colors", "sizes", "tags", "attributes", "shippingOptions", "loader", "productOptions"],
    data() {
        return {
            public_url: window.location.origin + process.env.MIX_FOLDER_PATH + '/',
            brand: { code: 0, label: "Select from the following" },
            category: { code: 0, label: "Select from the following" },
            title: '',
            shortDescription: '',
            selectOtherAttributes: false,
            selectColor: false,
            selectSize: false,
            selectTag: false,
            selectedShipping: { code: 0, label: 'Select from the following' },
            upsell: {},
            crossSell: {},
            boughtTogether: {},
            selectedAttributes: [],
            discountPerQty: [{ quantity: 0, price: 0 }],
            selectedColors: [], // Tracks selected colors
            selectedTags: [],
            selectedSizes: [],
            colorImages: {}, // Stores images by color
            schedule: {
                status: false,
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            },
            dimensions: {
                weight: 0,
                length: '',
                heigth: '',
                width: ''
            },
            regularPrice: 0,
            salePrice: 0,
            productDescription: '',
            productHighlights: '',
            warranty: '',
            quantityStep: '',
            maxQuantity: '',
            videoLink: ''
        }
    },
    mounted() {
        this.$parent.$on("productAdded", (value) => {
            if (value) {
                this.close();
            }
        });
    },
    created() {
        this.selectedAttributes = this.attributes.map(() => ({
            checked: false,
            value: ''
        }));
    },
    computed: {
        titleLength() {
            return this.title.length > 150 ? 150 : this.title.length;
        },
        shortDescriptionLength() {
            return this.shortDescription.length > 150 ? 150 : this.shortDescription.length;
        }
    },
    methods: {
        searchProduct(search) {
            if (search.length >= 3) {
                this.$emit('searchProduct', { search })
            }
        },
        onlyNumber($event) {
            let keyCode = $event.keyCode ? $event.keyCode : $event.which;
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                // 46 is dot
                $event.preventDefault();
            }
        },
        addDiscountQuantityOneRow(index) {
            if (this.discountPerQty.length == 3) {
                return swal({
                    title: "Error",
                    text: "Can't add more than 3",
                    icon: "error",
                    timer: 3000,
                });
            }
            this.discountPerQty.push({ quantity: 0, price: 0 });
        },
        removeDiscountQuantityRow(index) {
            this.discountPerQty.splice(index, 1); // Remove the item at the given index
        },
        toggleAttribute(attribute) {
            this[attribute] = !this[attribute];
        },
        addImage(color) {
            this.$emit('color', { color })
        },
        removeImage(color, index) {
            if (this.images[color]) {
                this.images[color].splice(index, 1);
            }
        },
        submitProduct() {
            // Validation for required fields
            if (!this.title.trim()) {
                return swal({
                    title: "Error",
                    text: "Product Title is required.",
                    icon: "error",
                    timer: 3000,
                });
            }
            if (!this.shortDescription.trim()) {
                return swal({
                    title: "Error",
                    text: "Short Description is required.",
                    icon: "error",
                    timer: 3000,
                });
            }
            if (!this.category.code) {
                return swal({
                    title: "Error",
                    text: "Category is required.",
                    icon: "error",
                    timer: 3000,
                });
            }
            if (!this.heroImage.attachment) {
                return swal({
                    title: "Error",
                    text: "Product Image is required.",
                    icon: "error",
                    timer: 3000,
                });
            }
            if (!this.regularPrice) {
                return swal({
                    title: "Error",
                    text: "Regular Price is required.",
                    icon: "error",
                    timer: 3000,
                });
            }
            // Create a new FormData instance
            let formData = new FormData();

            const productDescription = $('.productDescription').summernote('code');
            const productHighlights = $('.productHighlights').summernote('code');

            // Append form data
            formData.append('title', this.title);
            formData.append('shortDescription', this.shortDescription);
            formData.append('weight', this.weight);
            formData.append('brand', this.brand.code);
            formData.append('category', this.category.code);
            formData.append('selectedShipping', this.selectedShipping.code);
            formData.append('heroImage', this.heroImage.attachment);
            formData.append('videoLink', this.videoLink);
            formData.append('productDescription', productDescription); // Assuming the textarea has a ref
            formData.append('regularPrice', this.regularPrice);
            formData.append('salePrice', this.salePrice);

            formData.append('salePrice', this.salePrice);

            const upsells = Array.isArray(this.upsell) ? this.upsell : [];
            const crossSells = Array.isArray(this.crossSell) ? this.crossSell : [];

            // Append upsells to formData
            upsells.forEach((upsell, index) => {
                if(upsell){
                    formData.append(`upsells[${index}]`, upsell.code);
                }
            });

            // Append crossSells to formData
            crossSells.forEach((crossSell, index) => {
                if(crossSell){
                    formData.append(`crossSells[${index}]`, crossSell.code);
                }
            });

            // Ensure boughtTogethers is an array before using it
            const boughtTogethers = Array.isArray(this.boughtTogether) ? this.boughtTogether : [];

            // Append boughtTogethers to formData
            boughtTogethers.forEach((boughtTogether, index) => {
                if(boughtTogether){
                    formData.append(`boughtTogethers[${index}]`, boughtTogether.code);
                }
            });

            const findColorIdByName = (name) => {
                const color = this.colors.find(c => c.name === name);
                return color ? color.id : null;
            };

            // Append selected colors array using IDs
            this.selectedColors.forEach((colorName, index) => {
                const colorId = findColorIdByName(colorName);
                if (colorId) {
                    formData.append(`colors[${index}]`, colorId);
                }
            });

            // Append tags array
            this.selectedTags.forEach((tag, index) => {
                formData.append(`tags[${index}]`, tag);
            });

            // Append sizes array
            this.selectedSizes.forEach((size, index) => {
                formData.append(`sizes[${index}]`, size);
            });

            // Append attributes array
            this.selectedAttributes.forEach((attribute, index) => {
                formData.append(`attributes[${index}]`, attribute);
            });

            // Append product highlights, warranty, and other fields
            formData.append('productHighlights', productHighlights);
            formData.append('warranty', this.warranty);

            formData.append('quantityStep', this.quantityStep);
            formData.append('maximumQuantity', this.maxQuantity);

            // Append color images
            for (const color in this.images) {
                this.images[color].forEach(image => {
                    formData.append(`images[${color}][]`, image.attachment);
                });
            }
            formData.append('saleSchedule', JSON.stringify({
                status: this.schedule.status,
                from: this.schedule.from,
                to: this.schedule.to,
            }));

            // Append discountPerQty
            formData.append('discountPerQty', JSON.stringify(this.discountPerQty));
            formData.append('dimensions', JSON.stringify(this.dimensions));

            this.$emit('submitProduct', formData);
        },
        close() {
            this.brand = { code: 0, label: "Select from the following" };
            this.category = { code: 0, label: "Select from the following" };
            this.title = '';
            this.shortDescription = '';
            this.selectOtherAttributes = false;
            this.selectColor = false;
            this.selectSize = false;
            this.selectTag = false;
            this.selectedShipping = { code: 0, label: 'Select from the following' };
            this.upsell = {};
            this.crossSell = {};
            this.boughtTogether = {};
            this.selectedAttributes = [];
            this.discountPerQty = [{ quantity: 0, price: 0 }];
            this.selectedColors = [];
            this.selectedTags = [];
            this.selectedSizes = [];
            this.colorImages = {};
            this.schedule = {
                status: false,
                from: new Date().toISOString().substr(0, 10),
                to: new Date().toISOString().substr(0, 10),
            };
            this.dimensions = {
                weight: 0,
                length: '',
                heigth: '',
                width: ''
            };
            this.regularPrice = 0;
            this.salePrice = 0;
            this.productDescription = '';
            this.productHighlights = '';
            this.warranty = '';
            this.quantityStep = '';
            this.maxQuantity = '';
            this.videoLink = '';

            $('.summernote').summernote('code', '');
            $('.productHighlights').summernote('code');

            // If you have any file inputs, clear them here
            this.$emit('close', true) // Adjust the ref name as per your file input field
        }
    },
}
</script>
<style scoped>
.remove-icon {
    position: absolute;
    top: 3px;
    right: 3px;
    background-color: rgba(255, 255, 255, 0.7);
    border: none;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    font-size: 10px;
    line-height: 1;
    color: #ff0000;
    /* red color for the icon */
}
</style>
